var list = document.querySelector("#list-tricks");
var header = document.querySelector("header");
var button_hide = document.querySelector("#button_hide");
var button_display = document.querySelector("#button_display");

button_display
.addEventListener("click",(e) => {
  e.preventDefault();
  list.scrollIntoView();
});

button_hide
.addEventListener("click",(e) => {
  e.preventDefault();
  header.scrollIntoView();
});
