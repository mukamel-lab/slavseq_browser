#!/usr/bin/env python
# Created on: Jul 25, 2024 at 9:09:14 AM
__author__ = 'Michael Cuoco'

import matplotlib as mpl,numpy as np
from itertools import cycle
import pandas as pd

samples = pd.read_csv("data/slavseq_metadata.csv")
samples.sort_values(['diagnosis','race','donor','tissue','age','diagnosis','race'], inplace=True)
samples["Linking_id"] = samples["sample"]
samples['donor']= samples['donor'].astype(str)+' ('+samples['diagnosis']+' '+samples['race']+')'
samples.reset_index(inplace=True)
samples['order']=samples.index

with open('data/sampletable.tsv','w') as f:
  f.write('#sampleTable\n')
  samples[['Linking_id','donor','tissue','age','race','diagnosis','order']].to_csv(f,mode='a',sep='\t',index=False)
  f.write('\n')
  f.write('#colors\n')
  colors=cycle(list(mpl.cm.tab20.colors))
  for color,d in zip(colors, samples.donor.unique()):    
    mycolor=np.array(color)*256
    mycolor=mycolor.astype(int).astype(str)
    mycolor=','.join(mycolor)
    f.write(f'{d} {mycolor}\n')
    
print("Sample table created successfully at data/sampletable.tsv!")