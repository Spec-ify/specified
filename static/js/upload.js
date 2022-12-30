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
        $("#uploadform").submit();
    });
});


$("html").on("drop", function(e) { e.preventDefault(); e.stopPropagation(); });

$('#input-file-now').change(function(){
    document.getElementById("uploadform").submit();
})