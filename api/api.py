#!/usr/bin/python3

# Copyright (c) 2022 Raspberry Pi Ltd
# Author: Alasdair Allan <alasdair@raspberrypi.com>
# SPDX-License-Identifier: BSD-3-Clause

# A TensorFlow Lite example for Picamera2 on Raspberry Pi OS Bullseye
#
# Install necessary dependences before starting,
#
# $ sudo apt update
# $ sudo apt install build-essential
# $ sudo apt install libatlas-base-dev
# $ sudo apt install python3-pip
# $ pip3 install tflite-runtime
# $ pip3 install opencv-python==4.4.0.46
# $ pip3 install pillow
# $ pip3 install numpy
#
# and run from the command line,
#
# $ python3 real_time_with_labels.py --model mobilenet_v2.tflite --label coco_labels.txt

import argparse
import time
import cv2
import numpy as np
import tflite_runtime.interpreter as tflite
import threading
import io
import uvicorn

from picamera2 import MappedArray, Picamera2, Preview
from fastapi import FastAPI, Request, Depends
from fastapi.responses import StreamingResponse
from fastapi.exceptions import HTTPException
from model import PutDataRequest, GetDataRequest
import influxdb_client, os, time
from influxdb_client import InfluxDBClient, Point, WritePrecision
from influxdb_client.client.write_api import SYNCHRONOUS
from datetime import datetime as dt

tensorThread = None
processThread = None

app = FastAPI()

API_KEY = "bonjourjaimelespates"
# username: redboxing
# password: SuperPassword

token ="z56c2p5pCte0FJuWVSR-7Tw9Bdh-Th3uVa2GTQXbDnRQi82W7oe1-w10r_FwCYItMZuU0YmxybcO6QCP_MHgRA=="
org = "myOrg"
url = "http://localhost:8086"

client = influxdb_client.InfluxDBClient(url=url, token=token, org=org)

class TensorThread(threading.Thread):
    def run(self):
        self.normalSize = (640, 480)
        self.lowresSize = (210, 240)
        self.rectangles = []
        self.buffer = None
        
        self.picam2 = Picamera2()
        #picam2.start_preview(Preview.QTGL)
        config = self.picam2.create_preview_configuration(main={"size": self.normalSize},
                                                         lores={"size": self.lowresSize, "format": "YUV420"})
        self.picam2.configure(config)

        stride = self.picam2.stream_configuration("lores")["stride"]
        self.picam2.post_callback = self.DrawRectangles

        self.picam2.start()

        while True:
            self.buffer = self.picam2.capture_buffer("lores")
            grey = self.buffer[:stride * self.lowresSize[1]].reshape((self.lowresSize[1], stride))
            _ = self.InferenceTensorFlow(grey, "mobilenet_v2.tflite", None, "coco_labels.txt")
        
    def ReadLabelFile(self, file_path):
        with open(file_path, 'r') as f:
            lines = f.readlines()
        ret = {}
        for line in lines:
            pair = line.strip().split(maxsplit=1)
            ret[int(pair[0])] = pair[1].strip()
        return ret


    def DrawRectangles(self, request):
        with MappedArray(request, "main") as m:
            for rect in self.rectangles:
                print(rect)
                rect_start = (int(rect[0] * 2) - 5, int(rect[1] * 2) - 5)
                rect_end = (int(rect[2] * 2) + 5, int(rect[3] * 2) + 5)
                cv2.rectangle(m.array, rect_start, rect_end, (0, 255, 0, 0))
                if len(rect) == 5:
                    text = rect[4]
                    font = cv2.FONT_HERSHEY_SIMPLEX
                    cv2.putText(m.array, text, (int(rect[0] * 2) + 10, int(rect[1] * 2) + 10),
                                font, 1, (255, 255, 255), 2, cv2.LINE_AA)


    def InferenceTensorFlow(self, image, model, output, label=None):
        #global rectangles

        if label:
            labels = self.ReadLabelFile(label)
        else:
            labels = None

        interpreter = tflite.Interpreter(model_path=model, num_threads=4)
        interpreter.allocate_tensors()

        input_details = interpreter.get_input_details()
        output_details = interpreter.get_output_details()
        height = input_details[0]['shape'][1]
        width = input_details[0]['shape'][2]
        floating_model = False
        if input_details[0]['dtype'] == np.float32:
            floating_model = True

        rgb = cv2.cvtColor(image, cv2.COLOR_GRAY2RGB)
        initial_h, initial_w, channels = rgb.shape

        picture = cv2.resize(rgb, (width, height))

        input_data = np.expand_dims(picture, axis=0)
        if floating_model:
            input_data = (np.float32(input_data) - 127.5) / 127.5

        interpreter.set_tensor(input_details[0]['index'], input_data)

        interpreter.invoke()

        detected_boxes = interpreter.get_tensor(output_details[0]['index'])
        detected_classes = interpreter.get_tensor(output_details[1]['index'])
        detected_scores = interpreter.get_tensor(output_details[2]['index'])
        num_boxes = interpreter.get_tensor(output_details[3]['index'])

        self.rectangles = []
        for i in range(int(num_boxes)):
            top, left, bottom, right = detected_boxes[0][i]
            classId = int(detected_classes[0][i])
            score = detected_scores[0][i]
            if score > 0.5:
                xmin = left * initial_w
                ymin = bottom * initial_h
                xmax = right * initial_w
                ymax = top * initial_h
                box = [xmin, ymin, xmax, ymax]
                self.rectangles.append(box)
                if labels:
                    #print(labels[classId], 'score = ', score)
                    self.rectangles[-1].append(labels[classId])
                #else:
                    #print('score = ', score)

def process_data():
    query_api = client.query_api()
    while True:
        query = """from(bucket: "smarthome")
  |> range(start: -24d)
  |> filter(fn: (r) => r["_measurement"] == "sensors")"""

        tables = query_api.query(query, org="myOrg")

        datas = {}

        for record in tables[0].records:
            if not record.values.get("name") in datas:
                datas[record.values.get("name")] = {
                    "value": record.get_value(),
                    "time": record.get_time()
                }
        time.sleep(10)

        if "person" in tensorThread.rectangles:
            pass #turn light on
        else:
            pass #turn light off

        

def gen():
    while True:
        data = io.BytesIO()
        tensorThread.picam2.capture_file(data, format='jpeg')
        frame = data.getbuffer()
        yield(b'--frame\r\n'
              b'Content-Type: image/jpeg\r\n\r\n' + frame + b'\r\n')
        time.sleep(0.05)

@app.get('/stream')
def stream():
    return StreamingResponse(gen(), media_type='multipart/x-mixed-replace; boundary=frame')

def verify_api_key(req: Request):
    key = req.headers["Authorization"]
    if key != API_KEY:
        raise HTTPException(status_code=401,detail="Unauthorized")
    return True

@app.put("/data")
def put_data(body: PutDataRequest, authorized: bool = Depends(verify_api_key)):
    try:
        write_api = client.write_api(write_options=SYNCHRONOUS)
        point = (
            Point("sensors").tag("name", body.name).field("value", body.value)
        )
        write_api.write(bucket="smarthome", org="myOrg", record=point)
        return {"success":True}
    except:
        return {"success":False}

@app.post("/data")
def get_data(body: GetDataRequest):
    try:
        data = []

        query_api = client.query_api()
        query = """from(bucket: "smarthome")
  |> range(start: -{body.duration})
  |> filter(fn: (r) => r["_measurement"] == "sensors")
  |> filter(fn: (r) => r["name"] == "{body.name}")""".replace("{body.duration}", body.duration).replace("{body.name}", body.name)

        tables = query_api.query(query, org="myOrg")

        for table in tables:
            for record in table.records:
                data.append({
                    "name": record.values.get("name"),
                    "value": record.get_value(),
                    "time": record.get_time()
                })

        return {"success":True,"data":data}
    except:
        return {"success":False,"message":"Unknown Error."}

def main():
    global tensorThread
    tensorThread = TensorThread()
    tensorThread.start()

    global processThread
    processThread = threading.Thread(target=process_data)
    processThread.start()

    uvicorn.run(app, host="0.0.0.0", port=5000, access_log=True)

if __name__ == '__main__':
    main()
