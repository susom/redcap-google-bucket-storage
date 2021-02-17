<?php

namespace Stanford\GoogleStorage;
/** @var \Stanford\GoogleStorage\GoogleStorage $module */

try {
    $contentType = filter_var($_GET['content_type'], FILTER_SANITIZE_STRING);
    $fileName = filter_var($_GET['file_name'], FILTER_SANITIZE_STRING);
    $fieldName = filter_var($_GET['field_name'], FILTER_SANITIZE_STRING);
    $recordId = filter_var($_GET['record_id'], FILTER_SANITIZE_STRING);
    $eventId = filter_var($_GET['event_id'], FILTER_SANITIZE_NUMBER_INT);
    $instanceId = filter_var($_GET['instance_id'], FILTER_SANITIZE_NUMBER_INT);
    $bucket = $module->getBucket($fieldName);
    $prefix = $module->getFieldBucketPrefix($fieldName);
    $path = $module->buildUploadPath($prefix, $fieldName, $fileName, $recordId, $eventId, $instanceId);
    $response = $module->getGoogleStorageSignedUploadUrl($bucket, $path, $contentType);
    echo json_encode(array('status' => 'success', 'url' => $response, 'path' => $path));
} catch (\LogicException $e) {
    $module->emError($e->getMessage());
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
} catch (\Exception $e) {
    $module->emError($e->getMessage());
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}
?>