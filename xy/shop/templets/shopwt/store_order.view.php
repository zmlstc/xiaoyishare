<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>
<style>
.wtm-goods-gift {
	text-align: left;
}
.wtm-goods-gift ul {
    display: inline-block;
    font-size: 0;
    vertical-align: middle;
}
.wtm-goods-gift li {
    display: inline-block;
    letter-spacing: normal;
    margin-right: 4px;
    vertical-align: top;
    word-spacing: normal;
}
.wtm-goods-gift li a {
    background-color: #fff;
    display: table-cell;
    height: 30px;
    line-height: 0;
    overflow: hidden;
    text-align: center;
    vertical-align: middle;
    width: 30px;
}
.wtm-goods-gift li a img {
    max-height: 30px;
    max-width: 30px;
}
</style>
<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="javascript:history.back(-1)" title="返回列表"><i class="fa fa-arrow-bbs-o-left"></i></a>
      <div class="subject">
        <h3>支付订单</h3>
        <h5>商城交易订单查询及管理</h5>
      </div>
    </div>
  </div>
  <div class="wtap-order-style">
    <div class="titile">
      <h3></h3>
    </div>
<div class="wtap-order-flow">

    <?php if ($output['order_info']['order_type'] != 3) { ?>
      <ol class="num5">
        <li class="current">
          <h5>生成订单</h5>
          <i class="fa fa-arrow-bbs-right"></i>
          <time><?php echo date('Y-m-d H:i:s',$output['order_info']['add_time']);?></time>
        </li>
        <?php if ($output['order_info']['order_state'] == ORDER_STATE_CANCEL) { ?>
        <li class="current">
          <h5>取消订单</h5>
          <time><?php echo date('Y-m-d H:i:s',$output['order_info']['close_info']['log_time']);?></time>
        </li>
        <?php } else { ?>
        <li class="<?php if(intval($output['order_info']['payment_time']) && $output['order_info']['order_pay_state'] !== false) echo 'current'; ?>">
          <h5>完成付款</h5>
          <i class="fa fa-arrow-bbs-right"></i>
          <time><?php echo intval(date('His',$output['order_info']['payment_time'])) ? date('Y-m-d H:i:s',$output['order_info']['payment_time']) : date('Y-m-d',$output['order_info']['payment_time']);?></time>
        </li>
      
        <li class="<?php if($output['order_info']['evaluation_state'] == 1) { ?>current<?php } ?>">
          <h5>完成评价</h5>
          <time><?php echo $output['order_info']['extend_order_common']['evaluation_time'] ? date("Y-m-d H:i:s",$output['order_info']['extend_order_common']['evaluation_time']) : null; ?></time>
        </li>
        <?php } ?>
    </ol>
    <?php } else { ?>
      <ol class="num5">
        <li class="current">
          <h5>生成订单</h5>
          <i class="fa fa-arrow-bbs-right"></i>
          <time><?php echo date('Y-m-d H:i:s',$output['order_info']['add_time']);?></time>
        </li>
        <?php if ($output['order_info']['order_state'] == ORDER_STATE_CANCEL) { ?>
        <li class="current">
          <h5>取消订单</h5>
          <time><?php echo date('Y-m-d H:i:s',$output['order_info']['close_info']['log_time']);?></time>
        </li>
        <?php } ?>
       
        <li class="<?php if(intval($output['order_info']['payment_time']) && $output['order_info']['order_pay_state'] !== false) echo 'current';?>">
          <h5>完成付款</h5>
          <i class="fa fa-arrow-bbs-right"></i>
          <time>
          <?php if ($output['order_info']['payment_time']) { ?>
          <?php echo intval(date('His',$output['order_info']['payment_time'])) ? date('Y-m-d H:i:s',$output['order_info']['payment_time']) : date('Y-m-d',$output['order_info']['payment_time']);?>
          <?php } ?>
          </time>
        </li>
       
       
        <li class="<?php if($output['order_info']['evaluation_state'] == 1) { ?>current<?php } ?>">
          <h5>完成评价</h5>
          <time><?php echo $output['order_info']['extend_order_common']['evaluation_time'] ? date("Y-m-d H:i:s",$output['order_info']['extend_order_common']['evaluation_time']) : null; ?></time>
        </li>
        
    </ol>
    <?php }?>
    </div>

    <div class="wtap-order-details">
      
      <div class="tabs-panels">
        <div class="misc-info">
          <h4>下单/支付</h4>
          <dl>
            <dt><?php echo $lang['order_number'];?><?php echo $lang['wt_colon'];?></dt>
            <dd><?php echo $output['order_info']['order_sn'];?><?php if ($output['order_info']['order_type'] == 2) echo '[预定]';?><?php if ($output['order_info']['order_type'] == 3) echo '[门店自提]';?></dd>
            <dt>订单来源<?php echo $lang['wt_colon'];?></dt>
            <dd><?php echo str_replace(array(1,2), array('PC端','移动端'), $output['order_info']['order_from']);?></dd>
            <dt><?php echo $lang['order_time'];?><?php echo $lang['wt_colon'];?></dt>
            <dd><?php echo date('Y-m-d H:i:s',$output['order_info']['add_time']);?></dd>
          </dl>
          <?php if(intval($output['order_info']['payment_time'])){?>
          <dl>
            <dt>支付单号<?php echo $lang['wt_colon'];?></dt>
            <dd><?php echo $output['order_info']['pay_sn'];?></dd>
            <dt><?php echo $lang['payment'];?><?php echo $lang['wt_colon'];?></dt>
            <dd><?php echo orderPaymentName($output['order_info']['payment_code']);?></dd>
            <dt><?php echo $lang['payment_time'];?><?php echo $lang['wt_colon'];?></dt>
            <dd><?php echo intval(date('His',$output['order_info']['payment_time'])) ? date('Y-m-d H:i:s',$output['order_info']['payment_time']) : date('Y-m-d',$output['order_info']['payment_time']);?></dd>
          </dl>
          <?php } else if ($output['order_info']['payment_code'] == 'offline') { ?>
          <dl>
            <dt><?php echo $lang['payment'];?><?php echo $lang['wt_colon'];?></dt>
            <dd><?php echo orderPaymentName($output['order_info']['payment_code']);?></dd>
          </dl>          
          <?php } ?>
         
        </div>
        <div class="addr-note">
          <h4>购买/收货方信息</h4>
          <dl>
            <dt><?php echo $lang['buyer_name'];?><?php echo $lang['wt_colon'];?></dt>
            <dd><?php echo $output['order_info']['buyer_name'];?></dd>
            <dt></dt>
            <dd></dd>
          </dl>
         
        </div>

        <div class="contact-info">
          <h4>销售/发货方信息</h4>
          <dl>
            <dt><?php echo $lang['store_name'];?><?php echo $lang['wt_colon'];?></dt>
            <dd><?php echo $output['order_info']['store_name'];?></dd><dt>店主名称<?php echo $lang['wt_colon'];?></dt>
            <dd><?php echo $output['store_info']['seller_name'];?></dd>
            <dt>联系电话<?php echo $lang['wt_colon'];?></dt>
            <dd><?php echo $output['store_info']['store_phone'];?></dd>
          </dl>
         
        </div>


        <div class="total-amount">
          <h3><?php echo $lang['order_total_price'];?><?php echo $lang['wt_colon'];?><strong class="red_common"><?php echo $lang['currency'].wtPriceFormat($output['order_info']['order_amount']);?></strong></h3>
          <h4></h4>
         
        </div>
      </div>

    </div>
  </div>
</div>
<script type="text/javascript">
    $(function() {
        $(".tabs-nav > li > a").mousemove(function(e) {
            if (e.target == this) {
                var tabs = $(this).parent().parent().children("li");
                var panels = $(this).parents('.wtap-order-details:first').children(".tabs-panels");
                var index = $.inArray(this, $(this).parents('ul').find("a"));
                if (panels.eq(index)[0]) {
                    tabs.removeClass("current").eq(index).addClass("current");
                   panels.addClass("tabs-hide").eq(index).removeClass("tabs-hide");
                }
            }
        });
    });
</script>
