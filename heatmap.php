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
  loadCsv('donors_IGVconfig.csv', 'donors_tissues');
  loadCsv('slavseq_metadata.csv', 'cells');
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
            <select id="select_donor" class="selectpicker" onchange="updateCells(); updateTracks(); updateIGV(); "
              data-width="auto" data-toggle="tooltip" data-placement="top" data-header="Donor to show"
              data-actions-box="true">
              <option value="AllDonors" selected> All donors (pileups)</option>
              <option value="Heatmap" selected> All cells (heatmap)</option>
            </select>
          </li>

          <li class="nav-item cell-selection" id="select_cells_li">

            <select id="select_cells_pileup" class="selectpicker" multiple data-width="auto" title="Cell pileups"
              onchange="updateTracks(); updateIGV(); " data-toggle="tooltip" data-placement="top"
              data-live-search="true" data-header="Cells to show" data-actions-box="true"
              data-selected-text-format="count">
              <option value="All" selected> All cells</option>
            </select>
            <label>Pileup track height:</label>
            <input class="selectpicker" id="pileup_height" value="20" onchange="updateIGV(); " style="width:50px; ">
            <select id="select_cells_bam" class="selectpicker" onchange="updateTracks(); updateIGV(); " multiple
              data-width="auto" data-live-search="true" title="Cell BAMs" data-toggle="tooltip" data-placement="top"
              data-header="Cells to show" data-actions-box="true" data-selected-text-format="count">
              <option value="All" selected> All cells</option>
            </select>
          </li>

          <li class="nav-item tissue-selection">
            <select id="select_tissue" class="selectpicker" onchange="updateCells(); updateTracks(); updateIGV(); "
              data-width="auto" multiple data-toggle="tooltip" data-placement="top">
              <option value="HIP" selected> Hippocampus</option>
              <option value="DLPFC" selected> Dorsolateral pre-frontal cortex</option>
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


  <div id="igv-div" width="100%"></div>

  <script type="module">

    import igv from "./dist/igv.esm.min.js"

    const igvDiv = document.getElementById("igv-div");

    const options =
    {
      genome: "hs1",
      queryParametersSupported: true,
      locus: "chr5:156,188,335-156,927,125",
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
          'url': "https://brainome.ucsd.edu/emukamel/SLAVSeq_SZ/allsamples/chm13v2.0.XY.fasta.all_rmsk.bb",
          'order': 0.5,
        },
        {
          'name': "KNRGL calls by Megane",
          'format': "bed",
          'sourceType': "file",
          'url': "https://brainome.ucsd.edu/emukamel/SLAVSeq_SZ/allsamples/megane_knrgl/KNRGL_alldonors_megane.merged.bed",
          'order': 1,
        },
        {
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
            "option":"ATTRIBUTE",
            "attribute": "order",
            "direction": "ASC"
          }
        }
      ],
      "sampleinfo": [
        {
          "url": "./data/sampletable2.tsv"
        }
      ],
      roi: [
        {
          name: 'Non-reference germline L1 insertions (KNRGL called by Megane)',
          url: "./rois/KNRGL_alldonors_megane.merged.bed",
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
        },
      ]
    };

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
      var src = 'https://brainome.ucsd.edu/emukamel/SLAVSeq_SZ/IGV/?sessionURL=blob:' + blob
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

    export const browser = await igv.createBrowser(igvDiv, options)

    // updateDonors();
    // await updateTracks();
    // await updateCells();
    // await updateIGV();


    document.getElementById('getLinkButton').addEventListener('click', (event) => {
      getLink();
      copyLink();
    })
    document.getElementById('screenshotButton').addEventListener('click', () => { myScreenshot(); })

    globalThis.browser=browser; // Makes the browser available in the console

  </script>
</body>

</html>