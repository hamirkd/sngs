CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_etat_rapport_stock` AS  

(SELECT tsa.`id_sort_art` AS id,
'SORTIE' as type,
tsa.`art_sort_art` AS id_article,
tsa.`qte_sort_art` AS quantite,
CAST(`tsa`.`date_enr` AS DATE) AS `periode_enr`,
CAST(`ts`.`date_sort` AS DATE) AS `periode`,
ts.bon_sort AS numero_bon,
ts.mag_sort_src AS id_magasin_source,
ts.mag_sort_dst AS id_magasin_destinataire,
ms.nom_mag as magasin_source,
md.nom_mag as magasin_destinataire,
ta.nom_art as article,
tca.nom_cat as categorie

FROM `t_sortie_article` tsa
JOIN t_sortie ts on tsa.sort_sort_art=ts.id_sort
JOIN `t_magasin` ms ON ms.id_mag = ts.mag_sort_src
JOIN `t_magasin` md ON md.id_mag = ts.mag_sort_dst
JOIN `t_article` ta ON ta.id_art = tsa.art_sort_art
JOIN `t_categorie_article` tca ON tca.id_cat = ta.cat_art)

UNION

(SELECT taa.`id_appro_art` as id,
'APPROVISIONNEMENT' as type,
taa.art_appro_art as id_article,
taa.qte_appro_art as quantite,
CAST(`taa`.`date_enr` AS DATE) AS `periode_enr`,
CAST(`ta`.`date_appro` AS DATE) AS `periode`,
ta.bon_liv_appro AS numero_bon,
ts.mag_sort_src AS id_magasin_source,
taa.mag_appro_art AS id_magasin_destinataire,
ms.nom_mag as magasin_source,
md.nom_mag as magasin_destinataire,
tar.nom_art as article,
tca.nom_cat as categorie

FROM `t_approvisionnement_article` taa
JOIN t_approvisionnement ta ON ta.id_appro=taa.appro_appro_art
JOIN `t_magasin` md ON md.id_mag = taa.mag_appro_art
JOIN `t_article` tar ON tar.id_art = taa.art_appro_art
JOIN `t_categorie_article` tca ON tca.id_cat = tar.cat_art
LEFT JOIN t_sortie ts on ts.bon_sort=ta.bon_liv_appro
LEFT JOIN `t_magasin` ms ON ms.id_mag = ts.mag_sort_src)



SELECT `id_appro`, `bon_liv_appro`, `frns_appro`, `dette_appro`, `som_verse_dette`, 
`tva_appro`, `remise_achat_appro`, `remise_dette_appro`, `mnt_reel_achat_appro`, 
`mnt_avt_rmis_dette_appro`, `mnt_ttl_dette_appro`, `mnt_revient_appro`, `bl_bon_dette`, 
`bl_dette_regle`, `actif`, `date_appro`, `user_appro`, `login_appro`, `code_user_appro`, 
`date_enr` FROM `t_approvisionnement` WHERE 1

SELECT `id_appro_art`, `appro_appro_art`, `mag_appro_art`, `art_appro_art`, `qte_appro_art`, 
`prix_appro_art`, `mnt_appro_art`, `date_appro_art`, `login_appro_art`, `user_appro_art`, 
`code_user_appro_art`, `date_enr` FROM `t_approvisionnement_article` WHERE 1

SELECT `id_mag`, `nom_mag`, `code_mag`, `act_mag`, `type_mag`, `pays_mag`, `ville_mag`, `tel_mag`, `mob_mag`, `fax_mag`, `mail_mag`, `date_crea_mag`, `curent_bs`, `resp_mag`, `titre_resp_mag`, `resa_mag` FROM `t_magasin` WHERE 1
SELECT `id_art`, `code_art`, `nom_art`, `ref_art`, `seuil_art`, `marq_art`, `model_art`, `cat_art`, `unite_art`, `caract_art` FROM `t_article` WHERE 1
SELECT `id_cat`, `nom_cat`, `code_cat`, `activite` FROM `t_categorie_article` WHERE 1

SELECT `id_sort`, `bon_sort`, `mag_sort_src`, `mag_sort_dst`, `actif`, `date_sort`, `vu`, `bon_vu`, `rejeter`, `user_sort`, `login_sort`, `code_user_sort`, `date_enr`, `motif` FROM `t_sortie` ts, 