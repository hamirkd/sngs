sngs.controller("statCtrl", ["$scope", "$rootScope", "prmutils", "config", "$interval", function($scope, $rootScope, prmutils, config, $interval) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Analyse",
        subtitle: "Analyse decisionnelle",
        show: true,
        model: {}
    };
    $rootScope.title = "Graphiques";
    $rootScope.pageTitle = "Analyses + decisions";
    $scope.search = {};
    var today = new Date();
    var yyyy = today.getFullYear();
    $scope.search.year = yyyy;
    $scope.go = function() {
        var task;
        task = prmutils.getstats($scope.search);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.stats = result.data;
                    $scope.stconfig = {
                        title: "Ventes",
                        tooltips: true,
                        labels: false,
                        mouseover: function() {},
                        mouseout: function() {},
                        click: function() {},
                        legend: {
                            display: true,
                            position: "right"
                        }
                    };
                    $scope.stdata1 = {
                        series: ["Comptants", "Credits", "Dettes"],
                        data: [{
                            x: "JAN",
                            y: [$scope.stats.stcpt[0].comptant, $scope.stats.stcrce[0].creance, $scope.stats.stdet[0].dette]
                        }, {
                            x: "FEV",
                            y: [$scope.stats.stcpt[1].comptant, $scope.stats.stcrce[1].creance, $scope.stats.stdet[1].dette]
                        }, {
                            x: "MAR",
                            y: [$scope.stats.stcpt[2].comptant, $scope.stats.stcrce[2].creance, $scope.stats.stdet[2].dette]
                        }, {
                            x: "Avr",
                            y: [$scope.stats.stcpt[3].comptant, $scope.stats.stcrce[3].creance, $scope.stats.stdet[3].dette]
                        }, {
                            x: "Mai",
                            y: [$scope.stats.stcpt[4].comptant, $scope.stats.stcrce[4].creance, $scope.stats.stdet[4].dette]
                        }, {
                            x: "JUI",
                            y: [$scope.stats.stcpt[5].comptant, $scope.stats.stcrce[5].creance, $scope.stats.stdet[5].dette]
                        }, {
                            x: "JUIL",
                            y: [$scope.stats.stcpt[6].comptant, $scope.stats.stcrce[6].creance, $scope.stats.stdet[6].dette]
                        }, {
                            x: "AOU",
                            y: [$scope.stats.stcpt[7].comptant, $scope.stats.stcrce[7].creance, $scope.stats.stdet[7].dette]
                        }, {
                            x: "SEP",
                            y: [$scope.stats.stcpt[8].comptant, $scope.stats.stcrce[8].creance, $scope.stats.stdet[8].dette]
                        }, {
                            x: "OCT",
                            y: [$scope.stats.stcpt[9].comptant, $scope.stats.stcrce[9].creance, $scope.stats.stdet[9].dette]
                        }, {
                            x: "NOV",
                            y: [$scope.stats.stcpt[10].comptant, $scope.stats.stcrce[10].creance, $scope.stats.stdet[10].dette]
                        }, {
                            x: "DEC",
                            y: [$scope.stats.stcpt[11].comptant, $scope.stats.stcrce[11].creance, $scope.stats.stdet[11].dette]
                        }]
                    };
                    $scope.stdata2 = {
                        series: ["Ventes", "Depenses", "Marges"],
                        data: [{
                            x: "JAN",
                            y: [$scope.stats.stvnt[0].vente, $scope.stats.stdep[0].depense, $scope.stats.stben[0].bene]
                        }, {
                            x: "FEV",
                            y: [$scope.stats.stvnt[1].vente, $scope.stats.stdep[1].depense, $scope.stats.stben[1].bene]
                        }, {
                            x: "MAR",
                            y: [$scope.stats.stvnt[2].vente, $scope.stats.stdep[2].depense, $scope.stats.stben[2].bene]
                        }, {
                            x: "Avr",
                            y: [$scope.stats.stvnt[3].vente, $scope.stats.stdep[3].depense, $scope.stats.stben[3].bene]
                        }, {
                            x: "Mai",
                            y: [$scope.stats.stvnt[4].vente, $scope.stats.stdep[4].depense, $scope.stats.stben[4].bene]
                        }, {
                            x: "JUI",
                            y: [$scope.stats.stvnt[5].vente, $scope.stats.stdep[5].depense, $scope.stats.stben[5].bene]
                        }, {
                            x: "JUIL",
                            y: [$scope.stats.stvnt[6].vente, $scope.stats.stdep[6].depense, $scope.stats.stben[6].bene]
                        }, {
                            x: "AOU",
                            y: [$scope.stats.stvnt[7].vente, $scope.stats.stdep[7].depense, $scope.stats.stben[7].bene]
                        }, {
                            x: "SEP",
                            y: [$scope.stats.stvnt[8].vente, $scope.stats.stdep[8].depense, $scope.stats.stben[8].bene]
                        }, {
                            x: "OCT",
                            y: [$scope.stats.stvnt[9].vente, $scope.stats.stdep[9].depense, $scope.stats.stben[9].bene]
                        }, {
                            x: "NOV",
                            y: [$scope.stats.stvnt[10].vente, $scope.stats.stdep[10].depense, $scope.stats.stben[10].bene]
                        }, {
                            x: "DEC",
                            y: [$scope.stats.stvnt[11].vente, $scope.stats.stdep[11].depense, $scope.stats.stben[11].bene]
                        }]
                    }
                }
            } else {
                app.notify("Une erreur est survenue ...", "m")
            }
        })
    };
    $scope.go();
    if (app.PRMS.dynl) {
        var ivs = $interval(function() {
            if ($rootScope.pageTitle === "Analyses + decisions") {
                $scope.go()
            }
        }, config.INTERVAL_ETAT_CAIS_JOUR * 15, false);
        $scope.$on("$destroy", function() {
            $interval.cancel(ivs)
        })
    }
}]);
sngs.controller("etaCrceCtrl", ["$scope", "$rootScope", "prmutils", "config", "$interval", function($scope, $rootScope, prmutils, config, $interval) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Etats",
        subtitle: "Etat Credits Clients",
        show: true,
        model: {}
    };
    $rootScope.title = "Liste des Credits clients";
    $rootScope.pageTitle = "Etat Credits Clients";
    $scope.search = {};
    $scope.getclt = function() {
        var task = prmutils.getClientaCreances();
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
    $scope.searchF = function() {
        var task;
        task = prmutils.getEtatCreances($scope.search);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.creances = result.data
                }
            } else {
                app.notify("Une erreur est survenue ...", "m")
            }
        })
    };
    $scope.recover = function(customerId, customerNumber) {
        var task;
        task = prmutils.recover(customerId, customerNumber);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {}
            } else {
                app.notify("Une erreur est survenue ...", "m")
            }
        })
    };
    $scope.getTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.filtered.length; i++) {
            var vente = $scope.filtered[i];
            total += parseInt(vente.mnt_crce)
        }
        return total
    };
    $scope.searchF();
    if (app.PRMS.dynl) {
        var ivs = $interval(function() {
            if ($rootScope.pageTitle === "Etat Credits Clients") {
                $scope.searchF()
            }
        }, config.INTERVAL_ETAT_CRCE, false);
        $scope.$on("$destroy", function() {
            $interval.cancel(ivs)
        })
    }
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
sngs.controller("etaGrtCtrl", ["$scope", "$rootScope", "prmutils", "config", "$interval", function($scope, $rootScope, prmutils, config, $interval) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Etats",
        subtitle: "Etat Garanties Clients",
        show: true,
        model: {}
    };
    $rootScope.title = "Liste des Garanties clients";
    $rootScope.pageTitle = "Etat Garanties Clients";
    $scope.search = {};
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
    $scope.searchF = function() {
        var task;
        task = prmutils.getEtatGrt($scope.search);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.creances = result.data
                }
            } else {
                app.notify("Une erreur est survenue ...", "m")
            }
        })
    };
    $scope.getTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.filtered.length; i++) {
            var vente = $scope.filtered[i];
            total += parseInt(vente.mnt_crce)
        }
        return total
    };
    $scope.searchF();
    if (app.PRMS.dynl) {
        var ivs = $interval(function() {
            if ($rootScope.pageTitle === "Etat Garanties Clients") {
                $scope.searchF()
            }
        }, config.INTERVAL_ETAT_GRT, false);
        $scope.$on("$destroy", function() {
            $interval.cancel(ivs)
        })
    }
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
sngs.controller("rapgCmdCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Etats",
        subtitle: "Rapports COmmandes Clients",
        show: true,
        model: {}
    };
    $rootScope.title = "Liste des Commandes clients";
    $rootScope.pageTitle = "Rapport des Commandes Clients";
    $scope.search = {};
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
    $scope.searchF = function() {
        var task;
        task = prmutils.getRapCommande($scope.search);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.creances = result.data
                }
            } else {
                app.notify("Une erreur est survenue ...", "m")
            }
        })
    };
    $scope.getTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.filtered.length; i++) {
            var vente = $scope.filtered[i];
            total += parseInt(vente.mnt_crce)
        }
        return total
    };
    $scope.searchF();
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
sngs.controller("etaDetCtrl", ["$scope", "$rootScope", "prmutils", "config", "$interval", function($scope, $rootScope, prmutils, config, $interval) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Etats",
        subtitle: "Dettes",
        show: true,
        model: {}
    };
    $rootScope.title = "Liste des dettes fournisseurs";
    $rootScope.pageTitle = "Etat Dettes Fournisseurs";
    $scope.search = {};
    $scope.getfrns = function() {
        var task = prmutils.getFournisseurs();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.fournisseurs = result.data;
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
    $scope.searchF = function() {
        var task;
        task = prmutils.getEtatDettes($scope.search);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.dettes = result.data
                }
            } else {
                app.notify("Une erreur est survenue ...", "m")
            }
        })
    };
    $scope.getTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.filtered.length; i++) {
            var vente = $scope.filtered[i];
            total += parseInt(vente.mnt_dette)
        }
        return total
    };
    $scope.searchF();
    if (app.PRMS.dynl) {
        var ivs = $interval(function() {
            if ($rootScope.pageTitle === "Etat Dettes Fournisseurs") {
                $scope.searchF()
            }
        }, config.INTERVAL_ETAT_DETTE, false);
        $scope.$on("$destroy", function() {
            $interval.cancel(ivs)
        })
    }
    $scope.getBlArt = function(id_appro, code, frns) {
        var fac = {
            id_appro: id_appro
        };
        $scope.factCode = code + " - " + frns;
        var task = prmutils.showApproDetails(fac);
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
sngs.controller("etaCaisCtrl", ["$scope", "$rootScope", "config", "prmutils", "$interval", function($scope, $rootScope, config, prmutils, $interval) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Etat",
        subtitle: "Caisse",
        show: true,
        model: {}
    };
    $rootScope.title = "Etat de la caisse";
    $rootScope.pageTitle = "Etat Caisse";
    $scope.search = {};
    var bl_query_day;
    bl_query_day = true;
    task = prmutils.getMagasins();
    task.promise.then(function(result) {
        app.waiting.show = true;
        if (result.err === 0) {
            $scope.magasins = result.data;
            app.waiting.show = false
        } else {
            app.waiting.show = false
        }
    });
    $scope.searchFDay = function() {
        bl_query_day = true;
        var task = prmutils.getEtatCaisse($scope.search);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.caisse = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.searchFDay();
    $scope.searchF = function() {
        bl_query_day = false;
        var task;
        task = prmutils.getExtEtatCaisse($scope.search);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.caisse = result.data
                }
            } else {
                app.notify("Veuillez bien specifier la date ou la la bonne plage ...", "m")
            }
        })
    };
    if (app.PRMS.dynl) {
        var ivs = $interval(function() {
            if (bl_query_day === true) {
                $scope.searchFDay()
            }
        }, config.INTERVAL_ETAT_CAIS_JOUR, false);
        var iv = $interval(function() {
            if (bl_query_day === false) {
                $scope.searchF()
            }
        }, config.INTERVAL_ETAT_CAIS_DATE, false);
        $scope.$on("$destroy", function() {
            $interval.cancel(ivs);
            $interval.cancel(iv)
        })
    }
}]);
sngs.controller("etaCaisPCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Etat",
        subtitle: "Caisse Periodique",
        show: true,
        model: {}
    };
    $rootScope.title = "Etat de la caisse Periodique";
    $rootScope.pageTitle = "Etat Caisse P";
    $scope.search = {};
    var bl_query_day;
    bl_query_day = true;
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
}]);
sngs.controller("etaMargPCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Etat",
        subtitle: "Marge Periodique",
        show: true,
        model: {}
    };
    $rootScope.title = "Etat de la marge Periodique";
    $rootScope.pageTitle = "Etat Marge P";
    $scope.search = {};
    var bl_query_day;
    bl_query_day = true;
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
}]);
sngs.controller("etaValStkCtrl", ["$scope", "$rootScope", "prmutils", "config", "$interval", function($scope, $rootScope, prmutils, config, $interval) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Etat",
        subtitle: "Valeurs Stock",
        show: true,
        model: {}
    };
    $rootScope.title = "Valeur du stock";
    $rootScope.pageTitle = "Valeur Stock";
    $scope.refresh = function() {
        var task;
        task = prmutils.getValStock();
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.valstocks = result.data
                }
            } else {
                app.notify("Votre Reseau est momentannement instable ...", "m")
            }
        })
    };
    $scope.refresh();
    if (app.PRMS.dynl) {
        var ivs = $interval(function() {
            if ($rootScope.pageTitle === "Valeur Stock") {
                $scope.refresh()
            }
        }, config.INTERVAL_ETAT_VAL_STK, false);
        $scope.$on("$destroy", function() {
            $interval.cancel(ivs)
        })
    }
}]);
sngs.controller("etaValAchatStkCtrl", ["$scope", "$rootScope", "prmutils", "config", "$interval", function($scope, $rootScope, prmutils, config, $interval) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Etat",
        subtitle: "Valeurs Stock Achat",
        show: true,
        model: {}
    };
    $rootScope.title = "Valeur du stock Achat";
    $rootScope.pageTitle = "Valeur Stock Achat";
    $scope.refresh = function() {
        var task;
        task = prmutils.getValStockAchat();
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.valstocks = result.data
                }
            } else {
                app.notify("Votre Reseau est momentannement instable ...", "m")
            }
        })
    };
    $scope.refresh();
    if (app.PRMS.dynl) {
        var ivs = $interval(function() {
            if ($rootScope.pageTitle === "Valeur du stock Achat") {
                $scope.refresh()
            }
        }, config.INTERVAL_ETAT_VAL_STK, false);
        $scope.$on("$destroy", function() {
            $interval.cancel(ivs)
        })
    }
}]);
sngs.controller("etaValStkDetCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Etat",
        subtitle: "Valeur Detaillee",
        show: true,
        model: {}
    };
    $rootScope.title = "Valeur detaillee du stock";
    $rootScope.pageTitle = "Valeur Detaillee Stock";
    $scope.search = {};
    task = prmutils.getMagasins();
    task.promise.then(function(result) {
        app.waiting.show = true;
        if (result.err === 0) {
            $scope.magasins = result.data;
            $scope.search.magasin = $scope.magasins[0].id_mag;
            app.waiting.show = false
        } else {
            app.waiting.show = false
        }
    })
}]);
sngs.controller("etaFichArtCtrl", ["$scope", "$rootScope", "config", "prmutils", function($scope, $rootScope, config, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Stock",
        subtitle: "Fiche article",
        show: true,
        model: {}
    };
    $rootScope.title = "Fiche article";
    $rootScope.pageTitle = "Fiche article";
    $scope.search = {};
    app.waiting.show = false;
    task = prmutils.getMagasins();
    task.promise.then(function(result) {
        app.waiting.show = false;
        if (result.err === 0) {
            $scope.magasins = result.data;
            $scope.search.magasin = $scope.magasins[-1].id_mag;
            app.waiting.show = false
        } else {
            app.waiting.show = false
        }
    });
    $scope.getart = function() {
        task = prmutils.getArticles();
        task.promise.then(function(result) {
            app.waiting.show = false;
            if (result.err === 0) {
                $scope.articles = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    
    $scope.searchF = function() {
        var task;
        task = prmutils.getEtatFicheArt($scope.search);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.fiches = result.data.details;
                    $scope.qtestk = result.data.qtestk;
                    if ($scope.result.data.r === 1) {
                        app.notify("Magasin/boutique/point de vente non defini ...", "m")
                    }
                }
            } else {
                app.notify("Veuillez bien verifier vos critere pour la fiche article ...", "m")
            }
        })
    }
}]);