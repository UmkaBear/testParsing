<?php
$url1 = "https://zakupki.gov.ru/epz/order/notice/ea44/view/protocol/protocol-bid-list.html?regNumber=0329200062221006202&protocolId=35530565";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url1);
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

echo "Номер аукциона: $auctionNumber\n </br>";
echo "Начальная цена: $startPrice руб.\n </br>";
echo "Размещено в ЕИС: $publishedEIS\n </br>";
echo "Размещено на ЭП: $publishedEP\n </br>";
echo "Объект закупки: $purchaseObject\n </br>";
echo "Закон: $lawType\n </br>";
echo "Тип закупки: $purchaseType\n </br>";
echo "Наименование: $protocolName\n </br>";


$url2 = "https://zakupki.gov.ru/epz/order/notice/ea44/view/protocol/protocol-main-info.html?regNumber=0329200062221006202&protocolId=35530565";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url2);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64)");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Accept: text/html",
    "Accept-Language: ru-RU,ru;q=0.9",
]);

$html2 = curl_exec($ch);
curl_close($ch);

if (!$html2) {
    die("Ошибка загрузки страницы с дополнительной информацией");
}

preg_match('/Статус документа.*?section__info">(.*?)<\/span>/s', $html2, $matches);
$documentStatus = trim($matches[1] ?? 'Не найдено');

preg_match('/Наименование протокола.*?section__info">(.*?)<\/span>/s', $html2, $matches);
$protocolTitle = trim($matches[1] ?? 'Не найдено');

preg_match('/Организация, осуществляющая размещение протокола.*?section__info">(.*?)<\/span>/s', $html2, $matches);
$protocolOrganizer = trim($matches[1] ?? 'Не найдено');

preg_match('/Извещение.*?section__info">(.*?)<\/span>/s', $html2, $matches);
$noticeLink = trim($matches[1] ?? 'Не найдено');

preg_match('/Место подведения итогов.*?section__info">(.*?)<\/span>/s', $html2, $matches);
$auctionPlace = trim($matches[1] ?? 'Не найдено');

preg_match('/Дата и время составления протокола.*?section__info">(.*?)<\/span>/s', $html2, $matches);
$protocolCreationDate = trim($matches[1] ?? 'Не найдено');

preg_match('/Дата подписания протокола.*?section__info">(.*?)<\/span>/s', $html2, $matches);
$protocolSigningDate = trim($matches[1] ?? 'Не найдено');

preg_match('/Комиссия.*?section__info">(.*?)<\/span>/s', $html2, $matches);
$commissionInfo = trim($matches[1] ?? 'Не найдено');

preg_match_all('/<tr class="tableBlock__row">.*?<td class="tableBlock__col">(.*?)<\/td>.*?<td class="tableBlock__col">(.*?)<\/td>/s', $html2, $commissionMatches);
$commissionTable = '';
foreach ($commissionMatches[1] as $key => $name) {
    $role = $commissionMatches[2][$key];
    $commissionTable .= "Член комиссии: $name - Роль: $role\n<br>";
}

preg_match('/Всего членов комиссии.*?section__info">(.*?)<\/span>/s', $html2, $matches);
$totalMembers = trim($matches[1] ?? 'Не найдено');

preg_match('/Количество неголосующих членов комиссии.*?section__info">(.*?)<\/span>/s', $html2, $matches);
$nonVotingMembers = trim($matches[1] ?? 'Не найдено');

preg_match('/Количество присутствовавших членов комиссии.*?section__info">(.*?)<\/span>/s', $html2, $matches);
$presentMembers = trim($matches[1] ?? 'Не найдено');

echo "Сведения о состоянии протокола:\n<br>";
echo "Статус документа: $documentStatus\n<br>";
echo "Наименование протокола: $protocolTitle\n<br>";
echo "Организация, осуществляющая размещение протокола: $protocolOrganizer\n<br>";
echo "Извещение: $noticeLink\n<br>";
echo "Место подведения итогов: $auctionPlace\n<br>";
echo "Дата и время составления протокола: $protocolCreationDate\n<br>";
echo "Дата подписания протокола: $protocolSigningDate\n<br>";
echo "Информация о комиссии: $commissionInfo\n<br>";
echo "Состав комиссии:\n<br>$commissionTable";
echo "Всего членов комиссии: $totalMembers\n<br>";
echo "Количество неголосующих членов комиссии: $nonVotingMembers\n<br>";
echo "Количество присутствовавших членов комиссии: $presentMembers\n<br>";


$url3 = "https://zakupki.gov.ru/epz/order/notice/ea44/view/protocol/protocol-bid-list.html?regNumber=0329200062221006202&protocolId=35530565";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url3);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64)");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Accept: text/html",
    "Accept-Language: ru-RU,ru;q=0.9",
]);

$html3 = curl_exec($ch);
curl_close($ch);

if (!$html3) {
    die("Ошибка загрузки страницы");
}

preg_match_all('/<tr class="table__row">.*?<td class="table__row-item normal-text">(.*?)<\/td>.*?<td class="table__row-item normal-text">(.*?)<\/td>.*?<td class="table__row-item normal-text">(.*?)<\/td>.*?<td class="table__row-item normal-text">(.*?)<\/td>/s', $html3, $matches);

if (isset($matches[1])) {
    $numRows = count($matches[1]);
    for ($i = 0; $i < $numRows; $i++) {
        $bidNumber = trim($matches[1][$i]);
        $participantName = trim($matches[2][$i]);
        $admissionStatus = trim($matches[3][$i]);
        $serialNumber = trim($matches[4][$i]);
        
        echo "№ заявки: $bidNumber<br>";
        echo "Наименование участника: $participantName<br>";
        echo "Признак допуска заявки: $admissionStatus<br>";
        echo "Порядковый номер: $serialNumber<br><br>"; 
    }
} else {
    echo "Заявки не найдены.";
}



