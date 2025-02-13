<?php
$url = "https://zakupki.gov.ru/epz/order/notice/ea44/view/protocol/protocol-bid-list.html?regNumber=0329200062221006202&protocolId=35530565";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$html = curl_exec($ch);
curl_close($ch);

if (!$html) {
    die("Ошибка загрузки страницы");
}

preg_match('/<a href="\/epz\/order\/notice\/ea44\/view\/common-info\.html\?regNumber=(\d+)"/', $html, $matches);
$auctionNumber = $matches[1] ?? 'Не найдено';


preg_match('/<span class="cardMainInfo__content cost">\s*([\d\s,.]+)\s*[^0-9]/u', $html, $matches);
$startPrice = trim(str_replace([' ', ' ', ','], ['', '', '.'], $matches[1] ?? '0'));


echo "Номер аукциона: $auctionNumber\n";
echo "Начальная цена: $startPrice руб.\n";

