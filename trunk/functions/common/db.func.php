<?php
/*
db.config.php:数据库连接
author:
date:
*/
//conn db 1 as users
$db_flag = 0;
$my_conn = null;
function db_connect(){
    global $config,$db_flag,$my_conn;
    if ($db_flag != 1) {
        $my_conn = mysql_pconnect($config['dbhost1'],$config['dbuser'],$config['dbpwd']);
        mysql_select_db($config['dbdb1'],$my_conn);
        mysql_query('set names utf8');
        $db_flag = 1;
    }

    return $my_conn;
}

function mysql_x_query($sql){
    //do log or so on
	$in_debug = 0;
	if ($in_debug==1){
		list($usec, $sec) = explode(' ', microtime());
		$start_time = ((float)$usec + (float)$sec);		
	}
	global $i_am_in_cron;
	global $is_testing;


	$only_see_cron = false;
	if ($only_see_cron && isset($i_am_in_cron)){
		print "$sql\n";
	}
	$v = '';
	if( (!empty($v) && preg_match("/$v/", $sql, $counts)) 
		&& ($only_see_cron==false || isset($i_am_in_cron)  ))
	{	
		print "<br>$v: $sql<br>";
	}
	if( $is_testing==true){
		global $db_query_logs;	
		$db_query_logs[] = $sql;	
	}
	
	$a = mysql_query($sql);
	
	if ($in_debug==1){
		list($usec, $sec) = explode(' ', microtime());
		$stop_time = ((float)$usec + (float)$sec);
		$escape = round(($stop_time - $start_time) * 1000, 1);
		print "<br>$escape:$sql<br>";
	}
	
	
	if($a == false)
	{
		if( $is_testing==true || isset($i_am_in_cron)	)
		{
			print "<br>false: $sql\n<br>";
			print mysql_error();
		}else{
			exit();
		}
	}
    return $a;
}


$mem_flag = 0;
$mem_conn = null;
// 建立memcache连接
function mcached_conn()
{
    global $config, $mem_flag, $mem_conn;
    if($mem_flag != 1)
    {
        if ($config['mcache']['version']==0){
            $mem_conn = memcache_pconnect($config['mcache']['host'], 11211);
        } else {
            $mem_conn = new Memcached();
            $mem_conn->addServer($config['mcache']['host'], 11211);
        }
        
        $mem_flag = 1;
    }
    return $mem_conn;
}


function mysql_w_query($sql)
{
	return mysql_x_query($sql);
}
?>