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
    $scope.search.date_fin = today;
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
    // etat
    // Autorisé = 2
    // rejeter = 3
    // vu = 1
    $scope.vudep = function(fac, etat = 1) {
        fac['action'] = etat;
        fac['motif'] = "";
        if (etat == 3 || etat == 6) {
            var vls = prompt("Le motif du rejet SVP !! ", "");
            if (!vls) return
            vls = vls.trim();
            if (vls) {
                fac['motif'] = vls;
            }
        }
        if (fac.montant) {
            app.notify(result.message, "m");
        }
        var task = prmutils.vudep(fac);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                fac.vu = etat;
                app.waiting.show = false
                if (etat == 3)
                    app.notify(result.message, "m")
                else app.notify(result.message, "b")
                $scope.searchF();
            } else {
                app.waiting.show = false;
                app.notify(result.message, "m")
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
    $scope.downloadJSONAsCSV = function() {
        // Convert JSON data to CSV
        let csvData = app.jsonToCsv($scope.depenses); // Add .items.data
        // Create a CSV file and allow the user to download it
        let blob = new Blob([csvData], { type: 'text/csv' });
        let url = window.URL.createObjectURL(blob);
        let a = document.createElement('a');
        a.href = url;
        a.download = 'data.csv';
        document.body.appendChild(a);
        a.click();
    }
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

sngs.controller("etatRetourCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Retour des articles",
        subtitle: "Etat des retours",
        show: true,
        model: {}
    };
    $rootScope.title = "Etat des retours";
    $rootScope.pageTitle = "Etat retour";
    $scope.search = {};
    $scope.retour = {};
    var today = new Date();
    var today2 = new Date();
    today2.setMonth(today2.getMonth() + 1)
    var dd = today.getDate();
    var mm = today.getMonth();
    var mmm = today2.getMonth();
    var yyyy = today.getFullYear();
    var sss = today.getTime();
    if (dd < 10) {
        dd = "0" + dd
    }
    if (mm < 10) {
        mm = "0" + mm
    }
    today = dd + "/" + mm + "/" + yyyy;
    today2 = dd + "/" + mmm + "/" + yyyy;
    $scope.search.date_deb = today;
    $scope.search.date_fin = today2;
    $scope.retour = { date_retour: today2 };

    $scope.save = function(retour) {
        var task;
        if (!prmutils.isDate(retour.date_retour)) {
            app.notify("Le format de la date est incorrect", "m");
            return false
        }
        task = prmutils.saveRetourArticle(retour);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.retour.montant = null;
                    $scope.searchF();
                    app.notify(result.message, "b");
                    $scope.reinitialiser();
                }
            } else {
                app.notify("Une erreur est survenue ..." + result.message, "m")
            }
        })
    };
    
    $scope.gclt = function() {
        var task = prmutils.getOrClients();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.clients = result.data;
                for(let client of $scope.clients) {
                    if(client.tel_clt && client.tel_clt.trim().length>0)
                    {
                        client.nom_clt = client.nom_clt + " [" + client.tel_clt+"]"
                    }
                }
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.gclt();

    $scope.getcUsers = function() {
        var task = prmutils.getcUsers();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.users = result.data;
                for (let user of $scope.users) {
                    user['nom_prenom_user'] = user.nom_user + ' ' + user.prenom_user + ' - ' + user.code_user;
                }
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getcUsers();

    $scope.getMyMagasinsAcces = function() {
        if (app.userPfl.pfl == 1 || app.userPfl.pfl == 0) {
            task = prmutils.getMagasins();
        } else {
            task = prmutils.getMyMagasinsAcces();
        }
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.myMagasinsAcces = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        });
    }
    $scope.getMyMagasinsAcces();

    $scope.gus = function() {
        task = prmutils.getcUsers();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.users = result.data;
                for (let user of $scope.users) {
                    user['nom_prenom_user'] = '[' + user.code_user + '] ' + user.nom_user + ' ' + user.prenom_user;
                }
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.gus();
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

    $scope.reinitialiser = function() {
        $scope.retour = { date_retour: today2 };
    };
    $scope.getart();
    $scope.searchF = function() {
        var task;
        task = prmutils.getEtatRetourArticles($scope.search);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m");
                } else {
                    $scope.retours = result.data;
                    $scope.retours.sort((a, b) => a.date_retour > b.date_retour);
                }
            } else {
                app.notify("Une erreur est survenue ...", "m")
            }
        })
    };
    $scope.getTotal = function() {
        var totalQuantite = 0;
        var totalMontant = 0;
        for (var i = 0; i < $scope.filtered.length; i++) {
            var retour = $scope.filtered[i];
            totalMontant += parseInt(retour.montant)
            totalQuantite += parseInt(retour.quantite)
        }
        return { totalQuantite: totalQuantite, totalMontant: totalMontant };
    };
    $scope.searchF()
}]);


sngs.controller("etatDechargeCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Decharge",
        subtitle: "Etat des decharges",
        show: true,
        model: {}
    };
    $rootScope.title = "Etat des decharges";
    $rootScope.pageTitle = "Etat decharge";
    $scope.search = {};
    $scope.decharge = {};
    var today = new Date();
    var today2 = new Date();
    today2.setMonth(today2.getMonth() + 1)
    var dd = today.getDate();
    var mm = today.getMonth();
    var mmm = today2.getMonth();
    var yyyy = today.getFullYear();
    var sss = today.getTime();
    if (dd < 10) {
        dd = "0" + dd
    }
    if (mm < 10) {
        mm = "0" + mm
    }
    today = dd + "/" + mm + "/" + yyyy;
    today2 = dd + "/" + mmm + "/" + yyyy;
    $scope.search.date_deb = today;
    $scope.search.date_fin = today2;
    $scope.decharge = { date_decharge: today2 };

    $scope.save = function(decharge) {
        var task;
        if (!prmutils.isDate(decharge.date_decharge)) {
            app.notify("Le format de la date est incorrect", "m");
            return false
        }
        task = prmutils.saveDecharge(decharge);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.decharge.montant = null;
                    $scope.searchF();
                    app.notify(result.message, "b");
                    $scope.reinitialiser();
                }
            } else {
                app.notify("Une erreur est survenue ..." + result.message, "m")
            }
        })
    };
    

    $scope.reinitialiser = function() {
        $scope.decharge = { date_decharge: today2 };
    };
    $scope.searchF = function() {
        var task;
        task = prmutils.getEtatDecharges($scope.search);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m");
                } else {
                    $scope.decharges = result.data;
                }
            } else {
                app.notify("Une erreur est survenue ...", "m")
            }
        });
    };
    $scope.getTotal = function() {
        var totalMontant = 0;
        for (var i = 0; i < $scope.filtered.length; i++) {
            var decharge = $scope.filtered[i];
            totalMontant += parseInt(decharge.montant);
        }
        return { totalQuantite: totalQuantite, totalMontant: totalMontant };
    };
    $scope.searchF()
}]);

sngs.controller("etatPaiementCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Etat des paiements",
        subtitle: "Etat paiement",
        show: true,
        model: {}
    };
    $rootScope.title = "Etat des paiements";
    $rootScope.pageTitle = "Etat Paiements";
    $scope.search = {};
    var today = new Date();
    var today2 = new Date();
    today2.setMonth(today.getMonth() + 1);
    
    var dd = today.getDate();
    var mm = today.getMonth();
    var mmfin = today2.getMonth();
    var mmdebut = today.getMonth();
    var yyyydebut = today.getFullYear();
    var yyyyfin = today2.getFullYear();
    var sss = today.getTime();
    if (dd < 10) {
        dd = "0" + dd
    }
    if (mm < 10) {
        mm = "0" + mm
    }
    if (mmdebut < 10) {
        mmdebut = "0" + mmdebut
    }
    if (mmfin < 10) {
        mmfin = "0" + mmfin
    }
    if (today.getMonth() == 0) {
        today.setMonth(today.getMonth() - 1);
        mmdebut = today.getMonth();
        mmdebut = mmdebut + 1;
        yyyydebut = today.getFullYear();
    }
    if (mmfin == "00") {
        mmfin = "01";
    }
    today = dd + "/" + mmdebut + "/" + yyyydebut;
    today2 = dd + "/" + mmfin + "/" + yyyyfin;
    $scope.search.date_deb = today;
    $scope.search.date_fin = today2;
    
    /*$scope.gus = function() {
        task = prmutils.getUsers();
        task.promise.then(function(result) {
            console.log(result)
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.users = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };*/
     
    $scope.downloadJSONAsCSV = function() {
        // Convert JSON data to CSV
        let csvData = app.jsonToCsv($scope.paiements); // Add .items.data
        // Create a CSV file and allow the user to download it
        let blob = new Blob([csvData], { type: 'text/csv' });
        let url = window.URL.createObjectURL(blob);
        let a = document.createElement('a');
        a.href = url;
        a.download = 'data.csv';
        document.body.appendChild(a);
        a.click();
    }

    $scope.searchF = function() {
        var task;
        task = prmutils.getEtatPaiements($scope.search);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.paiements = result.data
                    $scope.paiements.sort((a, b) => a.date_paiement > b.date_paiement);
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
            total += parseInt(vente.montant)
        }
        return total
    };
    $scope.searchF();
    $scope.getMyMagasinsAcces = function() {
        if (app.userPfl.pfl == 1 || app.userPfl.pfl == 0) {
            task = prmutils.getMagasins();
        } else {
            task = prmutils.getMyMagasinsAcces();
        }
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.myMagasinsAcces = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        });
    }
    $scope.getMyMagasinsAcces();

    $scope.gus = function() {
        task = prmutils.getcUsers();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                console.log(result.data)
                $scope.users = result.data;
                for (let user of $scope.users) {
                    user['nom_prenom_user'] = '[' + user.code_user + '] ' + user.nom_user + ' ' + user.prenom_user;
                }
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.gus();
    
}]);
sngs.controller("etatDemandeCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Expression de besoin",
        subtitle: "Etat demandes",
        show: true,
        model: {}
    };
    $rootScope.title = "Etat des demandes";
    $rootScope.pageTitle = "Etat Demandes";
    $scope.search = {};
    var today = new Date();
    var today2 = new Date();
    today2.setMonth(today.getMonth() + 1);
    
    var dd = today.getDate();
    var mm = today.getMonth();
    var mmfin = today2.getMonth();
    var mmdebut = today.getMonth();
    var yyyydebut = today.getFullYear();
    var yyyyfin = today2.getFullYear();
    var sss = today.getTime();
    if (dd < 10) {
        dd = "0" + dd
    }
    if (mm < 10) {
        mm = "0" + mm
    }
    if (mmdebut < 10) {
        mmdebut = "0" + mmdebut
    }
    if (mmfin < 10) {
        mmfin = "0" + mmfin
    }
    if (today.getMonth() == 0) {
        today.setMonth(today.getMonth() - 1);
        mmdebut = today.getMonth();
        mmdebut = mmdebut + 1;
        yyyydebut = today.getFullYear();
    }
    today = dd + "/" + mmdebut + "/" + yyyydebut;
    today2 = dd + "/" + mmfin + "/" + yyyyfin;
    $scope.search.date_deb = today;
    $scope.search.date_fin = today2;
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
            console.log(result)
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.users = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    // etat
    // Autorisé = 2
    // rejeter = 3
    // vu = 1
    $scope.vudep = function(fac, etat = 1) {
        fac['action'] = etat;
        fac['motif'] = "";
        if (etat == 3 || etat == 6) {
            var vls = prompt("Le motif du rejet SVP !! ", "");
            if (!vls) return
            vls = vls.trim();
            if (vls) {
                fac['motif'] = vls;
            }
        }
        if (fac.montant) {
            app.notify(result.message, "m");
        }
        var task = prmutils.vudep(fac);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                fac.vu = etat;
                app.waiting.show = false
                if (etat == 3)
                    app.notify(result.message, "m")
                else app.notify(result.message, "b")
                $scope.searchF();
            } else {
                app.waiting.show = false;
                app.notify(result.message, "m")
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
        task = prmutils.getEtatDemandes($scope.search);
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
            total += parseInt(vente.montant)
        }
        return total
    };
    $scope.searchF()
}]);
sngs.controller("etatDemandeValidationCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Expression de besoin",
        subtitle: "Validation demandes",
        show: true,
        model: {}
    };
    $rootScope.title = "Validation des demandes";
    $rootScope.pageTitle = "Validation des Demandes";
    $scope.search = {};
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth();
    var mmm = today.getMonth() + 1;
    var yyyy = today.getFullYear();
    var sss = today.getTime();
    if (dd < 10) {
        dd = "0" + dd
    }
    if (mm < 10) {
        mm = "0" + mm
    }
    today = dd + "/" + mm + "/" + yyyy;
    today2 = dd + "/" + mmm + "/" + yyyy;
    $scope.search.date_deb = today;
    $scope.search.date_fin = today2;
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
    // etat
    // Autorisé = 2
    // rejeter = 3
    // vu = 1
    $scope.actionSurDemande = function(fac, etat = 1) {
        fac['action'] = etat;
        fac['motif'] = "";
        if (etat == 2) {
            var vls = prompt("Le motif du rejet SVP !! ", "");
            if (!vls) return
            vls = vls.trim();
            if (vls) {
                fac['motif'] = vls;
            }
        }

        fac['role'] = app.userPfl.droitValidateurDemande;
        var task = prmutils.actionSurDemande(fac);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                fac.vu = etat;
                app.waiting.show = false
                if (etat == 2)
                    app.notify(result.message, "m")
                else app.notify(result.message, "b")
                $scope.searchF();
            } else {
                app.waiting.show = false;
                app.notify(result.message, "m")
            }
        })
    };
    $scope.searchF = function() {
        var task;
        $scope.search['role'] = app.userPfl.droitValidateurDemande;
        task = prmutils.getDemandesByRole($scope.search);
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
            total += parseInt(vente.montant)
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

    task = prmutils.getMyMagasinsAcces();
    task.promise.then(function(result) {
        app.waiting.show = true;
        if (result.err === 0) {
            $scope.magasins = result.data;
            $scope.appstock.mag_appro_art = $scope.magasins[0].id_mag;
            app.waiting.show = false
        } else {
            app.waiting.show = false
        }
    });
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
        if (!app.userPfl.droitDepense){
            app.notify("Vous n'êtes pas autorisés à faire une dépense, veuillez faire une expression de besoin et vous faire rembourser", "m", 10000);
            return false
        }
        // if (app.userPfl.id != 117) {
        //     app.notify("Vous ne pouvez pas faire de dépense, veuillez faire une expression de besoin et vous faire rembourser", "m", 10000);
        //     return false
        // }
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
sngs.controller("demandeCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Manifestation de besoin",
        subtitle: "Demande",
        show: true,
        model: {}
    };
    $rootScope.title = "Demandes";
    $rootScope.pageTitle = "Demandes";
    $scope.demande = {};
    var datedemande;
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
    datedemande = dd + "/" + mm + "/" + yyyy;
    if (app.PRMS.resa === 0 || app.PRMS.resa === false) {
        $scope.demande.date_demande = datedemande
    } else {
        var task = prmutils.getDs();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.demande.date_demande = result.data.datej;
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
                $scope.type_demandes = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getDemandes = function() {
        console.log("====")
        var task = prmutils.getDemandes();
        task.promise.then(function(result) {
            console.log("====", result)
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.demandes = result.data;
                app.waiting.show = false
                $scope.demande = { date_demande: datedemande }
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getDemandes();
    $scope.save = function(demande) {
        var task;
        if (!prmutils.isDate(demande.date_demande)) {
            app.notify("Le format de la date est incorrect", "m");
            return false
        }
        task = prmutils.saveDemande(demande);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.demande.montant = null;
                    $scope.getDemandes();
                    app.notify(result.message, "b")

                }
            } else {
                app.notify("Une erreur est survenue ..." + result.message, "m")
            }
            console.log(result)
        })
    };


    $scope.getMyMagasinsAcces = function() {
        if (app.userPfl.pfl == 1 || app.userPfl.pfl == 0 || app.userPfl.mg == 0) {
            task = prmutils.getMagasins();
        } else {
            task = prmutils.getMyMagasinsAcces();
        }
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.myMagasinsAcces = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        });
    }
    $scope.getMyMagasinsAcces();
    $scope.getTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.filtered.length; i++) {
            var vente = $scope.filtered[i];
            total += parseInt(vente.montant)
        }
        return total
    }
}]);


sngs.controller("paiementCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Paiement",
        subtitle: "Paiement",
        show: true,
        model: {}
    };
    $rootScope.title = "Paiements";
    $rootScope.pageTitle = "Paiements";
    $scope.paiement = {};
    var datepaiement;
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
    datepaiement = dd + "/" + mm + "/" + yyyy;
    if (app.PRMS.resa === 0 || app.PRMS.resa === false) {
        $scope.paiement.date_paiement = datepaiement
    } else {
        var task = prmutils.getDs();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.paiement.date_paiement = result.data.datej;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    }
    
    $scope.getPaiements = function() {
        var task = prmutils.getPaiements();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.paiements = result.data;
                app.waiting.show = false
                $scope.paiement = { date_paiement: datepaiement }
            } else {
                app.waiting.show = false
            }
        })
    };
    
    $scope.downloadJSONAsCSV = function() {
        // Convert JSON data to CSV
        let csvData = app.jsonToCsv($scope.paiements); // Add .items.data
        // Create a CSV file and allow the user to download it
        let blob = new Blob([csvData], { type: 'text/csv' });
        let url = window.URL.createObjectURL(blob);
        let a = document.createElement('a');
        a.href = url;
        a.download = 'paiements.csv';
        document.body.appendChild(a);
        a.click();
    }
    $scope.getPaiements();
    $scope.save = function(paiement) {
        var task;
        if (!prmutils.isDate(paiement.date_paiement)) {
            app.notify("Le format de la date est incorrect", "m");
            return false
        }
        task = prmutils.savePaiement(paiement);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.paiement = { date_paiement: datepaiement };
                    $scope.getPaiements();
                    app.notify(result.message, "b")
                    $(".btn_reinitialiser").click();

                }
            } else {
                app.notify("Une erreur est survenue ..." + result.message, "m")
            }
            console.log(result)
        })
    };
    $scope.actionSurReglement = function(data, etat) {
        console.log("----------------", data, etat);
        if (etat == 2 && confirm("Voulez vous vraiment annuler cet paiement ? ") === true) {

        }
    }
    
    $scope.ajouterOuModifier = function(paiement) {
        console.log("-------")
        var task;
        if (!paiement.id_paiement) {
            $scope.paiement = { date_paiement: datepaiement };
            return false
        } else {
            $scope.paiement = paiement;
        }
    };

   
    $scope.gus = function() {
        task = prmutils.getcUsers();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                console.log(result.data)
                $scope.users = result.data;
                for (let user of $scope.users) {
                    user['nom_prenom_user'] = user.nom_user + ' ' + user.prenom_user + ' [' + user.code_user + '] ';
                }
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.gus();

    $scope.getMyMagasinsAcces = function() {
        if (app.userPfl.pfl == 1 || app.userPfl.pfl == 0 || app.userPfl.mg == 0) {
            task = prmutils.getMagasins();
        } else {
            task = prmutils.getMyMagasinsAcces();
        }
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.myMagasinsAcces = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        });
    }
    $scope.getMyMagasinsAcces();
    $scope.getTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.filtered.length; i++) {
            var paiement = $scope.filtered[i];
            total += parseInt(paiement.montant)
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
    $scope.bonDeVersement = function(versement) {
        var task;
        console.log("-------", versement)
        task = prmutils.bonDeVersement(versement);
        task.promise.then(function(result) {
            console.log(result,'')
            const blob = new Blob(result);
            window.open(URL.createObjectURL(result), '_blank');
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
    $scope.bonDeVersement = function(versement) {
        var task;
        if (!prmutils.isDate(versement.date_vrsmnt)) {
            app.notify("Le format de la date est incorrect", "m");
            return false
        }
        task = prmutils.bonDeVersement(versement);
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