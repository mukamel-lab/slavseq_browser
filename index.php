<head>
  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-1T4FCX4DFN"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() {dataLayer.push(arguments);}
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
  </style>
  <script src="js/fontawesome.js"></script>
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/solid.min.css"> -->
  <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/solid.min.js"></script> -->

  <?php
  include './loadCsv.php';
  loadCsv('config/donors_IGVconfig.csv', 'donors_tissues');
  loadCsv('config/slavseq_metadata.csv', 'cells');
  ?>

</head>

<body id="top">
  <nav class="navbar navbar-default" style="margin:0;">
    <div class="container-fluid">
      <ul class="nav navbar-nav">
        <!-- <li class="nav-item">
          <span class="glyphicon glyphicon-chevron-down" data-toggle="collapse" href="#topbar" title="Show/hide options"
            style="line-height: inherit; font-size:18;"></span>
        </li> -->
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
            <option value="Heatmap"> All cells (heatmap)</option>
            <option value="AllDonors_MaxSingleCells" selected> All donors - Max of single cell SLAV-seq</option>
            <option value="AllDonors_BulkSLAVseq" selected> All donors - Bulk SLAV-seq</option>
            <option value="AllDonors_BulkWGS" selected> All donors - Bulk 30X WGS</option>
          </select>
        </li>

        <li class="nav-item" id="select_tissue_li" style="width:none">
          <select id="select_tissue" class="selectpicker" multiple data-width="100%" data-toggle="tooltip"
            data-placement="top" title="Tissue" data-selected-text-format="static" data-title="Tissue"
            data-header="Tissues to show" data-actions-box="true">
            <option value="HIP"> Hippocampus</option>
            <option value="DLPFC"> Dorsolateral pre-frontal cortex</option>
          </select>
        </li>

        <li class="nav-item" id="select_cells_li" style="display:none; width:none;">
          <!-- <label>Pileup track height:</label> -->
          <input class="selectpicker" id="pileup_height" value="20" style="width:50px; display:none;">
          <select id="select_cells_pileup" class="selectpicker" multiple data-width="100%" data-title="Cell pileups"
            title="Cell pileups" data-toggle="tooltip" data-placement="top" data-live-search="true"
            data-header="Cells to show" data-actions-box="true" data-selected-text-format="static">
            <option value="All" selected> All cells</option>
          </select>
        </li>
        <li class="nav-item" id="select_bams_li" style="display:none; width:none;">
          <select id="select_cells_bam" class="selectpicker" multiple data-width="100%" data-live-search="true"
            title="Cell BAMs" data-toggle="tooltip" data-placement="top" data-header="Cells to show"
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
        </li>

      </ul>
    </div>


  </nav>

  <div id="igv-div"></div>

  <script type="module">

    import igv from "./js/igv.esm.min.js"
    import {all_roi_tracks} from "./roi_tracks.js"

    // Add ROI tracks for each donor
    const donors = donors_tissues.filter((d) => d.tissue == "HIP")
    for (const donor of donors) {
      var roiTrack = {
        'name': "Peaks in " + donor.donor,
        'url': './data/peaks_per_donor/peaks_' + donor.donor + '.bed',
        'indexed': false,
        'color': "rgba(1,1,255,0.5)",
        'format': 'bed',
        'tracktype': 'ROI'
      };
      all_roi_tracks.push(roiTrack);
    }

    var options =
    {
      genome: "hs1",
      queryParametersSupported: true,
      locus: "chr5:156,424,267-156,454,552",
      reference: {
        "id": "hs1",
        "blatDB": "hub_3671779_hs1",
        "name": "Human (T2T CHM13-v2.0/hs1)",
        "fastaURL": "https://s3.amazonaws.com/igv.org.genomes/chm13v2.0/chm13v2.0.fa",
        "indexURL": "https://s3.amazonaws.com/igv.org.genomes/chm13v2.0/chm13v2.0.fa.fai",
        "cytobandURL": "https://s3.amazonaws.com/igv.org.genomes/chm13v2.0/CHM13_v2.0.cytoBandMapped.bed",
        "aliasURL": "https://hgdownload.soe.ucsc.edu/goldenPath/hs1/bigZips/hs1.chromAlias.txt",
        "twoBitURL": "https://hgdownload.soe.ucsc.edu/goldenPath/hs1/bigZips/hs1.2bit",
        "twoBitBptURL": "https://hgdownload.soe.ucsc.edu/goldenPath/hs1/bigZips/hs1.2bit.bpt"
      },
      tracks: [
        {
          "name": "Genes",
          "format": "bed",
          "url": "https://s3.amazonaws.com/igv.org.genomes/chm13v2.0/chm13v2.0_geneLocations.short.bed.gz",
          "hidden": false,
          "searchable": true,
          "order": 0,
          "type": "annotation",
          "height": 5
        },
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

    for (const mytrack of all_roi_tracks.filter((x) => !x.name.startsWith("Peaks in D"))) {
      mytrack.height = 25;
      mytrack.order = 4;
      // Make the color non-transparent
      if (mytrack.color) {
        mytrack.color = mytrack.color.replace(/,[0-9]+\)/i, ',0)');
      }
      options.tracks.push(mytrack)
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

    ////////////////////////////////////
    // Functions for updating the dropdown lists

    function updateDonors() {
      // Update the select menus
      const donors = donors_tissues.filter((d) => d.tissue == "HIP")
      for (const donor of donors) {
        var option = document.createElement("option");
        option.text = donor.name;
        option.title = donor.donor;
        option.value = donor.donor;
        document.getElementById('select_donor').add(option);
      }
    }

    function updateTracks() {
      // Show and hide the appropriate dropdown menus
      var donor = document.getElementById('select_donor').value;
      if (donor == 'AllDonors_MaxSingleCells') {
        document.getElementById('select_cells_li').style.display = 'none';
        document.getElementById('select_bams_li').style.display = 'none';
        document.getElementById('select_tissue_li').style.display = 'block';
      } else if (donor.startsWith('AllDonors_Bulk')) {
        document.getElementById('select_cells_li').style.display = 'none';
        document.getElementById('select_bams_li').style.display = 'none';
        document.getElementById('select_tissue_li').style.display = 'none';
      }      else if (donor == 'Heatmap') {
        document.getElementById('select_cells_li').style.display = 'none';
        document.getElementById('select_bams_li').style.display = 'none';
        document.getElementById('select_tissue_li').style.display = 'none';
      } else {
        // Showing a specific donor
        document.getElementById('select_cells_li').style.display = 'block';
        document.getElementById('select_bams_li').style.display = 'block';
        document.getElementById('select_tissue_li').style.display = 'block';

        var option = document.createElement("option");
        option.text = donor + ' KNRGL Megane';
        option.value = donor + '_knrgl';
        option.selected = true;
        document.getElementById('select_rois').add(option);
        $('.selectpicker').selectpicker('refresh');
      }
    }

    function initializeROIs() {
      var activeROIs = browser.roiManager.roiSets.map((x) => x.name)
      for (const roi_track of all_roi_tracks.filter((x) => !x.name.startsWith("Peaks in D"))) {
        var option = document.createElement("option");
        option.text = roi_track.name;
        option.value = roi_track.name;
        option.selected = activeROIs.includes(roi_track.name);
        document.getElementById('select_rois').add(option);
      }
      $('.selectpicker').selectpicker('refresh');
    }

    function updateROIs() {
      // Remove the currently active ROI tracks
      var activeROITracks = browser.tracks.filter((x) => (x.config) ? x.config.tracktype == 'ROI' : 0)
      for (const roiTrack of activeROITracks) {browser.removeTrack(roiTrack)}
      browser.clearROIs();
      var rois = $('#select_rois').val()
      var roi_tracks = all_roi_tracks.filter((x) => rois.includes(x.name))

      if (document.getElementById('select_donor').value.startsWith('D')) {
        // When we're only showing a single donor, just show the ROIs for that donor
        var donor = document.getElementById('select_donor').value;
        roi_tracks.push({
          "name": "Peaks for Donor " + donor,
          'url': 'data/peaks_per_donor/peaks_' + donor + '.bed',
          'indexed': false,
          'tracktype': 'ROI',
          'height': 25,
          'order': 4.9
        })
      }
      browser.loadTrackList(roi_tracks);
      browser.loadROI(roi_tracks)
    }

    ////////////////////
    // Create tracks
    function updateCells() {
      var donor = document.getElementById('select_donor').value;
      var tissues = $('#select_tissue').val();
      var tissuenum = 0 ? donor.tissue == 'HIP' : 1;
      var cellsu = cells.filter((x) => (x.donor == donor) & (tissues.includes(x.tissue)) & (x.is_bulk == 'False'))
      for (const selector of ['pileup', 'bam']) {
        $("#select_cells_" + selector).empty();
        for (const cell of cellsu) {
          var option = document.createElement("option");
          option.text = cell.donor + ' ' + cell.tissue + ': ' + cell.sample;
          option.value = cell.sample;
          option.selected = selector == 'pileup';
          document.getElementById('select_cells_' + selector).add(option)
        }
      }
      $('.selectpicker').selectpicker('refresh');
    }

    function allDonorsTracks() {
      // Create a pileup track for each donor/tissue
      var myTracks = []
      var tissues = $('#select_tissue').val();
      for (const donor of donors_tissues.filter((x) => tissues.includes(x.tissue))) {
        var tissuenum = 0 ? donor.tissue == 'HIP' : 1;
        var myTrack = {
          'name': donor.donor + ' ' + donor.tissue,
          'url': donor.url,
          'format': 'bigwig',
          'type': 'wig',
          'windowFunction': 'max',
          'autoscale': false,
          'min': 0, 'max': 20,
          'height': 20,
          'color': donor.tissue == "HIP" ? "rgb(0,204,255)" : "rgb(0,0,255)",
          'visible': false,
          'order': 10 + (donor.index / 100) + (tissuenum / 1000),
          'roi': [{
            name: donor.donor + ' non-reference germline L1 insertions (KNRGL called by Megane)',
            url: "./rois/KNRGL_" + donor.donor + "_megane.bed",
            color: "rgba(255,94,1,0.90)"
          }],
          'overflowColor': "rgb(100,100,100)"
        };
        myTracks.push(myTrack)
      }
      browser.loadTrackList(myTracks)
    }

    function allCellsHeatmapTrack() {
      // Create a heatmap track showing all cells
      browser.loadTrack({
        "name": "All cells - coverage around peaks",
        "filename": "allcells_q30_R1_disc_bins1kb.seg.gz",
        "format": "seg",
        // "url": "./data/allcells_q30_R1_disc_bins1kb.coverage5.seg.gz", // Show only the bins with ≥5 reads
        // "indexURL": "./data/allcells_q30_R1_disc_bins1kb.coverage5.seg.gz.tbi",
        "url": "./data/allcells_q30_R1_disc_bins1kb.seg.gz", // Show all reads
        "indexURL": "./data/allcells_q30_R1_disc_bins1kb.seg.gz.tbi",
        "indexed": true,
        "sourceType": "file",
        "type": "seg",
        "height": 1000,
        "displayMode": "FILL",
        "order": 5,
        "sort": {
          "option": "ATTRIBUTE",
          "attribute": "order",
          "direction": "ASC"
        }
      })
    }

    function addBigWigTracks() {
      var cells_show = $("#select_cells_pileup").val();
      var cells_info = cells.filter((x) => (cells_show.includes(x.sample)) & (x.is_bulk == 'False'))
      var trackHeight = document.getElementById('pileup_height').value;
      var myTracks = []
      for (const cell_info of cells_info) {
        var myTrack = {
          'name': cell_info.donor + ' ' + cell_info.tissue + ':' + cell_info.sample,
          'url': 'https://brainome.ucsd.edu/emukamel/SLAVSeq_SZ/allsamples/SingleCells/pileups_q30_bothstrands/' + cell_info.sample + '.tagged.sorted.R1_discordant.q30.sorted.bigwig',
          'format': 'bigwig',
          'type': 'wig',
          'windowFunction': 'max',
          'autoscale': false,
          'min': 0, 'max': 20,
          'height': trackHeight, 'minHeight': 5,
          'color': cell_info.tissue == "HIP" ? "rgb(0,204,255)" : "rgb(0,0,255)",
          'visible': false,
          'order': 10
        };
        // browser.loadTrack(myTrack)
        myTracks.push(myTrack)
      }
      browser.loadTrackList(myTracks)
    }

    function addBamTracks() {
      // Add bam tracks
      var cells_show = $("#select_cells_bam").val();
      var cells_info = cells.filter((x) => cells_show.includes(x.sample))
      var myTracks = []
      for (const cell_info of cells_info) {
        var myTrack = {
          'name': cell_info.donor + ' ' + cell_info.tissue + ':' + cell_info.sample,
          'url': 'https://brainome.ucsd.edu/emukamel/SLAVSeq_SZ/allsamples/SingleCells/bam/' + cell_info.sample + '.tagged.sorted.bam',
          'indexURL': 'https://brainome.ucsd.edu/emukamel/SLAVSeq_SZ/allsamples/SingleCells/bam/' + cell_info.sample + '.tagged.sorted.bam.bai',
          'format': 'bam',
          'type': 'alignment',
          'height': 100,
          'minHeight': 10,
          'coverageColor': cell_info.tissue == "HIP" ? "rgb(0,204,255)" : "rgb(0,0,255)",
          'showSoftClips': true,
          'showCoverage': false,
          'displayMode': 'squished',
          'viewAsPairs': false,
          'visible': false,
          'visibilityWindow': 3000000,
          'maxTLEN': 10000,
          'order': 4
        };
        // browser.loadTrack(myTrack)
        myTracks.push(myTrack)
      }
      browser.loadTrackList(myTracks)
    }

    async function sortSegTracks() {
      console.log('Sort seg tracks')
      var mytracks = browser.tracks.filter((x) => x.type == 'seg')
      mytracks.forEach(
        (x) => x.sortByAttribute('order')
      )
      console.log(mytracks.length)
    }

    async function trackHeight(plusminus) {
      console.log('Changing track height: ' + plusminus)
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
        console.log('new height = ' + height)
        x.track.height = height;
        x.setTrackHeight(height)
      })
    }

    function updateIGV() {
      for (const track of (browser.tracks.filter((x) => ['wig', 'alignment', 'seg'].includes(x.type)))) {
        browser.removeTrack(track);
      }

      // Add pileup tracks (bigwig)
      var selectedDonor = document.getElementById('select_donor').value;
      switch (selectedDonor) {
        case 'AllDonors':
          allDonorsTracks();
          break;
        case 'Heatmap':
          allCellsHeatmapTrack();
          break;
        default:
          addBigWigTracks();
          addBamTracks();

      }
    }

    const igvDiv = document.getElementById("igv-div");
    const browser = await igv.createBrowser(igvDiv, options)

    await updateDonors();
    await updateTracks();
    await updateCells();
    await initializeROIs();
    if (!window.location.search.includes('sessionURL')) {await updateROIs();}
    // await browser.roiManager.toggleROIs();

    document.getElementById('getLinkButton').addEventListener('click', (event) => {
      getLink();
      copyLink();
    })
    document.getElementById('screenshotButton').addEventListener('click', () => {myScreenshot();})
    document.getElementById('select_donor').addEventListener('change', () => {console.log('Pressed select_donor'); updateCells(); updateTracks(); updateROIs(); updateIGV();})
    // document.getElementById('select_cells_pileup').addEventListener('change', () => {updateTracks(); updateIGV();})
    // document.getElementById('select_cells_bam').addEventListener('change', () => {updateTracks(); updateIGV();})
    document.getElementById('pileup_height').addEventListener('change', () => {updateIGV();})
    document.getElementById('btn_plus').addEventListener('click', () => {trackHeight('+');})
    document.getElementById('btn_minus').addEventListener('click', () => {trackHeight('-');})
    $('#toggleROIs_btn').change(function () {
      toggleROIs();
    })
    browser.on('locuschange', function () {
      toggleROIs();
      // sortSegTracks(); // This may be too slow and compromise performance?
    });
    browser.on('trackorderchanged', function () {toggleROIs()});

    globalThis.browser = browser; // Makes the browser available in the console

    // I don't know why, but we have to use jQuery to set up events which can get triggered by "select all" and "deselect all"
    $(document).ready(() => {
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
        console.log('changed bs select ROIs');
        updateROIs();
        toggleROIs();
      });
    })

  </script>
</body>

</html>