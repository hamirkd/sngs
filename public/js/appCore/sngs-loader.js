var depSngs = ['bower_components/bootstrap/dist/js/bootstrap.min.js',
    'bower_components/bootstrap-select/bootstrap-select.min.js',
    'bower_components/angular/angular.min.js',
    'bower_components/angular-ui-bootstrap-bower/ui-bootstrap-tpls.min.js',
    'bower_components/angular-route/angular-route.min.js',
    'bower_components/angular-translate/angular-translate.min.js',
    'bower_components/angular-base64/angular-base64.min.js',
    'bower_components/angular-animate/angular-animate.js',
    'app/mdls/main.js',
    'app/srvs/config.js',
    'app/srvs/dao.js',
    'app/srvs/utils.js',
    'app/srvs/prmutils.js',
    'app/drctvs/errors.js',
    'app/drctvs/list.js',
    'app/drctvs/anView.js',
    'app/drctvs/conUser.js',
    'app/drctvs/waiting.js',
    'app/ctrls/appController.js',
    'app/ctrls/loginController.js',
    'app/ctrls/homeController.js',
    'app/ctrls/paramController.js',
    'app/ctrls/stockController.js',
    'public/js/app.js'
];

$.each(depSngs, function(i, depSng) {
    $("<script/>").attr('src', '' + depSng + '').appendTo($('body'));
});
