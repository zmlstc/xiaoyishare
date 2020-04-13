$(function(){
    if (getQueryString('seller_key') != '') {
        var key = getQueryString('seller_key');
        var seller_name = getQueryString('seller_name');
        addCookie('seller_key', key);
        addCookie('seller_name', seller_name);
    } else {
        var key = getCookie('seller_key');
        var seller_name = getCookie('seller_name');
    }
    if(key && seller_name){
        $.ajax({
            type:'post',
            url:ApiUrl+"/index.php/seller_index",
            data:{key:key},
            dataType:'json',
            success:function(result){
                checkSellerLogin(result.login);
                var html = ''
                    + '<div class="member-info">'
                        + '<div class="user-avatar"><img src="' + result.datas.store_info.store_avatar + '"/></div>'
                        + '<div class="user-name"><span>'+result.datas.seller_info.seller_name+'</span></div>'
                        + '<div class="store-name"><span>'+result.datas.store_info.store_name+'<sup>' + result.datas.store_info.grade_name + '</sup></span></div>'
                    + '</div>'
                    + '<div class="member-collect">'
                        + '<span><a href="javascript:;"><em>' + result.datas.store_info.daily_sales.ordernum + '</em><p>昨日销量</p></a></span>'
                        + '<span><a href="javascript:;"><em>' +result.datas.store_info.monthly_sales.ordernum + '</em><p>当月销量</p></a></span>'
                        + '<span><a href="store_goods_list.html"><em>' +result.datas.statics.online + '</em><p>出售中</p></a></span>'
                    + '</div>';
                $(".member-top").html(html);
                
                //订单管理
                var html = ''
                    + '<li><a href="store_orders_list.html?data-state=state_new"><em style=" right: 0;background-color:white;display: inline-block; font-size: 0.9rem; font-weight: lighter; height: 1rem; line-height: 1rem; margin: 0px auto; min-width: 1rem; text-align: center; vertical-align: top; position: relative; color: gray;">'+result.datas.statics.payment+'</em><p>待付款</p></a></li>'
                    + '<li><a href="store_orders_list.html?data-state=state_pay"><em style=" right: 0;background-color:white;display: inline-block; font-size: 0.9rem; font-weight: lighter; height: 1rem; line-height: 1rem; margin: 0px auto; min-width: 1rem; text-align: center; vertical-align: top; position: relative; color: gray;">'+result.datas.statics.delivery+'</em><p>待发货</p></a></li>'
                    + '<li><a href="store_orders_list.html?data-state=state_send"><em style=" right: 0;background-color:white;display: inline-block; font-size: 0.9rem; font-weight: lighter; height: 1rem; line-height: 1rem; margin: 0px auto; min-width: 1rem; text-align: center; vertical-align: top; position: relative; color: gray;">'+result.datas.statics.no_receipt+'</em><p>已发货</p></a></li>'
                    + '<li><a href="store_orders_list.html?data-state=state_success"><em style=" right: 0;background-color:white;display: inline-block; font-size: 0.9rem; font-weight: lighter; height: 1rem; line-height: 1rem; margin: 0px auto; min-width: 1rem; text-align: center; vertical-align: top; position: relative; color: gray;">'+result.datas.statics.orderok+'</em><p>已完成</p></a></li>'
                    + '<li><a href="store_orders_list.html"><em style=" right: 0;background-color:white;display: inline-block; font-size: 0.9rem; font-weight: lighter; height: 1rem; line-height: 1rem; margin: 0px auto; min-width: 1rem; text-align: center; vertical-align: top; position: relative; color: gray;">'+result.datas.statics.refund_lock+'</em><p>退款/退货</p></a></li>';
                $("#order_ul").html(html);
                
                //商品管理
			var html = ''
				+ '<li><a href="store_goods_list.html"><i class="cc-07"></i><p>出售中</p></a></li>'
				+ '<li><a href="store_goods_list.html?showtype=offlinegoods"><i class="cc-10"></i><p>仓库中</p></a></li>'
				+ '<li><a href="store_goods_list.html?showtype=illegalgoods"><i class="cc-14"></i><p>违规商品</p></a></li>'
				+ '<li><a href="store_goods_add.html"><i class="cc-04"></i><p>发布商品</p></a></li>'
                $("#goods_ul").html(html);
                //订单统计
			var html = ''
				+ '<li><a href="seller_stat_index.html"><i class="cc-21"></i><p>店铺概况</p></a></li>'
				//+ '<li><a href="seller_stat_goodslist.html"><i class="cc-18"></i><p>店铺统计</p></a></li>'
				+ '<li><a href="seller_stat_storeflow.html"><i class="cc-19"></i><p>店铺流量</p></a></li>'
				+ '<li><a href="seller_stat_goodsflow.html"><i class="cc-20"></i><p>商品流量</p></a></li>'
				+ '<li><a href="seller_stat_hotgoods.html"><i class="cc-22"></i><p>热销排行</p></a></li>'
                $("#order_stat").html(html);                
                 //统计结算
                var html = ''
                    + '<li><div><p style="font-size:18px;color:red;font-weight:bold;">'+result.datas.statnew_arr.orderamount+'</p><p>营业额</p></div></li><li><div><p style="font-size:18px;color:red;font-weight:bold;">'+result.datas.statnew_arr.ordernum+'</p><p>下单量</p></div></li><li><div><p style="font-size:18px;color:red;font-weight:bold;">'+result.datas.statnew_arr.ordermembernum+'</p><p>下单会员</p></div></li><li><div><p style="font-size:18px;color:red;font-weight:bold;">'+result.datas.statnew_arr.avgorderamount+'</p><p>平均价格</p></div></li>'
                $("#asset_ul").html(html);
                
                return false;
            }
        });
    } else {
        delCookie('seller_key');
        delCookie('seller_name');
        delCookie('store_name');
        var html = ''
            + '<div class="member-info">'
                + '<a href="login.html" class="default-avatar" style="display:block;"></a>'
                + '<a href="login.html" class="to-login">点击登录</a>'
            + '</div>'
            + '<div class="member-collect">'
                + '<span>'
                    + '<a href="login.html">'
                        + '<em>0</em>'
                        + '<p>昨日销量</p>'
                    + '</a>'
                + '</span>'
                + '<span>'
                    + '<a href="login.html">'
                        + '<em>0</em>'
                        + '<p>当月销量</p>'
                    + '</a>'
                + '</span>'
                + '<span>'
                    + '<a href="login.html">'
                        + '<em>0</em>'
                        + '<p>出售中</p>'
                    + '</a>'
                + '</span>'
            + '</div>';
        $(".member-top").html(html);
        
        //订单管理
        var html = ''
            + '<li><a href="login.html"><i class="cc-01"></i><p>待付款</p></a></li>'
            + '<li><a href="login.html"><i class="cc-02"></i><p>待发货</p></a></li>'
            + '<li><a href="login.html"><i class="cc-03"></i><p>已发货</p></a></li>'
            + '<li><a href="login.html"><i class="cc-04"></i><p>已完成</p></a></li>'
	    + '<li><a href="login.html"><i class="cc-05"></i><p>退款/退货</p></a></li>';
        $("#order_ul").html(html);

       //商品管理
        var html = ''
			+ '<li><a href="login.html"><i class="cc-07"></i><p>出售中</p></a></li>'
			+ '<li><a href="login.html"><i class="cc-10"></i><p>仓库中</p></a></li><li>'
			+ '<a href="login.html"><i class="cc-14"></i><p>违规商品</p></a></li>'
			+ '<li><a href="login.html"><i class="cc-04"></i><p>发布商品</p></a></li>'
			$("#goods_ul").html(html);
        return false;
    }

    //滚动header固定到顶部
    $.scrollTransparent();
});