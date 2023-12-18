// On récupère les boutons précédent et suivant
let previous = document.getElementById('previous');
let next = document.getElementById('next');
// On récupère le tableau, le corps du tableau, les lignes du tableau
let table = document.querySelector('table');
let tbody = document.querySelector('tbody');
let rows = document.querySelectorAll('tbody tr');
// On récupère le nombre de lignes par page, le numéro de page courante et le nombre total de pages
let rowsPerPageInput = document.getElementById('rowsPerPage');

// On ajoute un écouteur d'événement sur le nombre de lignes par page, permettant de vérifier que la valeur saisie est correcte
rowsPerPageInput.addEventListener('input', function () {
    let enteredValue = parseInt(rowsPerPageInput.value);
    if (enteredValue <= 0 || isNaN(enteredValue)) {
        // Si la valeur est vide, égale à zéro ou non numérique
        // Remplacer par une valeur par défaut, par exemple, 1
        rowsPerPageInput.value = 1; // Ou une autre valeur par défaut
    }
    // Réinitialiser la page courante et afficher les lignes
    currentPage = 1;
    showRows();
    let numberOfPages = Math.ceil(rows.length / parseInt(rowsPerPageInput.value));
    totalPages.textContent = numberOfPages;
});

let currentPageNumber = document.getElementById('currentPageNumber');
let totalPages = document.getElementById('totalPages');
let currentPage = 1; // Initialisation de la page courante

// Fonction qui affiche les lignes en fonction de la page courante
function showRows() {
    // On récupère le nombre de lignes par page et le numéro de page courante
    let rowsPerPage = parseInt(rowsPerPageInput.value);
    let firstRow = (currentPage - 1) * rowsPerPage;
    let lastRow = firstRow + rowsPerPage; // On calcule le numéro de la dernière ligne à afficher sachant que la dernière ligne est exclue car vide

    // On itère pour afficher les lignes en fonction de la page courante
    for (let i = 0; i < rows.length; i++) {
        if (i >= firstRow && i < lastRow) {
            rows[i].style.display = 'table-row';
        } else {
            rows[i].style.display = 'none';
        }
    }

    // On calcule le nombre total de pages et on affiche la page courante
    currentPageNumber.textContent = currentPage;
    let visibleRowCount = 0;
    for (let i = 0; i < rows.length; i++) {
        if (rows[i].style.display === 'table-row') {
            visibleRowCount++;
        }
    }
    // On affiche le nombre de lignes visibles
    document.getElementById('visibleRows').textContent = visibleRowCount;
}

// Fonction qui affiche la page précédente
function previousPage() {
    if (currentPage > 1) {
        currentPage--;
        showRows();
    }
}
// On ajoute un écouteur d'événement sur le bouton précédent qui appelle la fonction previousPage
previous.addEventListener('click', previousPage);

// Fonction qui affiche la page suivante
function nextPage() {
    let rowsPerPage = parseInt(rowsPerPageInput.value);
    let numberOfPages = Math.ceil(rows.length / rowsPerPage);
    if (currentPage < numberOfPages) {
        currentPage++;
        showRows();
    }
}
// On ajoute un écouteur d'événement sur le bouton suivant qui appelle la fonction nextPage
next.addEventListener('click', nextPage);

// On ajoute un écouteur d'événement sur le nombre de lignes par page qui appelle une fonction anonyme
rowsPerPageInput.addEventListener('input', function () {
    // On réinitialise la page courante et on affiche les lignes
    currentPage = 1;
    showRows();
    let numberOfPages = Math.ceil(rows.length / parseInt(rowsPerPageInput.value));
    totalPages.textContent = numberOfPages;
});

// On calcule le nombre total de pages et on l'affiche
let numberOfPages = Math.ceil(rows.length / parseInt(rowsPerPageInput.value));
totalPages.textContent = numberOfPages;
showRows(); // On affiche les lignes

// On ajoute un écouteur d'événement sur le chargement du DOM qui appelle une fonction anonyme (chargement de la page)
document.addEventListener('DOMContentLoaded', function () {
    showRows();
});