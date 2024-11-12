<?php

namespace Stanford\GoogleStorage;
/** @var \Stanford\GoogleStorage\GoogleStorage $module */

try {
    if (isset($_GET['action']) && $_GET['action'] == 'upload') {
        $contentType = htmlspecialchars($_GET['content_type']);
        $fileName = htmlspecialchars($_GET['file_name']);
        $fieldName = htmlspecialchars($_GET['field_name']);
        $recordId = htmlspecialchars($_GET['record_id']);
        $eventId = filter_var($_GET['event_id'], FILTER_SANITIZE_NUMBER_INT);
        $instanceId = filter_var($_GET['instance_id'], FILTER_SANITIZE_NUMBER_INT);
        $bucket = $module->getBucket($fieldName);
        $prefix = $module->getFieldBucketPrefix($fieldName);
        $path = $module->buildUploadPath($prefix, $fieldName, $fileName, $recordId, $eventId, $instanceId);
        $response = $module->getGoogleStorageSignedUploadUrl($bucket, $path, $contentType);
        \REDCap::logEvent((defined('USERID')?USERID:'[survey-respondent]') . " generated Upload signed URL for $fileName ", '', null, null);
        $result = json_encode(array('status' => 'success', 'url' => $response, 'path' => $path), JSON_THROW_ON_ERROR);
        echo htmlentities($result, ENT_QUOTES);;
    } elseif (isset($_GET['action']) && $_GET['action'] == 'download') {
        $fileName = htmlspecialchars($_GET['file_name']);
        $fieldName = htmlspecialchars($_GET['field_name']);
        $bucket = $module->getBucket($fieldName);
        $link = $module->getGoogleStorageSignedUrl($bucket, trim($fileName));
        \REDCap::logEvent((defined('USERID')?USERID:'[survey-respondent]') . " generated Download signed URL for $fileName ", '', null, null);

        $result = json_encode(array('status' => 'success', 'link' => $link), JSON_THROW_ON_ERROR);
        echo htmlentities($result, ENT_QUOTES);;
    } else {
        throw new \Exception("cant find required action");
    }

} catch (\LogicException $e) {
    $module->emError($e->getMessage());
    http_response_code(404);
    $result = json_encode(array('status' => 'error', 'message' => $e->getMessage()), JSON_THROW_ON_ERROR);
    echo htmlentities($result, ENT_QUOTES);;
} catch (\Exception $e) {
    $module->emError($e->getMessage());
    http_response_code(404);
    $result = json_encode(array('status' => 'error', 'message' => $e->getMessage()), JSON_THROW_ON_ERROR);
    echo htmlentities($result, ENT_QUOTES);;
}
?>