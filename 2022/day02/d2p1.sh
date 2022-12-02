#!/bin/bash
sed 's#X\|A#1#g;s#B\|Y#2#g;s#C\|Z#3#g' | awk '{ s+=$2; if ($2==$1) s+=3; else if ((($2-$1+3)%3)==1) s+=6} END {print s}'
