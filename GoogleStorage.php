<?php

namespace Stanford\GoogleStorage;

require_once "emLoggerTrait.php";
require __DIR__ . '/vendor/autoload.php';

# Imports the Google Cloud client library
use Google\Cloud\Storage\StorageClient;
use Google\Cloud\Storage\Bucket;

/**
 * Class GoogleStorage
 * @package Stanford\GoogleStorage
 * @property \Google\Cloud\Storage\StorageClient $client
 * @property \Google\Cloud\Storage\Bucket[] $buckets
 * @property array $instances
 * test
 */
class GoogleStorage extends \ExternalModules\AbstractExternalModule
{

    use emLoggerTrait;


    /**
     * @var \Google\Cloud\Storage\StorageClient
     */
    private $client;

    /**
     * @var \Google\Cloud\Storage\Bucket[]
     */
    private $buckets;

    /**
     * @var array
     */
    private $instances;

    public function __construct()
    {
        try {
            parent::__construct();

            if (isset($_GET['pid']) && $this->getProjectSetting('google-api-token') != '' && $this->getProjectSetting('google-project-id') != '') {
                $this->setInstances();

                //configure google storage object
                $this->setClient(new StorageClient(['keyFile' => json_decode($this->getProjectSetting('google-api-token'), true), 'projectId' => $this->getProjectSetting('google-project-id')]));

                if (!empty($this->getInstances())) {
                    $buckets = array();
                    foreach ($this->getInstances() as $instance) {
                        $buckets[$instance['google-storage-bucket']] = $this->getClient()->bucket($instance['google-storage-bucket']);
                    }
                    $this->setBuckets($buckets);
                }
            }
        } catch (\Exception $e) {
            #echo $e->getMessage();
        }
    }

    /**
     * @param \Google\Cloud\Storage\Bucket $bucket
     * @param string $objectName
     * @param int $duration
     * @return string
     * @throws \Exception
     */
    public function getGoogleStorageSignedUrl($bucket, $objectName, $duration = 50)
    {
        $url = $bucket->object($objectName)->signedUrl(new \DateTime('+ ' . $duration . ' seconds'),
            [
                'version' => 'v4',
            ]);
        return $url;
    }

    /**
     * @param \Google\Cloud\Storage\Bucket $bucket
     * @param string $objectName
     * @param int $duration
     * @return string
     * @throws \Exception
     */
    public function getGoogleStorageSignedUploadUrl($bucket, $objectName, $duration = 3600)
    {
        $url = $bucket->object($objectName)->signedUrl(new \DateTime('+ ' . $duration . ' seconds'),
            [
                'method' => 'PUT',
                'contentType' => 'application/octet-stream',
                'version' => 'v4',
            ]);
        return $url;
    }

    /**
     * @return \Google\Cloud\Storage\StorageClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param \Google\Cloud\Storage\StorageClient $client
     */
    public function setClient(StorageClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $bucketName
     * @return \Google\Cloud\Storage\Bucket
     */
    public function getBucket($bucketName)
    {
        return $this->getBuckets()[$bucketName];
    }

    /**
     * @return Bucket[]
     */
    public function getBuckets()
    {
        return $this->buckets;
    }

    /**
     * @param Bucket[] $buckets
     */
    public function setBuckets(array $buckets)
    {
        $this->buckets = $buckets;
    }


    /**
     * @return array
     */
    public function getInstances()
    {
        return $this->instances;
    }

    /**
     */
    public function setInstances()
    {
        $this->instances = $this->getSubSettings('instance', $this->getProjectId());
    }


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