// modules are defined as an array
// [ module function, map of requires ]
//
// map of requires is short require name -> numeric require
//
// anything defined in a previous bundle is accessed via the
// orig method which is the require for previous bundles

(function (modules, entry, mainEntry, parcelRequireName, globalName) {
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
  newRequire.register = function (id, exports) {
    modules[id] = [
      function (require, module) {
        module.exports = exports;
      },
      {},
    ];
  };

  Object.defineProperty(newRequire, 'root', {
    get: function () {
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
      define(function () {
        return mainExports;
      });

      // <script>
    } else if (globalName) {
      this[globalName] = mainExports;
    }
  }
})({"kr2v4":[function(require,module,exports) {
var HMR_HOST = null;
var HMR_PORT = 1236;
var HMR_SECURE = false;
var HMR_ENV_HASH = "4a236f9275d0a351";
module.bundle.HMR_BUNDLE_ID = "0a94e3aa3cabe4b0";
"use strict";
function _createForOfIteratorHelper(o, allowArrayLike) {
    var it;
    if (typeof Symbol === "undefined" || o[Symbol.iterator] == null) {
        if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") {
            if (it) o = it;
            var i = 0;
            var F = function F() {
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
            if (typeof document !== 'undefined') removeErrorOverlay();
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
                }
            } catch (err) {
                _iterator.e(err);
            } finally{
                _iterator.f();
            }
            if (typeof document !== 'undefined') {
                // Render the fancy html overlay
                removeErrorOverlay();
                var overlay = createErrorOverlay(data.diagnostics.html); // $FlowFixMe
                document.body.appendChild(overlay);
            }
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
            errorHTML += "\n      <div>\n        <div style=\"font-size: 18px; font-weight: bold; margin-top: 20px;\">\n          \uD83D\uDEA8 ".concat(diagnostic.message, "\n        </div>\n        <pre>").concat(stack, "</pre>\n        <div>\n          ").concat(diagnostic.hints.map(function(hint) {
                return '<div>💡 ' + hint + '</div>';
            }).join(''), "\n        </div>\n        ").concat(diagnostic.documentation ? "<div>\uD83D\uDCDD <a style=\"color: violet\" href=\"".concat(diagnostic.documentation, "\" target=\"_blank\">Learn more</a></div>") : '', "\n      </div>\n    ");
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
    if (asset.type === 'css') reloadCSS();
    else if (asset.type === 'js') {
        var deps = asset.depsByBundle[bundle.HMR_BUNDLE_ID];
        if (deps) {
            var fn = new Function('require', 'module', 'exports', asset.output);
            modules[asset.id] = [
                fn,
                deps
            ];
        } else if (bundle.parent) hmrApply(bundle.parent, asset);
    }
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
    if (checkedAssets[id]) return true;
    checkedAssets[id] = true;
    var cached = bundle.cache[id];
    assetsToAccept.push([
        bundle,
        id
    ]);
    if (cached && cached.hot && cached.hot._acceptCallbacks.length) return true;
    var parents = getParents(module.bundle.root, id); // If no parents, the asset is new. Prevent reloading the page.
    if (!parents.length) return true;
    return parents.some(function(v) {
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

},{}],"iEO9d":[function(require,module,exports) {
var _getInfo = require("./lookup/get_info");
var _override = require("./lookup/override");
var _main = require("./main");
// Evaluate shortcodes, body classes, etc.
_main.main();
// Extend window object 
window.geoip_detect.get_info = _getInfo.get_info;
window.geoip_detect.set_override = _override.set_override;
window.geoip_detect.remove_override = _override.remove_override;

},{"./lookup/get_info":"3pqL8","./lookup/override":"1Iyp9","./main":"l5eIm"}],"3pqL8":[function(require,module,exports) {
var parcelHelpers = require("@parcel/transformer-js/src/esmodule-helpers.js");
parcelHelpers.defineInteropFlag(exports);
parcelHelpers.export(exports, "options", ()=>options
);
/**
 * Load the data from the server
 * 
 * (It can also be loaded from the browser localstorage, if the record data is present there already.)
 * 
 * @api
 * @return Promise(Record)
 */ parcelHelpers.export(exports, "get_info", ()=>get_info
);
var _record = require("../models/record");
var _recordDefault = parcelHelpers.interopDefault(_record);
var _xhr = require("../lib/xhr");
var _storage = require("./storage");
const options = window.geoip_detect?.options || {
    ajaxurl: "/wp-admin/admin-ajax.php",
    default_locales: [
        'en'
    ],
    cookie_duration_in_days: 7,
    cookie_name: 'geoip-detect-result',
    do_body_classes: false
};
let ajaxPromise = null;
function get_info_raw() {
    if (!ajaxPromise) {
        // Do Ajax Request only once per page load
        const url = options.ajaxurl + '?action=geoip_detect2_get_info_from_current_ip';
        ajaxPromise = _xhr.makeJSONRequest(url);
        ajaxPromise.then((response)=>{
            if (response?.extra?.error) console.error('Geolocation IP Detection Error: Server returned an error: ' + response.extra.error);
        });
    }
    return ajaxPromise;
}
async function get_info_cached() {
    let response = false;
    let storedResponse = false;
    // 1) Load Info from localstorage cookie cache, if possible
    if (options.cookie_name) {
        storedResponse = _storage.getRecordDataFromLocalStorage();
        if (storedResponse && storedResponse.extra) {
            if (storedResponse.extra.override === true) console.info('Geolocation IP Detection: Using cached response (override)');
            else console.info('Geolocation IP Detection: Using cached response');
            return storedResponse;
        }
    }
    // 2) Get response
    try {
        response = await get_info_raw();
    } catch (err) {
        console.log('Weird: Uncaught error...', err);
        response = err.responseJSON || err;
    }
    // 3) Save info to localstorage cookie cache
    if (options.cookie_name) {
        // Check if Override has been set now
        storedResponse = _storage.getRecordDataFromLocalStorage();
        if (storedResponse?.extra?.override === true) {
            console.info('Geolocation IP Detection: Using cached response (override)');
            return storedResponse;
        }
        let cache_duration = options.cookie_duration_in_days * 86400;
        if (response?.extra?.error) cache_duration = 60; // Cache errors only for 1 minute, then try again
        _storage.setRecordDataToLocalStorage(response, cache_duration);
    }
    return response;
}
async function get_info() {
    let response = await get_info_cached();
    if (typeof response !== 'object') {
        console.error('Geolocation IP Detection Error: Record should be an object, not a ' + typeof response, response);
        response = {
            'extra': {
                'error': response || 'Network error, look at the original server response ...'
            }
        };
    }
    const record = new _recordDefault.default(response, options.default_locales);
    return record;
}

},{"../models/record":"e1CAO","../lib/xhr":"j72SH","@parcel/transformer-js/src/esmodule-helpers.js":"ciiiV","./storage":"4gUzW"}],"e1CAO":[function(require,module,exports) {
var parcelHelpers = require("@parcel/transformer-js/src/esmodule-helpers.js");
parcelHelpers.defineInteropFlag(exports);
parcelHelpers.export(exports, "camelToUnderscore", ()=>camelToUnderscore
);
var _justSafeGet = require("just-safe-get");
var _justSafeGetDefault = parcelHelpers.interopDefault(_justSafeGet);
const _get_localized = function(ret, locales) {
    if (typeof ret === 'object' && ret !== null) {
        if (typeof ret.names === 'object' && typeof locales === 'object') for(let i = 0; i < locales.length; i++){
            let locale = locales[i];
            if (ret.names[locale]) return ret.names[locale];
        }
        if (ret.name) return ret.name;
        return '';
    }
    return ret;
};
const camelToUnderscore = function(key) {
    key = key.split('.').map((x)=>{
        if (typeof x !== 'string' || typeof x[0] !== 'string') return '';
        x = x[0].toLowerCase() + x.slice(1); // to allow "MostSpecificSubdivision"
        x = x.replace(/([A-Z])/g, "_$1").toLowerCase();
        return x;
    }).join('.');
    return key;
};
class Record {
    data = {
    };
    default_locales = [];
    constructor(data, default_locales){
        this.data = data || {
            is_empty: true
        };
        this.default_locales = [
            'en'
        ];
        this.default_locales = this._process_locales(default_locales);
    }
    get(prop, default_value) {
        return this.get_with_locales(prop, null, default_value);
    }
    get_raw(prop1) {
        prop1 = camelToUnderscore(prop1);
        return _justSafeGetDefault.default(this.data, prop1, null);
    }
    has_property(prop2) {
        const ret = this._lookup_with_locales(prop2, this.default_locales, null);
        return ret !== null;
    }
    _lookup_with_locales(prop3, locales, default_value1 = '') {
        locales = this._process_locales(locales);
        // Treat pseudo-property 'name' as if it never existed
        if (prop3.substr(-5) === '.name') prop3 = prop3.substr(0, prop3.length - 5);
        let ret = this.get_raw(prop3);
        // Localize property, if possible
        ret = _get_localized(ret, locales);
        if (ret === null || ret === '') ret = default_value1;
        return ret;
    }
    _process_locales(locales1) {
        if (typeof locales1 === 'string') locales1 = [
            locales1
        ];
        if (!Array.isArray(locales1) || locales1.length === 0) locales1 = this.default_locales;
        return locales1;
    }
    get_with_locales(prop4, locales2, default_value2) {
        const ret = this._lookup_with_locales(prop4, locales2, default_value2);
        if (typeof ret === 'object') console.warn('Geolocation IP Detection: The property "' + prop4 + '" is of type "' + typeof ret + '", should be string or similar', ret);
        if (typeof ret === 'undefined') {
            console.warn('Geolocation IP Detection: The property "' + prop4 + '" is not defined, please check spelling or maybe you need a different data source', {
                data: this.data
            });
            return '';
        }
        return ret;
    }
    get_country_iso() {
        let country = this.get('country.iso_code');
        if (country) country = country.substr(0, 2).toLowerCase();
        return country;
    }
    /**
     * Check if there is information available for this IP
     * @returns boolean 
     */ is_empty() {
        return this.get('is_empty', false);
    }
    /**
     * Get error message, if any
     * @return string Error Message
     */ error() {
        return this.get_raw('extra.error') || '';
    }
    /**
     * Get the raw data of this object
     * @returns object
     */ serialize() {
        return this.data;
    }
}
exports.default = Record;

},{"just-safe-get":"huxJX","@parcel/transformer-js/src/esmodule-helpers.js":"ciiiV"}],"huxJX":[function(require,module,exports) {
var parcelHelpers = require("@parcel/transformer-js/src/esmodule-helpers.js");
parcelHelpers.defineInteropFlag(exports);
parcelHelpers.export(exports, "default", ()=>objectSafeGet
);
var objectSafeGet = get;
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

},{"@parcel/transformer-js/src/esmodule-helpers.js":"ciiiV"}],"ciiiV":[function(require,module,exports) {
exports.interopDefault = function(a) {
    return a && a.__esModule ? a : {
        default: a
    };
};
exports.defineInteropFlag = function(a) {
    Object.defineProperty(a, '__esModule', {
        value: true
    });
};
exports.exportAll = function(source, dest) {
    Object.keys(source).forEach(function(key) {
        if (key === 'default' || key === '__esModule' || dest.hasOwnProperty(key)) return;
        Object.defineProperty(dest, key, {
            enumerable: true,
            get: function() {
                return source[key];
            }
        });
    });
    return dest;
};
exports.export = function(dest, destName, get) {
    Object.defineProperty(dest, destName, {
        enumerable: true,
        get: get
    });
};

},{}],"j72SH":[function(require,module,exports) {
var parcelHelpers = require("@parcel/transformer-js/src/esmodule-helpers.js");
parcelHelpers.defineInteropFlag(exports);
parcelHelpers.export(exports, "makeRequest", ()=>makeRequest
);
parcelHelpers.export(exports, "jsonDecodeIfPossible", ()=>jsonDecodeIfPossible
);
parcelHelpers.export(exports, "makeJSONRequest", ()=>makeJSONRequest
);
const makeRequest = function(url, method = 'GET') {
    // Create the XHR request
    var request = new XMLHttpRequest();
    // Return it as a Promise
    return new Promise(function(resolve, reject) {
        // Setup our listener to process compeleted requests
        request.onreadystatechange = function() {
            // Only run if the request is complete
            if (request.readyState !== 4) return;
            // Process the response
            if (request.status >= 200 && request.status < 300) // If successful
            resolve(request);
            else // If failed
            reject({
                status: request.status,
                statusText: request.statusText,
                request: request
            });
        };
        // Setup our HTTP request
        request.open(method || 'GET', url, true);
        // Send the request
        request.send();
    });
};
const jsonDecodeIfPossible = function(str) {
    try {
        return JSON.parse(str);
    } catch (e) {
        return createErrorObject('Invalid JSON: ' + str);
    }
};
function createErrorObject(errorMsg) {
    return {
        is_empty: true,
        extra: {
            error: errorMsg
        }
    };
}
const makeJSONRequest = async function(url, method = 'GET') {
    try {
        const request = await makeRequest(url, method);
        if (!request.responseText || request.responseText === '0') return createErrorObject('Got an empty response from server. Did you enable AJAX in the options?');
        return jsonDecodeIfPossible(request.responseText);
    } catch (e) {
        return jsonDecodeIfPossible(e.request.responseText);
    }
};

},{"@parcel/transformer-js/src/esmodule-helpers.js":"ciiiV"}],"4gUzW":[function(require,module,exports) {
var parcelHelpers = require("@parcel/transformer-js/src/esmodule-helpers.js");
parcelHelpers.defineInteropFlag(exports);
// Sync function in case it is known that no AJAX will occur
parcelHelpers.export(exports, "getRecordDataFromLocalStorage", ()=>getRecordDataFromLocalStorage
);
parcelHelpers.export(exports, "setRecordDataToLocalStorage", ()=>setRecordDataToLocalStorage
);
parcelHelpers.export(exports, "getRecordDataLastEvaluated", ()=>getRecordDataLastEvaluated
);
parcelHelpers.export(exports, "setRecordDataLastEvaluated", ()=>setRecordDataLastEvaluated
);
parcelHelpers.export(exports, "get_info_stored_locally_record", ()=>get_info_stored_locally_record
);
var _localStorageAccess = require("../lib/localStorageAccess");
var _getInfo = require("./get_info");
var _record = require("../models/record");
var _recordDefault = parcelHelpers.interopDefault(_record);
function getRecordDataFromLocalStorage() {
    return _localStorageAccess.getLocalStorage(_getInfo.options.cookie_name);
}
function setRecordDataToLocalStorage(data, cache_duration) {
    _localStorageAccess.setLocalStorage(_getInfo.options.cookie_name, data, cache_duration);
}
let lastEvaluated = {
};
function getRecordDataLastEvaluated() {
    return lastEvaluated;
}
function setRecordDataLastEvaluated() {
    lastEvaluated = getRecordDataFromLocalStorage();
}
function get_info_stored_locally_record() {
    return new _recordDefault.default(getRecordDataFromLocalStorage(), _getInfo.options.default_locales);
}

},{"../lib/localStorageAccess":"dC7e4","./get_info":"3pqL8","../models/record":"e1CAO","@parcel/transformer-js/src/esmodule-helpers.js":"ciiiV"}],"dC7e4":[function(require,module,exports) {
var parcelHelpers = require("@parcel/transformer-js/src/esmodule-helpers.js");
parcelHelpers.defineInteropFlag(exports);
parcelHelpers.export(exports, "setLocalStorage", ()=>setLocalStorage
);
parcelHelpers.export(exports, "removeLocalStorage", ()=>removeLocalStorage
);
parcelHelpers.export(exports, "getLocalStorage", ()=>getLocalStorage
);
const setLocalStorage = function(variable, value, ttl_sec) {
    var data = {
        value: value,
        expires_at: new Date().getTime() + ttl_sec * 1000 / 1
    };
    localStorage.setItem(variable.toString(), JSON.stringify(data));
};
function removeLocalStorage(variable) {
    localStorage.removeItem(variable);
}
const getLocalStorage = function(variable) {
    let data = null;
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

},{"@parcel/transformer-js/src/esmodule-helpers.js":"ciiiV"}],"1Iyp9":[function(require,module,exports) {
var parcelHelpers = require("@parcel/transformer-js/src/esmodule-helpers.js");
parcelHelpers.defineInteropFlag(exports);
/**
 * Override only one property, leave the other properties as-is.
 * @param {string} property 
 * @param {*} value 
 */ parcelHelpers.export(exports, "set_override_with_merge", ()=>set_override_with_merge
);
/**
 * This functions allows to override the geodetected data manually (e.g. a country selector)
 * 
 * @api
 * @param {*} record 
 * @param {object} options
 *   @param {number} duration_in_days When this override expires (default: 1 week later)
 *   @param {boolean} reevaluate If the shortcodes etc. should be re-evaluated (default: true)
 * @return boolean TRUE if override data changed
 */ parcelHelpers.export(exports, "set_override", ()=>set_override
);
/**
 * Remove the override data.
 * On next page load, the record data will be loaded from the server again.
 * 
 * @return boolean
 */ parcelHelpers.export(exports, "remove_override", ()=>remove_override
);
var _localStorageAccess = require("../lib/localStorageAccess");
var _getInfo = require("./get_info");
var _record = require("../models/record");
var _justSafeSet = require("just-safe-set");
var _justSafeSetDefault = parcelHelpers.interopDefault(_justSafeSet);
var _justSafeGet = require("just-safe-get");
var _justSafeGetDefault = parcelHelpers.interopDefault(_justSafeGet);
var _justCompare = require("just-compare");
var _justCompareDefault = parcelHelpers.interopDefault(_justCompare);
var _main = require("../main");
var _storage = require("./storage");
function processOptions(options) {
    options = options || {
    };
    if (typeof options == 'number') options = {
        'duration_in_days': options
    };
    options.duration_in_days = options.duration_in_days || _getInfo.options.cookie_duration_in_days;
    if (options.duration_in_days < 0) {
        console.warn('Geolocation IP Detection set_override_data() did nothing: A negative duration doesn\'t make sense. If you want to remove the override, use remove_override() instead.');
        return false;
    }
    if (typeof options.reevaluate == 'undefined') options.reevaluate = true;
    return options;
}
function changeRecord(record, property, value) {
    record = record || {
    };
    property = property || '';
    property = _record.camelToUnderscore(property);
    const oldData = _justSafeGetDefault.default(record, property);
    if (typeof oldData == 'object' && typeof oldData.names == 'object') property += '.name';
    if (property.endsWith('.name')) {
        property += 's'; // e.g. country.name -> country.names
        value = {
            'en': value
        };
    }
    _justSafeSetDefault.default(record, property, value);
    return record;
}
function set_override_with_merge(property, value, options) {
    let record = _storage.getRecordDataFromLocalStorage();
    record = changeRecord(record, property, value);
    set_override(record, options);
    console.log("Override is now: ", _storage.getRecordDataFromLocalStorage());
}
function set_override(record, options) {
    options = processOptions(options);
    if (record && typeof record.serialize === 'function') record = record.serialize();
    return set_override_data(record, options);
}
function set_override_data(newData, options) {
    newData = newData || {
    };
    _justSafeSetDefault.default(newData, 'extra.override', true);
    _localStorageAccess.setLocalStorage(_getInfo.options.cookie_name, newData, options.duration_in_days * 86400);
    if (options.reevaluate && !_justCompareDefault.default(newData, _storage.getRecordDataLastEvaluated())) {
        _main.main();
        return true;
    }
    return false;
}
function remove_override(options) {
    options = processOptions(options);
    _localStorageAccess.setLocalStorage(_getInfo.options.cookie_name, {
    }, -1);
    if (options.reevaluate) _main.main();
    return true;
}

},{"../lib/localStorageAccess":"dC7e4","./get_info":"3pqL8","../models/record":"e1CAO","just-safe-set":"9Zrpa","just-safe-get":"huxJX","just-compare":"7pzj3","../main":"l5eIm","@parcel/transformer-js/src/esmodule-helpers.js":"ciiiV","./storage":"4gUzW"}],"9Zrpa":[function(require,module,exports) {
var parcelHelpers = require("@parcel/transformer-js/src/esmodule-helpers.js");
parcelHelpers.defineInteropFlag(exports);
parcelHelpers.export(exports, "default", ()=>objectSafeSet
);
var objectSafeSet = set;
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

},{"@parcel/transformer-js/src/esmodule-helpers.js":"ciiiV"}],"7pzj3":[function(require,module,exports) {
var parcelHelpers = require("@parcel/transformer-js/src/esmodule-helpers.js");
parcelHelpers.defineInteropFlag(exports);
parcelHelpers.export(exports, "default", ()=>collectionCompare
);
var collectionCompare = compare;
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

},{"@parcel/transformer-js/src/esmodule-helpers.js":"ciiiV"}],"l5eIm":[function(require,module,exports) {
var parcelHelpers = require("@parcel/transformer-js/src/esmodule-helpers.js");
parcelHelpers.defineInteropFlag(exports);
parcelHelpers.export(exports, "main", ()=>main
);
var _index = require("./shortcodes/index");
var _bodyClasses = require("./body_classes");
var _getInfo = require("./lookup/get_info");
var _storage = require("./lookup/storage");
let firstCall = true;
function main() {
    console.log('Do Main');
    if (firstCall) {
        _index.do_shortcodes_init();
        firstCall = false;
    }
    if (_getInfo.options.do_body_classes) _bodyClasses.add_body_classes();
    // Do all the shortcodes that are in the HTML. Even if shortcodes is not enabled globally, they might be enabled for a specific shortcode.
    _index.do_shortcodes();
    _storage.setRecordDataLastEvaluated();
}

},{"./shortcodes/index":"cu2FA","./body_classes":"gvfnz","./lookup/get_info":"3pqL8","@parcel/transformer-js/src/esmodule-helpers.js":"ciiiV","./lookup/storage":"4gUzW"}],"cu2FA":[function(require,module,exports) {
var parcelHelpers = require("@parcel/transformer-js/src/esmodule-helpers.js");
parcelHelpers.defineInteropFlag(exports);
parcelHelpers.export(exports, "do_shortcodes_init", ()=>do_shortcodes_init
);
parcelHelpers.export(exports, "do_shortcodes", ()=>do_shortcodes
);
var _html = require("../lib/html");
var _helpers = require("./helpers");
var _normal = require("./normal");
var _onchange = require("./onchange");
var _showIf = require("./show-if");
const do_shortcodes_init = function() {
    _onchange.init();
};
const do_shortcodes = async function do_shortcodes() {
    // Before doing any of these, the DOM tree needs to be loaded
    await _html.domReady;
    // These are called in parallel, as they are async functions
    _helpers.action_on_elements('js-geoip-detect-shortcode', 'could not execute shortcode(s) [geoip_detect2 ...]', _normal.do_shortcode_normal);
    _helpers.action_on_elements('js-geoip-detect-flag', 'could not configure the flag(s)', _normal.do_shortcode_flags);
    _helpers.action_on_elements('js-geoip-text-input', 'could not set the value of the text input field(s)', _normal.do_shortcode_text_input);
    _helpers.action_on_elements('js-geoip-detect-country-select', 'could not set the value of the select field(s)', _normal.do_shortcode_country_select);
    _helpers.action_on_elements('js-geoip-detect-show-if', 'could not execute the show-if/hide-if conditions', _showIf.do_shortcode_show_if);
};

},{"../lib/html":"kn7Dj","./helpers":"8k0KJ","./normal":"fMA9u","./onchange":"aeUJp","./show-if":"1v2ZG","@parcel/transformer-js/src/esmodule-helpers.js":"ciiiV"}],"kn7Dj":[function(require,module,exports) {
var parcelHelpers = require("@parcel/transformer-js/src/esmodule-helpers.js");
parcelHelpers.defineInteropFlag(exports);
parcelHelpers.export(exports, "isUnitTesting", ()=>isUnitTesting
);
parcelHelpers.export(exports, "domReady", ()=>domReady
);
parcelHelpers.export(exports, "selectItemByValue", ()=>selectItemByValue
);
/**
 * @param {*} el                Select Tag
 * @param {string} attributeName     HTML attribute name to search by
 * @param {string} attributeValue    HTML attribute value to search by
 * @returns boolean TRUE if Value found in select tag
 */ parcelHelpers.export(exports, "selectItemByAttribute", ()=>selectItemByAttribute
);
function isUnitTesting() {
    return false;
}
const domReady = new Promise((resolve)=>{
    if (isUnitTesting()) resolve();
    if (document.readyState === "loading") {
        if (document.addEventListener) document.addEventListener('DOMContentLoaded', resolve);
        else document.attachEvent('onreadystatechange', function() {
            if (document.readyState != 'loading') resolve();
        });
    } else resolve();
});
function selectItemByValue(el, value) {
    for(var i = 0; i < el.options.length; i++)if (el.options[i].value === value) {
        el.selectedIndex = i;
        return true;
    }
    return false;
}
function selectItemByAttribute(el, attributeName, attributeValue) {
    for(let i = 0; i < el.options.length; i++)if (el.options[i].getAttribute(attributeName) === attributeValue) {
        el.selectedIndex = i;
        return true;
    }
    return false;
}

},{"@parcel/transformer-js/src/esmodule-helpers.js":"ciiiV"}],"8k0KJ":[function(require,module,exports) {
var parcelHelpers = require("@parcel/transformer-js/src/esmodule-helpers.js");
parcelHelpers.defineInteropFlag(exports);
// Get Options from data-options and json parse them
parcelHelpers.export(exports, "get_options", ()=>get_options
);
parcelHelpers.export(exports, "action_on_elements", ()=>action_on_elements
);
parcelHelpers.export(exports, "get_value_from_record", ()=>get_value_from_record
);
var _getInfo = require("../lookup/get_info");
function get_options(el) {
    const raw = el.getAttribute('data-options');
    try {
        return JSON.parse(raw);
    } catch (e) {
        return {
        };
    }
}
async function action_on_elements(className, errorMessage, callback) {
    const elements = document.getElementsByClassName(className);
    if (!elements.length) return;
    const record = await _getInfo.get_info();
    if (record.error()) {
        console.error('Geolocation IP Detection Error (' + errorMessage + '): ' + record.error());
        return;
    }
    Array.from(elements).forEach((el)=>callback(el, record)
    );
}
function get_value_from_record(el, record, property = null) {
    const opt = get_options(el);
    property = property || opt.property;
    if (opt.skip_cache) console.warn("Geolocation IP Detection: The property 'skip_cache' is ignored in AJAX mode. You could disable the response caching on the server by setting the constant GEOIP_DETECT_READER_CACHE_TIME.");
    return record.get_with_locales(property, opt.lang, opt.default);
}

},{"../lookup/get_info":"3pqL8","@parcel/transformer-js/src/esmodule-helpers.js":"ciiiV"}],"fMA9u":[function(require,module,exports) {
var parcelHelpers = require("@parcel/transformer-js/src/esmodule-helpers.js");
parcelHelpers.defineInteropFlag(exports);
parcelHelpers.export(exports, "do_shortcode_normal", ()=>do_shortcode_normal
);
parcelHelpers.export(exports, "do_shortcode_flags", ()=>do_shortcode_flags
);
parcelHelpers.export(exports, "do_shortcode_country_select", ()=>do_shortcode_country_select
);
parcelHelpers.export(exports, "do_shortcode_text_input", ()=>do_shortcode_text_input
);
var _events = require("../lib/events");
var _html = require("../lib/html");
var _helpers = require("./helpers");
var _onchange = require("./onchange");
function do_shortcode_normal(el, record) {
    el.innerText = _helpers.get_value_from_record(el, record);
}
function do_shortcode_flags(el, record) {
    const country = record.get_country_iso() || _helpers.get_options(el).default;
    if (country) el.classList.add('flag-icon-' + country);
}
function do_shortcode_country_select(el, record) {
    let country = record.get_country_iso();
    if (_html.selectItemByAttribute(el, 'data-c', country)) {
        _events.triggerNativeEvent(el, 'change');
        return;
    }
    // The country is not the list of countries - select empty option instead
    if (_html.selectItemByAttribute(el, 'data-c', '')) _events.triggerNativeEvent(el, 'change');
}
function do_shortcode_text_input(el, record) {
    el.value = _helpers.get_value_from_record(el, record);
    _events.triggerNativeEvent(el, 'change');
}

},{"../lib/events":"hXtBE","../lib/html":"kn7Dj","./helpers":"8k0KJ","./onchange":"aeUJp","@parcel/transformer-js/src/esmodule-helpers.js":"ciiiV"}],"hXtBE":[function(require,module,exports) {
var parcelHelpers = require("@parcel/transformer-js/src/esmodule-helpers.js");
parcelHelpers.defineInteropFlag(exports);
parcelHelpers.export(exports, "isInternalEvent", ()=>isInternalEvent
);
parcelHelpers.export(exports, "triggerNativeEvent", ()=>triggerNativeEvent
);
let _internalEvent = false;
function isInternalEvent() {
    return _internalEvent;
}
function triggerNativeEvent(el, eventName, options = null) {
    _internalEvent = true;
    let event;
    if (window.CustomEvent && typeof window.CustomEvent === 'function') event = new CustomEvent(eventName, {
        detail: options
    });
    else {
        // Compat for IE
        event = document.createEvent('CustomEvent');
        event.initCustomEvent(eventName, true, true, options);
    }
    el.dispatchEvent(event);
    _internalEvent = false;
}

},{"@parcel/transformer-js/src/esmodule-helpers.js":"ciiiV"}],"aeUJp":[function(require,module,exports) {
var parcelHelpers = require("@parcel/transformer-js/src/esmodule-helpers.js");
parcelHelpers.defineInteropFlag(exports);
parcelHelpers.export(exports, "init", ()=>init
);
/**
 * When calling this method, be very careful not to introduce an infinite loop!
 * @param {*} el 
 */ parcelHelpers.export(exports, "autosave_element", ()=>autosave_element
);
var _checkRecursive = require("../lib/check-recursive");
var _events = require("../lib/events");
var _override = require("../lookup/override");
var _helpers = require("./helpers");
function init() {
    document.addEventListener('change', event_listener_autosave_on_change, false);
}
function event_listener_autosave_on_change(event) {
    if (_events.isInternalEvent()) return;
    const target = event.target;
    if (target?.matches && target.matches('.js-geoip-detect-input-autosave')) {
        console.log('autosave on change', target);
        autosave_element(target);
    }
}
function autosave_element(el) {
    const property = _helpers.get_options(el).property;
    const value = el.value;
    if (!_checkRecursive.check_recursive_before()) return;
    if (el.matches('select.js-geoip-detect-country-select')) {
        const selected = el.options[el.selectedIndex];
        const isoCode = selected?.getAttribute('data-c');
        _override.set_override_with_merge('country.iso_code', isoCode.toUpperCase(), {
            reevaluate: false
        });
    }
    _override.set_override_with_merge(property, value, {
        reevaluate: true
    }); // might call do_shortcodes etc.
    _checkRecursive.check_recursive_after();
}

},{"../lib/check-recursive":"g8a79","../lib/events":"hXtBE","../lookup/override":"1Iyp9","./helpers":"8k0KJ","@parcel/transformer-js/src/esmodule-helpers.js":"ciiiV"}],"g8a79":[function(require,module,exports) {
var parcelHelpers = require("@parcel/transformer-js/src/esmodule-helpers.js");
parcelHelpers.defineInteropFlag(exports);
parcelHelpers.export(exports, "check_recursive_before", ()=>check_recursive_before
);
parcelHelpers.export(exports, "check_recursive_after", ()=>check_recursive_after
);
let _listener_active = false; // for recursion detection (maybe remove later)
let _change_counter = 0;
function check_recursive_before() {
    _change_counter++;
    if (_listener_active || _change_counter > 10) {
        console.warn('Error: Thats weird! autosave change detected a recursion (' + _change_counter + ')! Please file a bug report about this and include the first 10 lines of the callstack below:');
        console.trace();
        debugger;
        return false;
    }
    _listener_active = true;
    return true;
}
function check_recursive_after() {
    _listener_active = false;
}

},{"@parcel/transformer-js/src/esmodule-helpers.js":"ciiiV"}],"1v2ZG":[function(require,module,exports) {
var parcelHelpers = require("@parcel/transformer-js/src/esmodule-helpers.js");
parcelHelpers.defineInteropFlag(exports);
parcelHelpers.export(exports, "do_shortcode_show_if", ()=>do_shortcode_show_if
);
parcelHelpers.export(exports, "geoip_detect2_shortcode_evaluate_conditions", ()=>geoip_detect2_shortcode_evaluate_conditions
);
var _helpers = require("./helpers");
var _justIntersect = require("just-intersect");
var _justIntersectDefault = parcelHelpers.interopDefault(_justIntersect);
function do_shortcode_show_if(el, record) {
    const opt = _helpers.get_options(el);
    const evaluated = geoip_detect2_shortcode_evaluate_conditions(opt.parsed, opt, record);
    if (!evaluated) {
        el.style.display = "none";
        el.classList.add('geoip-hidden');
        el.classList.remove('geoip-shown');
    } else {
        el.style.display = '';
        el.classList.remove('geoip-hidden');
        el.classList.add('geoip-shown');
    }
}
function geoip_detect2_shortcode_evaluate_conditions(parsed, opt, record) {
    const alternativePropertyNames = [
        'name',
        'iso_code',
        'iso_code3',
        'code',
        'geoname_id', 
    ];
    let isConditionMatching = parsed.op === 'or' ? false : true;
    parsed.conditions.forEach((c)=>{
        let subConditionMatching = false;
        let values = [];
        const raw_value = record.get_raw(c.p);
        if (raw_value === null) subConditionMatching = false;
        else if (typeof raw_value === 'object') alternativePropertyNames.forEach((name)=>{
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
    actualValues = actualValues.map((x)=>String(x).toLowerCase()
    );
    expectedValues = expectedValues.split(',');
    if (expectedValues.indexOf('') !== -1) {
        if (actualValues.length === 0) return true;
    }
    const intersect = _justIntersectDefault.default(expectedValues, actualValues);
    return intersect.length > 0;
}

},{"./helpers":"8k0KJ","just-intersect":"eqdk9","@parcel/transformer-js/src/esmodule-helpers.js":"ciiiV"}],"eqdk9":[function(require,module,exports) {
var parcelHelpers = require("@parcel/transformer-js/src/esmodule-helpers.js");
parcelHelpers.defineInteropFlag(exports);
parcelHelpers.export(exports, "default", ()=>arrayIntersect
);
var arrayIntersect = intersect;
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

},{"@parcel/transformer-js/src/esmodule-helpers.js":"ciiiV"}],"gvfnz":[function(require,module,exports) {
var parcelHelpers = require("@parcel/transformer-js/src/esmodule-helpers.js");
parcelHelpers.defineInteropFlag(exports);
parcelHelpers.export(exports, "calc_classes", ()=>calc_classes
);
parcelHelpers.export(exports, "add_body_classes", ()=>add_body_classes
);
parcelHelpers.export(exports, "add_classes_to_body", ()=>add_classes_to_body
);
var _html = require("./lib/html");
var _getInfo = require("./lookup/get_info");
function calc_classes(record) {
    return {
        country: record.get('country.iso_code'),
        'country-is-in-european-union': record.get('country.is_in_european_union', false),
        continent: record.get('continent.code'),
        province: record.get('most_specific_subdivision.iso_code')
    };
}
function remove_css_classes_by_prefix(el, prefix) {
    const classes = el.className.split(" ").filter((c)=>!c.startsWith(prefix)
    );
    el.className = classes.join(" ").trim();
}
async function add_body_classes() {
    const record = await _getInfo.get_info();
    if (record.error()) {
        console.error('Geolocation IP Detection Error (could not add CSS-classes to body): ' + record.error());
        return;
    }
    await _html.domReady;
    add_classes_to_body(record);
}
function add_classes_to_body(record) {
    const css_classes = calc_classes(record);
    const body = document.getElementsByTagName('body')[0];
    // Remove old classes in case there are any
    remove_css_classes_by_prefix(body, 'geoip-');
    for (let key of Object.keys(css_classes)){
        const value = css_classes[key];
        if (value) {
            if (typeof value == 'string') body.classList.add(`geoip-${key}-${value}`);
            else body.classList.add(`geoip-${key}`);
        }
    }
}

},{"./lib/html":"kn7Dj","./lookup/get_info":"3pqL8","@parcel/transformer-js/src/esmodule-helpers.js":"ciiiV"}]},["kr2v4","iEO9d"], "iEO9d", "parcelRequire94b4")

//# sourceMappingURL=frontend.js.map
