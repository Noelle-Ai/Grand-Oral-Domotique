import Adafruit_DHT
import adafruit_bh1750
import board
import requests
import RPi.GPIO as GPIO
import smbus
import threading
import time
import uvicorn

from fastapi import FastAPI
from pydantic import BaseModel

AM2302_sensor = Adafruit_DHT.AM2302
AM2302_pin = 4
bus = smbus.SMBus(1)
i2c = board.I2C()
sensor = adafruit_bh1750.BH1750(i2c)

GPIO.setmode(GPIO.BCM)
GPIO.setup(21, GPIO.OUT, initial=GPIO.LOW)

app = FastAPI()


class LedRequest(BaseModel):
    enabled: bool


@app.get("/")
async def root():
    return {"message": "Hello World"}


@app.post("/led")
async def led(req: LedRequest):  # Requête de gestion du store
    print(req)
    status = GPIO.LOW
    if req.enabled:
        status = GPIO.HIGH
    GPIO.output(21, status)


def get_data():
    print("---------------------------------------------------")
    while True:
        # Lux meter
        print("[INDOOR] Luminosity : %.2f Lux" % sensor.lux)
        time.sleep(1)

        # TH02 Temperature and humidity sensor
        bus.write_byte_data(0x40, 0x03, 0x11)

        time.sleep(0.5)
        data = bus.read_i2c_block_data(0x40, 0x00, 3)

        cTemp = ((data[1] * 256 + (data[2] & 0xFC)) / 4.0) / 32.0 - 50.0

        bus.write_byte_data(0x40, 0x03, 0x01)

        time.sleep(0.5)
        data = bus.read_i2c_block_data(0x40, 0x00, 3)

        humidity_int = ((data[1] * 256 + (data[2] & 0xF0)) / 16.0) / 16.0 - 24.0
        humidity_int = humidity_int - (((humidity_int * humidity_int) * (-0.00393)) + (humidity_int * 0.4008) - 4.7844)
        humidity_int = humidity_int + (cTemp - 30) * (humidity_int * 0.00237 + 0.1973)

        print("[INDOOR] Humidity : %.2f %%" % humidity_int)
        print("[INDOOR] Temperature : %.2f C" % cTemp)

        humidity, temperature = Adafruit_DHT.read_retry(AM2302_sensor, AM2302_pin)

        if humidity is not None and temperature is not None:
            print('[OUTDOOR] Temp={0:0.1f}*C  Humidity={1:0.1f}%'.format(temperature, humidity))
        else:
            print('Failed to get reading. Try again!')

        r_temp_int = requests.put('http://172.21.184.53:5000/data', headers={"Authorization": "bonjourjaimelespates"},
                                  json={
                                      "name": "temp_int",
                                      "value": cTemp
                                  })

        r_temp_ext = requests.put('http://172.21.184.53:5000/data', headers={"Authorization": "bonjourjaimelespates"},
                                  json={
                                      "name": "temp_ext",
                                      "value": temperature
                                  })

        r_humidity_int = requests.put('http://172.21.184.53:5000/data',
                                      headers={"Authorization": "bonjourjaimelespates"},
                                      json={
                                          "name": "humidity_int",
                                          "value": humidity_int
                                      })

        r_humidity_ext = requests.put('http://172.21.184.53:5000/data',
                                      headers={"Authorization": "bonjourjaimelespates"},
                                      json={
                                          "name": "humidity_ext",
                                          "value": humidity
                                      })

        r_lux = requests.put('http://172.21.184.53:5000/data',
                             headers={"Authorization": 'bonjourjaimelespates'},
                             json={
                                 "name": "luminosity_int",
                                 "value": sensor.lux
                             })

        print("---------------------------------------------------")

        time.sleep(4)


if __name__ == "__main__":
    t = threading.Thread(target=get_data)
    t.start()
    uvicorn.run(app, host="0.0.0.0", port=8000)
