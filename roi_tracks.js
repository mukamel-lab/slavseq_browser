// Configure the ROI tracks. 
// TODO: Better color palette...
export const all_roi_tracks = [
  {
    "name": 'Non-reference germline L1 insertions (KNRGL called by Megane)',
    'url': "./rois/KNRGL_alldonors_megane.merged.bed",
    'indexed': false,
    'color': "rgba(99,99,99,0.5)",
    'format': 'bed',
    'tracktype':'ROI',
  },
  {
    'name': 'Filtered peaks',
    'url': './rois/allcells_max_q30.filtered.ForIGV.bed',
    'indexed': false,
    'color': "rgba(32,128,1,0.5)",
    'format': 'bed',
    'tracktype':'ROI'
  },
  {
    'name': 'Disc peaks (remove KNRGL,RefL1HS,PolyA)',
    'url': './rois/allcells_max_q30.R1_discordant.noKNRGL_noRefL1HS_slop60kb.noPolyA_2kb.bed',
    'indexed': false,
    'color': "rgba(94,94,1,0.5)",
    'format': 'bed',
    'tracktype':'ROI'
  },
  {
    name: "Apua's peak calls",
    'url': './rois/apua_calls_chm13.bed',
    'indexed': false,
    'color': "rgba(1,1,255,0.5)",
    'format': 'bed',
    'tracktype':'ROI'
  }];