<?
function areas_get_all_areas(){
    global $g_xcache_enabled;
    if ($g_xcache_enabled){
            $all_areas = get_xcache('sys','all_area',0);
    } else {
            $all_areas = get_cache('sys','all_area',0);
    }
    
    if ($all_areas ==-1 || $all_areas ==-2) {
            //没有缓存信息或超时，我需要重新刷一次自己的信息
            $all_areas = _update_all_areas();
    } else {
            $all_areas = $all_areas['data'];
    }
    return $all_areas;
}

function _update_all_areas(){
    global $g_xcache_enabled;
    $sql="SELECT * FROM s_map_area";
    $rst = mysql_x_query($sql);
    while ($row = mysql_fetch_assoc($rst)){
        $all_areas[$row['area_type']][$row['area_id']] = $row;
    }
    if ($g_xcache_enabled){
        set_xcache('sys','all_area',$all_areas,0);
    } else {
        set_cache('sys','all_area',$all_areas,0);
    }
    
    return $all_areas;
}

?>