sngs.controller("annulVersCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Sortie de caisse",
        subtitle: "Annuler un Versements",
        show: true,
        model: {}
    };
    $rootScope.title = "Versements";
    $rootScope.pageTitle = "Annulation Versements";
    $scope.getVersements = function() {
        var task = prmutils.getAnnVersements();
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
    $scope.undo = function(vers) {
        vers.supok = true;
        var task = prmutils.undoVers(vers);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
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
            total += parseInt(vente.mnt_vrsmnt)
        }
        return total
    };
    $scope.select = function(index) {
        $scope.index = index
    };
    $scope.rechF = function() {
        var task;
        if ($scope.rech.vare === "") {
            $scope.getDepenses()
        } else {
            task = prmutils.getVersementByDate($scope.rech.vare);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    if (result.data === "-1") {
                        app.notify(result.message, "m")
                    } else {
                        $scope.versements = result.data
                    }
                } else {
                    app.notify("Probleme de connexion reseau...", "m")
                }
            })
        }
    }
}]);
sngs.controller("annulDepCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Sortie de caisse",
        subtitle: "Annuler une Depenses",
        show: true,
        model: {}
    };
    $rootScope.title = "Depenses";
    $rootScope.pageTitle = "Annulation Depenses";
    $scope.getDepenses = function() {
        var task = prmutils.getAnnDepenses();
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
    $scope.undo = function(dep) {
        dep.supok = true;
        var task = prmutils.undoDep(dep);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
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
            total += parseInt(vente.mnt_dep)
        }
        return total
    };
    $scope.select = function(index) {
        $scope.index = index
    };
    $scope.rechF = function() {
        var task;
        if ($scope.rech.vare === "") {
            $scope.getDepenses()
        } else {
            task = prmutils.getDepenseByDate($scope.rech.vare);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    if (result.data === "-1") {
                        app.notify(result.message, "m")
                    } else {
                        $scope.depenses = result.data
                    }
                } else {
                    app.notify("Probleme de connexion reseau...", "m")
                }
            })
        }
    }
}]);
sngs.controller("annulCaissCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Caisse",
        subtitle: "Annuler une procvision de caisse",
        show: true,
        model: {}
    };
    $rootScope.title = "Caisse";
    $rootScope.pageTitle = "Annulation Provision Caisse";
    $scope.getProvisionsCaisse = function() {
        var task = prmutils.getAnnProvisionsCaisse();
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
    $scope.getProvisionsCaisse();
    $scope.undo = function(prov) {
        prov.supok = true;
        var task = prmutils.undoProv(prov);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
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
            total += parseInt(vente.mnt_cais)
        }
        return total
    };
    $scope.select = function(index) {
        $scope.index = index
    };
    $scope.rechF = function() {
        var task;
        if ($scope.rech.vare === "") {
            $scope.getProvisionsCaisse()
        } else {
            task = prmutils.getProvisionByDate($scope.rech.vare);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    if (result.data === "-1") {
                        app.notify(result.message, "m")
                    } else {
                        $scope.provisions = result.data
                    }
                } else {
                    app.notify("Probleme de connexion reseau...", "m")
                }
            })
        }
    }
}]);
sngs.controller("annulRegClntCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Reglements",
        subtitle: "Annuler Reglement Client",
        show: true,
        model: {}
    };
    $rootScope.title = "Factures Vente";
    $rootScope.pageTitle = "Annulation Reglt Client";
    $scope.getCreances = function() {
        var task = prmutils.getCreances();
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
    $scope.getCreances();
    $scope.undo = function(fac) {
        fac.supok = true;
        var task = prmutils.undoRegClnt(fac);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                app.waiting.show = false;
                $scope.details = []
            } else {
                app.waiting.show = false;
                $scope.details = []
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
    $scope.rechF = function() {
        var task;
        if ($scope.rech.vare === "") {
            $scope.getCreances()
        } else {
            task = prmutils.getCreanceBycode($scope.rech.vare);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    if (result.data === "-1") {
                        app.notify(result.message, "m")
                    } else {
                        $scope.creances = result.data
                    }
                } else {
                    app.notify("Oups!! Petits problemes de connexion..reseau instable.. veuillez reessayez! ...", "m")
                }
            })
        }
    };
    $scope.select = function(index) {
        $scope.index = index
    }
}]);
sngs.controller("annulRegClntGrtCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Reglements",
        subtitle: "Annuler Avances Garanties",
        show: true,
        model: {}
    };
    $rootScope.title = "Avances Garanties";
    $rootScope.pageTitle = "Annulation Avances Garanties";
    $scope.getCreances = function() {
        var task = prmutils.getCreancesGrt();
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
    $scope.getCreances();
    $scope.undo = function(fac) {
        fac.supok = true;
        var task = prmutils.undoRegClnt(fac);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                app.waiting.show = false;
                $scope.details = []
            } else {
                app.waiting.show = false;
                $scope.details = []
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
    $scope.rechF = function() {
        var task;
        if ($scope.rech.vare === "") {
            $scope.getCreances()
        } else {
            task = prmutils.getCreanceGrtBycode($scope.rech.vare);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    if (result.data === "-1") {
                        app.notify(result.message, "m")
                    } else {
                        $scope.creances = result.data
                    }
                } else {
                    app.notify("Oups!! Petits problemes de connexion..reseau instable.. veuillez reessayez! ...", "m")
                }
            })
        }
    };
    $scope.select = function(index) {
        $scope.index = index
    }
}]);
sngs.controller("annulRegFrnstCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Reglements",
        subtitle: "Annnuler Reglement Fournisseurs",
        show: true,
        model: {}
    };
    $rootScope.title = "Factures Achat";
    $rootScope.pageTitle = "Annulation Reglt Fournisseurs";
    $scope.getDettes = function() {
        var task = prmutils.getDettes();
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
    $scope.getDettes();
    $scope.undo = function(fac) {
        fac.supok = true;
        var task = prmutils.undoRegFrns(fac);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                app.waiting.show = false;
                $scope.details = []
            } else {
                app.waiting.show = false;
                $scope.details = []
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
    $scope.rechF = function() {
        var task;
        if ($scope.rech.vare === "") {
            $scope.getDettes()
        } else {
            task = prmutils.getDetteBycode($scope.rech.vare);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    if (result.data === "-1") {
                        app.notify(result.message, "m")
                    } else {
                        $scope.dettes = result.data
                    }
                } else {
                    app.notify("Oups!! Petits problemes de connexion..reseau instable.. veuillez reessayez! ...", "m")
                }
            })
        }
    };
    $scope.select = function(index) {
        $scope.index = index
    }
}]);
sngs.controller("annulVntCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Ventes",
        subtitle: "Annuler une Facture/Vente",
        show: true,
        model: {}
    };
    $rootScope.title = "Factures";
    $rootScope.pageTitle = "Annulation Ventes";
    $scope.rech = {};
    $scope.rech.vare = "";
    $scope.getFactures = function() {
        var task = prmutils.getFacturesFa();
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
    $scope.rechF = function() {
        var task;
        if ($scope.rech.vare === "") {
            $scope.getFactures()
        } else {
            task = prmutils.getFactFaBycode($scope.rech.vare);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    if (result.data === "-1") {
                        app.notify(result.message, "m")
                    } else {
                        $scope.factures = result.data;
                        $scope.num_fact = null;
                        $scope.details = null
                    }
                } else {
                    app.notify("Oups!! Petits problemes de connexion..reseau instable.. veuillez reessayez! ...", "m");
                    $scope.num_fact = null;
                    $scope.details = null
                }
            })
        }
    };
    $scope.undo = function(fac) {
        var today = new Date();
        var date_enr = new Date(fac.date_enr);
        date_enr.setHours(0, 0, 0, 0)
        today.setHours(0, 0, 0, 0)
            // console.log(fac);
            // console.log(date_enr);
            // console.log(today);
            // console.log("today != date_enr", today.toDateString() != date_enr.toDateString());

        if (app.userPfl.factureVenteAnnulee) {

        } else if (app.userPfl.droitFactureVenteAnnuleeToday) {
            if (today.toDateString() != date_enr.toDateString()) {
                app.notify("La date d'annulation de cette facture est dépassée, veuillez contacter votre supérieur pour l'annuler", "m", 5000);
                return;
            }
        } else {
            app.notify("Vous n'êtes pas autorisés à annuler une facture, veuillez contacter un supérieur ", "m", 5000);
            return;
        }

        var vls = prompt("Le motif de l'annulation SVP !! ", "");
        if (!vls) return
        vls = vls.trim();
        if (vls) {
            console.log(fac);
            fac.motif = vls;
            console.log(fac);
            fac.supok = true;
            var task = prmutils.undoFacture(fac);
            task.promise.then(function(result) {
                app.waiting.show = true;
                if (result.err === 0) {
                    if (result.data === "-1") {
                        app.notify(result.message, "m")
                    } else {
                        app.waiting.show = false;
                        $scope.details = []
                    }
                } else {
                    app.waiting.show = false;
                    $scope.details = [];
                    return false
                }
            })
        } else {
            app.notify("Vous n'avez pas fournit de motif", "m")
        }
    };
    $scope.getTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.filtered.length; i++) {
            var vente = $scope.filtered[i];
            total += parseInt(vente.mnt_dep)
        }
        return total
    };
    $scope.select = function(index) {
        $scope.index = index
    }
}]);
sngs.controller("annulSortCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Stock",
        subtitle: "Sortie > Details & Annulation",
        show: true,
        model: {}
    };
    $rootScope.title = "Bons de sorties";
    $rootScope.pageTitle = "Annulation Bons sorties";
    $scope.facture = null;
    $scope.num_fact = null;
    $scope.getSorties = function() {
        var task = prmutils.getSorties();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.sorties = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getSorties();
    $scope.showDetails = function(fac) {
        $scope.num_fact = fac.bon_sort;
        $scope.facture = fac;
        var task = prmutils.showSortDetails(fac);
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
    $scope.undo = function(fac) {
        $scope.sorties.splice($scope.sorties.indexOf(fac), 1);
        var task = prmutils.undoSort(fac);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                app.waiting.show = false;
                $scope.details = []
            } else {
                app.waiting.show = false;
                $scope.details = []
            }
        })
    };
    $scope.undoa = function(fac) {
        $scope.details.splice($scope.details.indexOf(fac), 1);
        var task = prmutils.undoSortArt(fac);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
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
            total += parseInt(vente.mnt_dep)
        }
        return total
    };
    $scope.rechF = function() {
        var task;
        if ($scope.rech.vare === "") {
            $scope.getSorties()
        } else {
            task = prmutils.getSortByCode($scope.rech.vare);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    if (result.data === "-1") {
                        app.notify(result.message, "m")
                    } else {
                        $scope.sorties = result.data;
                        $scope.details = null;
                        $scope.num_fact = null
                    }
                } else {
                    app.notify("Oups!! Petits problemes de connexion..reseau instable.. veuillez reessayez! ...", "m");
                    $scope.details = null;
                    $scope.num_fact = null
                }
            })
        }
    };
    $scope.select = function(index) {
        $scope.index = index
    }
}]);
sngs.controller("annulAppCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Stock",
        subtitle: "Approvisionnement > Details & Annulation",
        show: true,
        model: {}
    };
    $rootScope.title = "Bons";
    $rootScope.pageTitle = "Annulation Bons livraisons";
    $scope.facture = null;
    $scope.num_fact = null;
    $scope.getApprovisionnements = function() {
        var task = prmutils.getApprovisionnements();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.approvisionnements = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getApprovisionnements();
    $scope.showDetails = function(fac) {
        $scope.num_fact = fac.bon_liv_appro;
        $scope.facture = fac;
        var task = prmutils.showApproDetails(fac);
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
    $scope.undo = function(fac) {
        $scope.approvisionnements.splice($scope.approvisionnements.indexOf(fac), 1);
        var task = prmutils.undoAppro(fac);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                app.waiting.show = false;
                $scope.details = []
            } else {
                app.waiting.show = false;
                $scope.details = []
            }
        })
    };
    $scope.undoa = function(fac) {
        $scope.details.splice($scope.details.indexOf(fac), 1);
        var task = prmutils.undoApproArt(fac);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
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
            total += parseInt(vente.mnt_dep)
        }
        return total
    };
    $scope.rechF = function() {
        var task;
        if ($scope.rech.vare === "") {
            $scope.getApprovisionnements()
        } else {
            task = prmutils.getApproByCode($scope.rech.vare);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    if (result.data === "-1") {
                        app.notify(result.message, "m")
                    } else {
                        $scope.approvisionnements = result.data;
                        $scope.details = null;
                        $scope.num_fact = null
                    }
                } else {
                    app.notify("Oups!! Petits problemes de connexion..reseau instable.. veuillez reessayez! ...", "m");
                    $scope.details = null;
                    $scope.num_fact = null
                }
            })
        }
    };
    $scope.select = function(index) {
        $scope.index = index
    }
}]);
sngs.controller("annulCmdCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Stock",
        subtitle: "Comandes > Details & Annulation",
        show: true,
        model: {}
    };
    $rootScope.title = "Bons";
    $rootScope.pageTitle = "Annulation Bons de commandes";
    $scope.facture = null;
    $scope.num_fact = null;
    $scope.getApprovisionnements = function() {
        var task = prmutils.getaCommandes();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.approvisionnements = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getApprovisionnements();
    $scope.showDetails = function(fac) {
        $scope.num_fact = fac.bon_cmd;
        $scope.nid_cmd = fac.id_cmd;
        $scope.facture = fac;
        var task = prmutils.showCmdDetails(fac);
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
    $scope.undo = function(fac) {
        $scope.approvisionnements.splice($scope.approvisionnements.indexOf(fac), 1);
        var task = prmutils.undoCmd(fac);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                app.waiting.show = false;
                $scope.details = []
            } else {
                app.waiting.show = false;
                $scope.details = []
            }
        })
    };
    $scope.undoa = function(fac) {
        $scope.details.splice($scope.details.indexOf(fac), 1);
        var task = prmutils.undoCmdArt(fac);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
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
            total += parseInt(vente.mnt_dep)
        }
        return total
    };
    $scope.rechF = function() {
        var task;
        if ($scope.rech.vare === "") {
            $scope.getApprovisionnements()
        } else {
            task = prmutils.getCmdByCode($scope.rech.vare);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    if (result.data === "-1") {
                        app.notify(result.message, "m")
                    } else {
                        $scope.approvisionnements = result.data;
                        $scope.details = null;
                        $scope.num_fact = null
                    }
                } else {
                    app.notify("Oups!! Petits problemes de connexion..reseau instable.. veuillez reessayez! ...", "m");
                    $scope.details = null;
                    $scope.num_fact = null
                }
            })
        }
    };
    $scope.select = function(index) {
        $scope.index = index
    }
}]);