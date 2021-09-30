var submitButton = document.getElementById("submitButton");
var successMessage = document.getElementById("submitSuccessMessage");
var errorMessage = document.getElementById("submitErrorMessage");

var floatingForms = document.getElementsByClassName("form-floating");
var postForms = document.getElementsByClassName("post-form");

var post = [];

successMessage.style.display = "none";
errorMessage.style.display = "none";


// specific element will be verified
//the caller page give the specific var anfd specific function thing
if(typeof(url) !== "undefined")
{
  sendDataOnClick(post, url);
}


function sendDataOnClick(post, url)
{
  // submitButton.addEventListener("click", event => {
  //     // contactForm.submit();
  //     sendData(post, url);
  //   });
  submitButton.addEventListener("click", event => {

    validComplement = true;
    if(typeof validFormSpecificPage === 'function')
    {
      validComplement = validFormSpecificPage();
    }
    if(validateForm() && validComplement)
      {
        if(typeof displaySomethingSpecific === 'function'){
          displaySomethingSpecific();
        }
        sendData(post, url);
      }
    });
}

function sendData(data, url) 
{
  var XHR = new XMLHttpRequest();
  var urlEncodedData = "";
  var urlEncodedDataPairs = [];
  var name;

  // Transformez l"objet data en un tableau de paires clé/valeur codées URL.
  for(name in data) {
    urlEncodedDataPairs.push(encodeURIComponent(name) + "=" + encodeURIComponent(data[name]));
  }

  // Combinez les paires en une seule chaîne de caractères et remplacez tous
  // les espaces codés en % par le caractère"+" ; cela correspond au comportement
  // des soumissions de formulaires de navigateur.
  urlEncodedData = urlEncodedDataPairs.join("&").replace(/%20/g, "+");

  // Définissez ce qui se passe en cas de succès de soumission de données
  // XHR.addEventListener("load", function(event) {
  //   alert("Ouais ! Données envoyées et réponse chargée.");
  // });

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
    if(typeof hideSomethingSpecific === 'function'){  
      hideSomethingSpecific();
    }
      if(XHR.readyState === 4 && XHR.status == 200) {
          if(XHR.responseText === "success")
          {
              successMessage.style.display = "block";
              if(typeof doSomethingSpecificSuccess === 'function'){
                doSomethingSpecificSuccess();
              }
          }
          else if(XHR.responseText === "error")
          {
            errorMessage.style.display = "block";
            if(typeof doSomethingSpecificError === 'function'){
              doSomethingSpecificError();
            }
            }
          else
          {
            errorMessage.style.display = "block";
            errorMessage.innerHTML = XHR.responseText;
            errorMessage.className = "text-center text-danger mb-3";
            if(typeof doSomethingSpecificError === 'function'){
              doSomethingSpecificError();
            }
          }
      }
      // else{
      //     alert("pas de reponse");
      // }
  }
  XHR.send(urlEncodedData);
}