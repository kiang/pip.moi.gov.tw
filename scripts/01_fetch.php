<?php

$basePath = dirname(__DIR__);
include $basePath . '/vendor/autoload.php';

$cities = array('新北市', '臺北市', '桃園市', '臺中市', '臺南市', '高雄市', '宜蘭縣', '新竹縣', '苗栗縣', '彰化縣', '南投縣', '雲林縣', '嘉義縣', '屏東縣', '臺東縣', '花蓮縣', '澎湖縣', '基隆市', '新竹市', '嘉義市', '金門縣', '連江縣');

use Goutte\Client;
use Symfony\Component\DomCrawler\Field\InputFormField;

$client = new Client();

$crawler = $client->request('GET', 'https://pip.moi.gov.tw/V3/E/SCRE0104.aspx');
$form = $crawler->selectButton('查詢')->form();

$domdocument = new \DOMDocument;

$form->remove('ctl00$ContentPlaceHolder1$btnQuery1');

$ff = $domdocument->createElement('input');
$ff->setAttribute('name', 'ctl00$ContentPlaceHolder1$btnLow2_Query');
$ff->setAttribute('value', '查詢');
$form->set(new InputFormField($ff));

foreach ($cities AS $city) {
    $targetPath = $basePath . '/raw/低度用電/' . $city;
    if (!file_exists($targetPath)) {
        mkdir($targetPath, 0777, true);
    }
    for ($y = 101; $y <= 108; $y++) {
        $ty = str_pad($y, 3, '0', STR_PAD_LEFT);
        $targetFile = $targetPath . '/' . $ty . '.csv';
        if (!file_exists($targetFile)) {
            $client->submit($form, array(
                'ctl00$ContentPlaceHolder1$ddlLow2_Year' => $ty,
                'ctl00$ContentPlaceHolder1$ddlLow2_City' => $city,
            ));

            $c = mb_convert_encoding($client->getResponse()->getContent(), 'utf-8', 'big5');
            if (substr($c, 0, 3) === '縣') {
                file_put_contents($targetFile, $c);
            }
        }
    }
}

$form->remove('ctl00$ContentPlaceHolder1$btnLow2_Query');

$ff = $domdocument->createElement('input');
$ff->setAttribute('name', 'ctl00$ContentPlaceHolder1$btnInv2_Query');
$ff->setAttribute('value', '查詢');
$form->set(new InputFormField($ff));

$qItems = array('Surplus_Q1', 'Surplus_Q2', 'Surplus_Q3', 'Surplus_Q4');

foreach ($cities AS $city) {
    $targetPath = $basePath . '/raw/新建餘屋/' . $city;
    if (!file_exists($targetPath)) {
        mkdir($targetPath, 0777, true);
    }
    for ($y = 101; $y <= 108; $y++) {
        $ty = str_pad($y, 3, '0', STR_PAD_LEFT);
        foreach ($qItems AS $qItem) {
            $targetFile = $targetPath . '/' . $ty . '_' . substr($qItem, -2) . '.csv';
            if (!file_exists($targetFile)) {
                $client->submit($form, array(
                    'ctl00$ContentPlaceHolder1$ddlInv2_Year' => $ty,
                    'ctl00$ContentPlaceHolder1$ddlInv2_Q' => $qItem,
                    'ctl00$ContentPlaceHolder1$ddlInv2_City' => $city,
                ));

                $c = mb_convert_encoding($client->getResponse()->getContent(), 'utf-8', 'big5');
                if (substr($c, 0, 3) === '縣') {
                    file_put_contents($targetFile, $c);
                }
            }
        }
    }
}