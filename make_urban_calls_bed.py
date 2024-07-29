#!/usr/bin/env python
# Created on: Jul 23, 2024 at 4:03:56 PM
__author__ = 'Michael Cuoco'

import pandas as pd
import pyranges as pr
from pyliftover import LiftOver

# Correcting the columns and data to match for each group

# Case Data
case_columns = ["Donor", "Chr", "Coordinate", "Genes", "pL1", "Brain Disease", "SCZ GIFtS", "SCZ Score", "Note"]
case_data = [
    ["LIBD74", 2, 116889917, None, None, None, None, None, "In both"],
    ["LIBD74", 5, 57807232, None, None, None, None, None, None],
    ["LIBD80", "X", 148370927, None, None, None, None, None, None],
    ["LIBD106", 1, 198094727, None, None, None, None, None, None],
    ["LIBD106", 10, 4633926, "MANCR", "NA", "NA", None, None, None],
    ["LIBD106", 12, 40791495, "MUC19", "NA", "NA", None, None, None],
    ["LIBD106", 13, 49520843, None, None, None, None, None, None],
    ["LIBD106", 19, 47024362, "PPP5D1", "NA", "NA", None, None, None],
    ["LIBD107", 5, 137014723, "KLHL3", 1, "Cerebral Palsy", 50, 51.84, None],
    ["LIBD107", 10, 73389926, "CDH23", 0, None, 53, 95.2, None],
    ["LIBD120", 4, 24557664, "DHX15", 1, "X-Linked Intellectual Disability-Short Stature-Overweight Syndrome", 46, 53.02, None]
]

# Control Data
control_columns = ["Donor", "Chr", "Coordinate", "Genes", "pL1", "Brain Disease", "SCZ GIFtS", "SCZ Score"]
control_data = [
    ["LIBD96", 3, 176703905, None, None, None, None, None],
    ["LIBD96", 4, 159415250, None, None, None, None, None],
    ["LIBD96", 5, 71775314, "ZNF366", 0.2, None, "NA", "NA"],
    ["LIBD96", 20, 24193443, "LINCO1721(RNA)", None, None, "NA", "NA"],
    ["LIBD101", 3, 130861246, "NEK11", 0, None, "NA", "NA"],
    ["LIBD101", 13, 51589975, "GUCY1B2(RNA)", None, None, 37, 28.47],
    ["LIBD101", 18, 73290618, None, None, None, None, None],
    ["LIBD104", 1, 120507635, "NOTCH2", 1, "Enhancer", 60, 115.24],
    ["LIBD104", 3, 77923659, None, None, None, None, None],
    ["LIBD104", 5, 59590825, "PDE4D", 1, None, 57, 70.32],
    ["LIBD104", 9, 125414192, None, None, None, None, None],
    ["LIBD104", 12, 43364291, None, None, None, None, None]
]

# Case Fraction Data
case_frac_columns = ["Donor", "Chr", "Coordinate", "Genes", "pL1", "Brain Disease", "SCZ GIFtS", "SCZ Score", "Note"]
case_frac_data = [
    ["LIBD74", 2, 116889994, None, None, None, None, None, "In both"],
    ["LIBD74", 5, 14809914, "ANKH", 0.28, "Glioblastoma", 48, 76.22, None],
    ["LIBD80", 7, 9989509, None, None, None, None, None, None],
    ["LIBD82", 16, 13068908, "SHISA9", 0.87, None, 36, 35.01, None],
    ["LIBD82", 2, 15820689, None, None, None, None, None, None],
    ["LIBD82", 5, 165990053, None, None, None, None, None, None],
    ["LIBD82", 5, 70984398, None, None, None, None, None, None],
    ["LIBD82", 2, 173176865, None, None, None, None, None, None],
    ["LIBD98", 7, 119446560, None, None, None, None, None, None],
    ["LIBD100", 12, 27696644, "PPFIBP1", 0, "Neurodevelopmental Disorder With Seizures, Microcephaly, And Brain Abnormalities", 50, 36.54, None],
    ["LIBD106", 10, 63853367, "ARID5B (Exon)", 1, None, 47, 57.36, None]
]

# Control Fraction Data
control_frac_columns = ["Donor", "Chr", "Coordinate", "Genes", "pL1", "Brain Disease", "SCZ GIFtS", "SCZ Score", "Note"]
control_frac_data = [
    ["LIBD77", 3, 114717327, "ZBTB20", 0.97, "Major Depressive Disorder", 49, 59.3, None],
    ["LIBD87", 3, 79461998, "ROBO1", 0, "Neurooculorenal Syndrome", 52, 69.24, None],
    ["LIBD87", 7, 52603982, None, None, None, None, None, None],
    ["LIBD87", 19, 17687724, "COLGALT1", 0, None, None, None, None],
    ["LIBD99", 7, 61872426, None, None, None, None, None, None],
    ["LIBD99", 11, 79173809, None, None, None, None, None, None],
    ["LIBD99", 13, 79798403, None, None, None, None, None, None],
    ["LIBD99", 7, 61871310, None, None, None, None, None, None],
    ["LIBD104", 5, 29493119, None, None, None, None, None, None],
    ["LIBD105", 16, 61784772, "CDH8", None, None, 48, 37.17, None],
    ["LIBD113", 4, 84517333, None, None, None, None, None, None],
    ["LIBD113", 4, 79033217, "FRAS1", None, None, None, None, None],
    ["LIBD122", "X", 116958469, None, None, None, None, None, None],
    ["LIBD122", 7, 52790083, None, None, None, None, None, None]
]

data = pd.concat([pd.DataFrame(case_data, columns=case_columns),
		   pd.DataFrame(control_data, columns=control_columns),
		   pd.DataFrame(case_frac_data, columns=case_frac_columns),
		   pd.DataFrame(control_frac_data, columns=control_frac_columns)])
data.reset_index(drop=True, inplace=True)

# liftover to chm
print("Lifting over from hg19 to chm13v2")
lo = LiftOver('data/hg19-chm13v2.chain')
lifted = [lo.convert_coordinate("chr"+str(row["Chr"]), row["Coordinate"]) for _, row in data.iterrows()]

# remove entries that didnt liftover
missing = [i for i, x in enumerate(lifted) if len(x) == 0]
lifted = [x for i, x in enumerate(lifted) if i not in missing]
data = data.drop(index=missing)

# update remaining to new coordinates
data["Chromosome"] = [x[0][0] for x in lifted]
data["Start"] = [x[0][1] for x in lifted]
data["End"] = data["Start"] + 1

# add metadata
samples = (
	pd.read_csv("data/slavseq_metadata.csv")
	.set_index("libd_id")[["donor"]]
	.drop_duplicates()
)

data = (
	data
	.join(samples, on="Donor")
	.sort_values(["Chromosome","Start"])
	.reset_index(drop=True)
	.rename(columns={"donor":"Name", "pL1": "Score"})
)
    
outfile="rois/urban_L1.chm13.bed"
print(f"Saving to {outfile}")
pr.PyRanges(data[["Chromosome","Start","End","Name","Score"]]).to_bed(outfile)