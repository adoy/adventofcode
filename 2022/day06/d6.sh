#!/bin/bash
N=${1:-4}; read input; length=${#input}; for I in `seq 0 $((length-$N))`; do c=`echo ${input:$I:$N} | fold -w1 | sort | uniq -c | wc -l`; if [[ $c -eq $N ]]; then echo `expr $I + $N`; exit; fi; done;
