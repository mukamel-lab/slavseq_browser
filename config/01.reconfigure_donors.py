# Set up configuration .tsv file listing all of the "bulk" pileup and bam tracks
#

import pandas as pd
import os

basedir = "/mysqlpool/emukamel/SLAVSeq_SZ/slavseq_browser"

df = pd.read_csv(f"{basedir}/config/donors_IGVconfig.csv", 
                 index_col=0
                )
df=df.iloc[:,:12]
df=df.loc[(df['tissue']!='CBN')&(df['tissue']!='DURA')]

df_cbn=[]
for i, dfi in df.iterrows():
  donor_num = dfi.donor[1:]
  bulk_slavseq_path = f"data/bigwig/BulkSLAVseq/gDNA_usd{donor_num}.tagged.sorted.R1.q30.sorted_thresh3.bw"
  if os.path.exists(f"{basedir}/{bulk_slavseq_path}") and (dfi.tissue == "DLPFC"):
    df.loc[i, "AllDonors_BulkSLAVseq_pileup_path"] = bulk_slavseq_path
  else:
    df.loc[i, "AllDonors_BulkSLAVseq_pileup_path"] = ""
  
  bulk_WGS_path = f"data/bigwig/BulkWGS/D{donor_num}.{dfi.tissue}.BulkWGS_disc_q30.bigwig"
  if os.path.exists(f"{basedir}/{bulk_WGS_path}"):
      df.loc[i, f"AllDonors_BulkWGS_pileup_path"] = bulk_WGS_path
  else:
      df.loc[i, f"AllDonors_BulkWGS_pileup_path"] = ""
            
  tissue='DURA'  
  bulk_WGS_path = f"data/bigwig/BulkWGS/D{donor_num}.{tissue}.BulkWGS_disc_q30.bigwig"
  if (dfi.tissue=='DLPFC') and os.path.exists(f"{basedir}/{bulk_WGS_path}"):
    df_cbnu=dfi.copy()
    df_cbnu['tissue']='DURA'
    df_cbnu['AllDonors_BulkWGS_pileup_path'] = bulk_WGS_path
    df_cbnu['color']='rgb(100,100,100)'
    df_cbnu['AllDonors_BulkMaxSingleCells_pileup_path']=""
    df_cbnu['AllDonors_BulkSLAVseq_pileup_path'] = ''
    df_cbn.append(df_cbnu)

df_cbn=pd.DataFrame(df_cbn)
df=pd.concat((df,df_cbn))
df=df.reset_index()

for modality in ['WGS','SLAVseq']:
  df[f'AllDonors_Bulk{modality}_bam_path']=''
  for i, dfi in df.iterrows():
    donor_num = dfi.donor[1:]
    # Check for BAM files    
    tissue=dfi['tissue']
    bulk_bam=f'data/bam/Bulk{modality}/D{donor_num}.{tissue}.Bulk{modality}.bam'
    if os.path.exists(f'{basedir}/{bulk_bam}'):
      df.loc[i,f'AllDonors_Bulk{modality}_bam_path']=bulk_bam
    else:
      print(f'File does not exist: {basedir}/{bulk_bam}')

df[f'AllDonors_BulkMaxSingleCells_pileup_path']=''
for i, dfi in df.iterrows():
  donor_num = dfi.donor[1:]
  # path=f'data/bigwig/BulkMaxSingleCells/R1_discordant_q30.Donor{donor_num}_{dfi.tissue}_discordant.max.bw'
  path=f'data/bigwig/BulkMaxSingleCells/Donor{donor_num}_{dfi.tissue}_R1.max.bw'
  if os.path.exists(f'{basedir}/{path}'):
    df.loc[i,f'AllDonors_BulkMaxSingleCells_pileup_path']=path
  else:
    df.loc[i,f'AllDonors_BulkMaxSingleCells_pileup_path']=''
      
df.to_csv(f"{basedir}/config/donors_IGVconfig.csv",index=False)
