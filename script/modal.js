const modalContainer = document.querySelector(".modal-container"); // Sélectionner le modal
const modalContent = document.querySelector("#modal-content"); // Sélectionner le contenu du modal
const modalTableContainer = document.querySelector("#modal-table-container"); // Sélectionner le conteneur du tableau du modal

// Fonction pour charger le contenu du modal
function loadModalContent(url, rowIndex) {
    $.ajax({ // Requête AJAX pour charger le contenu du modal (c'est-à-dire sans recharger la page)
        // URL du modal à charger et type de la requête de chargement
        url: url,
        type: "GET",
        data: { rowIndex: rowIndex }, // Envoyer l'index de la ligne cliquée
        success: function (data) { // Fonction à exécuter si la requête est réussie : afficher le contenu du modal
            modalTableContainer.innerHTML = data;
            modalContainer.classList.add("active");
        },
        error: function () { // Fonction à exécuter si la requête échoue : afficher un message d'erreur dans le modal
            modalTableContainer.innerHTML = "Erreur lors du chargement du tableau.";
            modalContainer.classList.add("active");
        }
    });
}

// Charger le contenu du modal lors du clic sur une ligne du tableau
document.addEventListener("click", function (event) {
    const rowIndex = event.target.getAttribute("data-row"); // Récupérer l'index de la ligne cliquée, rowIndex = n° de remise pour les remises ou n° de siren d'un client
    if (rowIndex !== null) { // Si l'index n'est pas nul, c'est-à-dire si une ligne a été cliquée
        if (event.target.classList.contains("modal-trigger-remise")) { // Si la ligne cliquée est une ligne de remise (de la page 'client/remise.php')
            loadModalContent("../includes/detailed_table_remise.php", rowIndex);
        } else if (event.target.classList.contains("modal-trigger-impaye")) { // Si la ligne cliquée est une ligne d'impayé (de la page 'product-owner/impaye.php')
            loadModalContent("../includes/detailed_table_impaye.php", rowIndex);
        }
    }
});

// Fermer le modal lors du clic sur le bouton de fermeture
const closeBtn = document.getElementById("close-btn");
closeBtn.addEventListener("click", () => {
    modalContainer.classList.remove("active");
    modalTableContainer.innerHTML = ""; // Effacer le contenu précédent
});
