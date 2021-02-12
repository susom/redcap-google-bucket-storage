<?php
namespace Stanford\GoogleStorage;
/** @var \Stanford\GoogleStorage\GoogleStorage $module */

?>


<!DOCTYPE html>
<html lang="en">
<header>
    <title>Google Storage</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css"/>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
</header>
<body>
<h2>Google Storage Test</h2>
<form id="test-form" enctype="multipart/form-data">
    <input id="file-type" name="file" type="file" multiple/>
    <input type="button" value="Upload"/>
</form>
<progress></progress>
<script>
    var signedURL = ''
    var contentType = ''
    $("#file-type").on('change', function () {

        $.ajax({
            // Your server script to process the upload
            url: "<?php echo $module->getUrl('ajax/get_signed_url.php', false, true) ?>",
            type: 'GET',

            // Form data
            data: {
                'content_type': document.getElementById('file-type').files[0].type,
                'file_name': document.getElementById('file-type').files[0].name,
                'bucket_name': 'redcap-storage-test'
            },
            success: function (data) {
                var response = JSON.parse(data)
                if (response.status == 'success') {
                    signedURL = response.url
                    contentType = document.getElementById('file-type').files[0].type
                    console.log(signedURL)
                }
            }
        });
    })
    $(':button').on('click', function () {

        if (signedURL == '') {
            alert('Please make sure to select a file to upload');
            return;
        }
        //var formData = new FormData($('#test-form')[0]);
        //formData.append("file", document.getElementById("myFileField").files[0]);
        //var url = "<?php //echo $response ?>//"
        //var xhr = new XMLHttpRequest();
        //xhr.open("PUT", url);
        //
        //xhr.setRequestHeader("Content-Type", "application/octet-stream");
        //
        //xhr.onreadystatechange = function () {
        //    if (xhr.readyState === 4) {
        //        console.log(xhr.status);
        //        console.log(xhr.responseText);
        //    }};
        //
        //xhr.send(formData);

        var data = new FormData($('#test-form')[0]);
        $.ajax({
            // Your server script to process the upload
            url: signedURL,
            type: 'PUT',

            // Form data
            data: data,

            // Tell jQuery not to process data or worry about content-type
            // You *must* include these options!
            processData: false,
            contentType: false,
            cache: false,
            "headers": {
                "Access-Control-Allow-Origin": "*",
                "Content-Type": contentType,
            },
            complete: function () {
                signedURL = ''
                contentType = ''
            },
            // Custom XMLHttpRequest
            xhr: function () {
                var myXhr = $.ajaxSettings.xhr();
                if (myXhr.upload) {
                    // For handling the progress of the upload
                    myXhr.upload.addEventListener('progress', function (e) {
                        if (e.lengthComputable) {
                            $('progress').attr({
                                value: e.loaded,
                                max: e.total,
                            });
                        }
                    }, false);
                }
                return myXhr;
            }
        });
    });
</script><!-- end container -->
</body>
</html>


