df = pd.read_csv("../config/donors_IGVconfig.csv", index_col=0)

basedir='/mysqlpool/emukamel/SLAVSeq_SZ/slavseq_browser'
for i,dfi in df.iterrows():
  donor_num=dfi.donor[1:]
  bulk_slavseq_path=f'data/AllDonors_BulkSLAVseq/gDNA_usd{donor_num}.tagged.sorted.R1_discordant.q30.sorted.bigwig'
  if os.path.exists(
    f"{basedir}/{bulk_slavseq_path}"
  ) and (dfi.tissue=='DLPFC'):
    print(f"Found pileup for {donor_num}")
    df.loc[i,'AllDonors_BulkSLAVseq_path'] = bulk_slavseq_path
  else:
    df.loc[i,'AllDonors_BulkSLAVseq_path'] = ''
    
  # bulk_WGS_path==f'data/AllDonors_BulkWGS/gDNA_usd{donor_num}.tagged.sorted.R1_discordant.q30.sorted.bigwig'

df.to_csv('../config/donors_IGVconfig.csv')