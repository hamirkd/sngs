sngs.controller("etatReglementCltGrtCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Reglements",
        subtitle: "Etat Avances Garanties",
        show: true,
        model: {}
    };
    $rootScope.title = "Liste des avances Garanties";
    $rootScope.pageTitle = "Etat avances Garanties";
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
    var task = prmutils.etatReglementsGrtLastClt();
    task.promise.then(function(result) {
        app.waiting.show = true;
        if (result.err === 0) {
            $scope.creances = result.data;
            app.waiting.show = false
        } else {
            app.waiting.show = false
        }
    });
    $scope.searchF = function() {
        var task;
        task = prmutils.etatReglementsGrtClt($scope.search);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.creances = result.data
                }
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.getTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.filtered.length; i++) {
            var vente = $scope.filtered[i];
            total += parseInt(vente.mnt_paye_crce_clnt)
        }
        return total
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
    };
    $scope.vucr = function(fac) {
        var task = prmutils.vucr(fac);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                fac.vu = 1;
                app.waiting.show = false
            } else {
                app.waiting.show = false;
                app.notify("Ok...", "b")
            }
        })
    };
    $scope.tvucr = function() {
        var task = prmutils.tvucr();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                for (var i = 0; i < $scope.creances.length; i++) {
                    $scope.creances[i].vu = 1
                }
                $rootScope.crnv = 0;
                app.waiting.show = false
            } else {
                app.waiting.show = false;
                app.notify("Ok...", "b")
            }
        })
    };
    $scope.fullSearch = function() {
        $scope.loading = true;
        var task;
        if ($scope.fullSearchText.length > 1) {
            task = prmutils.queryEtatRegGrtClt($scope.fullSearchText)
        } else {
            task = prmutils.etatReglementsGrtLastClt()
        }
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.creances = result.data
                }
                $scope.loading = false
            } else {
                app.notify("ok ...", "b");
                $scope.loading = false
            }
        })
    }
}]);
sngs.controller("etatReglementCltCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Reglements",
        subtitle: "Etat reglements Clients",
        show: true,
        model: {}
    };
    $rootScope.title = "Liste des reglements clients";
    $rootScope.pageTitle = "Etat Reglt Clients";
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
    var task = prmutils.etatReglementsLastClt();
    task.promise.then(function(result) {
        app.waiting.show = true;
        if (result.err === 0) {
            $scope.creances = result.data;
            app.waiting.show = false
        } else {
            app.waiting.show = false
        }
    });
    $scope.searchF = function() {
        var task;
        task = prmutils.etatReglementsClt($scope.search);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.creances = result.data
                }
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.getTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.filtered.length; i++) {
            var vente = $scope.filtered[i];
            total += parseInt(vente.mnt_paye_crce_clnt)
        }
        return total
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
    };
    $scope.vucr = function(fac) {
        var task = prmutils.vucr(fac);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                fac.vu = 1;
                app.waiting.show = false
            } else {
                app.waiting.show = false;
                app.notify("Ok...", "b")
            }
        })
    };
    $scope.tvucr = function() {
        var task = prmutils.tvucr();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                for (var i = 0; i < $scope.creances.length; i++) {
                    $scope.creances[i].vu = 1
                }
                $rootScope.crnv = 0;
                app.waiting.show = false
            } else {
                app.waiting.show = false;
                app.notify("Ok...", "b")
            }
        })
    };
    $scope.fullSearch = function() {
        $scope.loading = true;
        var task;
        if ($scope.fullSearchText.length > 1) {
            task = prmutils.queryEtatRegClt($scope.fullSearchText)
        } else {
            task = prmutils.etatReglementsLastClt()
        }
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.creances = result.data
                }
                $scope.loading = false
            } else {
                app.notify("ok ...", "b");
                $scope.loading = false
            }
        })
    }
}]);
sngs.controller("etatRetardPayementCltCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Reglements",
        subtitle: "Etat Retards Payements Clients",
        show: true,
        model: {}
    };
    $rootScope.title = "Liste des Factures clients a regler";
    $rootScope.pageTitle = "Etat Retard Payement";
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
    var task = prmutils.etatRetardPayementsLastClt();
    task.promise.then(function(result) {
        app.waiting.show = true;
        if (result.err === 0) {
            $scope.creances = result.data;
            app.waiting.show = false
        } else {
            app.waiting.show = false
        }
    });
    $scope.searchF = function() {
        var task;
        task = prmutils.etatRetardPayementsClt($scope.search);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.creances = result.data
                }
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.getTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.filtered.length; i++) {
            var vente = $scope.filtered[i];
            total += parseInt(vente.reste)
        }
        return total
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
    };
    $scope.fullSearch = function() {
        $scope.loading = true;
        var task;
        if ($scope.fullSearchText.length > 1) {
            task = prmutils.queryEtatRetardPayements($scope.fullSearchText)
        } else {
            task = prmutils.etatRetardPayementsLastClt()
        }
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.creances = result.data
                }
                $scope.loading = false
            } else {
                app.notify("ok ...", "b");
                $scope.loading = false
            }
        })
    }
}]);
sngs.controller("etatReglementFrnsCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Reglements",
        subtitle: "Etat reglements Fournisseurs",
        show: true,
        model: {}
    };
    $rootScope.title = "Liste des reglements fournisseurs";
    $rootScope.pageTitle = "Etat Reglt Fournisseurs";
    $scope.search = {};
    $scope.getfrns = function() {
        var task = prmutils.getaFournisseurs();
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
    task = prmutils.etatReglementsLastFrns();
    task.promise.then(function(result) {
        app.waiting.show = true;
        if (result.err === 0) {
            $scope.dettes = result.data;
            app.waiting.show = false
        } else {
            app.waiting.show = false
        }
    });
    $scope.searchF = function() {
        var task;
        task = prmutils.etatReglementsFrns($scope.search);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.dettes = result.data
                }
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.getTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.filtered.length; i++) {
            var vente = $scope.filtered[i];
            total += parseInt(vente.mnt_paye_dette_frns)
        }
        return total
    };
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
sngs.controller("reglementFrnsCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Reglements",
        subtitle: "Reglement Fournisseur",
        show: true,
        model: {}
    };
    $rootScope.title = "Dette Fournisseur";
    $rootScope.pageTitle = "Reglement Fournisseur";
    $scope.appreglement = {};
    var datedette;
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
    datedette = dd + "/" + mm + "/" + yyyy;
    if (app.PRMS.resa === 0 || app.PRMS.resa === false) {
        $scope.appreglement.date_dette_frns = datedette
    } else {
        var task = prmutils.getDs();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.appreglement.date_dette_frns = result.data.datej;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    }
    $scope.appreglement.ref_dette_frns = "-";
    $scope.getDettesFournisseurs = function() {
        var task = prmutils.getDettesFournisseurs();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.dettes = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getDettesFournisseurs();
    $scope.save = function() {
        var task;
        ObjRegAvance = {
            id_appro: $scope.appreglement.dette_frns,
            id_frns: $scope.dette_details.id_frns,
            mnt_versement: $scope.appreglement.mnt_versement,
            ref: $scope.appreglement.ref_dette_frns,
            date_dette_frns: $scope.appreglement.date_dette_frns,
            mnt_dette: $scope.dette_details.mnt_revient_appro
        };
        if (!prmutils.isDate($scope.appreglement.date_dette_frns)) {
            app.notify("Le format de la date est incorrect", "m");
            return false
        }
        task = prmutils.paidReglementFrns(ObjRegAvance);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.getDetteDetails(ObjRegAvance.id_appro);
                    $scope.getDettesFournisseurs();
                    app.notify(result.message, "b")
                }
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.getDetteDetails = function(dette) {
        $scope.appreglement.mnt_versement = parseInt(0);
        task = prmutils.getDetteDetails(dette);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.dette_details = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        });
        det = {
            id_appro: dette
        };
        task = prmutils.showApproDetails(det);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.details = result.data
            } else {}
        })
    };
    $scope.validMnt = function() {
        $scope.appreglement.mnt_versement = (parseFloat($scope.appreglement.mnt_versement) + parseFloat($scope.dette_details.som_verse_dette) <= parseFloat($scope.dette_details.mnt_revient_appro)) ? parseFloat($scope.appreglement.mnt_versement) : parseFloat(0)
    };
    $scope.emptyForm = function() {
        $scope.appreglement.mnt_versement = parseInt(0);
        $scope.appreglement = {}
    }
}]);
sngs.controller("reglementgFrnsCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Reglements",
        subtitle: "Reglement Fournisseur par Fournisseur",
        show: true,
        model: {}
    };
    $rootScope.title = "Dette Fournisseur /Fournissseur";
    $rootScope.pageTitle = "Reglement Fournisseur /Fournisseur";
    $scope.appreglement = {};
    var datedette;
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
    datedette = dd + "/" + mm + "/" + yyyy;
    if (app.PRMS.resa === 0 || app.PRMS.resa === false) {
        $scope.appreglement.date_dette_frns = datedette
    } else {
        var task = prmutils.getDs();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.appreglement.date_dette_frns = result.data.datej;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    }
    $scope.appreglement.ref_dette_frns = "-";
    $scope.getDettesFournisseurs = function() {
        var task = prmutils.getDettesgFournisseurs();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.dettes = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getDettesFournisseurs();
    $scope.save = function() {
        var task;
        ObjRegAvance = {
            id_frns: $scope.appreglement.dette_frns,
            mnt_versement: $scope.appreglement.mnt_versement,
            ref: $scope.appreglement.ref_dette_frns,
            date_dette_frns: $scope.appreglement.date_dette_frns,
            mnt_dette: $scope.dette_details.mnt_revient_appro
        };
        if (!prmutils.isDate($scope.appreglement.date_dette_frns)) {
            app.notify("Le format de la date est incorrect", "m");
            return false
        }
        task = prmutils.paidReglementgFrns(ObjRegAvance);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.getDettegDetails(ObjRegAvance.id_frns);
                    $scope.getDettesFournisseurs();
                    app.notify(result.message, "b")
                }
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.getDettegDetails = function(dette) {
        $scope.appreglement.mnt_versement = parseInt(0);
        task = prmutils.getDettegDetails(dette);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.dette_details = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        });
        det = {
            id_frns: dette
        };
        task = prmutils.showApprogDetails(det);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.details = result.data
            } else {}
        })
    };
    $scope.validMnt = function() {
        $scope.appreglement.mnt_versement = (parseFloat($scope.appreglement.mnt_versement) + parseFloat($scope.dette_details.som_verse_dette) <= parseFloat($scope.dette_details.mnt_revient_appro)) ? parseFloat($scope.appreglement.mnt_versement) : parseFloat(0)
    };
    $scope.emptyForm = function() {
        $scope.appreglement.mnt_versement = parseInt(0);
        $scope.appreglement = {}
    }
}]);
sngs.controller("reglementCltCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    $scope.working = false;
    app.navbar.show = true;
    app.title = {
        text: "Reglements",
        subtitle: "Reglement Credit Client",
        show: true,
        model: {}
    };
    $rootScope.title = "Reglement Credit client";
    $rootScope.pageTitle = "Reglement Credit Client";
    $scope.appreglement = {};
    var datecrce;
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
    datecrce = dd + "/" + mm + "/" + yyyy;
    if (app.PRMS.resa === 0 || app.PRMS.resa === false) {
        $scope.appreglement.date_crce_clnt = datecrce
    } else {
        var task = prmutils.getDs();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.appreglement.date_crce_clnt = result.data.datej;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    }
    $scope.appreglement.ref_crce_clnt = "-";
    $scope.getCreancesClients = function() {
        var task = prmutils.getCreancesClients();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.creances = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getCreancesClients();
    $scope.save = function() {
        var task;
        ObjRegAvance = {
            id_fac: $scope.appreglement.creance_clt,
            id_clt: $scope.creance_details.id_clt,
            mnt_versement: $scope.appreglement.mnt_versement,
            date_crce_clnt: $scope.appreglement.date_crce_clnt,
            mnt_credit: $scope.creance_details.crdt_fact,
            ref: $scope.appreglement.ref_crce_clnt,
            mnt_rem_act: $scope.creance_details.remise_vnt_fact
        };
        if (!prmutils.isDate($scope.appreglement.date_crce_clnt)) {
            app.notify("Le format de la date est incorrect", "m");
            return false
        }
        task = prmutils.paidReglementClt(ObjRegAvance);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.getCreanceDetails(ObjRegAvance.id_fac);
                    $scope.getCreancesClients();
                    app.notify(result.message, "b")
                }
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.saveRemise = function() {
        var task;
        ObjRegAvance = {
            id_fac: $scope.appreglement.creance_clt,
            mnt_remise: $scope.appreglement.mnt_remise,
            mnt_credit: $scope.creance_details.crdt_fact,
            mnt_rem_act: $scope.creance_details.remise_vnt_fact
        };
        task = prmutils.paidRemiseClt(ObjRegAvance);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.getCreanceDetails(ObjRegAvance.id_fac);
                    $scope.getCreancesClients();
                    app.notify(result.message, "b");
                    $scope.appreglement.mnt_remise = parseInt(0)
                }
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.getCreanceDetails = function(creance) {
        $scope.working = true;
        $scope.creance_details = {};
        $scope.appreglement.mnt_versement = parseInt(0);
        task = prmutils.getCreanceDetails(creance);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.creance_details = result.data;
                app.waiting.show = false;
                $scope.working = false
            } else {
                app.waiting.show = false
            }
        });
        fact = {
            id_fact: creance
        };
        task = prmutils.showFactureDetails(fact);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.details = result.data
            } else {}
        })
    };
    $scope.validMnt = function() {
        $scope.appreglement.mnt_remise = parseFloat(0);
        $scope.appreglement.mnt_versement = (parseFloat($scope.appreglement.mnt_versement) + parseFloat($scope.creance_details.som_verse_crdt) <= parseFloat($scope.creance_details.crdt_fact)) ? parseFloat($scope.appreglement.mnt_versement) : parseFloat(0)
    };
    $scope.validRem = function() {
        $scope.appreglement.mnt_versement = parseFloat(0);
        $scope.appreglement.mnt_remise = (parseFloat($scope.appreglement.mnt_remise) <= parseFloat($scope.creance_details.crdt_fact) - parseFloat($scope.creance_details.som_verse_crdt) - parseFloat($scope.appreglement.mnt_versement)) ? parseFloat($scope.appreglement.mnt_remise) : parseFloat(0)
    };
    $scope.emptyForm = function() {
        $scope.appreglement.mnt_versement = parseInt(0);
        $scope.appreglement.mnt_remise = parseInt(0);
        $scope.appreglement = {}
    }
}]);
sngs.controller("reglementGrtCltCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    $scope.working = false;
    app.navbar.show = true;
    app.title = {
        text: "Reglements",
        subtitle: "Reglement garanties Client",
        show: true,
        model: {}
    };
    $rootScope.title = "Reglement Garanties client";
    $rootScope.pageTitle = "Reglement Garanties Client";
    $scope.appreglement = {};
    var datecrce;
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
    datecrce = dd + "/" + mm + "/" + yyyy;
    if (app.PRMS.resa === 0 || app.PRMS.resa === false) {
        $scope.appreglement.date_crce_clnt = datecrce
    } else {
        var task = prmutils.getDs();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.appreglement.date_crce_clnt = result.data.datej;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    }
    $scope.appreglement.ref_crce_clnt = "-";
    $scope.getCreancesClients = function() {
        var task = prmutils.getCreancesGrtClients();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.creances = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getCreancesClients();
    $scope.save = function() {
        var task;
        ObjRegAvance = {
            id_fac: $scope.appreglement.creance_clt,
            id_clt: $scope.creance_details.id_clt,
            mnt_versement: $scope.appreglement.mnt_versement,
            date_crce_clnt: $scope.appreglement.date_crce_clnt,
            mnt_credit: $scope.creance_details.crdt_fact,
            ref: $scope.appreglement.ref_crce_clnt,
            mnt_rem_act: $scope.creance_details.remise_vnt_fact
        };
        if (!prmutils.isDate($scope.appreglement.date_crce_clnt)) {
            app.notify("Le format de la date est incorrect", "m");
            return false
        }
        task = prmutils.paidReglementClt(ObjRegAvance);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.getCreanceDetails(ObjRegAvance.id_fac);
                    $scope.getCreancesClients();
                    app.notify(result.message, "b")
                }
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.saveRemise = function() {
        var task;
        ObjRegAvance = {
            id_fac: $scope.appreglement.creance_clt,
            mnt_remise: $scope.appreglement.mnt_remise,
            mnt_credit: $scope.creance_details.crdt_fact,
            mnt_rem_act: $scope.creance_details.remise_vnt_fact
        };
        task = prmutils.paidRemiseClt(ObjRegAvance);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.getCreanceDetails(ObjRegAvance.id_fac);
                    $scope.getCreancesClients();
                    app.notify(result.message, "b");
                    $scope.appreglement.mnt_remise = parseInt(0)
                }
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.getCreanceDetails = function(creance) {
        $scope.working = true;
        $scope.appreglement.mnt_versement = parseInt(0);
        $scope.creance_details = {};
        task = prmutils.getCreanceGrtDetails(creance);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.creance_details = result.data;
                app.waiting.show = false;
                $scope.working = false
            } else {
                app.waiting.show = false
            }
        });
        fact = {
            id_fact: creance
        };
        task = prmutils.showFactureDetails(fact);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.details = result.data
            } else {}
        })
    };
    $scope.validMnt = function() {
        $scope.appreglement.mnt_remise = parseFloat(0);
        $scope.appreglement.mnt_versement = (parseFloat($scope.appreglement.mnt_versement) + parseFloat($scope.creance_details.som_verse_crdt) <= parseFloat($scope.creance_details.crdt_fact)) ? parseFloat($scope.appreglement.mnt_versement) : parseFloat(0)
    };
    $scope.validRem = function() {
        $scope.appreglement.mnt_versement = parseFloat(0);
        $scope.appreglement.mnt_remise = (parseFloat($scope.appreglement.mnt_remise) <= parseFloat($scope.creance_details.crdt_fact) - parseFloat($scope.creance_details.som_verse_crdt) - parseFloat($scope.appreglement.mnt_versement)) ? parseFloat($scope.appreglement.mnt_remise) : parseFloat(0)
    };
    $scope.emptyForm = function() {
        $scope.appreglement.mnt_versement = parseInt(0);
        $scope.appreglement.mnt_remise = parseInt(0);
        $scope.appreglement = {}
    }
}]);
sngs.controller("reglementgCltCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Reglements",
        subtitle: "Reglement Credit Client/Client",
        show: true,
        model: {}
    };
    $rootScope.title = "Reglement Credit client/Client";
    $rootScope.pageTitle = "Reglement Credit Client/Client";
    $scope.appreglement = {};
    var datecrce;
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
    datecrce = dd + "/" + mm + "/" + yyyy;
    if (app.PRMS.resa === 0 || app.PRMS.resa === false) {
        $scope.appreglement.date_crce_clnt = datecrce
    } else {
        var task = prmutils.getDs();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.appreglement.date_crce_clnt = result.data.datej;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    }
    $scope.appreglement.ref_crce_clnt = "-";
    $scope.getCreancesgClients = function() {
        if (!app.userPfl.mg) {
            app.notify("Veuillez choisir un magasin", "m");
            return;
        }
        var task = prmutils.getCreancesgClients();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.creances = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getCreancesgClients();
    $scope.save = function() {
        var task;
        ObjRegAvance = {
            id_clt: $scope.appreglement.creance_clt,
            mnt_versement: $scope.appreglement.mnt_versement,
            ref: $scope.appreglement.ref_crce_clnt,
            date_crce_clnt: $scope.appreglement.date_crce_clnt,
            mnt_credit: $scope.creance_details.mnt_creditg
        };
        if (!prmutils.isDate($scope.appreglement.date_crce_clnt)) {
            app.notify("Le format de la date est incorrect", "m");
            return false
        }
        task = prmutils.paidReglementgClt(ObjRegAvance);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.getCreancegDetails(ObjRegAvance.id_clt);
                    $scope.getCreancesgClients();
                    app.notify(result.message, "b")
                }
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.getCreancegDetails = function(creancier) {
        $scope.appreglement.mnt_versement = parseInt(0);
        task = prmutils.getCreancegDetails(creancier);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.creance_details = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        });
        clt = {
            id_clt: creancier
        };
        task = prmutils.showFacturegDetails(clt);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.details = result.data
            } else {}
        })
    };
    $scope.validMnt = function() {
        $scope.appreglement.mnt_versement = (parseFloat($scope.appreglement.mnt_versement) + parseFloat($scope.creance_details.som_verseg) <= parseFloat($scope.creance_details.mnt_creditg)) ? parseFloat($scope.appreglement.mnt_versement) : parseFloat(0)
    };
    $scope.emptyForm = function() {
        $scope.appreglement.mnt_versement = parseInt(0);
        $scope.appreglement = {}
    }

    task = prmutils.getAllMagasins();
    task.promise.then(function(result) {
        app.waiting.show = true;
        if (result.err === 0) {
            $scope.magasins = result.data;
            app.waiting.show = false
        } else {
            app.waiting.show = false
        }
    });
}]);
sngs.controller("reglementGrtgCltCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Reglements",
        subtitle: "Reglement Garanties Client/Client",
        show: true,
        model: {}
    };
    $rootScope.title = "Reglement Garantie client/Client";
    $rootScope.pageTitle = "Reglement Garantie Client/Client";
    $scope.appreglement = {};
    var datecrce;
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
    datecrce = dd + "/" + mm + "/" + yyyy;
    if (app.PRMS.resa === 0 || app.PRMS.resa === false) {
        $scope.appreglement.date_crce_clnt = datecrce
    } else {
        var task = prmutils.getDs();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.appreglement.date_crce_clnt = result.data.datej;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    }
    $scope.appreglement.ref_crce_clnt = "-";
    $scope.getCreancesgClients = function() {
        var task = prmutils.getCreancesGrtgClients();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.creances = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getCreancesgClients();
    $scope.save = function() {
        var task;
        ObjRegAvance = {
            id_clt: $scope.appreglement.creance_clt,
            mnt_versement: $scope.appreglement.mnt_versement,
            ref: $scope.appreglement.ref_crce_clnt,
            date_crce_clnt: $scope.appreglement.date_crce_clnt,
            mnt_credit: $scope.creance_details.mnt_creditg
        };
        if (!prmutils.isDate($scope.appreglement.date_crce_clnt)) {
            app.notify("Le format de la date est incorrect", "m");
            return false
        }
        task = prmutils.paidReglementgGrtClt(ObjRegAvance);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.getCreancegDetails(ObjRegAvance.id_clt);
                    $scope.getCreancesgClients();
                    app.notify(result.message, "b")
                }
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.getCreancegDetails = function(creancier) {
        $scope.appreglement.mnt_versement = parseInt(0);
        task = prmutils.getCreancegGrtDetails(creancier);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.creance_details = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        });
        clt = {
            id_clt: creancier
        };
        task = prmutils.showFacturegGrtDetails(clt);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.details = result.data
            } else {}
        })
    };
    $scope.validMnt = function() {
        $scope.appreglement.mnt_versement = (parseFloat($scope.appreglement.mnt_versement) + parseFloat($scope.creance_details.som_verseg) <= parseFloat($scope.creance_details.mnt_creditg)) ? parseFloat($scope.appreglement.mnt_versement) : parseFloat(0)
    };
    $scope.emptyForm = function() {
        $scope.appreglement.mnt_versement = parseInt(0);
        $scope.appreglement = {}
    }
}]);