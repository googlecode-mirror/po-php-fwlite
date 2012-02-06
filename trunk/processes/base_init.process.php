<?php
/*
*  base_init.process.php
*  define init for po-php-fwlite  框架核心设置
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

if (isset($g_access_mode) && ($g_access_mode==1 || $g_access_mode==2)){
    header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
    header("Cache-Control: no-cache, must-revalidate");
}

define('PAGE',0);//page
define('HTML',1);//block HTML
define('JSON',2);//JSON data
define('XML',3);//XML data

//set default 补上被省略的module和action

$_allow_system['main'] = 1;
$_allow_system['admin'] = 1;

if(empty($_REQUEST['sub_sys']))
{
    $_REQUEST['sub_sys'] = 'main';
    $g_system = 'main';
} else {
    $g_system = addslashes($_REQUEST['sub_sys']);
    if ( !isset($_allow_system[$g_system]) ) {
        $g_system = 'main';
    }
}

if(!isset($_REQUEST['m']))
{
    $_REQUEST['m'] = 'index';
    $g_module = 'index';
} else {
    $g_module = addslashes($_REQUEST['m']);
}

if(!isset($_REQUEST['a']))
{
    $_REQUEST['a'] = 'index';
    $g_action = 'index';
} else {
    $g_action = addslashes($_REQUEST['a']);
}

if(!isset($_REQUEST['format']))
{
    //for web page, default as PAGE, for handler ,default json
    if ($g_access_mode==1){
        $_REQUEST['format'] = PAGE;
        $g_view = PAGE;
    } else {
        $_REQUEST['format'] = JSON;
        $g_view = JSON;
    }
} elseif ($_REQUEST['format']=='html') {
    $g_view = HTML;
} elseif ($_REQUEST['format']=='json') {
    $g_view = JSON;
} elseif ($_REQUEST['format']=='page') {
    $g_view = PAGE;
} else {
    if ($g_access_mode==1){
        $g_view = PAGE;
    } else {
        $g_view = JSON;
    }
}

define('IR', dirname(dirname( __FILE__)).'/');
define('UR', strtolower('http://'.$_SERVER['HTTP_HOST'].'/'.preg_replace('/(\/[^\/]*)$/', '', $_SERVER['REQUEST_URI'])).'/');

define('MR', IR.$g_system.'/modules/');
define('HR', IR.$g_system.'/handlers/');
define('VR', IR.$g_system.'/templates/');

mb_internal_encoding("utf8");

$zeit = time();

$g_sunrise = $zeit-($zeit+3600*8)%86400;
$g_sunset = $g_sunrise + 86400;

if ( !isset($g_xcache_enabled) ) {
    if ( function_exists('xcache_get') ) {
        $g_xcache_enabled = true;
    } else {
        $g_xcache_enabled = false;
    }
}
?>