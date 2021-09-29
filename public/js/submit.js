var submitButton = document.getElementById('submitButton');
var successMessage = document.getElementById("submitSuccessMessage");
var errorMessage = document.getElementById("submitErrorMessage");

var floatingForms = document.getElementsByClassName("form-floating");
var postForms = document.getElementsByClassName("post-form");

var form = document.forms["form"];

var post = [];

successMessage.style.display = 'none';
errorMessage.style.display = 'none';

// specific element will be verified
//the caller page give the specific var anfd specific function thing

sendDataOnClick(post);

function validateForm()
{
  $validateForm = true;
  for(postForm of postForms)
  {
    var editText = (postForm.getElementsByClassName("form-control"))[0];
    var invalid = (postForm.getElementsByClassName("invalid-feedback"))[0];
    if(! validateType(editText))
    {
      console.log("erreur validateType");
      $validateForm = false;
      invalid.style.display = 'block';
    }
    else
    {
      post[ editText['name']] =   editText['value'] ;
      console.log(post);
    }
  };
  return $validateForm;
}

function validateType(editText)
{
  $validateType = true;
  $value = editText['value'];
  $type = editText['type'];
  
  //textare part
  if(editText.tagName === 'TEXTAREA')
  {
    if($value.length < 3)
      {
        $validateType = false;
      }
  }
  
  //input part
  if(editText.tagName === 'INPUT')
  {
    switch($type)
    {
      case 'text':
        if($value === '')
        {
          $validateType = false;
        }
        break;  
        case 'password':
          if($value.length < 6)
          {
            $validateType = false;
          }
          break;
          case 'email':
            $validateType = false;
            if($value.length >= 6 )
            {
              $values = $value.split("@");
              if($values.length === 2)
              {
                //start something1@something2.something3
                //after the split
          // $values is like something1,something2.something3 
          $values = $values[1].split(".");
          if($values.length === 2)
          {
            // $values = something2.something3
            $value = $values[1];
            if($value.length >= 2)
            {
              $validateType = true;
            }
          }
        }
      }
      break;
    }
  }
  return $validateType;
}

function hideAllErrorAndSuccessMessage()
{
  errorMessage.style.display = 'none';
  successMessage.style.display = 'none';
  for(floatingForm of floatingForms)
    {
      //hide specific errorif exists
      var specificFeedback = floatingForm.getElementsByClassName("specific-feedback")[0];
      if(typeof specificFeedback !== 'undefined'  )
      {
        specificFeedback.style.display = 'none';
      }
      //hide rest error 
      var invalid = (floatingForm.getElementsByClassName("invalid-feedback"))[0];
      invalid.style.display = 'none';
    };
}
function sendDataOnClick(post)
{
  submitButton.addEventListener('click', event => {
    hideAllErrorAndSuccessMessage();
    if(validateForm() && validFormSpecificPage())
      {
        displaySomethingSpecific();
        url = createURL();
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

  // Transformez l'objet data en un tableau de paires clé/valeur codées URL.
  for(name in data) {
    urlEncodedDataPairs.push(encodeURIComponent(name) + '=' + encodeURIComponent(data[name]));
  }

  // Combinez les paires en une seule chaîne de caractères et remplacez tous
  // les espaces codés en % par le caractère'+' ; cela correspond au comportement
  // des soumissions de formulaires de navigateur.
  urlEncodedData = urlEncodedDataPairs.join('&').replace(/%20/g, '+');

  // Définissez ce qui se passe en cas de succès de soumission de données
  // XHR.addEventListener('load', function(event) {
  //   alert('Ouais ! Données envoyées et réponse chargée.');
  // });

  // Définissez ce qui arrive en cas d'erreur
  XHR.addEventListener('error', function(event) {
    alert('Oups! Une erreur s\' produite,  avec l\'object XMLHttpRequest permettant l\'interraction entre serveurs.');
  });

  // Configurez la requête
  // XHR.open('POST', 'https://example.com/cors.php');
  XHR.open('POST', url);
  // XHR.open('POST', 'sendMessage');
  
  // Ajoutez l'en-tête HTTP requise pour requêtes POST de données de formulaire
  XHR.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  // Finalement, envoyez les données.
  // XHR.onreadystatechange = function() 
  XHR.onreadystatechange = function() 
  {//Call a function when the state changes.
      hideSomethingSpecific();
      if(XHR.readyState == 4 && XHR.status == 200) {
          console.log(XHR.responseText);
          if(XHR.responseText === 'success')
          {
              successMessage.style.display = 'block';
              doSomethingSpecificSuccess();
          }
          else
          {
            errorMessage.style.display = 'block';
            errorMessage.innerHTML = XHR.responseText;
            errorMessage.className = "text-center text-danger mb-3";
            console.log('test'+XHR.responseText+'test');
            doSomethingSpecificError();
          }
      }
      // else{
      //     alert('pas de reponse');
      // }
  }
  XHR.send(urlEncodedData);
}

