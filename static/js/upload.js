//This is the drag and drop upload handler, I wish it was easier, but it's pretty self-explanatory.

//Using the jQ .on methods, we assign a set of instructions to exec at each stage of the drag event.
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
    //This used to originally be an ajax call post but then SealsRock figured out a plain old submit will do the trick too, :wicked:
    $('#uploadbox').on('drop', function (e) {
        $("#uploadform").submit();
    });
});


$("html").on("drop", function(e) { e.preventDefault(); e.stopPropagation(); });

$('#input-file-now').change(function(){
    document.getElementById("uploadform").submit();
})