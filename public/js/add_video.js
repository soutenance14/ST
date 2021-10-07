var index = 0;
const addTagFormDeleteLink = (tagFormLi) => {
    const removeFormButton = document.createElement('button')
    removeFormButton.classList
    removeFormButton.innerText = 'Supprimer ce lien'
    removeFormButton.setAttribute('class','btn-remove-video btn-secondary' )

    tagFormLi.append(removeFormButton);

    removeFormButton.addEventListener('click', (e) => {
        e.preventDefault()
        // remove the li for the tag form
        tagFormLi.remove();
    });
}

const addFormToCollection = (e) => {
    const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);
  
    const item = document.createElement('li');
  
    item.innerHTML = collectionHolder
      .dataset
      .prototype
      .replace(
        /__name__/g,
        index
        // collectionHolder.dataset.index
      );
        
    item.style.listStyle = "none";
    collectionHolder.appendChild(item);
    // item.setAttribute('class', 'form-control');
    console.log(item);
    index++;
    // collectionHolder.dataset.index++;

     // add a delete link to the new form
     addTagFormDeleteLink(item);
  };

  //management
  document
.querySelectorAll('.add_item_link')
.forEach(btn => btn.addEventListener("click", addFormToCollection));
console.log("test fin");