sngs.directive("animatedView", ["$route", "$anchorScroll", "$compile", "$controller", function($route, $anchorScroll, $compile, $controller) {
    return {
        restrict: "ECA",
        terminal: true,
        link: function(scope, element, attr) {
            var lastScope, onloadExp = attr.onload || "",
                defaults = {
                    duration: 500,
                    viewEnterAnimation: "slideBottom",
                    viewExitAnimation: "fadeOut",
                    slideAmount: 50,
                    disabled: false
                },
                locals, template, options = scope.$eval(attr.animations);
            angular.extend(defaults, options);
            scope.$on("$routeChangeSuccess", update);
            update();

            function destroyLastScope() {
                if (lastScope) {
                    lastScope.$destroy();
                    lastScope = null
                }
            }

            function clearContent() {
                element.html("");
                destroyLastScope()
            }

            function update() {
                locals = $route.current && $route.current.locals;
                template = locals && locals.$template;
                if (template) {
                    if (!defaults.disabled) {
                        if (element.children().length > 0) {
                            animate(defaults.viewExitAnimation)
                        } else {
                            animateEnterView(defaults.viewEnterAnimation)
                        }
                    } else {
                        bindElement()
                    }
                } else {
                    clearContent()
                }
            }

            function animateEnterView(animation) {
                $(element).css("display", "block");
                bindElement();
                animate(animation)
            }

            function animate(animationType) {
                switch (animationType) {
                    case "fadeOut":
                        $(element.children()).animate({}, defaults.duration, function() {
                            animateEnterView("slideLeft")
                        });
                        break;
                    case "slideLeft":
                        $(element.children()).animate({
                            left: "-=" + defaults.slideAmount,
                            opacity: 1
                        }, defaults.duration);
                        break;
                    case "slideRight":
                        $(element.children()).animate({
                            left: "+=" + defaults.slideAmount,
                            opacity: 1
                        }, defaults.duration);
                        break;
                    case "slideBottom":
                        $(element.children()).animate({
                            top: "+=" + defaults.slideAmount,
                            opacity: 1
                        }, defaults.duration);
                        break
                }
            }

            function bindElement() {
                element.html(template);
                destroyLastScope();
                var link = $compile(element.contents()),
                    current = $route.current,
                    controller;
                lastScope = current.scope = scope.$new();
                if (current.controller) {
                    locals.$scope = lastScope;
                    controller = $controller(current.controller, locals);
                    element.children().data("$ngControllerController", controller)
                }
                link(lastScope);
                lastScope.$emit("$viewContentLoaded");
                lastScope.$eval(onloadExp);
                $anchorScroll()
            }
        }
    }
}]);
sngs.directive("whenScrolled", function() {
    return function(scope, elm, attr) {
        var raw = elm[0];
        elm.bind("scroll", function() {
            if (raw.scrollTop + raw.offsetHeight >= raw.scrollHeight) {
                scope.$apply(attr.whenScrolled)
            }
        })
    }
});
sngs.directive("contenteditable", ["prmutils", function(prmutils) {
    return {
        require: "ngModel",
        scope: {
            dt: "=",
            fd: "@",
            typeo: "@"
        },
        link: function(scope, elm, attrs, ctrl) {
            elm.bind("blur", function() {
                scope.$apply(function() {
                    ctrl.$setViewValue(elm.html())
                });
                var p = elm.html();
                p = p.trim();
                if (p === "<br>") {
                    p = "-"
                }
                scope.dt[scope.fd] = p;
                if (scope.typeo === "art") {
                    task = prmutils.updateArticle(scope.dt.id_art, scope.dt)
                }
                if (scope.typeo === "stock") {
                    task = prmutils.setAdresseSotck(scope.dt.id_stk, scope.dt)
                }
                task.promise.then(function(result) {
                    if (result.err === 0) {
                        if (result.data === "-1") {
                            return false
                        } else {
                            return false
                        }
                    } else {
                        alert("Erreur : Veuillez contacter l Administrateur !")
                    }
                })
            });
            ctrl.render = function(value) {
                elm.html(value)
            };
            ctrl.$setViewValue(elm.html());
            elm.bind("keydown", function(event) {
                var esc = event.which === 27,
                    el = event.target;
                if (esc) {
                    ctrl.$setViewValue(elm.html());
                    event.preventDefault()
                }
            })
        }
    }
}]);