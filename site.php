<?php
/**
 * 成语接龙模块微站定义
 *
 * @author 欧阳
 * @url
 */
defined('IN_IA') or exit('Access Denied');

session_start();

const APP_ID = '15646358';
const API_KEY = 'PbXxe3KMX3zx0FkiocSr9WU5';
const SECRET_KEY = 'QfGOSKXQM1BcbqZGuUGz7FzLt96vN1Tm';

class Css9_image_recognitionModuleSite extends WeModuleSite {
	public $settings;
	public function __construct() {
		global $_W;
		$sql = 'SELECT `settings` FROM ' . tablename('uni_account_modules') . ' WHERE `uniacid` = :uniacid AND `module` = :module';
		$settings = pdo_fetchcolumn($sql, array(':uniacid' => $_W['uniacid'], ':module' => 'css9_image_recognition'));
		$this->settings = iunserializer($settings);
	}

	public function doWebRule(){
		global $_W, $_GPC;
		$rid = intval($_GPC['id']);
		echo $rid;
	}

	public function doMobileHome(){
		global $_W, $_GPC;
		$wx = $_W['account']['jssdkconfig']; 
		include $this->template('home');
	}

	public function doMobileRecognize(){
		global $_W, $_GPC;
		if(isset($_SESSION['auth']) && isset($_GPC['image'])){
			$url = 'https://aip.baidubce.com/rest/2.0/image-classify/v2/logo?access_token='.$_SESSION['auth']['access_token'];
			//var_dump($_GPC['image']);
			//$img = file_get_contents($_GPC['image']);
			//$img = base64_encode($img);
			die(json_encode(array('image' => $_GPC['image'])));
			/*
			$bodys = array(
			    'image' => $img,
			    'custom_lib' => true
			);
			$res = request_post($url, $bodys);
			//var_dump($res);
			die(json_encode($res));
			*/
		}else{
			$this->get_access_token();
			$this->doMobileRecognize();
		}
	}
	private function request_post($url = '', $param = ''){
	    if (empty($url) || empty($param)) {
	        return false;
	    }
	    $postUrl = $url;
	    $curlPost = $param;
	    $curl = curl_init();//初始化curl
	    curl_setopt($curl, CURLOPT_URL,$postUrl);//抓取指定网页
	    curl_setopt($curl, CURLOPT_HEADER, 0);//设置header
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
	    curl_setopt($curl,CURLOPT_PROXY,'127.0.0.1:8888');//设置代理服务器
	    curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
	    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
	    $data = curl_exec($curl);//运行curl
	    curl_close($curl);
	    return $data;
	}
	private function get_access_token(){
		$url = 'https://aip.baidubce.com/oauth/2.0/token';
		$post_data['grant_type']    = 'client_credentials';
		$post_data['client_id']     = API_KEY;
		$post_data['client_secret'] = SECRET_KEY;
		$o = "";
		foreach ( $post_data as $k => $v ) 
		{
			$o.= "$k=" . urlencode( $v ). "&" ;
		}
		$post_data = substr($o,0,-1);
		$res = $this->request_post($url, $post_data);
		$_SESSION['auth'] = $res;
	}
}