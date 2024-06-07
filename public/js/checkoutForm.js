form = document.getElementById("liste-commandes");

selectTag = document.getElementById("liste-commandes-select");

articlesList = {};



(document.getElementById("ajout-article")).addEventListener("click", (e) => {
    e.preventDefault();

    itemNameElement = document.createElement('div');
    itemNameElement.className = 'col-span-5 p-2';
    itemNameElement.textContent = articlesList[selectTag.value].nom;

    quantityElement = document.createElement('div');
    quantityElement.className = 'col-span-1 col-start-6 p-2';
    quantityElement.textContent = 1;

    priceElement = document.createElement('div');
    priceElement.className = 'col-span-1 col-start-7 p-2';
    priceElement.textContent = articlesList[selectTag.value].prix;

    delButtonElement = document.createElement('button');
    delButtonElement.className = 'col-span-1 col-start-8 p-2 bg-red-500 text-white rounded-md';
    delButtonElement.textContent = 'supprimer';

    form.appendChild(itemNameElement);
    form.appendChild(quantityElement);
    form.appendChild(priceElement);
    form.appendChild(delButtonElement);
})

