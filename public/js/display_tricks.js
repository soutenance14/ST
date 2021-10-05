var list = document.querySelector("#list-tricks");
var footer = document.querySelector("footer");
var button_hide = document.querySelector("#button_hide");
var button_display = document.querySelector("#button_display");

list.style.display = "none";
footer.style.display = "none";
button_hide.style.display = "none";

button_display
.addEventListener("click",(e) => {
  e.preventDefault();
  
  button_hide.style.display = "block";
  list.style.display = "flex";
  footer.style.display = "block";
  button_display.style.display = "none";
  list.scrollIntoView();
});

button_hide
.addEventListener("click",(e) => {
  e.preventDefault();
  button_hide.style.display = "none";
  list.style.display = "none";
  footer.style.display = "none";
  button_display.style.display = "block";
});

console.log("test");