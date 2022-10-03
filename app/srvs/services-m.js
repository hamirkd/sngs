var url = window.location;
// url = url.host + url.pathname + url.hash;
var serviceBaseUrl = url.host + url.pathname + "api";
angular
    .module("sngs.services", [])
    .factory("Vente", function($resource) {
        return $resource(
            serviceBaseUrl + "/facture/:id", { id: "@id_fact" }, {
                update: { method: "PUT" },
                getFacturesCredit: {
                    url: serviceBaseUrl + "/factures/credit",
                    method: "GET",
                },
            }
        );
    })
    .service("popupService", function($window) {
        this.showPopup = function(message) {
            return $window.confirm(message);
        };
    });