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


        Client.removeAutoParam()
        // with ajax disable all submit buttons till ajax is completed
        jQuery(document).on({
            ajaxStop: function () {
                $(".btn-primaryrc").removeAttr('disabled')
            },
            ajaxStart: function () {
                $(".btn-primaryrc").attr('disabled', 'disabled')
            }
        });

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
    // we want to remove this param so after on save record in saves to correct record id.
    removeAutoParam: function () {
        let url = new URL(window.location.href);
        var temp = url
        temp = temp.toString().replace('&auto=1', '');
        window.history.pushState({path: temp}, '', temp);

    },
    saveRecord: function (path) {
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
                    Client.downloadLinks = response.links
                    Client.processFields(path)
                }
            },
            error: function (request, error) {
                alert("Request: " + JSON.stringify(request));
            }
        });
    },
    processFields: function (path) {
        for (var prop in Client.fields) {
            $elem = jQuery("input[name=" + prop + "]").attr('type', 'hidden');
            var files = Client.downloadLinks[prop];

            if ($elem.val() !== '' || (files != undefined)) {

                if (path === undefined) {
                    var $links = $('<div id="' + prop + '-links"></div>')
                    for (var file in files) {
                        // if download links are disable
                        if (files[file] != '') {
                            $links.append('<div id="' + Client.convertPathToASCII(file) + '"><a class="google-storage-link" target="_blank" href="' + files[file] + '">' + file + '</a><br></div>')
                        } else {
                            $links.append('<div id="' + Client.convertPathToASCII(file) + '">' + file + '<br></div>')
                        }
                    }
                    $links.insertAfter($elem);
                    // if path is defined this mean the function was called after upload is complete. then we need to replace only the progress bar that completed.
                } else {
                    // if download links are disable
                    if (files[path] != '') {
                        $("#" + Client.convertPathToASCII(path)).html('<a class="google-storage-link" target="_blank" href="' + files[path] + '">' + path + '</a><br>')
                    } else {
                        $("#" + Client.convertPathToASCII(path)).html('' + path + '<br>')
                    }
                }
            }
            // only add form in the first time.
            if (path === undefined) {
                $('<form class="google-storage-form" enctype="multipart/form-data"><input multiple class="google-storage-field" name="file" data-field="' + prop + '" type="file"/></form>').insertAfter($elem)
            }

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
            },
            error: function (request, error) {
                alert("Request: " + JSON.stringify(request));
            }
        });
    },
    convertPathToASCII: function (path) {
        return path
            .split('')
            .map(x => x.charCodeAt(0))
            .reduce((a, b) => a + b);
    },
    uploadFile: function (url, type, file, path, field) {

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
                if ($('#' + Client.convertPathToASCII(path)).length) {
                    $('#' + Client.convertPathToASCII(path)).html('<progress data-name="' + file.name + '"></progress>' + file.name + '<span data-name="' + file.name + '"> <i class="fas fa-window-close"></i></span><br></div>')
                } else {
                    $('<div id="' + Client.convertPathToASCII(path) + '"><progress data-name="' + file.name + '"></progress>' + file.name + '<span data-name="' + file.name + '"> <i class="fas fa-window-close"></i></span><br></div>').insertAfter(Client.form);
                }
            },
            complete: function (xhr, status) {
                if (status == 'success') {
                    if (Client.filesPath[field] === undefined || Client.filesPath[field] === '') {
                        Client.filesPath = {
                            [field]: path
                        }
                    } else {
                        // only attach if file is new file
                        if (Client.filesPath[field].indexOf(path) !== false) {
                            Client.filesPath[field] += ',' + path
                        }
                    }
                    // make sure to set the value in case user clicked default save button .
                    jQuery("input[name=" + field + "]").val(Client.filesPath[field]);
                    Client.saveRecord(path);
                }

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

                    myXhr.upload.addEventListener('abort', function (e) {
                        console.log(e)
                    }, false);
                }

                var _cancel = $('span[data-name="' + file.name + '"]');

                _cancel.on('click', function () {
                    if (confirm('Are you sure you want to cancel upload for ' + file.name + '?')) {
                        myXhr.abort();
                        $('#' + Client.convertPathToASCII(path)).html('')
                    }
                })

                return myXhr;
            }
        });
    }
}