from glob import glob
import re

# export const all_roi_tracks = [
  
tracks='''
  {
    "name": 'Non-reference germline L1 insertions (KNRGL called by Megane)',
    'url': "./rois/megane_KNRGL_calls/AllDonors.KNRGL_merged.slop0.bed",
    'indexed': false,
    'color': "rgba(99,99,99,0.5)",
    'format': 'bed',
    'tracktype':'ROI',
    'donor':'all',
    'tissue':'all'
  },
  {
    name: "Our insertion calls using all scSLAVseq R1 reads",
    'url': './rois/scL1_inserts_calls.bed',
    'indexed': false,
    'color': "rgba(1,1,255,0.5)",
    'format': 'bed',
    'tracktype':'ROI',
    'donor':'all',
    'tissue':'all'
  },
  {
    name: "All of our peaks",
    'url': './rois/AllDonors.q30_mindist40000_thresh3.filtered.no_rmsk.bed',
    'indexed': false,
    'color': "rgba(1,1,255,0.5)",
    'format': 'bed',
    'tracktype':'ROI',
    'donor':'all',
    'tissue':'all'
  },
  {
    name: "Apua's peak calls",
    'url': './rois/apua_calls_chm13.bed',
    'indexed': false,
    'color': "rgba(1,1,255,0.5)",
    'format': 'bed',
    'tracktype':'ROI',
    'donor':'all',
    'tissue':'all'
  },
  {
    "name": 'L1 insertions called by Alex Urban',
    'url': "./rois/urban_L1.chm13.bed",
    'indexed': false,
    'color': "rgba(99,99,99,0.5)",
    'format': 'bed',
    'tracktype':'ROI',
    'donor':'all',
    'tissue':'all'
  },
  '''


  # {
  #   'name': 'Filtered peaks',
  #   'url': './rois/allcells_max_q30.filtered.ForIGV.bed',
  #   'indexed': false,
  #   'color': "rgba(32,128,1,0.5)",
  #   'format': 'bed',
  #   'tracktype':'ROI',
  #   'donor':'all',
  #   'tissue':'all'
  # },
  # {
  #   'name': 'Disc peaks (remove KNRGL,RefL1HS,PolyA)',
  #   'url': './rois/allcells_max_q30.R1_discordant.noKNRGL_noRefL1HS_slop60kb.noPolyA_2kb.bed',
  #   'indexed': false,
  #   'color': "rgba(94,94,1,0.5)",
  #   'format': 'bed',
  #   'tracktype':'ROI',
  #   'donor':'all',
  #   'tissue':'all'
  # },
  
megane_knrgls=glob('megane_KNRGL_calls/D*.bed')
for bed in megane_knrgls:
  donor=re.match(r'megane_KNRGL_calls/(D[0-9]+).*',bed).group(1)
  tissue=re.match(r'megane_KNRGL_calls/D[0-9]+\.(DURA|DLPFC|HIP)',bed).group(1)
  newtrack=('{\n'
            f'"name": "{donor} {tissue} KNRGL (MEGANE)",\n'
            f'"url":"./rois/{bed}",\n'
            '"indexed":false,\n'
            '"format":"bed",\n'
            '"tracktype":"ROI",\n'
            f'"donor":"{donor}",\n'
            f'"tissue":"{tissue}",\n'
            '"order":4.9,\n"height":25\n'
            '},\n'
            )
  tracks+=newtrack
            
tracks=tracks[:-2]
tracks='export const all_roi_tracks = ['+tracks
tracks+='];'

with open('../roi_tracks.js','w') as file:
  file.write(tracks)