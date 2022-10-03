sngs.controller("etaAppCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Stock",
        subtitle: "Approvisionnement > Etat approvisionnements",
        show: true,
        model: {}
    };
    $rootScope.title = "Articles Approvisionnes";
    $rootScope.pageTitle = "Etat Approvisionnements";
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
    $scope.searchF = function() {
        var task;
        task = prmutils.getEtaAppro($scope.search);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.approvisionnements = result.data
                }
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.searchF();
    $scope.getTotalQte = function() {
        var total = 0;
        for (var i = 0; i < $scope.filtered.length; i++) {
            var vente = $scope.filtered[i];
            total += parseInt(vente.qte_appro_art)
        }
        return total
    };
    $scope.getTotalMnt = function() {
        var total = 0;
        for (var i = 0; i < $scope.filtered.length; i++) {
            var vente = $scope.filtered[i];
            total += parseInt(vente.qte_appro_art * vente.prix_appro_art)
        }
        return total
    }
}]);
sngs.controller("etaCmdCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Stock",
        subtitle: "Commandes > Etat commandes",
        show: true,
        model: {}
    };
    $rootScope.title = "Articles COmmandes";
    $rootScope.pageTitle = "Etat Commandes";
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
    $scope.searchF = function() {
        var task;
        task = prmutils.getEtaCmd($scope.search);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.approvisionnements = result.data
                }
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.searchF();
    $scope.getTotalQte = function() {
        var total = 0;
        for (var i = 0; i < $scope.filtered.length; i++) {
            var vente = $scope.filtered[i];
            total += parseInt(vente.qte_cmd_art)
        }
        return total
    };
    $scope.getTotalMnt = function() {
        var total = 0;
        for (var i = 0; i < $scope.filtered.length; i++) {
            var vente = $scope.filtered[i];
            total += parseInt(vente.qte_cmd_art * vente.prix_cmd_art)
        }
        return total
    }
}]);
sngs.controller("etaSortCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Stock",
        subtitle: "Sortie > Etat sorties",
        show: true,
        model: {}
    };
    $rootScope.title = "Articles sorties";
    $rootScope.pageTitle = "Etat des sorties";
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
    $scope.getmag = function() {
        task = prmutils.getExceptMagasins();
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
    $scope.searchF = function() {
        var task;
        task = prmutils.getEtaSort($scope.search);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.sorties = result.data
                }
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.searchF()
}]);
sngs.controller("stockEtatCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    $scope.loading = true;
    app.navbar.show = true;
    app.title = {
        text: "Stock",
        subtitle: "Inventaire stock",
        show: true,
        model: {}
    };
    $rootScope.title = "Inventaire du stock des articles";
    $rootScope.pageTitle = "Inventaire du stock";
    $scope.fullSearchText = "";
    $scope.search = {};
    $scope.getmag = function() {
        task = prmutils.getMagasins();
        task.promise.then(function(result) {
            $scope.loading = true;
            if (result.err === 0) {
                $scope.magasins = result.data;
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
        var task = prmutils.loadMorealert($scope.alertes.length);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data.length > 0) {
                    $scope.alertes = $scope.alertes.concat(result.data)
                }
                $scope.loading = false
            } else {
                $scope.loading = false
            }
        })
    };
    $scope.getcat = function() {
        task = prmutils.getCategories();
        task.promise.then(function(result) {
            $scope.loading = true;
            if (result.err === 0) {
                $scope.categories = result.data;
                $scope.loading = false
            } else {
                $scope.loading = false
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
    $scope.approuv = function(data) {
        var objc = {
            p: data.id_stk
        };
        task = prmutils.approuvCorrection(objc);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                app.notify("Correction approuvee avec succes...", "b");
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.searchF = function() {
        $scope.loading = true;
        var task;
        task = prmutils.etatStock($scope.search);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.alertes = result.data
                }
                $scope.loading = false
            } else {
                app.notify("ok ...", "b");
                $scope.loading = false
            }
        })
    };
    $scope.searchFLimit = function() {
        $scope.loading = true;
        var task;
        task = prmutils.etatStocklimit();
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.alertes = result.data
                }
                $scope.loading = false
            } else {
                app.notify("ok ...", "b");
                $scope.loading = false
            }
        })
    };
    $scope.refresh = function() {
        $scope.searchFLimit()
    };
    $scope.fullSearch = function() {
        $scope.loading = true;
        var task;
        if ($scope.fullSearchText.length > 1) {
            task = prmutils.queryEtatStock($scope.fullSearchText)
        } else {
            task = prmutils.etatStocklimit()
        }
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.alertes = result.data
                }
                $scope.loading = false
            } else {
                app.notify("ok ...", "b");
                $scope.loading = false
            }
        })
    };
    $scope.affStock = function(data) {
        $scope.corrstock = data;
        $scope.corrstock.qte_stk = parseInt(data.qte_stk)
    };
    $scope.corrStock = function(data) {
        var task;
        task = prmutils.updateStock($scope.corrstock.id_stk, $scope.corrstock);
        task.promise.then(function(result) {
            if (result.err === 0) {
                app.notify(result.message, "b");
                $scope.corrstock = {}
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.searchAll = function() {
        $scope.search.magasin = null;
        $scope.search.categorie = null;
        $scope.search.article = null;
        $scope.searchF()
    };
    $scope.searchFLimit();
    $scope.loadArticlesOfCategorie = function(cat) {
        task = prmutils.getArticlesOfCategorie(cat);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.articles = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.showReplace = function(art) {
        $("#replacePannel").css("right", "0");
        $scope.anc_art = art
    };
    $scope.replaceItem = function(data) {
        var obj;
        obj = {
            anc: parseInt($scope.anc_art.art_stk),
            newe: parseInt($scope.replace.article)
        };
        task = prmutils.replaceItem(obj);
        task.promise.then(function(result) {
            if (result.err === 0) {
                app.notify(result.message, "b");
                $scope.replace.article = {};
                $scope.anc_art = {}
            } else {
                app.notify("ok ...", "b")
            }
        })
    }
}]);
sngs.controller("stockInvCtrl", ["$scope", '$http', 'config', "$rootScope", "prmutils", "$routeParams", function($scope, $http, config, $rootScope, prmutils, $routeParams) {
    var app = $scope.app;
    $scope.loading = true;
    app.navbar.show = true;
    var objectID = parseInt($routeParams.objectID);
    app.title = {
        text: "Stock",
        subtitle: "Inventaire stock",
        show: true,
        model: {}
    };
    $scope.datas = [];
    $scope.filtres = [];
    $scope.erreur = 0;
    $rootScope.title = "Inventaire stock";
    $rootScope.pageTitle = "Inventaire du stock";
    $scope.fullSearchText = "";
    $scope.search = {};
    $scope.approvisionnement;

    $scope.getInventaire = function() {
        task = prmutils.getInventaire();
        task.promise.then(function(result) {
            $scope.loading = true;
            if (result.err === 0) {
                $scope.inventaire = result.data;
                $scope.loading = false
            } else {
                $scope.loading = false
            }
        }).catch(err => {
            $scope.loading = false;
        })
    };
    $showErrors = false;
    $scope.compteur = 0;
    $scope.showError = function() {
        $showErrors = !$showErrors;
        if ($showErrors) {
            $scope.filtres = [];
            for (const data of $scope.datas) {
                if (data.erreur) $scope.filtres.push(data);
            }
        } else {
            $scope.filtres = angular.copy($scope.datas);
        }
    }
    $scope.importerInventaireDansLaBase = function() {
        $scope.compteur = 0;
        for (const data of $scope.datas) {
            if (data.ecart == 0) {
                $scope.compteur = $scope.compteur + 1;
                continue;
            }
            appstocks = {
                "prix_appro_art": 0,
                "mag_appro_art": app.userPfl.mg,
                "appro_appro_art": objectID,
                "art_appro_art": data.code,
                "qte_appro_art": data.ecart
            }
            console.log(appstocks)
            task = prmutils.insertStockAppro(appstocks);
            task.promise.then(function(result) {
                    $scope.compteur = $scope.compteur + 1;
                    if (result.err === 0) {
                        if (result.data === "-1") {
                            app.notify(result.message, "m");
                            $scope.djob = false
                        } else {
                            app.notify(result.message, "b");
                            $scope.djob = false;
                        }
                    } else {

                        app.notify("ok ...", "b");
                        $scope.djob = false
                    }
                })
                // $scope.loading = false;
        }
    }
    $scope.getInventaire();
    $scope.importInventaire = function() {
        $scope.loading = true;
        // alert(document.getElementById('fichierImporte').files[0]);
        var file_data = $('#fichierImporte').prop('files')[0];
        var form_data = new FormData();
        form_data.append('fichierInventaire', file_data);
        // alert(search);
        $scope.datas = [];
        $scope.erreur = 0;
        $.ajax({
            url: 'app/core/importInventaire.php', // <-- point to server-side PHP script 
            dataType: 'text', // <-- what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function(php_script_response) {
                // alert(php_script_response); // <-- display response from the PHP script, if any
                console.log(php_script_response)
                $scope.loading = false;
                $scope.datas = JSON.parse(php_script_response);
                $scope.datas = $scope.datas.datas;
                $scope.filtres = angular.copy($scope.datas);
                for (const data of $scope.datas) {
                    if (data.erreur) $scope.erreur = $scope.erreur + 1;
                }
                $scope.$apply();
            }
        });

        // task = prmutils.importInventaire(fd);

        // task.promise.then(function (result) {
        //     $scope.loading = true;
        //     console.log(result);
        //     if (result.err === 0) {
        //         $scope.data = result.data;
        //         $scope.loading = false
        //     } else {
        //         $scope.loading = false
        //     }
        // })
    }

}]);

sngs.controller("stockSessionCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    $scope.loading = true;
    app.navbar.show = true;
    app.title = {
        text: "Stock",
        subtitle: "Inventaire stock",
        show: true,
        model: {}
    };
    $rootScope.title = "Session inventaire du stock des articles";
    $rootScope.pageTitle = "Session inventaire du stock";
    $scope.fullSearchText = "";
    $scope.search = {};
    $scope.getmag = function() {
        task = prmutils.getMagasins();
        task.promise.then(function(result) {
            $scope.loading = true;
            if (result.err === 0) {
                $scope.magasins = result.data;
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
        var task = prmutils.loadMorealert($scope.alertes.length);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data.length > 0) {
                    $scope.alertes = $scope.alertes.concat(result.data)
                }
                $scope.loading = false
            } else {
                $scope.loading = false
            }
        })
    };
    $scope.getcat = function() {
        task = prmutils.getCategories();
        task.promise.then(function(result) {
            $scope.loading = true;
            if (result.err === 0) {
                $scope.categories = result.data;
                $scope.loading = false
            } else {
                $scope.loading = false
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
    $scope.approuv = function(data) {
        var objc = {
            p: data.id_stk
        };
        task = prmutils.approuvCorrection(objc);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                app.notify("Correction approuvee avec succes...", "b");
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.searchF = function() {
        $scope.loading = true;
        var task;
        task = prmutils.etatStock($scope.search);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.alertes = result.data
                }
                $scope.loading = false
            } else {
                app.notify("ok ...", "b");
                $scope.loading = false
            }
        })
    };
    $scope.searchFLimit = function() {
        $scope.loading = true;
        var task;
        task = prmutils.etatStocklimit();
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.alertes = result.data
                }
                $scope.loading = false
            } else {
                app.notify("ok ...", "b");
                $scope.loading = false
            }
        })
    };
    $scope.refresh = function() {
        $scope.searchFLimit()
    };
    $scope.fullSearch = function() {
        $scope.loading = true;
        var task;
        if ($scope.fullSearchText.length > 1) {
            task = prmutils.queryEtatStock($scope.fullSearchText)
        } else {
            task = prmutils.etatStocklimit()
        }
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.alertes = result.data
                }
                $scope.loading = false
            } else {
                app.notify("ok ...", "b");
                $scope.loading = false
            }
        })
    };
    $scope.affStock = function(data) {
        $scope.corrstock = data;
        $scope.corrstock.qte_stk = parseInt(data.qte_stk)
    };
    $scope.corrStock = function(data) {
        var task;
        task = prmutils.updateStock($scope.corrstock.id_stk, $scope.corrstock);
        task.promise.then(function(result) {
            if (result.err === 0) {
                app.notify(result.message, "b");
                $scope.corrstock = {}
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.searchAll = function() {
        $scope.search.magasin = null;
        $scope.search.categorie = null;
        $scope.search.article = null;
        $scope.searchF()
    };
    $scope.searchFLimit();
    $scope.loadArticlesOfCategorie = function(cat) {
        task = prmutils.getArticlesOfCategorie(cat);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.articles = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.showReplace = function(art) {
        $("#replacePannel").css("right", "0");
        $scope.anc_art = art
    };
    $scope.replaceItem = function(data) {
        var obj;
        obj = {
            anc: parseInt($scope.anc_art.art_stk),
            newe: parseInt($scope.replace.article)
        };
        task = prmutils.replaceItem(obj);
        task.promise.then(function(result) {
            if (result.err === 0) {
                app.notify(result.message, "b");
                $scope.replace.article = {};
                $scope.anc_art = {}
            } else {
                app.notify("ok ...", "b")
            }
        })
    }
}]);
sngs.controller("AutreStockEtatCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    $scope.loading = true;
    app.navbar.show = true;
    app.title = {
        text: "Stock",
        subtitle: "Autres stock",
        show: true,
        model: {}
    };
    $rootScope.title = "Autres stock des articles";
    $rootScope.pageTitle = "Autre stock";
    $scope.fullSearchText = "";
    $scope.search = {};
    $scope.fullSearch = function() {
        $scope.loading = true;
        var task;
        if ($scope.fullSearchText.length > 1) {
            task = prmutils.queryAuStock($scope.fullSearchText)
        } else {
            $scope.alertes = {};
            return false
        }
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.alertes = result.data
                }
                $scope.loading = false
            } else {
                app.notify("ok ...", "b");
                $scope.loading = false
            }
        })
    }
}]);
sngs.controller("stockEtatTrsfCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Stock",
        subtitle: "Etat Transfert",
        show: true,
        model: {}
    };
    $rootScope.title = "Etat des transferts";
    $rootScope.pageTitle = "Etat des transferts";
    $scope.search = {};
    $scope.supok = false;
    task = prmutils.getLimitedMagasins();
    task.promise.then(function(result) {
        app.waiting.show = true;
        if (result.err === 0) {
            $scope.magasins = result.data;
            app.waiting.show = false
        } else {
            app.waiting.show = false
        }
    });
    task = prmutils.getAllMagasins();
    task.promise.then(function(result) {
        app.waiting.show = true;
        if (result.err === 0) {
            $scope.magasinsd = result.data;
            app.waiting.show = false
        } else {
            app.waiting.show = false
        }
    });
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
    $scope.undot = function(fac) {
        var task = prmutils.undoTransf(fac);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                fac.supok = true;
                app.waiting.show = false
            } else {
                app.waiting.show = false;
                app.notify("Ok...", "b")
            }
        })
    };
    $scope.searchF = function() {
        var task;
        task = prmutils.etatTransf($scope.search);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.alertes = result.data
                }
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.searchF();
    $scope.select = function(index) {
        $scope.index = index
    }
}]);
sngs.controller("stockEtatDeffCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Stock",
        subtitle: "Etat Destockage",
        show: true,
        model: {}
    };
    $rootScope.title = "Etat des destockage";
    $rootScope.pageTitle = "Etat des destockage";
    $scope.search = {};
    task = prmutils.getLimitedMagasins();
    task.promise.then(function(result) {
        app.waiting.show = true;
        if (result.err === 0) {
            $scope.magasins = result.data;
            app.waiting.show = false
        } else {
            app.waiting.show = false
        }
    });
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
    $scope.undot = function(fac) {
        var task = prmutils.undoDeff(fac);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                app.notify(result.message, "b");
                $scope.searchF();
                app.waiting.show = false
            } else {
                app.waiting.show = false;
                app.notify("Ok...", "b")
            }
        })
    };
    $scope.vudef = function(fac) {
        var task = prmutils.vudef(fac);
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
    $scope.tvudef = function() {
        var task = prmutils.tvudef();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                for (var i = 0; i < $scope.alertes.length; i++) {
                    $scope.alertes[i].vu = 1
                }
                $rootScope.defnv = 0;
                app.waiting.show = false
            } else {
                app.waiting.show = false;
                app.notify("Ok...", "b")
            }
        })
    };
    $scope.searchF = function() {
        var task;
        task = prmutils.etatDeff($scope.search);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.alertes = result.data
                }
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.searchF()
}]);
sngs.controller("stockAlerteCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Stock",
        subtitle: "Alerte stock",
        show: true,
        model: {}
    };
    $rootScope.title = "Liste des articles en rupture de stock";
    $rootScope.pageTitle = "Ruptures de stock";
    $scope.search = {};
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
    $scope.searchF = function() {
        var task;
        task = prmutils.etatAlerte($scope.search);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.alertes = result.data
                }
            } else {
                app.notify("ok ...", "m")
            }
        })
    };
    $scope.searchF()
}]);
sngs.controller("stockAsCtrl", ["$scope", "$rootScope", "config", "prmutils", function($scope, $rootScope, config, prmutils) {
    var stockas = $scope.stockas;
    var app = $scope.app;
    app.view = {
        url: config.urlStockAs,
        model: stockas,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Stock",
        subtitle: "Provision de stock",
        show: true,
        model: {}
    };
    $rootScope.title = "Enregistrement d'un nouveau stock";
    $rootScope.pageTitle = "Entrees de stock";
    $scope.djob = false;
    $scope.stock = {
        qte_stk: 0
    };
    $scope.prices = {
        prix_mini: 0,
        prix_gros: 0
    };
    $scope.appstock = {};
    $scope.appstock.prix_appro_art = 0;
    $scope.getappo = function() {
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
    task = prmutils.getMagasins();
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
    $scope.gae = function() {
        task = prmutils.getArticleEntrees($scope.appstock.appro_appro_art);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.articlesentrees = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.undobl = function(data) {
        var task = prmutils.undoEntArt(data);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.gae();
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.save = function(appstock) {
        var task;
        $scope.djob = true;
        if (parseInt($scope.appstock.prix_appro_art) <= 0) {
            $scope.appstock.prix_appro_art = 0
        }
        if (parseInt($scope.appstock.prix_mini_art_mag) <= 0) {
            app.notify("Veuillez entrer un prix minimum  superieure a 0", "m");
            return false
        }
        if (parseInt($scope.appstock.prix_gros_art_mag) <= 0) {
            app.notify("Veuillez entrer un prix gros  superieure a 0", "m");
            return false
        }
        if ((parseInt($scope.appstock.prix_mini_art_mag) > 0 && ($scope.appstock.prix_gros_art_mag === null || typeof $scope.appstock.prix_gros_art_mag === undefined)) || (($scope.appstock.prix_mini_art_mag === null || typeof $scope.appstock.prix_mini_art_mag === undefined) && parseInt($scope.appstock.prix_gros_art_mag) > 0)) {
            app.notify("Veuillez definir les deux prix ensembles ", "m");
            return false
        }
        console.log(appstock);
        task = prmutils.insertStockAppro(appstock);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    $scope.appstock.art_appro_art = "";
                    $scope.appstock.qte_appro_art = "";
                    $scope.appstock.prix_appro_art = 0;
                    app.notify(result.message, "m");
                    $scope.djob = false
                } else {
                    $scope.form.$setPristine();
                    $scope.appstock.art_appro_art = "";
                    $scope.appstock.qte_appro_art = "";
                    $scope.appstock.prix_appro_art = 0;
                    app.refreshextcatmagCache();
                    app.notify(result.message, "b");
                    $scope.djob = false;
                    $scope.gae()
                }
            } else {
                app.refreshextcatmagCache();
                app.notify("ok ...", "b");
                $scope.djob = false
            }
        });
        $scope.stock = {
            qte_stk: 0
        };
        $scope.prices = {
            prix_mini: 0,
            prix_gros: 0
        }
    };
    $scope.loadArticlesOfCategorie = function(cat) {
        if (typeof cat == "undefined") {
            task = prmutils.getArticles()
        } else {
            task = prmutils.getArticlesOfCategorie(cat)
        }
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.articles = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.loadArticles = function(cat) {
        task = prmutils.getArticles();
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
        $scope.loadArticles(0)
    }
    if (!app.PRMS.liopl && !app.PRMS.cat) {
        $scope.loadArticles(0)
    }
    $scope.getStock = function(art, mag) {
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
        });
        $scope.getPrices(art, mag)
    };
    $scope.getPrices = function(art, mag) {
        task = prmutils.getPrices(art, mag);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === null) {
                    $scope.prices = {
                        prix_mini: 0,
                        prix_gros: 0
                    }
                } else {
                    $scope.prices = result.data
                }
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.$on("$destroy", function() {
        var task;
        task = prmutils.statusappro(0, $scope.appstock.appro_appro_art)
    })
}]);
sngs.controller("stockCmdCtrl", ["$scope", "$rootScope", "config", "prmutils", function($scope, $rootScope, config, prmutils) {
    var stockas = $scope.stockas;
    var app = $scope.app;
    app.view = {
        url: config.urlStockAs,
        model: stockas,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Stock",
        subtitle: "Commande",
        show: true,
        model: {}
    };
    $rootScope.title = "Enregistrement d'une nouvelle commande stock";
    $rootScope.pageTitle = "Commande de stock";
    $scope.djob = false;
    $scope.stock = {
        qte_stk: 0
    };
    $scope.prices = {
        prix_mini: 0,
        prix_gros: 0
    };
    $scope.appstock = {};
    $scope.appstock.prix_cmd_art = 0;
    $scope.getappo = function() {
        var task = prmutils.getCommandes();
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
    $scope.gae = function() {
        task = prmutils.getArticleCommandees($scope.appstock.cmd_cmd_art);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.articlesentrees = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.undobl = function(data) {
        var task = prmutils.undoEntArt(data);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.gae();
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.save = function(appstock) {
        var task;
        $scope.djob = true;
        if (parseInt($scope.appstock.prix_cmd_art) <= 0) {
            $scope.appstock.prix_cmd_art = 0
        }
        task = prmutils.insertStockCmd(appstock);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    $scope.appstock.art_cmd_art = "";
                    $scope.appstock.qte_cmd_art = "";
                    $scope.appstock.prix_cmd_art = 0;
                    app.notify(result.message, "m");
                    $scope.djob = false
                } else {
                    $scope.form.$setPristine();
                    $scope.appstock.art_cmd_art = "";
                    $scope.appstock.qte_cmd_art = "";
                    $scope.appstock.prix_cmd_art = 0;
                    app.notify(result.message, "b");
                    $scope.djob = false;
                    $scope.gae()
                }
            } else {
                app.notify("ok ...", "b");
                $scope.djob = false
            }
        });
        $scope.stock = {
            qte_stk: 0
        };
        $scope.prices = {
            prix_mini: 0,
            prix_gros: 0
        }
    };
    $scope.loadArticlesOfCategorie = function(cat) {
        if (typeof cat == "undefined") {
            task = prmutils.getArticles()
        } else {
            task = prmutils.getArticlesOfCategorie(cat)
        }
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.articles = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.loadArticles = function(cat) {
        task = prmutils.getArticles();
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
        $scope.loadArticles(0)
    }
    if (!app.PRMS.liopl && !app.PRMS.cat) {
        $scope.loadArticles(0)
    }
    $scope.getStock = function(art, mag) {
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
        });
        $scope.getPrices(art, mag)
    };
    $scope.getPrices = function(art, mag) {
        task = prmutils.getPrices(art, mag);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === null) {
                    $scope.prices = {
                        prix_mini: 0,
                        prix_gros: 0
                    }
                } else {
                    $scope.prices = result.data
                }
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.$on("$destroy", function() {
        var task
    })
}]);
sngs.controller("stockDeffCtrl", ["$scope", "$rootScope", "config", "prmutils", function($scope, $rootScope, config, prmutils) {
    var stockas = $scope.stockas;
    var app = $scope.app;
    app.view = {
        url: config.urlStockAs,
        model: stockas,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Stock",
        subtitle: "Destockage",
        show: true,
        model: {}
    };
    $rootScope.title = "Destockage des produits/articles";
    $rootScope.pageTitle = "Destockage articles";
    $scope.appdef = {};
    task = prmutils.getMagasins();
    task.promise.then(function(result) {
        app.waiting.show = true;
        if (result.err === 0) {
            $scope.magasins = result.data;
            $scope.appdef.mag_def = $scope.magasins[0].id_mag;
            app.waiting.show = false
        } else {
            app.waiting.show = false
        }
    });
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
    $scope.save = function(appdef) {
        var task;
        task = prmutils.insertStockDeff(appdef);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    $scope.appdef.art_def = "";
                    $scope.appdef.qte_def = "";
                    app.notify(result.message, "m")
                } else {
                    $scope.appdef.art_def = "";
                    $scope.appdef.qte_def = "";
                    app.notify(result.message, "b")
                }
            } else {
                app.notify("ok ...", "b")
            }
        });
        $scope.stock.qte_stk = 0
    };
    $scope.loadArticlesOfCategorie = function(cat) {
        task = prmutils.getArticlesOfCategorie(cat);
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
    $scope.getStock = function(art, mag) {
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
    }
}]);
sngs.controller("stockTrfCtrl", ["$scope", "$rootScope", "prmutils", function($scope, $rootScope, prmutils) {
    var app = $scope.app;
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Stock",
        subtitle: "Transfert de stock",
        show: true,
        model: {}
    };
    $rootScope.title = "Nouveau transfert de stock";
    $rootScope.pageTitle = "Transferts de stock";
    $scope.stockd = {
        qte_stk: 0
    };
    $scope.stocks = {
        qte_stk: 0
    };
    $scope.appstock = {};
    task = prmutils.getExceptMagasins();
    task.promise.then(function(result) {
        app.waiting.show = true;
        if (result.err === 0) {
            $scope.magasins = result.data;
            app.waiting.show = false
        } else {
            app.waiting.show = false
        }
    });
    task = prmutils.getLimitedMagasins();
    task.promise.then(function(result) {
        app.waiting.show = true;
        if (result.err === 0) {
            $scope.lmagasins = result.data;
            $scope.appstock.mag_src_transf = $scope.lmagasins[0].id_mag;
            app.waiting.show = false
        } else {
            app.waiting.show = false
        }
    });
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
    $scope.save = function(appstock) {
        var task;
        if (parseInt($scope.appstock.qte_transf) <= 0) {
            app.notify("Veuillez entrer une quantite  superieure a 0", "m");
            return false
        }
        if (angular.equals(parseInt($scope.appstock.mag_dst_transf), parseInt($scope.appstock.mag_src_transf))) {
            app.notify("La boutique/Magasin doit etre different(e) de celle(delui) de destination...", "m");
            return false
        }
        task = prmutils.transfStock(appstock);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    $scope.reset();
                    app.notify(result.message, "m")
                } else {
                    $scope.reset();
                    app.notify(result.message, "b");
                    $scope.getStocks(appstock.art_transf, appstock.mag_src_transf);
                    $scope.getStockd(appstock.art_transf, appstock.mag_dst_transf)
                }
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.loadArticlesOfCategorie = function(cat) {
        task = prmutils.getExtArticlesOfCategorie(0, cat);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.articles = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        });
        $scope.reset()
    };
    $scope.loadArticlesForSell = function() {
        task = prmutils.loadArticlesForSell();
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
        $scope.loadArticlesForSell()
    }
    if (!app.PRMS.liopl && !app.PRMS.cat) {
        $scope.loadArticlesForSell()
    }
    $scope.getStocks = function(art, mag) {
        task = prmutils.getTransStock(art, mag);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === null) {
                    $scope.stocks = {
                        qte_stk: 0
                    }
                } else {
                    $scope.stocks = result.data
                }
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getStockd = function(art, mag) {
        task = prmutils.getTransStock(art, mag);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === null) {
                    $scope.stockd = {
                        qte_stk: 0
                    }
                } else {
                    $scope.stockd = result.data
                }
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.validStk = function() {
        $scope.appstock.qte_transf = (parseInt($scope.appstock.qte_transf) > parseInt($scope.stocks.qte_stk)) ? parseInt(0) : parseInt($scope.appstock.qte_transf)
    };
    $scope.reset = function() {
        $scope.appstock.qte_transf = 0
    }
}]);
sngs.controller("stockSsCtrl", ["$scope", "$rootScope", "config", "prmutils", function($scope, $rootScope, config, prmutils) {
    var stockas = $scope.stockas;
    var app = $scope.app;
    app.view = {
        url: config.urlStockAs,
        model: stockas,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Stock",
        subtitle: "Sortie de stock",
        show: true,
        model: {}
    };
    $rootScope.title = "Enregistrement d'une nouvelle sortie de stock";
    $rootScope.pageTitle = "Sorties de stock";
    $scope.djob = false;
    $scope.stock = {
        qte_stk: 0
    };
    $scope.prices = {
        prix_mini: 0,
        prix_gros: 0
    };
    $scope.sortstock = {};
    $scope.getsort = function() {
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
    $scope.getcat = function(mag) {
        task = prmutils.getExtCategoriesOfMag(mag);
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
    $scope.save = function(sortstock) {
        var task;
        if (parseInt($scope.sortstock.qte_sort_art) <= 0) {
            app.notify("Veuillez entrer une quantite superieure a 0", "m");
            return false
        }
        if (parseInt($scope.sortstock.qte_sort_art) > parseInt($scope.stock.qte_stk)) {
            app.notify("La quantite demandee est superieure au stock disponible..", "m");
            return false
        }
        $scope.djob = true;
        task = prmutils.insertStockSort(sortstock);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    $scope.sortstock.art_sort_art = "";
                    $scope.sortstock.qte_sort_art = "";
                    app.notify(result.message, "m");
                    $scope.djob = false
                } else {
                    $scope.form.$setPristine();
                    $scope.sortstock.art_sort_art = "";
                    $scope.sortstock.qte_sort_art = "";
                    app.notify(result.message, "b");
                    $scope.djob = false
                }
            } else {
                app.notify("ok ...", "b");
                $scope.djob = false
            }
        });
        $scope.stock = {
            qte_stk: 0
        };
        $scope.gas()
    };
    $scope.loadArticlesOfCategorie = function(cat) {
        task = prmutils.getExtArticlesOfCategorie(0, cat);
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
    $scope.getStock = function(art, mag) {
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
    $scope.gas = function() {
        task = prmutils.getArticleSorties($scope.sortstock.sort_sort_art);
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.articlessorties = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.undobs = function(data) {
        var task = prmutils.undoSortArt(data);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.gas();
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.$on("$destroy", function() {
        var task;
        task = prmutils.statussort(0, $scope.sortstock.sort_sort_art)
    })
}]);
sngs.controller("stockBsCtrl", ["$scope", "$rootScope", "config", "prmutils", function($scope, $rootScope, config, prmutils) {
    var stockba = $scope.stockba;
    var app = $scope.app;
    app.view = {
        url: config.urlStockBs,
        model: stockba,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Stock",
        subtitle: "Bons de sorties",
        show: true,
        model: {}
    };
    $rootScope.title = "Liste des bons de sorties";
    $rootScope.pageTitle = "Bons de sorties";
    $scope.getaSorties = function() {
        app.waiting.show = true;
        var task = prmutils.getaSorties();
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.sorties = result.data;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getaSorties();
    $scope.status = function(status, id) {
        var task;
        task = prmutils.statussort(status, id);
        task.promise.then(function(result) {
            $scope.getaSorties()
        })
    };
    $scope.vusrt = function(fac) {
        var task = prmutils.vusrt(fac);
        task.promise.then(function(result) {
            app.waiting.show = true;
            app.notify(fac.message)
            if (result.err === 0) {
                fac.vu = 1;
                app.waiting.show = false
            } else {
                app.waiting.show = false;
                app.notify("Ok...", "b")
            }
        })
    };
    $scope.tvusrt = function() {
        var task = prmutils.tvusrt();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                for (var i = 0; i < $scope.sorties.length; i++) {
                    $scope.sorties[i].vu = 1
                }
                $rootScope.srtnv = 0;
                app.waiting.show = false
            } else {
                app.waiting.show = false;
                app.notify("Ok...", "b")
            }
        })
    }
    $scope.showDetails = function(bonEntree) {
        app.waiting.show = true;
        console.log(bonEntree)
        $scope.num_BonEntree = bonEntree.bon_sort;
        $scope.bonEntree = bonEntree;
        console.log(bonEntree)
        $("#detailsPannel").css("right", "0")
        app.waiting.show = false
    };


    $scope.rejetersrt = function(bonEntree) {
        var task = prmutils.rejetersrt(bonEntree);
        task.promise.then(function(result) {
            if (result.err === 0) {
                app.notify("Le bon a été relancé", "b");
                bonEntree.rejeter = 0;
                $("#detailsPannel").css("right", "-800px");
            } else {
                console.log(result)
                app.notify(result.message, 'm')
            }
        })
    };
}]);
sngs.controller("stockEditBsCtrl", ["$scope", "$rootScope", "config", "$location", "$routeParams", "$filter", "prmutils", "object", function($scope, $rootScope, config, $location, $routeParams, $filter, prmutils, object) {
    var stockeditba = $scope.stockeditba;
    var app = $scope.app;
    app.view = {
        url: config.urlStockEditBs,
        model: stockeditba,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    $scope.sortie = {};
    app.title = {
        text: "Stock",
        subtitle: "Bon de sortie",
        show: true,
        model: {}
    };
    $rootScope.pageTitle = "Bon de sortie";
    var datebs;
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
    datebs = dd + "/" + mm + "/" + yyyy;
    $scope.sortie.date_sort = datebs;
    prmutils.getExceptMagasins().promise.then(function(result) {
        $scope.magasins = result.data
    });
    var objectID = ($routeParams.objectID) ? parseInt($routeParams.objectID) : 0;
    $rootScope.title = (objectID > 0) ? "Modification Bon" : "Nouveau Bon de sortie";
    $scope.buttonText = (objectID > 0) ? "Modifier" : "Ajouter";
    $scope.isDisabled = (objectID > 0) ? false : false;
    var original = object.data;
    var prec_ben = original.id_mag;
    original.id_sort = objectID;
    $scope.sortie = angular.copy(original);
    $scope.sortie.id_sort = objectID;
    if (objectID < 1) {
        $scope.sortie = {}
    }
    $scope.getdateDs = function() {
        var task = prmutils.getDs();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                if (!$scope.sortie.bon_sort)
                    $scope.sortie.date_sort = result.data.datej;
                else {
                    $scope.sortie.date_sort = new Date($scope.sortie.date_sort).toLocaleDateString();

                }
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.isClean = function() {
        return angular.equals(original, $scope.sortie)
    };

    $scope.rejetersrt = function(fac) {
        var task = prmutils.rejetersrt(fac);
        task.promise.then(function(result) {
            if (result.err === 0) {
                app.notify("Le bon a été relancé", "b");
                $location.path(config.urlStockBs)
            } else {
                console.log(result)
                app.notify(result.message, 'm')
            }
        })
    };
    $scope.deleted = function(sortie) {
        var task;
        if (confirm("Confirmer vous la suppression du bon de sortie : " + $scope.sortie.bon_sort) === true) {
            task = prmutils.deleteSortie(sortie.id_sort)
        }
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    app.notify(result.message, "b");
                    $location.path(config.urlStockBs)
                }
            } else {
                app.notify("ok...", "b")
            }
        })
    };
    $scope.save = function(sortie) {
        var task;
        if (!prmutils.isDate(sortie.date_sort)) {
            app.notify("Le format de la date est incorrect", "m");
            return false
        }
        if (objectID <= 0) {
            task = prmutils.insertSortie(sortie);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    if (result.data === "-1") {
                        app.notify(result.msg, "m")
                    } else {
                        app.notify(result.msg, "b");
                        $location.path(config.urlStockBs)
                    }
                } else {
                    app.notify(result.msg, "m")
                }
            })
        } else {
            sortie.prec_ben = prec_ben;
            task = prmutils.updateSortie(objectID, sortie);
            task.promise.then(function(result) {
                console.log(result)
                if (result.err === 0) {
                    if (result.data === "-1") {
                        app.notify(result.msg == undefined ? result.message : result.msg, "m")
                    } else {
                        app.notify(result.msg == undefined ? result.message : result.msg, "b");
                        $location.path(config.urlStockBs)
                    }
                } else {
                    console.log(result);
                    app.notify(result.msg, "m");
                }
            })
        }
    };
    $scope.getdateDs()
}]);
sngs.controller("stockBaCtrl", ["$scope", "$rootScope", "config", "prmutils", function($scope, $rootScope, config, prmutils) {
    var stockba = $scope.stockba;
    var app = $scope.app;
    app.view = {
        url: config.urlStockBa,
        model: stockba,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Stock",
        subtitle: "Approvisionnements / Bons d'entrees",
        show: true,
        model: {}
    };
    $scope.fullSearchText = "";
    $scope.search = {};
    $rootScope.title = "Liste des bons d'entrees";
    $rootScope.pageTitle = "Bons d'entrees";
    $scope.getaApprovisionnements = function() {
        var task = prmutils.getaApprovisionnements();
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
    $scope.getAll = function() {
        $scope.getaApprovisionnements()
    };
    $scope.status = function(status, id, data) {
        if (data.openclose === "1") {
            data.openclose = "0"
        } else {
            data.openclose = "1"
        }
        var task;
        task = prmutils.statusappro(status, id);
        task.promise.then(function(result) {})
    };
    $scope.showDetails = function(fac) {
        $scope.num_fact = fac.bon_liv_appro;
        $scope.facture = fac;
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
    };
    $scope.getLimit = function() {
        $scope.loading = true;
        var task = prmutils.getLmApprovisionnements();
        task.promise.then(function(result) {
            $scope.loading = true;
            if (result.err === 0) {
                $scope.approvisionnements = result.data;
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
            task = prmutils.queryApprovisionnements($scope.fullSearchText)
        } else {
            task = prmutils.getLmApprovisionnements()
        }
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.approvisionnements = result.data;
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
        if (!$scope.objectIsNull($scope.search)) {
            return false
        }
        $scope.loading = true;
        var task = prmutils.loadapproMore($scope.approvisionnements.length);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data.length > 0) {
                    $scope.approvisionnements = $scope.approvisionnements.concat(result.data)
                }
                $scope.loading = false
            } else {
                $scope.loading = false
            }
        })
    };
    $scope.getLimit();
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
    $scope.searchF = function() {
        var task;
        task = prmutils.getEtaBl($scope.search);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.approvisionnements = result.data
                }
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.getTotalMnt = function() {
        var total = 0;
        for (var i = 0; i < $scope.filtered.length; i++) {
            var vente = $scope.filtered[i];
            total += parseInt(vente.mnt_revient_appro)
        }
        return total
    };
    $scope.getTotalAchat = function() {
        var total = 0;
        for (var i = 0; i < $scope.filtere.length; i++) {
            var achat = $scope.filtere[i];
            total += parseInt(achat.prix_appro_art * achat.qte_appro_art)
        }
        return total
    };
    $scope.objectIsNull = function(obj) {
        var rep = true;
        angular.forEach(obj, function(value, key) {
            if ((value !== null) && (value !== "")) {
                rep = false;
                return false
            }
        });
        return rep
    }
}]);
sngs.controller("stockBcmdCtrl", ["$scope", "$rootScope", "config", "prmutils", function($scope, $rootScope, config, prmutils) {
    var stockba = $scope.stockba;
    var app = $scope.app;
    app.view = {
        url: config.urlStockBa,
        model: stockba,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Stock",
        subtitle: "Commande / Bons de commandes",
        show: true,
        model: {}
    };
    $scope.fullSearchText = "";
    $scope.search = {};
    $rootScope.title = "Liste des bons de commande";
    $rootScope.pageTitle = "Bons de commande";
    $scope.getaApprovisionnements = function() {
        var task = prmutils.getCommandes();
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
    $scope.getAll = function() {
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
    $scope.statusca = function(status, id) {
        var task;
        var daterecu = "";
        if (status === 1) {
            var vls = prompt("Veuillez la date de reception de l'article (au format ::JJ/MM/AAAA::");
            if (vls) {
                daterecu = vls
            } else {
                app.notify("Veuillez preciser la date de reception de l'article", "m");
                return false
            }
        }
        task = prmutils.statusca(status, daterecu, id);
        task.promise.then(function(result) {
            for (i = 0; i < $scope.approvisionnements.length; i++) {
                if (result.data.cmd.id_cmd === $scope.approvisionnements[i].id_cmd) {
                    $scope.approvisionnements[i] = result.data.cmd;
                    break
                }
            }
            for (i = 0; i < $scope.details.length; i++) {
                if (result.data.detailcmd.id_cmd_art === $scope.details[i].id_cmd_art) {
                    $scope.details[i] = result.data.detailcmd;
                    break
                }
            }
        })
    };
    $scope.statusc = function(status, id) {
        var task;
        var daterecu = "";
        if (status === 1) {
            var vls = prompt("Veuillez la date de reception de l'article (au format ::JJ/MM/AAAA::");
            if (vls) {
                daterecu = vls
            } else {
                app.notify("Veuillez preciser la date de reception de l'article", "m");
                return false
            }
        }
        task = prmutils.statusc(status, daterecu, id);
        task.promise.then(function(result) {
            $scope.getAll()
        })
    };
    $scope.status = function(status, id) {
        var task;
        task = prmutils.statuscmd(status, id);
        task.promise.then(function(result) {
            for (i = 0; i < $scope.approvisionnements.length; i++) {
                if (result.data.id_cmd === $scope.approvisionnements[i].id_cmd) {
                    $scope.approvisionnements[i] = result.data;
                    break
                }
            }
        })
    };
    $scope.showDetails = function(fac) {
        $scope.num_fact = fac.bon_cmd;
        $scope.nid_cmd = fac.id_cmd;
        $scope.facture = fac;
        var task = prmutils.showCmdDetails(fac);
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
    $scope.details = function(fac) {
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
    $scope.getLimit = function() {
        $scope.loading = true;
        var task = prmutils.getLmCommandes();
        task.promise.then(function(result) {
            $scope.loading = true;
            if (result.err === 0) {
                $scope.approvisionnements = result.data;
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
            task = prmutils.queryCommandes($scope.fullSearchText)
        } else {
            task = prmutils.getLmCommandes()
        }
        task.promise.then(function(result) {
            if (result.err === 0) {
                $scope.approvisionnements = result.data;
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
        if (!$scope.objectIsNull($scope.search)) {
            return false
        }
        $scope.loading = true;
        var task = prmutils.loadcmdMore($scope.approvisionnements.length);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data.length > 0) {
                    $scope.approvisionnements = $scope.approvisionnements.concat(result.data)
                }
                $scope.loading = false
            } else {
                $scope.loading = false
            }
        })
    };
    $scope.getLimit();
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
    $scope.searchF = function() {
        var task;
        task = prmutils.getEtaBc($scope.search);
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    $scope.approvisionnements = result.data
                }
            } else {
                app.notify("ok ...", "b")
            }
        })
    };
    $scope.getTotalMnt = function() {
        var total = 0;
        for (var i = 0; i < $scope.filtered.length; i++) {
            var vente = $scope.filtered[i];
            total += parseInt(vente.mnt_revient_cmd)
        }
        return total
    };
    $scope.getTotalAchat = function() {
        var total = 0;
        for (var i = 0; i < $scope.filtere.length; i++) {
            var achat = $scope.filtere[i];
            total += parseInt(achat.prix_cmd_art * achat.qte_cmd_art)
        }
        return total
    };
    $scope.objectIsNull = function(obj) {
        var rep = true;
        angular.forEach(obj, function(value, key) {
            if ((value !== null) && (value !== "")) {
                rep = false;
                return false
            }
        });
        return rep
    }
}]);
sngs.controller("stockEditBaCtrl", ["$scope", "$rootScope", "config", "$location", "$routeParams", "prmutils", "$filter", "object", function($scope, $rootScope, config, $location, $routeParams, prmutils, $filter, object) {
    var stockeditba = $scope.stockeditba;
    var app = $scope.app;
    app.view = {
        url: config.urlStockEditBa,
        model: stockeditba,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Stock",
        subtitle: "Bon de livraison",
        show: true,
        model: {}
    };
    $rootScope.pageTitle = "Bon de livraison";
    prmutils.getFournisseurs().promise.then(function(result) {
        $scope.fournisseurs = result.data
    });
    var objectID = ($routeParams.objectID) ? parseInt($routeParams.objectID) : 0;
    $rootScope.title = (objectID > 0) ? "Modification Bon" : "Nouveau Bon de livraison";
    $scope.buttonText = (objectID > 0) ? "Modifier" : "Ajouter";
    $scope.isDisabled = (objectID > 0) ? false : false;
    var original = object.data;
    original.id_appro = objectID;
    $scope.approvisionnement = angular.copy(original);
    $scope.approvisionnement.id_appro = objectID;
    $scope.approvisionnement.mnt_revient_appro = parseInt($scope.approvisionnement.mnt_revient_appro);
    $scope.approvisionnement.dette_appro = parseInt($scope.approvisionnement.dette_appro);
    $scope.approvisionnement.bl_bon_dette = parseInt($scope.approvisionnement.bl_bon_dette);
    if (objectID < 1) {
        $scope.approvisionnement = {}
    }
    $scope.getdateDs = function() {
        var task = prmutils.getDs();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.approvisionnement.date_appro = result.data.datej;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.isClean = function() {
        return angular.equals(original, $scope.approvisionnement)
    };
    $scope.deleted = function(approvisionnement) {
        var task;
        if (confirm("Confirmer vous la suppression du bon l'approvisionnement : " + $scope.approvisionnement.bon_liv_appro) === true) {
            task = prmutils.deleteApprovisionnement(approvisionnement.id_appro)
        }
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    app.notify(result.message, "b");
                    $location.path(config.urlStockBa)
                }
            } else {
                app.notify("ok...", "b")
            }
        })
    };
    $scope.plafond = function(frns) {
        $scope.plafondFrns = frns.dette_frns
    };
    $scope.VerifPlafond = function() {
        if (parseInt($scope.approvisionnement.mnt_revient_appro) > $scope.plafondFrns) {
            $scope.approvisionnement.mnt_revient_appro = parseInt($scope.plafondFrns);
            app.notify("Attention Vous ne pouvez pas depasser le credit maximal du fournisseur indique ...", "m")
        }
    };
    $scope.save = function(approvisionnement) {
        var task;
        if (!prmutils.isDate(approvisionnement.date_appro)) {
            app.notify("Le format de la date est incorrect", "m");
            return false
        }
        approvisionnement.frns_appro = parseInt(approvisionnement.frns_appro);
        if (approvisionnement.bl_bon_dette === true && (!approvisionnement.dette_appro || approvisionnement.dette_appro === null)) {
            approvisionnement.dette_appro = 0
        }
        if ((parseInt(approvisionnement.mnt_revient_appro) <= 0) || (!approvisionnement.mnt_revient_appro) || (approvisionnement.mnt_revient_appro === null)) {
            approvisionnement.mnt_revient_appro = 0
        }
        if (objectID <= 0) {
            task = prmutils.insertApprovisionnement(approvisionnement);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    if (result.data === "-1") {
                        app.notify(result.message, "m")
                    } else {
                        app.notify(result.message, "b");
                        window.history.go(-1)
                    }
                } else {
                    app.notify("ok ...", "b")
                }
            })
        } else {
            if (!prmutils.isDate(approvisionnement.date_appro)) {
                app.notify("Le format de la date est incorrect", "m");
                return false
            }
            task = prmutils.updateApprovisionnement(objectID, approvisionnement);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    if (result.data === "-1") {
                        app.notify(result.message, "m")
                    } else {
                        app.notify(result.message, "b");
                        window.history.go(-1)
                    }
                } else {
                    app.notify("ok ...", "b")
                }
            })
        }
    };
    $scope.getdateDs()
}]);
sngs.controller("stockEditBcmdCtrl", ["$scope", "$rootScope", "config", "$location", "$routeParams", "prmutils", "$filter", "object", function($scope, $rootScope, config, $location, $routeParams, prmutils, $filter, object) {
    var stockeditba = $scope.stockeditba;
    var app = $scope.app;
    app.view = {
        url: config.urlStockEditBa,
        model: stockeditba,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Stock",
        subtitle: "Bon de commande",
        show: true,
        model: {}
    };
    $rootScope.pageTitle = "Bon de commande";
    prmutils.getFournisseurs().promise.then(function(result) {
        $scope.fournisseurs = result.data
    });
    var objectID = ($routeParams.objectID) ? parseInt($routeParams.objectID) : 0;
    $rootScope.title = (objectID > 0) ? "Modification Bon" : "Nouveau Bon de commande";
    $scope.buttonText = (objectID > 0) ? "Modifier" : "Ajouter";
    $scope.isDisabled = (objectID > 0) ? false : false;
    var original = object.data;
    original.id_cmd = objectID;
    $scope.approvisionnement = angular.copy(original);
    $scope.approvisionnement.id_cmd = objectID;
    if (objectID < 1) {
        $scope.approvisionnement = {}
    }
    $scope.getdateDs = function() {
        var task = prmutils.getDs();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.approvisionnement.date_cmd = result.data.datej;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.isClean = function() {
        return angular.equals(original, $scope.approvisionnement)
    };
    $scope.deleted = function(approvisionnement) {
        var task;
        if (confirm("Confirmer vous la suppression du bon de commande No : " + $scope.approvisionnement.bon_cmd) === true) {
            task = prmutils.deleteCommande(approvisionnement.id_cmd)
        }
        task.promise.then(function(result) {
            if (result.err === 0) {
                if (result.data === "-1") {
                    app.notify(result.message, "m")
                } else {
                    app.notify(result.message, "b");
                    $location.path(config.urlStockBcmd)
                }
            } else {
                app.notify("ok...", "b")
            }
        })
    };
    $scope.save = function(approvisionnement) {
        var task;
        if (!prmutils.isDate(approvisionnement.date_cmd)) {
            app.notify("Le format de la date est incorrect", "m");
            return false
        }
        approvisionnement.frns_cmd = parseInt(approvisionnement.frns_cmd);
        if (objectID <= 0) {
            task = prmutils.insertCommande(approvisionnement);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    if (result.data === "-1") {
                        app.notify(result.message, "m")
                    } else {
                        app.notify(result.message, "b");
                        window.history.go(-1)
                    }
                } else {
                    app.notify("ok ...", "b")
                }
            })
        } else {
            if (!prmutils.isDate(approvisionnement.date_cmd)) {
                app.notify("Le format de la date est incorrect", "m");
                return false
            }
            task = prmutils.updateCommande(objectID, approvisionnement);
            task.promise.then(function(result) {
                if (result.err === 0) {
                    if (result.data === "-1") {
                        app.notify(result.message, "m")
                    } else {
                        app.notify(result.message, "b");
                        window.history.go(-1)
                    }
                } else {
                    app.notify("ok ...", "b")
                }
            })
        }
    };
    $scope.getdateDs()
}]);
sngs.controller("stockBaAeCtrl", ["$scope", "$rootScope", "config", "prmutils", function($scope, $rootScope, config, prmutils) {
    var stockba = $scope.stockba;
    var app = $scope.app;
    app.view = {
        url: config.urlStockBs,
        model: stockba,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = true;
    app.title = {
        text: "Stock",
        subtitle: "Entrees > Bons En Attentes",
        show: true,
        model: {}
    };
    $rootScope.title = "Liste des bons en Attentes";
    $rootScope.pageTitle = "Bons En Attentes d'entrees";
    $scope.getSortiesAttentes = function() {
        var task = prmutils.getSortiesAttentes();
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
    $scope.getSortiesAttentes();
    $scope.rejetersrt = function(fac) {
        var task = prmutils.rejetersrt(fac);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                fac.bon_vu = 1;
                app.notify(result.message, 'b')
                $scope.getSortiesAttentes();
                app.waiting.show = false
                console.log(result)
                $("#detailsPannelRj").css("top", "100%")
            } else {
                app.waiting.show = false;
                console.log(result)
                app.notify(result.message, 'm')
            }
        })
    };
    $scope.bonvusrt = function(fac) {
        var task = prmutils.bonvusrt(fac);
        task.promise.then(function(result) {
            app.waiting.show = true;
            app.notify(result.message)
            if (result.err === 0) {
                fac.bon_vu = 1;
                $scope.getSortiesAttentes();
                app.waiting.show = false
            } else {
                app.waiting.show = false;
                app.notify(result.message)
            }
        })
    };
    $scope.tbonvusrt = function() {
        var task = prmutils.tbonvusrt();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                for (var i = 0; i < $scope.sorties.length; i++) {
                    $scope.sorties[i].bon_vu = 1
                }
                $rootScope.srtnba = 0;
                $scope.getSortiesAttentes();
                app.waiting.show = false
            } else {
                app.waiting.show = false;
                app.notify("Ok...", "b")
            }
        })
    };
    $scope.apprvae = function(fac) {
        var task = prmutils.apprvae(fac);
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $scope.getSortiesAttentes();
                app.waiting.show = false
                app.notify(result.message)
            } else {
                app.waiting.show = false;
                app.notify(result.message)
            }
        })
    };
    $scope.showDetails = function(fac) {
        $scope.num_fact = fac.bon_sort;
        var task = prmutils.showSortDetails(fac);
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

    $scope.showDetailsRj = function(bonEntree) {
        app.waiting.show = true;
        console.log(bonEntree)
        $scope.num_BonEntree = bonEntree.bon_sort;
        $scope.bonEntree = bonEntree;
        console.log(bonEntree)
        $("#detailsPannelRj").css("top", "10%")
        app.waiting.show = false
    };
}]);