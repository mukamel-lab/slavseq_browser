import pandas as pd
from glob import glob
from tqdm import tqdm
import re,os
from multiprocessing import Pool

# files=glob('/mysqlpool/emukamel/SLAVSeq_SZ/allsamples/Merged_SingleCells/peak_coverage_q30/*.allcells_max_peaks.R1_disc.bed.gz')
samples=pd.read_csv('config/slavseq_metadata.csv')
samples=samples[~samples['is_bulk']]

###########
# Create sample table
samples=pd.read_csv('config/slavseq_metadata.csv')
samples=samples[~samples['is_bulk']]

samples['Linking_id']=samples['donor'].astype(str)+'_'+samples['tissue']+'_'+samples['sample']
samples=samples.sort_values(['diagnosis','race','donor','tissue','age'])
samples['donor']=samples['donor'].astype(str)+' ('+samples['diagnosis']+' '+samples['race']+')'
samples=samples.reset_index()
samples['order']=samples.index

import matplotlib as mpl,numpy as np
from itertools import cycle

with open('config/sampletable.tsv','w') as f:
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
