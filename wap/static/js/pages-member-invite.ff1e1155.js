(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-member-invite"],{"2c0a":function(n,e,t){e=n.exports=t("2350")(!1),e.push([n.i,"uni-page-body[data-v-65711990]{background:#f5f5f5}.comment .left-nav[data-v-65711990]{background-color:#fff;color:#00baad}.comment .active[data-v-65711990]{background-color:#00baad;color:#fff}body.?%PAGE?%[data-v-65711990]{background:#f5f5f5}",""])},"46c2":function(n,e,t){var o=t("2c0a");"string"===typeof o&&(o=[[n.i,o,""]]),o.locals&&(n.exports=o.locals);var i=t("4f06").default;i("0fbd8b3c",o,!0,{sourceMap:!1,shadowMode:!1})},5809:function(n,e,t){"use strict";var o=t("46c2"),i=t.n(o);i.a},8582:function(n,e,t){"use strict";t.r(e);var o=t("b79e"),i=t.n(o);for(var a in o)"default"!==a&&function(n){t.d(e,n,function(){return o[n]})}(a);e["default"]=i.a},a533:function(n,e,t){"use strict";t.r(e);var o=t("bddb"),i=t("8582");for(var a in i)"default"!==a&&function(n){t.d(e,n,function(){return i[n]})}(a);t("5809");var s=t("2877"),r=Object(s["a"])(i["default"],o["a"],o["b"],!1,null,"65711990",null);e["default"]=r.exports},b79e:function(n,e,t){"use strict";var o=t("288e");Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0,t("96cf");var i=o(t("3b8d")),a=o(t("3ce9")),s=t("2f62"),r={data:function(){return{memberInfo:[],isShop:!1}},computed:(0,s.mapState)(["hasLogin","userInfo"]),components:{tkiQrcode:a.default},onLoad:function(n){console.log("页面加载"),console.log(n),"shop"==n.t&&(this.isShop=!0),this.loadData()},methods:{loadData:function(){var n=(0,i.default)(regeneratorRuntime.mark(function n(){var e=this;return regeneratorRuntime.wrap(function(n){while(1)switch(n.prev=n.next){case 0:console.log("===userInfo==="+this.hasLogin),console.log(this.userInfo),console.log(this.userInfo.token),this.hasLogin?this.$Request.post("member_invite").then(function(n){200==n.code?(console.log(n),1!=n.datas.is_realverify?(console.log("请先实名验证！"),uni.showToast({icon:"none",title:"请先实名验证！",duration:3e3,success:function(){uni.redirectTo({url:"set/realverify"})}})):e.memberInfo=n.datas.member_info):(uni.showToast({icon:"none",title:n.datas.error,duration:2e3}),uni.navigateTo({url:"login"}))}):uni.navigateTo({url:"login"});case 4:case"end":return n.stop()}},n,this)}));function e(){return n.apply(this,arguments)}return e}(),toPage:function(n){uni.navigateTo({url:n})},showShop:function(){this.isShop=!0},showUser:function(){this.isShop=!1},qrR:function(n){}}};e.default=r},bddb:function(n,e,t){"use strict";var o=function(){var n=this,e=n.$createElement,t=n._self._c||e;return t("v-uni-view",[t("v-uni-view",{staticClass:"comment"},[t("v-uni-view",{class:["left-nav",n.isShop?"active":""],on:{click:function(e){e=n.$handleEvent(e),n.showShop(e)}}},[n._v("商家")]),t("v-uni-view",{class:["right-nav",n.isShop?"":"active"],on:{click:function(e){e=n.$handleEvent(e),n.showUser(e)}}},[n._v("会员")])],1),t("v-uni-view",{staticClass:"qrcode mt20"},[t("v-uni-view",{staticClass:"qr-info"},[t("v-uni-image",{staticClass:"avatar",attrs:{src:n.memberInfo.avator}}),t("v-uni-view",{staticClass:"name ml10 clip"},[n._v(n._s(n.memberInfo.nickname))]),t("v-uni-button",{staticClass:"share",attrs:{bindtap:"sumbit","open-type":"share"}})],1),t("v-uni-view",{staticClass:"image"},[n.isShop?n._e():t("tki-qrcode",{ref:"qrcode",attrs:{val:n.memberInfo.user_url,size:"450",unit:"upx",iconSize:"40",lv:"3",onval:"3",loadMake:!0,showLoading:!1,usingComponents:!0},on:{result:function(e){e=n.$handleEvent(e),n.qrR(e)}}}),n.isShop?t("tki-qrcode",{ref:"qrcode",attrs:{val:n.memberInfo.store_url,size:"450",unit:"upx",iconSize:"40",lv:"3",onval:"3",loadMake:!0,showLoading:!1,usingComponents:!0},on:{result:function(e){e=n.$handleEvent(e),n.qrR(e)}}}):n._e()],1),t("v-uni-view",{staticClass:"title"},[n._v("邀请码："),t("v-uni-text",[n._v(n._s(n.memberInfo.invite_bm))])],1)],1),n.isShop?t("v-uni-view",{staticClass:"btn mt40 mb10",on:{click:function(e){e=n.$handleEvent(e),n.toPage("invitestore")}}},[n._v("查看已邀请商家")]):n._e(),n.isShop?n._e():t("v-uni-view",{staticClass:"btn mt40 mb10",on:{click:function(e){e=n.$handleEvent(e),n.toPage("invitemember")}}},[n._v("查看已邀请会员")])],1)},i=[];t.d(e,"a",function(){return o}),t.d(e,"b",function(){return i})}}]);