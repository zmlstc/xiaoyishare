(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-member-points-pshop"],{"83a9":function(t,e,i){"use strict";var n=i("9e33"),s=i.n(n);s.a},"8dc2":function(t,e,i){"use strict";i.r(e);var n=i("dd23"),s=i("a0f4");for(var a in s)"default"!==a&&function(t){i.d(e,t,function(){return s[t]})}(a);i("83a9");var o=i("2877"),l=Object(o["a"])(s["default"],n["a"],n["b"],!1,null,"8c191974",null);e["default"]=l.exports},"9e33":function(t,e,i){var n=i("b880");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var s=i("4f06").default;s("9bc92cc2",n,!0,{sourceMap:!1,shadowMode:!1})},a0f4:function(t,e,i){"use strict";i.r(e);var n=i("eb47"),s=i.n(n);for(var a in n)"default"!==a&&function(t){i.d(e,t,function(){return n[t]})}(a);e["default"]=s.a},b880:function(t,e,i){e=t.exports=i("2350")(!1),e.push([t.i,'@charset "UTF-8";\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\n/* 颜色变量 */\n/* 行为相关颜色 */\n/* 文字基本颜色 */\n/* 背景颜色 */\n/* 边框颜色 */\n/* 尺寸变量 */\n/* 文字尺寸 */\n/* 图片尺寸 */\n/* Border Radius */\n/* 水平间距 */\n/* 垂直间距 */\n/* 透明度 */\n/* 文章场景相关 */\n/* 页面左右间距 */\n/* 文字尺寸 */\n/*文字颜色*/\n/* 边框颜色 */\n/* 图片加载中颜色 */\n/* 行为相关颜色 */.loading-text[data-v-8c191974]{width:100%;display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;-webkit-box-pack:center;-webkit-justify-content:center;-ms-flex-pack:center;justify-content:center;-webkit-box-align:center;-webkit-align-items:center;-ms-flex-align:center;align-items:center;height:%?60?%;color:#979797;font-size:%?24?%}.addr[data-v-8c191974]{max-width:%?180?%;height:%?48?%;-webkit-flex-shrink:0;-ms-flex-negative:0;flex-shrink:0;display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;-webkit-box-align:center;-webkit-align-items:center;-ms-flex-align:center;align-items:center;padding:%?8?% 0;float:left;overflow:hidden;font-size:%?28?%;color:#666}.addr .icon[data-v-8c191974]{width:%?32?%;height:%?32?%;margin-right:%?5?%;background:url(https://www.fhlego.com/static/location.png) 50% no-repeat;background-size:100%}.addr uni-text[data-v-8c191974]{display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;height:%?48?%;font-size:%?28?%;overflow:hidden}',""])},dd23:function(t,e,i){"use strict";var n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",[i("v-uni-view",{staticClass:"column4"},[i("v-uni-view",{staticClass:"item"},[i("v-uni-view",{staticClass:"title"},[t._v("可用积分")]),i("v-uni-text",[t._v(t._s(t.memberpoints))])],1),i("v-uni-view",{staticClass:"item2",on:{click:function(e){e=t.$handleEvent(e),t.toPage("pointorder")}}},[i("v-uni-view",{staticClass:"my"}),i("v-uni-text",[t._v("我的兑换")])],1),i("v-uni-view",{staticClass:"item2",on:{click:function(e){e=t.$handleEvent(e),t.toPage("pointslog")}}},[i("v-uni-view",{staticClass:"list"}),i("v-uni-text",[t._v("积分明细")])],1),i("v-uni-view",{staticClass:"item2"},[i("v-uni-view",{staticClass:"rule"}),i("v-uni-text",[t._v("积分政策")])],1)],1),i("v-uni-view",{staticClass:"action-bar t-line b-line"},[i("v-uni-view",{staticClass:"addr",on:{click:function(e){e=t.$handleEvent(e),t.toCity(e)}}},[i("v-uni-view",{staticClass:"icon"}),i("v-uni-text",[t._v(t._s(t.city))])],1),i("v-uni-view",{staticStyle:{width:"73%",float:"right"}},[i("sl-filter",{attrs:{independence:!0,color:t.titleColor,themeColor:t.themeColor,menuList:t.menuList},on:{"update:menuList":function(e){e=t.$handleEvent(e),t.menuList=e},"update:menu-list":function(e){e=t.$handleEvent(e),t.menuList=e},result:function(e){e=t.$handleEvent(e),t.result(e)}}})],1)],1),t._l(t.datalist,function(e,n){return i("v-uni-view",{key:n,staticClass:"show-box b-line"},[i("v-uni-view",{staticClass:"box-left"},[i("v-uni-image",{staticClass:"image",attrs:{src:e.pgoods_image,mode:"aspectFill"}}),i("v-uni-view",{staticClass:"text-box"},[i("v-uni-view",{staticClass:"h3 clip-2"},[t._v(t._s(e.pgoods_name))]),i("v-uni-view",{staticClass:"shop-info"},[i("v-uni-view",{staticClass:"icon"}),i("v-uni-text",[t._v(t._s(e.store_name))])],1),i("v-uni-view",{staticClass:"deal"},[i("v-uni-view",{staticClass:"integral fl"},[i("v-uni-view",{staticClass:"icon"}),t._v(t._s(e.pgoods_points))],1),i("v-uni-view",{staticClass:"money fl ml10"},[i("v-uni-view",{staticClass:"icon"}),t._v(t._s(e.pgoods_price))],1)],1)],1)],1),i("v-uni-view",{staticClass:"box-right"},[i("v-uni-view",{staticClass:"date"},[i("v-uni-text",[t._v(t._s(e.pgoods_salenum))]),t._v("/"+t._s(e.pgoods_storage))],1),i("v-uni-view",{staticClass:"dh mt10",on:{click:function(i){i=t.$handleEvent(i),t.toPage("pinfo?id="+e.pgoods_id)}}},[t._v("立即兑换")])],1)],1)}),i("v-uni-view",{staticClass:"loading-text"},[t._v(t._s(t.loadingText))])],2)},s=[];i.d(e,"a",function(){return n}),i.d(e,"b",function(){return s})},eb47:function(t,e,i){"use strict";var n=i("288e");Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0,i("96cf");var s=n(i("3b8d"));i("55dd");var a=n(i("a4bb")),o=n(i("f499")),l=n(i("88ff")),r=i("2f62"),c={data:function(){return{datalist:[],loadingText:"正在加载...",hasmore:!0,curpage:1,predepoit:"",memberpoints:"--",city:"定位中",themeColor:"#000000",titleColor:"#666666",menuList:[{title:"排序",key:"sort",isSort:!0,reflexTitle:!0,detailList:[{title:"默认排序",value:"0"},{title:"按人气高至低",value:"hot"},{title:"按价值高至低",value:"pointsdesc"},{title:"按结束时间降序",value:"etime"}]},{title:"兑换",key:"isable",isSort:!0,reflexTitle:!0,detailList:[{title:"全部",value:"0"},{title:"我能兑换",value:"1"}]}],bysort:"",isable:0}},components:{slFilter:l.default},computed:(0,r.mapState)(["hasLogin","userInfo","Location"]),onLoad:function(){var t=this;t.city=t.Location.city_name,console.log("页面加载"),t.hasLogin&&t.$Request.post("pointprod/getmemberinfo").then(function(e){200==e.code&&(console.log(e),t.memberpoints=e.datas.member.member_points)}),t.loadData()},onReachBottom:function(){console.log("正在加载中..."),this.curpage++,this.loadData()},onPageScroll:function(){console.log("页面滚动...")},onPullDownRefresh:function(){console.log("上拉刷新..."),uni.stopPullDownRefresh()},methods:{result:function(t){console.log(t);var e=JSON.parse((0,o.default)(t)),i=(0,a.default)(e);console.log(i),"isable"==i[0]?e["isable"]!=this.isable&&(this.isable=e["isable"],this.clearData(),this.loadData()):"sort"==i[0]&&e["sort"]!=this.bysort&&(this.bysort=e["sort"],this.clearData(),this.loadData())},clearData:function(){this.datalist=[],this.loadingText="正在加载...",this.hasmore=!0,this.curpage=1},loadData:function(){var t=(0,s.default)(regeneratorRuntime.mark(function t(){var e,i;return regeneratorRuntime.wrap(function(t){while(1)switch(t.prev=t.next){case 0:if(e=this,console.log("===userInfo==="+this.hasLogin),console.log(this.userInfo),console.log(this.userInfo.token),this.hasmore){t.next=7;break}return this.loadingText="到底了",t.abrupt("return",!1);case 7:i={curpage:e.curpage,cityid:e.Location.city_id,isable:e.isable,orderby:e.bysort},e.$Request.post("pointprod/index",i).then(function(t){if(200==t.code){console.log(t),e.hasmore=t.hasmore,console.log(e.hasmore);for(var i=t.datas.list,n=0;n<i.length;n++)e.datalist.push(i[n]);if(!t.hasmore)return e.loadingText="到底了",!1}else uni.showToast({icon:"none",title:t.datas.error,duration:2e3}),uni.navigateTo({url:"login"})});case 9:case"end":return t.stop()}},t,this)}));function e(){return t.apply(this,arguments)}return e}(),toPage:function(t){this.hasLogin||(t="/pages/member/login"),uni.navigateTo({url:t})},toCity:function(){uni.navigateTo({url:"/pages/index/city?url=/pages/member/points/pshop"})}}};e.default=c}}]);