<?php

namespace Stanford\GoogleStorage;

use ExternalModules\ExternalModules;

require_once "emLoggerTrait.php";

/**
* Class GoogleStorage
* @package Stanford\GoogleStorage
*
*
*/
class GoogleStorage extends \ExternalModules\AbstractExternalModule
{

    use emLoggerTrait;

    /******************************************************************************************************************/
    /* HOOK METHODS                                                                                                   */
    /******************************************************************************************************************/

    /******************************************************************************************************************/
    /* METHODS                                                                                                       */
    /******************************************************************************************************************/
    // Need to set environment variable
    // GOOGLE_APPLICATION_CREDENTIALS="path/to/service-account/key.json for storage.create

    // contentType and objectPath values should be sent from front-end
    /*
    $contentType = "image/png";
    $objectPath = "my-favorite-cat-photo.png";

    $bucket = "my-cat-bucket";
    $storage = StorageOptions.getDefaultInstance.getService;
    $blob = storage.create(
        BlobInfo.newBuilder(bucket, objectPath)
        .setContentType(contentType)
        .build()
    );

    // Create a signed URL valid for PUT'ing a file with Path <objectPath> and Content-Type "PUT". Valid for 1 day
    $urlPut = storage.signUrl(blob, 1, TimeUnit.DAYS, SignUrlOption.httpMethod(HttpMethod.PUT), SignUrlOption.withContentType());
*/
    /******************************************************************************************************************/
    /* CRON METHODS                                                                                                   */
    /******************************************************************************************************************/

}