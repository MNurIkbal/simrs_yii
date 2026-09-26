<?php

namespace app\components\rabbitmq\excel;

use Doco\rabbitmq\task\ReportTask;
use Yii;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Client;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as StreamXlsx;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Collection\CellsFactory;
use PhpOffice\PhpSpreadsheet\Collection\Memory\SimpleCache1;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;
use app\modules\v1\models\LaporanStockInventoryFn;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoRestActiveFilter;

class LapStockInventoryTask extends ReportTask
{
    protected $header;
    protected $headerExcel;
    protected $title;
    protected $totalPerPage;
    protected $countData;
    protected $manual_excel;
    protected $filter;
    protected $list_data = [];
    protected $showed_data = [];

    const DEFAULT_DATA_SHOW = [0, 1, 2];
    const DEFAULT_HIDE_DATA = [];
    const DEFAULT_ROWSPAN = 2;

    const DEFAULT_LIST_COLUMN = [
        '0'  => ['title' => 'Nama Ruangan', 'data' => 'ruangan_nama'],
        '1'  => ['title' => 'Kode Obat', 'data' => 'obatalkes_kode'],
        '2'  => ['title' => 'Nama Obat Alkes', 'data' => 'obatalkes_nama'],
        '3'  => ['title' => 'Jenis Obat Alkes', 'data' => 'jenis_obat'],
        '4'  => ['title' => 'Generik', 'data' => 'is_generik'],
        '5'  => ['title' => 'Oral', 'data' => 'is_oral'],
        '6'  => ['title' => 'Satuan Kecil', 'data' => 'satuan_kecil'],
        '7'  => ['title' => 'Satuan Besar', 'data' => 'satuan_besar'],
        '8'  => ['title' => 'Nilai Konversi', 'data' => 'nilai_konv'],
        '9'  => ['title' => 'Stok Satuan Kecil', 'data' => 'qty_satuankecil'],
        '10' => ['title' => 'Stok Satuan Besar', 'data' => 'qty_satuanbesar'],
        '11' => ['title' => 'Weighted Average Satuan Kecil', 'data' => 'wa_satuan_kecil'],
        '12' => ['title' => 'Weighted Average Satuan Besar', 'data' => 'wa_satuan_besar'],
        '13' => ['title' => 'Total (Rp.) Weighted Average Satuan Kecil', 'data' => 'total_satuankecil'],
        '14' => ['title' => 'Total (Rp.) Weighted Average Satuan Besar', 'data' => 'total_satuanbesar'],
        '15' => ['title' => 'Base Price Satuan Kecil', 'data' => 'baseprice_kecil'],
        '16' => ['title' => 'Base Price Satuan Besar', 'data' => 'baseprice_besar'],
        '17' => ['title' => 'Total (Rp.) Base Price Satuan Kecil', 'data' => 'total_satuankecil_netto'],
        '18' => ['title' => 'Total (Rp.) Base Price Satuan Besar', 'data' => 'total_satuanbesar_netto'],
    ];

    private function isAlreadyCompleted(): bool
    {
        $redis = Yii::$app->redis;
        $retry = 3;
        while ($retry-- > 0) {
            $data = $redis->executeCommand('GET', ['progress:' . $this->unique_str]);
            if ($data) {
                $decoded = json_decode($data, true);
                if (isset($decoded['progress']) && $decoded['progress'] >= 100) {
                    return true;
                }
            }
            usleep(200000);
        }
        return false;
    }

    protected function prosesGetData()
    {
        if ($this->isAlreadyCompleted()) {
            $this->publishMessage($this->getMessage('finish'), 100, 'finish');
            return;
        }

        try {
            $this->initiateColumn();
            $this->list_data = $this->getDataAttributes();
        } catch (\Throwable $e) {
            Yii::error($e, 'rabbitmq');
            $this->publishMessage("{$this->getMessage('error')}: {$e->getMessage()}", 0, 'error');
        }
    }

    protected function prosesExport()
    {
        if ($this->isAlreadyCompleted()) {
            $this->publishMessage($this->getMessage('finish'), 100, 'finish');
            return;
        }

        try {
            $filePath = $this->generateExcel();
            $this->uploadFile($filePath);

            $this->publishMessage($this->getMessage('finish'), 100, 'finish');
        } catch (\Throwable $e) {
            Yii::error($e, 'rabbitmq');
            $this->publishError("{$this->getMessage('error')}: {$e->getMessage()}");
        }
    }

    private function getDataAttributes()
    {
        $startTime = microtime(true);

        $query = $this->generateData();
        $total = (int) $query->count();
        if ($total === 0) {
            $this->publishMessage("Tidak ada data untuk diekspor.", 0, 'info');
            return [];
        }

        $batchSize = $total > 30000 ? 10000 : 1000;

        $tmpCache = [];
        $processed = 0;
        $lastProgress = 0;
        $progressMin = 20;
        $progressMax = 40;
        $message = $this->getMessage('running');

        try {
            foreach ($query->batch($batchSize) as $rows) {
                foreach ($rows as $row) {
                    $tmpCache[] = $row;
                }

                $processed += count($rows);

                $this->updateExportProgress(
                    $processed,
                    $message,
                    $total,
                    $progressMin,
                    $progressMax,
                    $startTime,
                    $lastProgress
                );

                unset($rows);
                gc_collect_cycles();
            }

            $elapsed = microtime(true) - $startTime;
            Yii::info(sprintf(
                'Ekspor selesai (%d data) dalam %.2f detik',
                $total,
                $elapsed
            ), 'export-progress');

            return $tmpCache;
        } catch (\Throwable $e) {
            Yii::error($e, 'error rabbitmq attribut');
            $this->publishMessage("{$this->getMessage('error')}: {$e->getMessage()}", 0, 'error');
            return [];
        }
    }

    private function generateData()
    {
        $date = date('Y-m-d');
        $request = $this->filter ?? [];
        $model = new LaporanStockInventoryFn();
        $query = LaporanStockInventoryFn::getData($date);

        if (!empty($request['advanced-filter'])) {
            $adv = $request['advanced-filter'];

            if (!empty($adv['tanggal_inventory'])) {
                $date = date('Y-m-d', strtotime($adv['tanggal_inventory']));
                $query = $model::getData($date);
            }

            if (!empty($adv['ruangan_nama'])) {
                $query->andWhere(['ruangan_nama' => $adv['ruangan_nama']]);
            }

            if (!empty($adv['jenis_obat'])) {
                $query->andWhere(['jenis_obat' => $adv['jenis_obat']]);
            }
        }

        return DocoRestActiveFilter::advancedFilter($model, $query, $request);
    }

    private function generateExcel()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        try {
            $query = $this->generateData();
            $total = (int) $query->count();
            $batchSize = 1000;
            $processed = 0;
            $progressMin = 40;
            $progressMax = 90;
            $path = 'web/uploads/' . $this->unique_str . '.xlsx';

            if ($total <= 0) {
                $this->publishMessage($this->getMessage('not-found'), 100);
                return null;
            } 

            $this->publishMessage("{$this->getMessage('start')} ({$total} baris)...", 35);
            $config = $this->generateExcelHeader();

            $isLargeExport = $total > 30000;

            if ($isLargeExport) {
                $batchSize = 3000;
                $this->exportUsingSpout($query, $batchSize, $config, $path, $total, $progressMin, $progressMax);
            } else {
                $this->exportUsingPhpSpreadsheet($query, $batchSize, $config, $path, $total, $progressMin, $progressMax);
            }

            gc_collect_cycles();
            $this->publishMessage($this->getMessage('saved'), 95);

            return $path;
        } catch (\Throwable $e) {
            $this->publishMessage("{$this->getMessage('error')}: {$e->getMessage()}", 0, 'error');
        }
    }

    private function exportUsingSpout($query, $batchSize, $config, $path, $total, $progressMin, $progressMax)
    {
        $this->publishMessage(sprintf("File export, total %s baris data", number_format($total, 0, ',', '.')), 40);

        $writer = WriterFactory::create(Type::XLSX); 
        $writer->openToFile($path);
        
        $titleRow = [$config['title']]; $writer->addRow($titleRow); 
        $writer->addRow(['']); 

        foreach ($config['headerInfo'] as $label => $value) { 
            $writer->addRow(["{$label} : {$this->sanitizeCellValue($value)}"]); 
        } 

        $writer->addRow(['']);
        $headers = array_map([$this, 'sanitizeCellValue'], $config['nameHeaderSkipped']); 
        array_unshift($headers, 'No');
        $writer->addRow($headers);

        $startTime = microtime(true);
        $lastProgress = 0;
        $processed = 0;
        $message = $this->getMessage('generated');
        $no = 1;

        foreach ($query->batch($batchSize) as $rows) {
            $batchRows = [];
            foreach ($rows as $row) {
                $data = [];
                $data[] = $no++;
                
                foreach ($this->showed_data as $col) {
                    $val = $row[$col['data']] ?? '';
                    $data[] = $val === null || $val === '' ? '-' : $val;
                }
                $batchRows[] = $data;
            }
            $writer->addRows($batchRows);
            $processed += count($batchRows);

            $this->updateExportProgress($processed, $message, $total, $progressMin, $progressMax, $startTime, $lastProgress);

            unset($batchRows, $rows);
            gc_collect_cycles();
        }

        $writer->close();
    }

    private function exportUsingPhpSpreadsheet($query, $batchSize, $config, $path, $total, $progressMin, $progressMax)
    {
        $this->publishMessage(sprintf("File export, total %s baris data", number_format($total, 0, ',', '.')), 40);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($config['totalColumns']); $sheet->mergeCells("A1:{$lastColumn}1"); 
        $sheet->setCellValue('A1', $config['title']); 
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16); 
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $rowIndex = 2;
        foreach ($config['headerInfo'] as $label => $value) {
            $sheet->setCellValue("A{$rowIndex}", "{$label} : {$value}");
            $rowIndex++;
        }
        
        $rowIndex += 1;
        $headers = array_merge(['No'], $config['nameHeaderSkipped']);
        foreach ($headers as $i => $headerName) {
            $sheet->setCellValueByColumnAndRow($i + 1, $rowIndex, $headerName);
        }

        $startTime = microtime(true);
        $processed = 0;
        $lastProgress = 0;
        $dataStartRow = $rowIndex + 1;
        $message = $this->getMessage('generated');
        $no = 1;

        foreach ($query->batch($batchSize) as $rows) {
            foreach ($rows as $row) {
                $colIndex = 1;
                
                $sheet->setCellValueByColumnAndRow($colIndex++, $dataStartRow, $no);
                $normalizedRow = [];
                foreach ($row as $k => $v) {
                    $normalizedRow[strtolower($k)] = $v;
                }

                foreach ($this->showed_data as $col) {
                    $key = strtolower($col['data']);
                    $value = $normalizedRow[$key] ?? '-';
                    $sheet->setCellValueByColumnAndRow($colIndex++, $dataStartRow, $value);
                }
                $no++;
                $dataStartRow++;
                $processed++;
            }

            $this->updateExportProgress($processed, $message, $total, $progressMin, $progressMax, $startTime, $lastProgress);
            gc_collect_cycles();
        }

        $writer = new StreamXlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);
        $this->publishMessage($this->getMessage('saved'), 95);
        $writer->save($path);

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
    }

    private function updateExportProgress(&$processed, $message, $total, $progressMin, $progressMax, $startTime, &$lastProgress)
    {
        $progress = $progressMin + (($processed / max(1, $total)) * ($progressMax - $progressMin));
        $progressFile = ($processed / max(1, $total)) * 100;

        if ($progress >= $lastProgress + 5 || $processed === $total) {
            $elapsed    = microtime(true) - $startTime;
            $speed      = $processed > 0 ? $elapsed / $processed : 0;
            $remaining  = max(0, ($total - $processed) * $speed);
            
            $remainingFormatted = $this->formatDuration($remaining);

            $this->publishMessage(
                sprintf(
                    $message,
                    number_format($processed, 0, ',', '.'),
                    number_format($total, 0, ',', '.'),
                    $progressFile, 
                    $remainingFormatted 
                ),
                min(90, round($progress, 1))
            );
            $lastProgress = $progress;
        }
    }

    private function formatDuration($seconds)
    {
        $seconds = (int) round($seconds);
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        $parts = [];
        if ($days > 0) {
            $parts[] = $days . ' hari';
        }
        if ($hours > 0) {
            $parts[] = $hours . ' jam';
        }
        if ($minutes > 0) {
            $parts[] = $minutes . ' menit';
        }
        if ($secs > 0 || empty($parts)) {
            $parts[] = $secs . ' detik';
        }

        return implode(' ', $parts);
    }

    private function generateExcelHeader()
    {
        $filter         = $this->filter ?? [];
        $advancedFilter = $filter['advanced-filter'] ?? [];
        $title          = 'LAPORAN STOCK INVENTORY';

        $headerInfo = [
            'Tanggal Inventory' => $advancedFilter['tanggal_inventory'] ?? date('Y-m-d'),
            'Nama Ruangan'      => $advancedFilter['ruangan_nama'] ?? '-',
            'Jenis Obat Alkes'  => $advancedFilter['jenis_obat'] ?? '-',
        ];

        $nameHeaderSkipped = array_column($this->showed_data ?? [], 'title');
        $totalColumns = count($nameHeaderSkipped);

        return [
            'title' => $title,
            'headerInfo' => $headerInfo,
            'nameHeaderSkipped' => $nameHeaderSkipped,
            'totalColumns' => $totalColumns, 
            'titleStyle' => [ 
                'font' => [
                    'bold' => true, 
                    'size' => 14, 
                    'uppercase' => true
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ], 
            ],
        ];
    }

    private function uploadFile($path)
    {
        $client = $this->setUrlLapStockInventory();
        $client->post('lap-stock-inventory/drop-file', [
            'query' => ['filePath' => $this->unique_str],
            'multipart' => [[
                'name' => 'file',
                'contents' => file_get_contents($path),
                'filename' => $this->unique_str
            ]]
        ]);
    }

    private function setUrlLapStockInventory()
    {
        $params = Yii::$app->params['iniFile'];
        $urlBackend = $params['rabbitMq']['url_backend'] ?? 'http://web:8858/';
        $header = [
            'Authorization' => $this->token,
            'user-agent' => 'cli',
            'X-Owner' => $this->xOwner,
        ];
        return new Client([
            'base_uri' => $urlBackend . 'gudang/v1/',
            'headers' => $header
        ]);
    }

    private function initiateColumn()
    {
        $this->publishMessage($this->getMessage('start'), 10);
        $this->setColumnShowed();
        usleep(300000);
        $this->publishMessage($this->getMessage('reading'), 20);
        $this->setColumnHide();
    }

    private function setColumnShowed()
    {
        $filter_showed = $this->filter['advanced-filter']['toggle'] ?? [];
        if (!empty($filter_showed)) {
            $filter_showed = array_merge(self::DEFAULT_DATA_SHOW, explode(',', $filter_showed));
            foreach ($filter_showed as $value) {
                $this->showed_data[$value] = self::DEFAULT_LIST_COLUMN[$value];
            }
        } else {
            $this->showed_data = self::DEFAULT_LIST_COLUMN;
        }
    }

    private function setColumnHide()
    {
        foreach (self::DEFAULT_HIDE_DATA as $value) {
            if (isset($this->showed_data[$value])) {
                unset($this->showed_data[$value]);
            }
        }
    }

    private function publishMessage($msg, $progress, $status = 'progress')
    {
        $data = [
            'status' => $status,
            'messageProcess' => $msg,
            'progress' => $progress,
            'filename' => $this->unique_str
        ];

        $redis = Yii::$app->redis;
        $redis->executeCommand('SET', ['progress:' . $this->unique_str, json_encode($data)]);
        $redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode($data, JSON_UNESCAPED_UNICODE),
        ]);
    }

    private function publishError($msg)
    {
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . ($this->unique_str ?: 'unknown'),
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => $msg,
                'progress' => 100,
            ]),
        ]);
    }

    private function getMessage($type) 
    {
        $messages = [
            'start'     => 'Memulai proses export...',
            'reading'   => 'Membaca file yang akan di export...',
            'not-found' => 'Tidak ada data untuk diexport.',
            'running'   => 'Memulai proses export...',
            'progress'  => 'Proses export sedang berjalan...',
            'generated' => 'Generate file export excel total %s dari %s baris... (%.1f%%), dengan estimasi %.1f detik tersisa',
            'saved'     => 'Menyimpan file ke direktori...',
            'finish'    => 'Export file berhasil, Silahkan download.',
            'error'     => 'Terjadi kesalahan saat proses export '. $this->title,
        ];

        return $messages[$type] ?? 'Proses export...';
    }

    private function sanitizeCellValue($value)
    {
        if (is_array($value)) {
            return implode(', ', array_map([$this, 'sanitizeCellValue'], $value));
        }

        if (is_object($value)) {
            return method_exists($value, '__toString')
                ? (string)$value
                : json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        if ($value === null || $value === '') {
            return '-';
        }

        if (is_bool($value)) {
            return $value ? 'TRUE' : 'FALSE';
        }

        if (is_string($value)) {
            $value = trim($value);

            if (preg_match('/^=/', $value)) {
                $value = ltrim($value, '=');
            }

            if (preg_match('/^"\s*=\s*(.*)"$/', $value, $matches)) {
                $value = $matches[1];
            }

            if (preg_match('/^[=+\-@]/', $value)) {
                $value = "'{$value}";
            }

            $value = preg_replace('/[[:^print:]]/', '', $value);

            if (mb_strlen($value) > 32000) {
                $value = mb_substr($value, 0, 32000) . '...';
            }

            return $value;
        }

        if (is_numeric($value) && strlen((string)$value) >= 16) {
            return "'{$value}";
        }

        return (string)$value;
    }
}
