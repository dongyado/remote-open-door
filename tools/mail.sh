#!/bin/bash
to=$1
subject=$2
body=$3
 
cat <<EOF | s-nail -s "$subject" "$to"
$body
EOF

