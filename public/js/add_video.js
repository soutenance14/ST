const addTagFormDeleteLink = (tagFormLi) => {
    const removeFormButton = document.createElement('button')
    removeFormButton.classList
    removeFormButton.innerText = 'Delete this tag'

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
        collectionHolder.dataset.index
      );
  
    collectionHolder.appendChild(item);
  
    collectionHolder.dataset.index++;

     // add a delete link to the new form
     addTagFormDeleteLink(item);
  };

  //management
  document
.querySelectorAll('.add_item_link')
.forEach(btn => btn.addEventListener("click", addFormToCollection));
console.log("test fin");