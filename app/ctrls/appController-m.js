angular.module("sngs").controller("appCtrl", ["$rootScope", "localStorageService", "$scope", "config", "dao", "$location", "prmutils", "$interval", "$cookieStore", function($rootScope, localStorageService, $scope, config, dao, $location, prmutils, $interval, $cookieStore) {
    $scope.app = {
        waitingTimeBeforeTask: config.waitingTimeBeforeTask
    };
    $scope.login = {};
    $scope.parameditcat = {};
    $scope.parameditunit = {};
    $scope.parameditbank = {};
    $scope.parameditart = {};
    $scope.parameditmag = {};
    $scope.parameditcusr = {};
    $scope.paramedittd = {};
    $scope.parameditclt = {};
    $scope.parameditfrns = {};
    $scope.parameditba = {};
    $scope.home = {};
    $scope.param = {};
    var app = $scope.app;
    app.title = {
        show: false
    };
    app.waiting = {
        show: false
    };
    app.navbar = {
        show: false
    };
    app.view = {
        url: undefined,
        model: {},
        done: false
    };
    app.task = {};
    app.txtprint = " Imprimer ";
    app.userPfl = $cookieStore.get("userPfl");
    app.PRMS = $cookieStore.get("options");
    app.userSession = {};
    app.droits = [];
    app.options = {};
    app.appnf = config.appNameFull;
    app.appslog = config.appSlogan;
    app.appns = config.appNameShort;
    app.appv = config.appVersion;
    app.benef = config.structBenef;
    app.cont = config.contact;
    app.mail = config.email;
    app.appfld = config.appUrl;
    app.logOut = function() {
        app.userSession = {};
        app.login = {};
        var method = config.mdlFrontContrl + "logout";
        var task = {
            action: dao.getData(method),
            isFinished: false
        };
        task.action.promise.then(function(result) {
            if (result.err === 0) {
                app.waiting.show = false;
                app.userSession = {};
                app.options = {};
                $cookieStore.put("userPfl", "");
                $cookieStore.put("options", "");
                $rootScope.loginUser = null;
                app.userPfl = null;
                $rootScope.loginUser = null;
                $location.path(config.urlLogin)
            } else {
                app.notify(result.message, "m")
            }
        })
    };
    $scope.getcrnv = function() {
        var task = prmutils.getcrnv();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $rootScope.crnv = result.data.crnv;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getdefnv = function() {
        var task = prmutils.getdefnv();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $rootScope.defnv = result.data.defnv;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getdepnv = function() {
        var task = prmutils.getdepnv();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $rootScope.depnv = result.data.depnv;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getsrtnv = function() {
        var task = prmutils.getsrtnv();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $rootScope.srtnv = result.data.srtnv;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.changerMagasin = function(id_mag, nom_mag) {
        if (id_mag == -1) {
            location.reload();
            return;
        }
        app.waiting.show = true;
        app.userPfl.mag = nom_mag;
        app.userPfl.mg = id_mag;

        $cookieStore.put("userPfl", app.userPfl);
        task = prmutils.changerMagasin(id_mag, nom_mag);
        task.promise.then(function(result) {
            if (result.err === 0) {
                app.notify(result.message, "b");
                app.waiting.show = false;
                app.userPfl.mg = result.data.userMag;
                location.reload();
            } else {
                app.userPfl.mg = 0;
                app.notify(result.message, "m")
                app.waiting.show = false
                location.reload();
            }
        });
    };

    $scope.getMyMagasinsAcces = function() {
        task = prmutils.getMyMagasinsAcces();
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

    $scope.getAllMagasins = function() {
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
    }
    $scope.getAllMagasins();

    $scope.getsrtnba = function() {
        var task = prmutils.getsrtnba();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $rootScope.srtnba = result.data.srtnba;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getsrtnbarj = function() {
        var task = prmutils.getsrtnbarj();
        task.promise.then(function(result) {
            app.waiting.show = true;
            if (result.err === 0) {
                $rootScope.srtnbarj = result.data.srtnbarj;
                app.waiting.show = false
            } else {
                app.waiting.show = false
            }
        })
    };
    $scope.getcrnv();
    $scope.getdefnv();
    $scope.getdepnv();
    $scope.getsrtnv();
    $scope.getsrtnba();
    $scope.getsrtnbarj();
    var ivcr = $interval(function() {
        if (app.userPfl.length !== 0 && app.userPfl.mg === "0") {
            $scope.getcrnv()
        }
    }, config.INTERVAL_ALERTES, false);
    $scope.$on("$destroy", function() {
        if (app.userPfl.length !== 0) {
            $interval.cancel(ivcr)
        }
    });
    var ivdef = $interval(function() {
        if (app.userPfl.length !== 0 && app.userPfl.mg === "0") {
            $scope.getdefnv()
        }
    }, config.INTERVAL_ALERTES, false);
    var ivdep = $interval(function() {
        if (app.userPfl.length !== 0 && app.userPfl.mg === "0") {
            $scope.getdepnv()
        }
    }, config.INTERVAL_ALERTES, false);
    var ivsrt = $interval(function() {
        if (app.userPfl.length !== 0 && app.userPfl.mg === "0") {
            $scope.getsrtnv()
        }
    }, config.INTERVAL_ALERTES, false);
    var ivsrtae = $interval(function() {
        if (app.userPfl.length !== 0) {
            $scope.getsrtnba()
        }
    }, config.INTERVAL_ALERTES, false);
    $scope.$on("$destroy", function() {
        $interval.cancel(ivcr);
        $interval.cancel(ivdef);
        $interval.cancel(ivdep);
        $interval.cancel(ivsrt);
        $interval.cancel(ivsrtae)
    });
    app.refreshcatCache = function() {
        localStorageService.remove("categoriesArticlesCache")
    };
    app.refreshextcatmagCache = function() {
        localStorageService.remove("extendCategorieOfMag")
    };
    app.refreshartCache = function() {
        localStorageService.remove("ArticlesCache")
    };
    app.refrestdCache = function() {
        localStorageService.remove("typeDepensesCache")
    };
    app.refresbnkCache = function() {
        localStorageService.remove("banquesCache")
    };
    app.refresusrCache = function() {
        localStorageService.remove("usersCache")
    };
    app.refreshcltCache = function() {
        localStorageService.remove("ClientsCache");
        localStorageService.remove("ClientsOrCache")
    };
    app.refreshfrnsCache = function() {
        localStorageService.remove("FournisseursCache");
        localStorageService.remove("Fournisseurs2Cache")
    };
    app.refreshuntCache = function() {
        localStorageService.remove("UniteCache")
    };
    app.refreshmagCache = function() {
        localStorageService.remove("magasinsCache")
    };
    app.notify = function(msg, c, duree = 2000) {
        var cl = "#ab0909";
        if (c === "b") {
            cl = "#09ab09"
        }
        var elem = $("<div>", {
            id: "NoifyMessage",
            style: "background-color:" + cl + ";",
            html: msg
        });
        elem.click(function() {
            $(this).fadeOut(function() {
                $(this).remove()
            })
        });
        setTimeout(function() {
            elem.click()
        }, duree);
        elem.hide().appendTo("body").slideDown()
    }

}]);