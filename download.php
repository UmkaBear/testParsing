<?php

require 'database.php';

class CsvExporter
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function exportAuctionData()
    {
        $data = $this->fetchData();
        $this->outputCsv($data);
    }

    private function fetchData()
    {
        $tables = [
            'auction_info' => ['query' => "SELECT * FROM auction_info", 'header' => ['Номер аукциона', 'Начальная цена', 'Размещено в ЕИС', 'Размещено на ЭП', 'Объект закупки', 'Закон', 'Тип закупки', 'Наименование протокола']],
            'protocol_info' => ['query' => "SELECT * FROM protocol_info", 'header' => ['Статус документа', 'Наименование протокола', 'Организация', 'Извещение', 'Место подведения итогов', 'Дата и время составления', 'Дата подписания', 'Информация о комиссии', 'Всего членов комиссии', 'Неголосующие члены комиссии', 'Присутствующие члены комиссии']],
            'commission_members' => ['query' => "SELECT * FROM commission_members", 'header' => ['Имя', 'Роль']],
            'application' => ['query' => "SELECT * FROM application", 'header' => ['№ заявки', 'Наименование участника', 'Признак допуска заявки', 'Порядковый номер']]
        ];

        $result = [];
        foreach ($tables as $table => $info) {
            $stmt = $this->pdo->query($info['query']);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $result[$table] = ['header' => $info['header'], 'rows' => $rows];
        }

        return $result;
    }

    private function outputCsv($data)
    {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="auction_data.csv"');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");

        foreach ($data as $table => $info) {
            fputcsv($output, $info['header']);
            foreach ($info['rows'] as $row) {
                fputcsv($output, array_values($row));
            }
            fputcsv($output, []);
        }

        fclose($output);
    }
}

try {
    $exporter = new CsvExporter($pdo);
    $exporter->exportAuctionData();
} catch (Exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo "Ошибка экспорта данных";
    exit();
}
