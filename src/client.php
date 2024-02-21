<?php
namespace Stanford\GoogleStorage;
/** @var \Stanford\GoogleStorage\GoogleStorage $this */

$this->emLog('inside src/client.php');
?>
<!-- Warning dialog -->
<div id="upload-dialog" style="top: 10% !important; display: none"
     title="Upload Complete">File <strong id="uploaded-file-name"></strong> uploaded successfully
</div>
<!-- END Warning dialog -->

<script src="<?php echo $this->getUrl('assets/js/client.js') ?>"></script>
<script>
    GoogleStorageModule.Client.fields = <?php echo json_encode($this->getFields()); ?>;
    GoogleStorageModule.Client.downloadLinks = <?php echo !is_null($this->getDownloadLinks()) ? json_encode($this->getDownloadLinks()) : []; ?>;
    GoogleStorageModule.Client.filesPath = <?php echo !is_null($this->getFilesPath()) ? json_encode($this->getFilesPath()) : []; ?>;
    GoogleStorageModule.Client.getSignedURLAjax = "<?php echo $this->getUrl('ajax/get_signed_url.php', false, true) . '&pid=' . $this->getProjectId() ?>"
    GoogleStorageModule.Client.saveRecordURLAjax = "<?php echo $this->getUrl('ajax/save_record.php', false, true) . '&pid=' . $this->getProjectId() ?>"
    GoogleStorageModule.Client.recordId = "<?php echo $this->getRecordId() ?: '' ?>"
    GoogleStorageModule.Client.eventId = "<?php echo $this->getEventId() ?>"
    GoogleStorageModule.Client.instanceId = "<?php echo $this->getInstanceId() ?>"
    GoogleStorageModule.Client.isSurvey = "<?php echo $this->isSurvey() ? true : false ?>"
    GoogleStorageModule.Client.isAutoSaveDisabled = "<?php echo $this->isAutoSaveDisabled() ? true : false ?>"
    GoogleStorageModule.Client.isLinkDisabled = "<?php echo $this->isLinksDisabled() ?>"
    window.addEventListener("load",
        function () {
            setTimeout(function () {
                GoogleStorageModule.Client.init();
            }, 100)
        }
        , true);
</script>