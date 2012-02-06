<?php
include_once IR.'functions/common/cache_info.func.php';
include_once IR.'functions/account/user_session.func.php';

//先检查cookie
if (isset($_COOKIE[$config['srv_id'].'SAL'])) {
	//取cookie
	//cookie解密
	$cookie = get_cookie();
	$uid = $cookie[0];
	$sid = $cookie[1];

	$cid = $cookie[2];
    set_cookie($uid, $sid, $cid);
	//标志
} else {
	$uid = 0;
	$sid = 0;
	$cid = 0;
    set_cookie(0, 0, 0);
}

if ($uid == 0){
    return_no_login($g_view);
}else{
	if ($config['maintain_work']){
		if(!in_array($uid,$config['maintain_super_uid']) ){
			return_maintain_work($g_view);
		}
	} else {
		//服务器校验session
		if (!$result = get_player_info($uid)) {
			$uid = 0;
			set_cookie(0, 0, 0);
			return_no_login($g_view);
		}
		$g_user_base = user_get_user_base($uid);
		$g_user_extend = user_get_user_extend($uid);		 
		$user_key = md5($uid.$public_key);
		
		//获得登录id，同步数据库session
		$v_online = get_online_info($uid,$sid,$zeit);
		
		//判断连续登录，加载成就的hook
		$last_online_detail = $g_user_extend['last_online'];
		$days_last_online = ($last_online_detail - $last_online_detail%86400)/86400;
		$days_now_online = ($zeit - $zeit%86400)/86400;
		//连续登录
		if(($days_now_online - $days_last_online)==86400){
			$ach_info = ach_get_value(2,$uid);
			if(empty($ach_info)){
				ach_new_ach(2,$uid,1);
				ach_insert_value(2,$uid,1);
			}else{
				ach_update_value(2,$uid,$ach_info['value']);
			}
		}elseif(($days_now_online - $days_last_online)>86400){//可以归零了
			ach_update_value(2,$uid,1);	
	}
	}
}

$browser = common_get_user_browser();

$g_sunrise = $zeit-($zeit+8*3600)%86400;
$g_sunset = $g_sunrise + 86399;
?>
