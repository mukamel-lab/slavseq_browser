#!/usr/bin/env bash
# Author: Mike Cuoco
# Created on: 7/19/24, 4:05 PM
#
# Description: convert bam files to a single seg file

# exit if any non-zero, exit if undefined var
set -euo pipefail

# check if windows file exists
if [ ! -f data/chm13v2.0.XY.fasta.200bp.windows.bed ]; then
	echo "making 200bp windows across the genome with bedtools makewindows"
	bedtools makewindows -g data/chm13v2.0.XY.fasta.genome -w 200 > data/chm13v2.0.XY.fasta.200bp.windows.bed
fi

# define a function
function make_cov_bed {
	local BAM=$1
	echo "Running getting coverage from $BAM"
	samtools view -F 1408 -q 30 $BAM -b | \
		bedtools coverage -a data/chm13v2.0.XY.fasta.200bp.windows.bed -b stdin -counts -bed | \
		awk -v OFS='\t' -v sample="$(basename -s .tagged.sorted.bam $BAM)" '{if ($4 >= 5) print sample, $1, $2, $3, 1, $4}' > "${BAM%.tagged.sorted.bam}.q30_R1_bins200bp.coverage5.bed"
}
export -f make_cov_bed

function make_seg {
	local BAMS=$1
	local OUT=$2
	echo $BAMS | tr ' ' '\n' | xargs -n 1 -P 8 bash -c 'make_cov_bed $0'
	local REGIONS=$(echo "$BAMS" | xargs -n 1 bash -c 'echo "${0%.tagged.sorted.bam}.q30_R1_bins200bp.coverage5.bed"')	
	printf "Sample\tChromosome\tStart\tEnd\tNumProbes\tSegment_Mean\n" > $OUT # add header
	cat $REGIONS >> $OUT
	head -n 1 $OUT > ${OUT%.seg}.sorted.seg
	tail -n +2 $OUT | sort -k2,2 -k3,3n -k4,4n >> ${OUT%.seg}.sorted.seg
	rm -f $OUT
	bgzip -f ${OUT%.seg}.sorted.seg
	tabix -s2 -b3 -e4 -S1 -f ${OUT%.seg}.sorted.seg.gz
}
export -f make_seg

# make seg for bulk
BAMS=$(find /mysqlpool/mcuoco/for_igv/slavseq -type f -name "gDNA*.tagged.sorted.bam")
make_seg "$BAMS" data/bulk_q30_R1_bins200bp.coverage5.seg

# make seg for cells
BAMS=$(find /mysqlpool/mcuoco/for_igv/slavseq -type f -name "*tagged.sorted.bam" -not -name "*gDNA*")
make_seg "$BAMS" data/allcells_q30_R1_bins200bp.coverage5.seg
