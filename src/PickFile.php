<?php
namespace Stanford\GoogleStorage;
/** @var \Stanford\GoogleStorage\GoogleStorage $module */

require_once $module->getModulePath() . '/google_src/vendor/autoload.php';

use Google;
//use Google\Cloud\Storage\StorageClient;
use Google\Cloud\Core\ServiceBuilder;
use Exception;

$action = isset($_GET['action']) && !empty($_GET['action']) ? $_GET['action'] : null;
$user = isset($_GET['user']) && !empty($_GET['user']) ? $_GET['user'] : null;
$pid = isset($_GET['pid']) && !empty($_GET['pid']) ? $_GET['pid'] : null;
$eventId = isset($_GET['eventId']) && !empty($_GET['eventId']) ? $_GET['eventId'] : null;
$field = isset($_GET['field']) && !empty($_GET['field']) ? $_GET['field'] : null;
$secret = isset($_GET['secret']) && !empty($_GET['secret']) ? $_GET['secret'] : null;

$module->emDebug("Action is " . $action);

if ($action == "SAVE" || $action == "GET") {
    $module->emDebug("Action: $action, user: $user, pid: $pid, eventId: $eventId, field: $field, secret: $secret");

    date_default_timezone_set('UTC');
    $date = date('D, d M Y h:i:s e');
    $module->emDebug("Current date: " . $date);

    // Get google client and retrieve a valid token
    $token = retrieveGoogleToken();

    $projectId = 'redcap-upload';
    $bucketName = 'redcap-pids-1-to-1000';
    $objectName = 'test.txt';
    $header = array(
        //"Authorization"     => "Bearer " . $token,
        "Content-Type"      => "text/plain",
        //"Host"              => "redcap-pids-1-to-1000.storage.googleapis.com",
        "Access-Control-Request-Method"     => "GET",
        "Access-Control-Request-Headers"    => array("X-PINGOTHER", "Content-Type"),
        "X-PINGOTHER"                       => "pingpong"
        //"Date"              => $date
    );

    // Get a signed URL for this request
    $url = generateSignedUrl($action, $projectId, $bucketName, $objectName, $header);

    $module->emDebug("Signed URL: " . $url);
    if (empty($url) || $url == "") {
        $status = 0;
    } else {
        $status = 1;
    }

    $return = array(
            "status" => $status,
            "header" => $header,
            "url"   => $url
    );

    $module->emLog("Return data: " . json_encode($return));
    print json_encode($return);
    return;
}


/**
 * Retrieve a google token
 */
function retrieveGoogleToken() {

    /*
    $gc = \Google::getClient();
    if($gc->isAccessTokenExpired())
        $gc->getAuth()->refreshTokenWithAssertion();
    return json_decode($gc->getAccessToken(), true)['access_token'];
    */

    // I retrieved this from my google sandbox which would regenerate the token for me.
    $token = '';

    return $token;
}


/**
 * Generate a v4 signed URL for uploading an object.
 *
 * @param string $bucketName the name of your Google Cloud bucket.
 * @param string $objectName the name of your Google Cloud object.
 *
 * @return string $url
 */

function generateSignedUrl($action, $projectId, $bucketName, $objectName, $header) {

    global $module;

    $module->emDebug("In generateSignedUrl: bucketName: " . $bucketName . ", objectName: " . $objectName);
    $url = "";

    try {

        $keyFile = $module->getModulePath() . 'REDCap upload-f1d15a1fcde5.json';

        $gcloud = new ServiceBuilder([
                'keyFilePath'   => $keyFile,
                'projectId'     => $projectId
        ]);
        $storage = $gcloud->storage();
        $module->emDebug("Create storage client: " . json_encode($storage));

        $bucket = $storage->bucket($bucketName);
        $module->emDebug("Bucket object: " . json_encode($bucket));

        $object = $bucket->object($objectName);
        $url = $object->signedUploadUrl(new \DateTime('5 min'), $header);

        $module->emDebug("In generateSignedUrl - url: " . $url);


    } catch (Exception $ex) {
        $module->emDebug("In exception");
        $module->emError("Could not create StorageClient object for Google bucket: " . json_encode($ex));
    }

    $module->emDebug("Signed URL: " . $url);
    return $url;
}

?>

<!DOCTYPE html>
<html lang="en">
    <header>
        <title>File Picker</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css"/>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    </header>
    <body>
        <div class="container">
            <h2 class="title">
                File Picker
            </h2>
            <div class="row">
                <div class="col-6">
                    <button onclick="document.getElementById('file-input').click();">Select File for Upload</button>
                    <input id="file-input" type="file" name="name" style="display: none;" />
                </div>   <!-- end column  -->
            </div>  <!-- end row -->
        </div>  <!-- end container -->
    </body>
</html>

<script>

    var file_name = document.getElementById("file-input");

    file_name.onchange = e => {

        // getting a hold of the file reference
        var input = document.createElement('file-input');
        input.type = 'file';
        input.click();
        var filename = e.target.files[0].name;
        if (filename !== "") {
            readFile(filename);
        }
    }

    function readFile(filename) {

        // setting up the reader
        /*
        var reader = new FileReader();
        reader.readAsDataURL(file); // this is reading as data url

        // here we tell the reader what to do when it's done reading...
        reader.onload = readerEvent => {
            var content = readerEvent.target.result; // this is the content!
            alert("Content: " + content);
            console.log("Content: " + content);
        }
        */

        // Just for testing purposes so I know what I uploading
        var contents = "Today is January 5, 2020!**";
        PickFile.getGoogleToken("GET", "yasukawa", "20", "eventId", "field", "secret", contents);

    }

    var PickFile = PickFile || {};

    // Retrieve the signed URL and either get or put a file based on the ask
    PickFile.getGoogleToken = function(action, user, pid, eventId, field, secret, file) {

        $.ajax({
            type: "GET",
            data: {
                "action"        : action,
                "user"          : user,
                "pid"           : pid,
                "eventId"       : eventId,
                "field"         : field,
                "secret"        : secret
            },
            success:function(return_data) {
                var data = JSON.parse(return_data);
                alert("Return from getting token: " + data.url + ", and header: " + data.header.Authorization);

                if (data.status === 1) {
                    if (action === "GET") {
                        PickFile.retrieveGoogleFile(data.url, data.header);

                    } else if (action === "SAVE") {
                        PickFile.saveGoogleFile(data.url, data.header, file);
                    }

                } else {
                    console.log("Bad status from token retrieval");
                }
            }
        }).done(function (status) {
            console.log("Done Status from token retrieval");
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log("Failed to retrieve Google token");
        });
    };


    // upload to google
    PickFile.saveGoogleFile = function(url, header, file) {

        alert("In saveGoogleFile: file contents: " + file);

        $.ajax({
            type: "PUT",
            url: url,
            header: header,
            data: file,
            success:function(status) {
                var return_status = JSON.parse(status);
                alert("Return from google: " + JSON.stringify(return_status));
                console.log("Return from google: " + JSON.stringify(return_status));
                /*
                if (status === '1') {
                    console.log("Successfully uploaded file: " + status);
                } else {
                    console.log("Unsuccessful in file upload: " + status);
                }
                */
            }
        }).done(function (status) {
            alert("Return from google upload: " + status);
        }).fail(function (jqXHR, textStatus, errorThrown) {
            alert("Failed to upload file - textStatus: " + textStatus);
            alert("jqXHR: " + JSON.stringify(jqXHR));
        });
    };


    // download file
    PickFile.retrieveGoogleFile = function(url, header) {

        alert("In retrieveGoogleFile");

        $.ajax({
            type: "GET",
            url: url,
            //data: header,
            data: {
                "Content-Type"                      : "text/plain",
                "Access-Control-Request-Method"     : "GET",
                "Access-Control-Request-Headers"    : "Content-Type"
            },
            success:function(status) {

                var return_status = JSON.parse(status);
                alert("Return from Google: " + JSON.stringify(return_status));
                /*
                if (status === '1') {
                    console.log("Successfully downloaded file: " + status);
                } else {
                    console.log("Unsuccessful in file download: " + status);
                }
                */
            }
        }).done(function (status) {
            var return_status = JSON.parse(status);
            console.log("Return from google download: " + JSON.stringify(return_status));
        }).fail(function (jqXHR, textStatus, errorThrown) {
            var parse_jqXHR = JSON.parse(jqXHR);
            console.log("jqXHR: " + JSON.stringify(parse_jqXHR));
            console.log("textStatus: " + textStatus);
        });
    };

</script>
