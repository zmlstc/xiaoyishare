(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-member-set-feedback"],{"3b8d":function(t,e,n){"use strict";n.r(e),n.d(e,"default",function(){return a});var i=n("795b"),r=n.n(i);function o(t,e,n,i,o,a,s){try{var u=t[a](s),c=u.value}catch(l){return void n(l)}u.done?e(c):r.a.resolve(c).then(i,o)}function a(t){return function(){var e=this,n=arguments;return new r.a(function(i,r){var a=t.apply(e,n);function s(t){o(a,i,r,s,u,"next",t)}function u(t){o(a,i,r,s,u,"throw",t)}s(void 0)})}}},"5ae4":function(t,e,n){"use strict";var i=n("288e");Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var r=i(n("f499"));n("96cf");var o=i(n("3b8d")),a=n("2f62"),s={data:function(){return{imageList:[],postImageList:[],countIndex:8,count:[1,2,3,4,5,6],input_content:"",logining:!1,lxway:"",selectTxt:"选择反馈类型",list:[],index:-1}},computed:(0,a.mapState)(["hasLogin","userInfo"]),onLoad:function(){if(!this.hasLogin)return uni.navigateTo({url:"../login"}),!1;this.loadData()},methods:{loadData:function(){var t=this;t.$Request.post("member_feedback/class").then(function(e){200==e.code&&(console.log(e),t.list=e.datas.list)})},publish:function(){var t=this;if(this.input_content&&""!=this.input_content)if(this.index<0)uni.showModal({content:"请选择反馈类型",showCancel:!1});else{uni.showLoading({title:"正在处理中..."}),t.logining=!0;var e={feedback:this.input_content,fb_image:this.postImageList,lxway:this.lxway,fbclass:this.list[this.index].f_name,fbclass_id:this.list[this.index].f_id};this.$Request.post("member_feedback/feedback_add",e).then(function(e){200==e.code?(console.log(e),uni.showLoading({title:"提交成功！！"}),setTimeout(function(){uni.navigateBack()},1500)):(uni.showToast({icon:"none",title:e.datas.error,duration:2e3}),uni.hideLoading(),t.logining=!1)})}else uni.showModal({content:"内容不能为空",showCancel:!1})},chooseImage:function(){var t=(0,o.default)(regeneratorRuntime.mark(function t(){var e;return regeneratorRuntime.wrap(function(t){while(1)switch(t.prev=t.next){case 0:if(e=this,6!==this.imageList.length){t.next=3;break}return t.abrupt("return",!1);case 3:uni.chooseImage({sourceType:["camera","album"],count:e.imageList.length+e.count[e.countIndex]>6?6-e.imageList.length:e.count[e.countIndex],success:function(t){uni.showLoading({title:"图片处理中..."}),console.log((0,r.default)(t.tempFilePaths)),e.$Request.upLoadFile("member_feedback/file_upload",t.tempFilePaths[0]).then(function(n){console.log(n),console.log("===upload======"),200==n.code?(console.log("===upload=2====="),uni.hideLoading(),console.log(n),e.imageList=e.imageList.concat(t.tempFilePaths),e.postImageList=e.postImageList.concat(n.datas.file_name)):(uni.showLoading({title:n.datas.error+""}),setTimeout(function(){uni.hideLoading()},1500))})}});case 4:case"end":return t.stop()}},t,this)}));function e(){return t.apply(this,arguments)}return e}(),previewImage:function(t){var e=t.target.dataset.src;uni.previewImage({current:e,urls:this.imageList})},close:function(t){this.imageList.splice(t,1),this.postImageList.splice(t,1)},showWayList:function(t){console.log("选择值为："+t.target.value),this.index=t.target.value,this.selectTxt=this.list[this.index].f_name}}};e.default=s},"5bc6":function(t,e,n){"use strict";var i=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("v-uni-view",[n("v-uni-view",{staticClass:"feedback"},[n("v-uni-view",{staticClass:"title"},[n("v-uni-text",[t._v("*")]),t._v("意见类型")],1),n("v-uni-view",{staticClass:"item bf"},[n("v-uni-picker",{attrs:{value:t.index,range:t.list,"range-key":"f_name"},on:{change:function(e){e=t.$handleEvent(e),t.showWayList(e)}}},[n("v-uni-view",{staticClass:"uni-input"},[t._v(t._s(t.selectTxt))])],1)],1),n("v-uni-view",{staticClass:"title"},[n("v-uni-text",[t._v("*")]),t._v("问题和意见")],1),n("v-uni-textarea",{attrs:{"auto-height":"",placeholder:"请详细描述您的问题和意见..."},model:{value:t.input_content,callback:function(e){t.input_content=e},expression:"input_content"}}),n("v-uni-view",{staticClass:"title"},[t._v("图片上传（选填，提供问题截图，单张不超过2M）")]),n("v-uni-view",[n("v-uni-view",{staticClass:"uni-list list-pd"},[n("v-uni-view",{staticClass:"uni-list-cell cell-pd"},[n("v-uni-view",{staticClass:"uni-uploader"},[n("v-uni-view",{staticClass:"uni-uploader-head"},[n("v-uni-view",{staticClass:"uni-uploader-title"}),n("v-uni-view",{staticClass:"uni-uploader-info"},[t._v(t._s(t.imageList.length)+"/6")])],1),n("v-uni-view",{staticClass:"uni-uploader-body"},[n("v-uni-view",{staticClass:"uni-uploader__files"},[t._l(t.imageList,function(e,i){return[n("v-uni-view",{key:i+"_0",staticClass:"uni-uploader__file",staticStyle:{position:"relative"}},[n("v-uni-image",{staticClass:"uni-uploader__img",attrs:{mode:"aspectFill",src:e,"data-src":e},on:{click:function(e){e=t.$handleEvent(e),t.previewImage(e)}}}),n("v-uni-view",{staticClass:"close-view",on:{click:function(e){e=t.$handleEvent(e),t.close(i)}}},[t._v("×")])],1)]}),n("v-uni-view",{directives:[{name:"show",rawName:"v-show",value:t.imageList.length<6,expression:"imageList.length < 6"}],staticClass:"uni-uploader__input-box"},[n("v-uni-view",{staticClass:"uni-uploader__input",on:{click:function(e){e=t.$handleEvent(e),t.chooseImage(e)}}})],1)],2)],1)],1)],1)],1)],1),n("v-uni-view",{staticClass:"title"},[t._v("联系方式（选填,方便我们联系您）")]),n("v-uni-input",{attrs:{type:"text",placeholder:"手机号码/QQ/电子邮箱","placeholder-class":"pl-class"},model:{value:t.lxway,callback:function(e){t.lxway=e},expression:"lxway"}})],1),n("v-uni-button",{staticClass:"btn mt40 ",attrs:{disabled:t.logining},on:{click:function(e){e=t.$handleEvent(e),t.publish(e)}}},[t._v("提交")]),n("mpvue-picker",{ref:"mpvuePicker",attrs:{themeColor:t.themeColor,mode:t.mode,deepLength:t.deepLength,pickerValueDefault:t.pickerValueDefault,pickerValueArray:t.pickerValueArray},on:{onConfirm2:function(e){e=t.$handleEvent(e),t.onConfirm2(e)},onCancel:function(e){e=t.$handleEvent(e),t.onCancel(e)}}}),n("v-uni-view",{staticClass:"copyright"},[t._v("Copyright © 2016-2019 UP. All Rights Reserved.")])],1)},r=[];n.d(e,"a",function(){return i}),n.d(e,"b",function(){return r})},"73f1":function(t,e,n){"use strict";n.r(e);var i=n("5ae4"),r=n.n(i);for(var o in i)"default"!==o&&function(t){n.d(e,t,function(){return i[t]})}(o);e["default"]=r.a},7817:function(t,e,n){"use strict";n.r(e);var i=n("5bc6"),r=n("73f1");for(var o in r)"default"!==o&&function(t){n.d(e,t,function(){return r[t]})}(o);n("aea7");var a=n("2877"),s=Object(a["a"])(r["default"],i["a"],i["b"],!1,null,"4e896eab",null);e["default"]=s.exports},"7a10":function(t,e,n){e=t.exports=n("2350")(!1),e.push([t.i,"uni-page-body[data-v-4e896eab]{background:#f5f5f5}.cell-pd[data-v-4e896eab]{padding:%?20?% %?30?%}.uni-list[data-v-4e896eab]:before{height:0}.uni-list[data-v-4e896eab]:after{height:0}.list-pd[data-v-4e896eab]{margin-top:0}.close-view[data-v-4e896eab]{text-align:center;line-height:%?30?%;height:%?35?%;width:%?35?%;background:#ef5350;color:#fff;position:absolute;top:%?1?%;right:%?1?%;font-size:%?35?%;border-radius:%?8?%}body.?%PAGE?%[data-v-4e896eab]{background:#f5f5f5}",""])},"96cf":function(t,e){!function(e){"use strict";var n,i=Object.prototype,r=i.hasOwnProperty,o="function"===typeof Symbol?Symbol:{},a=o.iterator||"@@iterator",s=o.asyncIterator||"@@asyncIterator",u=o.toStringTag||"@@toStringTag",c="object"===typeof t,l=e.regeneratorRuntime;if(l)c&&(t.exports=l);else{l=e.regeneratorRuntime=c?t.exports:{},l.wrap=b;var f="suspendedStart",h="suspendedYield",d="executing",v="completed",p={},g={};g[a]=function(){return this};var m=Object.getPrototypeOf,w=m&&m(m(F([])));w&&w!==i&&r.call(w,a)&&(g=w);var y=k.prototype=x.prototype=Object.create(g);L.prototype=y.constructor=k,k.constructor=L,k[u]=L.displayName="GeneratorFunction",l.isGeneratorFunction=function(t){var e="function"===typeof t&&t.constructor;return!!e&&(e===L||"GeneratorFunction"===(e.displayName||e.name))},l.mark=function(t){return Object.setPrototypeOf?Object.setPrototypeOf(t,k):(t.__proto__=k,u in t||(t[u]="GeneratorFunction")),t.prototype=Object.create(y),t},l.awrap=function(t){return{__await:t}},C(E.prototype),E.prototype[s]=function(){return this},l.AsyncIterator=E,l.async=function(t,e,n,i){var r=new E(b(t,e,n,i));return l.isGeneratorFunction(e)?r:r.next().then(function(t){return t.done?t.value:r.next()})},C(y),y[u]="Generator",y[a]=function(){return this},y.toString=function(){return"[object Generator]"},l.keys=function(t){var e=[];for(var n in t)e.push(n);return e.reverse(),function n(){while(e.length){var i=e.pop();if(i in t)return n.value=i,n.done=!1,n}return n.done=!0,n}},l.values=F,T.prototype={constructor:T,reset:function(t){if(this.prev=0,this.next=0,this.sent=this._sent=n,this.done=!1,this.delegate=null,this.method="next",this.arg=n,this.tryEntries.forEach(O),!t)for(var e in this)"t"===e.charAt(0)&&r.call(this,e)&&!isNaN(+e.slice(1))&&(this[e]=n)},stop:function(){this.done=!0;var t=this.tryEntries[0],e=t.completion;if("throw"===e.type)throw e.arg;return this.rval},dispatchException:function(t){if(this.done)throw t;var e=this;function i(i,r){return s.type="throw",s.arg=t,e.next=i,r&&(e.method="next",e.arg=n),!!r}for(var o=this.tryEntries.length-1;o>=0;--o){var a=this.tryEntries[o],s=a.completion;if("root"===a.tryLoc)return i("end");if(a.tryLoc<=this.prev){var u=r.call(a,"catchLoc"),c=r.call(a,"finallyLoc");if(u&&c){if(this.prev<a.catchLoc)return i(a.catchLoc,!0);if(this.prev<a.finallyLoc)return i(a.finallyLoc)}else if(u){if(this.prev<a.catchLoc)return i(a.catchLoc,!0)}else{if(!c)throw new Error("try statement without catch or finally");if(this.prev<a.finallyLoc)return i(a.finallyLoc)}}}},abrupt:function(t,e){for(var n=this.tryEntries.length-1;n>=0;--n){var i=this.tryEntries[n];if(i.tryLoc<=this.prev&&r.call(i,"finallyLoc")&&this.prev<i.finallyLoc){var o=i;break}}o&&("break"===t||"continue"===t)&&o.tryLoc<=e&&e<=o.finallyLoc&&(o=null);var a=o?o.completion:{};return a.type=t,a.arg=e,o?(this.method="next",this.next=o.finallyLoc,p):this.complete(a)},complete:function(t,e){if("throw"===t.type)throw t.arg;return"break"===t.type||"continue"===t.type?this.next=t.arg:"return"===t.type?(this.rval=this.arg=t.arg,this.method="return",this.next="end"):"normal"===t.type&&e&&(this.next=e),p},finish:function(t){for(var e=this.tryEntries.length-1;e>=0;--e){var n=this.tryEntries[e];if(n.finallyLoc===t)return this.complete(n.completion,n.afterLoc),O(n),p}},catch:function(t){for(var e=this.tryEntries.length-1;e>=0;--e){var n=this.tryEntries[e];if(n.tryLoc===t){var i=n.completion;if("throw"===i.type){var r=i.arg;O(n)}return r}}throw new Error("illegal catch attempt")},delegateYield:function(t,e,i){return this.delegate={iterator:F(t),resultName:e,nextLoc:i},"next"===this.method&&(this.arg=n),p}}}function b(t,e,n,i){var r=e&&e.prototype instanceof x?e:x,o=Object.create(r.prototype),a=new T(i||[]);return o._invoke=I(t,n,a),o}function _(t,e,n){try{return{type:"normal",arg:t.call(e,n)}}catch(i){return{type:"throw",arg:i}}}function x(){}function L(){}function k(){}function C(t){["next","throw","return"].forEach(function(e){t[e]=function(t){return this._invoke(e,t)}})}function E(t){function e(n,i,o,a){var s=_(t[n],t,i);if("throw"!==s.type){var u=s.arg,c=u.value;return c&&"object"===typeof c&&r.call(c,"__await")?Promise.resolve(c.__await).then(function(t){e("next",t,o,a)},function(t){e("throw",t,o,a)}):Promise.resolve(c).then(function(t){u.value=t,o(u)},function(t){return e("throw",t,o,a)})}a(s.arg)}var n;function i(t,i){function r(){return new Promise(function(n,r){e(t,i,n,r)})}return n=n?n.then(r,r):r()}this._invoke=i}function I(t,e,n){var i=f;return function(r,o){if(i===d)throw new Error("Generator is already running");if(i===v){if("throw"===r)throw o;return $()}n.method=r,n.arg=o;while(1){var a=n.delegate;if(a){var s=j(a,n);if(s){if(s===p)continue;return s}}if("next"===n.method)n.sent=n._sent=n.arg;else if("throw"===n.method){if(i===f)throw i=v,n.arg;n.dispatchException(n.arg)}else"return"===n.method&&n.abrupt("return",n.arg);i=d;var u=_(t,e,n);if("normal"===u.type){if(i=n.done?v:h,u.arg===p)continue;return{value:u.arg,done:n.done}}"throw"===u.type&&(i=v,n.method="throw",n.arg=u.arg)}}}function j(t,e){var i=t.iterator[e.method];if(i===n){if(e.delegate=null,"throw"===e.method){if(t.iterator.return&&(e.method="return",e.arg=n,j(t,e),"throw"===e.method))return p;e.method="throw",e.arg=new TypeError("The iterator does not provide a 'throw' method")}return p}var r=_(i,t.iterator,e.arg);if("throw"===r.type)return e.method="throw",e.arg=r.arg,e.delegate=null,p;var o=r.arg;return o?o.done?(e[t.resultName]=o.value,e.next=t.nextLoc,"return"!==e.method&&(e.method="next",e.arg=n),e.delegate=null,p):o:(e.method="throw",e.arg=new TypeError("iterator result is not an object"),e.delegate=null,p)}function P(t){var e={tryLoc:t[0]};1 in t&&(e.catchLoc=t[1]),2 in t&&(e.finallyLoc=t[2],e.afterLoc=t[3]),this.tryEntries.push(e)}function O(t){var e=t.completion||{};e.type="normal",delete e.arg,t.completion=e}function T(t){this.tryEntries=[{tryLoc:"root"}],t.forEach(P,this),this.reset(!0)}function F(t){if(t){var e=t[a];if(e)return e.call(t);if("function"===typeof t.next)return t;if(!isNaN(t.length)){var i=-1,o=function e(){while(++i<t.length)if(r.call(t,i))return e.value=t[i],e.done=!1,e;return e.value=n,e.done=!0,e};return o.next=o}}return{next:$}}function $(){return{value:n,done:!0}}}(function(){return this||"object"===typeof self&&self}()||Function("return this")())},aea7:function(t,e,n){"use strict";var i=n("b0ba"),r=n.n(i);r.a},b0ba:function(t,e,n){var i=n("7a10");"string"===typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);var r=n("4f06").default;r("fb7834ac",i,!0,{sourceMap:!1,shadowMode:!1})}}]);