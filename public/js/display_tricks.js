var list = document.querySelector("#list");
var footer = document.querySelector("footer");
var button_hide = document.querySelector("#button_hide");

list.style.display = "none";
footer.style.display = "none";
button_hide.style.display = "none";

document
.querySelector("#button_display")
.addEventListener("click",(e) => {
  e.preventDefault();
  
  button_hide.style.display = "block";
  list.style.display = "flex";
  footer.style.display = "block";

  list.scrollIntoView();
});

button_hide
.addEventListener("click",(e) => {
  e.preventDefault();
  button_hide.style.display = "none";
  list.style.display = "none";
  footer.style.display = "none";
});


console.log("test");