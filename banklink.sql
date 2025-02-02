-- phpMyAdmin SQL Dump
-- Version de PHP :  7.0.33-0+deb9u12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


-- --------------------------------------------------------

--
-- Structure de la table `CLIENT`
--

CREATE TABLE `CLIENT` (
  `siren` char(9) NOT NULL,
  `raisonSociale` char(50) NOT NULL,
  `devise` char(3) NOT NULL,
  `numCarte` char(16) NOT NULL,
  `reseau` char(2) NOT NULL,
  `suppr` tinyint(1) NOT NULL,
  `idUser` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `CLIENT`
--

INSERT INTO `CLIENT` (`siren`, `raisonSociale`, `devise`, `numCarte`, `reseau`, `suppr`, `idUser`) VALUES
('012345678', 'Test3', 'USD', '0147852369014725', 'MC', 2, 13),
('123456789', 'Test1', 'EUR', '0123456789123456', 'CB', 2, 9),
('200520041', 'Abibas & Co.', 'EUR', '5583626447242567', 'MC', 2, 11),
('320367139', 'Nike France', 'USD', '4353576437900016', 'MC', 0, 8),
('322120916', 'Apple Computer, Inc.', 'USD', '4510780852594615', 'CB', 0, 3),
('327733184', 'Microsoft France', 'EUR', '3698521470369852', 'MC', 0, 14),
('383474814', 'Airbus Group N.V.', 'EUR', '4971199462092567', 'MC', 0, 5),
('542051180', 'TotalEnergies', 'EUR', '4979096530796165', 'VS', 2, 4),
('542065479', 'Stellantis Auto SAS', 'EUR', '5420654790092692', 'VS', 0, 6),
('818092132', 'Boeing Defence UK Ltd.', 'BGP', '340205970510339', 'CB', 2, 12),
('987654321', 'Test2', 'EUR', '9876543210123456', 'CB', 2, 10);

-- --------------------------------------------------------

--
-- Structure de la table `CLIENT_TEMP`
--

CREATE TABLE `CLIENT_TEMP` (
  `siren` char(9) NOT NULL,
  `raisonSociale` char(50) NOT NULL,
  `login` varchar(255) NOT NULL,
  `devise` char(3) NOT NULL,
  `numCarte` char(16) NOT NULL,
  `reseau` char(2) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `MOTIFS_IMPAYES`
--

CREATE TABLE `MOTIFS_IMPAYES` (
  `code` int(11) NOT NULL,
  `libelle` varchar(255) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `MOTIFS_IMPAYES`
--

INSERT INTO `MOTIFS_IMPAYES` (`code`, `libelle`) VALUES
(2, 'compte à découvert'),
(4, 'compte bloqué'),
(3, 'compte clôturé'),
(1, 'fraude à la carte'),
(6, 'opération contestée par le débiteur'),
(5, 'provision insuffisante'),
(8, 'raison non communiquée, contactez la banque du client'),
(7, 'titulaire décédé');

-- --------------------------------------------------------

--
-- Structure de la table `REMISE`
--

CREATE TABLE `REMISE` (
  `idRemise` int(11) NOT NULL,
  `numRemise` varchar(50) NOT NULL,
  `numTransaction` varchar(50) NOT NULL,
  `montant` float(5,2) NOT NULL,
  `dateRemise` datetime NOT NULL,
  `dateTransaction` datetime NOT NULL,
  `n_autorisation` char(6) NOT NULL,
  `numDossierImpaye` char(5) DEFAULT NULL,
  `siren` char(9) NOT NULL,
  `code` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `REMISE`
--

INSERT INTO `REMISE` (`idRemise`, `numRemise`, `numTransaction`, `montant`, `dateRemise`, `dateTransaction`, `n_autorisation`, `numDossierImpaye`, `siren`, `code`) VALUES
(1, '1', '1', 243.13, '2023-11-05 14:14:13', '2023-11-02 13:11:00', '000001', NULL, '322120916', NULL),
(2, '1', '2', 122.75, '2023-11-05 14:14:13', '2023-11-02 16:46:00', '000002', NULL, '322120916', NULL),
(3, '2', '3', -229.84, '2023-11-08 00:00:00', '2023-11-02 00:00:00', '000003', '00001', '322120916', 5),
(4, '3', '4', -125.97, '2023-11-08 00:00:00', '2023-11-03 00:00:00', '000004', '00002', '322120916', 1),
(5, '4', '5', -150.26, '2023-11-20 00:00:00', '2023-11-15 00:00:00', '000005', '00003', '383474814', 1),
(6, '4', '6', -659.17, '2023-11-20 00:00:00', '2023-11-16 00:00:00', '000006', '00004', '383474814', 3),
(7, '4', '7', -25.04, '2023-11-20 00:00:00', '2023-11-17 00:00:00', '000007', '00005', '383474814', 6),
(8, '4', '8', -176.41, '2023-11-20 00:00:00', '2023-11-18 00:00:00', '000008', '00006', '383474814', 8),
(9, '4', '9', -343.00, '2023-11-20 00:00:00', '2023-11-19 00:00:00', '000009', '00007', '383474814', 3),
(10, '5', '10', -847.41, '2023-10-20 00:00:00', '2023-10-20 00:00:00', '000010', '00008', '542065479', 2),
(11, '6', '11', -530.27, '2023-10-21 00:00:00', '2023-10-21 00:00:00', '000011', '00009', '542065479', 2),
(12, '7', '12', -692.55, '2023-10-22 00:00:00', '2023-10-22 00:00:00', '000012', '00010', '542065479', 4),
(13, '8', '13', -928.19, '2023-10-23 00:00:00', '2023-10-23 00:00:00', '000013', '00011', '542065479', 4),
(14, '9', '14', -888.12, '2023-10-24 00:00:00', '2023-10-24 00:00:00', '000014', '00012', '542065479', 4),
(15, '10', '15', 120.92, '2023-12-28 00:00:00', '2023-11-08 00:00:00', '000015', NULL, '320367139', NULL),
(16, '10', '16', 779.85, '2023-12-28 00:00:00', '2023-11-16 00:00:00', '000016', NULL, '320367139', NULL),
(17, '10', '17', 826.99, '2023-12-28 00:00:00', '2023-11-20 00:00:00', '000017', NULL, '320367139', NULL),
(18, '10', '18', 360.91, '2023-12-28 00:00:00', '2023-11-22 00:00:00', '000018', NULL, '320367139', NULL),
(19, '10', '19', 88.04, '2023-12-28 00:00:00', '2023-11-27 00:00:00', '000019', NULL, '320367139', NULL),
(20, '10', '20', 186.30, '2023-12-28 00:00:00', '2023-12-01 00:00:00', '000020', NULL, '320367139', NULL),
(21, '10', '21', 103.14, '2023-12-28 00:00:00', '2023-12-14 00:00:00', '000021', NULL, '320367139', NULL),
(22, '10', '22', 899.81, '2023-12-28 00:00:00', '2023-12-15 00:00:00', '000022', NULL, '320367139', NULL),
(23, '10', '23', 275.33, '2023-12-28 00:00:00', '2023-12-25 00:00:00', '000023', NULL, '320367139', NULL),
(24, '10', '24', 529.70, '2023-12-28 00:00:00', '2023-12-28 00:00:00', '000024', NULL, '320367139', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `UTILISATEUR`
--

CREATE TABLE `UTILISATEUR` (
  `idUser` int(11) NOT NULL,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `mail` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `UTILISATEUR`
--

INSERT INTO `UTILISATEUR` (`idUser`, `login`, `password`, `type`, `mail`) VALUES
(1, 'admin', '$2y$10$jNo9AqVUp8c3bJ1sfBv4Ju8JIL./eqvnjslQH3HwWTFkdxsMz/OcC', 'admin', 'admin@banklink.fr'),
(2, 'productowner', '$2y$10$PUWV888zCdMYpk.S0Gsd0uKZkf6Y6yAMEhIHxqtZZMDoF3Meyq5YK', 'product-owner', 'product.owner@banklink.fr'),
(3, 'apple', '$2y$10$cMZnvLHxaDm2WYor7vmWzuO9jgDMpdhJj6j7.Y1XB5ZdU.vd94UW.', 'client', 'cedric.mc11@gmail.com'),
(4, 'total', 'product', 'client', 'cedric.mc11@gmail.com'),
(5, 'airbus', '$2y$10$DxcGTQ3kUThT0bNYGY7CNe4exG.W5m5n4WzvvmXePpwmicvOopROG', 'client', 'cedric.mc11@gmail.com'),
(6, 'stellantis', '$2y$10$LvKOP.iJROq7vzK8t6VE7Ott/CvSmI0WGLsOOGxoKvX7nHKgQLxca', 'client', 'thamiz.sarboudine@edu.univ-eiffel.fr'),
(8, 'nike', '$2y$10$Rt0xD/tCL77C6Pp4/k97XeNuJidkjmaQ7Emitb6kEm8k9GtDiVrqe', 'client', 'cedric.mc11@gmail.com'),
(9, 'test1', 'product', 'client', 'cedric.mc11@gmail.com'),
(10, 'test2', 'product', 'client', 'cedric.mc11@gmail.com'),
(11, 'abibas', 'product', 'client', 'thamizsarb@gmail.com'),
(12, 'boeing', 'product', 'client', 'cedric.mc11@gmail.com'),
(13, 'test3', 'product', 'client', 'cedric.mc11@gmail.com'),
(14, 'microsoft', '$2y$10$zqLXwngWrZBbSdaDCVEcGuLkacw1YUqyWzcsmGitcoaHoCgtjytQa', 'client', 'cedric.mc11@gmail.com');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `CLIENT`
--
ALTER TABLE `CLIENT`
  ADD PRIMARY KEY (`siren`),
  ADD UNIQUE KEY `idUser` (`idUser`),
  ADD UNIQUE KEY `raisonSociale` (`raisonSociale`),
  ADD UNIQUE KEY `numCarte` (`numCarte`);

--
-- Index pour la table `CLIENT_TEMP`
--
ALTER TABLE `CLIENT_TEMP`
  ADD PRIMARY KEY (`siren`),
  ADD UNIQUE KEY `raisonSociale` (`raisonSociale`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `numCarte` (`numCarte`);

--
-- Index pour la table `MOTIFS_IMPAYES`
--
ALTER TABLE `MOTIFS_IMPAYES`
  ADD PRIMARY KEY (`code`),
  ADD UNIQUE KEY `libelle` (`libelle`);

--
-- Index pour la table `REMISE`
--
ALTER TABLE `REMISE`
  ADD PRIMARY KEY (`idRemise`),
  ADD UNIQUE KEY `numTransaction` (`numTransaction`),
  ADD UNIQUE KEY `numDossierImpaye` (`numDossierImpaye`),
  ADD KEY `siren` (`siren`),
  ADD KEY `code` (`code`);

--
-- Index pour la table `UTILISATEUR`
--
ALTER TABLE `UTILISATEUR`
  ADD PRIMARY KEY (`idUser`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `MOTIFS_IMPAYES`
--
ALTER TABLE `MOTIFS_IMPAYES`
  MODIFY `code` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT pour la table `REMISE`
--
ALTER TABLE `REMISE`
  MODIFY `idRemise` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT pour la table `UTILISATEUR`
--
ALTER TABLE `UTILISATEUR`
  MODIFY `idUser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `CLIENT`
--
ALTER TABLE `CLIENT`
  ADD CONSTRAINT `CLIENT_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `UTILISATEUR` (`idUser`);

--
-- Contraintes pour la table `REMISE`
--
ALTER TABLE `REMISE`
  ADD CONSTRAINT `REMISE_ibfk_1` FOREIGN KEY (`siren`) REFERENCES `CLIENT` (`siren`),
  ADD CONSTRAINT `REMISE_ibfk_2` FOREIGN KEY (`code`) REFERENCES `MOTIFS_IMPAYES` (`code`);
