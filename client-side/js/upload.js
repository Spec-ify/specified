//This is the drag and drop upload handler, I wish it was easier, but it's pretty self-explanatory.
let form = document.querySelector("#upload-form");
let html = document.querySelector("html");
let text = document.querySelector("#drag-text");
let box = document.querySelector("#upload-box");
let input = document.querySelector("#input-file-now");

// Prevent Redirect
html.addEventListener("dragover", (e) => {
	e.preventDefault();
	e.stopPropagation();
	text.textContent = "Drop it!";
});

html.addEventListener("dragleave", (e) => {
	e.preventDefault();
	e.stopPropagation();
});

html.addEventListener("drop", (e) => {
	e.preventDefault();
	e.stopPropagation();
});

form.addEventListener("dragenter", (e) => {
	e.stopPropagation();
	e.preventDefault();
	box.classList.add("hoverBox");
	text.textContent = "Drop it!";
});

form.addEventListener("dragover", (e) => {
	e.stopPropagation();
	e.preventDefault();
	box.classList.add("hoverBox");
	text.textContent = "Drop it!";
});

form.addEventListener("dragleave", (e) => {
	box.classList.remove("hoverBox");
	text.textContent = "You tease";
	e.stopPropagation();
	e.preventDefault();
});

// Drop
// Selects the input box, sets the files, and submits it
form.addEventListener("drop", (e) => {
	text.textContent = "Thanks! :D";
	input.files = e.dataTransfer.files;
	form.submit();
});

input.addEventListener("change", () => {
   form.submit();
});
