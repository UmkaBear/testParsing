<?php
require 'database.php';

function fetchAllRows(PDO $pdo, string $sql): array {
    try {
        $stmt = $pdo->query($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    } catch (PDOException $e) {
        die("Error fetching data: " . $e->getMessage());
    }
}

$auction_info_rows = fetchAllRows($pdo, "SELECT * FROM auction_info");
$protocol_info_rows = fetchAllRows($pdo, "SELECT * FROM protocol_info");
$commission_members_rows = fetchAllRows($pdo, "SELECT * FROM commission_members");
$application_rows = fetchAllRows($pdo, "SELECT * FROM application");

try {
    $stmt = $pdo->prepare("SELECT file_name, file_path FROM documents");
    $stmt->execute();
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching documents: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Информация о закупках</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <form method="post" action="parser.php">
        <button type="submit" name="update_data" class="update-btn">Актуализировать данные</button>
    </form>
    <form method="post" action="download.php">
        <button type="submit" name="download_data" class="download-btn">Скачать</button>
    </form>
    <h2>Общая информация</h2>
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
<h2>Список документов</h2>
<table>
    <thead>
        <tr>
            <th>Наименование файла</th>
            <th>Скачать</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($documents as $document) {
            $fileName = htmlspecialchars($document['file_name']);
            $filePath = htmlspecialchars($document['file_path']);
            echo "<tr>";
            echo "<td>{$fileName}</td>";
            echo "<td><a href='{$filePath}' download>Скачать</a></td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>
</body>
</html>
