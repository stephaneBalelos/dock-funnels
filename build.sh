#!/bin/bash

# Zip all the files in the plugin directory
# and create a zip file named dock-funnels.zip
# ignore node_modules inside the front-end directory
# print the output zip file name
# Define the plugin directory and the output zip file

PLUGIN_DIR="."
OUTPUT_ZIP="dock-funnels.zip"

# Create the zip file, excluding node_modules in the front-end directory
zip -r "$OUTPUT_ZIP" "$PLUGIN_DIR" -x "*/node_modules/*"
# Print the output zip file name
echo "Created zip file: $OUTPUT_ZIP"