//This is the drag and drop upload handler, I wish it was easier, but it's pretty self-explanatory.
let form = document.querySelector('form');
//Using the jQ .on methods, we assign a set of instructions to exec at each stage of the drag event.
$(function() {

    // Prevent Redirect
    $("html").on("dragover", function(e) {
        e.preventDefault();
        e.stopPropagation();
        $("#dragtext").text("Drop it!");
    });

    $("html").on("drop", function(e) { 
        e.preventDefault(); 
        e.stopPropagation(); 
    });

    form.addEventListener('dragenter', (e) => {
        e.stopPropagation();
        e.preventDefault();
        $("#dragtext").text("Drop it!");
        $('#uploadbox').addClass("hoverBox");
    });

    // Drag over
    form.addEventListener('dragover', (e) => {
            e.stopPropagation();
            e.preventDefault();
            $("#dragtext").text("Drop it!");
            $('#uploadbox').addClass("hoverBox");
        });

    form.addEventListener('dragleave', (e) => {
            $("#dragtext").text("You tease");
            $('#uploadbox').removeClass("hoverBox");
        });

    // Drop
    //This used to originally be an ajax call post but then SealsRock figured out a plain old submit will do the trick too, :wicked:
    form.addEventListener('drop', (e) => {
            document.querySelector('input').files = e.dataTransfer.files;
            $("#uploadform").submit();
        });
});


$("html").on("drop", function(e) { e.preventDefault(); e.stopPropagation(); });

$('#input-file-now').change(function(){
    document.getElementById("uploadform").submit();
})