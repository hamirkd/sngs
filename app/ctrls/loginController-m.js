angular.module("sngs").controller("loginCtrl", ["$rootScope", "$scope", "localStorageService", "config", "prmutils", "dao", "$location", "utils", "$cookieStore", function($rootScope, $scope, localStorageService, config, prmutils, dao, $location, utils, $cookieStore) {
    var login = $scope.login;
    var app = $scope.app;
    app.view = {
        url: config.urlLogin,
        model: login,
        done: false
    };
    app.waiting.show = false;
    app.navbar.show = false;
    app.title = {
        text: config.identification,
        show: false,
        model: {}
    };

    login.authenticate = function() {
        app.waiting.show = true;
        var task = {
            action: utils.waitForSomeTime(app.waitingTimeBeforeTask),
            isFinished: false
        };
        var promise = task.action.promise.then(function() {
            if (task.isFinished) {
                app.waiting.show = false;
                return true
            }
            var method = config.mdlFrontContrl + "login";

            var postData = {
                login: login.authLogin,
                password: login.authPassword
            };
            task = {
                action: dao.getData(method, postData),
                isFinished: false
            };
            return task.action.promise
        });
        promise.then(function(result) {

            console.log(result);
            if (result.err === 0) {
                app.waiting.show = false;
                app.userSession = {
                    id: result.data.id_user,
                    login: result.data.login_user,
                    name: result.data.nom_user,
                    lastName: result.data.prenom_user,
                    sex: result.data.sexe_user,
                    pfl: result.data.profil_user,
                    mg: result.data.mag_user,
                    mag: result.data.nom_mag,
                    reglCredit: result.data.regl_credit,
                    venteCredit: result.data.vente_credit,
                    factureVenteAnnulee: Number(result.data.facture_vente_annulee),
                    droitFactureVenteAnnuleeToday: Number(result.data.droit_facture_vente_annulee_today),
                    droitControlePrixVente: Number(result.data.droit_controle_prix_vente),
                    droitReglementFactureCredit: Number(result.data.droit_reglement_facture_credit),
                    magasins: result.data.magasins
                };
                app.options = {
                    tf: result.data.tva_fact,
                    bf: result.data.bic_fact,
                    pv: result.data.prix_vari,
                    pg: result.data.prix_gros,
                    cat: result.data.categorie_art,
                    grt: result.data.grt,
                    apu: result.data.aff_pu,
                    resa: result.data.restrict_annul,
                    tva: result.data.val_tva,
                    bic: result.data.val_bic,
                    dynl: result.data.dyn_load,
                    liopl: result.data.load_ipagld,
                    pfls: result.data.pfl_strict,
                    trsf: result.data.mdltrsf,
                    defdat: result.data.def_date,
                    prxcht: result.data.prix_achat,
                    cmd: result.data.mdl_cmd,
                    bonatt: result.data.mdl_bon_att
                };
                $cookieStore.put("userPfl", app.userSession);
                app.userPfl = $cookieStore.get("userPfl");
                $cookieStore.put("options", app.options);
                app.PRMS = $cookieStore.get("options");
                localStorageService.clearAll();
                login.authLogin = "";
                login.authPassword = "";
                app.view.done = true;
                $rootScope.loginUser = 1;
                login.getDroit();
                $location.path(config.urlHome);
            } else {
                app.waiting.show = false;
                app.notify(result.message, "m")
            }
        })
    }

    login.getDroit = function() {
        console.log("Exce")
        var task;
        var droit = {
            profil_id: app.userSession.pfl,
            mag_id: app.userSession.mg
        }

        task = prmutils.getDroit(droit);
        task.promise.then(function(result) {
            console.log(result)
            for (const iterator of result.data) {
                if (iterator.etat == '0') continue;
                app.droits.push(iterator.code_droit);
            }
            console.log(app.droits)
        })
    };
}]);