Client = {
    signedURL: '',
    contentType: '',
    getSignedURLAjax: '',
    saveRecordURLAjax: '',
    recordId: '',
    eventId: '',
    instanceId: '',
    downloadLinks: [],
    form: [],
    fields: [],
    filesPath: {},
    init: function () {

        // when ajax is completed then save the record.
        // jQuery(document).on({
        //     ajaxStop: function () {
        //
        //     }
        // });

        Client.processFields();
        $(".google-storage-field").on('change', function () {
            var files = $(this).prop("files")
            var field = $(this).data('field')
            Client.form = $(this).parents('form:first');
            for (var i = 0; i < files.length; i++) {
                Client.getSignedURL(files[i].type, files[i].name, field, files[i])
            }

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
    saveRecord: function () {
        $.ajax({
            // Your server script to process the upload
            url: Client.saveRecordURLAjax,
            type: 'POST',

            // Form data
            data: {
                'record_id': Client.recordId,
                'event_id': Client.eventId,
                'instance_id': Client.instanceId,
                'files_path': JSON.stringify(Client.filesPath)
            },
            success: function (data) {
                var response = JSON.parse(data)
                if (response.status === 'success') {
                    Client.uploadFile(response.url, type, file, response.path)
                }
            }
        });
    },
    processFields: function () {
        for (var prop in Client.fields) {
            $elem = jQuery("input[name=" + prop + "]").attr('type', 'hidden').addClass('google-storage')
            if ($elem.val() !== '') {
                var files = Client.downloadLinks[prop];
                for (var file in files) {
                    $('<a target="_blank" href="' + files[file] + '">' + file + '</a><br>').insertAfter($elem);
                }
            }
            $('<form class="google-storage-form" enctype="multipart/form-data"><input multiple class="google-storage-field" name="file" data-field="' + prop + '" type="file"/></form>').insertAfter($elem)
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
                    Client.uploadFile(response.url, type, file, response.path, field)
                }
            }
        });
    },
    uploadFile: function (url, type, file, path, field) {
        var data = new FormData();
        data.append('file', file)
        $.ajax({
            // Your server script to process the upload
            url: url,
            type: 'PUT',

            // Form data
            data: file,

            // Tell jQuery not to process data or worry about content-type
            // You *must* include these options!
            processData: false,
            contentType: false,
            cache: false,
            "headers": {
                "Access-Control-Allow-Origin": "*",
                "Content-Type": type,
            },
            beforeSend: function () {
                $('<progress data-name="' + file.name + '"></progress>' + file.name + '<br>').insertAfter(Client.form);
            },
            complete: function () {
                if (Client.filesPath[field] === undefined) {
                    Client.filesPath[field] = path
                } else {
                    Client.filesPath[field] += ', ' + path
                }
                Client.saveRecord();
            },
            // Custom XMLHttpRequest
            xhr: function () {
                var myXhr = $.ajaxSettings.xhr();
                if (myXhr.upload) {
                    // For handling the progress of the upload
                    myXhr.upload.addEventListener('progress', function (e) {
                        if (e.lengthComputable) {
                            $('progress[data-name="' + file.name + '"]').attr({
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