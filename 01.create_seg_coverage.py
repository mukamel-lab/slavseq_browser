import pandas as pd 
from glob import glob
from tqdm import tqdm
import re,os

# files=glob('/mysqlpool/emukamel/SLAVSeq_SZ/allsamples/Merged_SingleCells/peak_coverage_q30/*.allcells_max_peaks.R1_disc.bed.gz')
samples=pd.read_csv('/mysqlpool/emukamel/SLAVSeq_SZ/IGV/slavseq_metadata.csv')
samples=samples[~samples['is_bulk']]

datadir='/mysqlpool/emukamel/SLAVSeq_SZ/allsamples/Merged_SingleCells/peak_coverage_q30'
dfs=[]
for i,sample in tqdm(samples.iterrows(),total=samples.shape[0]):
  df=pd.read_csv(f'{datadir}/{sample["sample"]}.q30.allcells_max_peaks.R1_disc.bed.gz',sep='\t',
                 names=['id_bin','Segment_Mean'])
  df['Sample']='D'+str(sample['donor'])+'_'+sample['tissue']+'_'+sample["sample"]
  dfs.append(df)

df=pd.concat(dfs)

def id2loc(id_bin):
  re.match(r'chr.*:[0-9]+_.*',id_bin)
  
  

df['Chromosome']=df['id_bin'].str.extract(r'(chr.*):[0-9]+_.*$')
df['locus']=df['id_bin'].str.extract(r'chr.*:([0-9]+)_.*$').astype(int)
df['bin']=df['id_bin'].str.extract(r'chr.*:[0-9]+_(.*)$').astype(int)
binsize=1000
df['bin']=(df['bin']-20)*binsize


df['Start']=df['locus']+df['bin']
df['End']=df['locus']+df['bin']+binsize
df['NumProbes']=1

df=df.sort_values(['Chromosome','Start'])

df[['Sample','Chromosome','Start','End','NumProbes','Segment_Mean']].to_csv('/mysqlpool/emukamel/SLAVSeq_SZ/IGV/data/allcells.peaks.R1_disc_q30.seg',sep='\t',index=False)

os.system('bgzip data/allcells.peaks.R1_disc_q30.seg')
os.system('tabix -b3 -e4 -s2 -S1 -f data/allcells.peaks.R1_disc_q30.seg.gz')

# Create sample table
samples=pd.read_csv('/mysqlpool/emukamel/SLAVSeq_SZ/IGV/slavseq_metadata.csv')
samples=samples[~samples['is_bulk']]

samples['Linking_id']='D'+samples['donor'].astype(str)+'_'+samples['tissue']+'_'+samples['sample']
samples=samples.sort_values(['diagnosis','race','donor','tissue','age','diagnosis','race'])
samples['donor']='D'+samples['donor'].astype(str)+' ('+samples['diagnosis']+' '+samples['race']+')'
samples=samples.reset_index()
samples['order']=samples.index

import matplotlib as mpl,numpy as np
from itertools import cycle

with open('/mysqlpool/emukamel/SLAVSeq_SZ/IGV/data/sampletable.tsv','w') as f:
  f.write('#sampleTable\n')
  samples[['Linking_id','donor','tissue','age','race','diagnosis','order']].to_csv(f,mode='a',
                                                                         sep='\t',index=False)
  f.write('\n')
  f.write('#colors\n')
  colors=cycle(list(mpl.cm.tab20.colors))
  for color,d in zip(colors, samples.donor.unique()):    
    mycolor=np.array(color)*256
    mycolor=mycolor.astype(int).astype(str)
    mycolor=','.join(mycolor)
    f.write(f'{d} {mycolor}\n')

# #colors
# *	Classical	80,180,80
# *	Neural	10,150,220
# *	Proneural	110,35,180
# *	Mesenchymal	50,75,120
# GENDER	MALE	0,0,155
# GENDER	FEMALE	70,150,70
# Secondary or Recurrent	NO	0,0,100
# Secondary or Recurrent	REC	200,0,0
# Secondary or Recurrent	Sec	230,150,25
# Secondary or Recurrent	NA	150,150,150
# sil_width	-0.1:0.5	0,0,255	255,0,0
# KarnScore	*	0,0,255
# AgeAtFirstDiagnosis	*	0,0,255
# Survival (days)	*	0,0,255
# MGMT_methylated	Methylated	200,0,0
# % Tumor Nuclei	80:100	0,0,255
# % Necrosis	*	0,0,255
