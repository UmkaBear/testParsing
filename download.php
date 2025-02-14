<?php
require 'database.php';

$sql_auction_info = "SELECT * FROM auction_info";
$sql_protocol_info = "SELECT * FROM protocol_info";
$sql_commission_members = "SELECT * FROM commission_members";
$sql_application = "SELECT * FROM application";

$auction_info_stmt = $pdo->query($sql_auction_info);
$auction_info_rows = $auction_info_stmt->fetchAll(PDO::FETCH_ASSOC);

$protocol_info_stmt = $pdo->query($sql_protocol_info);
$protocol_info_rows = $protocol_info_stmt->fetchAll(PDO::FETCH_ASSOC);

$commission_members_stmt = $pdo->query($sql_commission_members);
$commission_members_rows = $commission_members_stmt->fetchAll(PDO::FETCH_ASSOC);

$application_stmt = $pdo->query($sql_application);
$application_rows = $application_stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="auction_data.csv"');

$output = fopen('php://output', 'w');

fputs($output, "\xEF\xBB\xBF");

fputcsv($output, ['Номер аукциона', 'Начальная цена', 'Размещено в ЕИС', 'Размещено на ЭП', 'Объект закупки', 'Закон', 'Тип закупки', 'Наименование протокола']);
foreach ($auction_info_rows as $row) {
    fputcsv($output, [$row['auction_number'], $row['start_price'], $row['published_eis'], $row['published_ep'], $row['purchase_object'], $row['law_type'], $row['purchase_type'], $row['protocol_name']]);
}

fputcsv($output, []);  

fputcsv($output, ['Статус документа', 'Наименование протокола', 'Организация', 'Извещение', 'Место подведения итогов', 'Дата и время составления', 'Дата подписания', 'Информация о комиссии', 'Всего членов комиссии', 'Неголосующие члены комиссии', 'Присутствующие члены комиссии']);
foreach ($protocol_info_rows as $row) {
    fputcsv($output, [$row['document_status'], $row['protocol_title'], $row['protocol_organizer'], $row['notice_link'], $row['auction_place'], $row['protocol_creation_date'], $row['protocol_signing_date'], $row['commission_info'], $row['total_members'], $row['non_voting_members'], $row['present_members']]);
}

fputcsv($output, []);  

fputcsv($output, ['Имя', 'Роль']);
foreach ($commission_members_rows as $row) {
    fputcsv($output, [$row['member_name'], $row['role']]);
}

fputcsv($output, []);  

fputcsv($output, ['№ заявки', 'Наименование участника', 'Признак допуска заявки', 'Порядковый номер']);
foreach ($application_rows as $row) {
    fputcsv($output, [$row['bid_number'], $row['participant_name'], $row['admission_status'], $row['serial_number']]);
}

fclose($output);
exit();
?>
