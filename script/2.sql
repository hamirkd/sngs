CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_etat_rapport` AS  

(
    SELECT CAST(`cr`.`date_crce_clnt` AS DATE) AS `periode`, 'reglement' AS type_text, `cr`.`mnt_paye_crce_clnt` AS montant, 
           cr.id_mag AS magasin, m.nom_mag AS nom_magasin , cr.id_crce_clnt as id
    FROM `t_creance_client` `cr`
    JOIN `t_magasin` m ON m.id_mag = cr.id_mag
    WHERE `cr`.`date_crce_clnt` <> '0000-00-00'
)

UNION 

(
    SELECT CAST(`f`.`date_fact` AS DATE) AS `periode`, 'comptant' AS type_text, `f`.`crdt_fact` AS `montant`, 
           f.mag_fact AS magasin, m.nom_mag AS nom_magasin , f.id_fact
    FROM `t_facture_vente` `f` 
    JOIN `t_magasin` m ON m.id_mag = f.mag_fact
    WHERE `f`.`bl_fact_crdt`=0 AND `f`.`sup_fact`=0 AND `f`.`date_fact` <> '0000-00-00'
)

UNION 

(
    SELECT CAST(`f`.`date_fact` AS DATE) AS `periode`, 'credit' AS type_text, `f`.`crdt_fact` AS `montant`, 
           f.mag_fact AS magasin, m.nom_mag AS nom_magasin , f.id_fact
    FROM `t_facture_vente` `f` 
    JOIN `t_magasin` m ON m.id_mag = f.mag_fact
    WHERE `f`.`bl_fact_crdt`=1 AND `f`.`sup_fact`=0 AND `f`.`date_fact` <> '0000-00-00'
)

UNION 

(
    SELECT CAST(`d`.`date_dep` AS DATE) AS `periode`, 'depense' AS type_text, `d`.`mnt_dep` AS `montant`, 
           d.mag_depense_id AS magasin, m.nom_mag AS nom_magasin , d.id_dep as id
    FROM `t_depense` `d`
    JOIN `t_magasin` m ON m.id_mag = d.mag_depense_id
    WHERE `d`.`date_dep` <> '0000-00-00'
)

UNION 

(
    SELECT CAST(`d`.`date_demande` AS DATE) AS `periode`, 'demande' AS type_text, `d`.`montant` AS `montant`, 
           `d`.`mag_demandeur` AS `magasin`, m.nom_mag AS nom_magasin, id_dem as id 
    FROM `t_demande` `d`
    JOIN `t_magasin` m ON m.id_mag = d.mag_demandeur
    WHERE `d`.`date_demande` <> '0000-00-00'
)

UNION 

(
    SELECT CAST(`v`.`date_vrsmnt` AS DATE) AS `periode`, 'versement' AS type_text, `v`.`mnt_vrsmnt` AS `montant`, 
           v.id_mag AS magasin, m.nom_mag AS nom_magasin, v.id_vrsmnt as id 
    FROM `t_versement` `v`
    JOIN `t_magasin` m ON m.id_mag = v.id_mag
    WHERE `v`.`date_vrsmnt` <> '0000-00-00'
);

ALTER TABLE `t_creance_client` ADD `id_mag` INT(11) NULL AFTER `date_enr`; 
update t_creance_client set id_mag = (select t_user.mag_user from t_user where t_user.code_user=t_creance_client.code_caissier_crce);

ALTER TABLE `t_caisse` ADD `id_mag` INT(11) NULL AFTER `date_enr`; 
UPDATE `t_caisse` SET `id_mag`= (select t_user.mag_user from t_user where t_user.code_user = t_caisse.code_caissier_cais);

