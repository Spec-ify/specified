$(function() {

    // preventing page from redirecting
    $("html").on("dragover", function(e) {
        e.preventDefault();
        e.stopPropagation();
        $("#dragtext").text("Drop it!");
    });

    $("html").on("drop", function(e) { e.preventDefault(); e.stopPropagation(); });

    // Drag enter
    $('#uploadbox').on('dragenter', function (e) {
        e.stopPropagation();
        e.preventDefault();
        $("#dragtext").text("Drop it!");
        $('#uploadbox').addClass("hoverBox");
    });

    // Drag over
    $('#uploadbox').on('dragover', function (e) {
        e.stopPropagation();
        e.preventDefault();

    });
    $('#uploadbox').on('dragleave', function (e) {
        $("#dragtext").text("You tease");
        $('#uploadbox').removeClass("hoverBox");
    });

    // Drop
    $('#uploadbox').on('drop', function (e) {
        e.stopPropagation();
        e.preventDefault();

        var file = e.originalEvent.dataTransfer.files;
        var fd = new FormData();
        fd.append('specify', file[0]);
        $.ajax({
            url: 'manualupload.php',
            type: 'post',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(data, textStatus){
                const startIndex = data.indexOf('id="filename">') + 'id="filename">'.length;
                const endIndex = data.indexOf('<',startIndex);
                const tempfile = data.substring(startIndex, endIndex);
                window.location.replace('viewer.php?file=files/'+tempfile+'.json');
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    });
});


$("html").on("drop", function(e) { e.preventDefault(); e.stopPropagation(); });

$('#input-file-now').change(function(){
    document.getElementById("uploadform").submit();
})