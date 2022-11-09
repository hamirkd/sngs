sngs.factory("prmutils", ["dao", "$q", "config", "localStorageService", function(dao, $q, config, localStorageService) {
    var servicesObject = {};
    servicesObject.getstats = function(obj) {
        return dao.getData(config.mdlEtat + "getStats", obj)
    };
    servicesObject.getcrnv = function() {
        return dao.getData(config.mdlReglement + "getcrnv")
    };
    servicesObject.vucr = function(obj) {
        return dao.getData(config.mdlReglement + "vucr", obj)
    };
    servicesObject.tvucr = function() {
        return dao.getData(config.mdlReglement + "tvucr")
    };
    servicesObject.getdefnv = function() {
        return dao.getData(config.mdlDeff + "getdefnv")
    };
    servicesObject.vudef = function(obj) {
        return dao.getData(config.mdlDeff + "vudef", obj)
    };
    servicesObject.tvudef = function() {
        return dao.getData(config.mdlDeff + "tvudef")
    };
    servicesObject.getdepnv = function() {
        return dao.getData(config.mdlDecaiss + "getdepnv")
    };
    servicesObject.vudep = function(obj) {
        return dao.getData(config.mdlDecaiss + "vudep", obj)
    };
    servicesObject.tvudep = function() {
        return dao.getData(config.mdlDecaiss + "tvudep")
    };

    /** La liste des inventaire de stock */
    servicesObject.getInventaire = function() {
        return dao.getData(config.mdlStockSs + "getInventaire")
    };

    servicesObject.importInventaire = function(fichierInventaire) {
        console.log(config.mdlStockSs + "importInventaire", fichierInventaire);
        console.log(fichierInventaire.get("file"));
        console.log(fichierInventaire.values(), '');
        console.log(fichierInventaire);
        return dao.getData(config.mdlStockSs + "importInventaire", fichierInventaire)
    };

    servicesObject.getsrtnv = function() {
        return dao.getData(config.mdlStockSs + "getsrtnv")
    };
    servicesObject.getsrtnba = function() {
        return dao.getData(config.mdlStockSs + "getsrtnba")
    };
    servicesObject.getsrtnbarj = function() {
        return dao.getData(config.mdlStockSs + "getsrtnbarj")
    };
    servicesObject.vusrt = function(obj) {
        return dao.getData(config.mdlStockSs + "vusrt", obj)
    };
    servicesObject.tvusrt = function() {
        return dao.getData(config.mdlStockSs + "tvusrt")
    };
    servicesObject.rejetersrt = function(obj) {
        return dao.getData(config.mdlStockSs + "rejetersrt", obj)
    };
    servicesObject.bonvusrt = function(obj) {
        return dao.getData(config.mdlStockSs + "bonvusrt", obj)
    };
    servicesObject.tbonvusrt = function() {
        return dao.getData(config.mdlStockSs + "tbonvusrt")
    };
    servicesObject.apprvae = function(obj) {
        return dao.getData(config.mdlStockBa + "apprvae", obj)
    };
    servicesObject.getFactures = function() {
        return dao.getData(config.mdlAnnul + "getFactures")
    };
    servicesObject.getAllFactures = function() {
        return dao.getData(config.mdlAnnul + "getAllFactures")
    };
    servicesObject.getFacturesGrtEnc = function() {
        return dao.getData(config.mdlAnnul + "getFacturesGrtEnc")
    };
    servicesObject.getFacturesFa = function() {
        return dao.getData(config.mdlAnnul + "getFacturesFa")
    };
    servicesObject.getFacturesGrt = function() {
        return dao.getData(config.mdlAnnul + "getFacturesGrt")
    };
    servicesObject.getFacturesCrdt = function() {
        return dao.getData(config.mdlAnnul + "getFacturesCrdt")
    };
    servicesObject.getFacturesPro = function() {
        return dao.getData(config.mdlAnnul + "getFacturesPro")
    };
    servicesObject.getTvaFactures = function() {
        return dao.getData(config.mdlAnnul + "getTvaFactures")
    };
    servicesObject.undoFacture = function(fact) {
        return dao.getData(config.mdlAnnul + "undoFacture", fact)
    };
    servicesObject.undoVnt = function(vnt) {
        return dao.getData(config.mdlAnnul + "undoVnt", vnt)
    };
    servicesObject.undoProVnt = function(vnt) {
        return dao.getData(config.mdlAnnul + "undoProVnt", vnt)
    };
    servicesObject.showFactureDetails = function(fact) {
        return dao.getData(config.mdlAnnul + "showFactureDetails", fact)
    };
    servicesObject.showProDetails = function(fact) {
        return dao.getData(config.mdlAnnul + "showProDetails", fact)
    };
    servicesObject.encaissGrt = function(fact) {
        return dao.getData(config.mdlAnnul + "encaissGrt", fact)
    };
    servicesObject.encaiss = function(fact) {
        return dao.getData(config.mdlAnnul + "encaiss", fact)
    };
    servicesObject.showFacturegDetails = function(clt) {
        return dao.getData(config.mdlAnnul + "showFacturegDetails", clt)
    };
    servicesObject.showFacturegGrtDetails = function(clt) {
        return dao.getData(config.mdlAnnul + "showFacturegGrtDetails", clt)
    };
    servicesObject.getFactBycode = function(fact) {
        return dao.getData(config.mdlAnnul + "getFactBycode&vr=" + fact)
    };
    servicesObject.getFactGrtEncBycode = function(fact) {
        return dao.getData(config.mdlAnnul + "getFactGrtEncBycode&vr=" + fact)
    };
    servicesObject.getFactFaBycode = function(fact) {
        return dao.getData(config.mdlAnnul + "getFactFaBycode&vr=" + fact)
    };
    servicesObject.getFactGrtBycode = function(fact) {
        return dao.getData(config.mdlAnnul + "getFactGrtBycode&vr=" + fact)
    };
    servicesObject.getFactCrdtBycode = function(fact) {
        return dao.getData(config.mdlAnnul + "getFactCrdtBycode&vr=" + fact)
    };
    servicesObject.getTvaFactBycode = function(fact) {
        return dao.getData(config.mdlAnnul + "getTvaFactBycode&vr=" + fact)
    };
    servicesObject.getAnnDepenses = function() {
        return dao.getData(config.mdlAnnul + "getDepenses")
    };
    servicesObject.undoDep = function(dep) {
        return dao.getData(config.mdlAnnul + "undoDep", dep)
    };
    servicesObject.getDepenseByDate = function(date) {
        return dao.getData(config.mdlAnnul + "getDepenseByDate&vr=" + date)
    };
    servicesObject.getAnnProvisionsCaisse = function() {
        return dao.getData(config.mdlAnnul + "getProvisions")
    };
    servicesObject.undoProv = function(dep) {
        return dao.getData(config.mdlAnnul + "undoProv", dep)
    };
    servicesObject.getProvisionByDate = function(date) {
        return dao.getData(config.mdlAnnul + "getProvisionByDate&vr=" + date)
    };
    servicesObject.getAnnVersements = function() {
        return dao.getData(config.mdlAnnul + "getVersements")
    };
    servicesObject.undoVers = function(vers) {
        return dao.getData(config.mdlAnnul + "undoVers", vers)
    };
    servicesObject.getVersementByDate = function(date) {
        return dao.getData(config.mdlAnnul + "getVersementByDate&vr=" + date)
    };
    servicesObject.getCreances = function() {
        return dao.getData(config.mdlAnnul + "getCreances")
    };
    servicesObject.getCreancesGrt = function() {
        return dao.getData(config.mdlAnnul + "getCreancesGrt")
    };
    servicesObject.undoRegClnt = function(fact) {
        return dao.getData(config.mdlAnnul + "undoRegClnt", fact)
    };
    servicesObject.getCreanceBycode = function(fact) {
        return dao.getData(config.mdlAnnul + "getCreanceBycode&vr=" + fact)
    };
    servicesObject.getCreanceGrtBycode = function(fact) {
        return dao.getData(config.mdlAnnul + "getCreanceGrtBycode&vr=" + fact)
    };
    servicesObject.getDettes = function() {
        return dao.getData(config.mdlAnnul + "getDettes")
    };
    servicesObject.undoRegFrns = function(fact) {
        return dao.getData(config.mdlAnnul + "undoRegFrns", fact)
    };
    servicesObject.getDetteBycode = function(fact) {
        return dao.getData(config.mdlAnnul + "getDetteBycode&vr=" + fact)
    };
    servicesObject.saveDepense = function(depense) {
        return dao.getData(config.mdlDecaiss + "saveDepense", depense)
    };
    servicesObject.saveProvision = function(prov) {
        return dao.getData(config.mdlDecaiss + "saveProvision", prov)
    };
    servicesObject.getDepenses = function() {
        return dao.getData(config.mdlDecaiss + "getDepenses")
    };
    servicesObject.getDepensesSl = function(sl) {
        return dao.getData(config.mdlDecaiss + "getDepensesSl", {
            sl: sl
        })
    };
    servicesObject.getProvisions = function() {
        return dao.getData(config.mdlDecaiss + "getProvisions")
    };
    servicesObject.getEtatDepenses = function(obj) {
        return dao.getData(config.mdlDecaiss + "getEtatDepenses", obj)
    };
    servicesObject.getEtatProvisions = function(obj) {
        return dao.getData(config.mdlDecaiss + "getEtatProvisions", obj)
    };
    servicesObject.saveVersement = function(versement) {
        return dao.getData(config.mdlDecaiss + "saveVersement", versement)
    };
    servicesObject.getVersements = function() {
        return dao.getData(config.mdlDecaiss + "getVersements")
    };
    servicesObject.getEtatVersements = function(obj) {
        return dao.getData(config.mdlDecaiss + "getEtatVersements", obj)
    };
    servicesObject.getValStock = function() {
        return dao.getData(config.mdlEtat + "getValStock")
    };
    servicesObject.getValStockAchat = function() {
        return dao.getData(config.mdlEtat + "getValStockAchat")
    };
    servicesObject.getEtatCaisse = function(search) {
        return dao.getData(config.mdlEtat + "getEtatCaisse", search)
    };
    servicesObject.getExtEtatCaisse = function(search) {
        return dao.getData(config.mdlEtat + "getExtEtatCaisse", search)
    };
    servicesObject.getEtatFicheArt = function(search) {
        return dao.getData(config.mdlEtat + "getEtatFicheArt", search)
    };
    servicesObject.getEtatCreances = function(obj) {
        return dao.getData(config.mdlEtat + "getEtatCreances", obj)
    };
    servicesObject.recover = function(customerId, customerNumber) {
        return dao.getData(config.mdlEtat + "recover&cn=" + customerNumber + "&clt=" + customerId)
    };
    servicesObject.getEtatGrt = function(obj) {
        return dao.getData(config.mdlEtat + "getEtatGrt", obj)
    };
    servicesObject.getRapCommande = function(obj) {
        return dao.getData(config.mdlEtat + "getRapCommande", obj)
    };
    servicesObject.getEtatDettes = function(obj) {
        return dao.getData(config.mdlEtat + "getEtatDettes", obj)
    };
    servicesObject.getDettesFournisseurs = function() {
        return dao.getData(config.mdlReglement + "getDettesFournisseurs")
    };
    servicesObject.getDettesgFournisseurs = function() {
        return dao.getData(config.mdlReglement + "getDettesgFournisseurs")
    };
    servicesObject.getDetteDetails = function(dette) {
        return dao.getData(config.mdlReglement + "getDetteDetails&id_appro=" + dette)
    };
    servicesObject.getDettegDetails = function(dette) {
        return dao.getData(config.mdlReglement + "getDettegDetails&id_frns=" + dette)
    };
    servicesObject.paidReglementFrns = function(avance) {
        return dao.getData(config.mdlReglement + "paidReglementFrns", avance)
    };
    servicesObject.paidReglementgFrns = function(avance) {
        return dao.getData(config.mdlReglement + "paidReglementgFrns", avance)
    };
    servicesObject.getCreancesClients = function() {
        return dao.getData(config.mdlReglement + "getCreancesClients")
    };
    servicesObject.getCreancesGrtClients = function() {
        return dao.getData(config.mdlReglement + "getCreancesGrtClients")
    };
    servicesObject.getCreancesgClients = function() {
        return dao.getData(config.mdlReglement + "getCreancesgClients")
    };
    servicesObject.getCreancesGrtgClients = function() {
        return dao.getData(config.mdlReglement + "getCreancesGrtgClients")
    };
    servicesObject.getCreanceDetails = function(creance) {
        return dao.getData(config.mdlReglement + "getCreanceDetails&id_fact=" + creance)
    };
    servicesObject.getCreanceGrtDetails = function(creance) {
        return dao.getData(config.mdlReglement + "getCreanceGrtDetails&id_fact=" + creance)
    };
    servicesObject.getCreancegDetails = function(client) {
        return dao.getData(config.mdlReglement + "getCreancegDetails&id_clt=" + client)
    };
    servicesObject.getCreancegGrtDetails = function(client) {
        return dao.getData(config.mdlReglement + "getCreancegGrtDetails&id_clt=" + client)
    };
    servicesObject.paidReglementClt = function(avance) {
        return dao.getData(config.mdlReglement + "paidReglementClt", avance)
    };
    servicesObject.paidRemiseClt = function(avance) {
        return dao.getData(config.mdlReglement + "paidRemiseClt", avance)
    };
    servicesObject.paidReglementgClt = function(avance) {
        return dao.getData(config.mdlReglement + "paidReglementgClt", avance)
    };
    servicesObject.paidReglementgGrtClt = function(avance) {
        return dao.getData(config.mdlReglement + "paidReglementgGrtClt", avance)
    };
    servicesObject.etatReglementsFrns = function(obj) {
        return dao.getData(config.mdlReglement + "etatReglementsFrns", obj)
    };
    servicesObject.etatReglementsLastFrns = function() {
        return dao.getData(config.mdlReglement + "etatReglementsLastFrns")
    };
    servicesObject.etatReglementsClt = function(obj) {
        return dao.getData(config.mdlReglement + "etatReglementsClt", obj)
    };
    servicesObject.queryEtatRegClt = function(obj) {
        return dao.getData(config.mdlReglement + "queryEtatRegClt&q=" + obj)
    };
    servicesObject.queryEtatRetardPayements = function(obj) {
        return dao.getData(config.mdlReglement + "queryEtatRetardPayements&q=" + obj)
    };
    servicesObject.etatReglementsGrtClt = function(obj) {
        return dao.getData(config.mdlReglement + "etatReglementsGrtClt", obj)
    };
    servicesObject.queryEtatRegGrtClt = function(obj) {
        return dao.getData(config.mdlReglement + "queryEtatRegGrtClt&q=" + obj)
    };
    servicesObject.etatReglementsLastClt = function() {
        return dao.getData(config.mdlReglement + "etatReglementsLastClt")
    };
    servicesObject.etatReglementsGrtLastClt = function() {
        return dao.getData(config.mdlReglement + "etatReglementsGrtLastClt")
    };
    servicesObject.etatRetardPayementsLastClt = function() {
        return dao.getData(config.mdlReglement + "etatRetardPayementsLastClt")
    };
    servicesObject.etatRetardPayementsClt = function(obj) {
        return dao.getData(config.mdlReglement + "etatRetardPayementsClt", obj)
    };
    servicesObject.getDs = function(obj) {
        return dao.getData(config.mdlVente + "getDs")
    };
    servicesObject.getDt = function(obj) {
        return dao.getData(config.mdlVente + "getDt")
    };
    servicesObject.etatFacture = function(obj) {
        return dao.getData(config.mdlAnnul + "etatFacture", obj)
    };
    servicesObject.queryEtatFacture = function(obj) {
        return dao.getData(config.mdlAnnul + "queryEtatFacture&q=" + obj)
    };
    servicesObject.etatVente = function(obj) {
        return dao.getData(config.mdlVente + "etatVente", obj)
    };
    servicesObject.queryEtatVente = function(obj) {
        return dao.getData(config.mdlVente + "queryEtatVente&q=" + obj)
    };
    servicesObject.getVentes = function(slice, sliceg) {
        return dao.getData(config.mdlVente + "getVentes", {
            sl: slice,
            slg: sliceg
        })
    };
    servicesObject.getExtCategories = function() {
        return dao.getData(config.mdlVente + "getExtCategories")
    };
    servicesObject.getExtCategoriesOfMag = function(mag) {
        if (!localStorageService.get("extendCategorieOfMag")) {
            return dao.getDataGet(config.mdlVente + "getExtCategoriesOfMag&id_mag=" + mag, "extendCategorieOfMag")
        } else {
            var task = $q.defer();
            reponse = {
                err: 0,
                data: JSON.parse(localStorageService.get("extendCategorieOfMag")),
                message: ""
            };
            task.resolve(reponse);
            return task
        }
    };
    servicesObject.getExtProCategories = function() {
        return dao.getData(config.mdlVente + "getExtProCategories")
    };
    servicesObject.getExtArticlesOfCategorie = function(objmag, objcat) {
        return dao.getData(config.mdlVente + "getExtArticlesOfCategorie&id_mag=" + objmag + "&id_cat=" + objcat)
    };
    servicesObject.getSupExtArticlesOfCategorie = function(objmag, objcat) {
        return dao.getData(config.mdlVente + "getSupExtArticlesOfCategorie&id_mag=" + objmag + "&id_cat=" + objcat)
    };
    servicesObject.getExtProArticlesOfCategorie = function(objcat) {
        return dao.getData(config.mdlVente + "getExtProArticlesOfCategorie&id_cat=" + objcat)
    };
    servicesObject.getSupExtProArticlesOfCategorie = function(objmag, objcat) {
        return dao.getData(config.mdlVente + "getExtProArticlesOfCategorie&id_mag=" + objmag + "&id_cat=" + objcat)
    };
    servicesObject.loadArticlesForSell = function(id_mag) {
        if (typeof(id_mag) === "undefined") {
            id_mag = 0
        }
        return dao.getData(config.mdlVente + "loadArticlesForSell&id_mag=" + id_mag)
    };
    servicesObject.loadArticlesForPro = function() {
        return dao.getData(config.mdlVente + "loadArticlesForPro")
    };
    servicesObject.getExtMagasins = function() {
        return dao.getData(config.mdlVente + "getExtMagasins")
    };
    servicesObject.venteCpt = function(ObjVente) {
        return dao.getData(config.mdlVente + "venteCpt", ObjVente)
    };
    servicesObject.rdfP = function(ObjVente) {
        return dao.getData(config.mdlVente + "rdfP", ObjVente)
    };
    servicesObject.venteCrdt = function(ObjVente) {
        return dao.getData(config.mdlVente + "venteCrdt", ObjVente)
    };
    servicesObject.venteProf = function(ObjVente) {
        return dao.getData(config.mdlVente + "venteProf", ObjVente)
    };
    servicesObject.venteGrt = function(ObjVente) {
        return dao.getData(config.mdlVente + "venteGrt", ObjVente)
    };
    servicesObject.addItem = function(Obj) {
        return dao.getData(config.mdlVente + "addItemFct", Obj)
    };
    servicesObject.addItemPro = function(Obj) {
        return dao.getData(config.mdlVente + "addItemProFct", Obj)
    };
    servicesObject.getApprovisionnements = function() {
        return dao.getData(config.mdlStockAs + "getApprovisionnements")
    };
    servicesObject.getaApprovisionnements = function() {
        return dao.getData(config.mdlStockAs + "getaApprovisionnements")
    };
    servicesObject.getCommandes = function() {
        return dao.getData(config.mdlStockCmd + "getCommandes")
    };
    servicesObject.getaCommandes = function() {
        return dao.getData(config.mdlStockCmd + "getaCommandes")
    };
    servicesObject.getLmCommandes = function() {
        return dao.getData(config.mdlStockCmd + "getLmCommandes")
    };
    servicesObject.getLmApprovisionnements = function() {
        return dao.getData(config.mdlStockAs + "getLmApprovisionnements")
    };
    servicesObject.loadapproMore = function(offset) {
        return dao.getData(config.mdlStockAs + "loadMore&offset=" + offset)
    };
    servicesObject.loadcmdMore = function(offset) {
        return dao.getData(config.mdlStockCmd + "loadMore&offset=" + offset)
    };
    servicesObject.queryApprovisionnements = function(obj) {
        return dao.getData(config.mdlStockAs + "queryApprovisionnements&q=" + obj)
    };
    servicesObject.queryCommandes = function(obj) {
        return dao.getData(config.mdlStockCmd + "queryCommandes&q=" + obj)
    };
    servicesObject.statusappro = function(sta, id) {
        return dao.getData(config.mdlStockAs + "setStat&s=" + sta + "&id=" + id)
    };
    servicesObject.statuscmd = function(sta, id) {
        return dao.getData(config.mdlStockCmd + "setStat&s=" + sta + "&id=" + id)
    };
    servicesObject.statusca = function(sta, dr, id) {
        return dao.getData(config.mdlStockCmd + "setStatca&s=" + sta + "&dr=" + dr + "&id=" + id)
    };
    servicesObject.statusc = function(sta, dr, id) {
        return dao.getData(config.mdlStockCmd + "setStatc&s=" + sta + "&dr=" + dr + "&id=" + id)
    };
    servicesObject.showApprogDetails = function(fact) {
        return dao.getData(config.mdlStockAs + "showApprogDetails", fact)
    };
    servicesObject.showApproDetails = function(fact) {
        return dao.getData(config.mdlStockAs + "showApproDetails", fact)
    };
    servicesObject.showCmdDetails = function(fact) {
        return dao.getData(config.mdlStockCmd + "showCmdDetails", fact)
    };
    servicesObject.getApproByCode = function(fact) {
        return dao.getData(config.mdlStockAs + "getApproByCode&vr=" + fact)
    };
    servicesObject.getCmdByCode = function(fact) {
        return dao.getData(config.mdlStockCmd + "getCmdByCode&vr=" + fact)
    };
    servicesObject.undoAppro = function(vnt) {
        return dao.getData(config.mdlStockAs + "undoAppro", vnt)
    };
    servicesObject.undoCmd = function(vnt) {
        return dao.getData(config.mdlStockCmd + "undoCmd", vnt)
    };
    servicesObject.undoApproArt = function(vnt) {
        return dao.getData(config.mdlStockAs + "undoApproArt", vnt)
    };
    servicesObject.undoCmdArt = function(vnt) {
        return dao.getData(config.mdlStockCmd + "undoCmdArt", vnt)
    };
    servicesObject.insertStockAppro = function(object) {
        return dao.getData(config.mdlStockAs + "insertStockAppro", object)
    };
    servicesObject.insertStockCmd = function(object) {
        return dao.getData(config.mdlStockCmd + "insertStockCmd", object)
    };
    servicesObject.getStock = function(art, mag) {
        return dao.getData(config.mdlStockStk + "getStock&id_art=" + art + "&id_mag=" + mag)
    };
    servicesObject.getTransStock = function(art, mag) {
        return dao.getData(config.mdlStockStk + "getTransStock&id_art=" + art + "&id_mag=" + mag)
    };
    servicesObject.getPrices = function(art, mag) {
        return dao.getData(config.mdlStockStk + "getPrices&id_art=" + art + "&id_mag=" + mag)
    };
    servicesObject.etatAlerte = function(obj) {
        return dao.getData(config.mdlStockStk + "etatAlerte", obj)
    };
    servicesObject.etatStock = function(obj) {
        return dao.getData(config.mdlStockStk + "etatStock", obj)
    };
    servicesObject.queryEtatStock = function(obj) {
        return dao.getData(config.mdlStockStk + "queryEtatStock&q=" + obj)
    };
    servicesObject.queryAuStock = function(obj) {
        return dao.getData(config.mdlStockStk + "queryAuStock&q=" + obj)
    };
    servicesObject.loadMorealert = function(offset) {
        return dao.getData(config.mdlStockStk + "loadMore&offset=" + offset)
    };
    servicesObject.etatStocklimit = function() {
        return dao.getData(config.mdlStockStk + "etatStocklm")
    };
    servicesObject.updateStock = function(objectID, object) {
        return dao.getData(config.mdlStockStk + "updateStock", {
            id: objectID,
            stock: object
        })
    };
    servicesObject.approuvCorrection = function(obj) {
        return dao.getData(config.mdlStockStk + "aprvstk", obj)
    };
    servicesObject.setAdresseSotck = function(objectID, object) {
        return dao.getData(config.mdlStockStk + "setAdresseSotck", {
            id: objectID,
            stock: object
        })
    };
    servicesObject.replaceItem = function(obj) {
        return dao.getData(config.mdlStockStk + "replaceItem", obj)
    };
    servicesObject.getAlCount = function(obj) {
        return dao.getData(config.mdlStockStk + "getAlCount")
    };
    servicesObject.getArticleEntrees = function(id) {
        return dao.getData(config.mdlStockStk + "getArticleEntrees&id=" + id)
    };
    servicesObject.getArticleCommandees = function(id) {
        return dao.getData(config.mdlStockCmd + "getArticleCommandees&id=" + id)
    };
    servicesObject.undoEntArt = function(vnt) {
        return dao.getData(config.mdlStockStk + "undoEntArt", vnt)
    };
    servicesObject.getSorties = function() {
        return dao.getData(config.mdlStockSs + "getSorties")
    };
    servicesObject.getSortie = function(objectID) {
        return dao.getData(config.mdlStockBs + "getSortie&id=" + objectID)
    };
    servicesObject.getaSorties = function() {
        return dao.getData(config.mdlStockSs + "getaSorties")
    };
    servicesObject.getSortiesAttentes = function() {
        return dao.getData(config.mdlStockSs + "getSortiesAttentes")
    };
    servicesObject.getEtaSort = function(object) {
        return dao.getData(config.mdlStockSs + "getEtaSort", object)
    };
    servicesObject.getArticleSorties = function(id) {
        return dao.getData(config.mdlStockSs + "getArticleSorties&id=" + id)
    };
    servicesObject.statussort = function(sta, id) {
        return dao.getData(config.mdlStockSs + "setStat&s=" + sta + "&id=" + id)
    };
    servicesObject.showSortDetails = function(fact) {
        return dao.getData(config.mdlStockSs + "showSortDetails", fact)
    };
    servicesObject.getSortByCode = function(fact) {
        return dao.getData(config.mdlStockSs + "getSortByCode&vr=" + fact)
    };
    servicesObject.undoSort = function(vnt) {
        return dao.getData(config.mdlStockSs + "undoSort", vnt)
    };
    servicesObject.undoSortArt = function(vnt) {
        return dao.getData(config.mdlStockSs + "undoSortArt", vnt)
    };
    servicesObject.insertStockSort = function(object) {
        return dao.getData(config.mdlStockSs + "insertStockSort", object)
    };
    servicesObject.insertSortie = function(object) {
        return dao.getData(config.mdlStockBs + "insertSortie", object)
    };
    servicesObject.updateSortie = function(objectID, object) {
        return dao.getData(config.mdlStockBs + "updateSortie", {
            id: objectID,
            sortie: object
        })
    };
    servicesObject.deleteSortie = function(objectID) {
        return dao.getData(config.mdlStockBs + "deleteSortie&id=" + objectID)
    };
    servicesObject.insertStockDeff = function(object) {
        return dao.getData(config.mdlDeff + "insertStockDeff", object)
    };
    servicesObject.undoDeff = function(deff) {
        return dao.getData(config.mdlDeff + "undoDeff", deff)
    };
    servicesObject.etatDeff = function(obj) {
        return dao.getData(config.mdlDeff + "etatDeff", obj)
    };
    servicesObject.transfStock = function(object) {
        return dao.getData(config.mdlTransfert + "transfStock", object)
    };
    servicesObject.etatTransf = function(obj) {
        return dao.getData(config.mdlTransfert + "etatTransf", obj)
    };
    servicesObject.undoTransf = function(vnt) {
        return dao.getData(config.mdlTransfert + "undoTransf", vnt)
    };
    servicesObject.getApprovisionnements = function() {
        return dao.getData(config.mdlStockBa + "getApprovisionnements")
    };
    servicesObject.getApprovisionnement = function(objectID) {
        return dao.getData(config.mdlStockBa + "getApprovisionnement&id=" + objectID)
    };
    servicesObject.getCommande = function(objectID) {
        return dao.getData(config.mdlStockBcmd + "getCommande&id=" + objectID)
    };
    servicesObject.getEtaAppro = function(object) {
        return dao.getData(config.mdlStockBa + "getEtaAppro", object)
    };
    servicesObject.getEtaCmd = function(object) {
        return dao.getData(config.mdlStockCmd + "getEtaCmd", object)
    };
    servicesObject.getEtaBl = function(object) {
        return dao.getData(config.mdlStockBa + "getEtaBl", object)
    };
    servicesObject.getEtaBc = function(object) {
        return dao.getData(config.mdlStockBcmd + "getEtaBc", object)
    };
    servicesObject.insertApprovisionnement = function(object) {
        return dao.getData(config.mdlStockBa + "insertApprovisionnement", object)
    };
    servicesObject.insertCommande = function(object) {
        return dao.getData(config.mdlStockBcmd + "insertCommande", object)
    };
    servicesObject.updateApprovisionnement = function(objectID, object) {
        return dao.getData(config.mdlStockBa + "updateApprovisionnement", {
            id: objectID,
            approvisionnement: object
        })
    };
    servicesObject.updateCommande = function(objectID, object) {
        return dao.getData(config.mdlStockBcmd + "updateCommande", {
            id: objectID,
            approvisionnement: object
        })
    };
    servicesObject.deleteApprovisionnement = function(objectID) {
        return dao.getData(config.mdlStockBa + "deleteApprovisionnement&id=" + objectID)
    };
    servicesObject.deleteCommande = function(objectID) {
        return dao.getData(config.mdlStockBcmd + "deleteCommande&id=" + objectID)
    };
    servicesObject.getCategories = function() {
        if (!localStorageService.get("categoriesArticlesCache")) {
            return dao.getDataGet(config.mdlParamCat + "getCategories", "categoriesArticlesCache")
        } else {
            var task = $q.defer();
            reponse = {
                err: 0,
                data: JSON.parse(localStorageService.get("categoriesArticlesCache")),
                message: ""
            };
            task.resolve(reponse);
            return task
        }
    };
    servicesObject.getCategorie = function(objectID) {
        return dao.getData(config.mdlParamCat + "getCategorie&id=" + objectID)
    };
    servicesObject.insertCategorie = function(object) {
        return dao.getData(config.mdlParamCat + "insertCategorie", object)
    };
    servicesObject.updateCategorie = function(objectID, object) {
        return dao.getData(config.mdlParamCat + "updateCategorie", {
            id: objectID,
            categorie: object
        })
    };
    servicesObject.deleteCategorie = function(objectID) {
        return dao.getData(config.mdlParamCat + "deleteCategorie&id=" + objectID)
    };
    servicesObject.loadMorecat = function(offset) {
        return dao.getData(config.mdlParamCat + "loadMore&offset=" + offset)
    };
    servicesObject.getUnites = function() {
        if (!localStorageService.get("UniteCache")) {
            return dao.getDataGet(config.mdlParamUnit + "getUnites", "UniteCache")
        } else {
            var task = $q.defer();
            reponse = {
                err: 0,
                data: JSON.parse(localStorageService.get("UniteCache")),
                message: ""
            };
            task.resolve(reponse);
            return task
        }
    };
    servicesObject.getUnite = function(objectID) {
        return dao.getData(config.mdlParamUnit + "getUnite&id=" + objectID)
    };
    servicesObject.insertUnite = function(object) {
        return dao.getData(config.mdlParamUnit + "insertUnite", object)
    };
    servicesObject.updateUnite = function(objectID, object) {
        return dao.getData(config.mdlParamUnit + "updateUnite", {
            id: objectID,
            unite: object
        })
    };
    servicesObject.deleteUnite = function(objectID) {
        return dao.getData(config.mdlParamUnit + "deleteUnite&id=" + objectID)
    };
    servicesObject.getBanques = function() {
        if (!localStorageService.get("banquesCache")) {
            return dao.getDataGet(config.mdlParamBank + "getBanques", "banquesCache")
        } else {
            var task = $q.defer();
            reponse = {
                err: 0,
                data: JSON.parse(localStorageService.get("banquesCache")),
                message: ""
            };
            task.resolve(reponse);
            return task
        }
    };
    servicesObject.getBanque = function(objectID) {
        return dao.getData(config.mdlParamBank + "getBanque&id=" + objectID)
    };
    servicesObject.insertBanque = function(object) {
        return dao.getData(config.mdlParamBank + "insertBanque", object)
    };
    servicesObject.updateBanque = function(objectID, object) {
        return dao.getData(config.mdlParamBank + "updateBanque", {
            id: objectID,
            banque: object
        })
    };
    servicesObject.deleteBanque = function(objectID) {
        return dao.getData(config.mdlParamBank + "deleteBanque&id=" + objectID)
    };
    servicesObject.getTypeDepenses = function() {
        if (!localStorageService.get("typeDepensesCache")) {
            return dao.getDataGet(config.mdlParamTd + "getTypeDepenses", "typeDepensesCache")
        } else {
            var task = $q.defer();
            reponse = {
                err: 0,
                data: JSON.parse(localStorageService.get("typeDepensesCache")),
                message: ""
            };
            task.resolve(reponse);
            return task
        }
    };
    servicesObject.getTypeDepense = function(objectID) {
        return dao.getData(config.mdlParamTd + "getTypeDepense&id=" + objectID)
    };
    servicesObject.insertTypeDepense = function(object) {
        return dao.getData(config.mdlParamTd + "insertTypeDepense", object)
    };
    servicesObject.updateTypeDepense = function(objectID, object) {
        return dao.getData(config.mdlParamTd + "updateTypeDepense", {
            id: objectID,
            t: object
        })
    };
    servicesObject.deleteTypeDepense = function(objectID) {
        return dao.getData(config.mdlParamTd + "deleteTypeDepense&id=" + objectID)
    };
    servicesObject.getArticles = function() {
        if (!localStorageService.get("ArticlesCache")) {
            return dao.getDataGet(config.mdlParamArt + "getArticles", "ArticlesCache")
        } else {
            var task = $q.defer();
            reponse = {
                err: 0,
                data: JSON.parse(localStorageService.get("ArticlesCache")),
                message: ""
            };
            task.resolve(reponse);
            return task
        }
    };

    servicesObject.queryArticles = function(obj) {
        return dao.getData(config.mdlParamArt + "queryArticles&q=" + obj)
    };
    servicesObject.getLmArticles = function() {
        return dao.getDataGet(config.mdlParamArt + "getLmArticles", "limitedArticlesCache")
    };
    servicesObject.loadMore = function(offset) {
        return dao.getData(config.mdlParamArt + "loadMore&offset=" + offset)
    };
    servicesObject.getArticle = function(objectID) {
        return dao.getData(config.mdlParamArt + "getArticle&id=" + objectID)
    };
    servicesObject.getArticlesOfCategorie = function(objectID) {
        return dao.getData(config.mdlParamArt + "getArticlesOfCategorie&id=" + objectID)
    };
    servicesObject.insertArticle = function(object) {
        return dao.getData(config.mdlParamArt + "insertArticle", object)
    };
    servicesObject.updateArticle = function(objectID, object) {
        return dao.getData(config.mdlParamArt + "updateArticle", {
            id: objectID,
            article: object
        })
    };
    servicesObject.deleteArticle = function(objectID) {
        return dao.getData(config.mdlParamArt + "deleteArticle&id=" + objectID)
    };
    servicesObject.getAllMagasins = function() {
        return dao.getData(config.mdlParamMag + "getAllMagasins")
    };
    servicesObject.getExceptMagasins = function() {
        return dao.getData(config.mdlParamMag + "getExceptMagasins")
    };
    servicesObject.getLimitedMagasins = function() {
        return dao.getData(config.mdlParamMag + "getLimitedMagasins")
    };
    servicesObject.getMagasins = function() {
        if (!localStorageService.get("magasinsCache")) {
            return dao.getDataGet(config.mdlParamMag + "getMagasins", "magasinsCache")
        } else {
            var task = $q.defer();
            reponse = {
                err: 0,
                data: JSON.parse(localStorageService.get("magasinsCache")),
                message: ""
            };
            task.resolve(reponse);
            return task
        }
    };
    servicesObject.getMyMagasinsAcces = function() {
        if (!localStorageService.get("magasinsCache")) {
            return dao.getDataGet(config.mdlParamMag + "getMyMagasinsAcces", "myMagasinsAcces")
        } else {
            var task = $q.defer();
            reponse = {
                err: 0,
                data: JSON.parse(localStorageService.get("myMagasinsAcces")),
                message: ""
            };
            task.resolve(reponse);
            return task
        }
    };
    servicesObject.getMagasin = function(objectID) {
        return dao.getData(config.mdlParamMag + "getMagasin&id=" + objectID)
    };
    servicesObject.changerMagasin = function(id_mag, nom_mag) {
        return dao.getData(config.mdlParamMag + "changerMagasin&id_mag=" + id_mag + "&nom_mag=" + nom_mag)
    };

    servicesObject.insertMagasin = function(object) {
        return dao.getData(config.mdlParamMag + "insertMagasin", object)
    };
    servicesObject.updateMagasin = function(objectID, object) {
        return dao.getData(config.mdlParamMag + "updateMagasin", {
            id: objectID,
            magasin: object
        })
    };
    servicesObject.deleteMagasin = function(objectID) {
        return dao.getData(config.mdlParamMag + "deleteMagasin&id=" + objectID)
    };
    servicesObject.getcUsers = function() {
        return dao.getData(config.mdlParamCusr + "getUsers")
    };
    servicesObject.getProfils = function() {
        return dao.getData(config.mdlParamCusr + "getProfils")
    };

    servicesObject.statususer = function(sta, id) {
        return dao.getData(config.mdlParamCusr + "setStat&s=" + sta + "&id=" + id)
    };
    servicesObject.veilleuser = function(sta, id) {
        return dao.getData(config.mdlParamCusr + "setVeil&s=" + sta + "&id=" + id)
    };
    servicesObject.reinitps = function(id) {
        return dao.getData(config.mdlParamCusr + "setPs&id=" + id)
    };
    servicesObject.getUser = function(objectID) {
        return dao.getData(config.mdlParamCusr + "getUser&id=" + objectID)
    };
    servicesObject.getProfil = function(objectID) {
        return dao.getData(config.mdlParamCusr + "getProfil&id=" + objectID)
    };
    servicesObject.getDroit = function(object) {
        return dao.getData(config.mdlParamCusr + "getDroit", {
            droit: object
        })
    };
    servicesObject.saveDroits = function(droit, profil_droits) {
        return dao.getData(config.mdlParamCusr + "saveDroits", {
            droit: droit,
            profil_droits: profil_droits
        })
    };


    servicesObject.getInfosStruct = function() {
        return dao.getData(config.mdlParamStruct + "getInfosStruct")
    };
    servicesObject.updateStructInfo = function(objectID, object) {
        return dao.getData(config.mdlParamStruct + "updateStructInfo", {
            id: objectID,
            struct: object
        })
    };
    servicesObject.getInfosOptions = function() {
        return dao.getData(config.mdlParamOpt + "getInfosOptions")
    };
    servicesObject.updateOptions = function(objectID, object) {
        return dao.getData(config.mdlParamOpt + "updateOptions", {
            id: objectID,
            opt: object
        })
    };
    servicesObject.insertUser = function(object) {
        return dao.getData(config.mdlParamCusr + "insertUser", object)
    };
    servicesObject.insertProfil = function(object) {
        return dao.getData(config.mdlParamCusr + "insertProfil", object)
    };
    servicesObject.updateProfil2 = function(objectID, object) {
        return dao.getData(config.mdlParamCusr + "updateProfil2", {
            id: objectID,
            profil: object
        })
    };


    servicesObject.updateUser = function(objectID, object) {
        return dao.getData(config.mdlParamCusr + "updateUser", {
            id: objectID,
            user: object
        })
    };
    servicesObject.updateProfil = function(objectID, object) {
        return dao.getData(config.mdlParamCusr + "updateProfil", {
            id: objectID,
            user: object
        })
    };

    servicesObject.deleteProfil = function(objectID) {
        return dao.getData(config.mdlParamCusr + "deleteProfil&id=" + objectID)
    };
    servicesObject.deleteUser = function(objectID) {
        return dao.getData(config.mdlParamCusr + "deleteUser&id=" + objectID)
    };
    servicesObject.getClients = function() {
        if (!localStorageService.get("ClientsCache")) {
            return dao.getDataGet(config.mdlParamClt + "getClients", "ClientsCache")
        } else {
            var task = $q.defer();
            reponse = {
                err: 0,
                data: JSON.parse(localStorageService.get("ClientsCache")),
                message: ""
            };
            task.resolve(reponse);
            return task
        }
    };
    servicesObject.getOrClients = function() {
        return dao.getDataGet(config.mdlParamClt + "getOrClients", "ClientsOrCache")
    };
    servicesObject.getCreditEncoursOfClient = function(client) {
        return dao.getData(config.mdlParamClt + "getCreditEncoursOfClient&id=" + client)
    };
    servicesObject.getaClients = function() {
        if (!localStorageService.get("ClientsCache")) {
            return dao.getDataGet(config.mdlParamClt + "getaClients", "ClientsCache")
        } else {
            var task = $q.defer();
            reponse = {
                err: 0,
                data: JSON.parse(localStorageService.get("ClientsCache")),
                message: ""
            };
            task.resolve(reponse);
            return task
        }
    };
    servicesObject.getClientaCreances = function() {
        return dao.getDataGet(config.mdlParamClt + "getClientaCreances")
    };
    servicesObject.getLmClients = function() {
        return dao.getData(config.mdlParamClt + "getLmClients")
    };
    servicesObject.loadartMore = function(offset) {
        return dao.getData(config.mdlParamClt + "loadMore&offset=" + offset)
    };
    servicesObject.queryClients = function(obj) {
        return dao.getData(config.mdlParamClt + "queryClients&q=" + obj)
    };
    servicesObject.statusclient = function(sta, id) {
        return dao.getData(config.mdlParamClt + "setStat&s=" + sta + "&id=" + id)
    };
    servicesObject.getClient = function(objectID) {
        return dao.getData(config.mdlParamClt + "getClient&id=" + objectID)
    };
    servicesObject.insertClient = function(object) {
        return dao.getData(config.mdlParamClt + "insertClient", object)
    };
    servicesObject.updateClient = function(objectID, object) {
        return dao.getData(config.mdlParamClt + "updateClient", {
            id: objectID,
            client: object
        })
    };
    servicesObject.deleteClient = function(objectID) {
        return dao.getData(config.mdlParamClt + "deleteClient&id=" + objectID)
    };
    servicesObject.replaceCustomer = function(obj) {
        return dao.getData(config.mdlParamClt + "replaceCustomer", obj)
    };
    servicesObject.getFournisseurs = function() {
        if (!localStorageService.get("Fournisseurs2Cache")) {
            return dao.getDataGet(config.mdlParamFrns + "getFournisseurs", "Fournisseurs2Cache")
        } else {
            var task = $q.defer();
            reponse = {
                err: 0,
                data: JSON.parse(localStorageService.get("Fournisseurs2Cache")),
                message: ""
            };
            task.resolve(reponse);
            return task
        }
    };
    servicesObject.getaFournisseurs = function() {
        if (!localStorageService.get("FournisseursCache")) {
            return dao.getDataGet(config.mdlParamFrns + "getaFournisseurs", "FournisseursCache")
        } else {
            var task = $q.defer();
            reponse = {
                err: 0,
                data: JSON.parse(localStorageService.get("FournisseursCache")),
                message: ""
            };
            task.resolve(reponse);
            return task
        }
    };
    servicesObject.statusfournisseur = function(sta, id) {
        return dao.getData(config.mdlParamFrns + "setStat&s=" + sta + "&id=" + id)
    };
    servicesObject.getFournisseur = function(objectID) {
        return dao.getData(config.mdlParamFrns + "getFournisseur&id=" + objectID)
    };
    servicesObject.insertFournisseur = function(object) {
        return dao.getData(config.mdlParamFrns + "insertFournisseur", object)
    };
    servicesObject.updateFournisseur = function(objectID, object) {
        return dao.getData(config.mdlParamFrns + "updateFournisseur", {
            id: objectID,
            fournisseur: object
        })
    };
    servicesObject.deleteFournisseur = function(objectID) {
        return dao.getData(config.mdlParamFrns + "deleteFournisseur&id=" + objectID)
    };
    servicesObject.getUsers = function() {
        if (!localStorageService.get("usersCache")) {
            return dao.getDataGet(config.mdlParamUsrs + "getUsers", "usersCache")
        } else {
            var task = $q.defer();
            reponse = {
                err: 0,
                data: JSON.parse(localStorageService.get("usersCache")),
                message: ""
            };
            task.resolve(reponse);
            return task
        }
    };
    servicesObject.gs = function() {
        return dao.getData(config.mdlS + "gs")
    };
    servicesObject.doSave = function() {
        return dao.getData(config.mdlParamAdmin + "save")
    };
    servicesObject.isDate = function(strDate) {
        if (strDate.length != 10) {
            return false
        }
        var dateParts = strDate.split("/");
        var date = new Date(dateParts[2], (dateParts[1] - 1), dateParts[0]);
        if (date.getDate() == dateParts[0] && date.getMonth() == (dateParts[1] - 1) && date.getFullYear() == dateParts[2]) {
            return true
        } else {
            return false
        }
    };
    return servicesObject
}]);
sngs.factory("socket", function($rootScope) {
    // var socket = io.connect("http://" + window.location.hostname + ":3000");
    console.log(window.location)
    var socket = io.connect("http://" + window.location.origin);
    return {
        on: function(eventName, callback) {
            socket.on(eventName, function() {
                var args = arguments;
                $rootScope.$apply(function() {
                    callback.apply(socket, args)
                })
            })
        },
        emit: function(eventName, data, callback) {
            socket.emit(eventName, data, function() {
                var args = arguments;
                $rootScope.$apply(function() {
                    if (callback) {
                        callback.apply(socket, args)
                    }
                })
            })
        }
    }
});