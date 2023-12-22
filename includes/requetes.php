<?php
$requetes = array(
    "select_user" => "
    SELECT idUser, password, type, mail 
    FROM UTILISATEUR 
    WHERE login = :login;",
    // Requête pour la récupération des données de l'utilisateur selon son login

    "select_client" => "
    SELECT raisonSociale, devise, numCarte, reseau 
    FROM CLIENT 
    WHERE idUser = :idUser;",
    // Requête pour la récupération des données du client selon son idUser

    "select_client_global_treso" => "
    SELECT devise, SUM(r.montant) AS tresoglobal
    FROM CLIENT c JOIN REMISE r ON c.siren = r.siren
    WHERE c.siren = :siren;",
    // Requête pour la récupération de la trésorerie global du client selon son siren

    "select_po_global_treso" => "
    SELECT login, raisonSociale, c.siren, devise, SUM(r.montant) AS tresoglobal
    FROM UTILISATEUR u, CLIENT c, REMISE r
    WHERE u.idUser = c.idUser AND c.siren = r.siren AND u.password IS NOT NULL
    GROUP BY login, raisonSociale, c.siren, devise;",
    // Requête pour la récupération de la trésorerie global de tous les clients

    "select_client_treso" => "
    SELECT c.siren,
           raisonSociale,
           (SELECT COUNT(*)
            FROM REMISE r2
            WHERE r2.numRemise = r.numRemise) AS transactions,
           devise,
            (SELECT SUM(r2.montant)
             FROM REMISE r2
             WHERE r2.numRemise = r.numRemise) AS montant
    FROM CLIENT c,
         REMISE r
    WHERE c.siren = r.siren
      AND c.siren = :siren
    GROUP BY numRemise;",
    // Requête pour la récupération de la trésorerie du client selon son siren

    "select_client_treso_date" => "
    SELECT c.siren,
           raisonSociale,
           (SELECT COUNT(*)
            FROM REMISE r2
            WHERE r2.numRemise = r.numRemise) AS transactions,
            devise,
            (SELECT SUM(r2.montant)
             FROM REMISE r2
             WHERE r2.numRemise = r.numRemise) AS montant
    FROM CLIENT c,
         REMISE r
    WHERE c.siren = r.siren
      AND c.siren = :siren
      AND DATE(r.dateRemise) = :treso
    GROUP BY numRemise;",
    // Requête pour la récupération de la trésorerie du client selon son siren et une date

    "select_client_remise_num" => "
    SELECT c.siren,
           raisonSociale,
           numRemise,
           dateRemise,
           (SELECT COUNT(*)
            FROM REMISE r2
            WHERE r2.numRemise = r.numRemise) AS transactions,
           devise,
           (SELECT SUM(r2.montant)
             FROM REMISE r2
             WHERE r2.numRemise = r.numRemise) AS montant
    FROM REMISE r,
         CLIENT c
    WHERE r.siren = c.siren
      AND c.siren = :siren
      AND numRemise = :remise
    GROUP BY numRemise,
             dateRemise;",
    // Requête pour la récupération des données d'une remise du client selon son siren et le numéro de la remise

    "select_po_remise_num" => "
    SELECT c.siren,
           raisonSociale,
           numRemise,
           dateRemise,
           (SELECT COUNT(*) 
            FROM REMISE r2
            WHERE r2.numRemise = r.numRemise) AS transactions,
           devise,
           (SELECT SUM(r2.montant)
            FROM REMISE r2
            WHERE r2.numRemise = r.numRemise) AS montant
    FROM REMISE r,
         CLIENT c
    WHERE r.siren = c.siren 
      AND numRemise = :numRemise
    GROUP BY numRemise,
             dateRemise,
             c.siren;",
    // Requête pour la récupération des données d'une remise du client selon le numéro de la remise

    "select_client_remise" => "
    SELECT c.siren,
           raisonSociale,
           numRemise,
           dateRemise,
           (SELECT COUNT(*)
            FROM REMISE r2
            WHERE r2.numRemise = r.numRemise) AS transactions,
           devise,
           (SELECT SUM(r2.montant)
             FROM REMISE r2
             WHERE r2.numRemise = r.numRemise) AS montant
    FROM REMISE r,
         CLIENT c
    WHERE r.siren = c.siren
      AND c.siren = :siren
    GROUP BY numRemise,
             dateRemise;",
    // Requête pour la récupération des données des remises du client selon son siren

    "select_po_remise" => "
    SELECT c.siren,
           raisonSociale,
           numRemise,
           dateRemise,
           (SELECT COUNT(*)
            FROM REMISE r2
            WHERE r2.numRemise = r.numRemise) AS transactions,
           devise,
           (SELECT SUM(r2.montant)
             FROM REMISE r2
             WHERE r2.numRemise = r.numRemise) AS montant
    FROM REMISE r,
         CLIENT c
    WHERE r.siren = c.siren
    GROUP BY numRemise,
             dateRemise,
             c.siren;",
    // Requête pour la récupération des données des remises de tous les clients

    "select_client_remise_date" => "
    SELECT c.siren,
           raisonSociale,
           numRemise,
           dateRemise,
           (SELECT COUNT(*)
            FROM REMISE r2
            WHERE r2.numRemise = r.numRemise) AS transactions,
           devise,
           (SELECT SUM(r2.montant)
             FROM REMISE r2
             WHERE r2.numRemise = r.numRemise) AS montant
    FROM REMISE r,
         CLIENT c
    WHERE r.siren = c.siren
      AND c.siren = :siren
      AND dateRemise BETWEEN :debut AND :fin
    GROUP BY numRemise,
             dateRemise;",
    // Requête pour la récupération des données des remises du client selon son siren et deux dates

    "select_client_transactions" => "
    SELECT c.siren,
           DATE(dateTransaction) AS dateTransaction,
           numCarte,
           reseau,
           n_autorisation,
           devise,
           montant
    FROM REMISE r,
         CLIENT c
    WHERE r.siren = c.siren
      AND numRemise = :remise
    GROUP BY dateTransaction,
             n_autorisation,
             siren,
             montant;",
    // Requête pour la récupération des données des transactions d'une remise du client selon le numéro de la remise

    "select_po_treso" => "
    SELECT c.siren,
           raisonSociale,
           (SELECT COUNT(*)
            FROM REMISE r2
            WHERE r2.numRemise = r.numRemise) AS transactions,
           devise,
            (SELECT SUM(r2.montant)
             FROM REMISE r2
             WHERE r2.numRemise = r.numRemise) AS montant
    FROM CLIENT c,
         REMISE r
    WHERE c.siren = r.siren
    GROUP BY numRemise, 
             siren;",
    // Requête pour la récupération de la trésorerie de tous les clients

    "select_po_treso_rs" => "
    SELECT c.siren,
           raisonSociale,
           (SELECT COUNT(*)
            FROM REMISE r2
            WHERE r2.numRemise = r.numRemise) AS transactions,
           devise,
            (SELECT SUM(r2.montant)
             FROM REMISE r2
             WHERE r2.numRemise = r.numRemise) AS montant
    FROM CLIENT c,
         REMISE r
    WHERE c.siren = r.siren
      AND raisonSociale = :rs
    GROUP BY raisonSociale,
             numRemise,
             siren;",
    // Requête pour la récupération de la trésorerie de tous les clients selon la raison sociale

    "select_po_treso_date" => "
    SELECT c.siren,
           raisonSociale,
           (SELECT COUNT(*)
            FROM REMISE r2
            WHERE r2.numRemise = r.numRemise) AS transactions,
            devise,
            (SELECT SUM(r2.montant)
             FROM REMISE r2
             WHERE r2.numRemise = r.numRemise) AS montant
    FROM CLIENT c,
         REMISE r
    WHERE c.siren = r.siren
      AND DATE(r.dateRemise) = :treso
    GROUP BY numRemise;",
    // Requête pour la récupération de la trésorerie de tous les clients selon une date

    "select_client_impaye_dossier" => "
    SELECT c.siren,
           raisonSociale,
           dateTransaction,
           dateRemise,
           numCarte,
           reseau,
           numDossierImpaye,
           devise,
           montant,
           libelle
    FROM REMISE r,
         CLIENT c,
         MOTIFS_IMPAYES m
    WHERE r.siren = c.siren
      AND c.siren = :siren
      AND numDossierImpaye = :numDossierImpaye
      AND m.code = r.code
      AND r.montant < 0
    GROUP BY numDossierImpaye,
             dateTransaction,
             dateRemise,
             montant,
             libelle;",
    // Requête pour la récupération des données d'un impayé du client selon son siren et le numéro du dossier

    "select_client_impaye" => "
    SELECT c.siren,
           raisonSociale,
           dateTransaction,
           dateRemise,
           numCarte,
           reseau,
           numDossierImpaye,
           devise,
           montant,
           libelle
    FROM REMISE r,
         CLIENT c,
         MOTIFS_IMPAYES m
    WHERE r.siren = c.siren
      AND c.siren = :siren
      AND m.code = r.code
      AND r.montant < 0
    GROUP BY numDossierImpaye,
             dateTransaction,
             dateRemise,
             montant,
             libelle;",
    // Requête pour la récupération des données des impayés du client selon son siren

    "select_client_impaye_date" => "
    SELECT c.siren,
           raisonSociale,
           dateTransaction,
           dateRemise,
           numCarte,
           reseau,
           numDossierImpaye,
           devise,
           montant,
           libelle
    FROM REMISE r,
         CLIENT c,
         MOTIFS_IMPAYES m
    WHERE r.siren = c.siren
      AND c.siren = :siren
      AND m.code = r.code
      AND r.montant < 0
      AND dateRemise BETWEEN :debut AND :fin
    GROUP BY numDossierImpaye,
             dateTransaction,
             dateRemise,
             montant,
             libelle;",
    // Requête pour la récupération des données des impayés du client selon son siren et deux dates

    "select_po_treso_siren_date" => "
    SELECT c.siren,
           raisonSociale,
           (SELECT COUNT(*)
            FROM REMISE r2
            WHERE r2.numRemise = r.numRemise) AS transactions,
            devise,
            (SELECT SUM(r2.montant)
             FROM REMISE r2
             WHERE r2.numRemise = r.numRemise) AS montant
    FROM CLIENT c,
         REMISE r
    WHERE c.siren = r.siren
      AND c.siren = :siren
      AND DATE(r.dateRemise) = :treso
    GROUP BY numRemise,
             siren;",
    // Requête pour la récupération de la trésorerie du client selon son siren et une date

    "select_po_treso_rs_date" => "
    SELECT c.siren,
           raisonSociale,
           (SELECT COUNT(*)
            FROM REMISE r2
            WHERE r2.numRemise = r.numRemise) AS transactions,
            devise,
            (SELECT SUM(r2.montant)
             FROM REMISE r2
             WHERE r2.numRemise = r.numRemise) AS montant
    FROM CLIENT c,
         REMISE r
    WHERE c.siren = r.siren
      AND raisonSociale = :rs
      AND DATE(r.dateRemise) = :treso
      GROUP BY numRemise,
             siren;",
    // Requête pour la récupération de la trésorerie du client selon sa raison sociale et une date

    "select_po_remise_siren" => "
    SELECT c.siren,
           raisonSociale,
           numRemise,
           dateRemise,
           (SELECT COUNT(*)
            FROM REMISE r2
            WHERE r2.numRemise = r.numRemise) AS transactions,
           devise,
           (SELECT SUM(r2.montant)
             FROM REMISE r2
             WHERE r2.numRemise = r.numRemise) AS montant
    FROM REMISE r,
         CLIENT c
    WHERE r.siren = c.siren
      AND c.siren = :siren
    GROUP BY numRemise,
             dateRemise,
             c.siren;",
    // Requête pour la récupération des données des remises du client selon son siren

    "select_po_remise_siren_num" => "
    SELECT c.siren,
           raisonSociale,
           numRemise,
           dateRemise,
           (SELECT COUNT(*)
            FROM REMISE r2
            WHERE r2.numRemise = r.numRemise) AS transactions,
           devise,
           (SELECT SUM(r2.montant)
             FROM REMISE r2
             WHERE r2.numRemise = r.numRemise) AS montant
    FROM REMISE r,
         CLIENT c
    WHERE r.siren = c.siren
      AND c.siren = :siren
      AND numRemise = :numRemise
    GROUP BY numRemise,
             dateRemise,
             c.siren;",
    // Requête pour la récupération des données d'une remise du client selon son siren et le numéro de la remise

    "select_po_remise_siren_date" => "
    SELECT c.siren,
           raisonSociale,
           numRemise,
           dateRemise,
           (SELECT COUNT(*)
            FROM REMISE r2
            WHERE r2.numRemise = r.numRemise) AS transactions,
           devise,
           (SELECT SUM(r2.montant)
             FROM REMISE r2
             WHERE r2.numRemise = r.numRemise) AS montant
    FROM REMISE r,
         CLIENT c
    WHERE r.siren = c.siren
      AND c.siren = :siren
      AND dateRemise BETWEEN :debut AND :fin
    GROUP BY numRemise,
            dateRemise,
            c.siren;",
    // Requête pour la récupération des données des remises du client selon son siren et deux dates

    "select_po_remise_rs" => "
    SELECT c.siren,
           raisonSociale,
           numRemise,
           dateRemise,
           (SELECT COUNT(*)
            FROM REMISE r2
            WHERE r2.numRemise = r.numRemise) AS transactions,
           devise,
           (SELECT SUM(r2.montant)
             FROM REMISE r2
             WHERE r2.numRemise = r.numRemise) AS montant
    FROM REMISE r,
         CLIENT c
    WHERE r.siren = c.siren
      AND raisonSociale = :rs
    GROUP BY numRemise,
             dateRemise,
             c.siren;",
    // Requête pour la récupération des données des remises du client selon sa raison sociale

    "select_po_remise_rs_num" => "
    SELECT c.siren,
           raisonSociale,
           numRemise,
           dateRemise,
           (SELECT COUNT(*)
            FROM REMISE r2
            WHERE r2.numRemise = r.numRemise) AS transactions,
           devise,
           (SELECT SUM(r2.montant)
             FROM REMISE r2
             WHERE r2.numRemise = r.numRemise) AS montant
    FROM REMISE r,
         CLIENT c
    WHERE r.siren = c.siren
      AND raisonSociale = :rs
      AND numRemise = :numRemise
    GROUP BY numRemise,
            dateRemise,
            c.siren;",
    // Requête pour la récupération des données d'une remise du client selon sa raison sociale et le numéro de la remise

    "select_po_remise_rs_date" => "
    SELECT c.siren,
           raisonSociale,
           numRemise,
           dateRemise,
           (SELECT COUNT(*)
            FROM REMISE r2
            WHERE r2.numRemise = r.numRemise) AS transactions,
           devise,
            (SELECT SUM(r2.montant)
             FROM REMISE r2
             WHERE r2.numRemise = r.numRemise) AS montant
    FROM REMISE r,
         CLIENT c
    WHERE r.siren = c.siren
      AND raisonSociale = :rs
      AND dateRemise BETWEEN :debut AND :fin
    GROUP BY numRemise,
             dateRemise,
             c.siren;",
    // Requête pour la récupération des données des remises du client selon sa raison sociale et deux dates

    "select_po_remise_date" => "
    SELECT c.siren,
           raisonSociale,
           numRemise,
           dateRemise,
           (SELECT COUNT(*)
            FROM REMISE r2
            WHERE r2.numRemise = r.numRemise) AS transactions,
            devise,
            (SELECT SUM(r2.montant)
             FROM REMISE r2
             WHERE r2.numRemise = r.numRemise) AS montant
    FROM REMISE r,
         CLIENT c
    WHERE r.siren = c.siren
      AND dateRemise BETWEEN :debut AND :fin
    GROUP BY numRemise,
             dateRemise,
             c.siren;",
    // Requête pour la récupération des données des remises de tous les clients selon deux dates

    "select_po_impaye" => "
    SELECT c.siren,
           raisonSociale,
           devise,
           (SELECT SUM(r2.montant)
             FROM REMISE r2
             WHERE r2.montant < 0 AND r2.siren = c.siren) AS montantTotal
    FROM REMISE r,
         CLIENT c
    WHERE r.siren = c.siren
      AND r.montant < 0
    GROUP BY c.siren;",
    // Requête pour la récupération des données des impayés de tous les clients

    "select_po_impaye_date" => "
    SELECT c.siren,
           raisonSociale,
           numDossierImpaye,
           dateTransaction,
           dateRemise,
           numCarte,
           reseau,
           devise,
           montant,
           libelle
    FROM REMISE r,
         CLIENT c,
         MOTIFS_IMPAYES m
    WHERE r.siren = c.siren
      AND m.code = r.code
      AND r.montant < 0
      AND dateRemise BETWEEN :debut AND :fin
    GROUP BY numDossierImpaye,
            dateTransaction,
            dateRemise,
            montant,
            libelle,
            c.siren;",
    // Requête pour la récupération des données des impayés de tous les clients selon deux dates

    "select_po_impaye_siren_dossier" => "
    SELECT c.siren,
           raisonSociale,
           numDossierImpaye,
           dateTransaction,
           dateRemise,
           numCarte,
           reseau,
           devise,
           montant,
           libelle
    FROM REMISE r,
         CLIENT c,
         MOTIFS_IMPAYES m
    WHERE r.siren = c.siren
      AND m.code = r.code
      AND r.montant < 0
      AND c.siren = :siren
      AND numDossierImpaye = :numDossierImpaye
    GROUP BY numDossierImpaye,
             dateTransaction,
             dateRemise,
             montant,
             libelle,
             c.siren;",
    // Requête pour la récupération des données d'un impayé du client selon son siren et le numéro du dossier

    "select_po_impaye_siren_date" => "
    SELECT c.siren,
           raisonSociale,
           numDossierImpaye,
           dateTransaction,
           dateRemise,
           numCarte,
           reseau,
           devise,
           montant,
           libelle
    FROM REMISE r,
        CLIENT c,
        MOTIFS_IMPAYES m
    WHERE r.siren = c.siren
      AND m.code = r.code
      AND r.montant < 0
      AND c.siren = :siren
      AND dateRemise BETWEEN :debut AND :fin
    GROUP BY numDossierImpaye,
             dateTransaction,
             dateRemise,
             montant,
             libelle,
             c.siren;",
    // Requête pour la récupération des données des impayés du client selon son siren et deux dates

    "select_po_impaye_siren" => "
    SELECT c.siren,
           raisonSociale,
           numDossierImpaye,
           dateTransaction,
           dateRemise,
           numCarte,
           reseau,
           devise,
           montant,
           libelle
    FROM REMISE r,
         CLIENT c,
         MOTIFS_IMPAYES m
    WHERE r.siren = c.siren
      AND m.code = r.code
      AND r.montant < 0
      AND c.siren = :siren
    GROUP BY numDossierImpaye,
             dateTransaction,
             dateRemise,
             montant,
             libelle,
             c.siren;",
    // Requête pour la récupération des données des impayés du client selon son siren

    "select_po_impaye_rs_dossier" => "
    SELECT c.siren,
           raisonSociale,
           numDossierImpaye,
           dateTransaction,
           dateRemise,
           numCarte,
           reseau,
           devise,
           montant,
           libelle
    FROM REMISE r,
         CLIENT c,
         MOTIFS_IMPAYES m
    WHERE r.siren = c.siren
      AND m.code = r.code
      AND r.montant < 0
      AND raisonSociale = :rs
      AND numDossierImpaye = :numDossierImpaye
    GROUP BY numDossierImpaye,
             dateTransaction,
             dateRemise,
             montant,
             libelle,
             c.siren;",
    // Requête pour la récupération des données d'un impayé du client selon sa raison sociale et le numéro du dossier

    "select_po_impaye_rs_date" => "
    SELECT c.siren,
           raisonSociale,
           numDossierImpaye,
           dateTransaction,
           dateRemise,
           numCarte,
           reseau,
           devise,
           montant,
           libelle
    FROM REMISE r,
        CLIENT c,
        MOTIFS_IMPAYES m
    WHERE r.siren = c.siren
      AND m.code = r.code
      AND r.montant < 0
      AND raisonSociale = :rs
      AND dateRemise BETWEEN :debut AND :fin
    GROUP BY numDossierImpaye,
             dateTransaction,
             dateRemise,
             montant,
             libelle,
             c.siren;",
    // Requête pour la récupération des données des impayés du client selon sa raison sociale et deux dates

    "select_po_impaye_rs" => "
    SELECT c.siren,
           raisonSociale,
           numDossierImpaye,
           dateTransaction,
           dateRemise,
           numCarte,
           reseau,
           devise,
           montant,
           libelle
    FROM REMISE r,
        CLIENT c,
        MOTIFS_IMPAYES m
    WHERE r.siren = c.siren
      AND m.code = r.code
      AND r.montant < 0
      AND raisonSociale = :rs
    GROUP BY numDossierImpaye,
             dateTransaction,
             dateRemise,
             montant,
             libelle,
             c.siren;",
    // Requête pour la récupération des données des impayés du client selon sa raison sociale

    "select_po_impaye_dossier" => "
    SELECT c.siren,
           raisonSociale,
           numDossierImpaye,
           dateTransaction,
           dateRemise,
           numCarte,
           reseau,
           devise,
           montant,
           libelle
    FROM REMISE r,
        CLIENT c,
        MOTIFS_IMPAYES m
    WHERE r.siren = c.siren
      AND m.code = r.code
      AND r.montant < 0
      AND numDossierImpaye = :numDossierImpaye
    GROUP BY numDossierImpaye,
             dateTransaction,
             dateRemise,
             montant,
             libelle,
             c.siren;",
    // Requête pour la récupération des données d'un impayé du client selon le numéro du dossier

    "select_numRemise" => "
    SELECT siren
    FROM REMISE
    WHERE numRemise = :numRemise;",
    // Requête pour la récupération du siren d'une remise selon son numéro

    "select_clients_impayes" => "
    SELECT c.siren,
           raisonSociale,
           dateTransaction,
           dateRemise,
           numCarte,
           reseau,
           numDossierImpaye,
           devise,
           montant,
           libelle
    FROM CLIENT c,
         REMISE r,
         MOTIFS_IMPAYES m
    WHERE c.siren = r.siren
      AND m.code = r.code
      AND r.montant < 0
      AND c.siren = :siren;"
    // Requête pour la récupération des données des impayés d'un client en détail
);
