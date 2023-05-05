from typing import Union
from pydantic import BaseModel

class PutDataRequest(BaseModel):
    name: str
    value: float
class GetDataRequest(BaseModel):
    name: str
    duration: str
