<?php

namespace Stanford\GoogleStorage;
/** @var \Stanford\GoogleStorage\GoogleStorage $module */

try {
    $result = json_encode($module->saveRecord(), JSON_THROW_ON_ERROR);
    echo htmlentities($result, ENT_QUOTES);;
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