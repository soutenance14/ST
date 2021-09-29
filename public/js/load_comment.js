const addComment = (data)=>{
  const comment = document.createElement('div');
  comment.innerHTML= data;
  comment.setAttribute("class","comment");
  document.querySelector("body").appendChild(comment);
  console.log("test");

}

  //management
  document
.querySelector('#load_more')
.addEventListener("click",(e)=> {
  e.preventDefault();
  addComment("test")
});



