Client = {
    signedURL: '',
    contentType: '',
    getSignedURLAjax: '',
    recordId: '',
    eventId: '',
    instanceId: '',
    fields: [],
    init: function () {
        Client.processFields();
        $(".google-storage-field").on('change', function () {
            var file = $(this).prop("files")
            var field = $(this).data('field')
            Client.getSignedURL(file[0].type, file[0].name, field)
        });

        $('.google-storage-upload').on('click', function () {
            if (Client.signedURL === '') {
                alert('Please make sure to select a file to upload');
                return;
            }
            var form = $(this).parents('form:first');
            Client.uploadFile(form)
        })
    },
    processFields: function () {
        for (var prop in Client.fields) {
            $elem = jQuery("input[name=" + prop + "]").attr('type', 'hidden').addClass('google-storage')
            $('<form class="google-storage-form" enctype="multipart/form-data"><input class="google-storage-field" name="file" data-field="' + prop + '" type="file"/><input class="google-storage-upload" type="button" value="Upload"/></form><progress></progress>').insertAfter($elem)
        }
    },
    getSignedURL: function (type, name, field) {
        $.ajax({
            // Your server script to process the upload
            url: Client.getSignedURLAjax,
            type: 'GET',

            // Form data
            data: {
                'content_type': type,
                'file_name': name,
                'field_name': field,
                'record_id': Client.recordId,
                'event_id': Client.eventId,
                'instance_id': Client.instanceId
            },
            success: function (data) {
                var response = JSON.parse(data)
                if (response.status === 'success') {
                    Client.signedURL = response.url
                    Client.contentType = type
                    console.log(Client)
                }
            }
        });
    },
    uploadFile: function (form) {
        var data = new FormData(form[0]);
        $.ajax({
            // Your server script to process the upload
            url: Client.signedURL,
            type: 'PUT',

            // Form data
            data: data,

            // Tell jQuery not to process data or worry about content-type
            // You *must* include these options!
            processData: false,
            contentType: false,
            cache: false,
            "headers": {
                "Access-Control-Allow-Origin": "*",
                "Content-Type": Client.contentType,
            },
            complete: function () {
                Client.signedURL = ''
                Client.contentType = ''
            },
            // Custom XMLHttpRequest
            xhr: function () {
                var myXhr = $.ajaxSettings.xhr();
                if (myXhr.upload) {
                    // For handling the progress of the upload
                    myXhr.upload.addEventListener('progress', function (e) {
                        if (e.lengthComputable) {
                            $('progress').attr({
                                value: e.loaded,
                                max: e.total,
                            });
                        }
                    }, false);
                }
                return myXhr;
            }
        });
    }
}