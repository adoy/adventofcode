#!/bin/bash
awk 'BEGIN{s=0}{if($1){s+=$1}else{print s;s=0}}END{print s}' | sort -n  | tail -3 | awk '{s+=$1;}NR==3{print $1}END{print s}'
