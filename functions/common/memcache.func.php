<?php
//-2:已经超时
//-1没找到
function get_xcache($key,$typ,$is_user=0,$special_refresh_interval=0)
{
    global $session_config,$config;
    $srv_id = $config['srv_id'];
    $ts = time();
    $cache_key = $srv_id.'_'.$key.'_'.$typ;
    if(xcache_isset($cache_key) )
    {
        $rst = xcache_get($cache_key);
        return $rst;
    } else {
        return -1;
    }
}

function set_xcache($key,$typ,$value,$check_online=1)
{
    global $config;
    global $session_config;
    $srv_id = $config['srv_id'];
    $ts = time();
    $rst['data'] = $value;
    $rst['last_update_zeit'] = $ts;
    $rst['online']= $check_online;
    if( xcache_set($srv_id.'_'.$key.'_'.$typ,$rst,$session_config['memcache_store_time']) )
    {
        return 1;
    } else {
        return -1;
    }
}

function delete_xcache($key,$typ)
{
    global $config;
    $srv_id = $config['srv_id'];
    xcache_unset(md5($srv_id.'_'.$key.'_'.$typ));
}



function memcache_version_set($memcache_obj, $key,$value,$flag,$ttl){
    global $config;
    if ($config['mcache']['version']==0){
        return memcache_set($memcache_obj, $key,$value,$flag,$ttl);
    } else {
        return $memcache_obj->set($key,$value,$ttl);
    }
}

function memcache_version_get($memcache_obj, $key){
    global $config;
    if ($config['mcache']['version']==0){
        return memcache_get($memcache_obj, $key);
    } else {
        return $memcache_obj->get($key);
    }
    
}

function memcache_version_del($memcache_obj, $key){
    global $config;
    if ($config['mcache']['version']==0){
        return memcache_delete($memcache_obj, $key);
    } else {
        return $memcache_obj->delete($key);
    }
    
}
//-2:已经超时
//-1没找到
function get_cache($key,$typ,$is_user=0,$special_refresh_interval=0)
{
    global $session_config,$config;
    $srv_id = $config['srv_id'];
    $ts = time();
    $memcache_obj = mcached_conn();
    if( $rst = memcache_version_get($memcache_obj, md5($srv_id.'_'.$key.'_'.$typ)) )
    {
        $rst['sync_memcache'] = 0;
        if ($ts - $rst['last_sync_memcache_zeit'] > $session_config['memcache_sync_time']) {
            $rst['last_sync_memcache_zeit'] = $ts;
            memcache_version_set($memcache_obj, md5($srv_id.'_'.$key.'_'.$typ),$rst,MEMCACHE_COMPRESSED,$session_config['memcache_store_time']);
            $rst['sync_memcache']=1;    
        }
        if ($special_refresh_interval!=0){
            $out_of_date_time = $special_refresh_interval;
        } elseif ($is_user==1){
            $out_of_date_time = $session_config['update_session_time'];
        } else {
            $out_of_date_time = $session_config['update_global_cache_time'];
        }
        if ($ts - $rst['last_update_zeit'] > $out_of_date_time) {

            if ($special_refresh_interval!=0){

                $rst['last_update_zeit'] = $ts;

                $rst['last_sync_memcache_zeit'] = $ts;

                memcache_version_set($memcache_obj, md5($srv_id.'_'.$key.'_'.$typ),$rst,MEMCACHE_COMPRESSED,$session_config['memcache_store_time']);

            }

            return -2;

        } else {
            return $rst;    
        }
    } else {
        return -1;
    }
}

function set_cache($key,$typ,$value,$check_online=1)
{
    global $config;
    global $session_config;
    $srv_id = $config['srv_id'];
    $ts = time();
    $rst['data'] = $value;
    $rst['last_sync_memcache_zeit'] = $ts;
    $rst['last_update_zeit'] = $ts;
    $rst['sync_memcache'] = 0;
    $rst['online']= $check_online;
    $memcache_obj = mcached_conn();
    if( memcache_version_set($memcache_obj, md5($srv_id.'_'.$key.'_'.$typ),$rst,MEMCACHE_COMPRESSED,$session_config['memcache_store_time']) )
    {
        return 1;
    } else {
        return -1;
    }
}

function delete_cache($key,$typ)
{
    global $config;
    $srv_id = $config['srv_id'];
    $memcache_obj = mcached_conn();
    memcache_version_del($memcache_obj,md5($srv_id.'_'.$key.'_'.$typ));
}
?>