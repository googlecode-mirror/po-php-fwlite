<?php
/*
*  base.func.php
*  basic function for project, not for framework 项目需要的核心函数，不是框架通用部分
*  need rewrite every function for your project
*  Guo Jia(Anthemius, NJ.weihang@gmail.com)
*
*  Created by Guo Jia on 2008-3-12.
*  Copyright 2008-2012 Guo Jia All rights reserved.
*
* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
* You may obtain a copy of the License at
*
*      http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
*/

include_once IR."functions/common/db.func.php";
include_once IR."functions/common/common.func.php";
include_once IR."functions/user/user_info.func.php";
include_once IR.'functions/common/cache_info.func.php';

function return_not_open(){
    global $game_www_url,$g_config;
    header('Location: '.$game_www_url.'?m=index&a=not_open&open_zeit='.$g_config['game_start'].'&server='.urlencode($g_config['srv_name']));
    exit; 
}

function return_no_login($view){
    global $game_www_url,$text_function_text,$mix_index;
    $this_jump_url = $game_www_url;
    if (isset($_COOKIE['mix_id'])){
        if (isset($mix_index[(int)$_COOKIE['mix_id']])){
            $this_jump_url = $mix_index[(int)$_COOKIE['mix_id']];
        }
    }
    if ($view == PAGE) {
        print
		'<script language="javascript" type="text/javascript">
           window.location.href="'.$this_jump_url.'"; 
		</script>';
	//header('Location: '.$this_jump_url );
	exit; 
    } elseif ($view == HTML) {
	print
		'<script language="javascript" type="text/javascript">
           window.location.href="'.$this_jump_url.'"; 
		</script>';
	exit;
    } elseif ($view == JSON) {
		$json_rst = array('rst'=>-99,'data'=>$text_function_text['base_login_info']);
        if (isset($_GET['callback'])) {
            print $_GET['callback'].'('.json_encode($json_rst).');';
        } else {
            print json_encode($json_rst);
        }
	exit;
    }
    exit;
}

function return_logined($view){
    global $game_index_url,$text_function_text;

    if ($view == PAGE) {
	header('Location: '.$game_index_url );
	exit; 
    } elseif ($view == HTML) {
    	print
		'<script language="javascript" type="text/javascript">
           window.location.href="'.$game_index_url.'"; 
		</script>';
	exit;
    } elseif ($view == JSON) {
	$json_rst = array('rst'=>-99,'data'=>$text_function_text['base_login_info']);
        if (isset($_GET['callback'])) {
            print $_GET['callback'].'('.json_encode($json_rst).');';
        } else {
            print json_encode($json_rst);
        }
	exit;
    }
    exit;
}



function get_cookie(){
    global $g_config;
    $str = $_COOKIE[$g_config['srv_id'].'SAL'];
    $str = strrev($str);
    $str = base64_decode($str);
    $array = explode(":", $str);
    return $array;
}

//@uid  用户id
//@sid  session id
//@cid  cookie id
function set_cookie($uid,$sid,$cid){
    global $g_config;
    $str = "$uid:$sid:$cid";
    $str = base64_encode($str);
    $str = strrev($str);
    setcookie($g_config['srv_id'].'SAL',$str,time()+3600*5,'/');
}

//typ=2:聊天服务器及时；typ=1:
function base_check_user_online($uid,$typ=1){
    global $g_config,$zeit;
    if($typ==1){
        $sql = "SELECT id FROM st_online_details WHERE last_online>=$zeit-30*60 AND last_online<=$zeit";
        $rst = mysql_w_query($sql);
        if(mysql_num_rows($rst)>0){
            return true;
        }else{
            return false;
        }
    }else if($typ==2){
        $user_name = $g_config['srv_id'].$uid;
        $chat_server = $g_config['chat']['server'];
        $url = "http://$chat_server:5280/api/status/online?key=secret&username=$user_name&host=$chat_server&resource=xiff";
        $rst = call_remote_by_curl($url);
        if ($rst>=1){
	    return true;
        } else {
	    return false;
        }
    }
}
?>
