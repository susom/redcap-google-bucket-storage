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
 * @property array $fields
 * @property array $record
 * @property array $downloadLinks
 * @property array $bucketPrefix
 * @property array $filesPath
 * @property \Project $project
 * @property string $recordId
 * @property int $eventId
 * @property int $instanceId
 * @property bool $linksDisabled
 * @property bool $isSurvey
 * @property bool $autoSaveDisabled
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

    private $project;

    private $fields;

    private $recordId;

    private $eventId;

    private $instanceId;

    private $record;

    private $downloadLinks;

    private $bucketPrefix;

    private $filesPath;

    private $linksDisabled;

    private $isSurvey;

    private $autoSaveDisabled;

    public function __construct()
    {
        try {
            parent::__construct();

            if (isset($_GET['pid']) && $this->getProjectSetting('google-api-token') != '' && $this->getProjectSetting('google-project-id') != '') {
                $this->setInstances();

                global $Proj;

                $this->setProject($Proj);

                $this->prepareGoogleStorageFields();
                //configure google storage object
                $this->setClient(new StorageClient(['keyFile' => json_decode($this->getProjectSetting('google-api-token'), true), 'projectId' => $this->getProjectSetting('google-project-id')]));

                if (!empty($this->getInstances())) {
                    $buckets = array();
                    $prefix = array();
                    foreach ($this->getInstances() as $instance) {
                        $buckets[$instance['google-storage-bucket']] = $this->getClient()->bucket($instance['google-storage-bucket']);
                        $prefix[$instance['google-storage-bucket']] = $instance['google-storage-bucket-prefix'];
                    }
                    $this->setBuckets($buckets);
                    $this->setBucketPrefix($prefix);
                }

                // set flag to display uploaded file download links
                if (!is_null($this->getProjectSetting('disable-file-link'))) {
                    $this->setLinksDisabled($this->getProjectSetting('disable-file-link'));

                } else {
                    $this->setLinksDisabled(false);
                }

                // set if we want auto save when file is uploaded.
                if (!is_null($this->getProjectSetting('disable-auto-save'))) {
                    $this->setAutoSaveDisabled($this->getProjectSetting('disable-auto-save'));
                } else {
                    $this->setAutoSaveDisabled(false);
                }
            }
        } catch (\Exception $e) {
            #echo $e->getMessage();
        }
    }

    /**
     * @param string $path
     */
    public function includeFile($path)
    {
        include_once $path;
    }

    private function prepareGoogleStorageFields()
    {
        $fields = array();
        $re = '/^@GOOGLE-STORAGE=/m';
        foreach ($this->getProject()->metadata as $name => $field) {
            preg_match_all($re, $field['misc'], $matches, PREG_SET_ORDER, 0);
            if (!empty($matches)) {
                $fields[$name] = str_replace('@GOOGLE-STORAGE=', '', $field['misc']);
            }
            unset($matches);
        }
        $this->setFields($fields);
    }

    public function getFieldInstrument($field)
    {
        foreach ($this->getProject()->forms as $name => $form) {
            if (array_key_exists($field, $form['fields'])) {
                return $name;
            }
        }
    }


    public function saveRecord()
    {
        if (isset($_POST['record_id']) && $_POST['record_id'] != '') {
            $recordId = (filter_var($_POST['record_id'], FILTER_SANITIZE_STRING));
        } else {
            $recordId = (\REDCap::reserveNewRecordId($this->getProjectId()));
        }
        $data[\REDCap::getRecordIdField()] = $recordId;
        $filesPath = json_decode($_POST['files_path'], true);
        foreach ($filesPath as $field => $item) {
            $data[$field] = $item;
            $form = $this->getFieldInstrument($field);
        }
        $this->setEventId(filter_var($_POST['event_id'], FILTER_SANITIZE_NUMBER_INT));
        $data['redcap_event_name'] = $this->getProject()->getUniqueEventNames($this->getEventId());
        if ($this->getProject()->isRepeatingForm($this->getEventId(), $form)) {
            $data['redcap_repeat_instance'] = filter_var($_POST['instance_id'], FILTER_SANITIZE_NUMBER_INT);
            $data['redcap_repeat_instrument'] = $form;
        }

        $response = \REDCap::saveData($this->getProjectId(), 'json', json_encode(array($data)));
        if (empty($response['errors'])) {
            $this->setRecord($recordId);
            $this->prepareDownloadLinks();
            $this->uploadLogFile(USERID, $this->getRecordId(), $data['redcap_event_name'], $field, $filesPath);
            return array('status' => 'success', 'links' => $this->getDownloadLinks());
        } else {
            if (is_array($response['errors'])) {
                throw new \Exception(implode(",", $response['errors']));
            } else {
                throw new \Exception($response['errors']);
            }
        }
    }

    private function prepareLogPath($path)
    {
        $lofFile = date('Y-m-d') . '.log';
        $path = $this->getFullPrefix($path) . $lofFile;
        return $path;
    }

    private function getFullPrefix($path)
    {
        $filePath = explode(',', $path);
        $match = explode('/', $filePath[0]);
        $filename = end($match);
        $path = str_replace($filename, '', $filePath[0]);
        return $path;
    }

    private function uploadLogFile($userId, $recordId, $eventName, $field, $path)
    {
        $logPath = $this->prepareLogPath($path[$field]);
        $signedURL = $this->getGoogleStorageSignedUrl($this->getBucket($field), $logPath);
        $uploadURL = $this->getGoogleStorageSignedUploadUrl($this->getBucket($field), $logPath, 'text/plain');
        $content = file_get_contents($signedURL);
        if ($content == false) {
            $content = "user_id,record_id,event_name,field,path,created_at\n";
        }
        $links = explode(',', $path[$field]);
        $time = time();
        foreach ($links as $link) {
            $content .= "$userId,$recordId,$eventName,$field,$link,$time\n";
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $uploadURL);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: text/plain'));
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
        if ($response = curl_exec($curl) === false) {
            throw new \LogicException(curl_error($curl));
        }
        curl_close($curl);
    }

    public function redcap_every_page_top()
    {
        try {
            $this->setIsSurvey(preg_match("/surveys\/\?s=[a-zA-Z0-9]{10}/m", $_SERVER['REQUEST_URI']));
            // in case we are loading record homepage load its the record children if existed
            if ((strpos($_SERVER['SCRIPT_NAME'], 'DataEntry/index.php') !== false || $this->isSurvey()) && $this->getFields()) {

                if (isset($_GET['event_id'])) {
                    $this->setEventId(filter_var($_GET['event_id'], FILTER_SANITIZE_NUMBER_INT));
                } else {
                    $this->setEventId($this->getFirstEventId());
                }

                if (isset($_GET['instance'])) {
                    $this->setInstanceId(filter_var($_GET['instance'], FILTER_SANITIZE_NUMBER_INT));
                }

                // do not set the record for surveys
                if (isset($_GET['id'])) {
                    $this->setRecordId(filter_var($_GET['id'], FILTER_SANITIZE_STRING));
                    $this->setRecord(filter_var($_GET['id'], FILTER_SANITIZE_STRING));
                    $this->prepareDownloadLinks();
                }


                $this->includeFile("src/client.php");
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

    }

    /**
     * get list of all files under specific prefix
     * @param \Google\Cloud\Storage\Bucket $bucket
     * @param string $prefix
     * @return array
     */
    private function getPrefixObjects($bucket, $prefix)
    {
        $files = array();
        $objects = $bucket->objects(array('prefix' => $prefix));
        foreach ($objects as $object) {
            $re = '/[0-9]{4}-[0-9]{2}-[0-9]{2}.log/m';

            preg_match_all($re, $object->name(), $matches, PREG_SET_ORDER, 0);

            if (!empty($matches)) {
                continue;
            }
            $files[] = $object->name();
        }
        return $files;
    }

    public function prepareDownloadLinks()
    {
        $record = $this->getRecord();
        $links = array();
        $filesPath = array();
        foreach ($this->getFields() as $field => $bucket) {
            if ($record[$this->getRecordId()][$this->getEventId()][$field] != '') {
                $files = explode(",", $record[$this->getRecordId()][$this->getEventId()][$field]);
                $bucket = $this->getBucket($field);

                if (!empty($field)) {
                    // check if files still exist in bucket.
                    $prefix = $this->getFullPrefix($files[0]);
                    $files = $this->getPrefixObjects($bucket, $prefix);
                    foreach ($files as $file) {
                        $links[$field][$file] = '';
//                        if ($this->isLinksDisabled()) {
                        $links[$field][$file] = '';
//                        } else {
//                            $links[$field][$file] = $this->getGoogleStorageSignedUrl($bucket, trim($file));
//                        }
                        if (isset($filesPath[$field])) {
                            $filesPath[$field] .= ',' . $file;
                        } else {
                            $filesPath[$field] = $file;
                        }
                    }
                }
            }
        }
        $this->setFilesPath($filesPath);
        $this->setDownloadLinks($links);
    }

    public function buildUploadPath($prefix, $fieldName, $fileName, $recordId, $eventId, $instanceId)
    {
        $prefix = $prefix != '' ? $prefix . '/' : '';

        if ($this->getProject()->longitudinal) {
            return $prefix . $recordId . '/' . $fieldName . '/' . \REDCap::getEventNames($eventId) . '/' . $instanceId . '/' . $fileName;
        }
        if (!empty($this->getProject()->RepeatingFormsEvents)) {
            return $prefix . $recordId . '/' . $fieldName . '/' . $instanceId . '/' . $fileName;
        }

        return $prefix . $recordId . '/' . $fieldName . '/' . $fileName;
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
    public function getGoogleStorageSignedUploadUrl($bucket, $objectName, $contentType = 'text/plain', $duration = 3600)
    {
        $url = $bucket->object($objectName)->signedUrl(new \DateTime('+ ' . $duration . ' seconds'),
            [
                'method' => 'PUT',
                'contentType' => $contentType,
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
     * @param string $fieldName
     * @return \Google\Cloud\Storage\Bucket
     */
    public function getBucket($fieldName)
    {
        $bucketName = $this->getFields()[$fieldName];
        return $this->getBuckets()[$bucketName];
    }

    /**
     * @param string $fieldName
     * @return string
     */
    public function getFieldBucketPrefix($fieldName)
    {
        $bucketName = $this->getFields()[$fieldName];
        return $this->getBucketPrefix()[$bucketName];
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
    public function setBuckets($buckets)
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

    /**
     * @return \Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param \Project $project
     */
    public function setProject(\Project $project)
    {
        $this->project = $project;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    /**
     * @return string
     */
    public function getRecordId()
    {
        return $this->recordId;
    }

    /**
     * @return string
     */
    public function setRecordId()
    {
        $temp = func_get_args();
        $recordId = $temp[0];
        $this->recordId = $recordId;
    }

    /**
     * @param string $recordId
     */
    public function send($removeDisplayName = false, $recipientIsSurveyParticipant = null, $enforceProtectedEmail = false, $emailCategory = null, $lang_id = null)
    {
        $this->recordId = $recordId;
    }

    /**
     * @return int
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * @param int $eventId
     */
    public function setEventId($eventId)
    {
        $this->eventId = $eventId;
    }

    /**
     * @return int
     */
    public function getInstanceId()
    {
        return $this->instanceId;
    }

    /**
     * @param int $instanceId
     */
    public function setInstanceId($instanceId)
    {
        $this->instanceId = $instanceId;
    }

    /**
     * @return array
     */
    public function getRecord()
    {
        return $this->record;
    }

    /**
     * @param array $record
     */
    public function setRecord($recordId)
    {
        $param = array(
            'project_id' => $this->getProjectId(),
            'return_format' => 'array',
            'events' => $this->getEventId(),
            'records' => [$recordId]
        );
        $data = array();
        $record = \REDCap::getData($param);
        $this->record = $record;
    }

    /**
     * @return array
     */
    public function getDownloadLinks()
    {
        return $this->downloadLinks;
    }

    /**
     * @param array $downloadLinks
     */
    public function setDownloadLinks($downloadLinks)
    {
        $this->downloadLinks = $downloadLinks;
    }

    /**
     * @return array
     */
    public function getBucketPrefix(): array
    {
        return $this->bucketPrefix;
    }

    /**
     * @param array $bucketPrefix
     */
    public function setBucketPrefix(array $bucketPrefix): void
    {
        $this->bucketPrefix = $bucketPrefix;
    }

    /**
     * @return array
     */
    public function getFilesPath()
    {
        return $this->filesPath;
    }

    /**
     * @param array $filesPath
     */
    public function setFilesPath(array $filesPath): void
    {
        $this->filesPath = $filesPath;
    }

    /**
     * @return bool
     */
    public function isLinksDisabled(): bool
    {
        return $this->linksDisabled;
    }

    /**
     * @param bool $linksDisabled
     */
    public function setLinksDisabled($linksDisabled): void
    {
        $this->linksDisabled = $linksDisabled;
    }

    /**
     * @return bool
     */
    public function isSurvey(): bool
    {
        return $this->isSurvey;
    }

    /**
     * @param bool $isSurvey
     */
    public function setIsSurvey($isSurvey): void
    {
        $this->isSurvey = $isSurvey;
    }

    /**
     * @return bool
     */
    public function isAutoSaveDisabled(): bool
    {
        return $this->autoSaveDisabled;
    }

    /**
     * @param bool $autoSaveDisabled
     */
    public function setAutoSaveDisabled($autoSaveDisabled): void
    {
        $this->autoSaveDisabled = $autoSaveDisabled;
    }


}