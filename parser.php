<?php
require_once 'database.php';

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

$sql = "INSERT INTO auction_info (auction_number, start_price, published_eis, published_ep, purchase_object, law_type, purchase_type, protocol_name)
        VALUES (:auction_number, :start_price, :published_eis, :published_ep, :purchase_object, :law_type, :purchase_type, :protocol_name)
        ON DUPLICATE KEY UPDATE
        start_price = VALUES(start_price),
        published_eis = VALUES(published_eis),
        published_ep = VALUES(published_ep),
        purchase_object = VALUES(purchase_object),
        law_type = VALUES(law_type),
        purchase_type = VALUES(purchase_type),
        protocol_name = VALUES(protocol_name)";

$stmt = $pdo->prepare($sql);

$stmt->bindParam(':auction_number', $auctionNumber);
$stmt->bindParam(':start_price', $startPrice);
$stmt->bindParam(':published_eis', $publishedEIS);
$stmt->bindParam(':published_ep', $publishedEP);
$stmt->bindParam(':purchase_object', $purchaseObject);
$stmt->bindParam(':law_type', $lawType);
$stmt->bindParam(':purchase_type', $purchaseType);
$stmt->bindParam(':protocol_name', $protocolName);

try {
    $stmt->execute();
    echo "Данные успешно сохранены в базу данных.";
} catch (PDOException $e) {
    echo "Ошибка при сохранении данных: " . $e->getMessage();
}

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
$protocolOrganizer = strip_tags($protocolOrganizer);

preg_match('/Извещение.*?section__info">(.*?)<\/span>/s', $html2, $matches);
$noticeLink = trim($matches[1] ?? 'Не найдено');
$noticeLink = strip_tags($noticeLink);

preg_match('/Место подведения итогов.*?section__info">(.*?)<\/span>/s', $html2, $matches);
$auctionPlace = trim($matches[1] ?? 'Не найдено');

preg_match('/Дата и время составления протокола.*?section__info">(.*?)\s*<span class=\'timeZoneName\'/s', $html2, $matches);
$protocolCreationDate = trim($matches[1] ?? 'Не найдено');

preg_match('/Дата подписания протокола.*?section__info">(.*?)\s*<span class=\'timeZoneName\'/s', $html2, $matches);
$protocolSigningDate = trim($matches[1] ?? 'Не найдено');

preg_match('/Комиссия.*?section__info">(.*?)<\/span>/s', $html2, $matches);
$commissionInfo = trim($matches[1] ?? 'Не найдено');

preg_match('/Всего членов комиссии.*?section__info">(.*?)<\/span>/s', $html2, $matches);
$totalMembers = trim($matches[1] ?? 'Не найдено');

preg_match('/Количество неголосующих членов комиссии.*?section__info">(.*?)<\/span>/s', $html2, $matches);
$nonVotingMembers = trim($matches[1] ?? 'Не найдено');

preg_match('/Количество присутствовавших членов комиссии.*?section__info">(.*?)<\/span>/s', $html2, $matches);
$presentMembers = trim($matches[1] ?? 'Не найдено');

$sql_check = "SELECT COUNT(*) FROM protocol_info WHERE auction_number = :auction_number";
$stmt_check = $pdo->prepare($sql_check);
$stmt_check->bindParam(':auction_number', $auctionNumber);
$stmt_check->execute();
$rowCount = $stmt_check->fetchColumn();

if ($rowCount == 0) {
    $sql_insert = "INSERT INTO protocol_info (
        auction_number,
        document_status,
        protocol_title,
        protocol_organizer,
        notice_link,
        auction_place,
        protocol_creation_date,
        protocol_signing_date,
        commission_info,
        total_members,
        non_voting_members,
        present_members
    ) VALUES (
        :auction_number,
        :document_status,
        :protocol_title,
        :protocol_organizer,
        :notice_link,
        :auction_place,
        :protocol_creation_date,
        :protocol_signing_date,
        :commission_info,
        :total_members,
        :non_voting_members,
        :present_members
    )";

    $stmt_insert = $pdo->prepare($sql_insert);

    $stmt_insert->bindParam(':auction_number', $auctionNumber);
    $stmt_insert->bindParam(':document_status', $documentStatus);
    $stmt_insert->bindParam(':protocol_title', $protocolTitle);
    $stmt_insert->bindParam(':protocol_organizer', $protocolOrganizer);
    $stmt_insert->bindParam(':notice_link', $noticeLink);
    $stmt_insert->bindParam(':auction_place', $auctionPlace);
    $stmt_insert->bindParam(':protocol_creation_date', $protocolCreationDate);
    $stmt_insert->bindParam(':protocol_signing_date', $protocolSigningDate);
    $stmt_insert->bindParam(':commission_info', $commissionInfo);
    $stmt_insert->bindParam(':total_members', $totalMembers);
    $stmt_insert->bindParam(':non_voting_members', $nonVotingMembers);
    $stmt_insert->bindParam(':present_members', $presentMembers);

    $stmt_insert->execute();
}

preg_match_all('/<tr class="tableBlock__row">.*?<td class="tableBlock__col">(.*?)<\/td>.*?<td class="tableBlock__col">(.*?)<\/td>/s', $html2, $commissionMatches);

foreach ($commissionMatches[1] as $key => $name) {
    $role = $commissionMatches[2][$key];
    
    $sql_check_commission = "SELECT COUNT(*) FROM commission_members WHERE auction_number = :auction_number AND member_name = :member_name";
    $stmt_check_commission = $pdo->prepare($sql_check_commission);
    $stmt_check_commission->bindParam(':auction_number', $auctionNumber);
    $stmt_check_commission->bindParam(':member_name', $name);
    $stmt_check_commission->execute();
    $commissionCount = $stmt_check_commission->fetchColumn();

    if ($commissionCount == 0) {
        $sql_insert_commission = "INSERT INTO commission_members (
            auction_number,
            member_name,
            role
        ) VALUES (
            :auction_number,
            :member_name,
            :role
        )";

        $stmt_insert_commission = $pdo->prepare($sql_insert_commission);

        $stmt_insert_commission->bindParam(':auction_number', $auctionNumber);
        $stmt_insert_commission->bindParam(':member_name', $name);
        $stmt_insert_commission->bindParam(':role', $role);

        $stmt_insert_commission->execute();
    }
}

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

preg_match_all('/<tr class="table__row">.*?<td class="table__row-item normal-text">(.*?)<\/td>.*?<td class="table__row-item normal-text">(.*?)<\/td>.*?<td class="table__row-item normal-text">(.*?)<\/td>.*?<td class="table__row-item normal-text">(.*?)<\/td>/s', $html3, $matches);

if (isset($matches[1])) {
    $numRows = count($matches[1]);
    for ($i = 0; $i < $numRows; $i++) {
        $bidNumber = trim($matches[1][$i]);
        $participantName = trim($matches[2][$i]);
        $admissionStatus = trim($matches[3][$i]);
        $serialNumber = trim($matches[4][$i]);
        
        $participantName = html_entity_decode($participantName, ENT_QUOTES, 'UTF-8');

        $sql_check_bid = "SELECT COUNT(*) FROM application WHERE bid_number = :bid_number";
        $stmt_check_bid = $pdo->prepare($sql_check_bid);
        $stmt_check_bid->bindParam(':bid_number', $bidNumber);
        $stmt_check_bid->execute();
        $rowCount_bid = $stmt_check_bid->fetchColumn();

        if ($rowCount_bid == 0) {
            $sql_insert_bid = "INSERT INTO application (
                bid_number,
                participant_name,
                admission_status,
                serial_number
            ) VALUES (
                :bid_number,
                :participant_name,
                :admission_status,
                :serial_number
            )";

            $stmt_insert_bid = $pdo->prepare($sql_insert_bid);

            $stmt_insert_bid->bindParam(':bid_number', $bidNumber);
            $stmt_insert_bid->bindParam(':participant_name', $participantName);
            $stmt_insert_bid->bindParam(':admission_status', $admissionStatus);
            $stmt_insert_bid->bindParam(':serial_number', $serialNumber);

            $stmt_insert_bid->execute();
        }
    }
}

$url4 = "https://zakupki.gov.ru/epz/order/notice/ea44/view/protocol/protocol-docs.html?regNumber=0329200062221006202&protocolId=35530565";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url4);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64)");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Accept: text/html",
    "Accept-Language: ru-RU,ru;q=0.9",
]);

$html4 = curl_exec($ch);
curl_close($ch);

preg_match_all('/<a\s+href="(https:\/\/zakupki\.gov\.ru\/44fz\/filestore\/public\/1\.0\/download\/priz\/file\.html\?uid=[^"]+)"[^>]*title="([^"]+)"/s', $html4, $matches);

$documentLinks = $matches[1]; 
$documentNames = $matches[2]; 

$documentsDir = 'documents/';
if (!is_dir($documentsDir)) {
    mkdir($documentsDir, 0777, true);
}

foreach ($documentLinks as $key => $fileUrl) {
    $fileName = trim(strip_tags($documentNames[$key])); 
    $fileName = preg_replace('/[^a-zA-Z0-9а-яА-Я0-9_\-\. ]/u', '', $fileName);
    $fileName = preg_replace('/\s+/', ' ', $fileName);
    $fileName = trim($fileName); 
    $safeFileName = $fileName;  
    $localPath = $documentsDir . $safeFileName;

    $sql_check_file = "SELECT COUNT(*) FROM documents WHERE file_name = :file_name AND file_path = :file_path";
    $stmt_check = $pdo->prepare($sql_check_file);
    $stmt_check->execute([
        'file_name' => $safeFileName,
        'file_path' => $localPath
    ]);
    
    $fileExists = $stmt_check->fetchColumn() > 0;

    if (!$fileExists) {
        if (!file_exists($localPath)) {
            $ch = curl_init($fileUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64)");
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Accept: */*",
                "Accept-Language: ru-RU,ru;q=0.9",
            ]);
            $fileContent = curl_exec($ch);
            curl_close($ch);

            if ($fileContent !== false) {
                file_put_contents($localPath, $fileContent);
            } else {
                echo "Ошибка при скачивании файла: " . $fileUrl . "\n";
            }
        }

        $sql_insert_file = "INSERT INTO documents (file_name, file_path) 
                            VALUES (:file_name, :file_path)";
        $stmt_insert = $pdo->prepare($sql_insert_file);
        $stmt_insert->execute([
            'file_name' => $safeFileName,
            'file_path' => $localPath
        ]);
    }
}

header("Location: index.php");
