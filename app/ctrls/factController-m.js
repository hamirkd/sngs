sngs.controller("factureEtaCtrl", ["$scope", "$rootScope", "config", "prmutils", function($scope, $rootScope, config, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: config.home,
        subtitle: "Etat des Factures",
        show: true,
        model: {}
    };
    $rootScope.title = "Liste des factures";
    $rootScope.pageTitle = "Etat des factures";
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
            total += parseInt(vente.crdt_fact)
        }
        return total
    };
    $scope.searchF = function() {
        var task;
        task = prmutils.etatFacture($scope.search);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.ventes = result.data
                }
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.searchF();
    $scope.fullSearch = function() {
        $scope.loading = true;
        var task;
        if ($scope.fullSearchText.length > 1) {
            task = prmutils.queryEtatFacture($scope.fullSearchText)
        } else {
            task = prmutils.etatFacture($scope.search)
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
    };
    $scope.getFactArt = function(id_fact, code, client) {
        var fac = {
            id_fact: id_fact
        };
        $scope.factCode = code + " - " + client;
        var task = prmutils.showFactureDetails(fac);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.details = result.data;
                app.waiting.show = false;
                $("#detailsPannel").css("right", "0")
            } else {
                app.waiting.show = false
            }
        })
    }
}]);
sngs.controller("factVntCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Factures",
        subtitle: "Facture Comptants",
        show: true,
        model: {}
    };
    $rootScope.title = "Factures Comptants";
    $rootScope.pageTitle = "Factures Comptants";
    $scope.newart = false;
    $scope.getFactures = function() {
        var task = prmutils.getFactures();
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
    $scope.getFactures();
    $scope.loadArticlesForSell = function() {
        task = prmutils.loadArticlesForSell($scope.id_mag);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.articles = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.showDetails = function(fac) {
        $scope.num_fact = fac.code_fact;
        $scope.sup_fact = fac.sup_fact;
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
                app.waiting.show = false;
                $scope.loadArticlesForSell()
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getDetailsTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.details.length; i++) {
            var vente = $scope.details[i];
            total += parseInt(vente.mnt_theo_vnt)
        }
        return total
    };
    $scope.undoVnt = function(vnt) {
        var task;
        var vls = prompt('Veuillez saisir la quantite a retirer par rapport au "' + vnt.qte_vnt + '" ', 0);
        if (vls) {
            if (parseInt(vls) > parseInt(vnt.qte_vnt)) {
                app.notify("Attention, la quantite saisie est superieure a la quantite disponible! ...", "m");
                return false
            }
            if (parseInt(vls) <= 0) {
                app.notify("Attention, la quantite saisie est incorrecte! ...", "m");
                return false
            } else {
                vnt.qte_vnt = parseInt(vls);
                vnt.mnt_theo_vnt = parseInt(vnt.qte_vnt) * parseFloat(vnt.pu_theo_vnt);
                task = prmutils.undoVnt(vnt);
                task.promise.then(function(result) {
                    app.waiting.show = true;
                    if (result.err === 0) {
                        $scope.showDetails($scope.fac);
                        app.waiting.show = false
                    } else {
                        app.waiting.show = false
                    }
                })
            }
        }
    };
    $scope.addItem = function(art_) {
        var art = {};
        art = angular.copy(art_);
        art.or_pm = parseFloat(art_.prix_mini_art);
        art.or_mnt = parseInt($scope.appvente.qte_appro_art) * parseFloat(art_.prix_mini_art);
        if ((parseInt($scope.appvente.qte_appro_art) <= 0 || $scope.appvente.qte_appro_art === null || typeof $scope.appvente.qte_appro_art === undefined)) {
            app.notify(" Veuillez revoir la quantite saisie ..! ", "m");
            return false
        }
        if ((parseInt($scope.appvente.qte_appro_art) > $scope.stock.qte_stk)) {
            app.notify(" Quantite superieure a la valeur disponible ..! ", "m");
            return false
        }
        art.qte = $scope.appvente.qte_appro_art;
        art.mnt = ($scope.appvente.bl_gros === 1) ? parseFloat(art.qte) * parseFloat(art.prix_gros_art) : parseInt(art.qte) * parseFloat(art.prix_mini_art);
        if (parseFloat($scope.appvente.prix_var) >= 0) {
            art.mnt = parseInt(art.qte) * parseFloat($scope.appvente.prix_var)
        }
        art.prix_mini_art = ($scope.appvente.bl_gros === 1) ? art.prix_gros_art : art.prix_mini_art;
        if (parseFloat($scope.appvente.prix_var) >= 0) {
            art.prix_mini_art = parseFloat($scope.appvente.prix_var)
        }
        art.id_fact = $scope.id_fact;
        art.id_clt = $scope.id_clt;
        art.date_fact = $scope.date_fact;
        art.id_mag = $scope.id_mag;
        var task;
        task = prmutils.addItem(art);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    app.notify(result.message, "b");
                    $scope.showDetails($scope.fac)
                }
            } else {
                app.notify("ok ...", "b")
            }
        });
        $scope.appvente.qte_appro_art = null;
        $scope.appvente.art_appro_art = null;
        $scope.stock.qte_stk = 0;
        $scope.appvente.prix_var = "";
        $scope.newart = false
    };
    $scope.getTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.filtere.length; i++) {
            var vente = $scope.filtere[i];
            total += parseInt(vente.mnt_theo_vnt)
        }
        return total
    };
    $scope.showNewArt = function() {
        $scope.newart = true
    };
    $scope.rechF = function() {
        var task;
        if ($scope.rech.vare === "") {
            $scope.getFactures()
        } else {
            task = prmutils.getFactBycode($scope.rech.vare);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    if (result.data === "-1") {
                        app.notify(result.message, "m")
                    } else {
                        $scope.factures = result.data;
                        $scope.num_fact = null;
                        $scope.id_mag = null;
                        $scope.details = null
                    }
                } else {
                    app.notify("Oups! Connexion instable ...", "m");
                    $scope.num_fact = null;
                    $scope.id_mag = null;
                    $scope.details = null
                }
            })
        }
    };
    $scope.loadArticlesOfCategorie = function(mag, cat) {
        task = prmutils.getSupExtArticlesOfCategorie($scope.id_mag, cat);
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
    $scope.getStocka = function(art, mag) {
        task = prmutils.getStock(art, $scope.id_mag);
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
        });
        $scope.loadExtCategories(mag);
        $scope.appvente.qte_appro_art = 0;
        $scope.appvente.prix_var = ""
    };
    $scope.select = function(index) {
        $scope.index = index
    }
}]);
sngs.controller("factVntGrtEncCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Factures",
        subtitle: "Facture Garanties Encaiss",
        show: true,
        model: {}
    };
    $rootScope.title = "Fac Grties Encais";
    $rootScope.pageTitle = "Fac Grties Encais";
    $scope.newart = false;
    $scope.getFactures = function() {
        var task = prmutils.getFacturesGrtEnc();
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
    $scope.getFactures();
    $scope.loadArticlesForSell = function() {
        task = prmutils.loadArticlesForSell($scope.id_mag);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.articles = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.showDetails = function(fac) {
        $scope.num_fact = fac.code_fact;
        $scope.sup_fact = fac.sup_fact;
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
                app.waiting.show = false;
                $scope.loadArticlesForSell()
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getDetailsTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.details.length; i++) {
            var vente = $scope.details[i];
            total += parseInt(vente.mnt_theo_vnt)
        }
        return total
    };
    $scope.undoVnt = function(vnt) {
        var task;
        var vls = prompt('Veuillez saisir la quantite a retirer par rapport au "' + vnt.qte_vnt + '" ', 0);
        if (vls) {
            if (parseInt(vls) > parseInt(vnt.qte_vnt)) {
                app.notify("Attention, la quantite saisie est superieure a la quantite disponible! ...", "m");
                return false
            }
            if (parseInt(vls) <= 0) {
                app.notify("Attention, la quantite saisie est incorrecte! ...", "m");
                return false
            } else {
                vnt.qte_vnt = parseInt(vls);
                vnt.mnt_theo_vnt = parseInt(vnt.qte_vnt) * parseFloat(vnt.pu_theo_vnt);
                task = prmutils.undoVnt(vnt);
                task.promise.then(function(result) {
                    app.waiting.show = true;
                    if (result.err === 0) {
                        $scope.showDetails($scope.fac);
                        app.waiting.show = false
                    } else {
                        app.waiting.show = false
                    }
                })
            }
        }
    };
    $scope.addItem = function(art_) {
        var art = {};
        art = angular.copy(art_);
        art.or_pm = parseFloat(art_.prix_mini_art);
        art.or_mnt = parseInt($scope.appvente.qte_appro_art) * parseFloat(art_.prix_mini_art);
        if ((parseInt($scope.appvente.qte_appro_art) <= 0 || $scope.appvente.qte_appro_art === null || typeof $scope.appvente.qte_appro_art === undefined)) {
            app.notify(" Veuillez revoir la quantite saisie ..! ", "m");
            return false
        }
        if ((parseInt($scope.appvente.qte_appro_art) > $scope.stock.qte_stk)) {
            app.notify(" Quantite superieure a la valeur disponible ..! ", "m");
            return false
        }
        art.qte = $scope.appvente.qte_appro_art;
        art.mnt = ($scope.appvente.bl_gros === 1) ? parseFloat(art.qte) * parseFloat(art.prix_gros_art) : parseInt(art.qte) * parseFloat(art.prix_mini_art);
        if (parseFloat($scope.appvente.prix_var) >= 0) {
            art.mnt = parseInt(art.qte) * parseFloat($scope.appvente.prix_var)
        }
        art.prix_mini_art = ($scope.appvente.bl_gros === 1) ? art.prix_gros_art : art.prix_mini_art;
        if (parseFloat($scope.appvente.prix_var) >= 0) {
            art.prix_mini_art = parseFloat($scope.appvente.prix_var)
        }
        art.id_fact = $scope.id_fact;
        art.id_clt = $scope.id_clt;
        art.date_fact = $scope.date_fact;
        art.id_mag = $scope.id_mag;
        var task;
        task = prmutils.addItem(art);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    app.notify(result.message, "b");
                    $scope.showDetails($scope.fac)
                }
            } else {
                app.notify("ok ...", "b")
            }
        });
        $scope.appvente.qte_appro_art = null;
        $scope.appvente.art_appro_art = null;
        $scope.stock.qte_stk = 0;
        $scope.appvente.prix_var = "";
        $scope.newart = false
    };
    $scope.getTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.filtered.length; i++) {
            var vente = $scope.filtered[i];
            total += parseInt(vente.mnt_dep)
        }
        return total
    };
    $scope.showNewArt = function() {
        $scope.newart = true
    };
    $scope.rechF = function() {
        var task;
        if ($scope.rech.vare === "") {
            $scope.getFactures()
        } else {
            task = prmutils.getFactGrtEncBycode($scope.rech.vare);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    if (result.data === "-1") {
                        app.notify(result.message, "m")
                    } else {
                        $scope.factures = result.data;
                        $scope.num_fact = null;
                        $scope.id_mag = null;
                        $scope.details = null
                    }
                } else {
                    app.notify("Oups! Connexion instable ...", "m");
                    $scope.num_fact = null;
                    $scope.id_mag = null;
                    $scope.details = null
                }
            })
        }
    };
    $scope.loadArticlesOfCategorie = function(mag, cat) {
        task = prmutils.getSupExtArticlesOfCategorie($scope.id_mag, cat);
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
    $scope.getStocka = function(art, mag) {
        task = prmutils.getStock(art, $scope.id_mag);
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
        });
        $scope.loadExtCategories(mag);
        $scope.appvente.qte_appro_art = 0;
        $scope.appvente.prix_var = ""
    };
    $scope.select = function(index) {
        $scope.index = index
    }
}]);
sngs.controller("factVntCrdtCtrl", ["$scope", "$rootScope", "Vente", "popupService", "$window", "prmutils", function($scope, $rootScope, Vente, popupService, $window, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Factures",
        subtitle: "Facture Credit",
        show: true,
        model: {}
    };
    $rootScope.title = "Factures Credit";
    $rootScope.pageTitle = "Factures Credit";
    $scope.newart = false;
    $scope.getFactures = function() {
        var task = prmutils.getFacturesCrdt();
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
    $scope.getFactures();
    $scope.loadArticlesForSell = function() {
        task = prmutils.loadArticlesForSell($scope.id_mag);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.articles = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.showDetails = function(fac) {
        $scope.num_fact = fac.code_fact;
        $scope.sup_fact = fac.sup_fact;
        $scope.remise_fact = fac.remise_vnt_fact;
        $scope.credit_fact = fac.crdt_fact;
        $scope.bltva = fac.bl_tva;
        $scope.blbic = fac.bl_bic;
        $scope.exotva = fac.exo_tva_clt;
        $scope.credit_fact = fac.crdt_fact;
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
    $scope.getDetailsTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.details.length; i++) {
            var vente = $scope.details[i];
            total += parseInt(vente.mnt_theo_vnt)
        }
        return total
    };
    $scope.undoVnt = function(vnt) {
        var task;
        var vls = prompt('Veuillez saisir la quantite a retirer par rapport au "' + vnt.qte_vnt + '" ', 0);
        if (vls) {
            if (parseInt(vls) > parseInt(vnt.qte_vnt)) {
                app.notify("Attention, la quantite saisie est superieure a la quantite disponible! ...", "m");
                return false
            }
            if (parseInt(vls) <= 0) {
                app.notify("Attention, la quantite saisie est incorrecte! ...", "m");
                return false
            } else {
                vnt.qte_vnt = parseInt(vls);
                vnt.mnt_theo_vnt = parseInt(vnt.qte_vnt) * parseFloat(vnt.pu_theo_vnt);
                task = prmutils.undoVnt(vnt);
                task.promise.then(function(result) {
                    app.waiting.show = true;
                    if (result.err === 0) {
                        $scope.showDetails($scope.fac);
                        app.waiting.show = false
                    } else {
                        app.waiting.show = false
                    }
                })
            }
        }
    };
    $scope.addItem = function(art_) {
        var art = {};
        art = angular.copy(art_);
        art.or_pm = parseFloat(art_.prix_mini_art);
        art.or_mnt = parseInt($scope.appvente.qte_appro_art) * parseFloat(art_.prix_mini_art);
        if ((parseInt($scope.appvente.qte_appro_art) <= 0 || $scope.appvente.qte_appro_art === null || typeof $scope.appvente.qte_appro_art === undefined)) {
            app.notify(" Veuillez revoir la quantite saisie ..! ", "m");
            return false
        }
        if ((parseInt($scope.appvente.qte_appro_art) > $scope.stock.qte_stk)) {
            app.notify(" Quantite superieure a la valeur disponible ..! ", "m");
            return false
        }
        art.qte = $scope.appvente.qte_appro_art;
        art.mnt = ($scope.appvente.bl_gros === 1) ? parseFloat(art.qte) * parseFloat(art.prix_gros_art) : parseInt(art.qte) * parseFloat(art.prix_mini_art);
        if (parseFloat($scope.appvente.prix_var) >= 0) {
            art.mnt = parseInt(art.qte) * parseFloat($scope.appvente.prix_var)
        }
        art.prix_mini_art = ($scope.appvente.bl_gros === 1) ? art.prix_gros_art : art.prix_mini_art;
        if (parseFloat($scope.appvente.prix_var) >= 0) {
            art.prix_mini_art = parseFloat($scope.appvente.prix_var)
        }
        art.id_fact = $scope.id_fact;
        art.id_clt = $scope.id_clt;
        art.date_fact = $scope.date_fact;
        art.id_mag = $scope.id_mag;
        var task;
        task = prmutils.addItem(art);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    app.notify(result.message, "b");
                    $scope.showDetails($scope.fac)
                }
            } else {
                app.notify("ok ...", "b")
            }
        });
        $scope.appvente.qte_appro_art = null;
        $scope.appvente.art_appro_art = null;
        $scope.stock.qte_stk = 0;
        $scope.appvente.prix_var = "";
        $scope.newart = false
    };
    $scope.getTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.filtere.length; i++) {
            var vente = $scope.filtere[i];
            total += parseInt(vente.mnt_theo_vnt)
        }
        return total
    };
    $scope.showNewArt = function() {
        $scope.newart = true
    };
    $scope.rechF = function() {
        var task;
        if ($scope.rech.vare === "") {
            $scope.getFactures()
        } else {
            task = prmutils.getFactCrdtBycode($scope.rech.vare);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    if (result.data === "-1") {
                        app.notify(result.message, "m")
                    } else {
                        $scope.factures = result.data;
                        $scope.num_fact = null;
                        $scope.id_mag = null;
                        $scope.details = null
                    }
                } else {
                    app.notify("Oups! Connexion instable ...", "m");
                    $scope.num_fact = null;
                    $scope.id_mag = null;
                    $scope.details = null
                }
            })
        }
    };
    $scope.loadArticlesOfCategorie = function(mag, cat) {
        task = prmutils.getSupExtArticlesOfCategorie($scope.id_mag, cat);
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
    $scope.getStocka = function(art, mag) {
        task = prmutils.getStock(art, $scope.id_mag);
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
        });
        $scope.loadExtCategories(mag);
        $scope.appvente.qte_appro_art = 0;
        $scope.appvente.prix_var = ""
    };
    $scope.select = function(index) {
        $scope.index = index
    }
}]);
sngs.controller("factVntProCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Factures",
        subtitle: "Facture Pro Forma",
        show: true,
        model: {}
    };
    $rootScope.title = "Factures Pro Forma";
    $rootScope.pageTitle = "Factures Pro Forma";
    $scope.newart = false;
    $scope.getFactures = function() {
        var task = prmutils.getFacturesPro();
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
    $scope.getFactures();
    $scope.loadArticlesForSell = function() {
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
    $scope.showDetails = function(fac) {
        $scope.num_pro = fac.code_pro;
        $scope.remise_pro = fac.remise_items_pro;
        $scope.credit_pro = fac.crdt_pro;
        $scope.bltva = fac.bl_tva;
        $scope.blbic = fac.bl_bic;
        $scope.exotva = fac.exo_tva_clt;
        $scope.credit_pro = fac.crdt_pro;
        $scope.id_mag = fac.mag_user;
        $scope.id_pro = fac.id_pro;
        $scope.id_clt = fac.id_clt;
        $scope.date_pro = fac.date_pro;
        $scope.fac = fac;
        var task = prmutils.showProDetails(fac);
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
    $scope.getDetailsTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.details.length; i++) {
            var vente = $scope.details[i];
            total += parseInt(vente.mnt_theo_items)
        }
        return total
    };
    $scope.undoVnt = function(vnt) {
        var task;
        var vls = prompt('Veuillez saisir la quantite a retirer par rapport au "' + vnt.qte_items + '" ', 0);
        if (vls) {
            if (parseInt(vls) <= 0) {
                app.notify("Attention, la quantite saisie est incorrecte! ...", "m");
                return false
            } else {
                vnt.qte_items = parseInt(vls);
                vnt.mnt_theo_items = parseInt(vnt.qte_items) * parseFloat(vnt.pu_theo_items);
                task = prmutils.undoProVnt(vnt);
                task.promise.then(function(result) {
                    app.waiting.show = true;
                    if (result.err === 0) {
                        $scope.showDetails($scope.fac);
                        app.waiting.show = false
                    } else {
                        app.waiting.show = false
                    }
                })
            }
        }
    };
    $scope.addItem = function(art_) {
        var art = {};
        art = angular.copy(art_);
        art.or_pm = parseFloat(art_.prix_mini_art);
        art.or_mnt = parseInt($scope.appvente.qte_appro_art) * parseFloat(art_.prix_mini_art);
        if ((parseInt($scope.appvente.qte_appro_art) <= 0 || $scope.appvente.qte_appro_art === null || typeof $scope.appvente.qte_appro_art === undefined)) {
            app.notify(" Veuillez revoir la quantite saisie ..! ", "m");
            return false
        }
        art.qte = $scope.appvente.qte_appro_art;
        art.mnt = ($scope.appvente.bl_gros === 1) ? parseFloat(art.qte) * parseFloat(art.prix_gros_art) : parseInt(art.qte) * parseFloat(art.prix_mini_art);
        if (parseFloat($scope.appvente.prix_var) >= 0) {
            art.mnt = parseInt(art.qte) * parseFloat($scope.appvente.prix_var)
        }
        art.prix_mini_art = ($scope.appvente.bl_gros === 1) ? art.prix_gros_art : art.prix_mini_art;
        if (parseFloat($scope.appvente.prix_var) >= 0) {
            art.prix_mini_art = parseFloat($scope.appvente.prix_var)
        }
        art.id_pro = $scope.id_pro;
        art.id_clt = $scope.id_clt;
        art.date_pro = $scope.date_pro;
        art.id_mag = $scope.id_mag;
        var task;
        task = prmutils.addItemPro(art);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    app.notify(result.message, "b");
                    $scope.showDetails($scope.fac)
                }
            } else {
                app.notify("ok ...", "b")
            }
        });
        $scope.appvente.qte_appro_art = null;
        $scope.appvente.art_appro_art = null;
        $scope.stock.qte_stk = 0;
        $scope.appvente.prix_var = "";
        $scope.newart = false
    };
    $scope.getTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.filtere.length; i++) {
            var vente = $scope.filtere[i];
            total += parseInt(vente.mnt_theo_items)
        }
        return total
    };
    $scope.showNewArt = function() {
        $scope.newart = true
    };
    $scope.rechF = function() {
        var task;
        if ($scope.rech.vare === "") {
            $scope.getFactures()
        } else {
            task = prmutils.getFactCrdtBycode($scope.rech.vare);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    if (result.data === "-1") {
                        app.notify(result.message, "m")
                    } else {
                        $scope.factures = result.data;
                        $scope.num_pro = null;
                        $scope.id_mag = null;
                        $scope.details = null
                    }
                } else {
                    app.notify("Oups! Connexion instable ...", "m");
                    $scope.num_pro = null;
                    $scope.id_mag = null;
                    $scope.details = null
                }
            })
        }
    };
    $scope.loadArticlesOfCategorie = function(mag, cat) {
        task = prmutils.getSupExtProArticlesOfCategorie($scope.id_mag, cat);
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
    $scope.getStocka = function(art, mag) {
        task = prmutils.getStock(art, $scope.id_mag);
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
        });
        $scope.loadExtCategories(mag);
        $scope.appvente.qte_appro_art = 0;
        $scope.appvente.prix_var = ""
    };
    $scope.select = function(index) {
        $scope.index = index
    }
}]);
sngs.controller("factVntTvaCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Factures",
        subtitle: "Facture Tva",
        show: true,
        model: {}
    };
    $rootScope.title = "Factures";
    $rootScope.pageTitle = "Factures avec Taxes";
    $scope.newart = false;
    $scope.getFactures = function() {
        var task = prmutils.getTvaFactures();
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
    $scope.getFactures();
    $scope.showDetails = function(fac) {
        $scope.num_fact = fac.code_fact;
        $scope.sup_fact = fac.sup_fact;
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
    $scope.getDetailsTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.details.length; i++) {
            var vente = $scope.details[i];
            total += parseInt(vente.mnt_theo_vnt)
        }
        return total
    };
    $scope.undoVnt = function(vnt) {
        var task = prmutils.undoVnt(vnt);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.showDetails($scope.fac);
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.addItem = function(art) {
        if ((parseInt($scope.appvente.qte_appro_art) <= 0 || $scope.appvente.qte_appro_art === null || typeof $scope.appvente.qte_appro_art === undefined)) {
            app.notify(" Veuillez revoir la quantite saisie ..! ", "m");
            return false
        }
        if ((parseInt($scope.appvente.qte_appro_art) > $scope.stock.qte_stk)) {
            app.notify(" Quantite superieure a la valeur disponible ..! ", "m");
            return false
        }
        art.qte = $scope.appvente.qte_appro_art;
        art.mnt = ($scope.appvente.bl_gros === 1) ? parseInt(art.qte) * parseInt(art.prix_gros_art) : parseInt(art.qte) * parseInt(art.prix_mini_art);
        if (parseInt($scope.appvente.prix_var) >= 0) {
            art.mnt = parseInt(art.qte) * parseInt($scope.appvente.prix_var)
        }
        art.prix_mini_art = ($scope.appvente.bl_gros === 1) ? art.prix_gros_art : art.prix_mini_art;
        if (parseInt($scope.appvente.prix_var) >= 0) {
            art.prix_mini_art = parseInt($scope.appvente.prix_var)
        }
        art.id_fact = $scope.id_fact;
        art.id_clt = $scope.id_clt;
        art.date_fact = $scope.date_fact;
        art.id_mag = $scope.id_mag;
        var task;
        task = prmutils.addItem(art);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    app.notify(result.message, "b");
                    $scope.showDetails($scope.fac)
                }
            } else {
                app.notify("ok ...", "b")
            }
        });
        $scope.appvente.qte_appro_art = null;
        $scope.appvente.art_appro_art = null;
        $scope.stock.qte_stk = 0;
        $scope.appvente.prix_var = "";
        $scope.newart = false
    };
    $scope.getTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.filtered.length; i++) {
            var vente = $scope.filtered[i];
            total += parseInt(vente.mnt_dep)
        }
        return total
    };
    $scope.showNewArt = function() {
        $scope.newart = true
    };
    $scope.rechF = function() {
        var task;
        if ($scope.rech.vare === "") {
            $scope.getFactures()
        } else {
            task = prmutils.getTvaFactBycode($scope.rech.vare);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    if (result.data === "-1") {
                        app.notify(result.message, "m")
                    } else {
                        $scope.factures = result.data;
                        $scope.num_fact = null;
                        $scope.id_mag = null;
                        $scope.details = null
                    }
                } else {
                    app.notify("Oups! Connexion instable ...", "m");
                    $scope.num_fact = null;
                    $scope.id_mag = null;
                    $scope.details = null
                }
            })
        }
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
    $scope.getStocka = function(art, mag) {
        task = prmutils.getStock(art, $scope.id_mag);
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
        });
        $scope.loadExtCategories(mag);
        $scope.appvente.qte_appro_art = 0;
        $scope.appvente.prix_var = ""
    };
    $scope.select = function(index) {
        $scope.index = index
    }
}]);
sngs.controller("factVntGrtCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Factures",
        subtitle: "Factures Garanties",
        show: true,
        model: {}
    };
    $rootScope.title = "Garanties";
    $rootScope.pageTitle = "Factures Garanties";
    $scope.newart = false;
    $scope.rech = {};
    $scope.rech.vare = "";
    $scope.getFactures = function() {
        var task = prmutils.getFacturesGrt();
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
    $scope.getFactures();
    $scope.loadArticlesForSell = function() {
        task = prmutils.loadArticlesForSell($scope.id_mag);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.articles = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.showDetails = function(fac) {
        $scope.num_fact = fac.code_fact;
        $scope.sup_fact = fac.sup_fact;
        $scope.remise_fact = fac.remise_vnt_fact;
        $scope.credit_fact = fac.crdt_fact;
        $scope.bltva = fac.bl_tva;
        $scope.blbic = fac.bl_bic;
        $scope.exotva = fac.exo_tva_clt;
        $scope.credit_fact = fac.crdt_fact;
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
    $scope.getDetailsTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.details.length; i++) {
            var vente = $scope.details[i];
            total += parseInt(vente.mnt_theo_vnt)
        }
        return total
    };
    $scope.undoVnt = function(vnt) {
        var task;
        var vls = prompt('Veuillez saisir la quantite a retirer par rapport au "' + vnt.qte_vnt + '" ', 0);
        if (vls) {
            if (parseInt(vls) > parseInt(vnt.qte_vnt)) {
                app.notify("Attention, la quantite saisie est superieure a la quantite disponible! ...", "m");
                return false
            }
            if (parseInt(vls) <= 0) {
                app.notify("Attention, la quantite saisie est incorrecte! ...", "m");
                return false
            } else {
                vnt.qte_vnt = parseInt(vls);
                vnt.mnt_theo_vnt = parseInt(vnt.qte_vnt) * parseFloat(vnt.pu_theo_vnt);
                task = prmutils.undoVnt(vnt);
                task.promise.then(function(result) {
                    app.waiting.show = true;
                    if (result.err === 0) {
                        $scope.showDetails($scope.fac);
                        app.waiting.show = false
                    } else {
                        app.waiting.show = false
                    }
                })
            }
        }
    };
    $scope.addItem = function(art_) {
        var art = {};
        art = angular.copy(art_);
        art.or_pm = parseFloat(art_.prix_mini_art);
        art.or_mnt = parseInt($scope.appvente.qte_appro_art) * parseFloat(art_.prix_mini_art);
        if ((parseInt($scope.appvente.qte_appro_art) <= 0 || $scope.appvente.qte_appro_art === null || typeof $scope.appvente.qte_appro_art === undefined)) {
            app.notify(" Veuillez revoir la quantite saisie ..! ", "m");
            return false
        }
        if ((parseInt($scope.appvente.qte_appro_art) > $scope.stock.qte_stk)) {
            app.notify(" Quantite superieure a la valeur disponible ..! ", "m");
            return false
        }
        art.qte = $scope.appvente.qte_appro_art;
        art.mnt = ($scope.appvente.bl_gros === 1) ? parseFloat(art.qte) * parseFloat(art.prix_gros_art) : parseInt(art.qte) * parseFloat(art.prix_mini_art);
        if (parseFloat($scope.appvente.prix_var) >= 0) {
            art.mnt = parseInt(art.qte) * parseFloat($scope.appvente.prix_var)
        }
        art.prix_mini_art = ($scope.appvente.bl_gros === 1) ? art.prix_gros_art : art.prix_mini_art;
        if (parseFloat($scope.appvente.prix_var) >= 0) {
            art.prix_mini_art = parseFloat($scope.appvente.prix_var)
        }
        art.id_fact = $scope.id_fact;
        art.id_clt = $scope.id_clt;
        art.date_fact = $scope.date_fact;
        art.id_mag = $scope.id_mag;
        var task;
        task = prmutils.addItem(art);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    app.notify(result.message, "b");
                    $scope.showDetails($scope.fac)
                }
            } else {
                app.notify("ok ...", "b")
            }
        });
        $scope.appvente.qte_appro_art = null;
        $scope.appvente.art_appro_art = null;
        $scope.stock.qte_stk = 0;
        $scope.appvente.prix_var = "";
        $scope.newart = false
    };
    $scope.getTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.filtered.length; i++) {
            var vente = $scope.filtered[i];
            total += parseInt(vente.mnt_dep)
        }
        return total
    };
    $scope.showNewArt = function() {
        $scope.newart = true
    };
    $scope.rechF = function() {
        var task;
        if ($scope.rech.vare === "") {
            $scope.getFactures()
        } else {
            task = prmutils.getFactGrtBycode($scope.rech.vare);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    if (result.data === "-1") {
                        app.notify(result.message, "m")
                    } else {
                        $scope.factures = result.data;
                        $scope.num_fact = null;
                        $scope.id_mag = null;
                        $scope.details = null
                    }
                } else {
                    app.notify("Oups! Connexion instable ...", "m");
                    $scope.num_fact = null;
                    $scope.id_mag = null;
                    $scope.details = null
                }
            })
        }
    };
    $scope.encaissGrt = function(fac) {
        if (confirm("Voulez vous vraiment Encaisse la Garantie ? ") === true) {
            var task = prmutils.encaissGrt(fac);
            task.promise.then(function(result) {
                app.waiting.show = true;
                if (result.err === 0) {
                    $scope.details = result.data;
                    $scope.rechF();
                    app.waiting.show = false
                } else {
                    app.waiting.show = false
                }
            })
        }
    };
    $scope.loadArticlesOfCategorie = function(mag, cat) {
        task = prmutils.getSupExtArticlesOfCategorie($scope.id_mag, cat);
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
    $scope.getStocka = function(art, mag) {
        task = prmutils.getStock(art, $scope.id_mag);
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
        });
        $scope.loadExtCategories(mag);
        $scope.appvente.qte_appro_art = 0;
        $scope.appvente.prix_var = ""
    };
    $scope.select = function(index) {
        $scope.index = index
    }
}]);