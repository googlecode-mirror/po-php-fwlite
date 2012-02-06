<?php
include_once IR.'functions/common/cache_info.func.php';
include_once IR.'functions/user/user_info.func.php';

//设置用户session
//设置用户cache
function init_user_session_and_cache($uid,$sid)
{
	global $zeit;
	set_cache($sid,'online_info',$uid,1);
	$user_base = user_get_user_base($uid,1);
}

function check_user_session($sid,$uid)
{
	//temp code
	return $uid;
	$this_uid = get_cache($sid,'online_info',1);
    if ($this_uid  ==-1){
		return 0;
	} elseif ($this_uid  ==-2) {
        //没有缓存信息或超时，我需要重新刷一次自己的信息
        set_cache($sid,'online_info',$uid,1);
		$this_uid = $uid;
    } else {
        $this_uid  = $this_uid ['data'];
    }
    return $this_uid;
}

function gen_session_id($uid,$ts,$key)
{
    global $config;
    $srv_id = $config['srv_id'];
    $sid = substr(md5($srv_id.$uid.$key),2,12);
    return $sid;
}

function gen_online_id($uid,$sid,$zeit){
    global $text_function_text;
	global $config;

	$ipx = get_user_ip();
	$ClientInfo = getenv("HTTP_USER_AGENT");
	
    $sql = "INSERT INTO st_online_details (uid,sid,first_online,last_online,ip1,ip2,ip3,ip4,ip_all,browser) VALUES($uid,'$sid',$zeit,$zeit,'$ipx[0]','$ipx[1]','$ipx[2]','$ipx[3]','$ipx[4]','$ClientInfo')";
    $rst = mysql_w_query($sql);
    return mysql_insert_id();
}


function get_online_info($uid,$sid,$zeit){
    //在线统计
    $v_online = get_cache($uid, 'online',1);
    if ($v_online == -1)
    {
        $online_id = gen_online_id($uid,$sid,$zeit);
        $online['online_id'] = $online_id;
        $online['sid'] = $sid;
		$online['last_update_db_online'] = $zeit;
        set_cache($uid, 'online', $online);
        $v_online = $online;
    }elseif ($v_online==-2){
        $sql = "SELECT MAX(`id`) as max_id FROM `st_online_details` WHERE `uid`='$uid'";
        $rst = mysql_query($sql);
        if ($row = mysql_fetch_assoc($rst)){
            $online['online_id'] = $row['max_id'];
        } else {
            $online_id = gen_online_id($uid,$sid,$zeit);
            $online['online_id'] = $online_id;
        }
        $online['sid'] = $sid;
		$online['last_update_db_online'] = $zeit;
        set_cache($uid, 'online', $online);
        $v_online = $online;
    }elseif ($v_online['sync_memcache']==1){
        $v_online = $v_online['data'];
        
        $sql = "UPDATE st_online_details SET last_online=$zeit WHERE id=$v_online[online_id]";
        $rst = mysql_w_query($sql);
	$sql = "UPDATE u_user SET last_online=$zeit WHERE uid=$uid";
        $rst = mysql_w_query($sql);
    } else {
        $v_online = $v_online['data'];
    }
    if ($v_online['sid']!=$sid){
        set_cookie($uid, $v_online['sid'], 0);
        $sid = $v_online['sid'];
    }
    return $v_online;
}


function update_online_memcache($uids){
    if (count($uids)>0) {
        foreach ($uids as $this_uid => $temp)
        {
            if( $this_uid>0 )
            {
                $v_online = get_cache($this_uid, 'online',1);
                if ($v_online==-1 || $v_online==-2){
                    
                } else {
                    $this_sid = $v_online['data']['sid'];
					user_update_user_base($this_uid,0);
					user_update_user_extend($this_uid,0);
					user_update_event_list($this_uid);
                }
            }
        }            
    }
}
?>
