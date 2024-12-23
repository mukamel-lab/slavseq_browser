<head>
  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-1T4FCX4DFN"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());

    gtag('config', 'G-1T4FCX4DFN');
  </script>

  <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
  <title>SLAV-Seq data browser</title>
  <!-- <link type='text/css' rel='stylesheet' href='browser/css/navbar.css' /> -->

  <meta charset="utf-8">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
  <link rel="stylesheet" href="./css/bootstrap-select.css">

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
  <script src="./js/bootstrap-select.js"></script>
  <script type='text/javascript' src='./js/html2canvas.min.js'></script>
  <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
  <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

  <style type="text/css">
    select.selectpicker {
      display: none;
    }

    .dropdown-menu {
      max-height: 400px;
      overflow-y: scroll;
    }

    .btn {
      /* padding: 2px 4px; */
      font-size: 10pt;
    }

    .toggle.btn {
      min-height: 0px;
    }

    div {
      text-align: center;
    }

    .div1 {
      float: left;
      padding: 8px 8px 8px 32px;
    }

    .nav-item {
      margin: 5;
    }

    .igv-track-label {
      font-size: 7px;
      /* Adjust this value to your desired size */
    }
  </style>
  <script src="js/fontawesome.js"></script>
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/solid.min.css"> -->
  <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/solid.min.js"></script> -->

  <?php
  include './loadCsv.php';
  loadCsv('config/donors2libd.csv', 'donors2libd');
  loadCsv('config/donors_IGVconfig.csv', 'donors_tissues');
  loadCsv('config/slavseq_metadata.csv', 'cells');
  ?>

</head>

<body id="top">
  <nav class="navbar navbar-default" style="margin:0;">
    <div class="container-fluid">
      <ul class="nav navbar-nav">
        <li class="nav-item">
          <button id="screenshotButton" class="btn btn-primary">
            <i class="fa fa-camera"></i> Screenshot</button>
        </li>
        <li class="nav-item">
          <button id="getLinkButton" class="btn btn-primary">
            <i class="fa fa-link" aria-hidden="true"></i> Copy shareable link</button>
          <input type="text" id="srcBox" size="1" style="visibility:hidden;"></input>
        </li>

        <li class="nav-item" id="select_donor_li">
          <select id="select_donor" class="selectpicker" data-selected-text-format="static" data-title="Donor(s)"
            multiple data-max-options="1" data-width="100%" data-toggle="tooltip" data-placement="top"
            data-header="Donor(s) to show" data-live-search="true">
            <option value="SingleCells_Heatmap"> All single cell SLAV-seq (heatmap)</option>
            <!-- <option value="BulkWGS_Heatmap"> All bulk WGS data (heatmap)</option> -->
            <option value="AllDonors_AllModalities"> All donors - Bulk+MaxSingleCells</option>
            <option value="AllDonors_BulkMaxSingleCells" selected> All donors - Max of single cell SLAV-seq</option>
            <option value="AllDonors_BulkSLAVseq"> All donors - Bulk SLAV-seq</option>
            <option value="AllDonors_BulkWGS"> All donors - Bulk WGS (discordant reads)</option>
          </select>
        </li>

        <li class="nav-item" id="select_tissue_li" style="width:none">
          <select id="select_tissue" class="selectpicker" multiple data-width="100%" data-toggle="tooltip"
            data-placement="top" title="Tissue" data-selected-text-format="static" data-title="Tissue"
            data-header="Tissues to show" data-actions-box="true">
            <option value="HIP"> Hippocampus</option>
            <option value="DLPFC"> Dorsolateral pre-frontal cortex</option>
            <option value="DURA"> Dura mater</option>
          </select>
        </li>

        <li class="nav-item" id="select_cells_li" style="display:none; width:none;">
          <!-- <label>Pileup track height:</label> -->
          <input class="selectpicker" id="pileup_height" value="20" style="width:50px; display:none;">
          <select id="select_cells_pileup" class="selectpicker" multiple data-width="100%" data-title="Pileups"
            title="Cell pileups" data-toggle="tooltip" data-placement="top" data-live-search="true"
            data-header="Cells to show" data-actions-box="true" data-selected-text-format="static">
            <option value="All" selected> All cells</option>
          </select>
        </li>
        <li class="nav-item" id="select_bams_li" style="display:none; width:none;">
          <select id="select_cells_bam" class="selectpicker" multiple data-width="100%" data-live-search="true"
            title="Alignments (BAM)" data-toggle="tooltip" data-placement="top" data-header="Cells to show"
            data-actions-box="true" data-selected-text-format="static">
            <option value="All" selected> All cells</option>
          </select>
        </li>

        <li class="nav-item donor-selection">
          <select id="select_rois" class="selectpicker" multiple data-width="100px" data-toggle="tooltip"
            data-placement="top" data-header="ROIs" data-actions-box="true" data-selected-text-format="static"
            data-title="ROIs">
          </select>
          <input class="btn" id="toggleROIs_btn" type="checkbox" data-toggle="toggle" data-off="Clear ROIs"
            data-on="Mark ROIs">
        </li>

        <li class="nav-item">
          <button id="btn_plus" class="btn btn-primary" title="Increase track size">
            <i class="fa fa-plus"></i>
          </button>
          <button id="btn_minus" class="btn btn-primary" title="Decrease track size">
            <i class="fa fa-minus"></i>
          </button>

          <input class="btn" id="toggleAutoscale_btn" type="checkbox" data-toggle="toggle" data-off="Fixed scale"
            data-on="Autoscale">
        </li>
      </ul>
    </div>


  </nav>

  <div id="igv-div"></div>

  <script type="module">

    import igv from "./js/igv.esm.EAM_mod.min.js"
    import { all_roi_tracks } from "./roi_tracks.js" // This loads the ROI tracks derived from all donors (e.g. merged KNRGLs)

    // // Add ROI tracks for each donor
    // const donors = donors_tissues.filter((d) => d.tissue == "HIP")
    // for (const donor of donors) {
    //   var roiTrack = {
    //     'name': "Peaks in " + donor.donor,
    //     'url': './data/peaks_per_donor/peaks_' + donor.donor + '.bed',
    //     'indexed': false,
    //     'color': "rgba(1,1,255,0.5)",
    //     'format': 'bed',
    //     'tracktype': 'ROI'
    //   };
    //   all_roi_tracks.push(roiTrack);
    // }

    var options =
    {
      genome: "hs1",
      queryParametersSupported: true,
      locus: "chr5:156,424,267-156,454,552",
      reference: {
        "id": "hs1",
        "blatDB": "hub_3671779_hs1",
        "name": "Human (T2T CHM13-v2.0/hs1)",
        // "fastaURL": "https://s3.amazonaws.com/igv.org.genomes/chm13v2.0/chm13v2.0.fa",
        // "indexURL": "https://s3.amazonaws.com/igv.org.genomes/chm13v2.0/chm13v2.0.fa.fai",
        // "cytobandURL": "https://s3.amazonaws.com/igv.org.genomes/chm13v2.0/CHM13_v2.0.cytoBandMapped.bed",
        // "aliasURL": "https://hgdownload.soe.ucsc.edu/goldenPath/hs1/bigZips/hs1.chromAlias.txt",
        // "twoBitURL": "https://hgdownload.soe.ucsc.edu/goldenPath/hs1/bigZips/hs1.2bit",
        // "twoBitBptURL": "https://hgdownload.soe.ucsc.edu/goldenPath/hs1/bigZips/hs1.2bit.bpt"
        "fastaURL": "./data/reference/GCF_009914755.1_T2T-CHM13v2.0_genomic.fna",
        "indexURL": "./data/reference/GCF_009914755.1_T2T-CHM13v2.0_genomic.fna.fai",
        "cytobandURL": "./data/reference/CHM13_v2.0.cytoBandMapped.bed",
        "aliasURL": "./data/reference/hs1.chromAlias.txt",
        "twoBitURL": "./data/reference/hs1.2bit",
        "twoBitBptURL": "./data/reference/hs1.2bit.bpt"
      },
      tracks: [
        // {
        //   "name": "Genes",
        //   "format": "bed",
        //   "url": "https://s3.amazonaws.com/igv.org.genomes/chm13v2.0/chm13v2.0_geneLocations.short.bed.gz",
        //   "hidden": false,
        //   "searchable": true,
        //   "order": 0,
        //   "type": "annotation",
        //   "height": 25
        // },
        {
          "id": "catLiftOffGenesV1",
          "name": "CAT/Liftoff Genes",
          "format": "bigbed",
          "description": " <a target = \"_blank\" href = \"https://hgdownload.soe.ucsc.edu/hubs/GCA/009/914/755/GCA_009914755.4/html/GCA_009914755.4_T2T-CHM13v2.0.catLiftOffGenesV1.html\">CAT + Liftoff Gene Annotations</a>",
          "url": "https://hgdownload.soe.ucsc.edu/hubs/GCA/009/914/755/GCA_009914755.4/bbi/GCA_009914755.4_T2T-CHM13v2.0.catLiftOffGenesV1/catLiftOffGenesV1.bb",
          "height": 25,
          "visibilityWindow": -1,
          "supportsWholeGenome": false,
          "order": 0.1,
          "type": "annotation",
          "displayMode": 'squish'
        },
        {
          'name': "RepeatMasker L1",
          'format': "bed",
          'type': 'annotation',
          'sourceType': "file",
          'displayMode': "expanded",
          'url': "data/L1HS.chm13v2.0_rmsk.reformat.bed.gz",
          'indexURL': "./data/L1HS.chm13v2.0_rmsk.reformat.bed.gz.tbi",
          'indexed': true,
          'order': 0.5,
        },
      ],
      "sampleinfo": [
        {
          "url": "./config/sampletable.tsv"
        }
      ]
    };

    for (const mytrack of all_roi_tracks.filter((x) => x.donor == 'all')) {
      mytrack.height = 30;
      mytrack.order = 4;
      // Make the color non-transparent
      if (mytrack.color) {
        mytrack.color = mytrack.color.replace(/,[0-9]+\)/i, ',0)');
      }
      // options.tracks.push(mytrack)
    }

    ////////////////////////////////////////////////
    // Functions for screenshot and links
    // Screenshot
    function myScreenshot() {
      html2canvas(document.body, {
        // scale: window.devicePixelRatio = 5,
        useCORS: true,
        allowTaint: true
      }).then(
        function (canvas) {
          saveAs(canvas.toDataURL(), 'IGV_Screenshot.png');
        }
      )
    }

    function saveAs(uri, filename) {
      var link = document.createElement('a');
      if (typeof link.download === 'string') {
        link.href = uri;
        link.download = filename;
        //Firefox requires the link to be in the body
        document.body.appendChild(link);
        //simulate click
        link.click();
        //remove the link when done
        document.body.removeChild(link);
      } else {
        window.open(uri);
      }
    }

    function getLink() {
      var blob = browser.compressedSession();
      var src = 'https://brainome.ucsd.edu/slavseq_browser/?sessionURL=blob:' + blob
      document.getElementById('srcBox').value = src;
      window.history.pushState("object or string", "", src);
    }

    function copyLink() {
      // Copy link to the current state to the clipboard

      /* Select the text field */
      var copySrc = document.getElementById('srcBox')
      copySrc.style.visibility = 'visible'; // The box must be visible to copy
      copySrc.select();
      copySrc.setSelectionRange(0, 99999); /*For mobile devices*/

      /* Copy the text inside the text field */
      document.execCommand("copy");
      copySrc.style.visibility = 'hidden';
    }

    function toggleROIs() {
      browser.roiManager.roiTable.setROIVisibility($('#toggleROIs_btn').prop('checked'));
    }

    function toggleAutoscale() {
      if ($('#toggleAutoscale_btn').prop('checked')) {
        for (var track of browser.tracks) {
          if ('dataRange' in track) { track.autoscale = true }
        }
        browser.updateViews();
      } else {
        for (var track of browser.tracks) {
          if ('dataRange' in track) {
            track.autoscale = false;
            track.dataRange['max'] = 20;
            // track.trackView.dataRange['max'] = 20;
          }
        }
        browser.repaintViews();
      }
    }

    ////////////////////////////////////
    // Functions for updating the dropdown lists

    async function updateDonors() {
      // Add each donor to the select_donor dropdown
      const donors = donors_tissues.filter((d) => d.tissue == "HIP")
      for (const donor of donors) {
        var option = document.createElement("option");
        option.text = donor.name;
        option.title = donor.donor;
        option.value = donor.donor;
        document.getElementById('select_donor').add(option);
      }
    }

    async function updateTracks() {
      // Show and hide the appropriate dropdown menus
      var donor = document.getElementById('select_donor').value;
      if (donor.startsWith('AllDonors_')) {
        document.getElementById('select_cells_li').style.display = 'none';
        document.getElementById('select_bams_li').style.display = 'none';
        document.getElementById('select_tissue_li').style.display = 'block';
      } else if (donor == 'SingleCells_Heatmap') {
        document.getElementById('select_cells_li').style.display = 'none';
        document.getElementById('select_bams_li').style.display = 'none';
        document.getElementById('select_tissue_li').style.display = 'none';
      } else {
        // Showing a specific donor
        document.getElementById('select_cells_li').style.display = 'block';
        document.getElementById('select_bams_li').style.display = 'block';
        document.getElementById('select_tissue_li').style.display = 'block';

        // var donor_rois = all_roi_tracks.filter((x) => (x.donor == donor) | (x.donor == 'all'))
        // for (const donor_roi of donor_rois) {
        //   var option = document.createElement("option");
        //   option.text = donor_roi.name;
        //   option.value = donor_roi.url;
        //   option.selected = false;
        //   document.getElementById('select_rois').add(option);
        // }
        // var option = document.createElement("option");
        // option.text = donor + ' KNRGL Megane';
        // option.value = donor + '_knrgl';
        // option.selected = true;
        // document.getElementById('select_rois').add(option);
        // $('.selectpicker').selectpicker('refresh');
      }

      updateROItracks();
    }

    async function updateROItracks() {
      var activeROIs = browser.roiManager.roiSets.map((x) => x.name)

      document.getElementById('select_rois').options.length = 0;
      var donor = document.getElementById('select_donor').value
      for (const roi_track of all_roi_tracks.filter((x) => (x.donor == 'all') | (x.donor == donor))) {
        var option = document.createElement("option");
        option.text = roi_track.name;
        option.value = roi_track.url;
        option.selected = false;
        document.getElementById('select_rois').add(option);
      }
      $('.selectpicker').selectpicker('refresh');
    }

    async function updateROIs() {
      // Remove the currently active ROI tracks
      var activeROITracks = browser.tracks.filter((x) => (x.config) ? x.config.tracktype == 'ROI' : 0)
      for (const roiTrack of activeROITracks) { browser.removeTrack(roiTrack) }
      browser.clearROIs();
      var selected_rois = $('#select_rois').val()
      var selected_roi_tracks = all_roi_tracks.filter((x) => selected_rois.includes(x.url))

      // browser.loadTrackList(selected_roi_tracks);
      myLoadBrowserTracks(selected_roi_tracks)
      browser.loadROI(selected_roi_tracks)
    }

    ////////////////////
    // Create tracks
    async function updateCells() {
      // After the user selects a single donor to display, update the list of cells and the available bulk tracks
      var donor = document.getElementById('select_donor').value;
      var tissues = $('#select_tissue').val();
      var tissuenum = 0 ? donor.tissue == 'HIP' : 1;
      var cellsu = cells.filter((x) => (x.donor == donor) & (tissues.includes(x.tissue)) & (x.is_bulk == 'False'))

      for (const selector of ['pileup', 'bam']) {
        $("#select_cells_" + selector).empty();

        // Add Bulk BAM/Pileup tracks
        for (const modality of ['WGS', 'SLAVseq']) {
          for (const tissue of ['DLPFC', 'HIP', 'DURA']) {
            if (donors_tissues.filter((x) => (x.tissue == tissue) & (x.donor == donor) & (x['AllDonors_Bulk' + modality + '_' + selector + '_path'].length > 0)).length > 0) {
              var option = document.createElement("option");
              option.selected = false;
              option.text = donor + ' Bulk ' + modality + ' ' + tissue;
              option.value = donor + '_Bulk' + modality + '_' + tissue;
              document.getElementById('select_cells_' + selector).add(option)
            }
          }
        }

        if (selector == 'pileup') {
          const modality = 'MaxSingleCells';
          for (const tissue of ['DLPFC', 'HIP']) {
            var option = document.createElement("option");
            option.selected = false;
            option.text = donor + ' ' + modality + ' ' + tissue;
            option.value = donor + '_Bulk' + modality + '_' + tissue;
            document.getElementById('select_cells_' + selector).add(option)
          }
        }

        for (const cell of cellsu) {
          var option = document.createElement("option");
          option.text = cell.donor + ' ' + cell.tissue + ': ' + cell.sample;
          option.value = cell.sample;
          option.selected = false;
          document.getElementById('select_cells_' + selector).add(option)
        }
      }
      $('.selectpicker').selectpicker('refresh');
    }

    async function allCellsHeatmapTrack() {
      // Create a heatmap track showing all cells
      const colorScale = {
        low: 0, lowR: 255, lowG: 255, lowB: 255,
        mid: 40, midR: 125, midG: 125, midB: 125,
        high: 1000.0, highR: 255.0, highG: 10.0, highB: 10.0
      }

      browser.loadTrack({
        "name": "All cells - coverage around peaks",
        "format": "seg",
        "isLog": true,

        // "filename": "allcells_q30_R1_disc_bins1kb.withZeros.seg.gz",
        // "url": "./data/allcells_q30_R1_disc_bins1kb.withZeros.seg.gz", // Show all reads
        // "indexURL": "./data/allcells_q30_R1_disc_bins1kb.withZeros.seg.gz.tbi",

        // "filename": "allcells_q30_R1_disc_bins1kb_coverage5.seg.gz",
        // "url": "./data/allcells_q30_R1_disc_bins1kb_coverage5.seg.gz", // Show all reads
        // "indexURL": "./data/allcells_q30_R1_disc_bins1kb_coverage5.seg.gz.tbi",

        "filename": "allcells.peaks.R1_disc_q30.seg.gz",
        "url": "./data/allcells_q30_R1_disc_bins1kb_coverage5.seg.gz", // Show all reads
        "indexURL": "./data/allcells_q30_R1_disc_bins1kb_coverage5.seg.gz.tbi",

        // "filename": "foo.seg.gz", "url": "./data/foo.seg.gz", "indexURL": "./data/foo.seg.gz.tbi", // This is a smaller heatmap showing just one donor
        "indexed": true,
        "sourceType": "file",
        "type": "seg",
        "height": 800,
        'maxHeight': 1600,
        "displayMode": "FILL",
        "order": 5,
        "sort": {
          "option": "ATTRIBUTE",
          "attribute": "order",
          "direction": "ASC"
        },
        "posColorScale": colorScale,
        "negColorScale": colorScale,
      })
    }

    // TODO: Create an option for showing only the donors that have all 3 tissues (DLPFC, HIP and DURA)
    async function pileupTracks(tracktypes) {
      // Load all pileup tracks (max across all single cells) of selected type (BulkWGS, BulkSLAVseq or MaxSingleCells)
      // var tracktype = document.getElementById('select_donor').value;

      const modality2num = { 'AllDonors_BulkWGS': 0, 'AllDonors_BulkSLAVseq': 1, 'AllDonors_BulkMaxSingleCells': 2 };
      const modality2scale = { 'AllDonors_BulkWGS': 20, 'AllDonors_BulkSLAVseq': 500, 'AllDonors_BulkMaxSingleCells': 20 };
      const tissue2num = { 'DLPFC': 0, 'HIP': 1, 'DURA': 2 };
      var myTracks = [];
      var autoscale = $('#toggleAutoscale_btn').prop('checked')
      var trackHeight = document.getElementById('pileup_height').value;
      const darkerColors = { 'HIP': 'rgb(0,104,155)', 'DLPFC': 'rgb(0,0,155)' }

      for (const tracktype of tracktypes) {
        var tissues = $('#select_tissue').val();
        var useTracks = donors_tissues.filter((x) => tissues.includes(x.tissue))
        useTracks = useTracks.filter((x) => x[tracktype + '_pileup_path'].length > 0)

        for (const donor of useTracks) {
          var tissuenum = tissue2num[donor.tissue]

          var myTrack = {
            'name': tracktype.replace('AllDonors_', '').replace('BulkMaxSingle', 'MaxSingle') + ' ' + donor.donor + ' ' + donor.tissue,
            'url': donor[tracktype + '_pileup_path'],
            'format': 'bigwig',
            'type': 'wig',
            'windowFunction': 'max',
            'autoscale': autoscale,
            'min': 0, 'max': modality2scale[tracktype],
            'height': trackHeight,
            'minHeight': 5,
            'color': ['AllDonors_BulkSLAVseq', 'AllDonors_BulkWGS'].includes(tracktype) ? darkerColors[donor.tissue] : donor.color,
            'order': 10 + Number(donor.donor.slice(1) / 100) + (modality2num[tracktype] / 1000) + (tissuenum / 10000),
            'overflowColor': "rgb(100,100,100)"
          };
          // if (tracktype == ['AllDonors_BulkWGS','AllDonors_BulkSLAVseq']) {
          myTrack.roi = [{
            name: donor.donor + ' non-reference germline L1 insertions (KNRGL called by Megane)',
            // url: "./rois/megane_KNRGL_calls/" + donor.donor + "." + donor.tissue + ".megane_final_gaussian.L1HS.bed",
            url: "./rois/megane_KNRGL_calls/KNRGL_subsets/AnyTissue/KNRGL." + donor.donor + ".bed",
            color: "rgba(255,94,1,0.90)"
          }]
          // }

          myTracks.push(myTrack)
        }
      }

      // Remove current tracks
      var activeTracks = browser.tracks.filter((x) => ['wig', 'bam'].includes(x.type))
      for (const activeTrack of activeTracks) { browser.removeTrack(activeTrack) }
      // browser.loadTrackList(myTracks)
      // for (const track of myTracks) { browser.loadTrack(track) }
      myLoadBrowserTracks(myTracks)
    }

    async function addBigWigTracks() {
      // Single cell tracks
      var cells_show = $("#select_cells_pileup").val();
      var cells_infos = cells.filter((x) => (cells_show.includes(x.sample)) & (x.is_bulk == 'False'))
      var trackHeight = document.getElementById('pileup_height').value;
      var myTracks = []
      for (const cell_info of cells_infos) {
        var myTrack = {
          'name': cell_info.donor + ' ' + cell_info.tissue + ':' + cell_info.sample,
          // 'url': 'data/bigwig/SingleCell_pileups_q30_bothstrands/' + cell_info.sample + '.tagged.sorted.R1_discordant.q30.sorted.bigwig',
          'url': 'data/bigwig/SingleCell_pileups_q30_bothstrands/' + cell_info.sample + '.tagged.sorted.R1.q30.sorted_thresh0.bigwig',
          'format': 'bigwig',
          'type': 'wig',
          'windowFunction': 'max',
          'autoscale': false,
          'min': 0, 'max': 20,
          'height': trackHeight,
          'minHeight': 5,
          'color': cell_info.tissue == "HIP" ? "rgb(0,204,255)" : "rgb(0,0,255)",
          // 'visible': false,
          'order': 10
        };
        myTracks.push(myTrack)
      }

      // Add BulkWGS and BulkSLAVseq pileups
      var tracks = document.getElementById('select_cells_pileup').selectedOptions;
      var donor = document.getElementById('select_donor').value;
      const tissue2num = { 'DLPFC': 0, 'HIP': 1, 'DURA': 2 };
      const modality2num = { 'BulkWGS': 0, 'BulkSLAVseq': 1, 'BulkMaxSingleCells': 2 };
      const autoscale = $('#toggleAutoscale_btn').prop('checked');
      for (const track of tracks) {
        if (track.value.includes('Bulk')) {
          var tracktype = track.value.split('_')[1]
          tracktype = tracktype.replace('AllDonors_', '')
          var tissue = track.value.split('_')[2]
          var myDonorTissue = donors_tissues.filter((x) => (x.donor == donor) & (x.tissue == tissue) & (x['AllDonors_' + tracktype + '_pileup_path'].length > 0))
          if (myDonorTissue.length == 1) {
            var myTrack = {
              'name': track.text,
              'url': myDonorTissue[0]['AllDonors_' + tracktype + '_pileup_path'],
              'format': 'bigwig',
              'type': 'wig',
              'order': 5.5 + modality2num[tracktype] / 10 + tissue2num[tissue] / 100,
              'color': myDonorTissue[0]['color'],
              'autoscale': autoscale,
              'min': 0, 'max': 20,
              'height': 20,
              'minHeight': 5
            };
            if (track.text.includes('Bulk WGS')) {
              var tissue = track.text.split(' ')[3]
              myTrack.roi = [{
                name: donor + ' non-reference germline L1 insertions (KNRGL called by Megane)',
                url: "./rois/megane_KNRGL_calls/" + donor + "." + tissue + ".megane_final_gaussian.L1HS.bed",
                color: "rgba(255,94,1,0.90)"
              }]
            }
            myTracks.push(myTrack)
          }
        }
      }

      // browser.loadTrackList(myTracks)
      // for (const track of myTracks) { browser.loadTrack(track) }
      myLoadBrowserTracks(myTracks)
    }

    async function addBamTracks() {
      // Add bam tracks for single cells
      var cells_show = $("#select_cells_bam").val();
      var cells_infos = cells.filter((x) => cells_show.includes(x.sample))
      var myTracks = []
      for (const cell_info of cells_infos) {
        var myTrack = {
          'name': cell_info.donor + ' ' + cell_info.tissue + ':' + cell_info.sample,
          // 'url': 'data/bam/SingleCells/' + cell_info.sample + '.tagged.sorted.bam',
          // 'indexURL': 'data/bam/SingleCells/' + cell_info.sample + '.tagged.sorted.bam.bai',
          'url': cell_info.url,
          'indexURL': cell_info.url + '.bai',
          'format': 'bam',
          'type': 'alignment',
          'filter': { mq: 30 },
          'height': 100,
          'minHeight': 10,
          'coverageColor': cell_info.tissue == "HIP" ? "rgb(0,204,255)" : "rgb(0,0,255)",
          'showSoftClips': true,
          'showInsertions': false, 'showMismatches': false, 'showCoverage': true,
          'showCoverage': false,
          'displayMode': 'squished',
          'viewAsPairs': false,
          'visibilityWindow': 3000000,
          'maxTLEN': 10000,
          'order': 6
        };
        myTracks.push(myTrack)
      }

      // Add BulkWGS and BulkSLAVseq bams
      var tracks = document.getElementById('select_cells_bam').selectedOptions;
      var donor = document.getElementById('select_donor').value;
      for (const track of tracks) {
        if (track.value.includes('Bulk')) {
          var tracktype = track.value.split('_')[1]
          tracktype = tracktype.replace('AllDonors_', '')
          var tissue = track.value.split('_')[2]
          var myDonorTissue = donors_tissues.filter((x) => (x.donor == donor) & (x.tissue == tissue) & (x['AllDonors_' + tracktype + '_bam_path'].length > 0))
          if (myDonorTissue.length == 1) {
            var myTrack = {
              'name': track.text,
              'url': myDonorTissue[0]['AllDonors_' + tracktype + '_bam_path'],
              'indexURL': myDonorTissue[0]['AllDonors_' + tracktype + '_bam_path'] + '.bai',
              'format': 'bam',
              'filter': { mq: 30 },
              'type': 'alignment',
              'height': 150,
              'minHeight': 10,
              'showSoftClips': true,
              'showInsertions': false, 'showMismatches': false, 'showCoverage': true,
              'displayMode': 'squished',
              'viewAsPairs': false,
              'visibilityWindow': 3000000,
              'maxTLEN': 10000,
              'order': 5.5,
              'samplingWindowSize': 100,
              'samplingDepth': 5,
            }
          };
          myTracks.push(myTrack)
        }
      }
      myLoadBrowserTracks(myTracks)
    }

    async function sortSegTracks() {
      var mytracks = browser.tracks.filter((x) => x.type == 'seg')
      mytracks.forEach(
        (x) => x.sortByAttribute('order')
      )
    }

    async function myLoadBrowserTracks(myTracks) {
      // Load 10 tracks at a time to avoid overloading the server. This is a bit ad hoc. 
      const chunkSize=10;
      for (let i = 0; i < myTracks.length; i += chunkSize) {
        const chunk = myTracks.slice(i, i + chunkSize);
        await browser.loadTrackList(chunk); // Wait for each chunk to be processed
      }
    }

    async function trackHeight(plusminus) {
      var tracks = browser.trackViews.filter((x) => ['wig', 'alignment', 'seg'].includes(x.track.type))
      tracks.forEach((x) => {
        var height = x.track.height;
        switch (plusminus) {
          case '+':
            height *= 1.1;
            break;
          case '-':
            height /= 1.1;
            break
        }
        x.track.height = height;
        x.setTrackHeight(height)
      })
    }

    async function updateIGV() {
      for (const track of (browser.tracks.filter((x) => ['wig', 'alignment', 'seg'].includes(x.type)))) {
        browser.removeTrack(track);
      }

      // Add pileup tracks (bigwig)
      var selectedDonor = document.getElementById('select_donor').value;
      switch (selectedDonor) {
        case 'AllDonors_AllModalities':
          pileupTracks(['AllDonors_BulkMaxSingleCells', 'AllDonors_BulkWGS',
            'AllDonors_BulkSLAVseq'
          ])
          break;
        case 'AllDonors_BulkMaxSingleCells':
        case 'AllDonors_BulkSLAVseq':
        case 'AllDonors_BulkWGS':
          pileupTracks([selectedDonor]);
          break;
        case 'SingleCells_Heatmap':
          allCellsHeatmapTrack();
          break;
        default:
          addBigWigTracks();
          addBamTracks();
      }
    }

    const igvDiv = document.getElementById("igv-div");
    const browser = await igv.createBrowser(igvDiv, options)
    browser.all_roi_tracks = all_roi_tracks;
    browser.options = options;

    async function initialize() {
      await updateDonors();
      await updateTracks();
      await updateCells();
      // await updateROItracks();
      if (!window.location.search.includes('sessionURL')) { await updateROIs(); }
    }

    document.getElementById('getLinkButton').addEventListener('click', (event) => {
      getLink();
      copyLink();
    })
    document.getElementById('screenshotButton').addEventListener('click', () => { myScreenshot(); })
    document.getElementById('select_donor').addEventListener('change', () => { updateCells(); updateTracks(); updateIGV(); })
    document.getElementById('pileup_height').addEventListener('change', () => { updateIGV(); })
    document.getElementById('btn_plus').addEventListener('click', () => { trackHeight('+'); })
    document.getElementById('btn_minus').addEventListener('click', () => { trackHeight('-'); })
    browser.on('locuschange', function () {
      toggleROIs();
      // sortSegTracks(); // This may be too slow and compromise performance?
    });
    browser.on('trackorderchanged', function () { toggleROIs() });

    // Make some functions and variables accessible globally
    globalThis.browser = browser; // Makes the browser available in the console

    // I don't know why, but we have to use jQuery to set up events which can get triggered by "select all" and "deselect all"
    $(document).ready(() => {
      $('#toggleROIs_btn').change(function () {
        toggleROIs();
      })
      $('#toggleAutoscale_btn').change(function () {
        toggleAutoscale();
      })
      $('#select_tissue').on('change', (e) => {
        updateCells();
        updateTracks();
        updateIGV();
      })
      $('#select_cells_pileup, #select_cells_bam').on('change', (e) => {
        updateTracks();
        updateIGV();
      })
      $('#select_rois').on('change', function () {
        updateROIs();
        toggleROIs();
      });

      initialize();
    })

  </script>
</body>

</html>