(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-member-register-shopreg3"],{"4b51":function(e,t,i){"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var n={data:function(){return{store_name:"",seller_name:"",pwd:"",pwd2:"",tky:""}},onLoad:function(e){console.log("页面加载"),this.tky=e.tk},methods:{checkseller_name:function(e){var t=e.detail.value;if(console.log(t),t.length<6||t.length>20)return uni.showToast({title:"登陆名为6-20位字符",icon:"none"}),!1;this.$Request.post("store_joinin/check_seller_name",{seller_name:t,type:1}).then(function(e){200==e.code?console.log(e):uni.showToast({icon:"none",title:e.datas.error,duration:2e3})})},checkpwd1:function(e){var t=e.detail.value;if(!/^(?=.*?[a-z)(?=.*>[A-Z])(?=.*?[0-9])[a-zA_Z0-9]{6,10}$/.test(t))return uni.showToast({title:"密码必须大于6位包含数字和字母",icon:"none"}),!1},checkpwd2:function(e){if(this.pwd!=this.pwd2)return uni.showToast({title:"两次输入的密码不一致",icon:"none"}),!1},shopReg:function(){if(this.seller_name.length<6||this.seller_name.length>20)return uni.showToast({title:"登陆名为6-20位字符",icon:"none"}),!1;if(this.store_name.length<5)return uni.showToast({title:"店铺名称不能为空",icon:"none"}),!1;if(!/^(?=.*?[a-z)(?=.*>[A-Z])(?=.*?[0-9])[a-zA_Z0-9]{6,10}$/.test(this.pwd))return uni.showToast({title:"密码必须大于6位包含数字和字母",icon:"none"}),!1;if(this.pwd!=this.pwd2)return uni.showToast({title:"两次输入的密码不一致",icon:"none"}),!1;var e={seller_name:this.seller_name,store_name:this.store_name,type:1,seller_pwd:this.pwd,tky:this.tky};this.$Request.post("store_joinin/step4",e).then(function(e){200==e.code?(console.log(e),uni.showToast({icon:"none",title:"已完成店铺入驻申请，请等待平台审核！",duration:3e3,success:function(){setTimeout(function(){uni.switchTab({url:"/pages/index/index"})},2e3)}})):uni.showToast({icon:"none",title:e.datas.error,duration:2e3})})}}};t.default=n},"7bf8":function(e,t,i){"use strict";var n=function(){var e=this,t=e.$createElement,i=e._self._c||t;return i("v-uni-view",{staticClass:"mian"},[i("v-uni-view",{staticClass:"welcome mb40"},[i("v-uni-view",{staticClass:"title"},[e._v("欢迎入驻小易共享平台")]),i("v-uni-view",{staticClass:"text"},[e._v("花一分钟完成注册")])],1),i("v-uni-view",{staticClass:"nav-reg b-line"},[i("v-uni-view",{staticClass:"item5"},[e._v("店铺名称")]),i("v-uni-view",{staticClass:"item6"},[i("v-uni-input",{attrs:{placeholder:"填写店铺的名称","placeholder-class":"pl-class",maxlength:"50"},model:{value:e.store_name,callback:function(t){e.store_name=t},expression:"store_name"}})],1)],1),i("v-uni-view",{staticClass:"nav-reg b-line"},[i("v-uni-view",{staticClass:"item5"},[e._v("用户名")]),i("v-uni-view",{staticClass:"item6"},[i("v-uni-input",{attrs:{placeholder:"填写商家登录用户名","placeholder-class":"pl-class",maxlength:"20",minlength:"6"},on:{blur:function(t){t=e.$handleEvent(t),e.checkseller_name(t)}},model:{value:e.seller_name,callback:function(t){e.seller_name=t},expression:"seller_name"}})],1)],1),i("v-uni-view",{staticClass:"nav-reg b-line"},[i("v-uni-view",{staticClass:"item5"},[e._v("密码")]),i("v-uni-view",{staticClass:"item6"},[i("v-uni-input",{staticClass:"input-text",attrs:{password:"",placeholder:"填写商家登录密码","placeholder-class":"pl-class",maxlength:"20"},on:{blur:function(t){t=e.$handleEvent(t),e.checkpwd1(t)}},model:{value:e.pwd,callback:function(t){e.pwd=t},expression:"pwd"}})],1)],1),i("v-uni-view",{staticClass:"nav-reg b-line"},[i("v-uni-view",{staticClass:"item5"},[e._v("确认密码")]),i("v-uni-view",{staticClass:"item6"},[i("v-uni-input",{staticClass:"input-text",attrs:{password:"",placeholder:"重复确认密码","placeholder-class":"pl-class",maxlength:"20"},on:{blur:function(t){t=e.$handleEvent(t),e.checkpwd2(t)}},model:{value:e.pwd2,callback:function(t){e.pwd2=t},expression:"pwd2"}})],1)],1),i("v-uni-view",{staticClass:"btn mt40 mb20",on:{click:function(t){t=e.$handleEvent(t),e.shopReg(t)}}},[e._v("完成入驻")])],1)},a=[];i.d(t,"a",function(){return n}),i.d(t,"b",function(){return a})},81075:function(e,t,i){"use strict";var n=i("8b24"),a=i.n(n);a.a},"8b24":function(e,t,i){var n=i("8c3c");"string"===typeof n&&(n=[[e.i,n,""]]),n.locals&&(e.exports=n.locals);var a=i("4f06").default;a("52f813de",n,!0,{sourceMap:!1,shadowMode:!1})},"8b74":function(e,t,i){"use strict";i.r(t);var n=i("7bf8"),a=i("ba46");for(var o in a)"default"!==o&&function(e){i.d(t,e,function(){return a[e]})}(o);i("81075");var l=i("2877"),s=Object(l["a"])(a["default"],n["a"],n["b"],!1,null,"704ed9e0",null);t["default"]=s.exports},"8c3c":function(e,t,i){t=e.exports=i("2350")(!1),t.push([e.i,".welcome[data-v-704ed9e0]{height:%?92?%;text-align:right;width:90%;padding:0 5%}.welcome .title[data-v-704ed9e0]{line-height:%?56?%;font-size:%?38?%}.welcome .text[data-v-704ed9e0]{line-height:%?36?%;color:#999}.agree[data-v-704ed9e0]{height:%?48?%;width:80%;padding:0 10%;font-size:%?24?%;color:#999}.agree uni-text[data-v-704ed9e0]{color:#00baad}.nav-reg[data-v-704ed9e0]{width:90%;margin:0 5%;height:%?98?%;font-size:%?34?%}\r\n/*高度48px栏目条*/.nav-reg uni-input[data-v-704ed9e0]{width:100%;height:%?98?%;font-size:%?28?%;color:#666}.nav-reg .item[data-v-704ed9e0]{width:22%;line-height:%?98?%;float:left;text-align:right}.nav-reg .item .acc[data-v-704ed9e0]{float:right;width:%?58?%;height:%?98?%;background:url(https://www.fhlego.com/static/login-0.png) 50% no-repeat;background-size:80%}.nav-reg .item .pass[data-v-704ed9e0]{float:right;width:%?58?%;height:%?98?%;background:url(https://www.fhlego.com/static/login-2.png) 50% no-repeat;background-size:80%}.nav-reg .item .phone[data-v-704ed9e0]{float:right;width:%?58?%;height:%?98?%;background:url(https://www.fhlego.com/static/login-1.png) 50% no-repeat;background-size:80%}.nav-reg .item .qr[data-v-704ed9e0]{float:right;width:%?58?%;height:%?98?%;background:url(https://www.fhlego.com/static/login-5.png) 50% no-repeat;background-size:80%}.nav-reg .item .yq[data-v-704ed9e0]{float:right;width:%?58?%;height:%?98?%;background:url(https://www.fhlego.com/static/login-4.png) 50% no-repeat;background-size:80%}.nav-reg .item .yz[data-v-704ed9e0]{float:right;width:%?58?%;height:%?98?%;background:url(https://www.fhlego.com/static/login-3.png) 50% no-repeat;background-size:80%}.nav-reg .item2[data-v-704ed9e0]{width:55%;line-height:%?98?%;float:left;color:#999;font-size:%?30?%;margin-left:%?20?%}.nav-reg .item3[data-v-704ed9e0]{width:40%;line-height:%?98?%;float:left;color:#999;font-size:%?30?%;margin-left:%?20?%}.nav-reg .item4[data-v-704ed9e0]{line-height:%?98?%;float:left;padding:%?24?% 0}.nav-reg .item4 .title[data-v-704ed9e0]{padding:%?6?% %?16?%;background:#00baad;line-height:%?38?%;float:left;overflow:hidden;font-size:%?24?%;border-radius:%?30?%;color:#fff}.nav-reg .item4 .again[data-v-704ed9e0]{padding:%?6?% %?16?%;background:#ccc;line-height:%?38?%;float:left;overflow:hidden;font-size:%?24?%;border-radius:%?30?%;color:#fff}.nav-reg .item5[data-v-704ed9e0]{width:32%;line-height:%?98?%;float:left;text-align:right}.nav-reg .item6[data-v-704ed9e0]{width:64%;line-height:%?98?%;float:left;margin-left:%?20?%;color:#999;font-size:%?28?%}.nav-reg .area[data-v-704ed9e0]{width:64%;height:%?98?%;line-height:%?98?%;float:left;margin-left:%?20?%;color:#999;font-size:%?28?%;overflow:hidden;-o-text-overflow:ellipsis;text-overflow:ellipsis}.nav-reg .item6 .text[data-v-704ed9e0]{float:right;font-size:%?26?%;color:#00baad}.nav-reg .item6 .upload[data-v-704ed9e0]{float:left;font-size:%?48?%;border:#ccc %?2?% solid;height:%?48?%;width:%?48?%;margin:%?25?% %?20?% 0 0;line-height:%?38?%;text-align:center}.header[data-v-704ed9e0]{width:%?161?%;height:%?161?%;background:#3fcdeb;-webkit-box-shadow:%?0?% %?12?% %?13?% %?0?% rgba(63,205,235,.47);box-shadow:%?0?% %?12?% %?13?% %?0?% rgba(63,205,235,.47);border-radius:50%;margin-top:%?30?%;margin-left:auto;margin-right:auto}.header uni-image[data-v-704ed9e0]{width:%?161?%;height:%?161?%;border-radius:50%}.wxname[data-v-704ed9e0]{width:100%;line-height:%?48?%;text-align:center;font-size:%?38?%}.tishi[data-v-704ed9e0]{width:100%;line-height:%?48?%;text-align:center}.tishi uni-text[data-v-704ed9e0]{color:#00cf38}",""])},ba46:function(e,t,i){"use strict";i.r(t);var n=i("4b51"),a=i.n(n);for(var o in n)"default"!==o&&function(e){i.d(t,e,function(){return n[e]})}(o);t["default"]=a.a}}]);