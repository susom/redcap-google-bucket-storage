<?php
namespace Stanford\GoogleStorage;
/** @var \Stanford\GoogleStorage\GoogleStorage $this */

$this->emLog('inside src/client.php');
?>
<script src="<?php echo $this->getUrl('assets/js/client.js') ?>"></script>
<script>
    Client.fields = <?php echo json_encode($this->getFields()); ?>;
    Client.downloadLinks = <?php echo !is_null($this->getDownloadLinks()) ? json_encode($this->getDownloadLinks()) : []; ?>;
    Client.filesPath = <?php echo !is_null($this->getFilesPath()) ? json_encode($this->getFilesPath()) : []; ?>;
    Client.getSignedURLAjax = "<?php echo $this->getUrl('ajax/get_signed_url.php', false, true) . '&pid=' . $this->getProjectId() ?>"
    Client.saveRecordURLAjax = "<?php echo $this->getUrl('ajax/save_record.php', false, true) . '&pid=' . $this->getProjectId() ?>"
    Client.recordId = "<?php echo $this->getRecordId() ?: '' ?>"
    Client.eventId = "<?php echo $this->getEventId() ?>"
    Client.instanceId = "<?php echo $this->getInstanceId() ?>"
    Client.isSurvey = "<?php echo $this->isSurvey() ? true : false ?>"
    Client.isAutoSaveDisabled = "<?php echo $this->isAutoSaveDisabled() ? true : false ?>"
    Client.isLinkDisabled = "<?php echo $this->isLinksDisabled() ?>"
    window.onload = setTimeout(function () {
        Client.init();
    }, 500)
</script>