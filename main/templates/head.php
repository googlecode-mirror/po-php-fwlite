<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<title><?=$v_title?></title>
	<link rel="shortcut icon" href="favicon.ico"/>
	<link rel="icon" href="animated_favicon1.gif" type="image/gif" />
	<?
	if ($browser['browser']=='MSIE' && $browser['version']<7){
	?>
		<link rel="stylesheet" href="./css/ie6.css" type="text/css"/>
	<?
	} else {
	?>
		<link rel="stylesheet" href="./css/global.css" type="text/css"/>
	<?
	}
	?>
	
	<link rel="stylesheet" href="./css/ajaxfileupload.css" type="text/css"/>

	<script src="./javascript/jquery.min.js" type="text/javascript" language="javascript"></script>
	<script src="./javascript/jquery-ui.min.js" type="text/javascript" language="javascript"></script>
	<script src="./javascript/jquery.easing.1.3.js" type="text/javascript" language="javascript"></script>
	<script src="./javascript/jquery.blockUI.js" type="text/javascript" language="javascript"></script>
	<script src="./javascript/swfobject.js" type="text/javascript" language="javascript"></script>
	<!--<script src="./javascript/jwplayer.js" type="text/javascript" language="javascript"></script>-->
	<script src="./javascript/header.js" type="text/javascript" language="javascript"></script>
	<script src="./javascript/ajaxfileupload.js" type="text/javascript" language="javascript"></script>
	<script type="text/javascript">
	<!--
	//php生成全局变量
	//-->
	var uid=<?=$uid?>;
	</script>
</head>
<body>
	<div id="wrap">
		<div id="common_header">
			common header
		</div>
		<div id="container">