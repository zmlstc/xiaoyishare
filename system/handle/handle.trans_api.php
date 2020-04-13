<?php
/**
 * 转帐
 *
 * @ShopWT版权所有，商业用途请到shopwt.com授权
 * @license    http://www.shopwt.com
 * @link       交流群号：58219240
 */
defined('ShopWT') or exit('Access Denied By ShopWT');
class trans_apiHandle {
	
	/** 支付宝转账**/
	public function alipayTransfer($transfer_info){
		if(empty($transfer_info)||!is_array($transfer_info)){
			return array();
		}
		
		require_once BASE_API_PATH.DS.'trans'.DS.'alipay/AopClient.php';
		require_once BASE_API_PATH.DS.'trans'.DS.'alipay/AopCertification.php';
		require_once BASE_API_PATH.DS.'trans'.DS.'alipay/request/AlipayTradeQueryRequest.php';
		require_once BASE_API_PATH.DS.'trans'.DS.'alipay/request/AlipayTradeWapPayRequest.php';
		require_once BASE_API_PATH.DS.'trans'.DS.'alipay/request/AlipayMerchantOrderSyncRequest.php';
		require_once BASE_API_PATH.DS.'trans'.DS.'alipay/request/AlipayMerchantItemFileUploadRequest.php';
		require_once BASE_API_PATH.DS.'trans'.DS.'alipay/request/AlipayFundTransToaccountTransferRequest.php';
		
		$aop = new AopClient ();
		$aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
		$aop->appId = '2016060101468884';
		//$aop->rsaPrivateKey     = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDuZCADc01XoFX2HbaNcbMKLKEXzaXfOWM2Qb49W3+UNepxsxvHnMiwZTKiF9dzTLAR/KkgCbsckA3+n5L5u+ga4cOhq2ajMfzTqdZXQ0pB1wfNKFFRlPoDK/+TkZBzttcjXM2G/v/WjoM+LLz5eSnIAkORCTNvNqJtKcceGKj39QIDAQAB';//'请填写开发者私钥去头去尾去回车，一行字符串';
		//$aop->alipayrsaPublicKey= 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCnxj/9qwVfgoUh/y2W89L6BkRAFljhNhgPdyPuBV64bfQNN1PjbCzkIM6qRdKBoLPXmKKMiFYnkd6rAoprih3/PrQEB/VsW8OoM8fxn67UDYuyBTqA23MML9q1+ilIZwBC2AQ2UBVOrFXfFl75p6/B5KsiNG9zpgmLCUYuLkxpLQIDAQAB';//'请填写支付宝公钥，一行字符串';
		$aop->rsaPrivateKey     = 'MIIEogIBAAKCAQEAmJ5nafOI1bOygjMa2lR+ud9a3vrGNpjDDPqbEFn4jX2B2dVp4aIq0s13A8TSpjEoGmth8ns4IGNtNokIi5J31oHIpInutiRthPeVtsh7QBK2B7AB5qlAcn1Jw1G++AapbqK6Od2hvKA+TepHrKRUbdYgmidemDBDWBDTNPA5UCImYsx7jxdoEYdrS0V/QA5aikqvhP2pobzc+T8jiD5BWCNSoS8Yl0veSzwDv/rbPh3VTnTIx1YkEDYd+LOm9YHtiIZAT35wrXUxwmj+PB3Q4YS7kTg1HT8a4vk26doVgus/9xDRC+M844nFVcpsr7JIz/eSB/LMRxYXVoAi5DlvXQIDAQABAoIBAELnnjoqUuAsKKM/OBRiWjOtoK6DjQU+LrOv4O5oIrgUPyp5jjMl3lQPUo9KZ3YHsqbyUDv2nIJGVAhGRoK5MOPOfWD0PruGeKSDzRQWC0Hpcfcox4yQwfnuxrljSq58bSZdG/pmGJMSRH3vqIAcgg6sghpSC/v3nG4s4gohDV79C+/TDtEO4HYfUiJzisaJ4y9BCtOnig50yEx7TCKl8WjJH0LYRehUjVsy72e1hbubh5EV7x9CNvewiZ0jRgtjh/riSSGwP4yWbZQExfDy+QuRhtbVy7+0xmhQCkTRNQIjkD0aONOUfoBW5W6f3qKfOIFXz/zE/Kpz5IPJ57qZHRkCgYEA0apdGRqYgk4G384YdW/Tvgs5jv79hE9lmjFH917RJvmth5uKCT/eAitV7mjPu9ivjouR8mDIWAVqvd2T8iRG4zu/YhpNgDMwflEBTVqG/D1O3u4MZDU1VvkH5kOi9fVvhmDhRhyWYmOKxD5/e7CeszdCO2w40v03l1Uz/47+Nq8CgYEAulisaoboWWVWYLz6c33jvGdc4kAVzQ3GkI8VjU4Z7nOJqcSnx6RStAwUQJ9IOr3UeExl6Of1cUe6qln9H3PsVIDUcpGQPEyFlgjBpvUTysykMElPwYf+4CaMVeYbzhko8U6qYlcPh8js5mrF4L06la47wD8ej6HdXmEnQK4GvbMCgYBuQCWgc8nKi3k16swVxO4VOxXTSebJ0m5c/JkZTTSySAymCHY3kuwNO/h924ORBcqbLG1chOQU1RRiLsgDRmw4RUzIK4ihZ0mSwsnqgTYGIb79nzPI5enciCtNPDfPGnbAtOUA1zuFFU79Dtg+lIt/NgmP8cKXwCuhWl5zfh/TZQKBgES5c00OWB6wGv0udQenBBh13nGqxN+NFxA1VPN6PGIOxlSQLHiah83TLpzuoYrYe8WqF2OgOIsAsz0kVxAEhsJbiP5/5Lv4VMQSkqjqddQzSNJuFzX+2+b7EIKVjOejq8/i/T+rcBTZ61THSBcscrDFQ1ARd0ntnmCyX/GgvOe1AoGAfb0Mh9Yx7ErMxiu4+MNf3dgEBbDxxSwaFsABcom+KOf9s3+u4KfkFmY59yd1BXGwlu5VtqS0cfxCpTZWlT/cFHnAbW4Qgx0frXj30alNlxtfSIcjcGNN4peg4ZIF9wy5s4OVMR6wAYO8E3r0PoZPWrw4uyWtfNGxyrXpXU1xOls=';
		$aop->alipayrsaPublicKey= 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA0B0NP6xK7g28cG7xAgCUzMbqJufLS9Y7sDLbUBtwPkfZkldwwIzsU7CLgIw0907PAyCjd4G0IcQuhKl2tAE+EcceZ060/BTiUsDVrCRNcUXm76g9PKVGY958445KTr5IJcO72nslwJOeDtwozAGJLVKwZHRtJ96pNR+UWthixLs677EdzhzNSqVxUV7wqUjr6O9PCh8WvPUm146wjbuLbeZ8SJp/Hany8ybHxc0+QaYablhjNUZiZogkhSFIg6AZY28tv0tFqX1dIBTNe8gH5KZRV18qw/aIqWsv7T5JioHOelBle6WJNXw/d6TPEt+6XCe1wQ6ts0OOWK5ILqd51wIDAQAB';
		$aop->apiVersion = '1.0';
		$aop->signType = 'RSA2';
		$aop->postCharset='UTF-8';
		$aop->format='json';
		$request = new AlipayFundTransToaccountTransferRequest ();
		$request->setBizContent("{" .
		"\"out_biz_no\":\"".$transfer_info['order_sn']."\"," .
		"\"payee_type\":\"ALIPAY_LOGONID\"," .
		"\"payee_account\":\"".$transfer_info['pay_account']."\"," .
		"\"amount\":\"".$transfer_info['amount']."\"," .
		//"\"payer_show_name\":\"上海交通卡退款\"," .
		"\"payee_real_name\":\"".$transfer_info['real_name']."\"," .
		"\"remark\":\"".$transfer_info['remark']."\"" .
		"  }");
		$result = $aop->execute ($request); 
 
		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$resultCode = $result->$responseNode->code;
		//Log::record('minutes--result====:'.$result);
		//Log::record('minutes--result==responseNode==:'.$result->$responseNode);
		//echo '-================_json---'.$result->$responseNode;
		if(!empty($resultCode)&&$resultCode == 10000){
			$data = array();
			$data['code'] = $resultCode;
			$data['out_biz_no'] = $result->$responseNode->out_biz_no;
			$data['pay_order_id'] = $result->$responseNode->order_id;
			$data['pay_date'] = $result->$responseNode->pay_date;
			
			return $data;
		} else {
			$data = array();
			$data['code'] = $resultCode;
			$data['out_biz_no'] = $result->$responseNode->out_biz_no;
			$data['pay_order_id'] = $result->$responseNode->order_id;
			//$data['pay_date'] = $result->$responseNode->pay_date;
			$data['msg'] = $result->$responseNode->msg;
			$data['sub_code'] = $result->$responseNode->sub_code;
			$data['sub_msg'] = $result->$responseNode->sub_msg;
			
			return $data;
		}
		
		
	}
	/** 支付宝转账查询**/
	public function alipayQuery($order_sn){
		
		require_once BASE_API_PATH.DS.'trans'.DS.'alipay/AopClient.php';
		require_once BASE_API_PATH.DS.'trans'.DS.'alipay/AopCertification.php';
		require_once BASE_API_PATH.DS.'trans'.DS.'alipay/request/AlipayTradeQueryRequest.php';
		require_once BASE_API_PATH.DS.'trans'.DS.'alipay/request/AlipayTradeWapPayRequest.php';
		require_once BASE_API_PATH.DS.'trans'.DS.'alipay/request/AlipayMerchantOrderSyncRequest.php';
		require_once BASE_API_PATH.DS.'trans'.DS.'alipay/request/AlipayMerchantItemFileUploadRequest.php';
		
		$aop = new AopClient ();
		$aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
		$aop->appId = '2016060101468884';
		$aop->rsaPrivateKey     = 'MIIEogIBAAKCAQEAmJ5nafOI1bOygjMa2lR+ud9a3vrGNpjDDPqbEFn4jX2B2dVp4aIq0s13A8TSpjEoGmth8ns4IGNtNokIi5J31oHIpInutiRthPeVtsh7QBK2B7AB5qlAcn1Jw1G++AapbqK6Od2hvKA+TepHrKRUbdYgmidemDBDWBDTNPA5UCImYsx7jxdoEYdrS0V/QA5aikqvhP2pobzc+T8jiD5BWCNSoS8Yl0veSzwDv/rbPh3VTnTIx1YkEDYd+LOm9YHtiIZAT35wrXUxwmj+PB3Q4YS7kTg1HT8a4vk26doVgus/9xDRC+M844nFVcpsr7JIz/eSB/LMRxYXVoAi5DlvXQIDAQABAoIBAELnnjoqUuAsKKM/OBRiWjOtoK6DjQU+LrOv4O5oIrgUPyp5jjMl3lQPUo9KZ3YHsqbyUDv2nIJGVAhGRoK5MOPOfWD0PruGeKSDzRQWC0Hpcfcox4yQwfnuxrljSq58bSZdG/pmGJMSRH3vqIAcgg6sghpSC/v3nG4s4gohDV79C+/TDtEO4HYfUiJzisaJ4y9BCtOnig50yEx7TCKl8WjJH0LYRehUjVsy72e1hbubh5EV7x9CNvewiZ0jRgtjh/riSSGwP4yWbZQExfDy+QuRhtbVy7+0xmhQCkTRNQIjkD0aONOUfoBW5W6f3qKfOIFXz/zE/Kpz5IPJ57qZHRkCgYEA0apdGRqYgk4G384YdW/Tvgs5jv79hE9lmjFH917RJvmth5uKCT/eAitV7mjPu9ivjouR8mDIWAVqvd2T8iRG4zu/YhpNgDMwflEBTVqG/D1O3u4MZDU1VvkH5kOi9fVvhmDhRhyWYmOKxD5/e7CeszdCO2w40v03l1Uz/47+Nq8CgYEAulisaoboWWVWYLz6c33jvGdc4kAVzQ3GkI8VjU4Z7nOJqcSnx6RStAwUQJ9IOr3UeExl6Of1cUe6qln9H3PsVIDUcpGQPEyFlgjBpvUTysykMElPwYf+4CaMVeYbzhko8U6qYlcPh8js5mrF4L06la47wD8ej6HdXmEnQK4GvbMCgYBuQCWgc8nKi3k16swVxO4VOxXTSebJ0m5c/JkZTTSySAymCHY3kuwNO/h924ORBcqbLG1chOQU1RRiLsgDRmw4RUzIK4ihZ0mSwsnqgTYGIb79nzPI5enciCtNPDfPGnbAtOUA1zuFFU79Dtg+lIt/NgmP8cKXwCuhWl5zfh/TZQKBgES5c00OWB6wGv0udQenBBh13nGqxN+NFxA1VPN6PGIOxlSQLHiah83TLpzuoYrYe8WqF2OgOIsAsz0kVxAEhsJbiP5/5Lv4VMQSkqjqddQzSNJuFzX+2+b7EIKVjOejq8/i/T+rcBTZ61THSBcscrDFQ1ARd0ntnmCyX/GgvOe1AoGAfb0Mh9Yx7ErMxiu4+MNf3dgEBbDxxSwaFsABcom+KOf9s3+u4KfkFmY59yd1BXGwlu5VtqS0cfxCpTZWlT/cFHnAbW4Qgx0frXj30alNlxtfSIcjcGNN4peg4ZIF9wy5s4OVMR6wAYO8E3r0PoZPWrw4uyWtfNGxyrXpXU1xOls=';
		$aop->alipayrsaPublicKey= 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA0B0NP6xK7g28cG7xAgCUzMbqJufLS9Y7sDLbUBtwPkfZkldwwIzsU7CLgIw0907PAyCjd4G0IcQuhKl2tAE+EcceZ060/BTiUsDVrCRNcUXm76g9PKVGY958445KTr5IJcO72nslwJOeDtwozAGJLVKwZHRtJ96pNR+UWthixLs677EdzhzNSqVxUV7wqUjr6O9PCh8WvPUm146wjbuLbeZ8SJp/Hany8ybHxc0+QaYablhjNUZiZogkhSFIg6AZY28tv0tFqX1dIBTNe8gH5KZRV18qw/aIqWsv7T5JioHOelBle6WJNXw/d6TPEt+6XCe1wQ6ts0OOWK5ILqd51wIDAQAB';
		$aop->apiVersion = '1.0';
		$aop->signType = 'RSA2';
		$aop->postCharset='UTF-8';
		$aop->format='json';
		$request = new AlipayFundTransOrderQueryRequest ();
		$request->setBizContent("{" .
		"\"out_biz_no\":\"".$order_sn."\"," .
		//"\"order_id\":\"20160627110070001502260006780837\"" .
		"  }");
		$result = $aop->execute ( $request); 

		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$resultCode = $result->$responseNode->code;
		if(!empty($resultCode)&&$resultCode == 10000){
			
			$data = array();
			$data['code'] = $resultCode;
			$data['out_biz_no'] = $result->$responseNode->out_biz_no;
			$data['pay_order_id'] = $result->$responseNode->order_id;
			$data['pay_date'] = $result->$responseNode->pay_date;
			$data['status '] = $result->$responseNode->status ;
			
			return $data;
			
		} else {
			$data = array();
			$data['code'] = $resultCode;
			$data['out_biz_no'] = $result->$responseNode->out_biz_no;
			//$data['pay_order_id'] = $result->$responseNode->order_id;
			//$data['pay_date'] = $result->$responseNode->pay_date;
			$data['msg'] = $result->$responseNode->msg;
			$data['sub_code'] = $result->$responseNode->sub_code;
			$data['sub_msg'] = $result->$responseNode->sub_msg;
			
			return $data;
		}
		
		
	}
	
	/** 微信转账**/
	public function wxpayTransfer($transfer_info){
		
		if(empty($transfer_info)||!is_array($transfer_info)){
			return array();
		}
		
		require_once BASE_API_PATH.DS.'trans'.DS.'wxpay/lib/WxPay.Api.php';
		require_once BASE_API_PATH.DS.'trans'.DS.'wxpay/WxPay.JsApiPay.php';
		require_once BASE_API_PATH.DS.'trans'.DS.'wxpay/WxPay.Config.php';
		require_once BASE_API_PATH.DS.'trans'.DS.'wxpay/log.php';
		
		$config = new WxPayConfig();
		$ret_data = WxPayApi::transfers($config, $transfer_info);
		if($ret_data&&$ret_data['return_code']=='SUCCESS'&&$ret_data['result_code']=='SUCCESS'){
			$data = array();
			$data['code'] = 10000;
			$data['out_biz_no'] = $ret_data['partner_trade_no'];
			$data['pay_order_id'] = $ret_data['payment_no'];
			$data['pay_date'] = $ret_data['payment_time'];
			
			return $data;
		}else{
			$data = array();
			$data['code'] = -100;
			$data['out_biz_no'] = $transfer_info['order_sn'];
			$data['pay_order_id'] = '';
			$data['msg'] = $ret_data['return_msg'];
			$data['sub_code'] = $ret_data['result_code'];
			$data['sub_msg'] = $ret_data['err_code'].'--'.$ret_data['err_code_des'];
			return $data;
		}
		
		
		/* 
		$key = 'bdDLUDy6b3gJXfPRr8L6wJKnpc0QCZbP';
		
		$url='https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
		$pars = array();
		$pars['mch_appid'] = 'wx761fc51f2055812f';//商户账号appid
		$pars['mchid']='1347652401';//商户号
		$pars['nonce_str'] = $this->getNonceStr();
		$pars['partner_trade_no'] =$transfer_info['order_sn'];//商户订单号
		$pars['openid'] =$transfer_info['pay_account'];
		$pars['check_name'] ='FORCE_CHECK';//'NO_CHECK' ;
		$pars['re_user_name'] =$transfer_info['real_name'] ;
		$monet_finall = $transfer_info['amount'] * 100;
		$pars['amount'] =$monet_finall; //这里是折算成1%的所以要*100
		$pars['desc'] =$transfer_info['remark'];
		$pars['spbill_create_ip'] =$transfer_info['ip'];
		
		ksort($pars, SORT_STRING);
		$string1 = '';
		foreach ($pars as $k => $v) {
		  $string1 .= "{$k}={$v}&";
		}
		 
		$string1 .= "key=".$key;
		$pars['sign'] = strtoupper(md5($string1));
		$xml = array2xml($pars); */
		
		
	}

	

}
