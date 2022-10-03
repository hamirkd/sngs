angular.module("sngs").factory("dao", ["$http", "$q", "config", "$base64", "localStorageService", function($http, $q, config, $base64, localStorageService) {
    function getData(urlAction, info, cacheId) {
        if (typeof(cacheId) === "undefined") {
            cacheId = null
        }
        var task = $q.defer();
        var url = config.serverUrl + urlAction;
        // console.log(url, info)
        var basic = "Basic " + $base64.encode("sngs:stock");
        var reponse;
        var headers = $http.defaults.headers.common;
        headers.Authorization = basic;
        var promise;
        if (info) {
            promise = $http.post(url, info, {
                timeout: config.timeout
            })
        } else {
            promise = $http.get(url, {
                timeout: config.timeout
            })
        }
        promise.then(success, failure);
        return task;

        function success(response) {
            var retrieve = response.data;
            reponse = retrieve.status == 0 ? {
                err: 0,
                data: retrieve.datas,
                message: retrieve.msg
            } : {
                err: 1,
                data: "",
                message: retrieve.msg
            };
            task.resolve(reponse)
        }

        function failure(response) {
            var status = response.status;
            var error;
            switch (status) {
                case 401:
                    error = 2;
                    break;
                case 403:
                    error = 3;
                    break;
                case 404:
                    error = 6;
                    break;
                case 0:
                    error = 4;
                    break;
                default:
                    error = 5
            }
            task.resolve({
                err: error,
                messages: [response.statusText]
            })
        }
    }

    function getDataGet(urlAction, cacheId) {
        var task = $q.defer();
        var url = config.serverUrl + urlAction;
        var basic = "Basic " + $base64.encode("sngs:stock");
        var reponse;
        var headers = $http.defaults.headers.common;
        headers.Authorization = basic;
        var promise;
        promise = $http.get(url, {
            timeout: config.timeout
        });
        promise.then(success, failure);
        return task;

        function success(response) {
            var retrieve = response.data;
            reponse = retrieve.status == 0 ? {
                err: 0,
                data: retrieve.datas,
                message: retrieve.msg
            } : {
                err: 1,
                data: "",
                message: retrieve.msg
            };
            localStorageService.set(cacheId, JSON.stringify(retrieve.datas));
            task.resolve(reponse)
        }

        function failure(response) {
            var status = response.status;
            var error;
            switch (status) {
                case 401:
                    error = 2;
                    break;
                case 403:
                    error = 3;
                    break;
                case 404:
                    error = 6;
                    break;
                case 0:
                    error = 4;
                    break;
                default:
                    error = 5
            }
            task.resolve({
                err: error,
                messages: [response.statusText]
            })
        }
    }
    return {
        getData: getData,
        getDataGet: getDataGet
    }
}]);