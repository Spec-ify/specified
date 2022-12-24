
    function searchFunction() {

    var input, filter, ul, li, a, i, txtValue;
    input = document.getElementById('searchBarDiv');
    filter = input.value.toUpperCase();
    ul = document.getElementById("myUL");
    li = ul.getElementsByTagName('div');


    for (i = 0; i < h1.length; i++) {
    h1 = li[i].getElementsByTagName("h1")[0];
    txtValue = h1.textContent || h1.innerText;
    if (txtValue.toUpperCase().indexOf(filter) > -1) {
    div[i].style.display = "";
} else {
    div[i].style.display = "none";
}
}
}