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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Информация о закупках</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <h1>Информация о закупках</h1>

    <h2>Данные о закупке</h2>
    <table>
        <thead>
            <tr>
                <th>Номер аукциона</th>
                <th>Начальная цена</th>
                <th>Размещено в ЕИС</th>
                <th>Размещено на ЭП</th>
                <th>Объект закупки</th>
                <th>Закон</th>
                <th>Тип закупки</th>
                <th>Наименование протокола</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($auction_info_rows as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['auction_number']) ?></td>
                    <td><?= htmlspecialchars($row['start_price']) ?></td>
                    <td><?= htmlspecialchars($row['published_eis']) ?></td>
                    <td><?= htmlspecialchars($row['published_ep']) ?></td>
                    <td><?= htmlspecialchars($row['purchase_object']) ?></td>
                    <td><?= htmlspecialchars($row['law_type']) ?></td>
                    <td><?= htmlspecialchars($row['purchase_type']) ?></td>
                    <td><?= htmlspecialchars($row['protocol_name']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Данные о протоколе</h2>
    <table>
        <thead>
            <tr>
                <th>Статус документа</th>
                <th>Наименование протокола</th>
                <th>Организация</th>
                <th>Извещение</th>
                <th>Место подведения итогов</th>
                <th>Дата и время составления</th>
                <th>Дата подписания</th>
                <th>Информация о комиссии</th>
                <th>Всего членов комиссии</th>
                <th>Неголосующие члены комиссии</th>
                <th>Присутствующие члены комиссии</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($protocol_info_rows as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['document_status']) ?></td>
                    <td><?= htmlspecialchars($row['protocol_title']) ?></td>
                    <td><?= htmlspecialchars($row['protocol_organizer']) ?></td>
                    <td><?= htmlspecialchars($row['notice_link']) ?></td>
                    <td><?= htmlspecialchars($row['auction_place']) ?></td>
                    <td><?= htmlspecialchars($row['protocol_creation_date']) ?></td>
                    <td><?= htmlspecialchars($row['protocol_signing_date']) ?></td>
                    <td><?= htmlspecialchars($row['commission_info']) ?></td>
                    <td><?= htmlspecialchars($row['total_members']) ?></td>
                    <td><?= htmlspecialchars($row['non_voting_members']) ?></td>
                    <td><?= htmlspecialchars($row['present_members']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Члены комиссии</h2>
    <table>
        <thead>
            <tr>
                <th>Имя</th>
                <th>Роль</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($commission_members_rows as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['member_name']) ?></td>
                    <td><?= htmlspecialchars($row['role']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <h2>Заявки </h2>
<table>
    <thead>
        <tr>
            <th>№ заявки</th>
            <th>Наименование участника</th>
            <th>Признак допуска заявки</th>
            <th>Порядковый номер</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($application_rows as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['bid_number']) ?></td>
                <td><?= htmlspecialchars($row['participant_name']) ?></td>
                <td><?= htmlspecialchars($row['admission_status']) ?></td>
                <td><?= htmlspecialchars($row['serial_number']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
