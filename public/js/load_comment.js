
//const & var
const addComment = (data)=>{
  try
  {
    const componentComments = document.createElement("div");
    obj = JSON.parse(data);
    console.log(obj);
    if(obj.status === "noComment")
    {
      // TODO replace by innerHTML in message noComment (create the div in twig)
      alert("Pas de commentaires supplémentaires trouvés."); 
    }
    else if(obj.status === "error")
    {
      // TODO replace by innerHTML in message error (create the div in twig)
      message = "Une erreur innatendue est survenue:"+ obj.code + " " + obj.message;
      alert(message);
    }
    else if(obj.status === "success")
    {
      comments = JSON.parse(obj.data);
      comments.forEach(comment => {
        oneComponent = document.createElement("div");
        user = document.createElement("div");
        contenu = document.createElement("div");
        // createdAt = document.createElement("div");
        
        user.innerHTML = comment.email;
        contenu.innerHTML = comment.contenu;
        // createdAt.innerHTML = comment.createdAt;
        
        oneComponent.appendChild(user);
        oneComponent.appendChild(contenu);
        // oneComponent.appendChild(createdAt);

        componentComments.appendChild(oneComponent);
      });
      offset = obj.offset + 2; 
      componentComments.setAttribute("class","comment");
      document.querySelector("body").appendChild(componentComments);
    }
  }
  catch(e)
  {
    alert("Impossible d'accéder aux données " + e.message);
  }
  
}

var offset = 0;
var limit = 2;

//management
  document
.querySelector("#load_more")
.addEventListener("click",(e)=> {
if(typeof(urlInit) !== "undefined")
{
  url = createUrl( offset, limit)
  sendData( url);
}
  e.preventDefault();
});

//function

function sendData( url) 
{
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
    if(XHR.readyState === 4 && XHR.status === 200) {
      addComment(XHR.responseText);
    }
  }
  XHR.send(url);
}

