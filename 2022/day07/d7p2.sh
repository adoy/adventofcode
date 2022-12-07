#!/bin/bash
awk 'BEGIN{len=-1}$2=="cd"{if($3==".."){print size[len];size[len--]=0}else len++} $1~/[0-9]+/ {for(i=0;i<=len;size[i++]+=$1);}END{do print size[len];while(len--)}' | tac | awk 'NR==1{r=(30000000-(70000000-$1))} $1>r {print $1}' | sort -nr | tail -1
