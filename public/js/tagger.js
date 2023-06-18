!function(t,e,n){"function"==typeof define&&define.amd?define([],e):"object"==typeof module&&module.exports?module.exports=e():t.tagger=e()}("undefined"!=typeof window?window:global,function(t){var e,n=(e="innerText"in document.createElement("div")?"innerText":"textContent",function(t){return t[e]});function i(t,e){if(t.length)return Array.from(t).map(function(t){return new i(t,e)});if(!(this instanceof i))return new i(t,e);var n=function t(){if(arguments.length<2)return arguments[0];var e=arguments[0];[].slice.call(arguments).reduce(function(e,n){return a(n)&&Object.keys(n).forEach(function(i){a(n[i])&&a(e[i])?e[i]=t({},e[i],n[i]):e[i]=n[i]}),e});return e}({},i.defaults,e);this.init(t,n)}function a(t){return"object"==typeof t&&null!==t&&"[object Object]"===Object.prototype.toString.call(t)}function l(e,n,i){return e=document.createElement(e),Object.keys(n).forEach(function(t){"style"===t?Object.keys(n.style).forEach(function(t){e.style[t]=n.style[t]}):e.setAttribute(t,n[t])}),i!==t&&i.forEach(function(t){var n;n="string"==typeof t?document.createTextNode(t):l.apply(null,t),e.appendChild(n)}),e}function s(t){return t.replace(/([-\\^$[\]()+{}?*.|])/g,"\\$1")}var o=0;return i.defaults={allow_duplicates:!1,allow_spaces:!0,completion:{list:[],delay:400,min_length:2},tag_limit:-1,add_on_blur:!1,link:function(t){return"/tag/"+t}},i.fn=i.prototype={init:function(t,e){this._id=++o;this._settings=e||{},this._ul=document.createElement("ul"),this._input=t;var n=document.createElement("div");e.wrap?n.className="tagger wrap":n.className="tagger",this._input.setAttribute("hidden","hidden"),this._input.setAttribute("type","hidden");var i=document.createElement("li");i.className="tagger-new",this._new_input_tag=document.createElement("input"),this.tags_from_input(),i.appendChild(this._new_input_tag),this._completion=document.createElement("div"),this._completion.className="tagger-completion",this._ul.appendChild(i),t.parentNode.replaceChild(n,t),n.appendChild(t),n.appendChild(this._ul),i.appendChild(this._completion),this._add_events(),this._settings.completion.list instanceof Array&&this._build_completion(this._settings.completion.list)},_add_events:function(){var t=this;this._ul.addEventListener("click",function(e){e.target.className.match(/close/)&&(t._remove_tag(e.target),e.preventDefault())}),this._settings.add_on_blur&&this._new_input_tag.addEventListener("blur",function(e){t.add_tag(t._new_input_tag.value.trim())&&(t._new_input_tag.value="")}),this._new_input_tag.addEventListener("keydown",function(e){if(13===e.keyCode||188===e.keyCode||32===e.keyCode&&!t._settings.allow_spaces)t.add_tag(t._new_input_tag.value.trim())&&(t._new_input_tag.value=""),e.preventDefault();else if(8!==e.keyCode||t._new_input_tag.value)32===e.keyCode&&(e.ctrlKey||e.metaKey)?("function"==typeof t._settings.completion.list&&t.complete(t._new_input_tag.value),t._toggle_completion(!0),e.preventDefault()):t._tag_limit()&&e.preventDefault();else{if(t._tags.length>0){var n=t._ul.querySelector("li:nth-last-child(2)");t._ul.removeChild(n),t._tags.pop(),t._input.value=t._tags.join(",")}e.preventDefault()}}),this._new_input_tag.addEventListener("input",function(e){var n=t._new_input_tag.value;if(t._tag_selected(n))t.add_tag(n)&&(t._toggle_completion(!1),t._new_input_tag.value="");else{"function"==typeof t._settings.completion.list&&t.complete(n);var i=t._settings.completion.min_length;t._toggle_completion(n.length>=i)}}),this._completion.addEventListener("click",function(e){"a"===e.target.tagName.toLowerCase()&&(t.add_tag(n(e.target)),t._new_input_tag.value="",t._completion.innerHTML="")})},_tag_selected:function(t){if(this._last_completion&&this._last_completion.includes(t)){var e=new RegExp("^"+s(t));return 1===this._last_completion.filter(function(t){return e.test(t)}).length}return!1},_toggle_completion:function(t){t?this._new_input_tag.setAttribute("list","tagger-completion-"+this._id):this._new_input_tag.removeAttribute("list")},_build_completion:function(t){if(this._completion.innerHTML="",this._last_completion=t,t.length){var e=l("datalist",{id:"tagger-completion-"+this._id},t.map(function(t){return["option",{},[t]]}));this._completion.appendChild(e)}},complete:function(t){if(this._settings.completion){var e=this._settings.completion.list;if("function"==typeof e){var n=e(t);n&&"function"==typeof n.then?n.then(this._build_completion.bind(this)):n instanceof Array&&this._build_completion(n)}else this._build_completion(e)}},tags_from_input:function(){this._tags=this._input.value.split(/\s*,\s*/).filter(Boolean),this._tags.forEach(this._new_tag.bind(this))},_new_tag:function(t){var e,n=["a",{href:"#",class:"close"},["×"]],i=["span",{class:"label"},[t]],a=this._settings.link(t);!1===a?e=l("li",{},[["span",{},[i,n]]]):e=l("li",{},[["a",{href:a,target:"_black"},[i,n]]]);this._ul.insertBefore(e,this._new_input_tag.parentNode)},_tag_limit:function(){return this._settings.tag_limit>0&&this._tags.length>=this._settings.tag_limit},add_tag:function(t){return!(!this._settings.allow_duplicates&&-1!==this._tags.indexOf(t))&&(!this._tag_limit()&&(!this.is_empty(t)&&(this._new_tag(t),this._tags.push(t),this._input.value=this._tags.join(","),!0)))},is_empty:function(e){switch(e){case"":case'""':case"''":case"``":case t:case null:return!0;default:return!1}},remove_tag:function(t,e=!0){if(this._tags=this._tags.filter(function(e){return t!==e}),this._input.value=this._tags.join(","),e){var n=Array.from(this._ul.querySelectorAll(".label")),i=new RegExp("^s*"+s(t)+"s*$"),a=n.find(function(t){return t.innerText.match(i)});if(!a)return!1;var l=a.closest("li");return this._ul.removeChild(l),!0}},_remove_tag:function(t){var e=t.closest("li"),n=e.querySelector(".label").innerText;this._ul.removeChild(e),this.remove_tag(n,!1)}},i});