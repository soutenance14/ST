
//const & var
const addComment = (data)=>{
  try
  {
    const componentComments = document.createElement("div");
    obj = JSON.parse(data);
    if(obj.message === "success")
    {
      comments = obj.data;
      comments.forEach(comment => {
        oneComponent = document.createElement("div");
        user = document.createElement("div");
        contenu = document.createElement("div");
        createdAt = document.createElement("div");
        
        user.innerHTML = comment.email;
        contenu.innerHTML = comment.contenu;
        createdAt.innerHTML = comment.created_at;
        
        oneComponent.appendChild(user);
        oneComponent.appendChild(contenu);
        oneComponent.appendChild(createdAt);

        componentComments.appendChild(oneComponent);
        console.log(obj.theoffset + "the offset")
      });
      offset +=2; 
      componentComments.setAttribute("class","comment");
      document.querySelector("body").appendChild(componentComments);
    }
    else
    {
      alert("Pas de commentaires supplémentaires trouvés.");
    }
  }
  catch(e)
  {
    alert("Impossible d'accéder aux données");
  }
  
}

var limit = 2;
var offset = 0;

//management
  document
.querySelector("#load_more")
.addEventListener("click",(e)=> {
if(typeof(urlInit) !== "undefined")
{
  url = createUrl( limit, offset)
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
  // XHR.open("POST", "https://example.com/cors.php");
  XHR.open("POST", url);
  // XHR.open("POST", "sendMessage");
  
  // Ajoutez l"en-tête HTTP requise pour requêtes POST de données de formulaire
  XHR.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  // Finalement, envoyez les données.
  // XHR.onreadystatechange = function() 
  XHR.onreadystatechange = function() 
  {//Call a function when the state changes.
    if(typeof hideSomethingSpecific === "function"){  
      hideSomethingSpecific();
    }
      if(XHR.readyState === 4 && XHR.status == 200) {
            addComment(XHR.responseText);
      }
  }
  XHR.send(url);
}

