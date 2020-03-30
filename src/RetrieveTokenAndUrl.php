<?php
namespace Stanford\GoogleStorage;
/** @var \Stanford\GoogleStorage\GoogleStorage $module */

//use REDCap;


$action = isset($_GET['action']) && !empty($_GET['action']) ? $_GET['action'] : null;
$user = isset($_GET['user']) && !empty($_GET['user']) ? $_GET['user'] : null;
$pid = isset($_GET['pid']) && !empty($_GET['pid']) ? $_GET['pid'] : null;
$eventId = isset($_GET['eventId']) && !empty($_GET['eventId']) ? $_GET['eventId'] : null;
$field = isset($_GET['field']) && !empty($_GET['field']) ? $_GET['field'] : null;
$secret = isset($_GET['secret']) && !empty($_GET['secret']) ? $_GET['secret'] : null;

$module->emDebug("Action: $action, user: $user, pid: $pid, eventId: $eventId, field: $field, secret: $secret");

$return = array("status"   => 1,
                "token"    => "",
                "url"      => "https://redcap-pids-1-to-1000.storage.googleapis.com/pid_1/hello_world_again2.txt");

$module->emLog("Return data: " . json_encode($return));
return;

?>

<!DOCTYPE html>
<html lang="en">
<header>
    <title>RetrieveTokenAndUrl</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css"/>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
</header>

<body>
    <h2>RetrieveTokenAndUrl</h2>
</body>
</html>