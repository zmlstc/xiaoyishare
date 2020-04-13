<?php
/**
 * 商家销售统计
 *
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权pnc授权
 */



defined('ShopWT') or exit('Access Denied By ShopWT');


class seller_stat_indexControl extends mobileSellerControl {
    private $search_arr;//处理后的参数
    private $stat_arr;//处理后的参数
    public function __construct()
    {
        parent::__construct();
        import('function.datehelper');
        import('function.statistics');
        $model_stat = Model('stat');
        $this->search_arr = $_POST;
        $searchtime_arr = $model_stat->getStarttimeAndEndtime($this->search_arr);
        $this->search_arr['stime'] = $searchtime_arr[0];
        $this->search_arr['etime'] = $searchtime_arr[1];
        //存储参数
        $this->search_arr = $_REQUEST;
        //处理搜索时间
        $this->search_arr = $model_stat->dealwithSearchTime($this->search_arr);
        //获得系统年份
        $year_arr = getSystemYearArr();
        //获得系统月份
        $month_arr = getSystemMonthArr();
        //获得本月的周时间段
        $week_arr = getMonthWeekArr($this->search_arr['week']['current_year'], $this->search_arr['week']['current_month']);
        $gccache_arr = Model('goods_class')->getGoodsclassCache($this->choose_gcid, 3);
        $this->gc_arr = $gccache_arr['showclass'];
        $this->stat_arr['gc_json']=$gccache_arr['showclass'];
        $this->stat_arr['gc_choose_json']=$gccache_arr['choose_gcid'];
        $this->stat_arr['year_arr']=$year_arr;
        $this->stat_arr['month_arr']=$month_arr;
        $this->stat_arr['week_arr']=$week_arr;
        $this->stat_arr['search_arr']=$this->search_arr;

    }
    /*
     *商家订单首页
     */
    public function indexWt()
    {
        $model = Model('stat');
        //统计的日期0点
        $stat_time = strtotime(date('Y-m-d', time())) - 86400;
        /*
         * 近7天
         */
        $stime = $stat_time - 86400 * 6;
        //7天前
        $etime = $stat_time + 86400 - 1;
        //昨天23:59
        $statnew_arr = array();
        //查询订单表下单量、下单金额、下单客户数
        $where = array();
        $where['order_isvalid'] = 1;
        //计入统计的有效订单
        $where['store_id'] = $this->store_info['store_id'];
        $where['order_add_time'] = array('between', array($stime, $etime));
        $field = ' COUNT(*) as ordernum, SUM(order_amount) as orderamount, COUNT(DISTINCT buyer_id) as ordermembernum, AVG(order_amount) as avgorderamount ';
        $stat_order = $model->getoneByStatorder($where, $field);
        $statnew_arr['ordernum'] = ($t = $stat_order['ordernum']) ? $t : 0;
        $statnew_arr['orderamount'] = wtPriceFormat(($t = $stat_order['orderamount']) ? $t : 0);
        $statnew_arr['ordermembernum'] = ($t = $stat_order['ordermembernum']) > 0 ? $t : 0;
        $statnew_arr['avgorderamount'] = wtPriceFormat(($t = $stat_order['avgorderamount']) ? $t : 0);
        unset($stat_order);
        //下单高峰期
        $where = array();
        $where['order_isvalid'] = 1;
        //计入统计的有效订单
        $where['store_id'] = $this->store_info['store_id'];
        $where['order_add_time'] = array('between', array($stime, $etime));
        $field = ' HOUR(FROM_UNIXTIME(order_add_time)) as hourval,COUNT(*) as ordernum ';
        if (C('dbdriver') == 'mysqli') {
            $_group = 'hourval';
        } else {
            $_group = 'HOUR(FROM_UNIXTIME(order_add_time))';
        }
        $orderlist = $model->statByStatorder($where, $field, 0, 0, 'ordernum desc,hourval asc', $_group);
        foreach ((array) $orderlist as $k => $v) {
            if ($k < 2) {
                //取前两个订单量高的时间段
                if (!$statnew_arr['hothour']) {
                    $statnew_arr['hothour'] = $v['hourval'] . ":00~" . ($v['hourval'] + 1) . ":00";
                } else {
                    $statnew_arr['hothour'] .= "，" . ($v['hourval'] . ":00~" . ($v['hourval'] + 1) . ":00");
                }
            }
        }
        unset($orderlist);
        //查询订单商品表下单商品数
        $where = array();
        $where['order_isvalid'] = 1;
        //计入统计的有效订单
        $where['store_id'] = $this->store_info['store_id'];
        $where['order_add_time'] = array('between', array($stime, $etime));
        $field = ' SUM(goods_num) as ordergoodsnum, AVG(goods_pay_price/goods_num) as avggoodsprice ';
        $stat_ordergoods = $model->getoneByStatordergoods($where, $field);
       
        $statnew_arr['ordergoodsnum'] = ($t = $stat_ordergoods['ordergoodsnum']) ? $t : 0;
        $statnew_arr['avggoodsprice'] = wtPriceFormat(($t = $stat_ordergoods['avggoodsprice']) ? $t : 0);
        unset($stat_ordergoods);
        //商品总数、收藏量
        $goods_list = $model->statByGoods(array('store_id' => $this->store_info['store_id'], 'is_virtual' => 0), 'COUNT(*) as goodsnum, SUM(goods_collect) as gcollectnum');
       
        $statnew_arr['goodsnum'] = ($t = $goods_list[0]['goodsnum']) > 0 ? $t : 0;
        $statnew_arr['gcollectnum'] = ($t = $goods_list[0]['gcollectnum']) > 0 ? $t : 0;
        //店铺收藏量
        $store_list = $model->getoneByStore(array('store_id' => $this->store_info['store_id']), 'store_collect');
        $statnew_arr['store_collect'] = ($t = $store_list['store_collect']) > 0 ? $t : 0;
        /*
         * 销售走势
         */
        //构造横轴数据
        for ($i = $stime; $i < $etime; $i += 86400) {
            //当前数据的时间
            $timetext = date('n', $i) . '-' . date('j', $i);
            //统计图数据
            $stat_list[$timetext] = 0;
            //横轴
            $stat_arr['xAxis']['categories'][] = $timetext;
        }
        $where = array();
        $where['order_isvalid'] = 1;
        //计入统计的有效订单
        $where['store_id'] = $this->store_info['store_id'];
        $where['order_add_time'] = array('between', array($stime, $etime));
        $field = ' min(order_add_time) as order_add_time,SUM(order_amount) as orderamount,MONTH(FROM_UNIXTIME(order_add_time)) as monthval,DAY(FROM_UNIXTIME(order_add_time)) as dayval ';
        if (C('dbdriver') == 'mysqli') {
            $_group = 'monthval,dayval';
        } else {
            $_group = 'MONTH(FROM_UNIXTIME(order_add_time)),DAY(FROM_UNIXTIME(order_add_time))';
        }
        $stat_order = $model->statByStatorder($where, $field, 0, 0, '', $_group);
        if ($stat_order) {
            foreach ($stat_order as $k => $v) {
                $stat_list[$v['monthval'] . '-' . $v['dayval']] = floatval($v['orderamount']);
            }
        }
        $stat_arr['legend']['enabled'] = false;
        $stat_arr['series'][0]['name'] = '下单金额';
        $stat_arr['series'][0]['data'] = array_values($stat_list);
        //得到统计图数据
        $stat_arr['title'] = '最近7天销售走势';
        $stat_arr['yAxis'] = '下单金额';
        $stattoday_json = json_decode(getStatData_LineLabels($stat_arr));
        unset($stat_arr);
        /*
         * 7日内商品销售TOP30
         */
        $stime = $stat_time - 86400 * 6;
        //7天前0点
        $etime = $stat_time + 86400 - 1;
        //今天24点
        $where = array();
        $where['order_isvalid'] = 1;
        //计入统计的有效订单
        $where['store_id'] = $this->store_info['store_id'];
        $where['order_add_time'] = array('between', array($stime, $etime));
        $field = ' sum(goods_num) as ordergoodsnum, goods_id, min(goods_name) as goods_name ';
        $goodstop30_arr = $model->statByStatordergoods($where, $field, 0, 30, 'ordergoodsnum desc', 'goods_id');
        /**
         * 7日内同行热卖商品
         */
        $where = array();
        $where['order_isvalid'] = 1;
        //计入统计的有效订单
        $where['order_add_time'] = array('between', array($stime, $etime));
        $where['store_id'] = array('neq', $this->store_info['store_id']);
        if (!checkPlatformStore()) {
            //如果不是平台店铺，则查询店铺经营类目的同行数据
            //查询店铺经营类目
            $store_bindclass = Model('store_bind_class')->getStoreBindClassList(array('store_id' => $this->store_info['store_id']));
            $goodsclassid_arr = array();
            foreach ((array) $store_bindclass as $k => $v) {
                if (intval($v['class_3']) > 0) {
                    $goodsclassid_arr[3][] = intval($v['class_3']);
                } elseif (intval($v['class_2']) > 0) {
                    $goodsclassid_arr[2][] = intval($v['class_2']);
                } elseif (intval($v['class_1']) > 0) {
                    $goodsclassid_arr[1][] = intval($v['class_1']);
                }
            }
            //拼接商品分类条件
            if ($goodsclassid_arr) {
                ksort($goodsclassid_arr);
                $gc_parentidwhere_keyarr = array();
                $gc_parentidwhere_arr = array();
                foreach ((array) $goodsclassid_arr as $k => $v) {
                    $gc_parentidwhere_keyarr[] = 'gc_parentid_' . $k;
                    $gc_parentidwhere_arr[] = array('in', $goodsclassid_arr[$k]);
                }
                if (count($gc_parentidwhere_keyarr) == 1) {
                    $where[$gc_parentidwhere_keyarr[0]] = $gc_parentidwhere_arr[0];
                } else {
                    $gc_parentidwhere_arr['_multi'] = '1';
                    $where[implode('|', $gc_parentidwhere_keyarr)] = $gc_parentidwhere_arr;
                }
            }
        }
        $field = ' sum(goods_num) as ordergoodsnum, goods_id, min(goods_name) as goods_name ';
        $othergoodstop30_arr = $model->statByStatordergoods($where, $field, 0, 30, 'ordergoodsnum desc', 'goods_id');
        output_data(array('goodstop30_arr'=>$goodstop30_arr,'othergoodstop30_arr'=>$othergoodstop30_arr,
        'stattoday_json'=>$stattoday_json,'statnew_arr'=>$statnew_arr,
        'stat_time'=>$stat_time,'web_seo'=>C('site_name') . ' - ' . '店铺概况','stat_arr'=>$this->stat_arr));
    }    
    
    
    
}
