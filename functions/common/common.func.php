<?php
/*
*  base.func.php
*  basic function for framework 框架通用基础函数
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
// 用CURL 请求远端接口
include_once IR.'config/badwords.php';
function call_remote_by_curl($url_str)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url_str ); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $remote_result = curl_exec($ch);

    if (curl_errno($ch)) {
        $remote_result = 0;
    }
    curl_close($ch);

    return $remote_result;
}

function get_user_ip(){
		$onlineip = '';
    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $onlineip = getenv('HTTP_CLIENT_IP');
    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $onlineip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $onlineip = getenv('REMOTE_ADDR');
    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $onlineip = $_SERVER['REMOTE_ADDR'];
    }

    preg_match("/[\d\.]{7,15}/", $onlineip, $onlineipmatches);
    $onlineip = $onlineipmatches[0] ? $onlineipmatches[0] : 'unknown';
	$ipx = explode(".", $onlineip);
	for($i=0;$i<=3;$i++)
	{
		if( !isset( $ipx[$i] ) )
		{
			$ipx[$i] = 0;
		}
	}
	$ipx[4] = $ipx[0]*pow(255,3)+$ipx[1]*pow(255,2)+$ipx[2]*255+$ipx[3];
	return $ipx;
}

//检查输入是否完备
//$val_input = check_input(array('aid','iid','uid'),'POST');
//if ($val_input!='') {
//$return_url = XXXXX
//如果有缺失，返回缺失的参数；如果没有缺失，返回''空字符串
function check_input($arr,$input_typ='GET') {
	if ($input_typ=='GET') {
		foreach ($arr as $this_value) {
			if ($this_value=='') {
				continue;
			}
			if (!isset($_GET[$this_value])) {
				return $this_value;
			}
		}	
	} else {
		foreach ($arr as $this_value) {
			if ($this_value=='') {
				continue;
			}
			if (!isset($_POST[$this_value])) {
				return $this_value;
			}
		}
	}
	return '';
}

//对于出错返回，构建返回url串
//出错处理方法
//handler返回后，刷新页面，
//页面判断$callback,如果有callback，则调用callback函数，参数是$rst_typ和$ext_rst
//callback函数负责回到刚才的页面并且输出错误信息。
//这样，即使页面url不变继续点击ajax进行操作，不会再次触发callback函数，不会再次显示出错信息，除非这时候强制刷新页面。

//总的来说，大部分输入校验进行js/AJAX，基本上不可能出现错误，出现错误一般都是伪造数据包，因此可能性极小。
//因此不需要针对每个详细的输出做过多的输出提示。
function build_return_url($rst_typ,$ext_rst,$callback='',$url=''){
    $base_str="rst=$rst_typ";
    if ($ext_rst!='') {
        $base_str .="&ext_rst=$ext_rst";
    }
	if ($callback!='') {
        $callback .="&callback=$callback";
    }
	if ($url!='') {
		
	}	
	elseif (isset($_SERVER['HTTP_REFERER'])) {
		$url = $_SERVER['HTTP_REFERER'];	
	} else {
		$url = 'index.php';
	}
	
	$url = preg_replace('/rst=\w*/', '', $url);
	$url = preg_replace('/ext_rst=\w*/', '', $url);
	$url = preg_replace('/callback=\w*/', '', $url);
	
    if(preg_match('/\?/',$url))
	{
		if(!preg_match('/[?&]$/',$url))
		{
			$return_url = $url."&".$base_str;
		}
		else
		{
			$return_url = $url.$base_str;
		}
	}
	else
	{
		$return_url = $url."?".$base_str;
	}
	return $return_url;
}

function getmicrotime(){
    list($usec, $sec) = explode(" ",microtime());
    return intval((float)$usec + (float)$sec);
} 
//格式化时间，不带天的
function formatTime($atime){
    $H = intval($atime /3600);
    $M = intval(($atime%3600)/60);
    $S = $atime%60;
    
    if ($H<10){$H = '0'.$H;}else{$H = $H;}
    if ($M<10){$M = '0'.$M;}else{$M = $M;}
    if ($S<10){$S = '0'.$S;}else{$S = $S;}
    $atimeTime = $H.':'.$M.':'.$S;
    return $atimeTime;
}

//格式化倒计时需要的时间，带天的
/*function format_need_time($atime){
    $H = intval(($atime)/3600);
    $M = intval(($atime%3600)/60);
    $S = $atime%60;
    if ($H<10){$H = '0'.$H;}else{$H = $H;}
    if ($M<10){$M = '0'.$M;}else{$M = $M;}
    if ($S<10){$S = '0'.$S;}else{$S = $S;}
    $atimeTime = $H.':'.$M.':'.$S;
    return $atimeTime;
}*/



/**
 *
 *
 * Return part of a string(Enhance the function substr())
 *
 * @param string  $String  the string to cut.
 * @param int     $Length  the length of returned string.
 * @param booble  $append  whether append "...": false|true
 * @return string           the cutted string.
 */
function sysSubStr($str,$len,$append = false)
{
	$len = ceil($len / 3);
	$chars = $str;
	$len_org = mb_strlen($str);
    $i=$m=$n=0;
    do{
		if (!isset($chars[$i])){
			break;
		}
        if (preg_match ("/[0-9a-zA-Z]/", $chars[$i])){//纯英文   
            $m++;   
		}   
		else
		{
			$n++;
		}//非英文字节,   
        $k = $n/3+$m/2;   
        $l = $n/3+$m;//最终截取长度；$l = $n/3+$m*2？
		if ($l>=$len_org){
			break;
		}
        $i++;   
    } while($k < $len);
	
	if ($l<$len){
		return $str;
	} else {
        if($append)
        {
			$l = $l - 1;
			$str = mb_substr($str,0,$l,'utf-8');
            $str .= "..";
        } else {
			$str = mb_substr($str,0,$l,'utf-8');
		}
        return $str;
	}
} 


/*
* support UTF-8 only,
* ** the function return HTML Format string **
*/
function html_escape_insert_wbrs($str, $n=10,
         $chars_to_break_after='',$chars_to_break_before='')
{
    $out = '';
    $strpos = 0;
    $spc = 0;
    $len = mb_strlen($str,'UTF-8');
    for ($i = 1; $i < $len; ++$i) {
      $prev_char = mb_substr($str,$i-1,1,'UTF-8');
      $next_char = mb_substr($str,$i,1,'UTF-8');
      if (_u_IsSpace($next_char)) {
        $spc = $i;
      } else {
        if ($i - $spc == $n
         || mb_strpos( $chars_to_break_after,
            $prev_char,0,'UTF-8' ) !== FALSE
         || mb_strpos( $chars_to_break_before,
            $next_char,0,'UTF-8')  !== FALSE )
          {
            $out .= htmlspecialchars(
                mb_substr($str,$strpos, $i-$strpos,'UTF-8')
                       ) . '<wbr>';
            $strpos = $i;
            $spc = $i;
          }
      }
    }
    $out .= htmlspecialchars(
             mb_substr($str,$strpos,$len-$strpos,'UTF-8')
               );
    return $out;
}
/////
function _u_IsSpace($ch)
{
  return mb_strpos("\t\r\n",$ch,0,'UTF-8') !== FALSE;
}

// 返回给定时间戳 time 的月份中, 第 nth 个 week 的 起始时戳
function get_saturday_in_month($time , $week, $nth=1)
{
    if( $week == 7) $week = 0;
    $date_array = getdate($time);
    $f_t = ($time - $time%86400) - 86400*($date_array['mday']-1);   // 月份第一天
    $last_t = date('t', $f_t);  // 此月的天数
    
    $w = getdate($f_t);
    $wday = $w['wday'];
    $w_array = array();
    for( $i=1 ; $i<=$last_t ; $i++ )
    {
        if( $wday++ == $week )
        {
            $w_array[] = $i;
        }

        if($wday == 7)
        {
            $wday = 0;
        }
    }
    $day = isset($w_array[$nth-1])?$w_array[$nth-1]:null;
    $day_stamp = 0;
    if($day)
    {
        $day_stamp = $f_t + ($day-1)*86400;
    }
    return $day_stamp;
}


function base_prepare() {
	date_default_timezone_set('Asia/Shanghai');
}

function get_tier_level($tier){
    global $g_region;
    if ($g_region=='CN'){
	return $tier;
    } else {
	return 11-$tier;
    }
}

//格式化倒计时需要的时间，带天的
function format_need_time($atime){
    $H = intval(($atime)/3600);
    $M = intval(($atime%3600)/60);
    $S = $atime%60;
    if ($H<10){$H = '0'.$H;}else{$H = $H;}
    if ($M<10){$M = '0'.$M;}else{$M = $M;}
    if ($S<10){$S = '0'.$S;}else{$S = $S;}
    $atimeTime = $H.':'.$M.':'.$S;
    return $atimeTime;
}

//
function format_date_time($time,$typ){
	if ($typ==1){
		//2009-12-12 00:00:00
		return date('Y-m-d H:i:s',$time);
	} elseif ($typ == 2){
		return date('m-d H:i:s',$time);	
	} elseif ($typ == 3)
	{
		return date('H:i:s',$time);
	}
	//2 12-12 00:00:00
	//3 00:00:00
}

//转换数字到二进制字符串，length是补齐后的长度
function convert_decbin($number,$length){
	$bin_num = decbin($number);
	while(strlen($bin_num)<$length){
		$bin_num = "0".$bin_num;
	}
	return $bin_num;
}

//根据国家选择一个对应的名字
function pickup_name_by($nid,$name_num){
	$sql = "SELECT rand_value FROM s_lastname ORDER BY rand_value limit";
	$rst = mysql_w_query($sql);
	$row = mysql_fetch_assoc($rst);
	$mix_rand = $row['rand_value'];
	
	$sql = "SELECT rand_value FROM s_lastname ORDER BY rand_value limit DESC";
	$rst = mysql_w_query($sql);
	$row = mysql_fetch_assoc($rst);
	$max_rand = $row['rand_value'];
	
	$index = mt_rand($mix_rand,$max_rand);
	$sql = "SELECT * FROM s_lastname WHERE rand_value = $index";
	$rst = mysql_w_query($sql);
	$row = mysql_fetch_assoc($rst);
	$lastname = $row['simplified_chinese'];
	
	$sql = "SELECT rand_value FROM s_firstname ORDER BY rand_value limit";
	$rst = mysql_w_query($sql);
	$row = mysql_fetch_assoc($rst);
	$mix_first_rand = $row['rand_value'];
	
	$sql = "SELECT rand_value FROM s_firstname ORDER BY rand_value limit DESC";
	$rst = mysql_w_query($sql);
	$row = mysql_fetch_assoc($rst);
	$max_first_rand = $row['rand_value'];
	
	$first_index = mt_rand($mix_first_rand,$max_first_rand);
	$sql = "SELECT * FROM s_firstname WHERE rand_value = $first_index";
	$rst = mysql_w_query($sql);
	$row = mysql_fetch_assoc($rst);
	$firstname = $row['simplified_chinese'];
	
	$name = $firstname.$lastname;
	return $name;
}

function RGB_TO_HSV ($R, $G, $B)  // RGB Values:Number 0-255 
{                                 // HSV Results:Number 0-1 
   $HSL = array(); 

   $var_R = ($R / 255); 
   $var_G = ($G / 255); 
   $var_B = ($B / 255); 

   $var_Min = min($var_R, $var_G, $var_B); 
   $var_Max = max($var_R, $var_G, $var_B); 
   $del_Max = $var_Max - $var_Min; 

   $V = $var_Max; 

   if ($del_Max == 0) 
   { 
      $H = 0; 
      $S = 0; 
   } 
   else 
   { 
      $S = $del_Max / $var_Max; 

      $del_R = ( ( ( $var_Max - $var_R ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max; 
      $del_G = ( ( ( $var_Max - $var_G ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max; 
      $del_B = ( ( ( $var_Max - $var_B ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max; 

      if      ($var_R == $var_Max) $H = $del_B - $del_G; 
      else if ($var_G == $var_Max) $H = ( 1 / 3 ) + $del_R - $del_B; 
      else if ($var_B == $var_Max) $H = ( 2 / 3 ) + $del_G - $del_R; 

      if ($H<0) $H++; 
      if ($H>1) $H--; 
   } 

   $HSL['H'] = $H; 
   $HSL['S'] = $S; 
   $HSL['V'] = $V; 

   return $HSL; 
} 

function HSV_TO_RGB ($H, $S, $V)  // HSV Values:Number 0-1 
{                                 // RGB Results:Number 0-255 
    $RGB = array(); 

    if($S == 0) 
    { 
        $R = $G = $B = $V * 255; 
    } 
    else 
    { 
        $var_H = $H * 6; 
        $var_i = floor( $var_H ); 
        $var_1 = $V * ( 1 - $S ); 
        $var_2 = $V * ( 1 - $S * ( $var_H - $var_i ) ); 
        $var_3 = $V * ( 1 - $S * (1 - ( $var_H - $var_i ) ) ); 

        if       ($var_i == 0) { $var_R = $V     ; $var_G = $var_3  ; $var_B = $var_1 ; } 
        else if  ($var_i == 1) { $var_R = $var_2 ; $var_G = $V      ; $var_B = $var_1 ; } 
        else if  ($var_i == 2) { $var_R = $var_1 ; $var_G = $V      ; $var_B = $var_3 ; } 
        else if  ($var_i == 3) { $var_R = $var_1 ; $var_G = $var_2  ; $var_B = $V     ; } 
        else if  ($var_i == 4) { $var_R = $var_3 ; $var_G = $var_1  ; $var_B = $V     ; } 
        else                   { $var_R = $V     ; $var_G = $var_1  ; $var_B = $var_2 ; } 

        $R = $var_R * 255; 
        $G = $var_G * 255; 
        $B = $var_B * 255; 
    } 

    $RGB['R'] = $R; 
    $RGB['G'] = $G; 
    $RGB['B'] = $B; 

    return $RGB; 
}
function common_get_user_browser() 
{ 
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $rst['browser'] = '';
    $rst['version'] = ''; 
    if(preg_match('/MSIE/i',$u_agent)) 
    { 
        $rst['browser'] = "MSIE";
	$match=preg_match('/MSIE ([0-9]\.[0-9])/',$u_agent,$reg);
	if($match==0)
	    $rst['version'] =  -1;
	else
	    $rst['version'] = floatval($reg[1]);
    } 
    elseif(preg_match('/Firefox/i',$u_agent)) 
    { 
        $rst['browser'] = "firefox"; 
    } 
    elseif(preg_match('/Safari/i',$u_agent)) 
    { 
        $rst['browser'] = "safari"; 
    } 
    elseif(preg_match('/Chrome/i',$u_agent)) 
    { 
        $rst['browser'] = "chrome"; 
    } 
    elseif(preg_match('/Flock/i',$u_agent)) 
    { 
        $rst['browser'] = "flock"; 
    } 
    elseif(preg_match('/Opera/i',$u_agent)) 
    { 
        $rst['browser'] = "opera"; 
    } 
    
    return $rst; 
} 
function common_get_IE_fix_png_style($img_src){
    return "filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=$img_src, sizingMethod='scale')";
}

function common_add_zero_left($src,$length){
    while(strlen($src)<$length){
        $src = "0".$src;
    }
    return $src;
}
function common_get_zipd_number($src){	
    $src = (int)$src;
	if($src==0){
		return 0;
	}
    global $g_lang;
	//$g_lang = 'en';
    if ($g_lang=='en'){
		$split = 1000;    
		$temp = floor($src / $split);
		if ($temp<1){
			$dst[] = $src;
		}
		while ($temp>=1){
			$dst[] = $src % $split;
			$temp = $src = floor($src / $split);
		}    
		$rst = '';
		if (count($dst)>1){
			$i = 0;
			foreach($dst as $this_temp){
				switch ($i){
				case 0:
					$this_temp = common_add_zero_left($this_temp,3);
					$this_temp = preg_replace('/0*$/','',$this_temp);
					if ($this_temp==''){
						$rst = 'M';
					} else {
						$rst = '.'.$this_temp.'M';
					}
					
					break;
				case 1:
					$this_temp = common_add_zero_left($this_temp,3);
					$rst = $this_temp.$rst;
					break;
				default:		    
					$rst = $this_temp.','.$rst;
					break;
				}
				$i++;
			}
		} else {
			$rst = $src.'K';
		}    
		$rst = preg_replace('/^0+/','',$rst);     
		return $rst;
	}else if($g_lang=='zh_CN'){
		if($src<10){
			$rst = $src.'千';
		}else if($src/(10*10000)<1){
		    if ($src%10==0){
			
			$rst = floor($src/10).'万';
		    } else {
			$rst = floor($src/10).'.'.($src%10).'万';
		    }
		}else if($src/(10*10000)>=1){
			$temp = floor($src/100000);
			$this_temp = floor($src)%100000;
			$this_temp = common_add_zero_left($this_temp,5);
			$this_temp = preg_replace('/0*$/','',$this_temp);
			if($this_temp!=0){
				$rst = $temp.'.'.$this_temp.'亿';
			}else{
				$rst = $temp.'亿';
			}
		}
		$rst = preg_replace('/^0+/','',$rst); 
		return $rst;
	}
}

//module=模块，当module=common时走通用的错误,$err_no对于hack型错误一律返回-100以后，不需要写出错提示
function common_set_json_rst($module,$err_no,$replace_arr = '',$replace_by_arr = ''){
    global $json_rst,$text_handler_text;
    if (isset($text_handler_text[$module][$err_no])){
	if ($replace_arr!=''){
	    $json_rst['error'] = str_replace($replace_arr,$replace_by_arr,$text_handler_text[$module][$err_no]);
	} else {
	    $json_rst['error'] = $text_handler_text[$module][$err_no];
	}
	
    } else {
	if ($err_no>0){
	    $json_rst['error'] = $text_handler_text['succ'];
	} else {
	    $json_rst['error'] = $text_handler_text['err'];
	}
	
    }
    $json_rst['err_module'] = $module;
    $json_rst['rstno'] = $err_no;
}

//特殊字屏蔽
function special_word_replace($str){
   return reg_special_word_return($str);
//	global $snda_word_list;
//    $snda_bad_word = array();
//	foreach($snda_word_list as $i=>$this_word_list){
//        //$snda_bad_word = $snda_bad_word+$this_word_list;
//        $snda_bad_word = array_merge($snda_bad_word, $this_word_list);  
//	}
//   
//    uasort($snda_bad_word, "my_sort_word_length");
//    //var_dump($snda_bad_word);
//    foreach($snda_bad_word as $str_replace){
//        $str_replace_to = '***'; 
//		$str = str_ireplace($str_replace,$str_replace_to,$str);
//    } 
//	return $str;
}
function my_sort_word_length($a,$b){
    return strlen($b)-strlen($a);
}
//reg特殊字屏蔽
function reg_special_word_return($str){
	global $snda_word_list;	 
	foreach($snda_word_list as $i=>$this_word_list){
		if(preg_match('/'.implode('|',$this_word_list).'/i',$str,$matches)>0){
				//var_dump($str,$this_word_list);
			return true;
		}
		//foreach($this_word_list as $str_replace){
		//	if(preg_match('/'.$str_replace.'/',$str)>0){
		//		//var_dump($str,$str_replace);
		//		return true;
		//	}			 
		//}
	}
	return false;
}
function get_length( $str )
{
	$len = 0;
	$str_length = strlen( $str );
	for( $i = 0 ; $i < $str_length ; $i++ )
	{
		if( intval( bin2hex( $str[$i] ) , 16 ) < 0x80 )
		{
			$len++;
		}
		else 
		{
			$len += 2;
			$i += 2;
		}
	}
	return $len;
}

/**
 * 格式化CM$ 只保留一个小数点
 */
function common_remain_one_decimal($str){
	$preg = '/^([\d]+[\.]?[\d]?)[\d]*([\D]+)$/';
	$str = preg_replace($preg, '\1\2', $str);
	return $str;
}

//
function common_trans_xy_from_latlng($lat,$lng,$max_x,$max_y,$pos_x = 0,$pos_y=0){
    /*Spherical Mercator*/
    $x = ($max_x * (180 + $lng) / 360) % $max_x;
    // latitude: using the Mercator projection
    $radlat = $lat * pi() / 180;  // convert from degrees to radians
    $y = log(tan(($radlat/2) + (pi()/4)));  // do the Mercator projection (w/ equator of 2pi units)
    $y = ($max_y / 2) - ($max_x * $y / (2 * pi()));   // fit it to our map
    return array('x'=>$x+$pos_x,'y'=>$y+$pos_y);
}

function common_trans_xy_from_latlng_by_equirectangular($lat,$lng,$max_x,$max_y,$pos_x = 0,$pos_y=0){
    /*equirectangular*/
    $x = round((($lng + 180)*($max_x / 360))+$pos_x);
    $y = round(((($lat * -1)+90)*($max_y / 180))+$pos_y);
    return array('x'=>$x,'y'=>$y);
}
/**
 * @Author:tanghaihua
 * @param: page 当前 页的id
 * @param:page_count 总页数
 * example:transfer_search_content.php
 */
function common_page_list($page,$page_count){
    $page_list = array();
    if($page_count <= 7){
		for ($i = 1; $i <= $page_count; $i++){
			$page_list[] = $i;
		}
    }else{
        $page_other = $page_count-4;
        if($page <= 4){
            $page_list = array(1,2,3,4,5,6,'...',$page_count);
        }else if($page>4 && $page<$page_other){
            $page_list = array(1,'...',$page-2,$page-1,$page,$page+1,$page+2,'...',$page_count);
        }else if($page >= $page_other){
            $page_list = array(1,'...',$page_count-5,$page_count-4,$page_count-3,$page_count-2,$page_count-1,$page_count);
        }
    }
    return $page_list;
}

//in_array的扩充版 可以在多维数组里找到某个值
function in_multi_array($needle, $haystack)
{
    $in_multi_array = false;
    if(in_array($needle, $haystack))
    {
        $in_multi_array = true;
    }
    else
    {   
        foreach($haystack as $key=>$value)
        {
            if(is_array($value))
            {
                if(in_multi_array($needle, $value))
                {
                    $in_multi_array = true;
                    break;
                }
            }
        }
    }
    return $in_multi_array;
}
?>