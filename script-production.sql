
CREATE TABLE `t_production` (
  `id_prod` int(11) NOT NULL,
  `bon_prod` varchar(255) DEFAULT NULL COMMENT 'la référence du bon de production',
  `prod_mag_src` int(11) DEFAULT NULL COMMENT 'le magasin qui a produit ce bon',
  `actif` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'si le bon a ete validé ou pas',
  `date_prod` date DEFAULT NULL COMMENT 'la date de production du bon',
  `user_prod` int(11) DEFAULT NULL COMMENT 'l identifiant de l utilisateur qui a créé le bon',
  `login_prod` varchar(50) DEFAULT NULL COMMENT 'le login associé',
  `code_user_prod` varchar(50) DEFAULT NULL COMMENT 'le code de l utilisateur qui a créé le bon',
  `code_user_confirm` varchar(20) DEFAULT NULL COMMENT 'le code de l utilisateur qui a confirmé le bon',
  `date_confirm` datetime DEFAULT NULL COMMENT 'la date de confirmation du bon',
  `code_user_rejet` varchar(20) DEFAULT NULL COMMENT 'le code de l utilisateur qui a rejeté le bon',
  `date_rejet_prod` date DEFAULT NULL COMMENT 'la date de rejet de la production',
  `date_enr` datetime DEFAULT current_timestamp(),
  `motif_rejet` varchar(50) DEFAULT NULL COMMENT 'le motif du rejet de la production',
  `prod_art` int(11) DEFAULT NULL COMMENT 'l identifiant de l article qui a été produit le bon',
  `qte` int(11) DEFAULT NULL COMMENT 'la quantité de l article produit',
  `nom_art` varchar(50) DEFAULT NULL COMMENT 'le nom de l article',
  `cout_achat` double DEFAULT 0 COMMENT 'cout d achat total',
  `charge_prod` double DEFAULT 0 COMMENT 'charge de production',
  `cout_prod_total` double DEFAULT 0 COMMENT 'cout de production'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `t_production_article`
--

CREATE TABLE `t_production_article` (
  `id_prod_art` int(11) NOT NULL,
  `prod_prod_art` int(11) DEFAULT NULL,
  `art_prod_art` int(11) DEFAULT NULL,
  `qte_prod_art` int(11) DEFAULT 0,
  `prod_mag_src` int(11) DEFAULT NULL COMMENT 'le magasin qui a utilisé l article',

  `code_user_confirm` varchar(20) DEFAULT NULL COMMENT 'le code de l utilisateur qui a confirmé le bon',
  `date_confirm` datetime DEFAULT NULL COMMENT 'la date de confirmation du bon',
  
  `user_prod_art` int(11) DEFAULT NULL,
  `code_user_prod_art` varchar(20) DEFAULT NULL,
  `cout_prod` double DEFAULT 0 COMMENT 'cout de production',
  `date_enr` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `t_production`
--
ALTER TABLE `t_production`
  ADD PRIMARY KEY (`id_prod`),
  ADD UNIQUE KEY `bon_prod` (`bon_prod`),
  ADD KEY `user_prod` (`user_prod`),
  ADD KEY `prod_mag_src` (`prod_mag_src`);


--
-- Index pour la table `t_production_article`
--
ALTER TABLE `t_production_article`
  ADD PRIMARY KEY (`id_prod_art`),
  ADD KEY `prod_prod_art` (`prod_prod_art`),
  ADD KEY `art_prod_art` (`art_prod_art`),
  ADD KEY `user_prod_art` (`user_prod_art`),
  ADD KEY `prod_mag_src` (`prod_mag_src`);
--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `t_production`
--
ALTER TABLE `t_production`
  MODIFY `id_prod` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `t_production`
--
ALTER TABLE `t_production_article`
  MODIFY `id_prod_art` int(11) NOT NULL AUTO_INCREMENT;


--
-- AUTO_INCREMENT pour la table `t_sortie_article`
--
ALTER TABLE `t_sortie_article`
  MODIFY `id_sort_art` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `t_production`
--
ALTER TABLE `t_production`
  ADD CONSTRAINT `t_production_ibfk_1` FOREIGN KEY (`prod_mag_src`) REFERENCES `t_magasin` (`id_mag`) ON UPDATE CASCADE,
  ADD CONSTRAINT `t_production_ibfk_3` FOREIGN KEY (`user_prod`) REFERENCES `t_user` (`id_user`) ON UPDATE CASCADE;


--
-- Contraintes pour la table `t_production_article`
--
ALTER TABLE `t_production_article`
  ADD CONSTRAINT `t_production_article_ibfk_1` FOREIGN KEY (`prod_mag_src`) REFERENCES `t_magasin` (`id_mag`) ON UPDATE CASCADE,
  ADD CONSTRAINT `t_production_article_ibfk_3` FOREIGN KEY (`user_prod_art`) REFERENCES `t_user` (`id_user`) ON UPDATE CASCADE;





ALTER TABLE `t_user` ADD `droit_confirmation_production` INT NOT NULL DEFAULT '0' AFTER `droit_confirmation_approvisionnement`;

ALTER TABLE `t_production` ADD `cout_achat` INT NOT NULL DEFAULT '0' AFTER `cout_prod_total`, ADD `charge_prod` INT NOT NULL DEFAULT '0' AFTER `cout_achat`;

