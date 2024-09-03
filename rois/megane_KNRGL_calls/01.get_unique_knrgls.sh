#!/bin/bash

for d in {1..48}; do
 donor=D${d}
 if [[ -e ${donor}.HIP.megane_final_gaussian.L1HS.bed ]]; then
 cat ${donor}.HIP.megane_final_gaussian.L1HS.bed ${donor}.DURA.megane_final_gaussian.L1HS.bed | \
  bedtools sort -i - | \
  bedtools merge -i - | \
  bedtools subtract -A -a ${donor}.DLPFC.megane_final_gaussian.L1HS.bed -b - | \
  awk '{print $1":"($2-10000)"-"($2+10000);}' > KNRGL_subsets/DLPFC_only/${donor}.DLPFC_only.igv

 cat ${donor}.DLPFC.megane_final_gaussian.L1HS.bed ${donor}.DURA.megane_final_gaussian.L1HS.bed | \
  bedtools sort -i - | \
  bedtools merge -i - | \
  bedtools subtract -A -a ${donor}.HIP.megane_final_gaussian.L1HS.bed -b - | \
  awk '{print $1":"($2-10000)"-"($2+10000);}' > KNRGL_subsets/HIP_only/${donor}.HIP_only.igv

 cat ${donor}.DLPFC.megane_final_gaussian.L1HS.bed ${donor}.HIP.megane_final_gaussian.L1HS.bed | \
  bedtools sort -i - | \
  bedtools merge -i - | \
  bedtools subtract -A -a ${donor}.DURA.megane_final_gaussian.L1HS.bed -b - | \
  awk '{print $1":"($2-10000)"-"($2+10000);}' > KNRGL_subsets/DURA_only/${donor}.DURA_only.igv

 cat ${donor}.DLPFC.megane_final_gaussian.L1HS.bed ${donor}.HIP.megane_final_gaussian.L1HS.bed | \
  bedtools sort -i - | \
  bedtools merge -i - | \
  bedtools subtract -A -b ${donor}.DURA.megane_final_gaussian.L1HS.bed -a - | \
  awk '{print $1":"($2-10000)"-"($2+10000);}' > KNRGL_subsets/Brain_only/${donor}.Brain_only.igv
  fi
done