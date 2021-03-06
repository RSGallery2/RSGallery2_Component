/**
 * @package     RSGallery2
 *
 * supports zip/ftp upload buttons
 * supports ajax drag and drop file upload wit two calls
 *
 * @subpackage  com_rsgallery2
 * @copyright   (C) 2016-2018 RSGallery2 Team
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author      finnern
 * @since       4.3.0
 */

//--------------------------------------------------------------------------------------
// new functions and new submit buttons
//--------------------------------------------------------------------------------------

/**
 * call imagesProperties view. There assign properties to dropped files
 */
Joomla.submitAssign2DroppedFiles = function () {
//        alert('submitAssignDroppedFiles:  ...');
    var form = document.getElementById('adminForm');

    // ToDo: check if one image exists

    form.task.value = 'imagesProperties.PropertiesView';

    form.submit();
};

/**
 * Upload zip file chacks and calls
 */
Joomla.submitButtonManualFileZipPc = function () {

    // alert('Upload Manual File Zip from Pc: controller upload.uploadFromZip ...');

    var form = document.getElementById('adminForm');

    var zip_path = form.zip_file.value;
    var gallery_id = jQuery('#SelectGalleries_01').val();
    var bOneGalleryName4All = jQuery('input[name="all_img_in_step1_01"]').val();

    // No file path given
    if (zip_path == "") {
        alert(Joomla.JText._('COM_RSGALLERY2_ZIP_MINUS_UPLOAD_SELECTED_BUT_NO_FILE_CHOSEN'));
    }
    else {
        // Is invalid galleryId selected ?
        if (bOneGalleryName4All && (gallery_id < 1)) {
            alert(Joomla.JText._('COM_RSGALLERY2_PLEASE_CHOOSE_A_CATEGORY_FIRST') + '(2)');
        }
        else {
            // yes transfer files ...
            form.task.value = 'upload.uploadFromZip'; // upload.uploadZipFile
            form.batchmethod.value = 'zip';
            form.ftppath.value = "";
            form.xcat.value = gallery_id;
            form.selcat.value = bOneGalleryName4All;
            form.rsgOption.value = "";


            jQuery('#loading').css('display', 'block');

            form.submit();

        }
    }
};
/**/

/*
 * Upload server file checks and calls
 */
Joomla.submitButtonManualFileFolderServer = function () {

    // alert('Upload Folder server: upload.uploadFromFtpFolder ...');

    var form = document.getElementById('adminForm');

    //var GalleryId = jQuery('#SelectGalleries_03').chosen().val();
    var gallery_id = jQuery('#SelectGalleries_02').val();
    var ftp_path = form.ftp_path.value;
    var bOneGalleryName4All = jQuery('input[name="all_img_in_step1_02"]').val();

    // ftp path is not given
    if (ftp_path == "") {
        alert(Joomla.JText._('COM_RSGALLERY2_FTP_UPLOAD_CHOSEN_BUT_NO_FTP_PATH_PROVIDED'));
    }
    else {
        // Is invalid galleryId selected ?
        if (bOneGalleryName4All && (gallery_id < 1)) {
            alert(Joomla.JText._('COM_RSGALLERY2_PLEASE_CHOOSE_A_CATEGORY_FIRST') + '(4)');
        }
        else {
            // yes transfer files ...
            form.task.value = 'upload.uploadFromFtpFolder'; // upload.uploadZipFile
            form.batchmethod.value = 'FTP';
            form.ftppath.value = ftp_path;
            form.xcat.value = gallery_id;
            form.selcat.value = bOneGalleryName4All;
            form.rsgOption.value = "";

            //jQuery('#loading').css('display', 'block');

            form.submit();
        }
    }
};
/**/

/**-------------------------------------------------------------------------------------
 * drag and drop files
 * -------------------------------------------------------------------------------------
 * each dropped filename gets a statusbar.
 * All image file names are collected in a list in order as they are dropped
 * Each filename will be send by ajax to create a database entry to keep the dropped order
 * On answer the files are uploaded parallel all at once
 * -------------------------------------------------------------------------------------
 */

jQuery(document).ready(function ($) {

    // ToDo: Test following with commenting out
    if (typeof FormData === 'undefined') {
        $('#legacy-uploader').show();
        $('#uploader-wrapper').hide();
        alert("exit");
        return;
    }

    var dragZone = $('#dragarea');
    var fileInput = $('#hidden_file_input');
    var buttonManualFile = $('#select_manual_file');
    var urlFileUpload = 'index.php?option=com_rsgallery2&task=upload.uploadAjaxSingleFile';
    var urlReserveDbImageId = 'index.php?option=com_rsgallery2&task=upload.uploadAjaxReserveDbImageId';
    var returnUrl = $('#installer-return').val();
    var gallery_id = $('#SelectGalleries_03').val();

    var dbReserveQueue = []; // File list for database image id request (keeping order)
    var uploadQueue = []; // File and data list waiting to be uploaded
    var imagesDroppedList = [];

    /*----------------------------------------------------
    Red or green border for drag and drop images
    ----------------------------------------------------*/

    if (!$('#SelectGalleries_03').val()) {
        $('#dragarea').addClass('dragareaDisabled')
    }

    $('#SelectGalleries_03').change(function () {
        // drop disabled ?
        if ($(this).val() == 0) {
            // $('#dragarea').css('border', '4px dotted red');
            $('#dragarea').addClass('dragareaDisabled')
        }
        else {
            //$('#dragarea').css('border', '4px dotted darkgreen');
            $('#dragarea').removeClass('dragareaDisabled')
        }
    });

    buttonManualFile.on('click', function (e) {
        //alert('buttonManualFile.on click: '); // + JSON.stringify($(this)));
        fileInput.click();
    });

    fileInput.on('change', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var gallery_id = $('#SelectGalleries_03').val();

        // prevent empty gallery
        if (gallery_id < 1) {
            alert(Joomla.JText._('COM_RSGALLERY2_PLEASE_CHOOSE_A_CATEGORY_FIRST') + '(5)');
        }
        else {

            var files = e.target.files;
            // files exist ?
            if (!files.length) {
                return;
            }

            var progressArea = $('#uploadProgressArea');
            prepareReserveDbImageId(files, progressArea);
        }
    });

    dragZone.on('dragenter', function (e) {
        e.preventDefault();
        e.stopPropagation();

        dragZone.addClass('hover');

        return false;
    });

    // Notify user when file is over the drop area
    dragZone.on('dragover', function (e) {
        e.preventDefault();
        e.stopPropagation();

        dragZone.addClass('hover');

        return false;
    });

    dragZone.on('dragleave', function (e) {
        e.preventDefault();
        e.stopPropagation();

        dragZone.removeClass('hover');

        return false;
    });

    /*----------------------------------------------------
    Drop files
    ----------------------------------------------------*/

    dragZone.on('drop', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var gallery_id = $('#SelectGalleries_03').val();

        // prevent empty gallery
        if (gallery_id < 1) {
            alert(Joomla.JText._('COM_RSGALLERY2_PLEASE_CHOOSE_A_CATEGORY_FIRST') + '(6)');
        }
        else {

            // collect files
            var files = e.originalEvent.target.files || e.originalEvent.dataTransfer.files;
            if (!files.length) {
                return;
            }

            dragZone.removeClass('hover');

            //--- We need to send dropped files to Server -------------

            // where the progress elements willbe created
            var progressArea = $('#uploadProgressArea');

            // files: start (prepare) sending
            prepareReserveDbImageId(files, progressArea);

        } // empty gallery
    });

    //--- no other drop on the form ---------------------

    $(document).on('dragenter', function (e) {
        e.stopPropagation();
        e.preventDefault();
    });
    $(document).on('dragover', function (e) {
        e.stopPropagation();
        e.preventDefault();
    });
    $(document).on('drop', function (e) {
        e.stopPropagation();
        e.preventDefault();
    });

    function wait(ms) {
        var d = new Date();
        var d2 = null;
        do {
            d2 = new Date();
        }
        while (d2 - d < ms);
    }

    // Uploading image count
    var imgCount = 0;

    //=================================================================================
    // Handle status bar for one actual uploading image
    function createStatusBar(progressArea) {
        imgCount++;
        var row = "odd";
        if (imgCount % 2 == 0) {
            row = "even";
        }

        // Add all elements. single line in *.css
        this.statusbar = $("<div class='statusbar " + row + "'></div>");
        this.filename = $("<div class='filename'></div>").appendTo(this.statusbar);
        this.size = $("<div class='filesize'></div>").appendTo(this.statusbar);
        this.progressBar = $("<div class='progressBar'><div></div></div>").appendTo(this.statusbar);
        //this.order = $("<div class='tmp_order'>O:____</div>").appendTo(this.statusbar);
        this.abort = $("<div class='abort'>Abort</div>").appendTo(this.statusbar);

        //// set as first element: Latest file on top to compare if already shown in image area
        //progressArea.prepend(this.statusbar);
        // set as last element: Latest file on top to compare if already shown in image area
        progressArea.append(this.statusbar);

        //--- file size in KB/MB .... -------------------

        this.setFileNameSize = function (name, size) {
            var sizeStr = "";
            var sizeKB = parseInt(size) / 1024;

            if (sizeKB > 1024) {
                var sizeMB = sizeKB / 1024;
                sizeStr = sizeMB.toFixed(2) + " MB";
            }
            else {
                sizeStr = sizeKB.toFixed(2) + " KB";
            }

            this.filename.html(name);
            this.size.html(sizeStr);
        };

        /**
        // Helper to check on right order
        this.AddOrderText = function (OrderTxt) {
            // alert ('OrderTxt: ' + OrderTxt);
            this.order.html('O: ' + OrderTxt);
        };
        /**/

        //========================================
        // Change progress value
        this.setProgress = function (progress) {

            var progressBarWidth = progress * this.progressBar.width() / 100;
            this.progressBar.find('div').animate({width: progressBarWidth}, 10).html(progress + "%");
            if (parseInt(progress) >= 99.999) {

                this.abort.hide();

            }
        };

        //========================================
        // Handle abort click
        // ToDo: Test for second ajax still working ?
        this.setAbort = function (jqxhr) {
            var sb = this.statusbar;
            this.abort.click(function () {
                jqxhr.abort();
                sb.hide();
            });
        }


        //========================================
        // Remove item after successful file upload
        // ToDo: Test for second ajax still working ?
        this.remove = function () {

            var sb = this.statusbar;
            sb.hide();

        /**
            this.abort.click(function () {
                jqxhr.abort();
                sb.hide();
            });
         /**/
        }
    }

    //=================================================================================
    // Add file data to a list which will be processed in order of appearance
    // Parameter: image file set, progressArea
    function prepareReserveDbImageId(files, progressArea) {

        // ToDo: On first file upload disable gallery change and isone .. change

        var gallery_id = $('#SelectGalleries_03').val();

        // All files selected by user
        for (var idx = 0; idx < files.length; idx++) {

            console.log('in: ' + files[idx].name);

            // ToDo: Check file size

            // Save for later send
            imagesDroppedList.push(files[idx]);
            var imagesDroppedListIdx = imagesDroppedList.length -1;

            // for function reserveDbImageId
            var data = new FormData();
            // data.append('upload_file', files[idx]);
            data.append('upload_file', files[idx].name);
            data.append('imagesDroppedListIdx', imagesDroppedListIdx);

            data.append(Token, '1');
            data.append('gallery_id', gallery_id);
            //data.append('idx', idx);

            // Set progress bar
            var statusBar = new createStatusBar(progressArea);
            statusBar.setFileNameSize(files[idx].name, files[idx].size);

            var queueObj = {};
            queueObj.data = data;
            queueObj.statusBar = statusBar;
            queueObj.fileName = files[idx].name;

            dbReserveQueue.push(queueObj);
        }

        // Send file from list
        startReserveDbImageId();
    }

    //=================================================================================
    // If sending is not busy start with oldest file.
    // Call first step -> reserve DB item and return ID

    var sendState = 0; // 1 busy
    // var sendTimeout = 3000; // sec: continue sending next on no answer or error -> ToDo: alarm ?

    function startReserveDbImageId() {

        // Not busy
        if (sendState == 0) {
            if (dbReserveQueue.length > 0) {
                var queueObj = dbReserveQueue.shift();
                var data = queueObj.data;
                var statusBar = queueObj.statusBar;
                var fileName = queueObj.fileName;

                sendState = 1; // 1 busy

                reserveDbImageId(data, statusBar, fileName);
            }
        }
        else {
            alert("0X.startReserveDbImageId. !!! Busy !!!");
        }
    }

    //=================================================================================
    // 1. ajax request for image database reservation so order is intact before sending files
    // ajax returns image db ID
    function reserveDbImageId (formData, statusBar, fileName) {

        // only running in firefox and chrome
        console.log('reserveDB: ' + fileName);

        /*=========================================================
         ajax: Reserve database image entry for the order of the dropped
        =========================================================*/

        var jqXHR = jQuery.ajax({
            //xhr: function () {
            //    var xhrobj = jQuery.ajaxSettings.xhr();
            //}
            url: urlReserveDbImageId,
            type: "POST",
            contentType: false,
            processData: false,
            cache: false,
            // timeout:20000, // 20 seconds timeout (was too short)
            data: formData
        })

        /*----------------------------------------------------
        On success / done
        ----------------------------------------------------*/

        .done(function (eData, textStatus, jqXHR) {
            //alert('done reserveDbImageId.Success: "' + String(eData) + '"')

            console.log('reserveDb: Success');

            var jData;

            // Start next ajax reserveDbImageId image file
            sendState = 0; // 1 == busy
            startReserveDbImageId();

            //--- Handle PHP Error and notification messages first (separate) -------------------------

            // is first part php error- or debug- echo string ?
            // find start of json
            var StartIdx = eData.indexOf('{"');
            if (StartIdx == 0) {
                jData = jQuery.parseJSON(eData);
            }
            else {
                // find error html text
                var errorText = eData.substring(0, StartIdx - 1);
                // append to be viewed
                var progressArea = $('#uploadProgressArea');
                progressArea.append(errorText);

                // extract json data of uploaded image
                var jsonText = eData.substring(StartIdx);
                jData = jQuery.parseJSON(jsonText);
            }

            // Check that JResponseJson data structure may be available
            if (!'success' in jData) {
                alert('reserveDbImageId: returned wrong data');
                return;
            }

            // ToDo: Handle JOOMLA Error and notification messages -> inside Json

            //--- success -------------------------


            // ID received
            if (jData.success == true) {

                // Prepare real file sending (upload)

                var gallery_id = $('#SelectGalleries_03').val();

                // for function sendFileToServer
                var data = new FormData();

                // fetch saved file data
                var imagesDroppedListIdx = jData.data.imagesDroppedListIdx;
                if (imagesDroppedListIdx < 0 || imagesDroppedList.length < imagesDroppedListIdx)
                {
                    alert('reserveDbImageId: imagesDroppedListIdx: ' + imagesDroppedListIdx + ' out of range (' + imagesDroppedList.length + ')');

                    return;
                }

                // Upload file data
                var UploadFile = imagesDroppedList [imagesDroppedListIdx];
                console.log('imagesDroppedListIdx: ' + imagesDroppedListIdx);
                console.log('UploadFile1: ' + UploadFile);
                data.append('upload_file', UploadFile);

                data.append(Token, '1');
                data.append('gallery_id', gallery_id);
                data.append('cid', jData.data.cid);
                data.append('fileName', jData.data.uploadFileName);
                console.log ('   >fileName: ' + jData.data.uploadFileName);
                data.append('targetFileName', jData.data.targetFileName);
                console.log ('   >targetFileName: ' + jData.data.targetFileName);

                uploadFileName = jData.data.uploadFileName;

                //// Add order text
                // statusBar.AddOrderText (jData.data.order);

                // start sending file
//                sendFileToServer(data, statusBar, uploadFileName);
                startUploadFile(data, statusBar, uploadFileName);
            }
            else {
                alert('Result Error 05');
                // error on file transfer
                var msg = jData.message;
                alert('Error on reserve DB image ID (1): "' + msg + '"');
                alert("eData: " + eData);

                // ToDo: Use count ....
                msg = jData.messages.error[0];
                alert("Error on reserveDbImageId (2): " + msg);
            }

        })

        /*----------------------------------------------------
        On fail / error
        ----------------------------------------------------*/

        .fail(function (jqXHR, textStatus, exceptionType) {

            console.log('reserveDb: fail');

            //// start next upload
            //sendState = 0; // 1 == busy
            //startReserveDbImageId () ;

            // alert ('fail: Status: "' + textStatus + '" exceptionType: "' + exceptionType + '" [' + jqXHR.status + ']');
            alert('reserveDbImageId: Drag and drop upload failed: "' + textStatus + '" -> "' + exceptionType + '" [' + jqXHR.status + ']');

            console.log(jqXHR);
        })

        /*----------------------------------------------------
        On always / complete
        ----------------------------------------------------*/

        .always(function (eData, textStatus, jqXHR) {
            //alert ('always: "' + textStatus + '"');

        });

    }


    //=================================================================================
    // If uploading parallel is below limit (4) call upload. Otherwise queue request

    var uploadCount = 0; // Number of actual parallel uploads
    var uploadLimit = 4;

    function startUploadFile(uploadData, statusBar, uploadFileName) {

        // Not too many upload parallel
        if (uploadCount < uploadLimit) {

            uploadCount++;

            sendFileToServer(uploadData, statusBar, uploadFileName);
        }
        else {
            var queueObj = {};
            queueObj.uploadData = uploadData;
            queueObj.statusBar = statusBar;
            queueObj.uploadFileName = uploadFileName;

            uploadQueue.push(queueObj);
        }
    }

    function uploadFinishedTryNext ()
    {
        // other files to be uploaded
        if (uploadQueue.length > 0) {
            var queueObj = uploadQueue.shift();
            var uploadData = queueObj.uploadData;
            var statusBar = queueObj.statusBar;
            var uploadFileName = queueObj.uploadFileName;

            sendFileToServer(uploadData, statusBar, uploadFileName);
        }
        else {
            uploadCount--;

            // Safety net: If some events are missing try to upload the rest
            while (uploadCount < 3 && (uploadQueue.length > 0)) {
                var queueObj = uploadQueue.shift();
                var uploadData = queueObj.uploadData;
                var statusBar = queueObj.statusBar;
                var uploadFileName = queueObj.uploadFileName;

                sendFileToServer(uploadData, statusBar, uploadFileName);

                uploadCount++;
            }

        }
    }


    //=================================================================================
    // 2. ajax request for image sending to server.
    // Afterwards handling progress bar and display of image on result
    // ajax returns ???
    // ToDo: Replace fileName with data['fileName']
    function sendFileToServer(formData, statusBar, uploadFileName) {

        console.log('sendFile: ' + uploadFileName);

        /*=========================================================
          ajax: Upload original file to server and create dependend images
        =========================================================*/

        var jqXHR = jQuery.ajax({
            xhr: function () {
                var xhrobj = jQuery.ajaxSettings.xhr();
                if (xhrobj.upload) {
                    xhrobj.upload.addEventListener('progress', function (event) {
                        var percent = 0;
                        // if (event.lengthComputable) {
                        var position = event.loaded || event.position;
                        var total = event.total;
                        if (event.lengthComputable) {
                            percent = Math.ceil(position / total * 100);
                        }
                        //Set progress
                        statusBar.setProgress(percent);
                    }, false);
                }
                return xhrobj;
            },
            url: urlFileUpload,
            type: "POST",
            contentType: false,
            processData: false,
            cache: false,
            // timeout:20000, // 20 seconds timeout (was too short)
            data: formData
        })

        /*----------------------------------------------------
        On success / done
        ----------------------------------------------------*/

        //---  --------------------------------
        .done(function (eData, textStatus, jqXHR) {
            //alert('done Success: "' + String(eData) + '"')

            console.log('sendFile: Success');

            var jData;

            //--- Handle PHP Error and notification messages first (separate) -------------------------

            // is first part php error- or debug- echo string ?
            // find start of json
            var StartIdx = eData.indexOf('{"');
            if (StartIdx == 0) {
                jData = jQuery.parseJSON(eData);
            }
            else {
				console.log('sendFile: Success with message');
                // find error html text
                var errorText = eData.substring(0, StartIdx - 1);
                // append to be viewed
                var progressArea = $('#uploadProgressArea');
                progressArea.append(errorText);

                // extract json data of uploaded image
                var jsonText = eData.substring(StartIdx);
                jData = jQuery.parseJSON(jsonText);
            }

            // Check that JResponseJson data structure may be available
             if (!'success' in jData) {
                alert('Drag and drop returned wrong data');
                return;
            }

            // ToDo: Handle JOOMLA Error and notification messages -> inside Json

            //--- success -------------------------

            // file successful transferred
            if (jData.success == true) {
				// console.log('sendFile: Html image link ');

                // Add HTML to show thumb of uploaded image

                this.imageBox = $("<li></li>").appendTo($('#imagesAreaList'));
                this.thumbArea = $("<div class='thumbnail imgProperty'></div>").appendTo(this.imageBox);
                this.imgComntainer = $("<div class='imgContainer' ></div>").appendTo(this.thumbArea);
                this.imageDisplay = $("<img class='img-rounded' data-src='holder.js/600x400' src='" + jData.data.dstFile + "' alt='' />").appendTo(this.imgComntainer);

                this.caption = $("<div class='caption' ></div>").appendTo(this.imageBox);
                this.imageDisplay = $("<small>" + jData.data.file + "</small>").appendTo(this.caption);
                this.imageId = $("<small> (" + jData.data.cid + ":" + jData.data.order + ")</small>").appendTo(this.imageDisplay);
                this.cid = $("<input name='cid[]' class='imageCid' type='hidden' value='" + jData.data.cid + "' />").appendTo(this.imageBox);

                // Find statusbar and remove it

                statusBar.remove ();
            }
            else {
                alert('Result Error 05');
                // error on file transfer
                var msg = jData.message;
                alert('Error on file transfer (1): "' + msg + '"');
                alert("eData: " + eData);

                // ToDo: Use count ....
                msg = jData.messages.error[0];
                alert("Error on file transfer (2): " + msg);
            }

        })

        /*----------------------------------------------------
        On fail / error
        ----------------------------------------------------*/

        .fail(function (jqXHR, textStatus, exceptionType) {

            console.log('sendFile: fail');

            //// start next upload
            //sendState = 0; // 1 == busy
            //startReserveDbImageId () ;

            // alert ('fail: Status: "' + textStatus + '" exceptionType: "' + exceptionType + '" [' + jqXHR.status + ']');
            alert('Drag and drop upload failed: "' + textStatus + '" -> "' + exceptionType + '" [' + jqXHR.status + ']');

            console.log(jqXHR);
        })

        /*----------------------------------------------------
        On always / complete
        ----------------------------------------------------*/

        .always(function (eData, textStatus, jqXHR) {
            //alert ('always: "' + textStatus + '"');
            uploadFinishedTryNext ();
        });

        // create abort HTML
        statusBar.setAbort(jqXHR);
    }

}); // Joomla ready ... ?

//--------------------------------------------------------------------------------------
// "old" submit buttons
//--------------------------------------------------------------------------------------

/* Deprecated old single image upload */
Joomla.submitButtonManualFileSingle = function () {

    // alert('Upload single images: legacy ...');

    var form = document.getElementById('adminForm');

    // yes transfer files ...
    form.task.value = 'upload'; // upload.uploadZipFile
    form.batchmethod.value = 'zip';
    form.ftppath.value = "";
    form.xcat.value = "";
    form.selcat.value = "";

    form.submit();
};

/* Zip file */
Joomla.submitButtonManualFileZipPcLegacy = function () {

    //alert('Upload Manual File Zip from Pc: legacy ...');

    var form = document.getElementById('adminForm');

    var zip_path = form.zip_file.value;
    var gallery_id = jQuery('#SelectGalleries_01').val();
    //var bOneGalleryName4All = jQuery('input[name="all_img_in_step1_01"]:checked').val();
    var bOneGalleryName4All = jQuery('input[name="all_img_in_step1_01"]').val();

    // No file path given
    if (zip_path == "") {
        alert(Joomla.JText._('COM_RSGALLERY2_ZIP_MINUS_UPLOAD_SELECTED_BUT_NO_FILE_CHOSEN'));
    }
    else {

        // Is invalid galleryId selected ?
        if (bOneGalleryName4All && (gallery_id < 1)) {
            alert(Joomla.JText._('COM_RSGALLERY2_PLEASE_CHOOSE_A_CATEGORY_FIRST') + '(1)');
        }
        else {
            // yes transfer files ...
            form.task.value = 'batchupload'; // upload.uploadZipFile
            form.rsgOption.value = "images";
            form.batchmethod.value = 'zip';
            form.ftppath.value = "";
            form.xcat.value = gallery_id;
            form.selcat.value = bOneGalleryName4All;

            jQuery('#loading').css('display', 'block');

            form.submit();
        }
    }
};

/* from server */
Joomla.submitButtonManualFileFolderServerLegacy = function () {

    // alert('Upload Folder server: legacy ...');

    var form = document.getElementById('adminForm');

    var gallery_id = jQuery('#SelectGalleries_02').val();
    var ftp_path = form.ftp_path.value;
    var bOneGalleryName4All = jQuery('input[name="all_img_in_step1_02"]').val();

    // ftp path is not given
    if (ftp_path == "") {
        alert(Joomla.JText._('COM_RSGALLERY2_FTP_UPLOAD_CHOSEN_BUT_NO_FTP_PATH_PROVIDED'));
    }
    else {
        // Is invalid galleryId selected ?
        if (bOneGalleryName4All && (gallery_id < 1)) {
            alert(Joomla.JText._('COM_RSGALLERY2_PLEASE_CHOOSE_A_CATEGORY_FIRST') + '(3)');
        }
        else {
            // yes transfer files ...
            form.task.value = 'batchupload'; // upload.uploadZipFile
            form.rsgOption.value = "images";
            form.batchmethod.value = 'FTP';
            form.ftppath.value = ftp_path;
            form.xcat.value = gallery_id;
            form.selcat.value = bOneGalleryName4All;

            jQuery('#loading').css('display', 'block');

            form.submit();
        }
    }
};


