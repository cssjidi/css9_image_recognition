<?php
/**
 * 语音回复处理类
 *
 * @author css9
 * @url
 */
defined('IN_IA') or exit('Access Denied');

class Css9_image_recognitionModuleProcessor extends WeModuleProcessor {
	public function respond() {
        if(!$this->inContext) {
            $this->beginContext();
            load()->func('communication'); 
			      $response = ihttp_get('http://api.jiongxiao.com/idiom.php?first=1');
			      $reply = json_decode($response['content'],true);
            $reply = $_SESSION['idiom'] = $reply['msg'];
        } else {
            $context = tarnsferWords($this->message['content']);
            if ($context == '结束' || $context == 'end' || $context == '停止' || $context == '老子不玩了') {
                unset($_SESSION['idiom']);
                $reply = '您成功退出成语接龙游戏';
                $this->endContext();
            }else if(isset($_SESSION['idiom'])){
                $prevIdiom = $_SESSION['idiom'];
                $response = ihttp_get('http://api.jiongxiao.com/idiom.php?t='.$context.'&f='.$prevIdiom);
                $res = json_decode($response['content'],true);
                $reply = tarnsferWords($res['msg']); 
                $code = $res['code'];
                if($code == 1){
                  $_SESSION['idiom'] = $res['msg'];
                }else if($code == 3){
                  unset($_SESSION['idiom']);
                  $this->endContext();
                }
            }
        }
        return $this->respText($reply);
    }
}

function tarnsferWords($str){
  if(empty($str)){
    return $str;
  }
  $obj = json_decode('{"str":"'.$str.'"}',ture);
  return $obj['str'];
}