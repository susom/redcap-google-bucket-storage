<?php
namespace Stanford\GoogleStorage;
/** @var \Stanford\GoogleStorage\GoogleStorage $module */

try {
    $bucket = $module->getBucket('redcap-storage-test');
    $response = $module->getGoogleStorageSignedUploadUrl($bucket, 'config.json');

    print('Generated PUT signed URL:' . PHP_EOL);
    print($response . PHP_EOL);
    print('You can use this URL with any user agent, for example:' . PHP_EOL);
    print("curl -X PUT -H 'Content-Type: application/octet-stream' " .
        '--upload-file my-file ' . $response . PHP_EOL);
} catch (\Exception $e) {
    echo $e->getMessage();
}
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
    <input name="file" type="file"/>
    <input type="button" value="Upload"/>
</form>
<progress></progress>
<script>

    $(':button').on('click', function () {
        var data = new FormData($('#test-form')[0]);
        console.log(data)
        $.ajax({
            // Your server script to process the upload
            url: "<?php echo $response ?>",
            type: 'PUT',

            // Form data
            data: data,

            // Tell jQuery not to process data or worry about content-type
            // You *must* include these options!
            processData: false,
            contentType: false,
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


