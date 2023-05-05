import requests

payload = { "name": "temp_int", "duration": "24d" }
r = requests.post("http://127.0.0.1:5000/data", json=payload)

print(f"data = {payload}")
print(f"response = {r.text}")
