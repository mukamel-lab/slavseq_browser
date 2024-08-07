import pandas as pd
import os

df = pd.read_csv("./config/donors_IGVconfig.csv", index_col=0)

basedir = "/mysqlpool/emukamel/SLAVSeq_SZ/slavseq_browser"
for i, dfi in df.iterrows():
    donor_num = dfi.donor[1:]
    bulk_slavseq_path = f"data/AllDonors_BulkSLAVseq/gDNA_usd{donor_num}.tagged.sorted.R1_discordant.q30.sorted.bigwig"
    if os.path.exists(f"{basedir}/{bulk_slavseq_path}") and (dfi.tissue == "DLPFC"):
        print(f"Found pileup for {donor_num}")
        df.loc[i, "AllDonors_BulkSLAVseq_path"] = bulk_slavseq_path
    else:
        df.loc[i, "AllDonors_BulkSLAVseq_path"] = ""

        for region in ["CBN", "DLPFC", "HIP"]:
            bulk_WGS_path = f"data/AllDonors_BulkWGS/D{donor_num:02}.{region}.BulkWGS_disc_q30.bigwig"
            if os.path.exists(f"{basedir}/{bulk_WGS_path}") and (dfi.tissue == "DLPFC"):
                print(f"Found pileup for {donor_num}")
                df.loc[i, f"AllDonors_BulkWGS_{region}_path"] = bulk_WGS_path
            else:
                df.loc[i, f"AllDonors_BulkWGS_{region}_path"] = ""

df.to_csv("./config/donors_IGVconfig.csv")
