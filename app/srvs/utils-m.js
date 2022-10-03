angular.module("sngs").factory("utils",["$timeout","$q",function($timeout,$q){var waitForSomeTime=function(milliseconds){var task=$q.defer();$timeout(function(){task.resolve(true)},milliseconds);return task};function getErrors(data){var errors=[];var err=data.err;switch(err){case 2:errors.push("not_authorized");break;case 3:errors.push("forbidden");break;case 4:errors.push("not_http_error");break;case 6:errors.push("not_found");break;default:errors=data.messages;break}if(!errors||errors.length==0){errors=["error_unknown"]}return errors}return{waitForSomeTime:waitForSomeTime,getErrors:getErrors}}]);