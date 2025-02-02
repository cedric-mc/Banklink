// Cette fonction est utilisée pour trier un tableau HTML en fonction d'une colonne spécifiée.
function sortTable(n) {
    // Déclaration des variables nécessaires pour le tri
    var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;

    // Sélection de la table à trier
    table = document.querySelector("table");

    // Initialisation des variables de contrôle du tri
    switching = true;
    dir = "asc"; // Direction du tri : ascendant par défaut

    // Boucle tant qu'il y a des éléments à trier
    while (switching) {
        switching = false;
        rows = table.rows;

        // Parcours des lignes du tableau
        for (i = 1; i < (rows.length - 1); i++) {
            shouldSwitch = false;

            // Récupération des valeurs à comparer
            x = parseFloat(rows[i].getElementsByTagName("TD")[n].innerHTML);
            y = parseFloat(rows[i + 1].getElementsByTagName("TD")[n].innerHTML);

            // Comparaison des valeurs en fonction de la direction du tri
            if (dir == "asc") {
                if (x > y) {
                    shouldSwitch = true;
                    break;
                }
            } else if (dir == "desc") {
                if (x < y) {
                    shouldSwitch = true;
                    break;
                }
            }
        }

        // Si un échange doit être effectué
        if (shouldSwitch) {
            // Echange des lignes
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            switchcount++;
        } else {
            // Si aucun échange n'a été effectué et que la direction est ascendante, on change la direction
            if (switchcount == 0 && dir == "asc") {
                dir = "desc";
                switching = true;
            }
        }
    }
}