sngs.controller("venteEtaCtrl", ["$scope", "$rootScope", "config", "prmutils", function($scope, $rootScope, config, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: config.home,
        subtitle: "Etat des ventes",
        show: true,
        model: {}
    };
    $rootScope.title = "Liste des ventes";
    $rootScope.pageTitle = "Etat des ventes";
    $scope.search = {};
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1;
    var yyyy = today.getFullYear();
    if (dd < 10) {
        dd = "0" + dd
    }
    if (mm < 10) {
        mm = "0" + mm
    }
    today = dd + "/" + mm + "/" + yyyy;
    $scope.search.date_deb = today;
    $scope.getclt = function() {
        var task = prmutils.getaClients();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.clients = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    /**
     * Exporter un tableau en excel
     */

    $scope.exportToExcel = function() {
        var task = prmutils.getaClients();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.clients = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getmag = function() {
        task = prmutils.getMagasins();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.magasins = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getcat = function() {
        task = prmutils.getCategories();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.categories = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getart = function() {
        task = prmutils.getArticles();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.articles = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getuser = function() {
        task = prmutils.getUsers();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.users = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.filtered.length; i++) {
            var vente = $scope.filtered[i];
            total += parseInt(vente.mnt_theo_vnt)
        }
        return total
    };
    $scope.searchF = function() {
        var task;
        task = prmutils.etatVente($scope.search);
        task.promise.then(function(result) {
            console.log(result);
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.ventes = result.data
                }
            } else {
                app.notify(result.message, "m")
            }
        })
    };
    $scope.searchF();
    $scope.fullSearch = function() {
        $scope.loading = true;
        var task;
        console.log($scope.fullSearchText)
        if ($scope.fullSearchText.length > 1) {
            task = prmutils.queryEtatVente($scope.fullSearchText)
        } else {
            task = prmutils.etatVente($scope.search)
        }
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.ventes = result.data
                }
                $scope.loading = false
            } else {
                app.notify("ok ...", "b");
                $scope.loading = false
            }
        })
    }
}]);
sngs.controller("venteVntCtrl", ["$scope", "$rootScope", "config", "prmutils", "$interval", "filterFilter", "socket", function($scope, $rootScope, config, prmutils, $interval, filterFilter, socket) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    $scope.ventes = [];
    if (app.userPfl.pfl != 3 || app.userPfl.mg != 0) {
        app.title = {
            text: config.home,
            subtitle: "Ventes du jour",
            show: true,
            model: {}
        }
    } else {
        app.title = {
            text: config.home,
            subtitle: "Ventes",
            show: true,
            model: {}
        }
    }
    if (app.userPfl.pfl == 5) {
        app.title = {
            text: config.home,
            subtitle: "Factures Non Validees/Encaissees du jour",
            show: true,
            model: {}
        }
    }
    if (app.userPfl.pfl != 3 || app.userPfl.mg != 0) {
        $rootScope.title = "Ventes du jour -";
        $rootScope.pageTitle = "Ventes du jour"
    } else {
        $rootScope.title = "Ventes du jour && Stock";
        $rootScope.pageTitle = "Ventes-Rupture de stock"
    }
    if (app.userPfl.pfl == 5) {
        $rootScope.title = "Factures Non Validees -"
    }
    if (app.userPfl.pfl == 6) {
        $rootScope.title = "Audit-Compta";
        $rootScope.pageTitle = "Audit-Compta";
        app.title = {
            text: "Audit/Compta",
            subtitle: "Controlle Audit/Comptabilite",
            show: true,
            model: {}
        }
    }
    var slice = 0;
    var sliceg = "0";
    var sliceDomaine = [];
    sliceDomaine[0] = 0;
    $scope.getAlCount = function() {
        task = prmutils.getAlCount();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.nbalerte = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };

    $scope.getVentes = function() {
        task = prmutils.getVentes(slice, sliceg);
        task.promise.then(function(result) {
            if (result.err === 0) {
                var temp = result.data.vj;
                var tempg = result.data.gj;
                if (temp !== null) {
                    $scope.ventes = temp.concat($scope.ventes);
                    sliceDomaine = $scope.ventes
                }
                $scope.etatj = result.data.ej;
                app.waiting.show = false;
                slice = parseInt(sliceDomaine[0].id_vnt);
                if (tempg !== null) {
                    $scope.ventes = tempg.concat($scope.ventes)
                }
                if (tempg !== null) {
                    for (i = 0; i < tempg.length; i++) {
                        if (parseInt(tempg[i].id_fact) > 0) {
                            sliceg += "," + tempg[i].id_fact
                        }
                    }
                }
            } else {
                app.waiting.show = false
            }
        })
    };
    socket.on("new_vente", function(data) {
        var obj = data.new_vente.new_vente;
        for (i = 0; i < obj.length; i++) {
            if ((app.userPfl.mg === obj[i].id_mag) || (app.userPfl.mg === "0")) {
                $scope.ventes = obj.concat($scope.ventes);
                $scope.filtered = filterFilter($scope.ventes, $scope.filterText);
                if (obj[i].bl_fact_crdt === "0" && obj[i].sup_fact === "0") {
                    $scope.etatj.mntcpt = parseFloat($scope.etatj.mntcpt) + parseFloat(obj[i].crdt_fact)
                }
                if (obj[i].bl_fact_crdt === "1" && obj[i].bl_fact_grt === "0" && obj[i].sup_fact === "0") {
                    $scope.etatj.mntcrdt = parseFloat($scope.etatj.mntcrdt) + parseFloat(obj[i].crdt_fact)
                }
                if (obj[i].bl_fact_grt === "1" && obj[i].bl_encaiss_grt === "0" && obj[i].sup_fact === "0") {
                    $scope.etatj.mntgrt = parseFloat($scope.etatj.mntgrt) + parseFloat(obj[i].crdt_fact)
                }
                if (obj[i].bl_fact_grt === "1" && obj[i].bl_encaiss_grt === "1" && obj[i].sup_fact === "0") {
                    $scope.etatj.mntencaiss = parseFloat($scope.etatj.mntencaiss) + parseFloat(obj[i].crdt_fact)
                }
            }
        }
    });
    $scope.getAllFactures = function() {
        var task = prmutils.getAllFactures();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.factures = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.showDetails = function(fac) {
        $scope.som_verse = 0;
        $scope.num_fact = fac.code_fact;
        $scope.remise_fact = fac.remise_vnt_fact;
        $scope.credit_fact = fac.crdt_fact;
        $scope.bltva = fac.bl_tva;
        $scope.blbic = fac.bl_bic;
        $scope.exotva = fac.exo_tva_clt;
        $scope.id_mag = fac.mag_user;
        $scope.id_fact = fac.id_fact;
        $scope.id_clt = fac.id_clt;
        $scope.date_fact = fac.date_fact;
        $scope.fac = fac;
        var task = prmutils.showFactureDetails(fac);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.details = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.caclMon = function() {};
    $scope.encaiss = function(fac) {
        if (confirm("Voulez vous vraiment Valider la facture ? ") === true) {
            var task = prmutils.encaiss(fac);
            task.promise.then(function(result) {
                app.waiting.show = true;
                if (result.err === 0) {
                    $scope.details = {};
                    $scope.num_fact = "";
                    $scope.remise_fact = "";
                    $scope.credit_fact = "";
                    $scope.getAllFactures();
                    app.waiting.show = false
                } else {
                    app.waiting.show = false
                }
            })
        }
    };
    $scope.getDetailsTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.details.length; i++) {
            var vente = $scope.details[i];
            total += parseInt(vente.mnt_theo_vnt)
        }
        return total
    };
    $scope.getTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.filtered.length; i++) {
            var vente = $scope.filtered[i];
            total += parseInt(vente.mnt_theo_vnt)
        }
        return total
    };
    if (app.userPfl.pfl != 5) {
        $scope.getVentes();
        $scope.getAlCount()
    } else {
        $scope.getAllFactures()
    }
    if (app.PRMS.dynl) {}
}]);
sngs.controller(" venteGrtCtrl", ["$window", "$scope", "$rootScope", "prmutils", "socket", function($window, $scope, $rootScope, prmutils, socket) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Ventes",
        subtitle: "Garantie",
        show: true,
        model: {}
    };
    $rootScope.title = "Nouvelle Garantie";
    $rootScope.pageTitle = "Garantie";
    $scope.djob = false;
    $scope.requestingArt = false;
    $scope.stock = {
        qte_stk: 0
    };
    $scope.items = [];
    $scope.itemsNewPrices = [];
    $scope.mnt_total = 0;
    $scope.appvente = {};
    $scope.appvente.bl_bic = 0;
    $scope.appvente.bl_tva = 0;
    $scope.appvente.remise = 0;
    $scope.appvente.avance = 0;
    $scope.creditEncoursClient = 0;
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1;
    var yyyy = today.getFullYear();
    if (dd < 10) {
        dd = "0" + dd
    }
    if (mm < 10) {
        mm = "0" + mm
    }
    $scope.num_facture = today;
    if (app.PRMS.resa === 0 || app.PRMS.resa === false) {
        $scope.appvente.date_vnt = dd + "/" + mm + "/" + yyyy
    } else {
        var task = prmutils.getDs();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.appvente.date_vnt = result.data.datej;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    }
    $scope.gclt = function() {
        var task = prmutils.getClients();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.clients = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.gclt();
    $scope.crdtenc = function() {
        var task = prmutils.getCreditEncoursOfClient($scope.appvente.vnt_clt.id_clt);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.creditEncoursClient = parseFloat(result.data.mntcec);
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.save = function(items) {
        var task;
        $scope.djob = true;
        $scope.appvente.remise = ($scope.appvente.remise > 0) ? $scope.appvente.remise : 0;
        ObjVente = {
            id_mag: $scope.appvente.mag_appro_art,
            num_ref_fac: $scope.appvente.ref_fact_vnt,
            num_fact: $scope.num_facture,
            id_clt: $scope.appvente.vnt_clt.id_clt,
            exo_tva_clt: $scope.appvente.vnt_clt.exo_tva_clt,
            mnt_total: $scope.mnt_total,
            remise: $scope.appvente.remise,
            avance: $scope.appvente.avance,
            date: $scope.appvente.date_vnt,
            tva: $scope.appvente.bl_tva,
            bic: $scope.appvente.bl_bic,
            items: items
        };
        task = prmutils.venteGrt(ObjVente);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m");
                    $scope.djob = false
                } else {
                    socket.emit("new_vente", {
                        new_vente: result.data
                    });
                    $scope.emptyForm();
                    app.notify(result.message, "b");
                    $scope.djob = false;
                    if (confirm("Voulez vous Imprimer la facture de la Garantie ? ") === true) {
                        $window.open("app/raps/factgrt.php?f=" + result.message.f)
                    }
                }
            } else {
                $scope.emptyForm();
                app.notify("ok ...", "b");
                $scope.djob = false
            }
        })
    };
    $scope.add = function(art_) {
        tot = $scope.getTotal();
        var art = {};
        art = angular.copy(art_);
        art.or_pm = parseFloat(art_.prix_mini_art);
        art.or_pg = parseFloat(art_.prix_gros_art);
        art.or_mnt = parseInt($scope.appvente.qte_appro_art) * parseFloat(art_.prix_mini_art);
        if ((parseInt($scope.appvente.qte_appro_art) <= 0 || $scope.appvente.qte_appro_art === null || typeof $scope.appvente.qte_appro_art === undefined)) {
            app.notify(" Veuillez revoir la quantite saisie ..! ", " m");
            return false
        }
        if ((parseInt($scope.appvente.qte_appro_art) > $scope.stock.qte_stk)) {
            app.notify(" Quantite superieure a la valeur disponible ..! ", "m");
            return false
        }
        art.qte = $scope.appvente.qte_appro_art;
        art.mnt = ($scope.appvente.bl_gros == 1) ? parseInt(art.qte) * parseFloat(art.prix_gros_art) : parseInt(art.qte) * parseFloat(art.prix_mini_art);
        if (parseFloat($scope.appvente.prix_var) >= 0) {
            art.mnt = parseInt(art.qte) * parseFloat($scope.appvente.prix_var)
        }
        if ((parseFloat(art.mnt) + tot) > (parseFloat($scope.appvente.vnt_clt.max_crdt_clt) - parseFloat($scope.creditEncoursClient))) {
            app.notify(" Attention au plafond maximal de credit autorise ...! ", "m");
            return false
        }
        if ($scope.items.indexOf(art) !== -1) {
            app.notify(" Impossible d'ajouter. article present dans la liste ..! ", "m");
            return false
        }
        art.prix_mini_art = ($scope.appvente.bl_gros == 1) ? art.prix_gros_art : art.prix_mini_art;
        if (parseFloat($scope.appvente.prix_var) >= 0) {
            art.prix_mini_art = parseFloat($scope.appvente.prix_var)
        }
        $scope.items.unshift(art);
        $scope.appvente.qte_appro_art = null;
        $scope.stock.qte_stk = 0;
        $scope.appvente.prix_var = ""
    };
    $scope.getTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.items.length; i++) {
            var item = $scope.items[i];
            total += parseFloat(item.mnt)
        }
        $scope.mnt_total = total;
        return total
    };
    $scope.validRmse = function() {
        $scope.appvente.remise = (parseFloat($scope.appvente.remise) < parseFloat($scope.getTotal())) ? parseFloat($scope.appvente.remise) : parseInt(0)
    };
    $scope.validAvance = function() {
        $scope.appvente.avance = (parseFloat($scope.appvente.avance) <= parseFloat($scope.getTotal()) - parseFloat($scope.appvente.remise)) ? parseFloat($scope.appvente.avance) : parseInt(0)
    };
    $scope.loadArticlesOfCategorie = function(mag, cat) {
        task = prmutils.getExtArticlesOfCategorie(mag, cat);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.articles = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        });
        $scope.appvente.art_appro_art = null;
        $scope.appvente.qte_appro_art = 0;
        $scope.appvente.prix_var = ""
    };
    $scope.loadArticlesForSell = function() {
        task = prmutils.loadArticlesForSell();
        task.promise.then(function(result) {
            console.log(result)
            if (result.err === 0) {
                $scope.articles = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    if (app.PRMS.liopl) {
        $scope.loadArticlesForSell()
    }
    if (!app.PRMS.liopl && !app.PRMS.cat) {
        $scope.loadArticlesForSell()
    }
    $scope.loadExtArticles = function(mag) {
        task = prmutils.getExtArticles(mag);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.articles = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.loadExtCategories = function(mag) {
        task = prmutils.getExtCategoriesOfMag(mag);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.categories = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.loadExtArticlesOfCategorie = function(cat) {
        task = prmutils.getExtArticlesOfCategorie(cat);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.articles = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getStockm = function(art, mag) {
        $scope.items = [];
        task = prmutils.getStock(art, mag);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === null) {
                    $scope.stock = {
                        qte_stk: 0
                    }
                } else {
                    $scope.stock = result.data
                }
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getStocka = function(art, mag) {
        $scope.requestingArt = true;
        task = prmutils.getStock(art, mag);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === null) {
                    $scope.stock = {
                        qte_stk: 0
                    }
                } else {
                    $scope.stock = result.data
                }
                app.waiting.show = false;
                $scope.requestingArt = false
            } else {
                app.waiting.show = false
            }
        });
        $scope.appvente.qte_appro_art = 0;
        $scope.appvente.prix_var = ""
    };
    $scope.emptyForm = function() {
        $scope.items = [];
        $scope.stock.qte_stk = 0;
        $scope.appvente.remise = 0;
        $scope.appvente.avance = 0;
        $scope.appvente.art_appro_art = {};
        $scope.mCategorie = "";
        $scope.appvente.vnt_clt = null
    };
    $scope.clearAr = function() {
        $scope.itemsNewPrices = []
    };
    $scope.cleanField = function() {
        $scope.filterItem = "";
        $scope.ft = ""
    };
    $scope.focusField = function(field) {
        $("[name='" + field + "' ]").focus();
        $("[name='" + field + "' ]").select()
    };
    $scope.focusEnter = function(keyEvent, field) {
        if (keyEvent.which === 13) {
            $("[name='" + field + "' ]").focus();
            $("[name='" + field + "' ]").select()
        }
    };
    $scope.focusEnterAdd = function(keyEvent, field) {
        if (keyEvent.which === 13) {
            $scope.add($scope.appvente.art_appro_art);
            $("[name='" + field + "' ]").focus();
            $("[name='" + field + "' ]").select()
        }
    };
    $scope.emptyArticle = function() {
        $scope.appvente.art_appro_art = {}
    }
}]);
sngs.controller("venteCrdtCtrl", ["$window", "$scope", "$rootScope", "prmutils", "socket", function($window, $scope, $rootScope, prmutils, socket) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Ventes",
        subtitle: "Vente a Credit",
        show: true,
        model: {}
    };
    $rootScope.title = "Nouvelle vente a credit";
    $rootScope.pageTitle = "Vente a credit";
    $scope.djob = false;
    $scope.requestingArt = false;
    $scope.stock = {
        qte_stk: 0
    };
    $scope.items = [];
    $scope.itemsNewPrices = [];
    $scope.mnt_total = 0;
    $scope.appvente = {};
    $scope.appvente.bl_bic = 0;
    $scope.appvente.bl_tva = 0;
    $scope.appvente.remise = 0;
    $scope.appvente.avance = 0;
    $scope.creditEncoursClient = 0;
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1;
    var yyyy = today.getFullYear();
    if (dd < 10) {
        dd = "0" + dd
    }
    if (mm < 10) {
        mm = "0" + mm
    }
    $scope.num_facture = today;
    if (app.PRMS.resa === 0 || app.PRMS.resa === false) {
        $scope.appvente.date_vnt = dd + "/" + mm + "/" + yyyy
    } else {
        var task = prmutils.getDs();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.appvente.date_vnt = result.data.datej;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    }
    $scope.gclt = function() {
        var task = prmutils.getClients();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.clients = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.gclt();
    $scope.crdtenc = function() {
        var task = prmutils.getCreditEncoursOfClient($scope.appvente.vnt_clt.id_clt);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.creditEncoursClient = parseFloat(result.data.mntcec);
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.save = function(items) {
        var task;
        $scope.djob = true;
        $scope.appvente.remise = ($scope.appvente.remise > 0) ? $scope.appvente.remise : 0;
        ObjVente = {
            id_mag: $scope.appvente.mag_appro_art,
            num_ref_fac: $scope.appvente.ref_fact_vnt,
            num_fact: $scope.num_facture,
            id_clt: $scope.appvente.vnt_clt.id_clt,
            exo_tva_clt: $scope.appvente.vnt_clt.exo_tva_clt,
            mnt_total: $scope.mnt_total,
            remise: $scope.appvente.remise,
            avance: $scope.appvente.avance,
            date: $scope.appvente.date_vnt,
            tva: $scope.appvente.bl_tva,
            bic: $scope.appvente.bl_bic,
            items: items
        };
        task = prmutils.venteCrdt(ObjVente);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m");
                    $scope.djob = false
                } else {
                    socket.emit("new_vente", {
                        new_vente: result.data
                    });
                    $scope.emptyForm();
                    app.notify(result.message, "b");
                    $scope.djob = false;
                    if (confirm("Voulez vous Imprimer la facture ? ") === true) {
                        $window.open("app/raps/fact.php?f=" + result.message.f)
                    }
                }
            } else {
                $scope.emptyForm();
                app.notify("ok ...", "b");
                $scope.djob = false
            }
        })
    };
    $scope.add = function(art_) {
        tot = $scope.getTotal();
        var art = {};
        art = angular.copy(art_);
        art.or_pm = parseFloat(art_.prix_mini_art);
        art.or_pg = parseFloat(art_.prix_gros_art);
        art.or_mnt = parseInt($scope.appvente.qte_appro_art) * parseFloat(art_.prix_mini_art);
        if ((parseInt($scope.appvente.qte_appro_art) <= 0 || $scope.appvente.qte_appro_art === null || typeof $scope.appvente.qte_appro_art === undefined)) {
            app.notify(" Veuillez revoir la quantite saisie ..! ", " m");
            return false
        }
        if ((parseInt($scope.appvente.qte_appro_art) > $scope.stock.qte_stk)) {
            app.notify(" Quantite superieure a la valeur disponible ..! ", "m");
            return false
        }
        art.qte = $scope.appvente.qte_appro_art;
        art.mnt = ($scope.appvente.bl_gros == 1) ? parseInt(art.qte) * parseFloat(art.prix_gros_art) : parseInt(art.qte) * parseInt(art.prix_mini_art);
        if (parseInt($scope.appvente.prix_var) >= 0) {
            art.mnt = parseInt(art.qte) * parseFloat($scope.appvente.prix_var)
        }

        console.log($scope.appvente.vnt_clt.max_crdt_clt)
        console.log(parseFloat($scope.creditEncoursClient))
        if ((parseFloat(art.mnt) + tot) > (parseFloat($scope.appvente.vnt_clt.max_crdt_clt) - parseFloat($scope.creditEncoursClient))) {
            app.notify(" Attention au plafond maximal de credit autorise ....! ", "m");
            return false
        }
        if ($scope.items.indexOf(art) !== -1) {
            app.notify(" Impossible d'ajouter. article present dans la liste ..! ", "m");
            return false
        }
        art.prix_mini_art = ($scope.appvente.bl_gros == 1) ? art.prix_gros_art : art.prix_mini_art;
        if (parseFloat($scope.appvente.prix_var) >= 0) {
            art.prix_mini_art = parseFloat($scope.appvente.prix_var)
        }
        $scope.items.unshift(art);
        $scope.appvente.qte_appro_art = null;
        $scope.stock.qte_stk = 0;
        $scope.appvente.prix_var = ""
    };
    $scope.getTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.items.length; i++) {
            var item = $scope.items[i];
            total += parseFloat(item.mnt)
        }
        $scope.mnt_total = total;
        return total
    };
    $scope.validRmse = function() {
        $scope.appvente.remise = (parseFloat($scope.appvente.remise) < parseFloat($scope.getTotal())) ? parseFloat($scope.appvente.remise) : parseInt(0)
    };
    $scope.validAvance = function() {
        $scope.appvente.avance = (parseFloat($scope.appvente.avance) <= parseFloat($scope.getTotal()) - parseFloat($scope.appvente.remise)) ? parseFloat($scope.appvente.avance) : parseInt(0)
    };
    $scope.loadArticlesOfCategorie = function(mag, cat) {
        task = prmutils.getExtArticlesOfCategorie(mag, cat);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.articles = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        });
        $scope.appvente.art_appro_art = null;
        $scope.appvente.qte_appro_art = 0;
        $scope.appvente.prix_var = ""
    };
    $scope.loadExtArticles = function(mag) {
        task = prmutils.getExtArticles(mag);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.articles = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.loadExtCategories = function(mag) {
        task = prmutils.getExtCategoriesOfMag(mag);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.categories = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.loadExtArticlesOfCategorie = function(cat) {
        task = prmutils.getExtArticlesOfCategorie(cat);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.articles = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.loadArticlesForSell = function() {
        task = prmutils.loadArticlesForSell();
        task.promise.then(function(result) {
            console.log(result)
            if (result.err === 0) {
                $scope.articles = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    if (app.PRMS.liopl) {
        $scope.loadArticlesForSell()
    }
    if (!app.PRMS.liopl && !app.PRMS.cat) {
        $scope.loadArticlesForSell()
    }
    $scope.getStockm = function(art, mag) {
        $scope.items = [];
        task = prmutils.getStock(art, mag);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === null) {
                    $scope.stock = {
                        qte_stk: 0
                    }
                } else {
                    $scope.stock = result.data
                }
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getStocka = function(art, mag) {
        $scope.requestingArt = true;
        task = prmutils.getStock(art, mag);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === null) {
                    $scope.stock = {
                        qte_stk: 0
                    }
                } else {
                    $scope.stock = result.data
                }
                $scope.requestingArt = false;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        });
        $scope.appvente.qte_appro_art = 0;
        $scope.appvente.prix_var = ""
    };
    $scope.emptyForm = function() {
        $scope.items = [];
        $scope.stock.qte_stk = 0;
        $scope.appvente.remise = 0;
        $scope.appvente.avance = 0;
        $scope.appvente.art_appro_art = {};
        $scope.mCategorie = "";
        $scope.appvente.vnt_clt = null
    };
    $scope.clearAr = function() {
        $scope.itemsNewPrices = []
    };
    $scope.cleanField = function() {
        $scope.filterItem = "";
        $scope.ft = ""
    };
    $scope.focusField = function(field) {
        $("[name='" + field + "' ]").focus();
        $("[name='" + field + "' ]").select()
    };
    $scope.focusEnter = function(keyEvent, field) {
        if (keyEvent.which === 13) {
            $("[name='" + field + "' ]").focus();
            $("[name='" + field + "' ]").select()
        }
    };
    $scope.focusEnterAdd = function(keyEvent, field) {
        if (keyEvent.which === 13) {
            $scope.add($scope.appvente.art_appro_art);
            $("[name='" + field + "' ]").focus();
            $("[name='" + field + "' ]").select()
        }
    };
    $scope.emptyArticle = function() {
        $scope.appvente.art_appro_art = {}
    }
}]);
sngs.controller("venteProCtrl", ["$window", "$scope", "$rootScope", "prmutils", function($window, $scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Facture",
        subtitle: "Facture Pro Forma",
        show: true,
        model: {}
    };
    $rootScope.title = "Nouvelle Facture Pro forma";
    $rootScope.pageTitle = "Facture Proforma";
    $scope.djob = false;
    $scope.requestingArt = false;
    $scope.stock = {
        qte_stk: 0
    };
    $scope.items = [];
    $scope.itemsNewPrices = [];
    $scope.mnt_total = 0;
    $scope.appvente = {};
    $scope.appvente.bl_bic = 0;
    $scope.appvente.bl_tva = 0;
    $scope.appvente.remise = 0;
    $scope.appvente.avance = 0;
    $scope.creditEncoursClient = 0;
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1;
    var yyyy = today.getFullYear();
    if (dd < 10) {
        dd = "0" + dd
    }
    if (mm < 10) {
        mm = "0" + mm
    }
    $scope.num_facture = today;
    $scope.appvente.date_vnt = dd + "/" + mm + "/" + yyyy;
    $scope.gclt = function() {
        var task = prmutils.getClients();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.clients = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.gclt();
    $scope.save = function(items) {
        var task;
        $scope.djob = true;
        $scope.appvente.remise = ($scope.appvente.remise > 0) ? $scope.appvente.remise : 0;
        ObjVente = {
            id_mag: $scope.appvente.mag_appro_art,
            num_ref_fac: $scope.appvente.ref_fact_vnt,
            num_fact: $scope.num_facture,
            id_clt: $scope.appvente.vnt_clt.id_clt,
            exo_tva_clt: $scope.appvente.vnt_clt.exo_tva_clt,
            mnt_total: $scope.mnt_total,
            remise: $scope.appvente.remise,
            avance: $scope.appvente.avance,
            date: $scope.appvente.date_vnt,
            tva: $scope.appvente.bl_tva,
            bic: $scope.appvente.bl_bic,
            items: items
        };
        task = prmutils.venteProf(ObjVente);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m");
                    $scope.djob = false
                } else {
                    $scope.emptyForm();
                    app.notify(result.message, "b");
                    $scope.djob = false;
                    if (confirm("Voulez vous Imprimer la facture ? ") === true) {
                        $window.open("app/raps/pro.php?f=" + result.message.f)
                    }
                }
            } else {
                $scope.emptyForm();
                app.notify("ok ...", "b");
                $scope.djob = false
            }
        })
    };
    $scope.add = function(art_) {
        tot = $scope.getTotal();
        var art = {};
        art = angular.copy(art_);
        art.or_pm = parseFloat(art_.prix_mini_art);
        art.or_pg = parseFloat(art_.prix_gros_art);
        art.or_mnt = parseInt($scope.appvente.qte_appro_art) * parseFloat(art_.prix_mini_art);
        if ((parseInt($scope.appvente.qte_appro_art) <= 0 || $scope.appvente.qte_appro_art === null || typeof $scope.appvente.qte_appro_art === undefined)) {
            app.notify(" Veuillez revoir la quantite saisie ..! ", " m");
            return false
        }
        art.qte = $scope.appvente.qte_appro_art;
        art.mnt = ($scope.appvente.bl_gros == 1) ? parseInt(art.qte) * parseFloat(art.prix_gros_art) : parseInt(art.qte) * parseInt(art.prix_mini_art);
        if (parseInt($scope.appvente.prix_var) >= 0) {
            art.mnt = parseInt(art.qte) * parseFloat($scope.appvente.prix_var)
        }
        if ($scope.items.indexOf(art) !== -1) {
            app.notify(" Impossible d'ajouter. article present dans la liste ..! ", "m");
            return false
        }
        art.prix_mini_art = ($scope.appvente.bl_gros == 1) ? art.prix_gros_art : art.prix_mini_art;
        if (parseFloat($scope.appvente.prix_var) >= 0) {
            art.prix_mini_art = parseFloat($scope.appvente.prix_var)
        }
        $scope.items.unshift(art);
        $scope.appvente.qte_appro_art = null;
        $scope.stock.qte_stk = 0;
        $scope.appvente.prix_var = ""
    };
    $scope.getTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.items.length; i++) {
            var item = $scope.items[i];
            total += parseFloat(item.mnt)
        }
        $scope.mnt_total = total;
        return total
    };
    $scope.validRmse = function() {
        $scope.appvente.remise = (parseFloat($scope.appvente.remise) < parseFloat($scope.getTotal())) ? parseFloat($scope.appvente.remise) : parseInt(0)
    };
    $scope.validAvance = function() {
        $scope.appvente.avance = (parseFloat($scope.appvente.avance) <= parseFloat($scope.getTotal()) - parseFloat($scope.appvente.remise)) ? parseFloat($scope.appvente.avance) : parseInt(0)
    };
    $scope.loadArticlesOfCategorie = function(mag, cat) {
        task = prmutils.getExtProArticlesOfCategorie(cat);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.articles = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        });
        $scope.appvente.art_appro_art = null;
        $scope.appvente.qte_appro_art = 0;
        $scope.appvente.prix_var = ""
    };
    $scope.loadExtArticles = function(mag) {
        task = prmutils.getExtArticles(mag);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.articles = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.loadExtCategories = function(mag) {
        task = prmutils.getExtProCategories();
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.categories = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.loadExtArticlesOfCategorie = function(cat) {
        task = prmutils.getExtProArticlesOfCategorie(cat);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.articles = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.loadArticlesForPro = function() {
        task = prmutils.loadArticlesForPro();
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.articles = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    if (app.PRMS.liopl) {
        $scope.loadArticlesForPro()
    }
    if (!app.PRMS.liopl && !app.PRMS.cat) {
        $scope.loadArticlesForPro()
    }
    $scope.getStockm = function(art, mag) {
        $scope.items = [];
        task = prmutils.getStock(art, mag);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === null) {
                    $scope.stock = {
                        qte_stk: 0
                    }
                } else {
                    $scope.stock = result.data
                }
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getStocka = function(art, mag) {
        $scope.requestingArt = true;
        task = prmutils.getStock(art, mag);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === null) {
                    $scope.stock = {
                        qte_stk: 0
                    }
                } else {
                    $scope.stock = result.data
                }
                $scope.requestingArt = false;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        });
        $scope.appvente.qte_appro_art = 0;
        $scope.appvente.prix_var = ""
    };
    $scope.emptyForm = function() {
        $scope.items = [];
        $scope.stock.qte_stk = 0;
        $scope.appvente.remise = 0;
        $scope.appvente.avance = 0;
        $scope.appvente.art_appro_art = {};
        $scope.mCategorie = "";
        $scope.appvente.vnt_clt = null
    };
    $scope.clearAr = function() {
        $scope.itemsNewPrices = []
    };
    $scope.cleanField = function() {
        $scope.filterItem = "";
        $scope.ft = ""
    };
    $scope.focusField = function(field) {
        $("[name='" + field + "' ]").focus();
        $("[name='" + field + "' ]").select()
    };
    $scope.focusEnter = function(keyEvent, field) {
        if (keyEvent.which === 13) {
            $("[name='" + field + "' ]").focus();
            $("[name='" + field + "' ]").select()
        }
    };
    $scope.focusEnterAdd = function(keyEvent, field) {
        if (keyEvent.which === 13) {
            $scope.add($scope.appvente.art_appro_art);
            $("[name='" + field + "' ]").focus();
            $("[name='" + field + "' ]").select()
        }
    };
    $scope.emptyArticle = function() {
        $scope.appvente.art_appro_art = {}
    }
}]);
sngs.controller("venteCptCtrl", ["$window", "$scope", "$rootScope", "prmutils", "socket", function($window, $scope, $rootScope, prmutils, socket) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Ventes",
        subtitle: "Vente au Comptant",
        show: true,
        model: {}
    };
    $rootScope.title = "Nouvelle vente au comptant";
    $rootScope.pageTitle = "Vente au comptant";
    $scope.djob = false;
    $scope.requestingArt = false;
    $scope.stock = {
        qte_stk: 0
    };
    $scope.items = [];
    $scope.itemsNewPrices = [];
    $scope.appvente = {};
    $scope.appvente.bl_bic = 0;
    $scope.appvente.bl_tva = 0;
    var datevnt;
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1;
    var yyyy = today.getFullYear();
    if (dd < 10) {
        dd = "0" + dd
    }
    if (mm < 10) {
        mm = "0" + mm
    }
    today = yyyy + "" + mm + "" + dd;
    datevnt = dd + "/" + mm + "/" + yyyy;
    $scope.num_facture = today;

    $scope.appvente.date_vnt = datevnt;
    if (app.PRMS.resa === 0 || app.PRMS.resa === false) {
        $scope.appvente.date_vnt = datevnt
    } else {
        var task = prmutils.getDs();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.appvente.date_vnt = result.data.datej;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    }
    $scope.save = function(items) {
        var task;
        $scope.djob = true;
        $scope.appvente.remise = ($scope.appvente.remise > 0) ? $scope.appvente.remise : 0;
        ObjVente = {
            id_mag: $scope.appvente.mag_appro_art,
            num_ref_fac: $scope.appvente.ref_fact_vnt,
            num_fact: $scope.num_facture,
            mnt_total: $scope.mnt_total,
            remise: $scope.appvente.remise,
            date: $scope.appvente.date_vnt,
            tva: $scope.appvente.bl_tva,
            bic: $scope.appvente.bl_bic,
            vnt_clt: $scope.appvente.vnt_clt.id_clt,
            exo_tva_clt: $scope.appvente.vnt_clt.exo_tva_clt,
            items: items
        };
        task = prmutils.venteCpt(ObjVente);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m");
                    $scope.djob = false
                } else {
                    socket.emit("new_vente", {
                        new_vente: result.data
                    });
                    $scope.emptyForm();
                    app.notify(result.message, "b");
                    $scope.djob = false;
                    if (confirm("Voulez vous Imprimer la facture ? ") === true) {
                        $window.open("app/raps/fact.php?f=" + result.message.f)
                    }
                }
            } else {
                $scope.emptyForm();
                app.notify("ok ...", "b");
                $scope.djob = false
            }
        })
    };
    $scope.gclt = function() {
        var task = prmutils.getOrClients();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.clients = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.gclt();
    $scope.vercours = function() {
        alert("oura")
    };
    $scope.add = function(art_) {
        var art = {};
        art = angular.copy(art_);
        art.or_pm = parseFloat(art_.prix_mini_art);
        art.or_mnt = parseInt($scope.appvente.qte_appro_art) * parseFloat(art_.prix_mini_art);
        if ((parseInt($scope.appvente.qte_appro_art) <= 0 || $scope.appvente.qte_appro_art === null || typeof $scope.appvente.qte_appro_art === undefined)) {
            app.notify(" Veuillez revoir la quantite saisie ..! ", " m");
            return false
        }
        if ((parseInt($scope.appvente.qte_appro_art) > $scope.stock.qte_stk)) {
            app.notify(" Quantite superieure a la valeur disponible ..! ", "m");
            return false
        }
        art.prix_mini_art = (parseFloat($scope.appvente.prix_var) >= 0) ? parseFloat($scope.appvente.prix_var) : art.prix_mini_art;
        art.qte = $scope.appvente.qte_appro_art;
        art.mnt = parseInt(art.qte) * parseFloat(art.prix_mini_art);
        if ($scope.items.indexOf(art) !== -1) {
            app.notify(" Impossible d'ajouter. article present dans la liste ..! ", "m");
            return false
        }
        $scope.items.unshift(art);
        $scope.appvente.qte_appro_art = null;
        $scope.stock.qte_stk = 0;
        $scope.appvente.prix_var = ""
    };
    $scope.getTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.items.length; i++) {
            var item = $scope.items[i];
            total += parseFloat(item.mnt)
        }
        $scope.mnt_total = total;
        return total
    };
    $scope.validRmse = function() {
        $scope.appvente.remise = (parseFloat($scope.appvente.remise) < parseFloat($scope.getTotal())) ? parseFloat($scope.appvente.remise) : parseInt(0)
    };
    $scope.loadArticlesOfCategorie = function(mag, cat) {
        task = prmutils.getExtArticlesOfCategorie(mag, cat);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.articles = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        });
        $scope.appvente.art_appro_art = null;
        $scope.appvente.qte_appro_art = 0;
        $scope.appvente.prix_var = ""
    };
    $scope.loadArticlesForSell = function() {
        task = prmutils.loadArticlesForSell();
        task.promise.then(function(result) {
            console.log(result)
            if (result.err === 0) {
                $scope.articles = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    if (app.PRMS.liopl) {
        $scope.loadArticlesForSell()
    }
    if (!app.PRMS.liopl && !app.PRMS.cat) {
        $scope.loadArticlesForSell()
    }
    $scope.loadExtArticles = function(mag) {
        task = prmutils.getExtArticles(mag);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.articles = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.loadExtCategories = function(mag) {
        task = prmutils.getExtCategoriesOfMag(mag);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.categories = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.loadExtArticlesOfCategorie = function(cat) {
        task = prmutils.getExtArticlesOfCategorie(cat);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.articles = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getStocka = function(art, mag) {
        $scope.requestingArt = true;
        task = prmutils.getStock(art, mag);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === null) {
                    $scope.stock = {
                        qte_stk: 0
                    }
                } else {
                    $scope.stock = result.data
                }
                $scope.requestingArt = false;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        });
        $scope.appvente.qte_appro_art = 0;
        $scope.appvente.prix_var = ""
    };
    $scope.getStockm = function(art, mag) {
        $scope.items = [];
        task = prmutils.getStock(art, mag);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === null) {
                    $scope.stock = {
                        qte_stk: 0
                    }
                } else {
                    $scope.stock = result.data
                }
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.emptyForm = function() {
        $scope.items = [];
        $scope.stock.qte_stk = 0;
        $scope.appvente.remise = 0;
        $scope.appvente.art_appro_art = null;
        $scope.appvente.vnt_clt = null
    };
    $scope.clearAr = function() {
        $scope.itemsNewPrices = []
    };
    $scope.cleanField = function() {
        $scope.filterItem = "";
        $scope.ft = ""
    };
    $scope.focusField = function(field) {
        $("[name='" + field + "' ]").focus();
        $("[name='" + field + "' ]").select()
    };
    $scope.focusEnter = function(keyEvent, field) {
        if (keyEvent.which === 13) {
            $("[name='" + field + "' ]").focus();
            $("[name='" + field + "' ]").select()
        }
    };
    $scope.focusEnterAdd = function(keyEvent, field) {
        if (keyEvent.which === 13) {
            $scope.add($scope.appvente.art_appro_art);
            $("[name='" + field + "' ]").focus();
            $("[name='" + field + "' ]").select()
        }
    };
    $scope.emptyArticle = function() {
        $scope.appvente.art_appro_art = {}
    }
}]);