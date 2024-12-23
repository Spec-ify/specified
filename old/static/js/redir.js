let viewmodebutton = document.getElementById("spec-toggle");
viewmodebutton.addEventListener("click", preserveViewmode);
function preserveViewmode() {
    console.log('click');
    window.localStorage.removeItem('viewmode');
}
