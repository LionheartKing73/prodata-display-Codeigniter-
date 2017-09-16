$(function() {
    var f= $("#uploader").plupload({
        // General settings
        runtimes : 'html5,html4',
        url : 'http://www.prodataretargeting.com/adword/uploadFile',

        // User can upload no more then 20 files in one go (sets multiple_queues to false)
        max_file_count: 20,

        chunk_size: '1mb',

        // Resize images on clientside if we can
        resize : {
            //width : 200,
            // height : 200,
            quality : 90,
            crop: true // crop to exact dimensions
        },

        filters : {
            // Maximum file size
            max_file_size : '1000mb',
            // Specify what files to browse for
            mime_types: [
                {title : "Image files", extensions : "jpeg,jpg,png"}
            ]
        },

        // Rename files by clicking on their titles
        rename: true,

        // Sort files
        sortable: true,

        // Enable ability to drag'n'drop files onto the widget (currently only HTML5 supports that)
        dragdrop: true,

        // Views to activate
        views: {
            list: true,
            thumbs: true, // Show thumbs
            active: 'thumbs'
        }      
    });

    count=0;
    var uploader= $("#uploader").plupload('getUploader');

    uploader.bind('FileUploaded', function (upldr, file, object) { 
        
        $("#upload-result").text("");
        var myData;
        try {
            myData = eval(object.response);
        } catch (err) {
            myData = eval('(' + object.response + ')');
        }
        if(myData.status=="false"){
             $("div[title='" + myData.title + "']").parent().remove();

            $("#upload-result").text("Your "+ myData.title+ " image's size doesn\t correspond to any of the Google Adwords's required sizes. Try again!");
        }else{
            var count=parseInt($("#img_count").val());
            count+=1;
            $("#img_count").val(count);
        }
    });


    uploader.bind('FilesRemoved', function(up, files) {
        // Called when files are removed from queue
        $("#upload-result").text("");
        var fileName=files[0]["name"];

        $.ajax({
            url: '/adword/deleteUploadedFiles',
            type: 'POST',
            data: {fname: fileName}
        });


        var count=parseInt($("#img_count").val());
        count-=1;
        $("#img_count").val(count);

    });


    uploader.bind('FilesAdded', function(up, files){
        $("#upload-result").text("");
    });
    // Handle the case when form was submitted before uploading has finished
    $('#form').submit(function(e) {

        // When all files are uploaded submit form
        $('#uploader').on('complete', function() {
            $('#form')[0].submit();
        });

        $('#uploader').plupload('start');

        return false; // Keep the form from submitting
    });
});
