angular.module("sngs").directive("waiting", [function() {
    return {
        restrict: "E",
        templateUrl: "app/vws/waiting-m.html",
        scope: true,
        link: function(scope, element, attrs) {
            scope.$watch(attrs.model, function(newValue) {
                scope.model = newValue
            })
        }
    }
}]);