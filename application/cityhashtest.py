import sys
from cityhash import CityHash32, CityHash64, CityHash128

def make_cityhash():
    return CityHash64(sys.argv[1] + sys.argv[2] + sys.argv[3])

print(make_cityhash(), end='')