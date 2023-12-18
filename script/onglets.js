// Fonction permettant de switcher entre les onglets de la page index de l'admin
function onglets_switch(evt, ongletName) {
    var i, tabcontent, tablinks;
    // i est une variable qui représente l'index de l'élément courant dans la collection.
    // tabcontent est une variable qui représente les éléments de classe tabcontent.
    // tablinks est une variable qui représente les éléments de classe tablinks.

    // Cacher tous les éléments avec la classe tabcontent
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // Supprimer la classe active de tous les éléments avec la classe tablinks
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tabcontent.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Afficher l'onglet actuel et ajouter une classe active au bouton qui a ouvert l'onglet
    document.getElementById(ongletName).style.display = "block";
    evt.currentTarget.className += " active";
}
