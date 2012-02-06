<?php
/*
*  default.config.php
*  default config for po-php-fwlite  框架默认配置，用以简化base.config内容
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

$session_config['update_session_time'] = 300;
$session_config['update_global_session_time'] = 900;
$session_config['update_global_cache_time'] = 300;
$session_config['memcache_store_time'] = 1800;
$session_config['memcache_sync_time'] = 60;

// 测试功能
$is_testing = true;
$g_supply_lang = array('zh_CN', 'en', 'de');

$pay_public_key = 'sa7d3K';
$rank_delay = 3600;

$g_lang = 'zh_CN';
$g_region = 'CN';
?>