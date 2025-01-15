<?php
$basePath = dirname(__DIR__);
$cities = [
    '65000' => '新北市',
    '63000' => '臺北市',
    '68000' => '桃園市',
    '66000' => '臺中市',
    '67000' => '臺南市',
    '64000' => '高雄市',
    '10002' => '宜蘭縣',
    '10004' => '新竹縣',
    '10005' => '苗栗縣',
    '10007' => '彰化縣',
    '10008' => '南投縣',
    '10009' => '雲林縣',
    '10010' => '嘉義縣',
    '10013' => '屏東縣',
    '10014' => '臺東縣',
    '10015' => '花蓮縣',
    '10016' => '澎湖縣',
    '10017' => '基隆市',
    '10018' => '新竹市',
    '10020' => '嘉義市',
    '09020' => '金門縣',
    '09007' => '連江縣',
];

$rawPath = $basePath . '/raw/社會住宅';
if(!file_exists($rawPath)) {
    mkdir($rawPath, 0777, true);
}

foreach($cities as $cityCode => $cityName) {
    $fh = fopen($rawPath . '/' . $cityName . '.csv', 'w');
    fputcsv($fh, ['縣市', '案名', '興辦主體', '戶數', '工程決標日期', '開工日期', '完工日期', '執行情況']);
    $content = file_get_contents('https://pip.moi.gov.tw/V3/B/SCRB0505.aspx?city=' . urlencode($cityName));
    $pos = strpos($content, '<table id="t1">');
    if(false !== $pos) {
        $posEnd = strpos($content, '<div class="view-footer">', $pos);
        $lines = explode('</tr>', substr($content, $pos, $posEnd - $pos));
        foreach($lines AS $line) {
            $cols = explode('</td>', $line);
            if(count($cols) !== 9) {
                continue;
            }
            array_pop($cols);
            foreach($cols AS $k => $v) {
                $cols[$k] = trim(strip_tags($v));
            }
            fputcsv($fh, $cols);
        }
    }
}