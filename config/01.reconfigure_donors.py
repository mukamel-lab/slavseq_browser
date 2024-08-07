import pandas as pd
import os

basedir = "/mysqlpool/emukamel/SLAVSeq_SZ/slavseq_browser"

df = pd.read_csv(f"{basedir}/config/donors_IGVconfig.csv", 
                 index_col=0
                )
df=df.iloc[:,:13]
df=df.loc[df['tissue']!='CBN']

df_cbn=[]
for i, dfi in df.iterrows():
  donor_num = dfi.donor[1:]
  bulk_slavseq_path = f"data/bigwig/BulkSLAVseq/gDNA_usd{donor_num}.tagged.sorted.R1_discordant.q30.sorted.bigwig"
  if os.path.exists(f"{basedir}/{bulk_slavseq_path}") and (dfi.tissue == "DLPFC"):
    print(f"Found pileup for BulkSLAVseq D{donor_num}")
    df.loc[i, "AllDonors_BulkSLAVseq_path"] = bulk_slavseq_path
  else:
    df.loc[i, "AllDonors_BulkSLAVseq_path"] = ""
  
  for region in ["DLPFC", "HIP"]:
    bulk_WGS_path = f"data/bigwig/BulkWGS/D{donor_num:02}.{region}.BulkWGS_disc_q30.bigwig"
    if os.path.exists(f"{basedir}/{bulk_WGS_path}") and (dfi.tissue == region):
        print(f"Found pileup for BulkWGS D{donor_num} {region}: {bulk_WGS_path}")
        df.loc[i, f"AllDonors_BulkWGS_path"] = bulk_WGS_path
        break
    else:
        df.loc[i, f"AllDonors_BulkWGS_path"] = ""
  region='CBN'  
  bulk_WGS_path = f"data/bigwig/BulkWGS/D{donor_num:02}.{region}.BulkWGS_disc_q30.bigwig"
  if (dfi.tissue=='DLPFC') and os.path.exists(f"{basedir}/{bulk_WGS_path}"):
    print(f"Found pileup for BulkWGS D{donor_num} {region}")
    df_cbnu=dfi.copy()
    df_cbnu['tissue']='CBN'
    df_cbnu['AllDonors_BulkWGS_path'] = bulk_WGS_path
    df_cbnu['color']='rgb(0,100,255)'
    df_cbnu['AllDonors_MaxSingleCells_path']=""
    df_cbn.append(df_cbnu)

df_cbn=pd.DataFrame(df_cbn)
df=pd.concat((df,df_cbn))
df=df.reset_index()

df.to_csv(f"{basedir}/config/donors_IGVconfig.csv",index=False)
