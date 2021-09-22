// modules are defined as an array
// [ module function, map of requires ]
//
// map of requires is short require name -> numeric require
//
// anything defined in a previous bundle is accessed via the
// orig method which is the require for previous bundles

(function(modules, entry, mainEntry, parcelRequireName, globalName) {
  /* eslint-disable no-undef */
  var globalObject =
    typeof globalThis !== 'undefined'
      ? globalThis
      : typeof self !== 'undefined'
      ? self
      : typeof window !== 'undefined'
      ? window
      : typeof global !== 'undefined'
      ? global
      : {};
  /* eslint-enable no-undef */

  // Save the require from previous bundle to this closure if any
  var previousRequire =
    typeof globalObject[parcelRequireName] === 'function' &&
    globalObject[parcelRequireName];

  var cache = previousRequire.cache || {};
  // Do not use `require` to prevent Webpack from trying to bundle this call
  var nodeRequire =
    typeof module !== 'undefined' &&
    typeof module.require === 'function' &&
    module.require.bind(module);

  function newRequire(name, jumped) {
    if (!cache[name]) {
      if (!modules[name]) {
        // if we cannot find the module within our internal map or
        // cache jump to the current global require ie. the last bundle
        // that was added to the page.
        var currentRequire =
          typeof globalObject[parcelRequireName] === 'function' &&
          globalObject[parcelRequireName];
        if (!jumped && currentRequire) {
          return currentRequire(name, true);
        }

        // If there are other bundles on this page the require from the
        // previous one is saved to 'previousRequire'. Repeat this as
        // many times as there are bundles until the module is found or
        // we exhaust the require chain.
        if (previousRequire) {
          return previousRequire(name, true);
        }

        // Try the node require function if it exists.
        if (nodeRequire && typeof name === 'string') {
          return nodeRequire(name);
        }

        var err = new Error("Cannot find module '" + name + "'");
        err.code = 'MODULE_NOT_FOUND';
        throw err;
      }

      localRequire.resolve = resolve;
      localRequire.cache = {};

      var module = (cache[name] = new newRequire.Module(name));

      modules[name][0].call(
        module.exports,
        localRequire,
        module,
        module.exports,
        this
      );
    }

    return cache[name].exports;

    function localRequire(x) {
      return newRequire(localRequire.resolve(x));
    }

    function resolve(x) {
      return modules[name][1][x] || x;
    }
  }

  function Module(moduleName) {
    this.id = moduleName;
    this.bundle = newRequire;
    this.exports = {};
  }

  newRequire.isParcelRequire = true;
  newRequire.Module = Module;
  newRequire.modules = modules;
  newRequire.cache = cache;
  newRequire.parent = previousRequire;
  newRequire.register = function(id, exports) {
    modules[id] = [
      function(require, module) {
        module.exports = exports;
      },
      {},
    ];
  };

  Object.defineProperty(newRequire, 'root', {
    get: function() {
      return globalObject[parcelRequireName];
    },
  });

  globalObject[parcelRequireName] = newRequire;

  for (var i = 0; i < entry.length; i++) {
    newRequire(entry[i]);
  }

  if (mainEntry) {
    // Expose entry point to Node, AMD or browser globals
    // Based on https://github.com/ForbesLindesay/umd/blob/master/template.js
    var mainExports = newRequire(mainEntry);

    // CommonJS
    if (typeof exports === 'object' && typeof module !== 'undefined') {
      module.exports = mainExports;

      // RequireJS
    } else if (typeof define === 'function' && define.amd) {
      define(function() {
        return mainExports;
      });

      // <script>
    } else if (globalName) {
      this[globalName] = mainExports;
    }
  }
})({"sMOXz":[function(require,module,exports) {
var HMR_HOST = null;
var HMR_PORT = null;
var HMR_SECURE = false;
var HMR_ENV_HASH = "69f74e7f31319ffd";
module.bundle.HMR_BUNDLE_ID = "8abec209e802d94a";
"use strict";
function _createForOfIteratorHelper(o, allowArrayLike) {
    var it;
    if (typeof Symbol === "undefined" || o[Symbol.iterator] == null) {
        if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") {
            if (it) o = it;
            var i = 0;
            var F = function F1() {
            };
            return {
                s: F,
                n: function n() {
                    if (i >= o.length) return {
                        done: true
                    };
                    return {
                        done: false,
                        value: o[i++]
                    };
                },
                e: function e(_e) {
                    throw _e;
                },
                f: F
            };
        }
        throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
    }
    var normalCompletion = true, didErr = false, err;
    return {
        s: function s() {
            it = o[Symbol.iterator]();
        },
        n: function n() {
            var step = it.next();
            normalCompletion = step.done;
            return step;
        },
        e: function e(_e2) {
            didErr = true;
            err = _e2;
        },
        f: function f() {
            try {
                if (!normalCompletion && it.return != null) it.return();
            } finally{
                if (didErr) throw err;
            }
        }
    };
}
function _unsupportedIterableToArray(o, minLen) {
    if (!o) return;
    if (typeof o === "string") return _arrayLikeToArray(o, minLen);
    var n = Object.prototype.toString.call(o).slice(8, -1);
    if (n === "Object" && o.constructor) n = o.constructor.name;
    if (n === "Map" || n === "Set") return Array.from(o);
    if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen);
}
function _arrayLikeToArray(arr, len) {
    if (len == null || len > arr.length) len = arr.length;
    for(var i = 0, arr2 = new Array(len); i < len; i++)arr2[i] = arr[i];
    return arr2;
}
/* global HMR_HOST, HMR_PORT, HMR_ENV_HASH, HMR_SECURE */ /*::
import type {
  HMRAsset,
  HMRMessage,
} from '@parcel/reporter-dev-server/src/HMRServer.js';
interface ParcelRequire {
  (string): mixed;
  cache: {|[string]: ParcelModule|};
  hotData: mixed;
  Module: any;
  parent: ?ParcelRequire;
  isParcelRequire: true;
  modules: {|[string]: [Function, {|[string]: string|}]|};
  HMR_BUNDLE_ID: string;
  root: ParcelRequire;
}
interface ParcelModule {
  hot: {|
    data: mixed,
    accept(cb: (Function) => void): void,
    dispose(cb: (mixed) => void): void,
    // accept(deps: Array<string> | string, cb: (Function) => void): void,
    // decline(): void,
    _acceptCallbacks: Array<(Function) => void>,
    _disposeCallbacks: Array<(mixed) => void>,
  |};
}
declare var module: {bundle: ParcelRequire, ...};
declare var HMR_HOST: string;
declare var HMR_PORT: string;
declare var HMR_ENV_HASH: string;
declare var HMR_SECURE: boolean;
*/ var OVERLAY_ID = '__parcel__error__overlay__';
var OldModule = module.bundle.Module;
function Module(moduleName) {
    OldModule.call(this, moduleName);
    this.hot = {
        data: module.bundle.hotData,
        _acceptCallbacks: [],
        _disposeCallbacks: [],
        accept: function accept(fn) {
            this._acceptCallbacks.push(fn || function() {
            });
        },
        dispose: function dispose(fn) {
            this._disposeCallbacks.push(fn);
        }
    };
    module.bundle.hotData = undefined;
}
module.bundle.Module = Module;
var checkedAssets, acceptedAssets, assetsToAccept;
function getHostname() {
    return HMR_HOST || (location.protocol.indexOf('http') === 0 ? location.hostname : 'localhost');
}
function getPort() {
    return HMR_PORT || location.port;
} // eslint-disable-next-line no-redeclare
var parent = module.bundle.parent;
if ((!parent || !parent.isParcelRequire) && typeof WebSocket !== 'undefined') {
    var hostname = getHostname();
    var port = getPort();
    var protocol = HMR_SECURE || location.protocol == 'https:' && !/localhost|127.0.0.1|0.0.0.0/.test(hostname) ? 'wss' : 'ws';
    var ws = new WebSocket(protocol + '://' + hostname + (port ? ':' + port : '') + '/'); // $FlowFixMe
    ws.onmessage = function(event) {
        checkedAssets = {
        };
        acceptedAssets = {
        };
        assetsToAccept = [];
        var data = JSON.parse(event.data);
        if (data.type === 'update') {
            // Remove error overlay if there is one
            removeErrorOverlay();
            var assets = data.assets.filter(function(asset) {
                return asset.envHash === HMR_ENV_HASH;
            }); // Handle HMR Update
            var handled = assets.every(function(asset) {
                return asset.type === 'css' || asset.type === 'js' && hmrAcceptCheck(module.bundle.root, asset.id, asset.depsByBundle);
            });
            if (handled) {
                console.clear();
                assets.forEach(function(asset) {
                    hmrApply(module.bundle.root, asset);
                });
                for(var i = 0; i < assetsToAccept.length; i++){
                    var id = assetsToAccept[i][1];
                    if (!acceptedAssets[id]) hmrAcceptRun(assetsToAccept[i][0], id);
                }
            } else window.location.reload();
        }
        if (data.type === 'error') {
            // Log parcel errors to console
            var _iterator = _createForOfIteratorHelper(data.diagnostics.ansi), _step;
            try {
                for(_iterator.s(); !(_step = _iterator.n()).done;){
                    var ansiDiagnostic = _step.value;
                    var stack = ansiDiagnostic.codeframe ? ansiDiagnostic.codeframe : ansiDiagnostic.stack;
                    console.error('🚨 [parcel]: ' + ansiDiagnostic.message + '\n' + stack + '\n\n' + ansiDiagnostic.hints.join('\n'));
                } // Render the fancy html overlay
            } catch (err) {
                _iterator.e(err);
            } finally{
                _iterator.f();
            }
            removeErrorOverlay();
            var overlay = createErrorOverlay(data.diagnostics.html); // $FlowFixMe
            document.body.appendChild(overlay);
        }
    };
    ws.onerror = function(e) {
        console.error(e.message);
    };
    ws.onclose = function() {
        console.warn('[parcel] 🚨 Connection to the HMR server was lost');
    };
}
function removeErrorOverlay() {
    var overlay = document.getElementById(OVERLAY_ID);
    if (overlay) {
        overlay.remove();
        console.log('[parcel] ✨ Error resolved');
    }
}
function createErrorOverlay(diagnostics) {
    var overlay = document.createElement('div');
    overlay.id = OVERLAY_ID;
    var errorHTML = '<div style="background: black; opacity: 0.85; font-size: 16px; color: white; position: fixed; height: 100%; width: 100%; top: 0px; left: 0px; padding: 30px; font-family: Menlo, Consolas, monospace; z-index: 9999;">';
    var _iterator2 = _createForOfIteratorHelper(diagnostics), _step2;
    try {
        for(_iterator2.s(); !(_step2 = _iterator2.n()).done;){
            var diagnostic = _step2.value;
            var stack = diagnostic.codeframe ? diagnostic.codeframe : diagnostic.stack;
            errorHTML += "\n      <div>\n        <div style=\"font-size: 18px; font-weight: bold; margin-top: 20px;\">\n          \uD83D\uDEA8 ".concat(diagnostic.message, "\n        </div>\n        <pre>\n          ").concat(stack, "\n        </pre>\n        <div>\n          ").concat(diagnostic.hints.map(function(hint) {
                return '<div>' + hint + '</div>';
            }).join(''), "\n        </div>\n      </div>\n    ");
        }
    } catch (err) {
        _iterator2.e(err);
    } finally{
        _iterator2.f();
    }
    errorHTML += '</div>';
    overlay.innerHTML = errorHTML;
    return overlay;
}
function getParents(bundle, id) /*: Array<[ParcelRequire, string]> */ {
    var modules = bundle.modules;
    if (!modules) return [];
    var parents = [];
    var k, d, dep;
    for(k in modules)for(d in modules[k][1]){
        dep = modules[k][1][d];
        if (dep === id || Array.isArray(dep) && dep[dep.length - 1] === id) parents.push([
            bundle,
            k
        ]);
    }
    if (bundle.parent) parents = parents.concat(getParents(bundle.parent, id));
    return parents;
}
function updateLink(link) {
    var newLink = link.cloneNode();
    newLink.onload = function() {
        if (link.parentNode !== null) // $FlowFixMe
        link.parentNode.removeChild(link);
    };
    newLink.setAttribute('href', link.getAttribute('href').split('?')[0] + '?' + Date.now()); // $FlowFixMe
    link.parentNode.insertBefore(newLink, link.nextSibling);
}
var cssTimeout = null;
function reloadCSS() {
    if (cssTimeout) return;
    cssTimeout = setTimeout(function() {
        var links = document.querySelectorAll('link[rel="stylesheet"]');
        for(var i = 0; i < links.length; i++){
            // $FlowFixMe[incompatible-type]
            var href = links[i].getAttribute('href');
            var hostname = getHostname();
            var servedFromHMRServer = hostname === 'localhost' ? new RegExp('^(https?:\\/\\/(0.0.0.0|127.0.0.1)|localhost):' + getPort()).test(href) : href.indexOf(hostname + ':' + getPort());
            var absolute = /^https?:\/\//i.test(href) && href.indexOf(window.location.origin) !== 0 && !servedFromHMRServer;
            if (!absolute) updateLink(links[i]);
        }
        cssTimeout = null;
    }, 50);
}
function hmrApply(bundle, asset) {
    var modules = bundle.modules;
    if (!modules) return;
    if (asset.type === 'css') {
        reloadCSS();
        return;
    }
    var deps = asset.depsByBundle[bundle.HMR_BUNDLE_ID];
    if (deps) {
        var fn = new Function('require', 'module', 'exports', asset.output);
        modules[asset.id] = [
            fn,
            deps
        ];
    } else if (bundle.parent) hmrApply(bundle.parent, asset);
}
function hmrAcceptCheck(bundle, id, depsByBundle) {
    var modules = bundle.modules;
    if (!modules) return;
    if (depsByBundle && !depsByBundle[bundle.HMR_BUNDLE_ID]) {
        // If we reached the root bundle without finding where the asset should go,
        // there's nothing to do. Mark as "accepted" so we don't reload the page.
        if (!bundle.parent) return true;
        return hmrAcceptCheck(bundle.parent, id, depsByBundle);
    }
    if (checkedAssets[id]) return;
    checkedAssets[id] = true;
    var cached = bundle.cache[id];
    assetsToAccept.push([
        bundle,
        id
    ]);
    if (cached && cached.hot && cached.hot._acceptCallbacks.length) return true;
    return getParents(module.bundle.root, id).some(function(v) {
        return hmrAcceptCheck(v[0], v[1], null);
    });
}
function hmrAcceptRun(bundle, id) {
    var cached = bundle.cache[id];
    bundle.hotData = {
    };
    if (cached && cached.hot) cached.hot.data = bundle.hotData;
    if (cached && cached.hot && cached.hot._disposeCallbacks.length) cached.hot._disposeCallbacks.forEach(function(cb) {
        cb(bundle.hotData);
    });
    delete bundle.cache[id];
    bundle(id);
    cached = bundle.cache[id];
    if (cached && cached.hot && cached.hot._acceptCallbacks.length) cached.hot._acceptCallbacks.forEach(function(cb) {
        var assetsToAlsoAccept = cb(function() {
            return getParents(module.bundle.root, id);
        });
        if (assetsToAlsoAccept && assetsToAccept.length) // $FlowFixMe[method-unbinding]
        assetsToAccept.push.apply(assetsToAccept, assetsToAlsoAccept);
    });
    acceptedAssets[id] = true;
}

},{}],"2vQaq":[function(require,module,exports) {
"use strict";
var _get_info = require("./lookup/get_info");
var _override = require("./lookup/override");
var _main = require("./main");
// Evaluate shortcodes, body classes, etc.
_main.main(); // Extend window object 
window.geoip_detect.get_info = _get_info.get_info;
window.geoip_detect.set_override = _override.set_override;
window.geoip_detect.remove_override = _override.remove_override;

},{"./lookup/get_info":"eKpYj","./lookup/override":"lMA0q","./main":"gI94w"}],"eKpYj":[function(require,module,exports) {
"use strict";
var _interopRequireDefault = require("@babel/runtime/helpers/interopRequireDefault");
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.get_info = get_info;
exports.options = void 0;
var _regenerator = _interopRequireDefault(require("@babel/runtime/regenerator"));
var _typeof2 = _interopRequireDefault(require("@babel/runtime/helpers/typeof"));
var _asyncToGenerator2 = _interopRequireDefault(require("@babel/runtime/helpers/asyncToGenerator"));
var _record = _interopRequireDefault(require("../models/record"));
var _xhr = require("../lib/xhr");
var _override = require("./override");
var _window$geoip_detect;
var options = ((_window$geoip_detect = window.geoip_detect) === null || _window$geoip_detect === void 0 ? void 0 : _window$geoip_detect.options) || {
    ajaxurl: "/wp-admin/admin-ajax.php",
    default_locales: [
        'en'
    ],
    cookie_duration_in_days: 7,
    cookie_name: 'geoip-detect-result',
    do_body_classes: false
};
exports.options = options;
var ajaxPromise = null;
function get_info_raw() {
    if (!ajaxPromise) {
        // Do Ajax Request only once per page load
        var url = options.ajaxurl + '?action=geoip_detect2_get_info_from_current_ip';
        ajaxPromise = _xhr.makeJSONRequest(url);
        ajaxPromise.then(function(response) {
            var _response$extra;
            if (response !== null && response !== void 0 && (_response$extra = response.extra) !== null && _response$extra !== void 0 && _response$extra.error) console.error('Geolocation IP Detection Error: Server returned an error: ' + response.extra.error);
        });
    }
    return ajaxPromise;
}
function get_info_cached() {
    return _get_info_cached.apply(this, arguments);
}
/**
 * Load the data from the server
 * 
 * (It can also be loaded from the browser localstorage, if the record data is present there already.)
 * 
 * @api
 * @return Promise(Record)
 */ function _get_info_cached() {
    _get_info_cached = _asyncToGenerator2.default(/*#__PURE__*/ _regenerator.default.mark(function _callee() {
        var response, storedResponse, _storedResponse, _storedResponse$extra, _response, _response$extra2, cache_duration;
        return _regenerator.default.wrap(function _callee$(_context) {
            while(true)switch(_context.prev = _context.next){
                case 0:
                    response = false;
                    storedResponse = false; // 1) Load Info from localstorage cookie cache, if possible
                    if (!options.cookie_name) {
                        _context.next = 7;
                        break;
                    }
                    storedResponse = _override.getRecordDataFromLocalStorage();
                    if (!(storedResponse && storedResponse.extra)) {
                        _context.next = 7;
                        break;
                    }
                    if (storedResponse.extra.override === true) console.info('Geolocation IP Detection: Using cached response (override)');
                    else console.info('Geolocation IP Detection: Using cached response');
                    return _context.abrupt("return", storedResponse);
                case 7:
                    _context.prev = 7;
                    _context.next = 10;
                    return get_info_raw();
                case 10:
                    response = _context.sent;
                    _context.next = 17;
                    break;
                case 13:
                    _context.prev = 13;
                    _context.t0 = _context["catch"](7);
                    console.log('Uncaught ERROR ??');
                    response = _context.t0.responseJSON || _context.t0;
                case 17:
                    if (!options.cookie_name) {
                        _context.next = 25;
                        break;
                    }
                    // Check if Override has been set now
                    storedResponse = _override.getRecordDataFromLocalStorage();
                    if (!(((_storedResponse = storedResponse) === null || _storedResponse === void 0 ? void 0 : (_storedResponse$extra = _storedResponse.extra) === null || _storedResponse$extra === void 0 ? void 0 : _storedResponse$extra.override) === true)) {
                        _context.next = 22;
                        break;
                    }
                    console.info('Geolocation IP Detection: Using cached response (override)');
                    return _context.abrupt("return", storedResponse);
                case 22:
                    cache_duration = options.cookie_duration_in_days * 86400;
                    if ((_response = response) !== null && _response !== void 0 && (_response$extra2 = _response.extra) !== null && _response$extra2 !== void 0 && _response$extra2.error) cache_duration = 60; // Cache errors only for 1 minute, then try again
                    _override.setRecordDataToLocalStorage(response, cache_duration);
                case 25:
                    return _context.abrupt("return", response);
                case 26:
                case "end":
                    return _context.stop();
            }
        }, _callee, null, [
            [
                7,
                13
            ]
        ]);
    }));
    return _get_info_cached.apply(this, arguments);
}
function get_info() {
    return _get_info.apply(this, arguments);
}
function _get_info() {
    _get_info = _asyncToGenerator2.default(/*#__PURE__*/ _regenerator.default.mark(function _callee2() {
        var response, record;
        return _regenerator.default.wrap(function _callee2$(_context2) {
            while(true)switch(_context2.prev = _context2.next){
                case 0:
                    _context2.next = 2;
                    return get_info_cached();
                case 2:
                    response = _context2.sent;
                    if (_typeof2.default(response) !== 'object') {
                        console.error('Geolocation IP Detection Error: Record should be an object, not a ' + _typeof2.default(response), response);
                        response = {
                            'extra': {
                                'error': response || 'Network error, look at the original server response ...'
                            }
                        };
                    }
                    record = new _record.default(response, options.default_locales);
                    return _context2.abrupt("return", record);
                case 6:
                case "end":
                    return _context2.stop();
            }
        }, _callee2);
    }));
    return _get_info.apply(this, arguments);
}

},{"@babel/runtime/helpers/interopRequireDefault":"eigyQ","@babel/runtime/regenerator":"1L3WO","@babel/runtime/helpers/typeof":"1XGzZ","@babel/runtime/helpers/asyncToGenerator":"5j50L","../models/record":"hmNok","../lib/xhr":"fkAhj","./override":"lMA0q"}],"eigyQ":[function(require,module,exports) {
function _interopRequireDefault(obj) {
    return obj && obj.__esModule ? obj : {
        "default": obj
    };
}
module.exports = _interopRequireDefault;
module.exports["default"] = module.exports, module.exports.__esModule = true;

},{}],"1L3WO":[function(require,module,exports) {
module.exports = require("regenerator-runtime");

},{"regenerator-runtime":"cH8Iq"}],"cH8Iq":[function(require,module,exports) {
/**
 * Copyright (c) 2014-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */ var runtime = function(exports) {
    var Op = Object.prototype;
    var hasOwn = Op.hasOwnProperty;
    var undefined; // More compressible than void 0.
    var $Symbol = typeof Symbol === "function" ? Symbol : {
    };
    var iteratorSymbol = $Symbol.iterator || "@@iterator";
    var asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator";
    var toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag";
    function define(obj, key, value) {
        Object.defineProperty(obj, key, {
            value: value,
            enumerable: true,
            configurable: true,
            writable: true
        });
        return obj[key];
    }
    try {
        // IE 8 has a broken Object.defineProperty that only works on DOM objects.
        define({
        }, "");
    } catch (err) {
        define = function(obj, key, value) {
            return obj[key] = value;
        };
    }
    function wrap(innerFn, outerFn, self, tryLocsList) {
        // If outerFn provided and outerFn.prototype is a Generator, then outerFn.prototype instanceof Generator.
        var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator;
        var generator = Object.create(protoGenerator.prototype);
        var context = new Context(tryLocsList || []);
        // The ._invoke method unifies the implementations of the .next,
        // .throw, and .return methods.
        generator._invoke = makeInvokeMethod(innerFn, self, context);
        return generator;
    }
    exports.wrap = wrap;
    // Try/catch helper to minimize deoptimizations. Returns a completion
    // record like context.tryEntries[i].completion. This interface could
    // have been (and was previously) designed to take a closure to be
    // invoked without arguments, but in all the cases we care about we
    // already have an existing method we want to call, so there's no need
    // to create a new function object. We can even get away with assuming
    // the method takes exactly one argument, since that happens to be true
    // in every case, so we don't have to touch the arguments object. The
    // only additional allocation required is the completion record, which
    // has a stable shape and so hopefully should be cheap to allocate.
    function tryCatch(fn, obj, arg) {
        try {
            return {
                type: "normal",
                arg: fn.call(obj, arg)
            };
        } catch (err) {
            return {
                type: "throw",
                arg: err
            };
        }
    }
    var GenStateSuspendedStart = "suspendedStart";
    var GenStateSuspendedYield = "suspendedYield";
    var GenStateExecuting = "executing";
    var GenStateCompleted = "completed";
    // Returning this object from the innerFn has the same effect as
    // breaking out of the dispatch switch statement.
    var ContinueSentinel = {
    };
    // Dummy constructor functions that we use as the .constructor and
    // .constructor.prototype properties for functions that return Generator
    // objects. For full spec compliance, you may wish to configure your
    // minifier not to mangle the names of these two functions.
    function Generator() {
    }
    function GeneratorFunction() {
    }
    function GeneratorFunctionPrototype() {
    }
    // This is a polyfill for %IteratorPrototype% for environments that
    // don't natively support it.
    var IteratorPrototype = {
    };
    define(IteratorPrototype, iteratorSymbol, function() {
        return this;
    });
    var getProto = Object.getPrototypeOf;
    var NativeIteratorPrototype = getProto && getProto(getProto(values([])));
    if (NativeIteratorPrototype && NativeIteratorPrototype !== Op && hasOwn.call(NativeIteratorPrototype, iteratorSymbol)) // This environment has a native %IteratorPrototype%; use it instead
    // of the polyfill.
    IteratorPrototype = NativeIteratorPrototype;
    var Gp = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(IteratorPrototype);
    GeneratorFunction.prototype = GeneratorFunctionPrototype;
    define(Gp, "constructor", GeneratorFunctionPrototype);
    define(GeneratorFunctionPrototype, "constructor", GeneratorFunction);
    GeneratorFunction.displayName = define(GeneratorFunctionPrototype, toStringTagSymbol, "GeneratorFunction");
    // Helper for defining the .next, .throw, and .return methods of the
    // Iterator interface in terms of a single ._invoke method.
    function defineIteratorMethods(prototype) {
        [
            "next",
            "throw",
            "return"
        ].forEach(function(method) {
            define(prototype, method, function(arg) {
                return this._invoke(method, arg);
            });
        });
    }
    exports.isGeneratorFunction = function(genFun) {
        var ctor = typeof genFun === "function" && genFun.constructor;
        return ctor ? ctor === GeneratorFunction || // For the native GeneratorFunction constructor, the best we can
        // do is to check its .name property.
        (ctor.displayName || ctor.name) === "GeneratorFunction" : false;
    };
    exports.mark = function(genFun) {
        if (Object.setPrototypeOf) Object.setPrototypeOf(genFun, GeneratorFunctionPrototype);
        else {
            genFun.__proto__ = GeneratorFunctionPrototype;
            define(genFun, toStringTagSymbol, "GeneratorFunction");
        }
        genFun.prototype = Object.create(Gp);
        return genFun;
    };
    // Within the body of any async function, `await x` is transformed to
    // `yield regeneratorRuntime.awrap(x)`, so that the runtime can test
    // `hasOwn.call(value, "__await")` to determine if the yielded value is
    // meant to be awaited.
    exports.awrap = function(arg) {
        return {
            __await: arg
        };
    };
    function AsyncIterator(generator, PromiseImpl) {
        function invoke(method, arg, resolve, reject) {
            var record = tryCatch(generator[method], generator, arg);
            if (record.type === "throw") reject(record.arg);
            else {
                var result = record.arg;
                var value = result.value;
                if (value && typeof value === "object" && hasOwn.call(value, "__await")) return PromiseImpl.resolve(value.__await).then(function(value1) {
                    invoke("next", value1, resolve, reject);
                }, function(err) {
                    invoke("throw", err, resolve, reject);
                });
                return PromiseImpl.resolve(value).then(function(unwrapped) {
                    // When a yielded Promise is resolved, its final value becomes
                    // the .value of the Promise<{value,done}> result for the
                    // current iteration.
                    result.value = unwrapped;
                    resolve(result);
                }, function(error) {
                    // If a rejected Promise was yielded, throw the rejection back
                    // into the async generator function so it can be handled there.
                    return invoke("throw", error, resolve, reject);
                });
            }
        }
        var previousPromise;
        function enqueue(method, arg) {
            function callInvokeWithMethodAndArg() {
                return new PromiseImpl(function(resolve, reject) {
                    invoke(method, arg, resolve, reject);
                });
            }
            return previousPromise = // If enqueue has been called before, then we want to wait until
            // all previous Promises have been resolved before calling invoke,
            // so that results are always delivered in the correct order. If
            // enqueue has not been called before, then it is important to
            // call invoke immediately, without waiting on a callback to fire,
            // so that the async generator function has the opportunity to do
            // any necessary setup in a predictable way. This predictability
            // is why the Promise constructor synchronously invokes its
            // executor callback, and why async functions synchronously
            // execute code before the first await. Since we implement simple
            // async functions in terms of async generators, it is especially
            // important to get this right, even though it requires care.
            previousPromise ? previousPromise.then(callInvokeWithMethodAndArg, // Avoid propagating failures to Promises returned by later
            // invocations of the iterator.
            callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg();
        }
        // Define the unified helper method that is used to implement .next,
        // .throw, and .return (see defineIteratorMethods).
        this._invoke = enqueue;
    }
    defineIteratorMethods(AsyncIterator.prototype);
    define(AsyncIterator.prototype, asyncIteratorSymbol, function() {
        return this;
    });
    exports.AsyncIterator = AsyncIterator;
    // Note that simple async functions are implemented on top of
    // AsyncIterator objects; they just return a Promise for the value of
    // the final result produced by the iterator.
    exports.async = function(innerFn, outerFn, self, tryLocsList, PromiseImpl) {
        if (PromiseImpl === void 0) PromiseImpl = Promise;
        var iter = new AsyncIterator(wrap(innerFn, outerFn, self, tryLocsList), PromiseImpl);
        return exports.isGeneratorFunction(outerFn) ? iter // If outerFn is a generator, return the full iterator.
         : iter.next().then(function(result) {
            return result.done ? result.value : iter.next();
        });
    };
    function makeInvokeMethod(innerFn, self, context) {
        var state = GenStateSuspendedStart;
        return function invoke(method, arg) {
            if (state === GenStateExecuting) throw new Error("Generator is already running");
            if (state === GenStateCompleted) {
                if (method === "throw") throw arg;
                // Be forgiving, per 25.3.3.3.3 of the spec:
                // https://people.mozilla.org/~jorendorff/es6-draft.html#sec-generatorresume
                return doneResult();
            }
            context.method = method;
            context.arg = arg;
            while(true){
                var delegate = context.delegate;
                if (delegate) {
                    var delegateResult = maybeInvokeDelegate(delegate, context);
                    if (delegateResult) {
                        if (delegateResult === ContinueSentinel) continue;
                        return delegateResult;
                    }
                }
                if (context.method === "next") // Setting context._sent for legacy support of Babel's
                // function.sent implementation.
                context.sent = context._sent = context.arg;
                else if (context.method === "throw") {
                    if (state === GenStateSuspendedStart) {
                        state = GenStateCompleted;
                        throw context.arg;
                    }
                    context.dispatchException(context.arg);
                } else if (context.method === "return") context.abrupt("return", context.arg);
                state = GenStateExecuting;
                var record = tryCatch(innerFn, self, context);
                if (record.type === "normal") {
                    // If an exception is thrown from innerFn, we leave state ===
                    // GenStateExecuting and loop back for another invocation.
                    state = context.done ? GenStateCompleted : GenStateSuspendedYield;
                    if (record.arg === ContinueSentinel) continue;
                    return {
                        value: record.arg,
                        done: context.done
                    };
                } else if (record.type === "throw") {
                    state = GenStateCompleted;
                    // Dispatch the exception by looping back around to the
                    // context.dispatchException(context.arg) call above.
                    context.method = "throw";
                    context.arg = record.arg;
                }
            }
        };
    }
    // Call delegate.iterator[context.method](context.arg) and handle the
    // result, either by returning a { value, done } result from the
    // delegate iterator, or by modifying context.method and context.arg,
    // setting context.delegate to null, and returning the ContinueSentinel.
    function maybeInvokeDelegate(delegate, context) {
        var method = delegate.iterator[context.method];
        if (method === undefined) {
            // A .throw or .return when the delegate iterator has no .throw
            // method always terminates the yield* loop.
            context.delegate = null;
            if (context.method === "throw") {
                // Note: ["return"] must be used for ES3 parsing compatibility.
                if (delegate.iterator["return"]) {
                    // If the delegate iterator has a return method, give it a
                    // chance to clean up.
                    context.method = "return";
                    context.arg = undefined;
                    maybeInvokeDelegate(delegate, context);
                    if (context.method === "throw") // If maybeInvokeDelegate(context) changed context.method from
                    // "return" to "throw", let that override the TypeError below.
                    return ContinueSentinel;
                }
                context.method = "throw";
                context.arg = new TypeError("The iterator does not provide a 'throw' method");
            }
            return ContinueSentinel;
        }
        var record = tryCatch(method, delegate.iterator, context.arg);
        if (record.type === "throw") {
            context.method = "throw";
            context.arg = record.arg;
            context.delegate = null;
            return ContinueSentinel;
        }
        var info = record.arg;
        if (!info) {
            context.method = "throw";
            context.arg = new TypeError("iterator result is not an object");
            context.delegate = null;
            return ContinueSentinel;
        }
        if (info.done) {
            // Assign the result of the finished delegate to the temporary
            // variable specified by delegate.resultName (see delegateYield).
            context[delegate.resultName] = info.value;
            // Resume execution at the desired location (see delegateYield).
            context.next = delegate.nextLoc;
            // If context.method was "throw" but the delegate handled the
            // exception, let the outer generator proceed normally. If
            // context.method was "next", forget context.arg since it has been
            // "consumed" by the delegate iterator. If context.method was
            // "return", allow the original .return call to continue in the
            // outer generator.
            if (context.method !== "return") {
                context.method = "next";
                context.arg = undefined;
            }
        } else // Re-yield the result returned by the delegate method.
        return info;
        // The delegate iterator is finished, so forget it and continue with
        // the outer generator.
        context.delegate = null;
        return ContinueSentinel;
    }
    // Define Generator.prototype.{next,throw,return} in terms of the
    // unified ._invoke helper method.
    defineIteratorMethods(Gp);
    define(Gp, toStringTagSymbol, "Generator");
    // A Generator should always return itself as the iterator object when the
    // @@iterator function is called on it. Some browsers' implementations of the
    // iterator prototype chain incorrectly implement this, causing the Generator
    // object to not be returned from this call. This ensures that doesn't happen.
    // See https://github.com/facebook/regenerator/issues/274 for more details.
    define(Gp, iteratorSymbol, function() {
        return this;
    });
    define(Gp, "toString", function() {
        return "[object Generator]";
    });
    function pushTryEntry(locs) {
        var entry = {
            tryLoc: locs[0]
        };
        if (1 in locs) entry.catchLoc = locs[1];
        if (2 in locs) {
            entry.finallyLoc = locs[2];
            entry.afterLoc = locs[3];
        }
        this.tryEntries.push(entry);
    }
    function resetTryEntry(entry) {
        var record = entry.completion || {
        };
        record.type = "normal";
        delete record.arg;
        entry.completion = record;
    }
    function Context(tryLocsList) {
        // The root entry object (effectively a try statement without a catch
        // or a finally block) gives us a place to store values thrown from
        // locations where there is no enclosing try statement.
        this.tryEntries = [
            {
                tryLoc: "root"
            }
        ];
        tryLocsList.forEach(pushTryEntry, this);
        this.reset(true);
    }
    exports.keys = function(object) {
        var keys = [];
        for(var key in object)keys.push(key);
        keys.reverse();
        // Rather than returning an object with a next method, we keep
        // things simple and return the next function itself.
        return function next() {
            while(keys.length){
                var key1 = keys.pop();
                if (key1 in object) {
                    next.value = key1;
                    next.done = false;
                    return next;
                }
            }
            // To avoid creating an additional object, we just hang the .value
            // and .done properties off the next function object itself. This
            // also ensures that the minifier will not anonymize the function.
            next.done = true;
            return next;
        };
    };
    function values(iterable) {
        if (iterable) {
            var iteratorMethod = iterable[iteratorSymbol];
            if (iteratorMethod) return iteratorMethod.call(iterable);
            if (typeof iterable.next === "function") return iterable;
            if (!isNaN(iterable.length)) {
                var i = -1, next = function next1() {
                    while((++i) < iterable.length)if (hasOwn.call(iterable, i)) {
                        next1.value = iterable[i];
                        next1.done = false;
                        return next1;
                    }
                    next1.value = undefined;
                    next1.done = true;
                    return next1;
                };
                return next.next = next;
            }
        }
        // Return an iterator with no values.
        return {
            next: doneResult
        };
    }
    exports.values = values;
    function doneResult() {
        return {
            value: undefined,
            done: true
        };
    }
    Context.prototype = {
        constructor: Context,
        reset: function(skipTempReset) {
            this.prev = 0;
            this.next = 0;
            // Resetting context._sent for legacy support of Babel's
            // function.sent implementation.
            this.sent = this._sent = undefined;
            this.done = false;
            this.delegate = null;
            this.method = "next";
            this.arg = undefined;
            this.tryEntries.forEach(resetTryEntry);
            if (!skipTempReset) {
                for(var name in this)// Not sure about the optimal order of these conditions:
                if (name.charAt(0) === "t" && hasOwn.call(this, name) && !isNaN(+name.slice(1))) this[name] = undefined;
            }
        },
        stop: function() {
            this.done = true;
            var rootEntry = this.tryEntries[0];
            var rootRecord = rootEntry.completion;
            if (rootRecord.type === "throw") throw rootRecord.arg;
            return this.rval;
        },
        dispatchException: function(exception) {
            if (this.done) throw exception;
            var context = this;
            function handle(loc, caught) {
                record.type = "throw";
                record.arg = exception;
                context.next = loc;
                if (caught) {
                    // If the dispatched exception was caught by a catch block,
                    // then let that catch block handle the exception normally.
                    context.method = "next";
                    context.arg = undefined;
                }
                return !!caught;
            }
            for(var i = this.tryEntries.length - 1; i >= 0; --i){
                var entry = this.tryEntries[i];
                var record = entry.completion;
                if (entry.tryLoc === "root") // Exception thrown outside of any try block that could handle
                // it, so set the completion value of the entire function to
                // throw the exception.
                return handle("end");
                if (entry.tryLoc <= this.prev) {
                    var hasCatch = hasOwn.call(entry, "catchLoc");
                    var hasFinally = hasOwn.call(entry, "finallyLoc");
                    if (hasCatch && hasFinally) {
                        if (this.prev < entry.catchLoc) return handle(entry.catchLoc, true);
                        else if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc);
                    } else if (hasCatch) {
                        if (this.prev < entry.catchLoc) return handle(entry.catchLoc, true);
                    } else if (hasFinally) {
                        if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc);
                    } else throw new Error("try statement without catch or finally");
                }
            }
        },
        abrupt: function(type, arg) {
            for(var i = this.tryEntries.length - 1; i >= 0; --i){
                var entry = this.tryEntries[i];
                if (entry.tryLoc <= this.prev && hasOwn.call(entry, "finallyLoc") && this.prev < entry.finallyLoc) {
                    var finallyEntry = entry;
                    break;
                }
            }
            if (finallyEntry && (type === "break" || type === "continue") && finallyEntry.tryLoc <= arg && arg <= finallyEntry.finallyLoc) // Ignore the finally entry if control is not jumping to a
            // location outside the try/catch block.
            finallyEntry = null;
            var record = finallyEntry ? finallyEntry.completion : {
            };
            record.type = type;
            record.arg = arg;
            if (finallyEntry) {
                this.method = "next";
                this.next = finallyEntry.finallyLoc;
                return ContinueSentinel;
            }
            return this.complete(record);
        },
        complete: function(record, afterLoc) {
            if (record.type === "throw") throw record.arg;
            if (record.type === "break" || record.type === "continue") this.next = record.arg;
            else if (record.type === "return") {
                this.rval = this.arg = record.arg;
                this.method = "return";
                this.next = "end";
            } else if (record.type === "normal" && afterLoc) this.next = afterLoc;
            return ContinueSentinel;
        },
        finish: function(finallyLoc) {
            for(var i = this.tryEntries.length - 1; i >= 0; --i){
                var entry = this.tryEntries[i];
                if (entry.finallyLoc === finallyLoc) {
                    this.complete(entry.completion, entry.afterLoc);
                    resetTryEntry(entry);
                    return ContinueSentinel;
                }
            }
        },
        "catch": function(tryLoc) {
            for(var i = this.tryEntries.length - 1; i >= 0; --i){
                var entry = this.tryEntries[i];
                if (entry.tryLoc === tryLoc) {
                    var record = entry.completion;
                    if (record.type === "throw") {
                        var thrown = record.arg;
                        resetTryEntry(entry);
                    }
                    return thrown;
                }
            }
            // The context.catch method must only be called with a location
            // argument that corresponds to a known catch block.
            throw new Error("illegal catch attempt");
        },
        delegateYield: function(iterable, resultName, nextLoc) {
            this.delegate = {
                iterator: values(iterable),
                resultName: resultName,
                nextLoc: nextLoc
            };
            if (this.method === "next") // Deliberately forget the last sent value so that we don't
            // accidentally pass it on to the delegate.
            this.arg = undefined;
            return ContinueSentinel;
        }
    };
    // Regardless of whether this script is executing as a CommonJS module
    // or not, return the runtime object so that we can declare the variable
    // regeneratorRuntime in the outer scope, which allows this module to be
    // injected easily by `bin/regenerator --include-runtime script.js`.
    return exports;
}(// If this script is executing as a CommonJS module, use module.exports
// as the regeneratorRuntime namespace. Otherwise create a new empty
// object. Either way, the resulting object will be used to initialize
// the regeneratorRuntime variable at the top of this file.
typeof module === "object" ? module.exports : {
});
try {
    regeneratorRuntime = runtime;
} catch (accidentalStrictMode) {
    // This module should not be running in strict mode, so the above
    // assignment should always work unless something is misconfigured. Just
    // in case runtime.js accidentally runs in strict mode, in modern engines
    // we can explicitly access globalThis. In older engines we can escape
    // strict mode using a global Function call. This could conceivably fail
    // if a Content Security Policy forbids using Function, but in that case
    // the proper solution is to fix the accidental strict mode problem. If
    // you've misconfigured your bundler to force strict mode and applied a
    // CSP to forbid Function, and you're not willing to fix either of those
    // problems, please detail your unique predicament in a GitHub issue.
    if (typeof globalThis === "object") globalThis.regeneratorRuntime = runtime;
    else Function("r", "regeneratorRuntime = r")(runtime);
}

},{}],"1XGzZ":[function(require,module,exports) {
function _typeof(obj) {
    if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
        module.exports = _typeof = function _typeof1(obj1) {
            return typeof obj1;
        };
        module.exports["default"] = module.exports, module.exports.__esModule = true;
    } else {
        module.exports = _typeof = function _typeof1(obj1) {
            return obj1 && typeof Symbol === "function" && obj1.constructor === Symbol && obj1 !== Symbol.prototype ? "symbol" : typeof obj1;
        };
        module.exports["default"] = module.exports, module.exports.__esModule = true;
    }
    return _typeof(obj);
}
module.exports = _typeof;
module.exports["default"] = module.exports, module.exports.__esModule = true;

},{}],"5j50L":[function(require,module,exports) {
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) {
    try {
        var info = gen[key](arg);
        var value = info.value;
    } catch (error) {
        reject(error);
        return;
    }
    if (info.done) resolve(value);
    else Promise.resolve(value).then(_next, _throw);
}
function _asyncToGenerator(fn) {
    return function() {
        var self = this, args = arguments;
        return new Promise(function(resolve, reject) {
            var gen = fn.apply(self, args);
            function _next(value) {
                asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value);
            }
            function _throw(err) {
                asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err);
            }
            _next(undefined);
        });
    };
}
module.exports = _asyncToGenerator;
module.exports["default"] = module.exports, module.exports.__esModule = true;

},{}],"hmNok":[function(require,module,exports) {
"use strict";
var _interopRequireDefault = require("@babel/runtime/helpers/interopRequireDefault");
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = exports.camelToUnderscore = void 0;
var _classCallCheck2 = _interopRequireDefault(require("@babel/runtime/helpers/classCallCheck"));
var _createClass2 = _interopRequireDefault(require("@babel/runtime/helpers/createClass"));
var _typeof2 = _interopRequireDefault(require("@babel/runtime/helpers/typeof"));
var _justSafeGet = _interopRequireDefault(require("just-safe-get"));
var _get_localized = function _get_localized1(ret, locales) {
    if (_typeof2.default(ret) === 'object' && ret !== null) {
        if (_typeof2.default(ret.names) === 'object' && _typeof2.default(locales) === 'object') for(var i = 0; i < locales.length; i++){
            var locale = locales[i];
            if (ret.names[locale]) return ret.names[locale];
        }
        if (ret.name) return ret.name;
        return '';
    }
    return ret;
};
var camelToUnderscore = function camelToUnderscore1(key) {
    key = key.split('.').map(function(x) {
        if (typeof x !== 'string' || typeof x[0] !== 'string') return '';
        x = x[0].toLowerCase() + x.slice(1); // to allow "MostSpecificSubdivision"
        x = x.replace(/([A-Z])/g, "_$1").toLowerCase();
        return x;
    }).join('.');
    return key;
};
exports.camelToUnderscore = camelToUnderscore;
var Record = /*#__PURE__*/ function() {
    function Record1(data, default_locales) {
        _classCallCheck2.default(this, Record1);
        this.data = {
        };
        this.default_locales = [];
        this.data = data || {
            is_empty: true
        };
        this.default_locales = [
            'en'
        ];
        this.default_locales = this._process_locales(default_locales);
    }
    _createClass2.default(Record1, [
        {
            key: "get",
            value: function get(prop, default_value) {
                return this.get_with_locales(prop, null, default_value);
            }
        },
        {
            key: "get_raw",
            value: function get_raw(prop) {
                prop = camelToUnderscore(prop);
                return _justSafeGet.default(this.data, prop, null);
            }
        },
        {
            key: "has_property",
            value: function has_property(prop) {
                var ret = this._lookup_with_locales(prop, this.default_locales, null);
                return ret !== null;
            }
        },
        {
            key: "_lookup_with_locales",
            value: function _lookup_with_locales(prop, locales) {
                var default_value = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '';
                locales = this._process_locales(locales); // Treat pseudo-property 'name' as if it never existed
                if (prop.substr(-5) === '.name') prop = prop.substr(0, prop.length - 5);
                var ret = this.get_raw(prop); // Localize property, if possible
                ret = _get_localized(ret, locales);
                if (ret === null || ret === '') ret = default_value;
                return ret;
            }
        },
        {
            key: "_process_locales",
            value: function _process_locales(locales) {
                if (typeof locales === 'string') locales = [
                    locales
                ];
                if (!Array.isArray(locales) || locales.length === 0) locales = this.default_locales;
                return locales;
            }
        },
        {
            key: "get_with_locales",
            value: function get_with_locales(prop, locales, default_value) {
                var ret = this._lookup_with_locales(prop, locales, default_value);
                if (_typeof2.default(ret) === 'object') console.warn('Geolocation IP Detection: The property "' + prop + '" is of type "' + _typeof2.default(ret) + '", should be string or similar', ret);
                if (typeof ret === 'undefined') {
                    console.warn('Geolocation IP Detection: The property "' + prop + '" is not defined, please check spelling or maybe you need a different data source', {
                        data: this.data
                    });
                    return '';
                }
                return ret;
            }
        },
        {
            key: "get_country_iso",
            value: function get_country_iso() {
                var country = this.get('country.iso_code');
                if (country) country = country.substr(0, 2).toLowerCase();
                return country;
            }
        },
        {
            key: "is_empty",
            value: function is_empty() {
                return this.get('is_empty', false);
            }
        },
        {
            key: "error",
            value: function error() {
                return this.get_raw('extra.error') || '';
            }
        },
        {
            key: "serialize",
            value: function serialize() {
                return this.data;
            }
        }
    ]);
    return Record1;
}();
var _default = Record;
exports.default = _default;

},{"@babel/runtime/helpers/interopRequireDefault":"eigyQ","@babel/runtime/helpers/classCallCheck":"fIqcI","@babel/runtime/helpers/createClass":"eFNXV","@babel/runtime/helpers/typeof":"1XGzZ","just-safe-get":"eXzhy"}],"fIqcI":[function(require,module,exports) {
function _classCallCheck(instance, Constructor) {
    if (!(instance instanceof Constructor)) throw new TypeError("Cannot call a class as a function");
}
module.exports = _classCallCheck;
module.exports["default"] = module.exports, module.exports.__esModule = true;

},{}],"eFNXV":[function(require,module,exports) {
function _defineProperties(target, props) {
    for(var i = 0; i < props.length; i++){
        var descriptor = props[i];
        descriptor.enumerable = descriptor.enumerable || false;
        descriptor.configurable = true;
        if ("value" in descriptor) descriptor.writable = true;
        Object.defineProperty(target, descriptor.key, descriptor);
    }
}
function _createClass(Constructor, protoProps, staticProps) {
    if (protoProps) _defineProperties(Constructor.prototype, protoProps);
    if (staticProps) _defineProperties(Constructor, staticProps);
    return Constructor;
}
module.exports = _createClass;
module.exports["default"] = module.exports, module.exports.__esModule = true;

},{}],"eXzhy":[function(require,module,exports) {
module.exports = get;
/*
  const obj = {a: {aa: {aaa: 2}}, b: 4};

  get(obj, 'a.aa.aaa'); // 2
  get(obj, ['a', 'aa', 'aaa']); // 2

  get(obj, 'b.bb.bbb'); // undefined
  get(obj, ['b', 'bb', 'bbb']); // undefined

  get(obj.a, 'aa.aaa'); // 2
  get(obj.a, ['aa', 'aaa']); // 2

  get(obj.b, 'bb.bbb'); // undefined
  get(obj.b, ['bb', 'bbb']); // undefined

  get(obj.b, 'bb.bbb', 42); // 42
  get(obj.b, ['bb', 'bbb'], 42); // 42

  get(null, 'a'); // undefined
  get(undefined, ['a']); // undefined

  get(null, 'a', 42); // 42
  get(undefined, ['a'], 42); // 42

  const obj = {a: {}};
  const sym = Symbol();
  obj.a[sym] = 4;
  get(obj.a, sym); // 4
*/ function get(obj, propsArg, defaultValue) {
    if (!obj) return defaultValue;
    var props, prop;
    if (Array.isArray(propsArg)) props = propsArg.slice(0);
    if (typeof propsArg == 'string') props = propsArg.split('.');
    if (typeof propsArg == 'symbol') props = [
        propsArg
    ];
    if (!Array.isArray(props)) throw new Error('props arg must be an array, a string or a symbol');
    while(props.length){
        prop = props.shift();
        if (!obj) return defaultValue;
        obj = obj[prop];
        if (obj === undefined) return defaultValue;
    }
    return obj;
}

},{}],"fkAhj":[function(require,module,exports) {
"use strict";
var _interopRequireDefault = require("@babel/runtime/helpers/interopRequireDefault");
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.makeJSONRequest = exports.jsonDecodeIfPossible = exports.makeRequest = void 0;
var _regenerator = _interopRequireDefault(require("@babel/runtime/regenerator"));
var _asyncToGenerator2 = _interopRequireDefault(require("@babel/runtime/helpers/asyncToGenerator"));
// @see https://gomakethings.com/promise-based-xhr/
var makeRequest = function makeRequest1(url) {
    var method = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'GET';
    // Create the XHR request
    var request = new XMLHttpRequest(); // Return it as a Promise
    return new Promise(function(resolve, reject) {
        // Setup our listener to process compeleted requests
        request.onreadystatechange = function() {
            // Only run if the request is complete
            if (request.readyState !== 4) return; // Process the response
            if (request.status >= 200 && request.status < 300) // If successful
            resolve(request);
            else // If failed
            reject({
                status: request.status,
                statusText: request.statusText,
                request: request
            });
        }; // Setup our HTTP request
        request.open(method || 'GET', url, true); // Send the request
        request.send();
    });
};
exports.makeRequest = makeRequest;
var jsonDecodeIfPossible = function jsonDecodeIfPossible1(str) {
    try {
        return JSON.parse(str);
    } catch (e) {
        return createErrorObject('Invalid JSON: ' + str);
    }
};
exports.jsonDecodeIfPossible = jsonDecodeIfPossible;
function createErrorObject(errorMsg) {
    return {
        is_empty: true,
        extra: {
            error: errorMsg
        }
    };
}
var makeJSONRequest = /*#__PURE__*/ function() {
    var _ref = _asyncToGenerator2.default(/*#__PURE__*/ _regenerator.default.mark(function _callee(url) {
        var method, request, _args = arguments;
        return _regenerator.default.wrap(function _callee$(_context) {
            while(true)switch(_context.prev = _context.next){
                case 0:
                    method = _args.length > 1 && _args[1] !== undefined ? _args[1] : 'GET';
                    _context.prev = 1;
                    _context.next = 4;
                    return makeRequest(url, method);
                case 4:
                    request = _context.sent;
                    if (!(!request.responseText || request.responseText === '0')) {
                        _context.next = 7;
                        break;
                    }
                    return _context.abrupt("return", createErrorObject('Got an empty response from server. Did you enable AJAX in the options?'));
                case 7:
                    return _context.abrupt("return", jsonDecodeIfPossible(request.responseText));
                case 10:
                    _context.prev = 10;
                    _context.t0 = _context["catch"](1);
                    return _context.abrupt("return", jsonDecodeIfPossible(_context.t0.request.responseText));
                case 13:
                case "end":
                    return _context.stop();
            }
        }, _callee, null, [
            [
                1,
                10
            ]
        ]);
    }));
    return function makeJSONRequest1(_x) {
        return _ref.apply(this, arguments);
    };
}();
exports.makeJSONRequest = makeJSONRequest;

},{"@babel/runtime/helpers/interopRequireDefault":"eigyQ","@babel/runtime/regenerator":"1L3WO","@babel/runtime/helpers/asyncToGenerator":"5j50L"}],"lMA0q":[function(require,module,exports) {
"use strict";
var _interopRequireDefault = require("@babel/runtime/helpers/interopRequireDefault");
var _typeof3 = require("@babel/runtime/helpers/typeof");
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.set_override_with_merge = set_override_with_merge;
exports.set_override = set_override;
exports.remove_override = remove_override;
exports.getRecordDataFromLocalStorage = getRecordDataFromLocalStorage;
exports.setRecordDataToLocalStorage = setRecordDataToLocalStorage;
exports.get_info_stored_locally_record = get_info_stored_locally_record;
var _typeof2 = _interopRequireDefault(require("@babel/runtime/helpers/typeof"));
var _localStorageAccess = require("../lib/localStorageAccess");
var _get_info = require("./get_info");
var _record = _interopRequireWildcard(require("../models/record"));
var _justSafeSet = _interopRequireDefault(require("just-safe-set"));
var _justSafeGet = _interopRequireDefault(require("just-safe-get"));
var _justCompare = _interopRequireDefault(require("just-compare"));
var _main = require("../main");
function _getRequireWildcardCache(nodeInterop) {
    if (typeof WeakMap !== "function") return null;
    var cacheBabelInterop = new WeakMap();
    var cacheNodeInterop = new WeakMap();
    return (_getRequireWildcardCache = function _getRequireWildcardCache1(nodeInterop1) {
        return nodeInterop1 ? cacheNodeInterop : cacheBabelInterop;
    })(nodeInterop);
}
function _interopRequireWildcard(obj, nodeInterop) {
    if (!nodeInterop && obj && obj.__esModule) return obj;
    if (obj === null || _typeof3(obj) !== "object" && typeof obj !== "function") return {
        default: obj
    };
    var cache = _getRequireWildcardCache(nodeInterop);
    if (cache && cache.has(obj)) return cache.get(obj);
    var newObj = {
    };
    var hasPropertyDescriptor = Object.defineProperty && Object.getOwnPropertyDescriptor;
    for(var key in obj)if (key !== "default" && Object.prototype.hasOwnProperty.call(obj, key)) {
        var desc = hasPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : null;
        if (desc && (desc.get || desc.set)) Object.defineProperty(newObj, key, desc);
        else newObj[key] = obj[key];
    }
    newObj.default = obj;
    if (cache) cache.set(obj, newObj);
    return newObj;
}
/**
 * Override only one property, leave the other properties as-is.
 * @param {string} property 
 * @param {*} value 
 * @param {number} duration_in_days 
 */ function set_override_with_merge(property, value, duration_in_days) {
    var record = getRecordDataFromLocalStorage() || {
    };
    property = property || '';
    property = _record.camelToUnderscore(property);
    console.log('data before', record);
    var oldData = _justSafeGet.default(record, property);
    if (_typeof2.default(oldData) == 'object' && _typeof2.default(oldData.names) == 'object') property += '.name';
    if (property.endsWith('.name')) {
        property += 's';
        value = {
            'en': value
        };
    }
    _justSafeSet.default(record, property, value);
    set_override(record, duration_in_days);
    console.log('data after', getRecordDataFromLocalStorage());
}
/**
 * This functions allows to override the geodetected data manually (e.g. a country selector)
 * 
 * @api
 * @param {*} record 
 * @param {number} duration_in_days When this override expires (default: 1 week later)
 * @return boolean TRUE if override data changed
 */ function set_override(record, duration_in_days) {
    if (record && typeof record.serialize === 'function') record = record.serialize();
    duration_in_days = duration_in_days || _get_info.options.cookie_duration_in_days;
    if (duration_in_days < 0) {
        console.warn('Geolocation IP Detection set_override_data() did nothing: A negative duration doesn\'t make sense. If you want to remove the override, use remove_override() instead.');
        return false;
    }
    return set_override_data(record, duration_in_days);
}
function set_override_data(newData, duration_in_days) {
    newData = newData || {
    };
    _justSafeSet.default(newData, 'extra.override', true);
    var oldData = getRecordDataFromLocalStorage();
    _localStorageAccess.setLocalStorage(_get_info.options.cookie_name, newData, duration_in_days * 86400);
    if (!_justCompare.default(newData, oldData)) {
        // if data has changed, trigger re-evaluation for shortcodes etc
        setTimeout(function() {
            _main.main();
        }, 10);
        return true;
    }
    return false;
}
/**
 * Remove the override data.
 * On next page load, the record data will be loaded from the server again.
 * 
 * @return boolean
 */ function remove_override() {
    _localStorageAccess.setLocalStorage(_get_info.options.cookie_name, {
    }, -1);
    return true;
} // Sync function in case it is known that no AJAX will occur
function getRecordDataFromLocalStorage() {
    return _localStorageAccess.getLocalStorage(_get_info.options.cookie_name);
}
function setRecordDataToLocalStorage(data, cache_duration) {
    _localStorageAccess.setLocalStorage(_get_info.options.cookie_name, data, cache_duration);
}
function get_info_stored_locally_record() {
    return new _record.default(getRecordDataFromLocalStorage(), _get_info.options.default_locales);
}

},{"@babel/runtime/helpers/interopRequireDefault":"eigyQ","@babel/runtime/helpers/typeof":"1XGzZ","../lib/localStorageAccess":"zpcTZ","./get_info":"eKpYj","../models/record":"hmNok","just-safe-set":"2Kuev","just-compare":"5jX6S","../main":"gI94w","just-safe-get":"eXzhy"}],"zpcTZ":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.getLocalStorage = exports.setLocalStorage = void 0;
var setLocalStorage = function setLocalStorage1(variable, value, ttl_sec) {
    var data = {
        value: value,
        expires_at: new Date().getTime() + ttl_sec * 1000 / 1
    };
    localStorage.setItem(variable.toString(), JSON.stringify(data));
};
exports.setLocalStorage = setLocalStorage;
var getLocalStorage = function getLocalStorage1(variable) {
    var data = null;
    try {
        data = JSON.parse(localStorage.getItem(variable.toString()));
    } catch (e) {
        return null;
    }
    if (data !== null) {
        if (data.expires_at !== null && data.expires_at < new Date().getTime()) localStorage.removeItem(variable.toString());
        else return data.value;
    }
    return null;
};
exports.getLocalStorage = getLocalStorage;

},{}],"2Kuev":[function(require,module,exports) {
module.exports = set;
/*
  var obj1 = {};
  set(obj1, 'a.aa.aaa', 4); // true
  obj1; // {a: {aa: {aaa: 4}}}

  var obj2 = {};
  set(obj2, ['a', 'aa', 'aaa'], 4); // true
  obj2; // {a: {aa: {aaa: 4}}}

  var obj3 = {a: {aa: {aaa: 2}}};
  set(obj3, 'a.aa.aaa', 3); // true
  obj3; // {a: {aa: {aaa: 3}}}

  // don't clobber existing
  var obj4 = {a: {aa: {aaa: 2}}};
  set(obj4, 'a.aa', {bbb: 7}); // false

  const obj5 = {a: {}};
  const sym = Symbol();
  set(obj5.a, sym, 7); // true
  obj5; // {a: {Symbol(): 7}}
*/ function set(obj, propsArg, value) {
    var props, lastProp;
    if (Array.isArray(propsArg)) props = propsArg.slice(0);
    if (typeof propsArg == 'string') props = propsArg.split('.');
    if (typeof propsArg == 'symbol') props = [
        propsArg
    ];
    if (!Array.isArray(props)) throw new Error('props arg must be an array, a string or a symbol');
    lastProp = props.pop();
    if (!lastProp) return false;
    prototypeCheck(lastProp);
    var thisProp;
    while(thisProp = props.shift()){
        prototypeCheck(thisProp);
        if (typeof obj[thisProp] == 'undefined') obj[thisProp] = {
        };
        obj = obj[thisProp];
        if (!obj || typeof obj != 'object') return false;
    }
    obj[lastProp] = value;
    return true;
}
function prototypeCheck(prop) {
    if (prop === '__proto__' || prop === 'constructor' || prop === 'prototype') throw new Error('setting of prototype values not supported');
}

},{}],"5jX6S":[function(require,module,exports) {
module.exports = compare;
/*
  primitives: value1 === value2
  functions: value1.toString == value2.toString
  arrays: if length, sequence and values of properties are identical
  objects: if length, names and values of properties are identical
  compare([[1, [2, 3]], [[1, [2, 3]]); // true
  compare([[1, [2, 3], 4], [[1, [2, 3]]); // false
  compare({a: 2, b: 3}, {a: 2, b: 3}); // true
  compare({a: 2, b: 3}, {b: 3, a: 2}); // true
  compare({a: 2, b: 3, c: 4}, {a: 2, b: 3}); // false
  compare({a: 2, b: 3}, {a: 2, b: 3, c: 4}); // false
  compare([[1, [2, {a: 4}], 4], [[1, [2, {a: 4}]]); // true
*/ function compare(value1, value2) {
    if (value1 === value2) return true;
    /* eslint-disable no-self-compare */ // if both values are NaNs return true
    if (value1 !== value1 && value2 !== value2) return true;
    if (({
    }).toString.call(value1) != ({
    }).toString.call(value2)) return false;
    if (value1 !== Object(value1)) // non equal primitives
    return false;
    if (!value1) return false;
    if (Array.isArray(value1)) return compareArrays(value1, value2);
    if (({
    }).toString.call(value1) == '[object Set]') return compareArrays(Array.from(value1), Array.from(value2));
    if (({
    }).toString.call(value1) == '[object Object]') return compareObjects(value1, value2);
    else return compareNativeSubtypes(value1, value2);
}
function compareNativeSubtypes(value1, value2) {
    // e.g. Function, RegExp, Date
    return value1.toString() === value2.toString();
}
function compareArrays(value1, value2) {
    var len = value1.length;
    if (len != value2.length) return false;
    var alike = true;
    for(var i = 0; i < len; i++)if (!compare(value1[i], value2[i])) {
        alike = false;
        break;
    }
    return alike;
}
function compareObjects(value1, value2) {
    var keys1 = Object.keys(value1).sort();
    var keys2 = Object.keys(value2).sort();
    var len = keys1.length;
    if (len != keys2.length) return false;
    for(var i = 0; i < len; i++){
        var key1 = keys1[i];
        var key2 = keys2[i];
        if (!(key1 == key2 && compare(value1[key1], value2[key2]))) return false;
    }
    return true;
}

},{}],"gI94w":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.main = main;
var _index = require("./shortcodes/index");
var _body_classes = require("./body_classes");
var _get_info = require("./lookup/get_info");
var firstCall = true;
function main() {
    if (firstCall) {
        _index.do_shortcodes_init();
        firstCall = false;
    }
    if (_get_info.options.do_body_classes) _body_classes.add_body_classes();
     // Do all the shortcodes that are in the HTML. Even if shortcodes is not enabled globally, they might be enabled for a specific shortcode.
    _index.do_shortcodes();
}

},{"./shortcodes/index":"5GDfG","./body_classes":"enq3r","./lookup/get_info":"eKpYj"}],"5GDfG":[function(require,module,exports) {
"use strict";
var _interopRequireDefault = require("@babel/runtime/helpers/interopRequireDefault");
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.do_shortcodes = exports.do_shortcodes_init = void 0;
var _regenerator = _interopRequireDefault(require("@babel/runtime/regenerator"));
var _asyncToGenerator2 = _interopRequireDefault(require("@babel/runtime/helpers/asyncToGenerator"));
var _html = require("../lib/html");
var _helpers = require("./helpers");
var _normal = require("./normal");
var _onchange = require("./onchange");
var _showIf = require("./show-if");
var do_shortcodes_init = function do_shortcodes_init1() {
    _onchange.init();
};
exports.do_shortcodes_init = do_shortcodes_init;
var do_shortcodes = /*#__PURE__*/ function() {
    var _do_shortcodes = _asyncToGenerator2.default(/*#__PURE__*/ _regenerator.default.mark(function _callee() {
        return _regenerator.default.wrap(function _callee$(_context) {
            while(true)switch(_context.prev = _context.next){
                case 0:
                    _context.next = 2;
                    return _html.domReady;
                case 2:
                    // These are called in parallel, as they are async functions
                    _helpers.action_on_elements('js-geoip-detect-shortcode', 'could not execute shortcode(s) [geoip_detect2 ...]', _normal.do_shortcode_normal);
                    _helpers.action_on_elements('js-geoip-detect-flag', 'could not configure the flag(s)', _normal.do_shortcode_flags);
                    _helpers.action_on_elements('js-geoip-text-input', 'could not set the value of the text input field(s)', _normal.do_shortcode_text_input);
                    _helpers.action_on_elements('js-geoip-detect-country-select', 'could not set the value of the select field(s)', _normal.do_shortcode_country_select);
                    _helpers.action_on_elements('js-geoip-detect-show-if', 'could not execute the show-if/hide-if conditions', _showIf.do_shortcode_show_if);
                case 7:
                case "end":
                    return _context.stop();
            }
        }, _callee);
    }));
    function do_shortcodes1() {
        return _do_shortcodes.apply(this, arguments);
    }
    return do_shortcodes1;
}();
exports.do_shortcodes = do_shortcodes;

},{"@babel/runtime/helpers/interopRequireDefault":"eigyQ","@babel/runtime/regenerator":"1L3WO","@babel/runtime/helpers/asyncToGenerator":"5j50L","../lib/html":"lmPYU","./helpers":"hzVb2","./normal":"h0egg","./onchange":"5L2qh","./show-if":"kT4lU"}],"lmPYU":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.selectItemByValue = selectItemByValue;
exports.selectItemByAttribute = selectItemByAttribute;
exports.domReady = void 0;
var domReady = new Promise(function(resolve) {
    if (document.readyState === "loading") {
        if (document.addEventListener) document.addEventListener('DOMContentLoaded', resolve);
        else document.attachEvent('onreadystatechange', function() {
            if (document.readyState != 'loading') resolve();
        });
    } else resolve();
});
exports.domReady = domReady;
function selectItemByValue(el, value) {
    for(var i = 0; i < el.options.length; i++)if (el.options[i].value === value) {
        el.selectedIndex = i;
        break;
    }
}
function selectItemByAttribute(el, attributeName, attributeValue) {
    for(var i = 0; i < el.options.length; i++)if (el.options[i].getAttribute(attributeName) === attributeValue) {
        el.selectedIndex = i;
        break;
    }
}

},{}],"hzVb2":[function(require,module,exports) {
"use strict";
var _interopRequireDefault = require("@babel/runtime/helpers/interopRequireDefault");
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.get_options = get_options;
exports.action_on_elements = action_on_elements;
exports.get_value_from_record = get_value_from_record;
var _regenerator = _interopRequireDefault(require("@babel/runtime/regenerator"));
var _asyncToGenerator2 = _interopRequireDefault(require("@babel/runtime/helpers/asyncToGenerator"));
var _get_info = require("../lookup/get_info");
// Get Options from data-options and json parse them
function get_options(el) {
    var raw = el.getAttribute('data-options');
    try {
        return JSON.parse(raw);
    } catch (e) {
        return {
        };
    }
}
function action_on_elements(_x, _x2, _x3) {
    return _action_on_elements.apply(this, arguments);
}
function _action_on_elements() {
    _action_on_elements = _asyncToGenerator2.default(/*#__PURE__*/ _regenerator.default.mark(function _callee(className, errorMessage, callback) {
        var elements, record;
        return _regenerator.default.wrap(function _callee$(_context) {
            while(true)switch(_context.prev = _context.next){
                case 0:
                    elements = document.getElementsByClassName(className);
                    if (elements.length) {
                        _context.next = 3;
                        break;
                    }
                    return _context.abrupt("return");
                case 3:
                    _context.next = 5;
                    return _get_info.get_info();
                case 5:
                    record = _context.sent;
                    if (!record.error()) {
                        _context.next = 9;
                        break;
                    }
                    console.error('Geolocation IP Detection Error (' + errorMessage + '): ' + record.error());
                    return _context.abrupt("return");
                case 9:
                    Array.from(elements).forEach(function(el) {
                        return callback(el, record);
                    });
                case 10:
                case "end":
                    return _context.stop();
            }
        }, _callee);
    }));
    return _action_on_elements.apply(this, arguments);
}
function get_value_from_record(el, record) {
    var property = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
    var opt = get_options(el);
    property = property || opt.property;
    if (opt.skip_cache) console.warn("Geolocation IP Detection: The property 'skip_cache' is ignored in AJAX mode. You could disable the response caching on the server by setting the constant GEOIP_DETECT_READER_CACHE_TIME.");
    return record.get_with_locales(property, opt.lang, opt.default);
}

},{"@babel/runtime/helpers/interopRequireDefault":"eigyQ","@babel/runtime/regenerator":"1L3WO","@babel/runtime/helpers/asyncToGenerator":"5j50L","../lookup/get_info":"eKpYj"}],"h0egg":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.do_shortcode_normal = do_shortcode_normal;
exports.do_shortcode_flags = do_shortcode_flags;
exports.do_shortcode_country_select = do_shortcode_country_select;
exports.do_shortcode_text_input = do_shortcode_text_input;
var _events = require("../lib/events");
var _html = require("../lib/html");
var _helpers = require("./helpers");
function do_shortcode_normal(el, record) {
    el.innerText = _helpers.get_value_from_record(el, record);
}
function do_shortcode_flags(el, record) {
    var country = record.get_country_iso() || _helpers.get_options(el).default;
    if (country) el.classList.add('flag-icon-' + country);
}
function do_shortcode_country_select(el, record) {
    var country = record.get_country_iso();
    _html.selectItemByAttribute(el, 'data-c', country);
    _events.triggerNativeEvent(el, 'change');
}
function do_shortcode_text_input(el, record) {
    el.value = _helpers.get_value_from_record(el, record);
    _events.triggerNativeEvent(el, 'change');
}

},{"../lib/events":"TLtUS","../lib/html":"lmPYU","./helpers":"hzVb2"}],"TLtUS":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.isInternalEvent = isInternalEvent;
exports.triggerNativeEvent = triggerNativeEvent;
var _internalEvent = false;
function isInternalEvent() {
    return _internalEvent;
}
function triggerNativeEvent(el, name) {
    _internalEvent = true;
    if (document.createEvent) {
        var event = document.createEvent('HTMLEvents');
        event.initEvent(name, true, false);
        el.dispatchEvent(event);
    } else el.fireEvent('on' + name);
    _internalEvent = false;
}

},{}],"5L2qh":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.init = init;
var _events = require("../lib/events");
var _override = require("../lookup/override");
var _helpers = require("./helpers");
var _listener_active = false; // for recursion detection (maybe remove later)
var _change_counter = 0; // ToDo remove later!
function init() {
    document.addEventListener('change', event_listener_autosave_on_change, false);
}
function event_listener_autosave_on_change(event) {
    if (_events.isInternalEvent()) return;
    var target = event.target;
    if (target.matches('.js-geoip-detect-input-autosave')) {
        console.log('autosave on change', target);
        var property = _helpers.get_options(target).property;
        var value = target.value;
        if (value) {
            _change_counter++;
            if (_listener_active || _change_counter > 100) {
                console.warn('Thats weird! autosave change detected a recursion!');
                debugger;
                return;
            } else {
                _listener_active = true;
                if (target.matches('select.js-geoip-detect-country-select')) {
                    var selected = target.options[target.selectedIndex];
                    var isoCode = selected === null || selected === void 0 ? void 0 : selected.getAttribute('data-c');
                    if (isoCode) _override.set_override_with_merge('country.iso_code', isoCode.toUpperCase());
                }
                _override.set_override_with_merge(property, value); // might call do_shortcodes etc.
                _listener_active = false;
            }
        }
    }
}

},{"../lib/events":"TLtUS","../lookup/override":"lMA0q","./helpers":"hzVb2"}],"kT4lU":[function(require,module,exports) {
"use strict";
var _interopRequireDefault = require("@babel/runtime/helpers/interopRequireDefault");
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.do_shortcode_show_if = do_shortcode_show_if;
exports.geoip_detect2_shortcode_evaluate_conditions = geoip_detect2_shortcode_evaluate_conditions;
var _typeof2 = _interopRequireDefault(require("@babel/runtime/helpers/typeof"));
var _helpers = require("./helpers");
var _justIntersect = _interopRequireDefault(require("just-intersect"));
function do_shortcode_show_if(el, record) {
    var opt = _helpers.get_options(el);
    var evaluated = geoip_detect2_shortcode_evaluate_conditions(opt.parsed, opt, record);
    if (!evaluated) el.style.display = "none !important";
    else el.style.display = '';
}
function geoip_detect2_shortcode_evaluate_conditions(parsed, opt, record) {
    var alternativePropertyNames = [
        'name',
        'iso_code',
        'iso_code3',
        'code',
        'geoname_id'
    ];
    var isConditionMatching = parsed.op === 'or' ? false : true;
    parsed.conditions.forEach(function(c) {
        var subConditionMatching = false;
        var values = [];
        var raw_value = record.get_raw(c.p);
        if (raw_value === null) subConditionMatching = false;
        else if (_typeof2.default(raw_value) === 'object') alternativePropertyNames.forEach(function(name) {
            if (raw_value[name]) values.push(raw_value[name]);
            else if (name == 'name') values.push(record.get_with_locales(c.p, opt.lang));
        });
        else values = [
            raw_value
        ];
        subConditionMatching = geoip_detect2_shortcode_check_subcondition(c.v, values);
        if (c.not) subConditionMatching = !subConditionMatching;
        if (parsed.op === 'or') isConditionMatching = isConditionMatching || subConditionMatching;
        else isConditionMatching = isConditionMatching && subConditionMatching;
    });
    if (parsed.not) isConditionMatching = !isConditionMatching;
    return isConditionMatching;
}
function geoip_detect2_shortcode_check_subcondition(expectedValues, actualValues) {
    if (actualValues[0] === true) actualValues = [
        'true',
        'yes',
        'y',
        '1'
    ];
    else if (actualValues[0] === false) actualValues = [
        'false',
        'no',
        'n',
        '0',
        ''
    ];
    actualValues = actualValues.map(function(x) {
        return String(x).toLowerCase();
    });
    expectedValues = expectedValues.split(',');
    var intersect = _justIntersect.default(expectedValues, actualValues);
    return intersect.length > 0;
}

},{"@babel/runtime/helpers/interopRequireDefault":"eigyQ","@babel/runtime/helpers/typeof":"1XGzZ","./helpers":"hzVb2","just-intersect":"1yLu5"}],"1yLu5":[function(require,module,exports) {
module.exports = intersect;
/*
  intersect([1, 2, 5, 6], [2, 3, 5, 6]); // [2, 5, 6]
  intersect([1, 2, 2, 4, 5], [3, 2, 2, 5, 7]); // [2, 5]
*/ function intersect(arr1, arr2) {
    if (!Array.isArray(arr1) || !Array.isArray(arr2)) throw new Error('expected both arguments to be arrays');
    var result = [];
    var len = arr1.length;
    for(var i = 0; i < len; i++){
        var elem = arr1[i];
        if (arr2.indexOf(elem) > -1 && result.indexOf(elem) == -1) result.push(elem);
    }
    return result;
}

},{}],"enq3r":[function(require,module,exports) {
"use strict";
var _interopRequireDefault = require("@babel/runtime/helpers/interopRequireDefault");
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.calc_classes = calc_classes;
exports.add_body_classes = add_body_classes;
exports.add_classes_to_body = add_classes_to_body;
var _regenerator = _interopRequireDefault(require("@babel/runtime/regenerator"));
var _asyncToGenerator2 = _interopRequireDefault(require("@babel/runtime/helpers/asyncToGenerator"));
var _html = require("./lib/html");
var _get_info = require("./lookup/get_info");
function calc_classes(record) {
    return {
        country: record.get('country.iso_code'),
        'country-is-in-european-union': record.get('country.is_in_european_union', false),
        continent: record.get('continent.code'),
        province: record.get('most_specific_subdivision.iso_code')
    };
}
function remove_css_classes_by_prefix(el, prefix) {
    var classes = el.className.split(" ").filter(function(c) {
        return !c.startsWith(prefix);
    });
    el.className = classes.join(" ").trim();
}
function add_body_classes() {
    return _add_body_classes.apply(this, arguments);
}
function _add_body_classes() {
    _add_body_classes = _asyncToGenerator2.default(/*#__PURE__*/ _regenerator.default.mark(function _callee() {
        var record;
        return _regenerator.default.wrap(function _callee$(_context) {
            while(true)switch(_context.prev = _context.next){
                case 0:
                    _context.next = 2;
                    return _get_info.get_info();
                case 2:
                    record = _context.sent;
                    if (!record.error()) {
                        _context.next = 6;
                        break;
                    }
                    console.error('Geolocation IP Detection Error (could not add CSS-classes to body): ' + record.error());
                    return _context.abrupt("return");
                case 6:
                    _context.next = 8;
                    return _html.domReady;
                case 8:
                    add_classes_to_body(record);
                case 9:
                case "end":
                    return _context.stop();
            }
        }, _callee);
    }));
    return _add_body_classes.apply(this, arguments);
}
function add_classes_to_body(record) {
    var css_classes = calc_classes(record);
    var body = document.getElementsByTagName('body')[0]; // Remove old classes in case there are any
    remove_css_classes_by_prefix(body, 'geoip-');
    for(var _i = 0, _Object$keys = Object.keys(css_classes); _i < _Object$keys.length; _i++){
        var key = _Object$keys[_i];
        var value = css_classes[key];
        if (value) {
            if (typeof value == 'string') body.classList.add("geoip-".concat(key, "-").concat(value));
            else body.classList.add("geoip-".concat(key));
        }
    }
}

},{"@babel/runtime/helpers/interopRequireDefault":"eigyQ","@babel/runtime/regenerator":"1L3WO","@babel/runtime/helpers/asyncToGenerator":"5j50L","./lib/html":"lmPYU","./lookup/get_info":"eKpYj"}]},["sMOXz","2vQaq"], "2vQaq", "parcelRequire94b4")

//# sourceMappingURL=frontend.js.map
