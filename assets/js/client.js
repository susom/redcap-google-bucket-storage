var GoogleStorageModule = {};
GoogleStorageModule.Client = {
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
    isSurvey: false,
    isLinkDisabled: false,
    isAutoSaveDisabled: false,
    decode_object: function (obj) {
        try {
            // parse text to json object
            var parsedObj = obj;
            if (typeof obj === 'string') {
                var temp = obj.replace(/&quot;/g, '"').replace(/[\n\r\t\s]+/g, ' ')
                parsedObj = JSON.parse(temp);
            }

            for (key in parsedObj) {
                if (typeof parsedObj[key] === 'object') {
                    parsedObj[key] = GoogleStorageModule.Client.decode_object(parsedObj[key])
                } else {
                    // ignore boolean because changing them to string will cause error.
                    if (typeof parsedObj[key] != 'boolean') {
                        parsedObj[key] = GoogleStorageModule.Client.decode_string(parsedObj[key])
                    }
                }
            }
            return parsedObj
        } catch (error) {
            console.error(error);
            alert(error)
            // expected output: ReferenceError: nonExistentFunction is not defined
            // Note - error messages will vary depending on browser
        }
    },
    decode_string: function (input) {
        var txt = document.createElement("textarea");
        txt.innerHTML = input;
        return txt.value;
    },
    init: function () {

        // test builder
        GoogleStorageModule.Client.removeAutoParam()
        // with ajax disable all submit buttons till ajax is completed
        jQuery(document).on({
            ajaxStop: function () {
                $(".btn-primaryrc").removeAttr('disabled')
            },
            ajaxStart: function () {
                $(".btn-primaryrc").attr('disabled', 'disabled')
            }
        });

        GoogleStorageModule.Client.processFields();
        $(".google-storage-field").on('change', function () {
            var files = $(this).prop("files")
            var field = $(this).data('field')
            GoogleStorageModule.Client.form = $(this).parents('form:first');
            for (var i = 0; i < files.length; i++) {
                GoogleStorageModule.Client.getSignedURL(files[i].type, files[i].name, field, files[i])
            }

            //GoogleStorageModule.Client.getSignedURL(file[0].type, file[0].name, field)
        });


        $(document).on('click', '.get-download-link', function () {
            var fieldName = $(this).data('field-name')
            var fileName = $(this).data('file-name')
            var id = $(this).data('file-id')
            GoogleStorageModule.Client.getDownloadSignedURL(fieldName, fileName, id)
        })
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
            url: GoogleStorageModule.Client.saveRecordURLAjax,
            type: 'POST',

            // Form data
            data: {
                'record_id': GoogleStorageModule.Client.recordId,
                'event_id': GoogleStorageModule.Client.eventId,
                'instance_id': GoogleStorageModule.Client.instanceId,
                'files_path': JSON.stringify(GoogleStorageModule.Client.filesPath)
            },
            success: function (temp) {
                var response = GoogleStorageModule.Client.decode_object(temp)
                if (response.status === 'success') {
                    GoogleStorageModule.Client.downloadLinks = response.links
                    GoogleStorageModule.Client.processFields(path);

                    // change few parameter to let redcap save existing record
                    record_exists = 1;
                    $('input[name ="hidden_edit_flag"]').val(1);

                }
            },
            complete: function () {
                $(".btn-primaryrc").removeAttr('disabled')
            },
            error: function (request, error) {
                alert("Request: " + JSON.stringify(request));
            }
        });
    },
    processFields: function (path) {
        for (var prop in GoogleStorageModule.Client.fields) {
            $elem = jQuery("input[name=" + prop + "]").attr('type', 'hidden');
            var files = GoogleStorageModule.Client.downloadLinks[prop];

            if ($elem.val() !== '' || (files != undefined)) {

                if (path === undefined) {
                    var $links = $('<div id="' + prop + '-links"></div>')
                    for (var file in files) {
                        // if download links are disable
                        // if (files[file] != '') {
                        //     $links.append('<div id="' + GoogleStorageModule.Client.convertPathToASCII(file) + '"><a class="google-storage-link" target="_blank" href="' + files[file] + '">' + file + '</a><br></div>')
                        // } else {
                        //     $links.append('<div id="' + GoogleStorageModule.Client.convertPathToASCII(file) + '"><div class="file-name" data-file-id="' + GoogleStorageModule.Client.convertPathToASCII(file) + '">' + file + '</div> <a  data-field-name="' + prop + '" data-file-id="' + GoogleStorageModule.Client.convertPathToASCII(file) + '" data-file-name="' + file + '" class="get-download-link btn btn-primary btn-sm" href="#">Get Download Link</a><br></div>')
                        // }
                        if (GoogleStorageModule.Client.isLinkDisabled) {
                            $links.append('<div id="' + GoogleStorageModule.Client.convertPathToASCII(file) + '"><div class="file-name" data-file-id="' + GoogleStorageModule.Client.convertPathToASCII(file) + '">' + file + '</div></div>')
                        } else {
                            $links.append('<div id="' + GoogleStorageModule.Client.convertPathToASCII(file) + '"><div class="file-name" data-file-id="' + GoogleStorageModule.Client.convertPathToASCII(file) + '">' + file + '</div> <a  data-field-name="' + prop + '" data-file-id="' + GoogleStorageModule.Client.convertPathToASCII(file) + '" data-file-name="' + file + '" class="get-download-link btn btn-primary btn-sm" href="#">Get Download Link</a><br></div>')
                        }

                    }
                    $links.insertAfter($elem);
                    // if path is defined this mean the function was called after upload is complete. then we need to replace only the progress bar that completed.
                } else {
                    // if download links are disable
                    if ((files !== undefined && files[path] != '')) {
                        $("#" + GoogleStorageModule.Client.convertPathToASCII(path)).html('<a class="google-storage-link" target="_blank" href="' + files[path] + '">' + path + '</a><br>')
                    } else {
                        if (GoogleStorageModule.Client.isLinkDisabled) {
                            $("#" + GoogleStorageModule.Client.convertPathToASCII(path)).html('<div class="file-name" data-file-id="' + GoogleStorageModule.Client.convertPathToASCII(path) + '">' + path + '</div><br>')
                        } else {
                            $("#" + GoogleStorageModule.Client.convertPathToASCII(path)).html('<div class="file-name" data-file-id="' + GoogleStorageModule.Client.convertPathToASCII(path) + '">' + path + '</div><a  data-field-name="' + prop + '" data-file-id="' + GoogleStorageModule.Client.convertPathToASCII(path) + '" data-file-name="' + path + '" class="get-download-link btn btn-primary btn-sm" href="#">Get Download Link</a><br>')
                        }
                    }
                }
            }
            // only add form in the first time.
            if (path === undefined) {
                $('<form class="google-storage-form" enctype="multipart/form-data"><input multiple class="google-storage-field" data-field="' + prop + '" type="file"/></form>').insertAfter($elem)
            }

            if (path !== undefined) {
                GoogleStorageModule.Client.uploadDialog(path)
            }
        }
    },
    uploadDialog: function (path) {
        $("#uploaded-file-name").text(path);
        $('#upload-dialog').dialog({
            bgiframe: true, modal: true, width: 400, position: ['center', 20],
            open: function () {
                fitDialog(this);
            },
            buttons: {
                Close: function () {
                    $(this).dialog('close');
                }
            }
        });
    },
    getDownloadSignedURL: function (field_name, file_name, id) {
        $.ajax({
            // Your server script to process the upload
            url: GoogleStorageModule.Client.getSignedURLAjax,
            type: 'GET',

            // Form data
            data: {
                'file_name': file_name,
                'field_name': field_name,
                'action': 'download'
            },
            success: function (temp) {
                var response = GoogleStorageModule.Client.decode_object(temp)
                if (response.status === 'success') {
                    $("#" + id).html('<a target="_blank" href="' + response.link + '">' + file_name + '</a>')
                }
            },
            error: function (request, error) {
                alert("Request: " + JSON.stringify(request));
            }
        });
    },
    getSignedURL: function (type, name, field, file) {
        $.ajax({
            // Your server script to process the upload
            url: GoogleStorageModule.Client.getSignedURLAjax,
            type: 'GET',

            // Form data
            data: {
                'content_type': type,
                'file_name': name,
                'field_name': field,
                'record_id': GoogleStorageModule.Client.recordId,
                'event_id': GoogleStorageModule.Client.eventId,
                'instance_id': GoogleStorageModule.Client.instanceId,
                'action': 'upload'
            },
            success: function (temp) {
                var response = GoogleStorageModule.Client.decode_object(temp)
                if (response.status === 'success') {
                    GoogleStorageModule.Client.uploadFile(response.url, type, file, response.path, field)
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
                if ($('#' + GoogleStorageModule.Client.convertPathToASCII(path)).length) {
                    $('#' + GoogleStorageModule.Client.convertPathToASCII(path)).html('<progress data-name="' + file.name + '"></progress>' + file.name + '<span data-name="' + file.name + '"> <i class="fas fa-window-close"></i></span><br></div>')
                } else {
                    $('<div id="' + GoogleStorageModule.Client.convertPathToASCII(path) + '"><progress data-name="' + file.name + '"></progress>' + file.name + '<span data-name="' + file.name + '"> <i class="fas fa-window-close"></i></span><br></div>').insertAfter(GoogleStorageModule.Client.form);
                }
            },
            complete: function (xhr, status) {
                if (status == 'success') {
                    if (GoogleStorageModule.Client.filesPath[field] === undefined || GoogleStorageModule.Client.filesPath[field] === '') {
                        GoogleStorageModule.Client.filesPath = {
                            [field]: path
                        }
                    } else {
                        // only attach if file is new file
                        if (GoogleStorageModule.Client.filesPath[field].indexOf(path) !== false) {
                            GoogleStorageModule.Client.filesPath[field] += ',' + path
                        }
                    }
                    // make sure to set the value in case user clicked default save button .
                    jQuery("input[name=" + field + "]").val(GoogleStorageModule.Client.filesPath[field]);

                    // do not save for surveys
                    if (GoogleStorageModule.Client.isAutoSaveDisabled == false) {
                        GoogleStorageModule.Client.saveRecord(path);
                    } else {
                        GoogleStorageModule.Client.processFields(path);
                    }

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
                        $('#' + GoogleStorageModule.Client.convertPathToASCII(path)).html('')
                    }
                })

                return myXhr;
            }
        });
    }
}