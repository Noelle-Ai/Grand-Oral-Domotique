sudo apt update
sudo apt install software-properties-common
sudo add-apt-repository ppa:deadsnakes/ppa
sudo apt update
sudo apt install build-essential zlib1g-dev libncurses5-dev libgdbm-dev libnss3-dev libssl-dev libreadline-dev libffi-dev wget
sudo apt install python3
sudo apt install python3-pip
sudo pip3 install adafruit-blinka
sudo pip3 install adafruit-circuitpython-dht
sudo apt-get install libgpiod2
sudo pip3 install adafruit-circuitpython-bh1750
pip install "fastapi[all]"
sudo apt-get update
sudo apt-get install python3-smbus python-dev python3-dev i2c-tools git-all
git clone https://github.com/adafruit/Adafruit_Python_DHT.git
cd Adafruit_Python_DHT
sudo apt-get update
sudo apt-get install build-essential python-dev python3-setuptools python-setuptools
sudo python setup.py install
sudo python3 setup.py install
