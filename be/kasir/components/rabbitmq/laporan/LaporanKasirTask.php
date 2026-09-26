<?php

namespace app\components\rabbitmq\laporan;

use Doco\rabbitmq\task\ReportTask;
use Doco\components\DocoHelpers;
use Yii;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Client;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use app\modules\v1\models\KonfigLaporan;

class LaporanKasirTask extends ReportTask
{
    protected $header;
    protected $headerExcel;
    protected $title;
    protected $totalPerPage;
    protected $countData;
    protected $manual_excel;
    protected $list_data = [];

    /** proses get Data */
    protected function prosesGetData()
    {
        sleep(1);
        $this->list_data = $this->getDataAttibutes($this->filter);
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
			'message' => json_encode([
				 'status' => 'finish', 
				 'messageProcess' => 'Berhasil menyiapkan data.',
				 'progress' => 70
			 ]),
        ]);
    }

    /** proses export */
    protected function prosesExport()
    {
        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang menyiapkan file excel.',
                'progress' => 80
            ]),
        ]);
        
        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengimport data ke dalam excel.',
                'progress' => 85
            ]),
        ]);

        $this->generateExcel();

        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Proses import excel berhasil.',
                'progress' => 90
            ]),
        ]);

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
            'message' => json_encode([
                 'status' => 'finish', 
                 'messageProcess' => 'Proses berhasil.',
                 'progress' => 100,
                 'filename' => $this->unique_str,
                 'jenis_laporan' => $this->filter['jenis_laporan'],
                 'range_tanggal' => $this->filter['range_tanggal']
             ]),
         ]);
    }

    private function getDataAttibutes()
    {
        try {
            $range_tanggal = ArrayHelper::getValue($this->filter, 'range_tanggal');
            $jenis_laporan = ArrayHelper::getValue($this->filter, 'jenis_laporan');
            $start = date('Y-m-01 00:00:00');
            $end = date('Y-m-d 23:59:59');
            
            if ($range_tanggal) {
                $explode = explode(" - ", $range_tanggal);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
            }
            $source = KonfigLaporan::find()->where(['jenis_laporan' => $jenis_laporan])->asArray()->all();
            $source = isset($source[0]) ? $source[0] : [];
            $source_view = ArrayHelper::getValue($source, 'source_view');
            $fieldWhere = ArrayHelper::getValue($source, 'filter');

            if (!empty(ArrayHelper::getValue($source, 'additional_data', []))) {
                $additional_data = json_decode(ArrayHelper::getValue($source, 'additional_data', []), true);
                if (isset($additional_data['is_function']) && $additional_data['is_function']) {
                    $source_view = "{$source_view}(:dateStart, :dateEnd)";
                    $query = $this->dbConnection()->createCommand("SELECT * FROM {$source_view}");
                } else {
                    $query = $this->dbConnection()->createCommand("SELECT * FROM {$source_view} WHERE {$fieldWhere} BETWEEN :dateStart AND :dateEnd");
                }
            } else {
                $query = $this->dbConnection()->createCommand("SELECT * FROM {$source_view} WHERE {$fieldWhere} BETWEEN :dateStart AND :dateEnd");
            }

            $query->bindValue(':dateStart', $start);
            $query->bindValue(':dateEnd', $end);
            return $query->queryAll();       
        } catch (\Exception $e) {
            $this->logError($e);
            return [
                'message' => $e->getMessage()
            ];
		}
    }

    private function generateExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->mergeCells('A1:Z1');
        $sheet->setCellValue("A1", $this->title);
        $row = 2;
        foreach ($this->headerExcel as $key => $val) {
            $sheet->mergeCells("A{$row}:Z{$row}");
            $sheet->setCellValue("A{$row}", $key . " : {$val}");
            $row++;
        }
        
        if(!empty($this->footerExcel)){
            $this->data = count($this->getDataAttibutes($this->filter)) + 6;
            $styleFooter = array(
                'font' => array(
                    'bold' => true,
                ),
            );
            $sheet->getStyle('A'.$this->data.':Z'.$this->data)->applyFromArray($styleFooter);

            foreach (json_decode($this->footerExcel) as $key => $val) {
                $sheet->setCellValue("{$key}".$this->data, '='."{$val}".'('.$key.'5:'.$key . $this->data . ')'); 
            }
        }

        $styleTitle = array(
            'font' => array(
                'bold' => true,
                'size' => 16,
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ),
        );
        $sheet->getStyle('A1:Z1')->applyFromArray($styleTitle);

        $styleTitleHeader = array(
            'font' => array(
                'bold' => true,
                'size' => 11,
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ),
        );
        $sheet->getStyle('A4:Z4')->applyFromArray($styleTitleHeader);
        foreach (range('A', 'W') as $size) {            
            $sheet->getColumnDimension($size)->setAutoSize(true);
        }

        if (isset($this->list_data[0])) {
            $headers = array_keys($this->list_data[0]); // get headers from source array
            foreach ($headers as $key => $str) {
                $headers[$key] = str_replace('_', ' ', $str);
            }
            array_unshift($this->list_data, array_map('ucwords', $headers)); 
        }

        $timer = microtime(true);
        $sheet->fromArray(
            $this->list_data,
            null,
            'A4'
        );
        
        $writer = new Xlsx($spreadsheet);
        $filePath = 'web/'.'uploads/'. $this->unique_str .'.xlsx';
        $writer->save($filePath);
        $foo = 'elapsed time: '. round(microtime(true) - $timer, 3). ' sec';
        Yii::error($foo);
    }
}