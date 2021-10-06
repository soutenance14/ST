
//const & var
const loading_gif = document.querySelector("#loading-gif");
const load_more = document.querySelector("#load_more");
const addComment = (data) =>{
  try
  {
    const componentComments = document.createElement("div");
    obj = JSON.parse(data);
    load_more.disabled = false;
    if(obj.status === "noComment")
    {
      document.querySelector("#error-comment").innerHTML = "Pas de commentaires (supplémentaires) trouvés.";
      load_more.disabled = true;
    }
    else if(obj.status === "error")
    {
      document.querySelector("#error-comment")
      .innerHTML = "Une erreur innatendue est survenue:"+ obj.code + " " + obj.message;
    }
    else if(obj.status === "success")
    {
      comments = JSON.parse(obj.data);
      comments.forEach(comment => {
        // all view component
        oneComponent = document.createElement("div");
        userAndCreatedAt = document.createElement("h6");
        contenu = document.createElement("p");
        //content part
        contenu.innerHTML = comment.contenu;
        //get formatted date in localeString for createdAt comment
        date = new Date(comment.createdAt.timestamp * 1000);//use millisecondes
        localeDate = date.toLocaleString("fr-FR", 
        {
          day: "numeric",
          month: "numeric",
          year: "numeric",
          hour: "numeric",
          minute: "numeric"
        });
        // Concat username + formatted date
        userAndCreatedAt.innerHTML = "Par " + comment.username 
        + " le " + localeDate;

        // add all caracteriistics component
        // to global oneComponent
        oneComponent.appendChild(contenu);
        oneComponent.appendChild(userAndCreatedAt);
        // give class for good style
        userAndCreatedAt.setAttribute("class", "user-comment");
        contenu.setAttribute("class", "content-comment");
        oneComponent.setAttribute("class", "component-comment");
        // add global oneComponent to
        // all componentComments
        componentComments.appendChild(oneComponent);
      });
      componentComments.setAttribute("class","comments");
      document.querySelector("#comments-part").appendChild(componentComments);
    }
  }
  catch(e)
  {
    offset -= limit;
    alert("Impossible d'accéder aux données " + e.message);
  }
}

//init check and send (for the first comments)
if(  typeof(offset) !== "undefined"
    &&  typeof(limit) !== "undefined"
    &&  typeof(urlInit) !== "undefined")
{
  checkAndSend(urlInit, offset, limit);
}

//management
  document
.querySelector("#load_more")
.addEventListener("click",(e) => {
  if(  typeof(offset) !== "undefined"
    &&  typeof(limit) !== "undefined"
    &&  typeof(urlInit) !== "undefined")
  {
    offset += limit;
    checkAndSend(urlInit, offset, limit);
  }
  e.preventDefault();
});

//function

function checkAndSend(urlInit, offset, limit)
{
  url = createUrl(urlInit, offset, limit)
  sendTo( url);
} 
function sendTo( url) 
{
  //display gif loading
  loading_gif.style.display = "block";
  load_more.disabled = true;
  var XHR = new XMLHttpRequest();

  // Définissez ce qui arrive en cas d"erreur
  XHR.addEventListener("error", function(event) {
    alert("Oups! Une erreur s\" produite,  avec l\"object XMLHttpRequest permettant l\"interraction entre serveurs.");
  });

  // Configurez la requête
  XHR.open("POST", url);
  
  // Ajoutez l"en-tête HTTP requise pour requêtes POST de données de formulaire
  XHR.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  // Finalement, envoyez les données.
  XHR.onreadystatechange = function() 
  {
    loading_gif.style.display = "none";
    if(XHR.readyState === 4 && XHR.status === 200) {
      addComment(XHR.responseText);
    }
  }
  XHR.send(url);
}

