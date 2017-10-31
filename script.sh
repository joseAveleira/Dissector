#!/bin/bash
echo $1
echo $2
# echo $3
fields="$3"



# replace all blanks
formatFields=${fields//__/ }
# echo "$formatFields"
   

tshark -r "$1" -T fields $formatFields  -E separator=, -E quote=d > "$2"



 sleep 5
