!function(){function e(e){return e&&e.__esModule?e.default:e}function t(e,t,r,n,o,i,a){try{var c=e[i](a),u=c.value}catch(e){return void r(e)}c.done?t(u):Promise.resolve(u).then(n,o)}function r(e){return function(){var r=this,n=arguments;return new Promise((function(o,i){var a=e.apply(r,n);function c(e){t(a,o,i,c,u,"next",e)}function u(e){t(a,o,i,c,u,"throw",e)}c(void 0)}))}}function n(e,t){for(var r=0;r<t.length;r++){var n=t[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}function o(e,t,r){return t in e?Object.defineProperty(e,t,{value:r,enumerable:!0,configurable:!0,writable:!0}):e[t]=r,e}function i(e){return e&&e.constructor===Symbol?"symbol":typeof e}var a={},c=function(e){var t,r=Object.prototype,n=r.hasOwnProperty,o="function"==typeof Symbol?Symbol:{},i=o.iterator||"@@iterator",a=o.asyncIterator||"@@asyncIterator",c=o.toStringTag||"@@toStringTag";function u(e,t,r){return Object.defineProperty(e,t,{value:r,enumerable:!0,configurable:!0,writable:!0}),e[t]}try{u({},"")}catch(e){u=function(e,t,r){return e[t]=r}}function s(e,t,r,n){var o=t&&t.prototype instanceof y?t:y,i=Object.create(o.prototype),a=new A(n||[]);return i._invoke=function(e,t,r){var n=f;return function(o,i){if(n===p)throw new Error("Generator is already running");if(n===h){if("throw"===o)throw i;return T()}for(r.method=o,r.arg=i;;){var a=r.delegate;if(a){var c=L(a,r);if(c){if(c===v)continue;return c}}if("next"===r.method)r.sent=r._sent=r.arg;else if("throw"===r.method){if(n===f)throw n=h,r.arg;r.dispatchException(r.arg)}else"return"===r.method&&r.abrupt("return",r.arg);n=p;var u=l(e,t,r);if("normal"===u.type){if(n=r.done?h:d,u.arg===v)continue;return{value:u.arg,done:r.done}}"throw"===u.type&&(n=h,r.method="throw",r.arg=u.arg)}}}(e,r,a),i}function l(e,t,r){try{return{type:"normal",arg:e.call(t,r)}}catch(e){return{type:"throw",arg:e}}}e.wrap=s;var f="suspendedStart",d="suspendedYield",p="executing",h="completed",v={};function y(){}function g(){}function m(){}var _={};u(_,i,(function(){return this}));var w=Object.getPrototypeOf,b=w&&w(w(O([])));b&&b!==r&&n.call(b,i)&&(_=b);var x=m.prototype=y.prototype=Object.create(_);function k(e){["next","throw","return"].forEach((function(t){u(e,t,(function(e){return this._invoke(t,e)}))}))}function E(e,t){function r(o,i,a,c){var u=l(e[o],e,i);if("throw"!==u.type){var s=u.arg,f=s.value;return f&&"object"==typeof f&&n.call(f,"__await")?t.resolve(f.__await).then((function(e){r("next",e,a,c)}),(function(e){r("throw",e,a,c)})):t.resolve(f).then((function(e){s.value=e,a(s)}),(function(e){return r("throw",e,a,c)}))}c(u.arg)}var o;this._invoke=function(e,n){function i(){return new t((function(t,o){r(e,n,t,o)}))}return o=o?o.then(i,i):i()}}function L(e,r){var n=e.iterator[r.method];if(n===t){if(r.delegate=null,"throw"===r.method){if(e.iterator.return&&(r.method="return",r.arg=t,L(e,r),"throw"===r.method))return v;r.method="throw",r.arg=new TypeError("The iterator does not provide a 'throw' method")}return v}var o=l(n,e.iterator,r.arg);if("throw"===o.type)return r.method="throw",r.arg=o.arg,r.delegate=null,v;var i=o.arg;return i?i.done?(r[e.resultName]=i.value,r.next=e.nextLoc,"return"!==r.method&&(r.method="next",r.arg=t),r.delegate=null,v):i:(r.method="throw",r.arg=new TypeError("iterator result is not an object"),r.delegate=null,v)}function j(e){var t={tryLoc:e[0]};1 in e&&(t.catchLoc=e[1]),2 in e&&(t.finallyLoc=e[2],t.afterLoc=e[3]),this.tryEntries.push(t)}function S(e){var t=e.completion||{};t.type="normal",delete t.arg,e.completion=t}function A(e){this.tryEntries=[{tryLoc:"root"}],e.forEach(j,this),this.reset(!0)}function O(e){if(e){var r=e[i];if(r)return r.call(e);if("function"==typeof e.next)return e;if(!isNaN(e.length)){var o=-1,a=function r(){for(;++o<e.length;)if(n.call(e,o))return r.value=e[o],r.done=!1,r;return r.value=t,r.done=!0,r};return a.next=a}}return{next:T}}function T(){return{value:t,done:!0}}return g.prototype=m,u(x,"constructor",m),u(m,"constructor",g),g.displayName=u(m,c,"GeneratorFunction"),e.isGeneratorFunction=function(e){var t="function"==typeof e&&e.constructor;return!!t&&(t===g||"GeneratorFunction"===(t.displayName||t.name))},e.mark=function(e){return Object.setPrototypeOf?Object.setPrototypeOf(e,m):(e.__proto__=m,u(e,c,"GeneratorFunction")),e.prototype=Object.create(x),e},e.awrap=function(e){return{__await:e}},k(E.prototype),u(E.prototype,a,(function(){return this})),e.AsyncIterator=E,e.async=function(t,r,n,o,i){void 0===i&&(i=Promise);var a=new E(s(t,r,n,o),i);return e.isGeneratorFunction(r)?a:a.next().then((function(e){return e.done?e.value:a.next()}))},k(x),u(x,c,"Generator"),u(x,i,(function(){return this})),u(x,"toString",(function(){return"[object Generator]"})),e.keys=function(e){var t=[];for(var r in e)t.push(r);return t.reverse(),function r(){for(;t.length;){var n=t.pop();if(n in e)return r.value=n,r.done=!1,r}return r.done=!0,r}},e.values=O,A.prototype={constructor:A,reset:function(e){if(this.prev=0,this.next=0,this.sent=this._sent=t,this.done=!1,this.delegate=null,this.method="next",this.arg=t,this.tryEntries.forEach(S),!e)for(var r in this)"t"===r.charAt(0)&&n.call(this,r)&&!isNaN(+r.slice(1))&&(this[r]=t)},stop:function(){this.done=!0;var e=this.tryEntries[0].completion;if("throw"===e.type)throw e.arg;return this.rval},dispatchException:function(e){if(this.done)throw e;var r=this;function o(n,o){return c.type="throw",c.arg=e,r.next=n,o&&(r.method="next",r.arg=t),!!o}for(var i=this.tryEntries.length-1;i>=0;--i){var a=this.tryEntries[i],c=a.completion;if("root"===a.tryLoc)return o("end");if(a.tryLoc<=this.prev){var u=n.call(a,"catchLoc"),s=n.call(a,"finallyLoc");if(u&&s){if(this.prev<a.catchLoc)return o(a.catchLoc,!0);if(this.prev<a.finallyLoc)return o(a.finallyLoc)}else if(u){if(this.prev<a.catchLoc)return o(a.catchLoc,!0)}else{if(!s)throw new Error("try statement without catch or finally");if(this.prev<a.finallyLoc)return o(a.finallyLoc)}}}},abrupt:function(e,t){for(var r=this.tryEntries.length-1;r>=0;--r){var o=this.tryEntries[r];if(o.tryLoc<=this.prev&&n.call(o,"finallyLoc")&&this.prev<o.finallyLoc){var i=o;break}}i&&("break"===e||"continue"===e)&&i.tryLoc<=t&&t<=i.finallyLoc&&(i=null);var a=i?i.completion:{};return a.type=e,a.arg=t,i?(this.method="next",this.next=i.finallyLoc,v):this.complete(a)},complete:function(e,t){if("throw"===e.type)throw e.arg;return"break"===e.type||"continue"===e.type?this.next=e.arg:"return"===e.type?(this.rval=this.arg=e.arg,this.method="return",this.next="end"):"normal"===e.type&&t&&(this.next=t),v},finish:function(e){for(var t=this.tryEntries.length-1;t>=0;--t){var r=this.tryEntries[t];if(r.finallyLoc===e)return this.complete(r.completion,r.afterLoc),S(r),v}},catch:function(e){for(var t=this.tryEntries.length-1;t>=0;--t){var r=this.tryEntries[t];if(r.tryLoc===e){var n=r.completion;if("throw"===n.type){var o=n.arg;S(r)}return o}}throw new Error("illegal catch attempt")},delegateYield:function(e,r,n){return this.delegate={iterator:O(e),resultName:r,nextLoc:n},"next"===this.method&&(this.arg=t),v}},e}(a);try{regeneratorRuntime=c}catch(e){"object"==typeof globalThis?globalThis.regeneratorRuntime=c:Function("r","regeneratorRuntime = r")(c)}var u=function(e,t,r){if(!e)return r;var n,o;Array.isArray(t)&&(n=t.slice(0));"string"==typeof t&&(n=t.split("."));"symbol"==(void 0===t?"undefined":i(t))&&(n=[t]);if(!Array.isArray(n))throw new Error("props arg must be an array, a string or a symbol");for(;n.length;){if(o=n.shift(),!e)return r;if(void 0===(e=e[o]))return r}return e};var s=function(e,t){if("object"==typeof e&&null!==e){if("object"==typeof e.names&&"object"==typeof t)for(var r=0;r<t.length;r++){var n=t[r];if(e.names[n])return e.names[n]}return e.name?e.name:""}return e},l=function(e){return e=e.split(".").map((function(e){return"string"!=typeof e||"string"!=typeof e[0]?"":e=(e=e[0].toLowerCase()+e.slice(1)).replace(/([A-Z])/g,"_$1").toLowerCase()})).join(".")},f=function(){"use strict";function e(t,r){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e),o(this,"data",{}),o(this,"default_locales",[]),this.data=t||{is_empty:!0},this.default_locales=["en"],this.default_locales=this._process_locales(r)}var t,r,a;return t=e,(r=[{key:"get",value:function(e,t){return this.get_with_locales(e,null,t)}},{key:"get_raw",value:function(e){return e=l(e),u(this.data,e,null)}},{key:"has_property",value:function(e){return null!==this._lookup_with_locales(e,this.default_locales,null)}},{key:"_lookup_with_locales",value:function(e,t){var r=arguments.length>2&&void 0!==arguments[2]?arguments[2]:"";t=this._process_locales(t),".name"===e.substr(-5)&&(e=e.substr(0,e.length-5));var n=this.get_raw(e);return null!==(n=s(n,t))&&""!==n||(n=r),n}},{key:"_process_locales",value:function(e){return"string"==typeof e&&(e=[e]),Array.isArray(e)&&0!==e.length||(e=this.default_locales),e}},{key:"get_with_locales",value:function(e,t,r){var n=this._lookup_with_locales(e,t,r);return"object"==typeof n&&console.warn('Geolocation IP Detection: The property "'+e+'" is of type "'+(void 0===n?"undefined":i(n))+'", should be string or similar',n),void 0===n?(console.warn('Geolocation IP Detection: The property "'+e+'" is not defined, please check spelling or maybe you need a different data source',{data:this.data}),""):n}},{key:"get_country_iso",value:function(){var e=this.get("country.iso_code");return e&&(e=e.substr(0,2).toLowerCase()),e}},{key:"is_empty",value:function(){return this.get("is_empty",!1)}},{key:"error",value:function(){return this.get_raw("extra.error")||""}},{key:"serialize",value:function(){return this.data}}])&&n(t.prototype,r),a&&n(t,a),e}(),d=function(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"GET",r=new XMLHttpRequest;return new Promise((function(n,o){r.onreadystatechange=function(){4===r.readyState&&(r.status>=200&&r.status<300?n(r):o({status:r.status,statusText:r.statusText,request:r}))},r.open(t||"GET",e,!0),r.send()}))},p=function(e){try{return JSON.parse(e)}catch(t){return h("Invalid JSON: "+e)}};function h(e){return{is_empty:!0,extra:{error:e}}}var v,y=(v=r(e(a).mark((function t(r){var n,o,i=arguments;return e(a).wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return n=i.length>1&&void 0!==i[1]?i[1]:"GET",e.prev=1,e.next=4,d(r,n);case 4:if((o=e.sent).responseText&&"0"!==o.responseText){e.next=7;break}return e.abrupt("return",h("Got an empty response from server. Did you enable AJAX in the options?"));case 7:return e.abrupt("return",p(o.responseText));case 10:return e.prev=10,e.t0=e.catch(1),e.abrupt("return",p(e.t0.request.responseText));case 13:case"end":return e.stop()}}),t,null,[[1,10]])}))),function(){return v.apply(this,arguments)}),g=function(e,t,r){var n={value:t,expires_at:(new Date).getTime()+1e3*r/1};localStorage.setItem(e.toString(),JSON.stringify(n))};function m(){return function(e){var t=null;try{t=JSON.parse(localStorage.getItem(e.toString()))}catch(e){return null}if(null!==t){if(!(null!==t.expires_at&&t.expires_at<(new Date).getTime()))return t.value;localStorage.removeItem(e.toString())}return null}(x.cookie_name)}function _(e,t){g(x.cookie_name,e,t)}var w,b={};var x=(null===(w=window.geoip_detect)||void 0===w?void 0:w.options)||{ajaxurl:"/wp-admin/admin-ajax.php",default_locales:["en"],cookie_duration_in_days:7,cookie_name:"geoip-detect-result",do_body_classes:!1},k=null;function E(){if(!k){var e=x.ajaxurl+"?action=geoip_detect2_get_info_from_current_ip";(k=y(e)).then((function(e){var t;(null==e||null===(t=e.extra)||void 0===t?void 0:t.error)&&console.error("Geolocation IP Detection Error: Server returned an error: "+e.extra.error)}))}return k}function L(){return j.apply(this,arguments)}function j(){return(j=r(e(a).mark((function t(){var r,n,o,i,c;return e(a).wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(r=!1,n=!1,!x.cookie_name){e.next=7;break}if(!(n=m())||!n.extra){e.next=7;break}return!0===n.extra.override?console.info("Geolocation IP Detection: Using cached response (override)"):console.info("Geolocation IP Detection: Using cached response"),e.abrupt("return",n);case 7:return e.prev=7,e.next=10,E();case 10:r=e.sent,e.next=17;break;case 13:e.prev=13,e.t0=e.catch(7),console.log("Weird: Uncaught error...",e.t0),r=e.t0.responseJSON||e.t0;case 17:if(!x.cookie_name){e.next=26;break}if(!0!==(null==(n=m())||null===(o=n.extra)||void 0===o?void 0:o.override)){e.next=23;break}return console.info("Geolocation IP Detection: Using cached response (override)"),e.abrupt("return",n);case 23:c=86400*x.cookie_duration_in_days,(null==r||null===(i=r.extra)||void 0===i?void 0:i.error)&&(c=60),_(r,c);case 26:return e.abrupt("return",r);case 27:case"end":return e.stop()}}),t,null,[[7,13]])})))).apply(this,arguments)}function S(){return A.apply(this,arguments)}function A(){return(A=r(e(a).mark((function t(){var r,n;return e(a).wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,L();case 2:return"object"!=typeof(r=e.sent)&&(console.error("Geolocation IP Detection Error: Record should be an object, not a "+(void 0===r?"undefined":i(r)),r),r={extra:{error:r||"Network error, look at the original server response ..."}}),n=new f(r,x.default_locales),e.abrupt("return",n);case 6:case"end":return e.stop()}}),t)})))).apply(this,arguments)}var O=function(e,t,r){var n,o,a;Array.isArray(t)&&(n=t.slice(0));"string"==typeof t&&(n=t.split("."));"symbol"==(void 0===t?"undefined":i(t))&&(n=[t]);if(!Array.isArray(n))throw new Error("props arg must be an array, a string or a symbol");if(!(o=n.pop()))return!1;T(o);for(;a=n.shift();)if(T(a),void 0===e[a]&&(e[a]={}),!(e=e[a])||"object"!=typeof e)return!1;return e[o]=r,!0};function T(e){if("__proto__"===e||"constructor"===e||"prototype"===e)throw new Error("setting of prototype values not supported")}var P=G;function G(e,t){return e===t||(e!=e&&t!=t||{}.toString.call(e)=={}.toString.call(t)&&(e===Object(e)&&(!!e&&(Array.isArray(e)?I(e,t):"[object Set]"=={}.toString.call(e)?I(Array.from(e),Array.from(t)):"[object Object]"=={}.toString.call(e)?function(e,t){var r=Object.keys(e).sort(),n=Object.keys(t).sort(),o=r.length;if(o!=n.length)return!1;for(var i=0;i<o;i++){var a=r[i],c=n[i];if(a!=c||!G(e[a],t[c]))return!1}return!0}(e,t):function(e,t){return e.toString()===t.toString()}(e,t)))))}function I(e,t){var r=e.length;if(r!=t.length)return!1;for(var n=!0,o=0;o<r;o++)if(!G(e[o],t[o])){n=!1;break}return n}var N=new Promise((function(e){"loading"===document.readyState?document.addEventListener?document.addEventListener("DOMContentLoaded",e):document.attachEvent("onreadystatechange",(function(){"loading"!=document.readyState&&e()})):e()}));function C(e,t,r){for(var n=0;n<e.options.length;n++)if(e.options[n].getAttribute(t)===r)return e.selectedIndex=n,!0;return!1}function D(e){var t=e.getAttribute("data-options");try{return JSON.parse(t)}catch(e){return{}}}function J(e,t,r){return R.apply(this,arguments)}function R(){return(R=r(e(a).mark((function t(r,n,o){var i,c;return e(a).wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if((i=document.getElementsByClassName(r)).length){e.next=3;break}return e.abrupt("return");case 3:return e.next=5,S();case 5:if(!(c=e.sent).error()){e.next=9;break}return console.error("Geolocation IP Detection Error ("+n+"): "+c.error()),e.abrupt("return");case 9:Array.from(i).forEach((function(e){return o(e,c)}));case 10:case"end":return e.stop()}}),t)})))).apply(this,arguments)}function F(e,t){var r=arguments.length>2&&void 0!==arguments[2]?arguments[2]:null,n=D(e);return r=r||n.property,n.skip_cache&&console.warn("Geolocation IP Detection: The property 'skip_cache' is ignored in AJAX mode. You could disable the response caching on the server by setting the constant GEOIP_DETECT_READER_CACHE_TIME."),t.get_with_locales(r,n.lang,n.default)}var U=!1;function M(e,t){var r,n=arguments.length>2&&void 0!==arguments[2]?arguments[2]:null;U=!0,window.CustomEvent&&"function"==typeof window.CustomEvent?r=new CustomEvent(t,{detail:n}):(r=document.createEvent("CustomEvent")).initCustomEvent(t,!0,!0,n),e.dispatchEvent(r),U=!1}var q=!1,z=0;function W(e){if(!U){var t=e.target;(null==t?void 0:t.matches)&&t.matches(".js-geoip-detect-input-autosave")&&function(e){var t=D(e).property,r=e.value;if(z++,!(q||z>10?(console.warn("Error: Thats weird! autosave change detected a recursion ("+z+")! Please file a bug report about this and include the first 10 lines of the callstack below:"),console.trace(),0):(q=!0,1)))return;if(e.matches("select.js-geoip-detect-country-select")){var n=e.options[e.selectedIndex];ie("country.iso_code",(null==n?void 0:n.getAttribute("data-c")).toUpperCase(),{reevaluate:!1})}ie(t,r,{reevaluate:!0}),q=!1}(t)}}function X(e,t){e.innerText=F(e,t)}function Y(e,t){var r=t.get_country_iso()||D(e).default;r&&e.classList.add("flag-icon-"+r)}function B(e,t){(C(e,"data-c",t.get_country_iso())||C(e,"data-c",""))&&M(e,"change")}function H(e,t){e.value=F(e,t),M(e,"change")}var Z=function(e,t){if(!Array.isArray(e)||!Array.isArray(t))throw new Error("expected both arguments to be arrays");for(var r=[],n=e.length,o=0;o<n;o++){var i=e[o];t.indexOf(i)>-1&&-1==r.indexOf(i)&&r.push(i)}return r};function $(e,t){var r=D(e);(function(e,t,r){var n=["name","iso_code","iso_code3","code","geoname_id"],o="or"!==e.op;e.conditions.forEach((function(i){var a=!1,c=[],u=r.get_raw(i.p);null===u?a=!1:"object"==typeof u?n.forEach((function(e){u[e]?c.push(u[e]):"name"==e&&c.push(r.get_with_locales(i.p,t.lang))})):c=[u],a=function(e,t){!0===t[0]?t=["true","yes","y","1"]:!1===t[0]&&(t=["false","no","n","0",""]);if(t=t.map((function(e){return String(e).toLowerCase()})),-1!==(e=e.split(",")).indexOf("")&&0===t.length)return!0;return Z(e,t).length>0}(i.v,c),i.not&&(a=!a),o="or"===e.op?o||a:o&&a})),e.not&&(o=!o);return o})(r.parsed,r,t)?(e.style.display="",e.classList.remove("geoip-hidden"),e.classList.add("geoip-shown")):(e.style.display="none",e.classList.add("geoip-hidden"),e.classList.remove("geoip-shown"))}var K,Q=function(){document.addEventListener("change",W,!1)},V=(K=r(e(a).mark((function t(){return e(a).wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,N;case 2:J("js-geoip-detect-shortcode","could not execute shortcode(s) [geoip_detect2 ...]",X),J("js-geoip-detect-flag","could not configure the flag(s)",Y),J("js-geoip-text-input","could not set the value of the text input field(s)",H),J("js-geoip-detect-country-select","could not set the value of the select field(s)",B),J("js-geoip-detect-show-if","could not execute the show-if/hide-if conditions",$);case 7:case"end":return e.stop()}}),t)}))),function(){return K.apply(this,arguments)});function ee(){return(ee=r(e(a).mark((function t(){var r;return e(a).wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,S();case 2:if(!(r=e.sent).error()){e.next=6;break}return console.error("Geolocation IP Detection Error (could not add CSS-classes to body): "+r.error()),e.abrupt("return");case 6:return e.next=8,N;case 8:te(r);case 9:case"end":return e.stop()}}),t)})))).apply(this,arguments)}function te(e){var t,r,n,o=function(e){return{country:e.get("country.iso_code"),"country-is-in-european-union":e.get("country.is_in_european_union",!1),continent:e.get("continent.code"),province:e.get("most_specific_subdivision.iso_code")}}(e),i=document.getElementsByTagName("body")[0];r="geoip-",n=(t=i).className.split(" ").filter((function(e){return!e.startsWith(r)})),t.className=n.join(" ").trim();var a=!0,c=!1,u=void 0;try{for(var s,l=Object.keys(o)[Symbol.iterator]();!(a=(s=l.next()).done);a=!0){var f=s.value,d=o[f];d&&("string"==typeof d?i.classList.add("geoip-".concat(f,"-").concat(d)):i.classList.add("geoip-".concat(f)))}}catch(e){c=!0,u=e}finally{try{a||null==l.return||l.return()}finally{if(c)throw u}}}var re=!0;function ne(){re&&(Q(),re=!1),x.do_body_classes&&function(){ee.apply(this,arguments)}(),V(),b=m()}function oe(e){return"number"==typeof(e=e||{})&&(e={duration_in_days:e}),e.duration_in_days=e.duration_in_days||x.cookie_duration_in_days,e.duration_in_days<0?(console.warn("Geolocation IP Detection set_override_data() did nothing: A negative duration doesn't make sense. If you want to remove the override, use remove_override() instead."),!1):(void 0===e.reevaluate&&(e.reevaluate=!0),e)}function ie(e,t,r){var n=m();ae(n=function(e,t,r){e=e||{},t=l(t=t||"");var n=u(e,t);return"object"==typeof n&&"object"==typeof n.names&&(t+=".name"),t.endsWith(".name")&&(t+="s",r={en:r}),O(e,t,r),e}(n,e,t),r)}function ae(e,t){return t=oe(t),e&&"function"==typeof e.serialize&&(e=e.serialize()),function(e,t){if(O(e=e||{},"extra.override",!0),g(x.cookie_name,e,86400*t.duration_in_days),t.reevaluate&&!P(e,function(){return b}()))return ne(),!0;return!1}(e,t)}ne(),window.geoip_detect.get_info=S,window.geoip_detect.set_override=ae,window.geoip_detect.set_override_with_merge=ie,window.geoip_detect.remove_override=function(e){return e=oe(e),g(x.cookie_name,{},-1),e.reevaluate&&ne(),!0}}();
//# sourceMappingURL=frontend.js.map
