Client = {
    signedURL: '',
    contentType: '',
    getSignedURLAjax: '',
    recordId: '',
    eventId: '',
    instanceId: '',
    form: [],
    fields: [],
    filesPath: '',
    init: function () {
        Client.processFields();
        $(".google-storage-field").on('change', function () {
            var files = $(this).prop("files")
            var field = $(this).data('field')

            for (var i = 0; i < files.length; i++) {
                Client.getSignedURL(files[i].type, files[i].name, field, files[i])
            }
            console.log(files)
            //Client.getSignedURL(file[0].type, file[0].name, field)
        });

        // $('.google-storage-upload').on('click', function () {
        //     if (Client.signedURL === '') {
        //         alert('Please make sure to select a file to upload');
        //         return;
        //     }
        //
        //     Client.uploadFile(form)
        // })
    },
    processFields: function () {
        for (var prop in Client.fields) {
            $elem = jQuery("input[name=" + prop + "]").attr('type', 'hidden').addClass('google-storage')
            $('<form class="google-storage-form" enctype="multipart/form-data"><input multiple class="google-storage-field" name="file" data-field="' + prop + '" type="file"/></form><progress></progress>').insertAfter($elem)
        }
    },
    getSignedURL: function (type, name, field, file) {
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
                    Client.uploadFile(response.url, type, file, response.path)
                }
            }
        });
    },
    uploadFile: function (url, type, file, path) {
        var data = new FormData();
        data.append('file', file)
        $.ajax({
            // Your server script to process the upload
            url: url,
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
                "Content-Type": type,
            },
            complete: function () {
                Client.filesPath += filesPath
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