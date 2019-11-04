import functools
import sys
import ast
import json

def comparator(a,b):
    #if brands are same, need to compare color 
    if a["brand"] == search["brand"] and b["brand"] == search["brand"]:
        if a["color"] == search["color"] and b["color"] == search["color"]:
            return 0
        elif a["color"] != search["color"] and b["color"] == search["color"]:
            return 1
        elif a["color"] == search["color"] and b["color"] != search["color"]:
            return -1
        else:
            return 0
    #if brands different, the brand that matches appears first no matter the color
    elif a["brand"] != search["brand"] and b["brand"] == search["brand"]:
        return 1
    elif a["brand"] == search["brand"] and b["brand"] != search["brand"]:
        return -1
    else: #if brands don't match at all, then display color matching first
        if a["color"] == search["color"] and b["color"] == search["color"]:
            return 0
        elif a["color"] != search["color"] and b["color"] == search["color"]:
            return 1
        elif a["color"] == search["color"] and b["color"] != search["color"]:
            return -1
        else:
            return 0





records = json.loads(sys.argv[1])
search = json.loads(sys.argv[2])

result = sorted(records, key = functools.cmp_to_key(comparator))
print(json.dumps(result))