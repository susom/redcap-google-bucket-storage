<?php

namespace Stanford\GoogleStorage;
/** @var \Stanford\GoogleStorage\GoogleStorage $module */

try {
    $contentType = filter_var($_GET['content_type'], FILTER_SANITIZE_STRING);
    $fileName = filter_var($_GET['file_name'], FILTER_SANITIZE_STRING);
    $bucketName = filter_var($_GET['bucket_name'], FILTER_SANITIZE_STRING);
    $bucket = $module->getBucket($bucketName);
    $response = $module->getGoogleStorageSignedUploadUrl($bucket, $fileName, $contentType);
    echo json_encode(array('status' => 'success', 'url' => $response));
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