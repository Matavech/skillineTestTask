<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("1С-Битрикс: Управление сайтом");

$APPLICATION->IncludeComponent('mv:userlist', '', []);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>