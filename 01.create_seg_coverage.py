#
# Prepare data file for heatmap

import pandas as pd
from glob import glob
from tqdm import tqdm
import re,os
from multiprocessing import Pool

# files=glob('/mysqlpool/emukamel/SLAVSeq_SZ/allsamples/Merged_SingleCells/peak_coverage_q30/*.allcells_max_peaks.R1_disc.bed.gz')
samples=pd.read_csv('config/slavseq_metadata.csv')
samples=samples[~samples['is_bulk']]

# Use the full bam file
# datadir=f'/mysqlpool/emukamel/SLAVSeq_SZ/allsamples/SingleCells/bed_coverage_donor_q30_bins1kb_coverage{min_coverage}'
datadir=f'/mysqlpool/emukamel/SLAVSeq_SZ/allsamples/SingleCells_R1/bed_coverage_donor_q30_bins1kb'
dfs=[]
def loadsample(i, samples=samples):
  sample=samples.iloc[i]
  bedfile=f'{datadir}/{sample["sample"]}.q30_R1_disc_bins1kb_withZeros.bed.gz'
  assert os.path.exists(bedfile), f'File does not exists: {bedfile}'
  df=pd.read_csv(bedfile,sep='\t',
                names=['Chromosome','Start','End','Segment_Mean'])
  df=df[df['End']>df['Start']]
  df['Sample']=str(sample['donor'])+'_'+sample['tissue']+'_'+sample["sample"]
  return df

with Pool() as p:
  dfs=list(tqdm(p.imap(loadsample, range(samples.shape[0])), 
                total=samples.shape[0],
                desc="Loading binned coverages"))

min_coverage=0
df=pd.concat(dfs)
df['NumProbes']=1
df=df.sort_values(['Chromosome','Start'])

for min_coverage in [0,1,3,5]:
  dfu=df[df['Segment_Mean']>=min_coverage]
  dfu[['Sample','Chromosome','Start','End','NumProbes','Segment_Mean']].to_csv(f'data/allcells_q30_R1_disc_bins1kb_coverage{min_coverage}.seg',sep='\t',index=False)

  os.system(f'bgzip -f data/allcells_q30_R1_disc_bins1kb_coverage{min_coverage}.seg')
  os.system(f'tabix -b3 -e4 -s2 -S1 -f data/allcells_q30_R1_disc_bins1kb_coverage{min_coverage}.seg.gz')

# Make a filtered file with just the bins that have coverage≥5 reads
#os.system('zcat allcells_q30_R1_disc_bins1kb.seg.gz | awk '(NR==1)||($6>=5)'| bgzip > data/allcells_q30_R1_disc_bins1kb.coverage5.seg.gz')
#os.system('tabix -b3 -e4 -s2 -S1 -f data/allcells_q30_R1_disc_bins1kb.coverage5.seg.gz')

