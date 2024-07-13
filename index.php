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
  <script src="./js/fontawesome.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
  <link rel="stylesheet" href="./css/bootstrap-select.css">

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
  <script src="./js/bootstrap-select.js"></script>
  <script type='text/javascript' src='./js/html2canvas.min.js'></script>

  <style type="text/css">
    select.selectpicker {
      display: none;
    }

    /* Prevent FOUC }*/

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

  <?php
  include './loadCsv.php';
  loadCsv('data/donors_IGVconfig.csv', 'donors_tissues');
  loadCsv('data/slavseq_metadata.csv', 'cells');
  ?>

</head>

<body id="top">
  <nav class="navbar navbar-default" style="margin:0;">
    <div class="container-fluid">
      <ul class="nav navbar-nav">
        <li class="nav-item">
          <span class="glyphicon glyphicon-chevron-down" data-toggle="collapse" href="#topbar" title="Show/hide options"
            style="line-height: inherit; font-size:18;"></span>
        </li>
        <li class="nav-item">
          <button id="screenshotButton" class="btn btn-primary">
            <i class="fa fa-camera"></i> Save screenshot</button>
        </li>
        <li class="nav-item">
          <button id="getLinkButton" class="btn btn-primary">
            <i class="fa fa-link" aria-hidden="true"></i> Copy shareable link</button>
          <input type="text" id="srcBox" size="1" style="visibility:hidden;"></input>
        </li>
      </ul>
    </div>

    <div class="collapse in" id="topbar">
      <div class="container-fluid">
        <ul class="nav navbar-nav">
          <li class="nav-item donor-selection">
            <select id="select_donor" class="selectpicker" data-width="auto" data-toggle="tooltip" data-placement="top"
              data-header="Donor to show" data-actions-box="true">
              <option value="AllDonors" selected> All donors (pileups)</option>
              <option value="Heatmap"> All cells (heatmap)</option>
            </select>
          </li>

          <li class="nav-item tissue-selection" id="select_tissue_li">
            <select id="select_tissue" class="selectpicker" data-width="auto" multiple data-toggle="tooltip"
              data-placement="top">
              <option value="HIP"> Hippocampus</option>
              <option value="DLPFC"> Dorsolateral pre-frontal cortex</option>
            </select>
          </li>

          <li class="nav-item cell-selection" id="select_cells_li" style="display: none">

            <select id="select_cells_pileup" class="selectpicker" multiple data-width="auto" title="Cell pileups"
              data-toggle="tooltip" data-placement="top" data-live-search="true" data-header="Cells to show"
              data-actions-box="true" data-selected-text-format="count" >
              <option value="All" selected> All cells</option>
            </select>
            <label>Pileup track height:</label>
            <input class="selectpicker" id="pileup_height" value="20" style="width:50px; ">
            <select id="select_cells_bam" class="selectpicker" multiple data-width="auto" data-live-search="true"
              title="Cell BAMs" data-toggle="tooltip" data-placement="top" data-header="Cells to show"
              data-actions-box="true" data-selected-text-format="count">
              <option value="All" selected> All cells</option>
            </select>
          </li>

          <!-- 
          <li class="nav-item">
            <p>
              <button onclick="myRefresh(); getLink();" class="btn btn-primary">Reload browser</button>
            </p>
          </li> -->

        </ul>
      </div>
    </div>

  </nav>

  <div id="igv-div" width="50%"></div>

  <script type="module">

    import igv from "./dist/igv.esm.min.js"

    const options =
    {
      genome: "hs1",
      queryParametersSupported: true,
      locus: "chr5:155,155,059-157,910,460",
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
          // "displayMode": "EXPANDED",
          "height": 100,
          "visibilityWindow": -1,
          "supportsWholeGenome": false,
          "order": 0.1,
          "type": "annotation",
          "displayMode": 'squish'
        },
        {
          'name': "RepeatMasker",
          'format': "bigbed",
          'type': 'annotation',
          'sourceType': "file",
          'displayMode': "expanded",
          'url': "data/chm13v2.0.XY.fasta.all_rmsk.bb",
          'order': 0.5,
        },
        {
          'name': "KNRGL calls by Megane",
          'format': "bed",
          'displayMode':'expanded',
          'sourceType': "file",
          'height':40,
          'url': "data/KNRGL_alldonors_megane.merged.bed",
          'order': 0.6,
        },
        {
          'name': "Filtered peaks",
          'format': "bed",
          'displayMode':'squish',
          'height':25,
          'sourceType': "file",
          'url': './rois/allcells_max_q30.filtered.ForIGV.bed',
          'order': 1,
        },
        {
          'name': 'Disc peaks (remove KNRGL,RefL1HS,PolyA)',
          'format': "bed",
          'displayMode':'squish',
          'height':25,
          'sourceType': "file",
          'url': './rois/allcells_max_q30.R1_discordant.noKNRGL_noRefL1HS_slop60kb.noPolyA_2kb.bed',
          'order': 1.1,
        }
      ],
      "sampleinfo": [
        {
          "url": "./data/sampletable.tsv"
        }
      ],
      roi: [
        {
          "name": 'Non-reference germline L1 insertions (KNRGL called by Megane)',
          'url': "./rois/KNRGL_alldonors_megane.merged.bed",
          indexed: false,
          color: "rgba(255,255,255,0)",
          visible: false,
        },
        {
          name: 'Filtered peaks',
          url: './rois/allcells_max_q30.filtered.ForIGV.bed',
          indexed: false,
          color: "rgba(94,255,1,0.25)"
        },
        {
          name: 'Disc peaks (remove KNRGL,RefL1HS,PolyA)',
          url: './rois/allcells_max_q30.R1_discordant.noKNRGL_noRefL1HS_slop60kb.noPolyA_2kb.bed',
          indexed: false,
          color: "rgba(94,94,1,0.25)"
        }
      ]
    };

    ////////////////////////////////////////////////
    // Functions for screenshot and links
    // Screenshot
    export function myScreenshot() {
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

    export function saveAs(uri, filename) {
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

    export function getLink() {
      var blob = browser.compressedSession();
      var src = 'https://brainome.ucsd.edu/slavseq_browser/?sessionURL=blob:' + blob
      document.getElementById('srcBox').value = src;
      window.history.pushState("object or string", "", src);
    }

    export function copyLink() {
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

    ////////////////////////////////////
    // Functions for updating the dropdown lists

    export function updateDonors() {
      // Update the select menus
      const donorMenu = document.getElementById('select_donor');
      const donors = donors_tissues.filter((d) => d.tissue == "HIP")
      for (const donor of donors) {
        var option = document.createElement("option");
        option.text = "Donor " + donor.donor;
        option.title = "D" + donor.donor;
        option.value = donor.donor;
        donorMenu.add(option);
      }
    }

    export function updateTracks() {
      var donor = document.getElementById('select_donor').value;
      if (['AllDonors'].includes(donor)) {
        document.getElementById('select_cells_li').style.display = 'none';
        document.getElementById('select_tissue_li').style.display = 'block';
      }
      else if (['Heatmap'].includes(donor)) {
        document.getElementById('select_cells_li').style.display = 'none';
        document.getElementById('select_tissue_li').style.display = 'none';
      } else {
        document.getElementById('select_cells_li').style.display = 'block';
        document.getElementById('select_tissue_li').style.display = 'block';

      }
    }

    export function updateCells() {
      var donor = document.getElementById('select_donor').value;
      var tissues = $('#select_tissue').val();
      var cellsu = cells.filter((x) => (x.donor == donor) & (tissues.includes(x.tissue)) & (x.is_bulk == 'False'))
      for (const selector of ['pileup', 'bam']) {
        $("#select_cells_" + selector).empty();
        for (const cell of cellsu) {
          var option = document.createElement("option");
          option.text = 'D' + cell.donor + ' ' + cell.tissue + ': ' + cell.sample;
          option.value = cell.sample;
          option.selected = selector == 'pileup';
          document.getElementById('select_cells_' + selector).add(option)
        }
      }
      $('.selectpicker').selectpicker('refresh');
    }

    ////////////////////
    // Create tracks
    export function allDonorsTracks() {
      // Create a pileup track for each donor/tissue
      var myTracks = []
      var tissues = $('#select_tissue').val();
      for (const donor of donors_tissues.filter((x) => tissues.includes(x.tissue))) {
        var tissuenum = 0 ? donor.tissue=='HIP' : 1;
        var myTrack = {
          'name': "Donor" + donor.donor + ' ' + donor.tissue,
          'url': donor.url,
          'format': 'bigwig',
          'type': 'wig',
          'windowFunction': 'max',
          'autoscale': false,
          'min': 0, 'max': 20,
          'height': 20,
          'color': donor.tissue == "HIP" ? "rgb(0,204,255)" : "rgb(0,0,255)",
          'visible': false,
          'order': 10 + donor.index / 100 + tissuenum/1000,
          'roi': [{
            name: donor.donor + ' non-reference germline L1 insertions (KNRGL called by Megane)',
            url: "./rois/KNRGL_Donor" + donor.donor + "_megane.bed",
            color: "rgba(255,94,1,0.90)"
          }],
          'overflowColor': "rgb(100,100,100)"
        };
        myTracks.push(myTrack)
      }
      browser.loadTrackList(myTracks)
    }

    export function allCellsHeatmapTrack() {
      // Create a heatmap track showing all cells
      browser.loadTrack({
        "name": "All cells - coverage around peaks",
        "filename": "allcells.peaks.R1_disc_q30.seg.gz",
        "format": "seg",
        "url": "./data/allcells.peaks.R1_disc_q30.seg.gz",
        "indexURL": "./data/allcells.peaks.R1_disc_q30.seg.gz.tbi",
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

    export function addBigWigTracks() {
      var cells_show = $("#select_cells_pileup").val();
      var cells_info = cells.filter((x) => (cells_show.includes(x.sample)) & (x.is_bulk == 'False'))
      var trackHeight = document.getElementById('pileup_height').value;
      var myTracks = []
      for (const cell_info of cells_info) {
        var myTrack = {
          'name': "Donor" + cell_info.donor + ' ' + cell_info.tissue + ':' + cell_info.sample,
          'url': 'https://brainome.ucsd.edu/emukamel/SLAVSeq_SZ/allsamples/SingleCells/pileups_q30_bothstrands/' + cell_info.sample + '.tagged.sorted.R1_discordant.q30.sorted.bigwig',
          'format': 'bigwig',
          'type': 'wig',
          'windowFunction': 'max',
          'autoscale': false,
          'min': 0, 'max': 20,
          'height': trackHeight,
          'color': cell_info.tissue == "HIP" ? "rgb(0,204,255)" : "rgb(0,0,255)",
          'visible': false,
          'order': 3
        };
        // browser.loadTrack(myTrack)
        myTracks.push(myTrack)
      }
      browser.loadTrackList(myTracks)
    }

    export function addBamTracks() {
      // Add bam tracks
      var cells_show = $("#select_cells_bam").val();
      var cells_info = cells.filter((x) => cells_show.includes(x.sample))
      var myTracks = []
      for (const cell_info of cells_info) {
        var myTrack = {
          'name': "Donor" + cell_info.donor + ' ' + cell_info.tissue + ':' + cell_info.sample,
          'url': 'https://brainome.ucsd.edu/emukamel/SLAVSeq_SZ/allsamples/SingleCells/bam/' + cell_info.sample + '.tagged.sorted.bam',
          'indexURL': 'https://brainome.ucsd.edu/emukamel/SLAVSeq_SZ/allsamples/SingleCells/bam/' + cell_info.sample + '.tagged.sorted.bam.bai',
          'format': 'bam',
          'type': 'alignment',
          'height': 300,
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

    export function updateIGV() {
      for (const track of (browser.tracks.filter((x) => ['wig', 'alignment', 'seg'].includes(x.type)))) {
        browser.removeTrack(track);
      }

      // Add pileup tracks (bigwig)
      if (['AllDonors'].includes($("#select_donor").val())) {
        allDonorsTracks();
      } else if (['Heatmap'].includes($("#select_donor").val())) {
        allCellsHeatmapTrack();
      } else {
        addBigWigTracks();
        addBamTracks();
      }
    }

    const igvDiv = document.getElementById("igv-div");
    const browser = await igv.createBrowser(igvDiv, options)

    await updateDonors();
    await updateTracks();
    await updateCells();

    document.getElementById('getLinkButton').addEventListener('click', (event) => {
      getLink();
      copyLink();
    })
    document.getElementById('screenshotButton').addEventListener('click', () => { myScreenshot(); })
    document.getElementById('select_donor').addEventListener('change', () => { updateCells(); updateTracks(); updateIGV(); })
    document.getElementById('select_cells_pileup').addEventListener('change', () => { updateTracks(); updateIGV(); })
    document.getElementById('select_cells_bam').addEventListener('change', () => { updateTracks(); updateIGV(); })
    document.getElementById('select_tissue').addEventListener('change', () => { updateCells(); updateTracks(); updateIGV(); })
    document.getElementById('pileup_height').addEventListener('change', () => { updateIGV(); })

    globalThis.browser = browser; // Makes the browser available in the console

  </script>
</body>

</html>