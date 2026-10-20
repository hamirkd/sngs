select a.id_appro,a.bon_liv_appro,a.date_appro,m.code_mag, m.nom_mag,ta.nom_art,aa.qte_appro_art from t_approvisionnement_article aa left join t_approvisionnement a on aa.appro_appro_art=a.id_appro
left join t_magasin m on m.id_mag=aa.mag_appro_art left join t_article ta on aa.art_appro_art=ta.id_art
where aa.date_appro_art BETWEEN '2026-01-01' AND '2026-10-10';


# Requete pour recupérer tous les bons d''approvisionnement du magasin de Ponsomtenga
select distinct a.id_appro,a.bon_liv_appro,a.date_appro,m.code_mag, m.nom_mag,ta.id_art from t_approvisionnement_article aa left join t_approvisionnement a on aa.appro_appro_art=a.id_appro
left join t_magasin m on m.id_mag=aa.mag_appro_art left join t_article ta on aa.art_appro_art=ta.id_art
where m.code_mag='MAG21';



select aa.art_appro_art from t_approvisionnement_article aa left join t_approvisionnement a on aa.appro_appro_art=a.id_appro
left join t_magasin m on m.id_mag=aa.mag_appro_art
where m.code_mag='MAG21' AND aa.date_appro_art BETWEEN '2026-01-01' AND '2026-10-10' group by aa.art_appro_art;


/* Requete pour recupérer tous les bons d'approvisionnement des articles approvisionnés dans d'autres magasins que MAG21 mais qui sont aussi approvisionnés dans MAG21 */
select a.id_appro,a.bon_liv_appro,a.date_appro,m.code_mag, m.nom_mag,ta.nom_art,aa.qte_appro_art from t_approvisionnement_article aa left join t_approvisionnement a on aa.appro_appro_art=a.id_appro
left join t_magasin m on m.id_mag=aa.mag_appro_art left join t_article ta on aa.art_appro_art=ta.id_art
where aa.date_appro_art BETWEEN '2026-01-01' AND '2026-10-10' AND m.code_mag<>'MAG21' AND aa.art_appro_art IN (
select aa.art_appro_art from t_approvisionnement_article aa left join t_approvisionnement a on aa.appro_appro_art=a.id_appro
left join t_magasin m on m.id_mag=aa.mag_appro_art
where m.code_mag='MAG21' AND aa.date_appro_art BETWEEN '2026-01-01' AND '2026-10-10' group by aa.art_appro_art
);



select aa.art_appro_art from t_approvisionnement_article aa left join t_approvisionnement a on aa.appro_appro_art=a.id_appro
left join t_magasin m on m.id_mag=aa.mag_appro_art
where m.code_mag='MAG21' AND aa.date_appro_art BETWEEN '2026-01-01' AND '2026-10-10' group by aa.art_appro_art;


SELECT ts.date_stk,m.nom_mag, a.nom_art, ts.qte_stk FROM `t_stock` ts left join t_magasin m on m.id_mag=ts.mag_stk left join t_article a on a.id_art=ts.art_stk WHERE  ts.art_stk IN (
select aa.art_appro_art from t_approvisionnement_article aa left join t_approvisionnement a on aa.appro_appro_art=a.id_appro
left join t_magasin m on m.id_mag=aa.mag_appro_art
where m.code_mag='MAG21' AND aa.date_appro_art BETWEEN '2026-01-01' AND '2026-10-10' group by aa.art_appro_art
) AND ts.date_stk  BETWEEN '2026-01-01' AND '2026-10-10';



SELECT
    a.id_appro,
    a.bon_liv_appro,
    date(aa.date_appro_art) as date_appro,
    aa.art_appro_art AS id_art,
    ta.nom_art,
    aa.qte_appro_art AS qte_recue,
    ts.qte_stk AS qte_stock_mag21
FROM t_approvisionnement_article aa

JOIN t_approvisionnement a
    ON a.id_appro = aa.appro_appro_art

JOIN t_magasin m
    ON m.id_mag = aa.mag_appro_art

JOIN t_article ta
    ON ta.id_art = aa.art_appro_art

LEFT JOIN t_stock ts
    ON ts.art_stk = aa.art_appro_art
    AND ts.mag_stk = aa.mag_appro_art

WHERE m.code_mag = 'MAG21' AND a.bon_liv_appro like 'arriva%'
--AND (a.bon_liv_appro like '%TKL%' OR a.bon_liv_appro like '%poumon%' OR a.bon_liv_appro like '%olipes%'
--    OR a.bon_liv_appro like '%mega%' OR a.bon_liv_appro like '%Bywell%' OR a.bon_liv_appro like '%KORYO%')
  AND aa.date_appro_art BETWEEN '2026-01-01' AND '2026-10-10'
ORDER BY
    a.date_appro,
    a.bon_liv_appro,
    ta.nom_art;


 select `bon_liv_appro`, `date_appro`,sm.nom_art,qte_recue, sm.qte_stock_mag21, 
 m.nom_mag,ts.qte_stk, tpa.prix_mini_art,tpa.prix_max_art,tpa.prix_achat_art from t_stock ts left join t_magasin m on m.id_mag=ts.mag_stk left join stock_mag21 sm on sm.id_art=ts.art_stk left join t_prix_article tpa on tpa.art_prix_art=sm.id_art and tpa.a_jour=0 where bon_liv_appro is not null AND m.archive=0 order by bon_liv_appro,sm.nom_art,m.nom_mag


CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_stock_restant_bon`  AS SELECT `sm`.`bon_liv_appro` AS `bon_liv_appro`, `sm`.`date_appro` AS `date_appro`, `sm`.`nom_art` AS `nom_art`, `sm`.`qte_recue` AS `qte_recue`, `sm`.`qte_stock_mag21` AS `qte_stock_mag21`, `m`.`nom_mag` AS `nom_mag`, `ts`.`qte_stk` AS `qte_stk`, `tpa`.`prix_mini_art` AS `prix_mini_art`, `tpa`.`prix_max_art` AS `prix_max_art`, `tpa`.`prix_achat_art` AS `prix_achat_art`,`ts`.`art_stk`,`ts`.`mag_stk` FROM (((`t_stock` `ts` left join `t_magasin` `m` on(`m`.`id_mag` = `ts`.`mag_stk`)) left join `stock_mag21` `sm` on(`sm`.`id_art` = `ts`.`art_stk`)) left join `t_prix_article` `tpa` on(`tpa`.`art_prix_art` = `sm`.`id_art` and `tpa`.`a_jour` = 0)) WHERE `sm`.`bon_liv_appro` is not null AND `m`.`archive` = 0 ORDER BY `sm`.`bon_liv_appro` ASC, `sm`.`nom_art` ASC, `m`.`nom_mag` ASC  ;
