angular.module("sngs").controller("homeCtrl", ["$scope", "config", "$filter", "$translate", "$locale", "$location", "utils", "dao", function($scope, config, $filter, $translate, $locale, $location, utils, dao) {
    var home = $scope.home;
    var app = $scope.app;
    app.view = {
        url: config.urlHome,
        model: home,
        done: false
    };
    app.navbar.show = true;
    app.title = {
        text: config.home,
        subtitle: "Ventes du jour",
        show: true,
        model: {}
    };
    $rootScope.pageTitle = "Ventes du jour"
}]);