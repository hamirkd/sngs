-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 30 déc. 2024 à 19:04
-- Version du serveur :  5.7.31
-- Version de PHP : 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `bd_songo_sgns`
--

-- --------------------------------------------------------

--
-- Structure de la vue `v_etat_rapport`
--

DROP VIEW IF EXISTS `v_etat_rapport`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_etat_rapport`  AS  (select cast(`cr`.`date_crce_clnt` as date) AS `periode`,'reglement' AS `type_text`,`cr`.`mnt_paye_crce_clnt` AS `montant`,`cr`.`id_mag` AS `magasin`,`m`.`nom_mag` AS `nom_magasin`,`cr`.`id_crce_clnt` AS `id` from (`t_creance_client` `cr` join `t_magasin` `m` on((`m`.`id_mag` = `cr`.`id_mag`))) where (`cr`.`date_crce_clnt` <> '0000-00-00')) union (select cast(`f`.`date_fact` as date) AS `periode`,'comptant' AS `type_text`,`f`.`crdt_fact` AS `montant`,`f`.`mag_fact` AS `magasin`,`m`.`nom_mag` AS `nom_magasin`,`f`.`id_fact` AS `id_fact` from (`t_facture_vente` `f` join `t_magasin` `m` on((`m`.`id_mag` = `f`.`mag_fact`))) where ((`f`.`bl_fact_crdt` = 0) and (`f`.`sup_fact` = 0) and (`f`.`date_fact` <> '0000-00-00'))) union (select cast(`f`.`date_fact` as date) AS `periode`,'credit' AS `type_text`,`f`.`crdt_fact` AS `montant`,`f`.`mag_fact` AS `magasin`,`m`.`nom_mag` AS `nom_magasin`,`f`.`id_fact` AS `id_fact` from (`t_facture_vente` `f` join `t_magasin` `m` on((`m`.`id_mag` = `f`.`mag_fact`))) where ((`f`.`bl_fact_crdt` = 1) and (`f`.`sup_fact` = 0) and (`f`.`date_fact` <> '0000-00-00'))) union (select cast(`d`.`date_dep` as date) AS `periode`,'depense' AS `type_text`,`d`.`mnt_dep` AS `montant`,`d`.`mag_depense_id` AS `magasin`,`m`.`nom_mag` AS `nom_magasin`,`d`.`id_dep` AS `id` from (`t_depense` `d` join `t_magasin` `m` on((`m`.`id_mag` = `d`.`mag_depense_id`))) where (`d`.`date_dep` <> '0000-00-00')) union (select cast(`d`.`date_demande` as date) AS `periode`,'demande' AS `type_text`,`d`.`montant` AS `montant`,`d`.`mag_demandeur` AS `magasin`,`m`.`nom_mag` AS `nom_magasin`,`d`.`id_dem` AS `id` from (`t_demande` `d` join `t_magasin` `m` on((`m`.`id_mag` = `d`.`mag_demandeur`))) where (`d`.`date_demande` <> '0000-00-00')) union (select cast(`v`.`date_vrsmnt` as date) AS `periode`,'versement' AS `type_text`,`v`.`mnt_vrsmnt` AS `montant`,`v`.`id_mag` AS `magasin`,`m`.`nom_mag` AS `nom_magasin`,`v`.`id_vrsmnt` AS `id` from (`t_versement` `v` join `t_magasin` `m` on((`m`.`id_mag` = `v`.`id_mag`))) where (`v`.`date_vrsmnt` <> '0000-00-00')) ;

--
-- VIEW `v_etat_rapport`
-- Données : Aucun(e)
--

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
