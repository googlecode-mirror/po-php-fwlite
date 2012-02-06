<?
include_once IR.'functions/common/memcache.func.php';
include_once IR.'functions/common/ubbcode.func.php';
function user_get_user_base($uid,$need_online=0)
{ 
	global $g_users_base;
	if (isset($g_users_base[$uid])){
		return $g_users_base[$uid];
	}	 
	$user_info_base = user_update_user_base($uid,$need_online); 
	$g_users_base[$uid] = $user_info_base;
	return $user_info_base;
}

function user_update_user_base($uid,$need_online=0)
{
	global $zeit,$g_sunrise;
	$sql = "SELECT * FROM u_user WHERE uid=$uid";
	$rst = mysql_w_query($sql);
    $row_user_base = array();
	//if($row_user_base=mysql_fetch_assoc($rst)){
	//	
	//}else{
	//	return false;	
	//}
	//set_cache($uid,'user_base_info',$row_user_base,$need_online);
	return $row_user_base;

}
?>