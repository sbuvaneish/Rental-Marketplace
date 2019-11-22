import colorgram
import webcolors
import sys
import json
from PIL import Image
from io import BytesIO
import base64

def closest_colour(requested_colour):
    min_colours = {}
    for key, name in webcolors.css3_hex_to_names.items():
        r_c, g_c, b_c = webcolors.hex_to_rgb(key)
        rd = (r_c - requested_colour[0]) ** 2
        gd = (g_c - requested_colour[1]) ** 2
        bd = (b_c - requested_colour[2]) ** 2
        min_colours[(rd + gd + bd)] = name
    return min_colours[min(min_colours.keys())]

def get_colour_name(requested_colour):
    try:
        closest_name = actual_name = webcolors.rgb_to_name(requested_colour)
    except ValueError:
        closest_name = closest_colour(requested_colour)
        actual_name = None
    return actual_name, closest_name

image_object = Image.open(BytesIO(base64.b64decode(json.loads(sys.argv[1]))))
color = colorgram.extract(image_object, 2)
color_1 = color[0].rgb
color_2 = color[1].rgb
requested_colour_1 = (color_1.r, color_1.g, color_1.b)
requested_colour_2 = (color_2.r, color_2.g, color_2.b)
actual_name_1, closest_name_1 = get_colour_name(requested_colour_1)
actual_name_2, closest_name_2 = get_colour_name(requested_colour_2)

print(json.dumps([closest_name_1, closest_name_2]))
