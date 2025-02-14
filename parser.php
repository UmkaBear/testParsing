<?php
$url = "https://zakupki.gov.ru/epz/order/notice/ea44/view/protocol/protocol-bid-list.html?regNumber=0329200062221006202&protocolId=35530565";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64)");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Accept: text/html",
    "Accept-Language: ru-RU,ru;q=0.9",
]);

$html = curl_exec($ch);
curl_close($ch);

if (!$html) {
    die("Ошибка загрузки страницы");
}

preg_match('/regNumber=(\d+)/', $html, $matches);
$auctionNumber = $matches[1] ?? 'Не найдено';

preg_match('/<span class="cardMainInfo__content cost">\s*([\d\s,.]+)\s*[^0-9]/u', $html, $matches);
$startPrice = trim(str_replace([' ', ' ', ','], ['', '', '.'], $matches[1] ?? '0'));

preg_match('/Размещено в ЕИС.*?content">(.*?)<\/span>/s', $html, $matches);
$publishedEIS = trim($matches[1] ?? 'Не найдено');

preg_match('/Размещено на ЭП.*?content">(.*?)<\/span>/s', $html, $matches);
$publishedEP = trim($matches[1] ?? 'Не найдено');

preg_match('/Объект закупки.*?content">(.*?)<\/span>/s', $html, $matches);
$purchaseObject = trim($matches[1] ?? 'Не найдено');

preg_match('/(\d+-ФЗ).*?distancedText.*?>(.*?)<\/span>/s', $html, $matches);
$lawType = trim($matches[1] ?? 'Не найдено');
$purchaseType = trim($matches[2] ?? 'Не найдено');

preg_match('/protocol">(.*?)<\/span>/', $html, $matches);
$protocolName = trim($matches[1] ?? 'Не найдено');

echo "Номер аукциона: $auctionNumber\n";
echo "Начальная цена: $startPrice руб.\n";
echo "Размещено в ЕИС: $publishedEIS\n";
echo "Размещено на ЭП: $publishedEP\n";
echo "Объект закупки: $purchaseObject\n";
echo "Закон: $lawType\n";
echo "Тип закупки: $purchaseType\n";
echo "Наименование: $protocolName\n";
