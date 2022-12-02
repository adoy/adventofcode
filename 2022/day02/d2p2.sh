#!/bin/bash
sed 's#A#1#g;s#B#2#g;s#C#3#g' | awk '{ if ($2=="X") s+=($1+1)%3+1; else if ($2=="Y") s+=(3+$1); else s+=6+($1)%3+1 } END {print s}'

