Client = {
    signedURL: '',
    contentType: '',
    getSignedURLAjax: '',
    init: function () {
        $(".google-storage").on('change', function () {
            var file = $(this)
            Client.getSignedURL(file.type, file.name)
        });

        $(':button').on('click', function () {

            if (Client.signedURL === '') {
                alert('Please make sure to select a file to upload');
                return;
            }
        })
    },
    getSignedURL: function (type, name, bucket) {
        $.ajax({
            // Your server script to process the upload
            url: Client.getSignedURLAjax,
            type: 'GET',

            // Form data
            data: {'content_type': type, 'file_name': name, 'bucket_name': bucket},
            success: function (data) {
                var response = JSON.parse(data)
                if (response.status === 'success') {
                    signedURL = response.url
                    contentType = type
                    console.log(signedURL)
                }
            }
        });
    },
    uploadFile: function () {
        var data = new FormData($('#test-form')[0]);
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