"use strict";
var sngs;
sngs = angular.module("sngs", ["sngs.services", "ngRoute", "ngResource", "base64", "ngLocale", "ngAnimate", "ngCookies", "angularCharts", "LocalStorageModule"]).config(function(localStorageServiceProvider) {
    localStorageServiceProvider.setPrefix("lsSnsgs")
}).config(["$routeProvider", "$locationProvider", function($routeProvider, $locationProvider) {


    $routeProvider.when("/", {
        templateUrl: "app/vws/login-m.html",
        controller: "loginCtrl"
    });
    $routeProvider.when("/home", {
        templateUrl: "app/vws/vente/vnt-vnt-m.html",
        controller: "venteVntCtrl"
    });
    $routeProvider.when("/param", {
        templateUrl: "app/vws/param/prm_art-m.html",
        controller: "paramCtrl"
    });
    $routeProvider.when("/prmcat", {
        templateUrl: "app/vws/param/prm-cat-m.html",
        controller: "paramCatArtCtrl"
    });
    $routeProvider.when("/prmcat/:objectID", {
        templateUrl: "app/vws/param/prm-edit-cat-m.html",
        controller: "paramEditCatArtCtrl",
        resolve: {
            object: ["prmutils", "$route", function(prmutils, $route) {
                var objectID = $route.current.params.objectID;
                var task = prmutils.getCategorie(objectID);
                return task.promise.then(function(result) {
                    if (result.err === 0) {
                        return result
                    } else {
                        return result
                    }
                })
            }]
        }
    });
    $routeProvider.when("/prmunit", {
        templateUrl: "app/vws/param/prm-unit-m.html",
        controller: "paramUnitArtCtrl"
    });
    $routeProvider.when("/prmunit/:objectID", {
        templateUrl: "app/vws/param/prm-edit-unit-m.html",
        controller: "paramEditUnitArtCtrl",
        resolve: {
            object: ["prmutils", "$route", function(prmutils, $route) {
                var objectID = $route.current.params.objectID;
                var task = prmutils.getUnite(objectID);
                return task.promise.then(function(result) {
                    if (result.err == 0) {
                        return result
                    } else {
                        return result
                    }
                })
            }]
        }
    });
    $routeProvider.when("/prmbnk", {
        templateUrl: "app/vws/param/prm-banque-m.html",
        controller: "paramBnkCtrl"
    });
    $routeProvider.when("/prmbnk/:objectID", {
        templateUrl: "app/vws/param/prm-edit-banque-m.html",
        controller: "paramEditBnkCtrl",
        resolve: {
            object: ["prmutils", "$route", function(prmutils, $route) {
                var objectID = $route.current.params.objectID;
                var task = prmutils.getBanque(objectID);
                return task.promise.then(function(result) {
                    if (result.err == 0) {
                        return result
                    } else {
                        return result
                    }
                })
            }]
        }
    });
    $routeProvider.when("/prmart", {
        templateUrl: "app/vws/param/prm-art-m.html",
        controller: "paramArtArtCtrl"
    });
    $routeProvider.when("/prmart/:objectID", {
        templateUrl: "app/vws/param/prm-edit-art-m.html",
        controller: "paramEditArtArtCtrl",
        resolve: {
            object: ["prmutils", "$route", function(prmutils, $route) {
                var objectID = $route.current.params.objectID;
                var task = prmutils.getArticle(objectID);
                return task.promise.then(function(result) {
                    if (result.err == 0) {
                        console.log(result)
                        return result
                    } else {
                        return result
                    }
                })
            }]
        }
    });
    $routeProvider.when("/prmmag", {
        templateUrl: "app/vws/param/prm-mag-m.html",
        controller: "paramMagCtrl"
    });
    $routeProvider.when("/prmmag/:objectID", {
        templateUrl: "app/vws/param/prm-edit-mag-m.html",
        controller: "paramEditMagCtrl",
        resolve: {
            object: ["prmutils", "$route", function(prmutils, $route) {
                var objectID = $route.current.params.objectID;
                var task = prmutils.getMagasin(objectID);
                return task.promise.then(function(result) {
                    if (result.err == 0) {
                        return result
                    } else {
                        return result
                    }
                })
            }]
        }
    });
    $routeProvider.when("/prmtd", {
        templateUrl: "app/vws/param/prm-dep-m.html",
        controller: "paramTdCtrl"
    });
    $routeProvider.when("/prmtd/:objectID", {
        templateUrl: "app/vws/param/prm-edit-dep-m.html",
        controller: "paramEditTdCtrl",
        resolve: {
            object: ["prmutils", "$route", function(prmutils, $route) {
                var objectID = $route.current.params.objectID;
                var task = prmutils.getTypeDepense(objectID);
                return task.promise.then(function(result) {
                    if (result.err == 0) {
                        return result
                    } else {
                        return result
                    }
                })
            }]
        }
    });
    $routeProvider.when("/prmclt", {
        templateUrl: "app/vws/param/prm-clt-m.html",
        controller: "paramCltCtrl"
    });
    $routeProvider.when("/prmclt/:objectID", {
        templateUrl: "app/vws/param/prm-edit-clt-m.html",
        controller: "paramEditCltCtrl",
        resolve: {
            object: ["prmutils", "$route", function(prmutils, $route) {
                var objectID = $route.current.params.objectID;
                var task = prmutils.getClient(objectID);
                return task.promise.then(function(result) {
                    if (result.err == 0) {
                        return result
                    } else {
                        return result
                    }
                })
            }]
        }
    });
    $routeProvider.when("/prmfrns", {
        templateUrl: "app/vws/param/prm-frns-m.html",
        controller: "paramFrnsCtrl"
    });
    $routeProvider.when("/prmfrns/:objectID", {
        templateUrl: "app/vws/param/prm-edit-frns-m.html",
        controller: "paramEditFrnsCtrl",
        resolve: {
            object: ["prmutils", "$route", function(prmutils, $route) {
                var objectID = $route.current.params.objectID;
                var task = prmutils.getFournisseur(objectID);
                return task.promise.then(function(result) {
                    if (result.err == 0) {
                        return result
                    } else {
                        return result
                    }
                })
            }]
        }
    });
    $routeProvider.when("/prmpusr", {
        templateUrl: "app/vws/param/prm-prfl-usr-m.html",
        controller: "paramPflusrCtrl"
    });
    $routeProvider.when("/prmstruct", {
        templateUrl: "app/vws/param/prm-struct-m.html",
        controller: "paramStructCtrl"
    });
    $routeProvider.when("/prmopt", {
        templateUrl: "app/vws/param/prm-opt-m.html",
        controller: "paramOptCtrl"
    });
    $routeProvider.when("/prmcusr", {
        templateUrl: "app/vws/param/prm-cusr-m.html",
        controller: "paramCusrCtrl"
    });

    $routeProvider.when("/prmcusr/:objectID", {
        templateUrl: "app/vws/param/prm-edit-cusr-m.html",
        controller: "paramEditCusrCtrl",
        resolve: {
            object: ["prmutils", "$route", function(prmutils, $route) {
                var objectID = $route.current.params.objectID;
                var task = prmutils.getUser(objectID);
                return task.promise.then(function(result) {
                    console.log(result);
                    if (result.err == 0) {
                        return result
                    } else {
                        return result
                    }
                })
            }]
        }
    });

    /** Droit, profil */
    $routeProvider.when("/prmaccess", {
        templateUrl: "app/vws/param/prm-access-m.html",
        controller: "paramAccessCtrl"
    });
    $routeProvider.when("/prmaccessdroit", {
        templateUrl: "app/vws/param/prm-edit-access-droit-m.html",
        controller: "paramAccessDroitCtrl"
    });

    $routeProvider.when("/prmaccess/:objectID", {
        templateUrl: "app/vws/param/prm-edit-access-m.html",
        controller: "paramEditAccessCtrl",
        resolve: {
            object: ["prmutils", "$route", function(prmutils, $route) {
                var objectID = $route.current.params.objectID;
                var task = prmutils.getProfil(objectID);
                return task.promise.then(function(result) {
                    console.log(result);
                    if (result.err == 0) {
                        return result
                    } else {
                        return result
                    }
                })
            }]
        }
    });

    $routeProvider.when("/stkbaae", {
        templateUrl: "app/vws/stock/stk-ba-ae-m.html",
        controller: "stockBaAeCtrl"
    });
    $routeProvider.when("/stkba", {
        templateUrl: "app/vws/stock/stk-ba-m.html",
        controller: "stockBaCtrl"
    });
    $routeProvider.when("/stkba/:objectID", {
        templateUrl: "app/vws/stock/stk-edit-ba-m.html",
        controller: "stockEditBaCtrl",
        resolve: {
            object: ["prmutils", "$route", function(prmutils, $route) {
                var objectID = $route.current.params.objectID;
                var task = prmutils.getApprovisionnement(objectID);
                return task.promise.then(function(result) {
                    if (result.err == 0) {
                        return result
                    } else {
                        return result
                    }
                })
            }]
        }
    });
    $routeProvider.when("/stkbcmd", {
        templateUrl: "app/vws/stock/stk-bcmd-m.html",
        controller: "stockBcmdCtrl"
    });
    $routeProvider.when("/stkbcmd/:objectID", {
        templateUrl: "app/vws/stock/stk-edit-bcmd-m.html",
        controller: "stockEditBcmdCtrl",
        resolve: {
            object: ["prmutils", "$route", function(prmutils, $route) {
                var objectID = $route.current.params.objectID;
                var task = prmutils.getCommande(objectID);
                return task.promise.then(function(result) {
                    if (result.err == 0) {
                        return result
                    } else {
                        return result
                    }
                })
            }]
        }
    });
    $routeProvider.when("/stkbs", {
        templateUrl: "app/vws/stock/stk-bs-m.html",
        controller: "stockBsCtrl"
    });
    $routeProvider.when("/stkbs/:objectID", {
        templateUrl: "app/vws/stock/stk-edit-bs-m.html",
        controller: "stockEditBsCtrl",
        resolve: {
            object: ["prmutils", "$route", function(prmutils, $route) {
                var objectID = $route.current.params.objectID;
                var task = prmutils.getSortie(objectID);
                return task.promise.then(function(result) {
                    if (result.err == 0) {
                        console.log(result)
                        return result
                    } else {
                        return result
                    }
                })
            }]
        }
    });
    $routeProvider.when("/stklrt", {
        templateUrl: "app/vws/stock/stk-eta-alerte-m.html",
        controller: "stockAlerteCtrl"
    });
    $routeProvider.when("/stketa", {
        templateUrl: "app/vws/stock/stk-eta-m.html",
        controller: "stockEtatCtrl"
    });
    $routeProvider.when("/stkinv-inventaire/:objectID", {
        templateUrl: "app/vws/stock/stk-eta-m-inventaire.html",
        controller: "stockInvCtrl"
    });
    $routeProvider.when("/stksession", {
        templateUrl: "app/vws/stock/stk-session-m.html",
        controller: "stockSessionCtrl"
    });
    $routeProvider.when("/austk", {
        templateUrl: "app/vws/stock/autre-stk-m.html",
        controller: "AutreStockEtatCtrl"
    });
    $routeProvider.when("/stketatrsf", {
        templateUrl: "app/vws/stock/stk-eta-trsf-m.html",
        controller: "stockEtatTrsfCtrl"
    });
    $routeProvider.when("/stketadeff", {
        templateUrl: "app/vws/stock/stk-eta-deff-m.html",
        controller: "stockEtatDeffCtrl"
    });
    $routeProvider.when("/stkas", {
        templateUrl: "app/vws/stock/stk-as-m.html",
        controller: "stockAsCtrl"
    });
    $routeProvider.when("/stkcmd", {
        templateUrl: "app/vws/stock/stk-cmd-m.html",
        controller: "stockCmdCtrl"
    });
    $routeProvider.when("/stkss", {
        templateUrl: "app/vws/stock/stk-ss-m.html",
        controller: "stockSsCtrl"
    });
    $routeProvider.when("/stktrf", {
        templateUrl: "app/vws/stock/stk-trsf-m.html",
        controller: "stockTrfCtrl"
    });
    $routeProvider.when("/stkdeff", {
        templateUrl: "app/vws/stock/stk-deff-m.html",
        controller: "stockDeffCtrl"
    });
    $routeProvider.when("/fctvnt", {
        templateUrl: "app/vws/facture/fact-vnt-m.html",
        controller: "factVntCtrl"
    });
    $routeProvider.when("/fctvntcrdt", {
        templateUrl: "app/vws/facture/fact-vnt-crdt-m.html",
        controller: "factVntCrdtCtrl"
    });
    $routeProvider.when("/fctpro", {
        templateUrl: "app/vws/facture/fact-vnt-proforma-m.html",
        controller: "factVntProCtrl"
    });
    $routeProvider.when("/fctvnttva", {
        templateUrl: "app/vws/facture/fact-vnt-tva-m.html",
        controller: "factVntTvaCtrl"
    });
    $routeProvider.when("/fctvntgrt", {
        templateUrl: "app/vws/facture/fact-vnt-grt-m.html",
        controller: "factVntGrtCtrl"
    });
    $routeProvider.when("/fctvntgrtenc", {
        templateUrl: "app/vws/facture/fact-vnt-grt-enc-m.html",
        controller: "factVntGrtEncCtrl"
    });
    $routeProvider.when("/vntcpt", {
        templateUrl: "app/vws/vente/vnt-cpt-m.html",
        controller: "venteCptCtrl"
    });
    $routeProvider.when("/vntcrdt", {
        templateUrl: "app/vws/vente/vnt-crdt-m.html",
        controller: "venteCrdtCtrl"
    });
    $routeProvider.when("/vntpro", {
        templateUrl: "app/vws/vente/vnt-proforma-m.html",
        controller: "venteProCtrl"
    });
    $routeProvider.when("/vntgrt", {
        templateUrl: "app/vws/vente/vnt-grt-m.html",
        controller: "venteGrtCtrl"
    });
    $routeProvider.when("/vnteta", {
        templateUrl: "app/vws/vente/vnt-eta-m.html",
        controller: "venteEtaCtrl"
    });
    $routeProvider.when("/facteta", {
        templateUrl: "app/vws/facture/fact-eta-m.html",
        controller: "factureEtaCtrl"
    });
    $routeProvider.when("/rglmtclt", {
        templateUrl: "app/vws/reglement/rglmt-clt-m.html",
        controller: "reglementCltCtrl"
    });
    $routeProvider.when("/rglmtcltg", {
        templateUrl: "app/vws/reglement/rglmtg-clt-m.html",
        controller: "reglementgCltCtrl"
    });
    $routeProvider.when("/rglmtgrtclt", {
        templateUrl: "app/vws/reglement/rglmtgrt-clt-m.html",
        controller: "reglementGrtCltCtrl"
    });
    $routeProvider.when("/rglmtgrtcltg", {
        templateUrl: "app/vws/reglement/rglmtgrtg-clt-m.html",
        controller: "reglementGrtgCltCtrl"
    });
    $routeProvider.when("/etarglc", {
        templateUrl: "app/vws/reglement/etat-reg-clt-m.html",
        controller: "etatReglementCltCtrl"
    });
    $routeProvider.when("/etarglcgrt", {
        templateUrl: "app/vws/reglement/etat-reg-clt-grt-m.html",
        controller: "etatReglementCltGrtCtrl"
    });
    $routeProvider.when("/etaretpay", {
        templateUrl: "app/vws/reglement/etat-retard-reg-clt-m.html",
        controller: "etatRetardPayementCltCtrl"
    });
    $routeProvider.when("/rglmtfrnsg", {
        templateUrl: "app/vws/reglement/rglmtg-frns-m.html",
        controller: "reglementgFrnsCtrl"
    });
    $routeProvider.when("/rglmtfrns", {
        templateUrl: "app/vws/reglement/rglmt-frns-m.html",
        controller: "reglementFrnsCtrl"
    });
    $routeProvider.when("/etarglf", {
        templateUrl: "app/vws/reglement/etat-reg-frns-m.html",
        controller: "etatReglementFrnsCtrl"
    });
    $routeProvider.when("/etacais", {
        templateUrl: "app/vws/etat/eta-cais-m.html",
        controller: "etaCaisCtrl"
    });
    $routeProvider.when("/etacaisp", {
        templateUrl: "app/vws/etat/eta-caisp-m.html",
        controller: "etaCaisPCtrl"
    });
    $routeProvider.when("/etamargp", {
        templateUrl: "app/vws/etat/eta-margp-m.html",
        controller: "etaMargPCtrl"
    });
    $routeProvider.when("/valstk", {
        templateUrl: "app/vws/etat/eta-val-stk-m.html",
        controller: "etaValStkCtrl"
    });
    $routeProvider.when("/valstka", {
        templateUrl: "app/vws/etat/eta-val-stk-achat-m.html",
        controller: "etaValAchatStkCtrl"
    });
    $routeProvider.when("/valstk", {
        templateUrl: "app/vws/etat/eta-val-stk-m.html",
        controller: "etaValStkCtrl"
    });
    $routeProvider.when("/valstkdet", {
        templateUrl: "app/vws/etat/eta-val-stk-det-m.html",
        controller: "etaValStkDetCtrl"
    });
    $routeProvider.when("/etacrce", {
        templateUrl: "app/vws/etat/etat-crce-m.html",
        controller: "etaCrceCtrl"
    });
    $routeProvider.when("/etagrt", {
        templateUrl: "app/vws/etat/etat-grt-m.html",
        controller: "etaGrtCtrl"
    });
    $routeProvider.when("/rapgcmd", {
        templateUrl: "app/vws/etat/rapgcmd-m.html",
        controller: "rapgCmdCtrl"
    });
    $routeProvider.when("/etadet", {
        templateUrl: "app/vws/etat/etat-det-m.html",
        controller: "etaDetCtrl"
    });
    $routeProvider.when("/dep", {
        templateUrl: "app/vws/decaissement/decaiss-dep-m.html",
        controller: "decaissDepCtrl"
    });
    $routeProvider.when("/vers", {
        templateUrl: "app/vws/decaissement/decaiss-vers-m.html",
        controller: "decaissVersCtrl"
    });
    $routeProvider.when("/caiss", {
        templateUrl: "app/vws/decaissement/encaiss-cais-m.html",
        controller: "encaissCaisCtrl"
    });
    $routeProvider.when("/etadep", {
        templateUrl: "app/vws/decaissement/etat-dep-m.html",
        controller: "etaDepCtrl"
    });
    $routeProvider.when("/etavers", {
        templateUrl: "app/vws/decaissement/etat-vers-m.html",
        controller: "etaVersCtrl"
    });
    $routeProvider.when("/etacaiss", {
        templateUrl: "app/vws/decaissement/etat-caiss-m.html",
        controller: "etaCaissCtrl"
    });
    $routeProvider.when("/etacaisp", {
        templateUrl: "app/vws/etat/eta-caisp-m.html",
        controller: "etaCaisPCtrl"
    });
    $routeProvider.when("/ficar", {
        templateUrl: "app/vws/etat/etat-fich-art-m.html",
        controller: "etaFichArtCtrl"
    });
    $routeProvider.when("/annvnt", {
        templateUrl: "app/vws/annulation/annul-vnt-m.html",
        controller: "annulVntCtrl"
    });
    $routeProvider.when("/annpro", {
        templateUrl: "app/vws/annulation/annul-proforma-m.html",
        controller: "annulProCtrl"
    });
    $routeProvider.when("/annregcl", {
        templateUrl: "app/vws/annulation/annul-regclnt-m.html",
        controller: "annulRegClntCtrl"
    });
    $routeProvider.when("/annregclgrt", {
        templateUrl: "app/vws/annulation/annul-regclnt-grt-m.html",
        controller: "annulRegClntGrtCtrl"
    });
    $routeProvider.when("/anndep", {
        templateUrl: "app/vws/annulation/annul-dep-m.html",
        controller: "annulDepCtrl"
    });
    $routeProvider.when("/anncaiss", {
        templateUrl: "app/vws/annulation/annul-caiss-m.html",
        controller: "annulCaissCtrl"
    });
    $routeProvider.when("/annvers", {
        templateUrl: "app/vws/annulation/annul-vers-m.html",
        controller: "annulVersCtrl"
    });
    $routeProvider.when("/annregfr", {
        templateUrl: "app/vws/annulation/annul-regfrns-m.html",
        controller: "annulRegFrnstCtrl"
    });
    $routeProvider.when("/anapp", {
        templateUrl: "app/vws/annulation/annul-app-m.html",
        controller: "annulAppCtrl"
    });
    $routeProvider.when("/ancmd", {
        templateUrl: "app/vws/annulation/annul-cmd-m.html",
        controller: "annulCmdCtrl"
    });
    $routeProvider.when("/ansort", {
        templateUrl: "app/vws/annulation/annul-sort-m.html",
        controller: "annulSortCtrl"
    });
    $routeProvider.when("/stat", {
        templateUrl: "app/vws/etat/stat-m.html",
        controller: "statCtrl"
    });
    $routeProvider.when("/etaapp", {
        templateUrl: "app/vws/stock/stk-eta-app-m.html",
        controller: "etaAppCtrl"
    });
    $routeProvider.when("/etacmd", {
        templateUrl: "app/vws/stock/stk-eta-cmd-m.html",
        controller: "etaCmdCtrl"
    });
    $routeProvider.when("/etasort", {
        templateUrl: "app/vws/stock/stk-eta-sort-m.html",
        controller: "etaSortCtrl"
    });
    $routeProvider.when("/prmsav", {
        templateUrl: "app/vws/param/prm-sauv-m.html",
        controller: "paramSauvCtrl"
    });
    $routeProvider.otherwise({
        redirectTo: "/"
    })
}]).constant("PARAMS", {
    VENTE_A_GARANTIE: false,
    PROFIL_STRICT: "non",
    LOAD_ITEMS_ON_PAGE_LOAD: false
});
sngs.run(["$rootScope", "$cookieStore", "PARAMS", "$location", function($rootScope, PARAMS, $location) {
    $rootScope.$on("$routeChangeSuccess", function(event, current, previous) {
        $rootScope.title = current.$$route.title;
        $rootScope.pageTitle = current.$$route.pageTitle;
        $rootScope.PRMS = PARAMS
    })
}]);