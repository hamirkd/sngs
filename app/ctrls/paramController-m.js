sngs.controller("paramFrnsCtrl", ["$scope", "$rootScope", "config", "prmutils", function($scope, $rootScope, config, prmutils) {
    var paramclt = $scope.paramclt;
    var app = $scope.app;
    app.view = {
        url: config.urlParamFrns,
        model: paramclt,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Parametrages",
        subtitle: "Fournisseurs",
        show: true,
        model: {}
    };
    $rootScope.title = "Liste des fournisseurs";
    $rootScope.pageTitle = "Fournisseurs";
    $scope.getaFournisseurs = function() {
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
    $scope.getaFournisseurs();
    $scope.status = function(status, id) {
        var task;
        task = prmutils.statusfournisseur(status, id);
        task.promise.then(function(result) {
            $scope.getaFournisseurs()
        })
    }
}]);
sngs.controller("paramEditFrnsCtrl", ["$scope", "$rootScope", "config", "dao", "$location", "$routeParams", "utils", "prmutils", "object", function($scope, $rootScope, config, dao, $location, $routeParams, utils, prmutils, object) {
    var parameditFrns = $scope.parameditFrns;
    var app = $scope.app;
    app.view = {
        url: config.urlParamEditFrns,
        model: parameditFrns,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Parametrages",
        subtitle: "Fournisseur",
        show: true,
        model: {}
    };
    $rootScope.pageTitle = "Fournisseur";
    $scope.types = [{
        name: "ordinaire",
        label: "Ordinaire"
    }, {
        name: "passant",
        label: "Passant"
    }];
    var objectID = ($routeParams.objectID) ? parseInt($routeParams.objectID) : 0;
    $rootScope.title = (objectID > 0) ? "Modification Fournisseur" : "Nouveau Fournisseur";
    $scope.buttonText = (objectID > 0) ? "Modifier" : "Ajouter";
    var original = object.data;
    original.id_frns = objectID;
    $scope.fournisseur = angular.copy(original);
    $scope.fournisseur.id_frns = objectID;
    $scope.fournisseur.dette_frns = parseInt($scope.fournisseur.dette_frns);
    $scope.isClean = function() {
        return angular.equals(original, $scope.fournisseur)
    };
    $scope.deleted = function(fournisseur) {
        var task;
        if (confirm("Confirmer vous la suppression du fournisseur : " + $scope.fournisseur.code_frns) === true) {
            task = prmutils.deleteFournisseur(fournisseur.id_frns)
        }
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    app.refreshfrnsCache();
                    app.notify(result.message, "b");
                    $location.path(config.urlParamFrns)
                }
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.save = function(fournisseur) {
        var task;
        if (objectID <= 0) {
            task = prmutils.insertFournisseur(fournisseur);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    app.refreshfrnsCache();
                    app.notify(result.message, "b");
                    $scope.fournisseur.nom_frns = null;
                    $scope.fournisseur.sex_frns = null;
                    $scope.fournisseur.dette_frns = null;
                    $scope.fournisseur.adr_frns = null;
                    $scope.fournisseur.mail_frns = null;
                    $scope.fournisseur.tel_frns = null
                } else {
                    app.notify("ok ...", "b")
                }
            })
        } else {
            task = prmutils.updateFournisseur(objectID, fournisseur);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    app.refreshfrnsCache();
                    app.notify(result.message, "b");
                    $location.path(config.urlParamFrns)
                } else {
                    app.notify("ok ...", "b")
                }
            })
        }
    }
}]);
sngs.controller("paramCltCtrl", ["$scope", "$rootScope", "config", "prmutils", function($scope, $rootScope, config, prmutils) {
    var paramclt = $scope.paramclt;
    var app = $scope.app;
    app.view = {
        url: config.urlParamClt,
        model: paramclt,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Parametrages",
        subtitle: "Clients",
        show: true,
        model: {}
    };
    $rootScope.title = "Liste des clients";
    $rootScope.pageTitle = "Clients";
    $scope.fullSearchText = "";
    $scope.getaClients = function() {
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
    $scope.status = function(status, id) {
        var task;
        task = prmutils.statusclient(status, id);
        task.promise.then(function(result) {
            $scope.getaClients()
        })
    };
    $scope.getAll = function() {
        $scope.getaClients()
    };
    $scope.getLimit = function() {
        $scope.loading = true;
        var task = prmutils.getLmClients();
        task.promise.then(function(result) {
            $scope.loading = true;
            if (result.err === 0) {
                $scope.clients = result.data;
                $scope.loading = false
            } else {
                $scope.loading = false
            }
        })
    };
    $scope.fullSearch = function() {
        $scope.loading = true;
        var task;
        if ($scope.fullSearchText.length > 1) {
            task = prmutils.queryClients($scope.fullSearchText)
        } else {
            task = prmutils.getLmClients()
        }
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.clients = result.data;
                $scope.loading = false
            } else {
                $scope.loading = false
            }
        })
    };
    $scope.loadMore = function() {
        if ($scope.fullSearchText.length > 0) {
            return false
        }
        $scope.loading = true;
        var task = prmutils.loadartMore($scope.clients.length);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data.length > 0) {
                    $scope.clients = $scope.clients.concat(result.data)
                }
                $scope.loading = false
            } else {
                $scope.loading = false
            }
        })
    };
    $scope.getLimit();
    $scope.showReplace = function(clnt) {
        $("#replacePannel").css("right", "0");
        $scope.anc_clnt = clnt
    };
    $scope.replaceItem = function(data) {
        var obj;
        obj = {
            anc: parseInt($scope.anc_clnt.id_clt),
            newe: parseInt($scope.replace.client)
        };
        task = prmutils.replaceCustomer(obj);
        task.promise.then(function(result) {
            if (result.err === 0) {
                app.notify(result.message, "b");
                $scope.replace.client = {};
                $scope.anc_clnt = {}
            } else {
                app.notify("ok ...", "b")
            }
        })
    }
}]);
sngs.controller("paramEditCltCtrl", ["$scope", "$rootScope", "localStorageService", "config", "dao", "$location", "$routeParams", "utils", "prmutils", "object", function($scope, $rootScope, localStorageService, config, dao, $location, $routeParams, utils, prmutils, object) {
    var parameditClt = $scope.parameditClt;
    var app = $scope.app;
    app.view = {
        url: config.urlParamEditClt,
        model: parameditClt,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Parametrages",
        subtitle: "Client",
        show: true,
        model: {}
    };
    $rootScope.pageTitle = "Client";
    $scope.exos = [{
        name: 0,
        label: "Non [ Paye les taxes]"
    }, {
        name: 1,
        label: "Oui [Ne Paye pas les taxes]"
    }];
    var objectID = ($routeParams.objectID) ? parseInt($routeParams.objectID) : 0;
    $rootScope.title = (objectID > 0) ? "Modification Client" : "Nouveau Client";
    $scope.buttonText = (objectID > 0) ? "Modifier" : "Ajouter";
    var original = object.data;
    original.id_clt = objectID;
    $scope.client = angular.copy(original);
    $scope.client.id_clt = objectID;
    $scope.client.max_crdt_clt = parseInt($scope.client.max_crdt_clt);
    $scope.isClean = function() {
        return angular.equals(original, $scope.client)
    };
    $scope.deleted = function(client) {
        var task;
        if (confirm("Confirmer vous la suppression du client : " + $scope.client.code_clt) === true) {
            task = prmutils.deleteClient(client.id_clt)
        }
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    app.refreshcltCache();
                    app.notify(result.message, "b");
                    $location.path(config.urlParamClt)
                }
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.save = function(client) {
        var task;
        if (objectID <= 0) {
            task = prmutils.insertClient(client);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    app.notify(result.message, "b");
                    app.refreshcltCache();
                    $scope.client.nom_clt = null;
                    $scope.client.sex_clt = null;
                    $scope.client.max_crdt_clt = null;
                    $scope.client.adr_clt = null;
                    $scope.client.mail_clt = null;
                    $scope.client.tel_clt = null;
                    $scope.client.regime_clt = null;
                    $scope.client.situation_clt = null;
                    $scope.client.bp_clt = null;
                    $scope.client.division_clt = null;
                    $scope.client.exo_tva_clt = null;
                    localStorageService.remove("ClientsCache");
                    localStorageService.remove("ClientsOrCache")
                } else {
                    app.notify("ok ...", "b")
                }
            })
        } else {
            task = prmutils.updateClient(objectID, client);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    app.refreshcltCache();
                    app.notify(result.message, "b");
                    $location.path(config.urlParamClt)
                } else {
                    app.notify("ok ...", "b")
                }
            })
        }
    }
}]);
sngs.controller("paramMagCtrl", ["$scope", "$rootScope", "config", "prmutils", function($scope, $rootScope, config, prmutils) {
    var parammag = $scope.parammag;
    var app = $scope.app;
    app.view = {
        url: config.urlParamMag,
        model: parammag,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Parametrages",
        subtitle: "Magasins",
        show: true,
        model: {}
    };
    $rootScope.title = "Liste des magasins";
    $rootScope.pageTitle = "Boutiques/Magasins";
    var task = prmutils.getMagasins();
    return task.promise.then(function(result) {
        app.waiting.show = true;
        if (result.err === 0) {
            $scope.magasins = result.data;
            app.waiting.show = false
        } else {
            app.waiting.show = false
        }
    })
}]);
sngs.controller("paramEditMagCtrl", ["$scope", "$rootScope", "config", "dao", "$location", "$routeParams", "utils", "prmutils", "object", function($scope, $rootScope, config, dao, $location, $routeParams, utils, prmutils, object) {
    var parameditmag = $scope.parameditmag;
    var app = $scope.app;
    app.view = {
        url: config.urlParamEditMag,
        model: parameditmag,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Parametrages",
        subtitle: "Magasin",
        show: true,
        model: {}
    };
    $scope.types = [{
        name: "secondaire",
        label: "Secondaire"
    }, {
        name: "primaire",
        label: "Primaire"
    }];
    $rootScope.pageTitle = "Boutique/Magasin";
    var objectID = ($routeParams.objectID) ? parseInt($routeParams.objectID) : 0;
    $rootScope.title = (objectID > 0) ? "Modification Magasin" : "Nouveau Magasin";
    $scope.buttonText = (objectID > 0) ? "Modifier" : "Ajouter";
    var original = object.data;
    original.id_mag = objectID;
    $scope.magasin = angular.copy(original);
    $scope.magasin.id_mag = objectID;
    $scope.isClean = function() {
        return angular.equals(original, $scope.magasin)
    };
    $scope.deleted = function(magasin) {
        var task;
        if (confirm("Confirmer vous la suppression du magasin : " + $scope.magasin.code_mag) === true) {
            task = prmutils.deleteMagasin(magasin.id_mag)
        }
        task.promise.then(function(result) {
            if (result.err === 0) {
                app.refreshmagCache();
                app.notify(result.message, "b");
                $location.path(config.urlParamMag)
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.save = function(magasin) {
        var task;
        if (objectID <= 0) {
            task = prmutils.insertMagasin(magasin);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    app.refreshmagCache();
                    app.notify(result.message, "b");
                    $location.path(config.urlParamMag)
                } else {
                    app.notify("ok ...", "b")
                }
            })
        } else {
            task = prmutils.updateMagasin(objectID, magasin);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    app.refreshmagCache();
                    app.notify(result.message, "b");
                    $location.path(config.urlParamMag)
                } else {
                    app.notify("ok ...", "b")
                }
            })
        }
    }
}]);
sngs.controller("paramCatArtCtrl", ["$scope", "$rootScope", "config", "prmutils", function($scope, $rootScope, config, prmutils) {
    var paramcat = $scope.paramcat;
    var app = $scope.app;
    app.view = {
        url: config.urlParamCat,
        model: paramcat,
        done: false
    };
    $scope.loading = true;
    app.navbar.show = true;
    app.title = {
        text: "Parametrages",
        subtitle: "Categorie d'articles",
        show: true,
        model: {}
    };
    $rootScope.title = "Liste des categories";
    $rootScope.pageTitle = "Categories Articles";
    $scope.searchAll = function() {
        $scope.loading = true;
        var task = prmutils.getCategories();
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.categories = result.data;
                $scope.loading = false
            } else {
                $scope.loading = false
            }
        })
    };
    $scope.loadMore = function() {
        $scope.loading = true;
        var task = prmutils.loadMorecat($scope.categories.length);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data.length > 0) {
                    $scope.categories = $scope.categories.concat(result.data)
                }
                $scope.loading = false
            } else {
                $scope.loading = false
            }
        })
    };
    $scope.searchAll()
}]);
sngs.controller("paramEditCatArtCtrl", ["$scope", "$rootScope", "config", "dao", "$location", "$routeParams", "utils", "prmutils", "object", function($scope, $rootScope, config, dao, $location, $routeParams, utils, prmutils, object) {
    var parameditcat = $scope.parameditcat;
    var app = $scope.app;
    app.view = {
        url: config.urlParamEditCat,
        model: parameditcat,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Parametrages",
        subtitle: "Categorie d'articles",
        show: true,
        model: {}
    };
    $rootScope.pageTitle = "Categorie article";
    var objectID = ($routeParams.objectID) ? parseInt($routeParams.objectID) : 0;
    $rootScope.title = (objectID > 0) ? "Modification Categorie" : "Nouvelle Categorie";
    $scope.buttonText = (objectID > 0) ? "Modifier" : "Ajouter";
    var original = object.data;
    original.id_cat = objectID;
    $scope.categorie = angular.copy(original);
    $scope.categorie.id_cat = objectID;
    $scope.isClean = function() {
        return angular.equals(original, $scope.categorie)
    };
    $scope.deleted = function(categorie) {
        var task;
        if (confirm("Confirmer vous la suppression de la categorie : " + $scope.categorie.code_cat) === true) {
            task = prmutils.deleteCategorie(categorie.id_cat)
        }
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    app.refreshcatCache();
                    app.refreshextcatmagCache();
                    app.notify(result.message, "b");
                    $location.path(config.urlParamCat)
                }
            } else {
                app.notify("Attention un article de cette categorie a deja ete vendu", "m")
            }
        })
    };
    $scope.save = function(categorie) {
        var task;
        if ($scope.categorie.nom_cat.length <= 0) {
            app.notify("Veuillez Preciser une valeur pour la categorie/groupe de l'article...", "m");
            return false
        }
        if (objectID <= 0) {
            task = prmutils.insertCategorie(categorie);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    if (result.data === "-1") {
                        app.notify(result.message, "m")
                    } else {
                        app.refreshcatCache();
                        app.refreshextcatmagCache();
                        app.notify(result.message, "b");
                        $scope.categorie.nom_cat = ""
                    }
                } else {
                    app.notify("ok ...", "b")
                }
            })
        } else {
            task = prmutils.updateCategorie(objectID, categorie);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    if (result.data === "-1") {
                        app.notify(result.message, "m")
                    } else {
                        app.refreshcatCache();
                        app.refreshextcatmagCache();
                        app.notify(result.message, "b");
                        $location.path(config.urlParamCat)
                    }
                } else {
                    app.notify("ok ...", "b")
                }
            })
        }
    }
}]);
sngs.controller("paramUnitArtCtrl", ["$scope", "$rootScope", "config", "prmutils", function($scope, $rootScope, config, prmutils) {
    var paramunit = $scope.paramunit;
    var app = $scope.app;
    app.view = {
        url: config.urlParamUnit,
        model: paramunit,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Parametrages",
        subtitle: "Unite d'articles",
        show: true,
        model: {}
    };
    $rootScope.title = "Liste des unites";
    $rootScope.pageTitle = "Unites d'Articles";
    var task = prmutils.getUnites();
    return task.promise.then(function(result) {
        app.waiting.show = true;
        if (result.err === 0) {
            $scope.unites = result.data;
            app.waiting.show = false
        } else {
            app.waiting.show = false
        }
    })
}]);
sngs.controller("paramEditUnitArtCtrl", ["$scope", "$rootScope", "config", "dao", "$location", "$routeParams", "utils", "prmutils", "object", function($scope, $rootScope, config, dao, $location, $routeParams, utils, prmutils, object) {
    var parameditunit = $scope.parameditunit;
    var app = $scope.app;
    app.view = {
        url: config.urlParamEditUnit,
        model: parameditunit,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Parametrages",
        subtitle: "Unites d'articles",
        show: true,
        model: {}
    };
    $rootScope.pageTitle = "Unite article";
    var objectID = ($routeParams.objectID) ? parseInt($routeParams.objectID) : 0;
    $rootScope.title = (objectID > 0) ? "Modification Unite" : "Nouvelle Unite";
    $scope.buttonText = (objectID > 0) ? "Modifier" : "Ajouter";
    var original = object.data;
    original.id_unite = objectID;
    $scope.unite = angular.copy(original);
    $scope.unite.id_unite = objectID;
    $scope.isClean = function() {
        return angular.equals(original, $scope.unite)
    };
    $scope.deleted = function(unite) {
        var task;
        if (confirm("Confirmer vous la suppression de l'unite : " + $scope.unite.code_unite) === true) {
            task = prmutils.deleteUnite(unite.id_unite)
        }
        task.promise.then(function(result) {
            if (result.err === 0) {
                app.refreshuntCache();
                app.notify(result.message, "b");
                $location.path(config.urlParamUnit)
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.save = function(unite) {
        var task;
        if ($scope.unite.nom_unite.length <= 0) {
            app.notify("Veuillez Preciser une valeur pour l'unite de l'article...", "m");
            return false
        }
        if (objectID <= 0) {
            task = prmutils.insertUnite(unite);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    app.notify(result.message, "b");
                    $scope.unite.nom_unite = "";
                    app.refreshuntCache()
                } else {
                    app.notify("ok ...", "b")
                }
            })
        } else {
            task = prmutils.updateUnite(objectID, unite);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    app.refreshuntCache();
                    app.notify(result.message, "b");
                    $location.path(config.urlParamUnit)
                } else {
                    app.notify("ok ...", "b")
                }
            })
        }
    }
}]);
sngs.controller("paramBnkCtrl", ["$scope", "$rootScope", "config", "prmutils", function($scope, $rootScope, config, prmutils) {
    var paramunit = $scope.paramunit;
    var app = $scope.app;
    app.view = {
        url: config.urlParamBank,
        model: paramunit,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Parametrages",
        subtitle: "Banques",
        show: true,
        model: {}
    };
    $rootScope.title = "Liste des banques";
    $rootScope.pageTitle = "Banques";
    var task = prmutils.getBanques();
    return task.promise.then(function(result) {
        app.waiting.show = true;
        if (result.err === 0) {
            $scope.banques = result.data;
            app.waiting.show = false
        } else {
            app.waiting.show = false
        }
    })
}]);
sngs.controller("paramEditBnkCtrl", ["$scope", "$rootScope", "config", "dao", "$location", "$routeParams", "utils", "prmutils", "object", function($scope, $rootScope, config, dao, $location, $routeParams, utils, prmutils, object) {
    var parameditbank = $scope.parameditbank;
    var app = $scope.app;
    app.view = {
        url: config.urlParamEditBank,
        model: parameditbank,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Parametrages",
        subtitle: "Banques",
        show: true,
        model: {}
    };
    $rootScope.pageTitle = "Banque";
    var objectID = ($routeParams.objectID) ? parseInt($routeParams.objectID) : 0;
    $rootScope.title = (objectID > 0) ? "Modification Banque" : "Nouvelle Banque";
    $scope.buttonText = (objectID > 0) ? "Modifier" : "Ajouter";
    var original = object.data;
    original.id_bnk = objectID;
    $scope.banque = angular.copy(original);
    $scope.banque.id_bnk = objectID;
    $scope.isClean = function() {
        return angular.equals(original, $scope.banque)
    };
    $scope.deleted = function(banque) {
        var task;
        if (confirm("Confirmer vous la suppression de la banque : " + $scope.banque.code_bank) === true) {
            task = prmutils.deleteBanque(banque.id_bnk)
        }
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    app.refresbnkCache();
                    app.notify(result.message, "b");
                    $location.path(config.urlParamBank)
                }
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.save = function(banque) {
        var task;
        if ($scope.banque.nom_bank.length <= 0) {
            app.notify("Veuillez Preciser une valeur pour cette banque...", "m");
            return false
        }
        if (objectID <= 0) {
            task = prmutils.insertBanque(banque);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    if (result.data === "-1") {
                        app.notify(result.message, "m")
                    } else {
                        app.refresbnkCache();
                        app.notify(result.message, "b");
                        $scope.banque.nom_bank = ""
                    }
                } else {
                    app.notify("ok ...", "b")
                }
            })
        } else {
            task = prmutils.updateBanque(objectID, banque);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    if (result.data === "-1") {
                        app.notify(result.message, "m")
                    } else {
                        app.refresbnkCache();
                        app.notify(result.message, "b");
                        $location.path(config.urlParamBank)
                    }
                } else {
                    app.notify("ok ...", "b")
                }
            })
        }
    }
}]);
sngs.controller("paramTdCtrl", ["$scope", "$rootScope", "config", "prmutils", function($scope, $rootScope, config, prmutils) {
    var paramtd = $scope.paramtd;
    var app = $scope.app;
    app.view = {
        url: config.urlParamTd,
        model: paramtd,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Parametrages",
        subtitle: "Types de depense ",
        show: true,
        model: {}
    };
    $rootScope.title = "Liste des types de depense";
    $rootScope.pageTitle = "Types de depenses";
    var task = prmutils.getTypeDepenses();
    return task.promise.then(function(result) {
        app.waiting.show = true;
        if (result.err === 0) {
            $scope.type_deps = result.data;
            app.waiting.show = false
        } else {
            app.waiting.show = false
        }
    })
}]);
sngs.controller("paramEditTdCtrl", ["$scope", "$rootScope", "config", "dao", "$location", "$routeParams", "utils", "prmutils", "object", function($scope, $rootScope, config, dao, $location, $routeParams, utils, prmutils, object) {
    var paramedittd = $scope.paramedittd;
    var app = $scope.app;
    app.view = {
        url: config.urlParamEditTd,
        model: paramedittd,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Parametrages",
        subtitle: "Types de depense",
        show: true,
        model: {}
    };
    $rootScope.pageTitle = "Type de depense";
    var objectID = ($routeParams.objectID) ? parseInt($routeParams.objectID) : 0;
    $rootScope.title = (objectID > 0) ? "Modification TypeDepense" : "Nouveau Type de depense";
    $scope.buttonText = (objectID > 0) ? "Modifier" : "Ajouter";
    var original = object.data;
    original.id_type_dep = objectID;
    $scope.type_dep = angular.copy(original);
    $scope.type_dep.id_type_dep = objectID;
    $scope.isClean = function() {
        return angular.equals(original, $scope.type_dep)
    };
    $scope.deleted = function(type_dep) {
        var task;
        if (confirm("Confirmer vous la suppression du type de depense : " + $scope.type_dep.code_type_dep) === true) {
            task = prmutils.deleteTypeDepense(type_dep.id_type_dep)
        }
        task.promise.then(function(result) {
            if (result.err === 0) {
                app.refrestdCache();
                app.notify(result.message, "b");
                $location.path(config.urlParamTd)
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.save = function(type_dep) {
        var task;
        if ($scope.type_dep.lib_type_dep.length <= 0) {
            app.notify("Veuillez Preciser une valeur pour ce type/nature de depense...", "m");
            return false
        }
        if (objectID <= 0) {
            task = prmutils.insertTypeDepense(type_dep);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    app.refrestdCache();
                    app.notify(result.message, "b");
                    $location.path(config.urlParamTd)
                } else {
                    app.notify("ok ...", "b")
                }
            })
        } else {
            task = prmutils.updateTypeDepense(objectID, type_dep);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    app.refrestdCache();
                    app.notify(result.message, "b");
                    $location.path(config.urlParamTd)
                } else {
                    app.notify("ok ...", "b")
                }
            })
        }
    }
}]);
sngs.controller("paramArtArtCtrl", ["$scope", "$rootScope", "config", "prmutils", function($scope, $rootScope, config, prmutils) {
    var paramart = $scope.paramart;
    var app = $scope.app;
    app.view = {
        url: config.urlParamArt,
        model: paramart,
        done: false
    };
    $scope.loading = true;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Parametrages",
        subtitle: "Article",
        show: true,
        model: {}
    };
    $rootScope.title = "Liste des articles";
    $rootScope.pageTitle = "Articles";
    $scope.fullSearchText = "";
    $scope.getAll = function() {
        $scope.loading = true;
        var task = prmutils.getArticles();
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.articles = result.data;
                $scope.loading = false
            } else {
                $scope.loading = false
            }
        })
    };
    $scope.getLimit = function() {
        $scope.loading = true;
        var task = prmutils.getLmArticles();
        task.promise.then(function(result) {
            $scope.loading = true;
            console.log(result)
            if (result.err === 0) {
                $scope.articles = result.data;
                $scope.loading = false
            } else {
                $scope.loading = false
            }
        })
    };
    $scope.fullSearch = function() {
        $scope.loading = true;
        var task;
        if ($scope.fullSearchText.length > 1) {
            task = prmutils.queryArticles($scope.fullSearchText)
        } else {
            task = prmutils.getLmArticles()
        }
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.articles = result.data;
                $scope.loading = false
            } else {
                $scope.loading = false
            }
        })
    };
    $scope.loadMore = function() {
        if ($scope.fullSearchText.length > 0) {
            return false
        }
        $scope.loading = true;
        var task = prmutils.loadMore($scope.articles.length);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data.length > 0) {
                    $scope.articles = $scope.articles.concat(result.data)
                }
                $scope.loading = false
            } else {
                $scope.loading = false
            }
        })
    };
    $scope.getLimit()
}]);
sngs.controller("paramEditArtArtCtrl", ["$scope", "$rootScope", "config", "dao", "$location", "$routeParams", "utils", "prmutils", "object", function($scope, $rootScope, config, dao, $location, $routeParams, utils, prmutils, object) {
    var parameditart = $scope.parameditart;
    var app = $scope.app;
    app.view = {
        url: config.urlParamEditArt,
        model: parameditart,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Parametrages",
        subtitle: "Articles",
        show: true,
        model: {}
    };
    $rootScope.pageTitle = "Article";
    $scope.getcat = function() {
        prmutils.getCategories().promise.then(function(result) {
            $scope.categories = result.data
        })
    };
    $scope.getunit = function() {
        prmutils.getUnites().promise.then(function(result) {
            $scope.unites = result.data
        })
    };
    var objectID = ($routeParams.objectID) ? parseInt($routeParams.objectID) : 0;
    $rootScope.title = (objectID > 0) ? "Modification Article" : "Nouveau Article";
    $scope.buttonText = (objectID > 0) ? "Modifier" : "Ajouter";
    var original = object.data;
    original.id_art = objectID;
    if (objectID < 1) {
        original = {
            nom_art: null,
            cat_art: null,
            unite_art: null,
            seuil_art: 1,
            marq_art: null,
            model_art: null,
            caract_art: null,
            prix_mini_art: null,
            prix_max_art: null,
            prix_gros_art: null
        }
    }
    $scope.article = angular.copy(original);
    $scope.article.id_art = objectID;
    $scope.article.prix_mini_art = parseInt($scope.article.prix_mini_art);
    $scope.article.prix_max_art = parseInt($scope.article.prix_max_art);
    $scope.article.prix_gros_art = parseInt($scope.article.prix_gros_art);
    $scope.article.prix_achat_art = parseInt($scope.article.prix_achat_art);
    $scope.article.seuil_art = parseInt($scope.article.seuil_art);
    $scope.isClean = function() {
        return angular.equals(original, $scope.article)
    };
    $scope.deleted = function(article) {
        var task;
        if (confirm("Confirmer vous la suppression de l'article : " + $scope.article.code_art) === true) {
            task = prmutils.deleteArticle(article.id_art)
        }
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    app.refreshartCache();
                    app.notify(result.message, "b");
                    window.history.go(-1)
                }
            } else {
                window.history.go(-1);
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.save = function(article) {
        var task;
        if (parseInt($scope.article.prix_mini_art) <= 0) {
            app.notify("Attention : prix minimum null", "m");
            return false
        }
        if (parseInt($scope.article.prix_gros_art) <= 0) {
            app.notify("Attention : prix en gros null", "m");
            return false
        }
        if (parseInt($scope.article.prix_max_art) <= 0) {
            app.notify("Attention : prix en maximum null", "m");
            return false
        }
        if (parseInt($scope.article.prix_max_art) < parseInt($scope.article.prix_mini_art)) {
            app.notify("Attention : prix en maximum doit être supérieur au prix minimum", "m");
            return false
        }
        if (parseInt($scope.article.prix_achat_art) <= 0) {
            $scope.article.prix_achat_art = 0
        }
        if (parseInt($scope.article.seuil_art) < 1) {
            $scope.article.seuil_art = 1
        }
        if (objectID <= 0) {
            task = prmutils.insertArticle(article);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    if (result.data === "-1") {
                        app.notify(result.message, "m")
                    } else {
                        app.refreshartCache();
                        app.notify(result.message, "b");
                        $scope.article.nom_art = null;
                        $scope.article.ref_art = null;
                        $scope.article.marq_art = null;
                        $scope.article.model_art = null;
                        $scope.article.caract_art = null;
                        $scope.article.prix_gros_art = null;
                        $scope.article.prix_mini_art = null;
                        $scope.article.prix_max_art = null;
                        $scope.article.prix_achat_art = null
                    }
                } else {
                    window.history.go(-1);
                    app.notify("ok ...", "b")
                }
            })
        } else {
            task = prmutils.updateArticle(objectID, article);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    if (result.data === "-1") {
                        app.notify(result.message, "m")
                    } else {
                        app.refreshartCache();
                        app.notify(result.message, "b");
                        window.history.go(-1)
                    }
                } else {
                    app.notify("ok ...", "b")
                }
            })
        }
    }
    $scope.getcat();
    $scope.getunit();
}]);
sngs.controller("paramAccessCtrl", ["$scope", "$rootScope", "config", "prmutils", function($scope, $rootScope, config, prmutils) {
    var paramcusr = $scope.paramcusr;
    var app = $scope.app;
    app.view = {
        url: config.urlParamCusr,
        model: paramcusr,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Parametrages",
        subtitle: "Profil",
        show: true,
        model: {}
    };
    $rootScope.title = "Liste des profils";
    $rootScope.pageTitle = "Profil";
    $scope.getProfils = function() {
        var task = prmutils.getProfils();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.profils = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getProfils();
    $scope.verrou = function(status, id) {
        var task;
        task = prmutils.statususer(status, id);
        task.promise.then(function(result) {
            $scope.getProfils()
        })
    };
    $scope.veille = function(status, id) {
        var task;
        task = prmutils.veilleuser(status, id);
        task.promise.then(function(result) {
            $scope.getcUsers()
        })
    };
    $scope.reinit = function(id) {
        var task;
        task = prmutils.reinitps(id);
        task.promise.then(function(result) {
            app.notify(result.message, "b")
        })
    }
}]);
sngs.controller("paramAccessDroitCtrl", ["$scope", "$rootScope", "config", "prmutils", function($scope, $rootScope, config, prmutils) {
    var paramaccessdroid = $scope.paramaccessdroid;
    var app = $scope.app;
    app.view = {
        url: config.urlParamaccessdroid,
        model: paramaccessdroid,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Parametrages",
        subtitle: "Droit",
        show: true,
        model: {}
    };
    $rootScope.title = "Configuration des droits";
    $rootScope.pageTitle = "Profil";
    $scope.getProfils = function() {
        var task = prmutils.getProfils();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.profils = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getProfils();
    //

    $scope.enregistrer = function(droit, profil_droits) {
        var task;

        task = prmutils.saveDroits(droit, profil_droits);
        task.promise.then(function(result) {
            console.log(result)
            $rootScope.profil_droits = result.data;
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    app.notify(result.message, "b");
                }
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.getDroit = function(droit) {
        var task;

        task = prmutils.getDroit(droit);
        task.promise.then(function(result) {
            console.log(result)
            $rootScope.profil_droits = result.data;
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    app.notify(result.message, "b");
                }
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    task = prmutils.getMagasins();
    task.promise.then(function(result) {
        app.waiting.show = true;
        console.log(result)
        if (result.err === 0) {
            $scope.magasins = result.data;
            app.waiting.show = false
        } else {
            app.waiting.show = false
        }
    });
}]);

sngs.controller("paramEditAccessCtrl", ["$scope", "$rootScope", "config", "dao", "$location", "$routeParams", "utils", "prmutils", "object", function($scope, $rootScope, config, dao, $location, $routeParams, utils, prmutils, object) {
    var paramprofil = $scope.paramprofil;
    var app = $scope.app;
    app.view = {
        url: config.urlParamprofil,
        model: paramprofil,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Parametrages",
        subtitle: "Profil",
        show: true,
        model: {}
    };

    $rootScope.title = "Modification de profil";
    $rootScope.pageTitle = "Profil";
    // console.log($routeParams)

    $rootScope.pageTitle = "Profil";
    var objectID = ($routeParams.objectID) ? parseInt($routeParams.objectID) : 0;
    $rootScope.title = (objectID > 0) ? "Modification Profil" : "Nouvel Profil";
    $scope.buttonText = (objectID > 0) ? "Modifier" : "Ajouter";
    var original = object.data;
    original.id_profil = objectID;
    $scope.profil = angular.copy(original);
    $scope.profil.id_profil = objectID;

    $scope.deleted = function(profil) {
        var task;
        if (confirm("Confirmer vous la suppression du profil : " + $scope.profil.lib_profil) === true) {
            task = prmutils.deleteProfil(profil.id_profil)
        }
        task.promise.then(function(result) {
            if (result.err === 0) {
                app.refresusrCache();
                app.notify(result.message, "b");
                $location.path(config.urlParamAccess)
            } else {
                app.notify("ok ...", "b")
            }
        })
    };

    $scope.save = function(profil) {
        var task;
        if (!profil.code_profil) {
            app.notify(" Veuillez definir un code..", "m");
            return false
        }
        if (!profil.lib_profil) {
            app.notify(" Veuillez definir un libelle..", "m");
            return false
        }
        if (objectID <= 0) {
            task = prmutils.insertProfil(profil);
            task.promise.then(function(result) {
                console.log(result)
                if (result.err === 0) {
                    if (result.data === "-1") {
                        app.notify(result.message, "m")
                    } else {
                        app.notify(result.message, "b");
                        $location.path(config.urlParamAccess)
                    }
                } else {
                    app.notify(result.message, "m")
                }
            })
        } else {
            task = prmutils.updateProfil2(objectID, profil);
            task.promise.then(function(result) {
                console.log(result, profil)
                if (result.err === 0) {
                    app.notify(result.message, "b");
                    $location.path(config.urlParamAccess)
                } else {
                    app.notify(result.message, "m")
                    $location.path(config.urlParamAccess)
                }
            })
        }
    }
}]);

sngs.controller("paramCusrCtrl", ["$scope", "$rootScope", "config", "prmutils", function($scope, $rootScope, config, prmutils) {
    var paramcusr = $scope.paramcusr;
    var app = $scope.app;
    app.view = {
        url: config.urlParamCusr,
        model: paramcusr,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Parametrages",
        subtitle: "Utilisateurs",
        show: true,
        model: {}
    };
    $rootScope.title = "Liste des utilisateurs";
    $rootScope.pageTitle = "Utilisateurs";
    $scope.getcUsers = function() {
        var task = prmutils.getcUsers();
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
    $scope.getcUsers();
    $scope.verrou = function(status, id) {
        var task;
        task = prmutils.statususer(status, id);
        task.promise.then(function(result) {
            $scope.getcUsers()
        })
    };
    $scope.veille = function(status, id) {
        var task;
        task = prmutils.veilleuser(status, id);
        task.promise.then(function(result) {
            $scope.getcUsers()
        })
    };
    $scope.reinit = function(id) {
        var task;
        task = prmutils.reinitps(id);
        task.promise.then(function(result) {
            app.notify(result.message, "b")
        })
    }
}]);
sngs.controller("paramEditCusrCtrl", ["$scope", "$rootScope", "config", "dao", "$location", "$routeParams", "utils", "prmutils", "object", function($scope, $rootScope, config, dao, $location, $routeParams, utils, prmutils, object) {
    var parameditcusr = $scope.parameditcusr;
    var app = $scope.app;
    app.view = {
        url: config.urlParamEditCusr,
        model: parameditcusr,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
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
    app.title = {
        text: "Parametrages",
        subtitle: "Utilisateur",
        show: true,
        model: {}
    };
    $rootScope.pageTitle = "Utilisateur";
    var objectID = ($routeParams.objectID) ? parseInt($routeParams.objectID) : 0;
    $rootScope.title = (objectID > 0) ? "Modification Utilisateur" : "Nouvel Utilisateur";
    $scope.buttonText = (objectID > 0) ? "Modifier" : "Ajouter";
    var original = object.data;
    original.id_user = objectID;
    $scope.user = angular.copy(original);
    $scope.user.id_user = objectID;
    $scope.isClean = function() {
        return angular.equals(original, $scope.user)
    };
    $scope.deleted = function(user) {
        var task;
        if (confirm("Confirmer vous la suppression de l'utilisateur : " + $scope.user.code_user) === true) {
            task = prmutils.deleteUser(user.id_user)
        }
        task.promise.then(function(result) {
            if (result.err === 0) {
                app.refresusrCache();
                app.notify(result.message, "b");
                $location.path(config.urlParamCusr)
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.save = function(user) {
        var task;
        if (!user.profil_user) {
            app.notify(" Veuillez definir un profil..", "m");
            return false
        }
        if (!user.login_user) {
            app.notify(" Veuillez definir un identifiant pour l'utilisateur..", "m");
            return false
        }
        if (objectID <= 0) {
            task = prmutils.insertUser(user);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    if (result.data === "-1") {
                        app.notify(result.message, "m")
                    } else {
                        app.refresusrCache();
                        app.notify(result.message, "b");
                        $location.path(config.urlParamCusr)
                    }
                } else {
                    app.notify("ok ...", "b")
                }
            })
        } else {
            task = prmutils.updateUser(objectID, user);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    app.refresusrCache();
                    app.notify(result.message, "b");
                    $location.path(config.urlParamCusr)
                } else {
                    app.notify("ok ...", "b")
                }
            })
        }
    }
}]);
sngs.controller("paramPflusrCtrl", ["$scope", "$rootScope", "config", "dao", "$location", "$routeParams", "utils", "prmutils", function($scope, $rootScope, config, dao, $location, $routeParams, utils, prmutils) {
    var parameditcusr = $scope.parameditcusr;
    var app = $scope.app;
    app.view = {
        url: null,
        model: parameditcusr,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    task = prmutils.getUser(app.userPfl.id);
    task.promise.then(function(result) {
        app.waiting.show = true;
        if (result.err === 0) {
            $scope.user = result.data;
            app.waiting.show = false
        } else {
            app.waiting.show = false
        }
    });
    app.title = {
        text: "Compte",
        subtitle: "Mon Profil",
        show: true,
        model: {}
    };
    $rootScope.title = "Mise a jour du profil";
    $rootScope.pageTitle = "Mon profil";
    $scope.buttonText = "Mettre a jour";
    $scope.save = function(user) {
        var task;
        if (!angular.equals(user.npwd_user, user.conf_pass)) {
            app.notify(" Les mots de passes ne sont pas identiques..", "m");
            return false
        }
        task = prmutils.updateProfil(app.userPfl.id, user);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    app.notify(result.message, "b")
                }
            } else {
                console.log(result)
                app.notify(result.message, "m")
            }
        })
    }
}]);
sngs.controller("paramStructCtrl", ["$scope", "$rootScope", "config", "dao", "$location", "$routeParams", "utils", "prmutils", function($scope, $rootScope, config, dao, $location, $routeParams, utils, prmutils) {
    var parameditcusr = $scope.parameditcusr;
    var app = $scope.app;
    app.view = {
        url: null,
        model: parameditcusr,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    task = prmutils.getInfosStruct();
    task.promise.then(function(result) {
        app.waiting.show = true;
        if (result.err === 0) {
            $scope.structure = result.data;
            app.waiting.show = false
        } else {
            app.waiting.show = false
        }
    });
    app.title = {
        text: "Structure",
        subtitle: "Notre Structure",
        show: true,
        model: {}
    };
    $rootScope.title = "Mise a jour infos Structure";
    $rootScope.pageTitle = "Structure";
    $scope.buttonText = "Mettre a jour";
    $scope.save = function(structure) {
        var task;
        task = prmutils.updateStructInfo(structure.id_struct, structure);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    app.notify(result.message, "b")
                }
            } else {
                app.notify("ok ...", "b")
            }
        })
    }
}]);
sngs.controller("paramOptCtrl", ["$scope", "$rootScope", "config", "dao", "$location", "$routeParams", "utils", "prmutils", function($scope, $rootScope, config, dao, $location, $routeParams, utils, prmutils) {
    var parameditcusr = $scope.parameditcusr;
    var app = $scope.app;
    app.view = {
        url: null,
        model: parameditcusr,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    task = prmutils.getInfosOptions();
    task.promise.then(function(result) {
        app.waiting.show = true;
        if (result.err === 0) {
            $scope.options = result.data;
            app.waiting.show = false
        } else {
            app.waiting.show = false
        }
    });
    app.title = {
        text: "Options",
        subtitle: "Nos Options",
        show: true,
        model: {}
    };
    $rootScope.title = "Mise a jour options";
    $rootScope.pageTitle = "Options Configs";
    $scope.buttonText = "Mettre a jour";
    $scope.save = function(options) {
        var task;
        task = prmutils.updateOptions(options.id_conf, options);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    app.notify(result.message, "b")
                }
            } else {
                app.notify("ok ...", "b")
            }
        })
    }
}]);
sngs.controller("paramSauvCtrl", ["$scope", "$rootScope", "config", "prmutils", function($scope, $rootScope, config, prmutils) {
    var paramart = $scope.paramart;
    var app = $scope.app;
    app.view = {
        url: config.urlParamArt,
        model: paramart,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Administration",
        subtitle: "Sauvegarde",
        show: true,
        model: {}
    };
    $rootScope.title = "Sauvegarde";
    $rootScope.pageTitle = "Sauvegarde";
    $scope.save = function() {
        var task;
        task = prmutils.doSave();
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    app.notify(result.message, "b")
                }
            } else {
                app.notify("ok ...", "b")
            }
        })
    }
}]);