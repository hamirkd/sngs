sngs.controller("etaDepCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Sorties de caisse",
        subtitle: "Etat depenses",
        show: true,
        model: {}
    };
    $rootScope.title = "etat des depenses";
    $rootScope.pageTitle = "Etat Depenses";
    $scope.search = {};
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1;
    var yyyy = today.getFullYear();
    var sss = today.getTime();
    if (dd < 10) {
        dd = "0" + dd
    }
    if (mm < 10) {
        mm = "0" + mm
    }
    today = dd + "/" + mm + "/" + yyyy;
    $scope.search.date_deb = today;
    $scope.gtd = function() {
        var task = prmutils.getTypeDepenses();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.type_depenses = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.gus = function() {
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
    $scope.vudep = function(fac) {
        var task = prmutils.vudep(fac);
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
    $scope.tvudep = function() {
        var task = prmutils.tvudep();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                for (var i = 0; i < $scope.depenses.length; i++) {
                    $scope.depenses[i].vu = 1
                }
                $rootScopedepnv = 0;
                app.waiting.show = false
            } else {
                app.waiting.show = false;
                app.notify("Ok...", "b")
            }
        })
    };
    $scope.searchF = function() {
        var task;
        task = prmutils.getEtatDepenses($scope.search);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {

                    $scope.depenses = result.data
                        // result.data.sort(function(a, b) {
                        //     console.log(a.date_dep, b.date_dep, a.date_dep > b.date_dep)
                        //     return a.date_dep > b.date_dep
                        // });
                    $scope.depenses.sort((a, b) => a.date_dep > b.date_dep);
                    // console.log($scope.depenses);
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
            total += parseInt(vente.mnt_dep)
        }
        return total
    };
    $scope.searchF()
}]);
sngs.controller("decaissDepCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Sortie de caisse",
        subtitle: "Depense",
        show: true,
        model: {}
    };
    $rootScope.title = "Depenses";
    $rootScope.pageTitle = "Depenses";
    $scope.depense = {};
    var datedep;
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1;
    var yyyy = today.getFullYear();
    var sss = today.getTime();
    if (dd < 10) {
        dd = "0" + dd
    }
    if (mm < 10) {
        mm = "0" + mm
    }
    datedep = dd + "/" + mm + "/" + yyyy;
    if (app.PRMS.resa === 0 || app.PRMS.resa === false) {
        $scope.depense.date_dep = datedep
    } else {
        var task = prmutils.getDs();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.depense.date_dep = result.data.datej;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    }
    $scope.gtd = function() {
        var task = prmutils.getTypeDepenses();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.type_depenses = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getDepenses = function() {
        var task = prmutils.getDepenses();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.depenses = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getDepenses();
    $scope.save = function(depense) {
        var task;
        if (!prmutils.isDate(depense.date_dep)) {
            app.notify("Le format de la date est incorrect", "m");
            return false
        }
        task = prmutils.saveDepense(depense);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.depense.mnt_dep = null;
                    $scope.getDepenses();
                    app.notify(result.message, "b")
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
            total += parseInt(vente.mnt_dep)
        }
        return total
    }
}]);
sngs.controller("etaCaissCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Caisse",
        subtitle: "Etat des Provisions",
        show: true,
        model: {}
    };
    $rootScope.title = "Caisse";
    $rootScope.pageTitle = "Etat-Provisions-Caisse";
    $scope.search = {};
    var datedep;
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1;
    var yyyy = today.getFullYear();
    var sss = today.getTime();
    if (dd < 10) {
        dd = "0" + dd
    }
    if (mm < 10) {
        mm = "0" + mm
    }
    datedep = dd + "/" + mm + "/" + yyyy;
    $scope.search.date_deb = datedep;
    $scope.gus = function() {
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
        task = prmutils.getEtatProvisions($scope.search);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.provisions = result.data
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
            total += parseInt(vente.mnt_cais)
        }
        return total
    };
    $scope.searchF()
}]);
sngs.controller("encaissCaisCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Caisse",
        subtitle: "Provision de caisse",
        show: true,
        model: {}
    };
    $rootScope.title = "Caisse";
    $rootScope.pageTitle = "Caisse";
    $scope.provision = {};
    var datedep;
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1;
    var yyyy = today.getFullYear();
    var sss = today.getTime();
    if (dd < 10) {
        dd = "0" + dd
    }
    if (mm < 10) {
        mm = "0" + mm
    }
    datedep = dd + "/" + mm + "/" + yyyy;
    $scope.provision.date_cais = datedep;
    $scope.getProvisions = function() {
        var task = prmutils.getProvisions();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.provisions = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getProvisions();
    $scope.save = function(provision) {
        var task;
        if (!prmutils.isDate(provision.date_cais)) {
            app.notify("Le format de la date est incorrecte", "m");
            return false
        }
        task = prmutils.saveProvision(provision);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.provision.mnt_cais = null;
                    $scope.getProvisions();
                    app.notify(result.message, "b")
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
            total += parseInt(vente.mnt_cais)
        }
        return total
    }
}]);
sngs.controller("etaVersCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Sorties de caisse",
        subtitle: "Etat versements",
        show: true,
        model: {}
    };
    $rootScope.title = "etat des versements";
    $rootScope.pageTitle = "Etat Versements";
    $scope.search = {};
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1;
    var yyyy = today.getFullYear();
    var sss = today.getTime();
    if (dd < 10) {
        dd = "0" + dd
    }
    if (mm < 10) {
        mm = "0" + mm
    }
    today = dd + "/" + mm + "/" + yyyy;
    $scope.search.date_deb = today;
    $scope.gbq = function() {
        var task = prmutils.getBanques();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.banques = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.gus = function() {
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
        task = prmutils.getEtatVersements($scope.search);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.versements = result.data
                }
            } else {
                app.notify("Oupss!! Connexion instable...", "m")
            }
        })
    };
    $scope.getTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.filtered.length; i++) {
            var vente = $scope.filtered[i];
            total += parseInt(vente.mnt_vrsmnt)
        }
        return total
    };
    $scope.searchF()
}]);
sngs.controller("decaissVersCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Sortie de caisse",
        subtitle: "Versement",
        show: true,
        model: {}
    };
    $rootScope.title = "Versements";
    $rootScope.pageTitle = "Versements";
    $scope.versement = {};
    var datevrsmnt;
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
    datevrsmnt = dd + "/" + mm + "/" + yyyy;
    if (app.PRMS.resa === 0 || app.PRMS.resa === false) {
        $scope.versement.date_vrsmnt = datevrsmnt
    } else {
        var task = prmutils.getDs();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.versement.date_vrsmnt = result.data.datej;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    }
    $scope.gbq = function() {
        var task = prmutils.getBanques();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.banques = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getVersements = function() {
        var task = prmutils.getVersements();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.versements = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getVersements();
    $scope.save = function(versement) {
        var task;
        if (!prmutils.isDate(versement.date_vrsmnt)) {
            app.notify("Le format de la date est incorrect", "m");
            return false
        }
        task = prmutils.saveVersement(versement);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.versement.mnt_vrsmnt = null;
                    $scope.getVersements();
                    app.notify(result.message, "b")
                }
            } else {
                app.notify("Oups! Connexion instable ...", "m")
            }
        })
    };
    $scope.getTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.filtered.length; i++) {
            var vente = $scope.filtered[i];
            total += parseInt(vente.mnt_vrsmnt)
        }
        return total
    }
}]);