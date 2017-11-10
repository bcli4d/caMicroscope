<?php
/**
 * OSD Viewer and Scripts
 */
?>
<div id="container">
    <div id="tool"></div>
    <div id="panel"></div>

    <!-- div id="bookmarkURLDiv"></div -->

    <div id="weightpanel">
        <a href='#'>
            <div id='closeWeightPanel'><img src='images/ic_close_white_24px.svg' title='Close' alt="Close X" height="16"
                                            width="16"></div>
        </a>
        <div id="bar1" class="bar" align="right">
            <div id="slide1" class="slide"></div>
        </div>
        <label class="lb_heatmap"><input type="checkbox" id="cb1" checked> Lymphocyte Sensitivity</label>
        <div id="bar2" class="bar" align="right">
            <div id="slide2" class="slide"></div>
        </div>
        <label class="lb_heatmap"><input type="checkbox" id="cb2" checked> Necrosis Specificity</label>
        <div id="bar3" class="bar" align="right">
            <div id="slide3" class="slide"></div>
        </div>
        <label class="lb_heatmap"><input type="checkbox" id="cb3" checked> Smoothness</label><br>
        <p>
            <button type="button" class="btn_heatmap" id="btn_revertWeight">Revert Weights</button>
            <br>
        <p>
            <input type="radio" name="weighttype" value="LymSe" id="LymSe"> <label for="LymSe" class=radio_markup>Lymphocyte
                Prediction</label> <br>
            <input type="radio" name="weighttype" value="NecSe" id="NecSe"> <label for="NecSe" class=radio_markup>Necrosis
                Prediction</label> <br>
            <input type="radio" name="weighttype" value="BothSe" id="BothSe" checked> <label for="BothSe"
                                                                                             class=radio_markup>Lym
                Prediction with Nec Filtering</label> <br>
            <button type="button" class="btn_heatmap" id="btn_saveHeatmapWeight">Finalize</button>
            <button type="button" class="btn_heatmap" id="btn_heatmapweight_help">&#x2753</button>
    </div>

    <div id="markuppanel">
        <a href='#'>
            <div id='closeMarkupPanel'><img src='images/ic_close_white_24px.svg' title='Close' alt="Close X" height="16"
                                            width="16"></div>
        </a>
        <input type="radio" name="marktype" value="LymPos" checked="checked" id="LymPos" class="radio_markup">
        <label for="LymPos" class=radio_markup> (1) LymPos (draw thin line)</label><br>
        <input type="radio" name="marktype" value="LymNeg" id="LymNeg" class="radio_markup">
        <label for="LymNeg" class=radio_markup> (2) LymNeg (draw thin line)</label><br>
        <p>
        <p>
            <input type="radio" name="marktype" value="LymPosBig" id="LymPosBig" class="radio_markup">
            <label for="LymPosBig" class=radio_markup> (3) LymPos (draw thick line)</label><br>
            <input type="radio" name="marktype" value="LymNegBig" id="LymNegBig" class="radio_markup">
            <label for="LymNegBig" class=radio_markup> (4) LymNeg (draw thick line)</label><br>
        <p>
        <p>
            <input type="radio" name="marktype" value="TumorPos" id="TumorPos" class="radio_markup">
            <label for="TumorPos" class=radio_markup> (5) TumorPos (draw polygon)</label><br>
            <input type="radio" name="marktype" value="TumorNeg" id="TumorNeg" class="radio_markup">
            <label for="TumorNeg" class=radio_markup> (6) TumorNeg (draw polygon)</label><br>
        <p>
        <p>
            <input type="radio" name="marktype" value="Moving" id="rb_Moving" class="radio_markup">
            <label for="rb_Moving" class=radio_markup> (7) Save then Navigate</label><br>
            <button type="button" class="btn_mark" id="btn_savemark">Save</button>
            <button type="button" class="btn_mark" id="btn_undomark">Cancel</button>
            <button type="button" class="btn_mark" id="btn_mark_help">&#x2753</button>
    </div>
    <div id="div_weight_locked" style="display: none;">Free</div>

    <div id="switchuserpanel"><a href='#'>
            <div id='closeSwitchUser'><img src='images/ic_close_white_24px.svg' title='Close' alt="Close X" height="16"
                                           width="16"></div>
        </a>
        <h6><img src="images/switch_user.svg" alt="Switch user" height="30" width="30"> Change username to:</h6><br/>
    </div>

    <div id="algosel">
        <div id="tree"></div>
    </div>
    <div class="demoarea">
        <div id="viewer" class="openseadragon"></div>
    </div>
    <div id="navigator"></div>
</div>

<div id="confirmDelete" style="display:none">
    <p> Please enter the secret: <input id="deleteSecret" type="password"/> <a href="#confirmDelete"
                                                                               rel="modal:close">
            <button id="confirmDeleteButton">Delete</button>
        </a></p>
</div>

<script type="text/javascript">
    $.noConflict();
    var annotool = null;
    var tissueId = <?php echo json_encode($_GET['tissueId']); ?>;
    console.log("tissueId is: " + tissueId);
    var cancerType = "<?php echo $_SESSION["cancerType"] ?>";
    console.log("cancerType is: " + cancerType);

    var imagedata = new OSDImageMetaData({imageId: tissueId});
    //console.log("imagedata: ", imagedata);

    var MPP = imagedata.metaData[0];
    console.log(MPP);
    //console.log(imagedata);
    var fileLocation = imagedata.metaData[1];//.replace("tcga_data","tcga_images");
    //console.log(fileLocation);

    //jQuery("#bookmarkURLDiv").hide();

    var viewer = new OpenSeadragon.Viewer({
        id: "viewer",
        prefixUrl: "images/",
        showNavigator: true,
        navigatorPosition: "BOTTOM_RIGHT",
        //navigatorId: "navigator",
        zoomPerClick: 2,
        animationTime: 0.75,
        maxZoomPixelRatio: 2,
        visibilityRatio: 1,
        constrainDuringPan: true
        //zoomPerScroll: 1
    });
    console.log(viewer.navigator);
    /*
    console.log(viewer.navigator);
    var zoomLevels = viewer.zoomLevels({
        levels: [0.001, 0.01, 0.2, 0.1, 1]
    });
    */

    viewer.addHandler("open", addOverlays);
    viewer.clearControls();
    viewer.open("<?php print_r($config['fastcgi_server']); ?>?DeepZoom=" + fileLocation);
    var imagingHelper = new OpenSeadragonImaging.ImagingHelper({viewer: viewer});
    imagingHelper.setMaxZoom(2); // TODO: 2 or 1 ?
    //console.log(this.MPP);
    viewer.scalebar({
        type: OpenSeadragon.ScalebarType.MAP,
        pixelsPerMeter: (1 / (parseFloat(this.MPP["mpp-x"]) * 0.000001)),
        xOffset: 5,
        yOffset: 10,
        stayInsideImage: true,
        color: "rgb(150,150,150)",
        fontColor: "rgb(100,100,100)",
        backgroundColor: "rgba(255,255,255,0.5)",
        barThickness: 2
    });

    /*
    // No longer using Filters/BRIGHTNESS
    osdVersion = OpenSeadragon.version;
    if ((osdVersion.major === 2 && osdVersion.minor >= 1) || osdVersion.major > 2) {
        // This plugin requires OpenSeadragon 2.1+
        viewer.setFilterOptions({
            filters: {
                processors: OpenSeadragon.Filters.BRIGHTNESS(0)
            }
        });
    }
    */

    //console.log(viewer);

    function isAnnotationActive() {
        this.isOpera = (!!window.opr && !!opr.addons) || navigator.userAgent.indexOf(' OPR/') >= 0;
        // console.log("isOpera", this.isOpera);
        this.isFirefox = typeof InstallTrigger !== 'undefined';
        // console.log("isFirefox", this.isFirefox);
        this.isSafari = ((navigator.userAgent.toLowerCase().indexOf('safari') > -1) && !(navigator.userAgent.toLowerCase().indexOf('chrome') > -1) && (navigator.appName == "Netscape"));
        // console.log("isSafari", this.isSafari);
        this.isChrome = !!window.chrome && !!window.chrome.webstore;
        // console.log("isChrome", this.isChrome);
        this.isIE = /*@cc_on!@*/false || !!document.documentMode;
        // console.log("isIE", this.isIE);
        this.annotationActive = !( this.isIE || this.isOpera);
        // console.log("annotationActive", this.annotationActive);
        return this.annotationActive;
    }

    function addOverlays() {
        var annotationHandler = new AnnotoolsOpenSeadragonHandler(viewer, {});
        //var sessionUsername = 'test@gmail.com';
        // TODO:
        var sessionUsername = <?php echo '"' . $_SESSION['email'] . '"' ?>;

        annotool = new annotools({
            canvas: 'openseadragon-canvas',
            iid: tissueId,
            viewer: viewer,
            annotationHandler: annotationHandler,
            mpp: MPP,
            cancerType: cancerType, // TODO:
            username: sessionUsername
        });
        filteringtools = new FilterTools(); // TODO:

        //console.log(tissueId);
        var toolBar = new ToolBar('tool', {
            left: '0px',
            top: '0px',
            height: '48px',
            width: '100%',
            iid: tissueId,
            annotool: annotool,
            FilterTools: filteringtools,
            cancerType: cancerType // TODO:
        });

        annotool.toolBar = toolBar;
        annotationHandler.annotool = annotool; // TODO:
        annotationHandler.toolbar = toolBar; // TODO:
        toolBar.createButtons();
        //var panel = new panel();
        jQuery("#panel").hide();

        /* weight / markup / switch user */
        jQuery("#weightpanel").hide();
        jQuery("#markuppanel").hide();
        jQuery("#switchuserpanel").hide();

        /* Close weight panel */
        jQuery('#closeWeightPanel').click(function (e) {
            e.preventDefault();
            jQuery("#weightpanel").hide('slide');
        });

        /* Close markup panel */
        jQuery('#closeMarkupPanel').click(function (e) {
            e.preventDefault();
            annotool.mode = 'normal';
            jQuery("canvas").css("cursor", "default");
            jQuery("#freeLineMarkupButton").removeClass("active");
            jQuery("#markuppanel").hide('slide');
            annotool.drawLayer.hide();
            annotool.addMouseEvents();
        });

        var user_email = "<?php echo $_SESSION["email"]; ?>";
        console.log("user_email :" + user_email);

        var index = user_email.indexOf("@");
        var user = user_email.substring(0, index);
        var execution_id = user + "_composite_input";

        annotool.execution_id = execution_id;
        annotool.user = user;
        console.log("execution_id :" + annotool.execution_id);

        /*Pan and zoom to point*/
        var bound_x = <?php echo json_encode($_GET['x']); ?>;
        var bound_y = <?php echo json_encode($_GET['y']); ?>;
        var zoomTmp = <?php echo json_encode($_GET['zoom']); ?> ||
        viewer.viewport.getMaxZoom();
        var zoom = Number(zoomTmp); // convert string to number if zoom is string

        /*
        var savedFilters = [
          {'name': 'Brightness', 'value': 100},
          {'name': 'Erosion', 'value': 3},
          {'name': 'Invert'}
        ]

        if (savedFilters) {
          console.log('some filters are saved')
          console.log(filteringtools)
          filteringtools.showFilterControls();
          for(var i=0; i<savedFilters.length; i++){

                console.log(i);
                var f = savedFilters[i];
                var filterName = f.name;
                console.log(filterName);
                jQuery("#"+filterName+"_add").click();
                jQuery("#control"+filterName).val(f.value);
                jQuery("#control"+filterName+"Num").val(f.value);
            }
        }*/

        checkState();

        if (bound_x && bound_y) {
            var ipt = new OpenSeadragon.Point(+bound_x, +bound_y);
            var vpt = viewer.viewport.imageToViewportCoordinates(ipt);
            viewer.viewport.panTo(vpt);
            viewer.viewport.zoomTo(zoom);
        } else {
            console.log("bounds not specified");
        }
    }//end of addOverlays()

    function checkState() {
        var stateID = <?php echo json_encode($_GET['stateID']); ?>;
        //Check if loading from saved state
        if (stateID) {
            //fetch state from firebase
            jQuery.get("https://test-8f679.firebaseio.com/camicroscopeStates/" + stateID + ".json?auth=kweMPSAo4guxUXUodU0udYFhC27yp59XdTEkTSJ4", function (data) {

                var savedFilters = data.state.filters;
                var viewport = data.state.viewport;
                var pan = data.state.pan;
                var zoom = data.state.zoom || viewer.viewport.getMaxZoom();

                //pan and zoom have preference over viewport
                if (pan && zoom) {
                    viewer.viewport.panTo(pan);
                    viewer.viewport.zoomTo(zoom);

                } else {
                    if (viewport) {
                        console.log("here");
                        var bounds = new OpenSeadragon.Rect(viewport.x, viewport.y, viewport.width, viewport.height);
                        viewer.viewport.fitBounds(bounds, true);
                    }
                }
                // check if there are savedFilters
                if (savedFilters) {
                    filteringtools.showFilterControls();

                    for (var i = 0; i < savedFilters.length; i++) {


                        var f = savedFilters[i];
                        var filterName = f.name;

                        jQuery("#" + filterName + "_add").click();
                        if (filterName === "SobelEdge") {
                            console.log("sobel");
                        } else {
                            jQuery("#control" + filterName).val(1 * f.value);
                            jQuery("#control" + filterName + "Num").val(1 * f.value);

                        }
                    }
                }
                filteringtools.updateFilters();

            });
        }
    }

    if (!String.prototype.format) {
        String.prototype.format = function () {
            var args = arguments;
            return this.replace(/{(\d+)}/g, function (match, number) {
                return typeof args[number] != 'undefined'
                    ? args[number]
                    : match
                    ;
            });
        };
    }

</script>
