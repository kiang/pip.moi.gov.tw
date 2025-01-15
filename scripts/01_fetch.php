<?php

$basePath = dirname(__DIR__);
include $basePath . '/vendor/autoload.php';

$cities = [
    '10000' => '全國',
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
$currentYear = date('Y') - 1911;
$periods = [
    '113H1' => '113上半年',
    '112H2' => '112下半年',
    '112H1' => '112上半年',
    '111H2' => '111下半年',
    '111H1' => '111上半年',
    '110H2' => '110下半年',
    '110H1' => '110上半年',
    '109H2' => '109下半年',
    '109H1' => '109上半年',
    '108' => '108',
    '107' => '107',
    '106' => '106',
    '105' => '105',
    '104' => '104',
    '103' => '103',
    '102' => '102',
    '101' => '101',
    '100' => '100',
    '099' => '099',
    '098' => '098',
];

use Goutte\Client;
use Symfony\Component\DomCrawler\Field\InputFormField;

$client = new Client();

$crawler = $client->request('GET', 'https://pip.moi.gov.tw/V3/E/SCRE0104.aspx');
$form = $crawler->selectButton('查詢')->form();

$domdocument = new \DOMDocument;

$ff = $domdocument->createElement('input');
$ff->setAttribute('name', 'ctl00$ContentPlaceHolder1$btnLow3_Query');
$ff->setAttribute('value', '查詢');
$form->set(new InputFormField($ff));

foreach ($cities as $city => $cityName) {
    $targetPath = $basePath . '/raw/低度用電/' . $cityName;
    if (!file_exists($targetPath)) {
        mkdir($targetPath, 0777, true);
    }
    foreach ($periods as $period => $periodName) {
        $targetFile = $targetPath . '/' . $period . '.csv';
        if (!file_exists($targetFile)) {
            $client->submit($form, array(
                'ctl00$ContentPlaceHolder1$ddlLow3_Year' => $period,
                'ctl00$ContentPlaceHolder1$ddlLow3_City' => $city,
            ));

            $c = $client->getResponse()->getContent();
            if (!empty($c)) {
                file_put_contents($targetFile, $c);
            }
        }
    }
}

$form->remove('ctl00$ContentPlaceHolder1$btnLow3_Query');

$periods = [
    '113Q2' => '113年第2季',
    '113Q1' => '113年第1季',
    '112Q4' => '112年第4季',
    '112Q3' => '112年第3季',
    '112Q2' => '112年第2季',
    '112Q1' => '112年第1季',
    '111Q4' => '111年第4季',
    '111Q3' => '111年第3季',
    '111Q2' => '111年第2季',
    '111Q1' => '111年第1季',
    '110Q4' => '110年第4季',
    '110Q3' => '110年第3季',
    '110Q2' => '110年第2季',
    '110Q1' => '110年第1季',
    '109Q4' => '109年第4季',
    '109Q3' => '109年第3季',
    '109Q2' => '109年第2季',
    '109Q1' => '109年第1季',
];

$ff = $domdocument->createElement('input');
$ff->setAttribute('name', 'ctl00$ContentPlaceHolder1$btnInv3_Query');
$ff->setAttribute('value', '查詢');
$form->set(new InputFormField($ff));

foreach ($cities as $city => $cityName) {
    $targetPath = $basePath . '/raw/新建餘屋/' . $cityName;
    if (!file_exists($targetPath)) {
        mkdir($targetPath, 0777, true);
    }
    foreach ($periods as $period => $periodName) {
        $targetFile = $targetPath . '/' . $period . '.csv';
        if (!file_exists($targetFile)) {
            $client->submit($form, array(
                'ctl00$ContentPlaceHolder1$ddlInv3_Year' => $period,
                'ctl00$ContentPlaceHolder1$ddlInv3_City' => $city,
            ));

            $c = $client->getResponse()->getContent();
            if (!empty($c)) {
                file_put_contents($targetFile, $c);
            }
        }
    }
}
