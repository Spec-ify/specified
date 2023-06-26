let viewmodebutton = document.getElementById("spectoggle");
viewmodebutton.addEventListener("click", preserveViewmode);
function preserveViewmode() {
    console.log('click');
    window.localStorage.removeItem('viewmode');
}
