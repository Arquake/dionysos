form = document.getElementById("liste-commandes");

selectTag = document.getElementById("liste-commandes-select");

formArticles = document.getElementById("form-articles");

articlesList = {};

currentListOfArticle = {};


(document.getElementById("ajout-article")).addEventListener("click", (e) => {
    e.preventDefault();
    if (selectTag.value in articlesList) {

        if(selectTag.value in currentListOfArticle){
            increaseQuantityElement(currentListOfArticle[selectTag.value], articlesList[selectTag.value]);
        } else {
            createLineArticles(selectTag.value);
        }
    }
});


(document.getElementById("checkout-validation")).addEventListener("click", (e) => {
    for(item in currentListOfArticle) {
        itemElement = document.createElement('input');
        itemElement.className = 'hidden';
        itemElement.name = item;
        itemElement.value = currentListOfArticle[item].quantity;
        form.appendChild(itemElement);
    }
});


function buttonDelClearElements(element) {
    (element.itemNameElement).remove();
    (element.quantityElement).remove();
    (element.priceElement).remove();
    (element.delButtonElement).remove();
}

function decreaseQuantityElement(value) {
    element = currentListOfArticle[value];
    articleListElement = articlesList[value];
    element.quantity--;
    if (element.quantity <= 0) {
        buttonDelClearElements(element);
        delete currentListOfArticle[value];
    } else {
        element.quantityElement.textContent = element.quantity;
        element.priceElement.textContent = (articleListElement.prix * element.quantity).toFixed(2);
    }
}

function increaseQuantityElement(element,articleListElement) {
    element.quantity++;
    element.quantityElement.textContent = element.quantity;
    element.priceElement.textContent = (articleListElement.prix * element.quantity).toFixed(2);
}

function createLineArticles(value){
    itemNameElement = document.createElement('div');
    itemNameElement.className = 'col-span-5 p-2 mt-2';
    itemNameElement.textContent = articlesList[value].nom;

    quantityElement = document.createElement('div');
    quantityElement.className = 'col-span-1 col-start-6 p-2 mt-2';
    quantityElement.textContent = 1;

    priceElement = document.createElement('div');
    priceElement.className = 'col-span-1 col-start-7 p-2 mt-2';
    priceElement.textContent = articlesList[value].prix;

    delButtonElement = document.createElement('button');
    delButtonElement.className = 'col-span-1 col-start-8 p-2 bg-red-500 text-white rounded-md mt-2 active:bg-red-600 duration-150';
    delButtonElement.textContent = 'retirer';
    delButtonElement.value = value;
    addListenerOndelButton(delButtonElement)

    currentListOfArticle[value] = {quantity : 1,
        quantityElement : quantityElement,
        priceElement : priceElement,
        itemNameElement: itemNameElement,
        delButtonElement : delButtonElement,};

    form.appendChild(itemNameElement);
    form.appendChild(quantityElement);
    form.appendChild(priceElement);
    form.appendChild(delButtonElement);
}

function addListenerOndelButton(element){
    element.addEventListener("click", (e) => {
        e.preventDefault();
        if(e.target.value in currentListOfArticle) {
            decreaseQuantityElement(e.target.value);
        }
    });
}