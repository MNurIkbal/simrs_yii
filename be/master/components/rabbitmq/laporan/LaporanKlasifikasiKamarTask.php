<?php

namespace app\components\rabbitmq\laporan;

use Doco\rabbitmq\task\ReportTask;
use app\modules\v1\models\KlasifikasiKamarView;
use Doco\components\DocoHelpers;
use Yii;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Client;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LaporanKlasifikasiKamarTask extends ReportTask
{
    protected $header;
    protected $headerExcel;
    protected $customData;
    protected $footer;
    protected $title;
    protected $totalPerPage;
    protected $countData;

    protected $list_data = [];

    /** proses get Data */
    protected function prosesGetData()
    {
        sleep(1);
        $this->list_data = $this->getDataAttibutes();
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

        $options = [
            "skipIncrement" => true,
            "customHeader" => $this->custHeader()
        ];

        $filePath = DocoHelpers::exportExcel($this->title, $this->list_data, $this->headerExcel, $options, [], [], true);
        $path = 'web/'.'uploads/'. $this->unique_str .'.xlsx';
        $filePath->save($path);
        // $this->generateExcel();

        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Proses import excel berhasil.',
                'progress' => 90
            ]),
        ]);

        if (file_exists($path)) {
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'export-excel:'.$this->unique_str,
                'message' => json_encode([
                     'status' => 'finish', 
                     'messageProcess' => 'Proses berhasil.',
                     'progress' => 100,
                     'filename' => $this->unique_str
                 ]),
            ]);
            Yii::error($this->title. ' Berhasil Di Download');
        }
    }

    private function getDataAttibutes()
    {
        try {
            $advance_filter = ArrayHelper::getValue($this->filter, 'advanced-filter', []);
            $model = new KlasifikasiKamarView;
            $query = $model::find();
            $query->select([
                'klasifikasikamar_nama', 'sirsonline_nama', 'eiscovid_nama', 'namakelas_aplicare', 'spgdt_nama', 'is_active'
            ]);

            $klasifikasikamar_nama = ArrayHelper::getValue($advance_filter, 'klasifikasikamar_nama');
            $sirsonline_nama = ArrayHelper::getValue($advance_filter, 'sirsonline_nama');
            $eiscovid_nama = ArrayHelper::getValue($advance_filter, 'eiscovid_nama');
            $namakelas_aplicare = ArrayHelper::getValue($advance_filter, 'namakelas_aplicare');
            $spgdt_nama = ArrayHelper::getValue($advance_filter, 'spgdt_nama');
            $is_active = ArrayHelper::getValue($advance_filter, 'is_active');

            if ($klasifikasikamar_nama) {
                $query->andWhere(['ILIKE', 'LOWER(klasifikasikamar_nama)', strtolower($klasifikasikamar_nama)]);
            }
            if ($sirsonline_nama) {
                $query->andWhere(['sirsonline_nama' => $sirsonline_nama]);
            }
            if ($eiscovid_nama) {
                $query->andWhere(['eiscovid_nama' => $eiscovid_nama]);
            }
            if ($namakelas_aplicare) {
                $query->andWhere(['namakelas_aplicare' => $namakelas_aplicare]);
            }
            if ($spgdt_nama) {
                $query->andWhere(['spgdt_nama' => $spgdt_nama]);
            }
            if ($is_active) {
                $is_active = $is_active == 1 ? true : false;
                $query->andWhere(['is_active' => $is_active]);
            }
            $data = $query->asArray()->all();
            
            $result = [];
            $no = 1;
            foreach($data as $key => $value) {
                $newData['No'] = $no++;
                $newData['Nama Klasifikasi'] = ArrayHelper::getValue($value, 'klasifikasikamar_nama');
                $newData['SIRS Online'] = ArrayHelper::getValue($value, 'sirsonline_nama');
                $newData['EIS Covid'] = ArrayHelper::getValue($value, 'eiscovid_nama');
                $newData['Aplicare'] = ArrayHelper::getValue($value, 'namakelas_aplicare');
                $newData['SPGDT'] = ArrayHelper::getValue($value, 'spgdt_nama');
                $newData['Status'] = !empty($value['is_active']) ? 'Aktif' : 'Tidak Aktif';

                $result[] = $newData;
            }
            return $result;
		} catch (\Exception $e) {
            $this->logError($e);
            return [
                'message' => $e->getMessage()
            ];
		}
    }

    private function custHeader() 
    {	
        return [
            [
                [
                    'label'=>'No',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Nama Klasifikasi',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'SIRS Online',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'EIS Covid',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Aplicare',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'SPGDT',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Status',
                    'rowspan'=>2,
                ],
            ]
        ];
    }

    private function generateExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->mergeCells('A1:W1');
        $sheet->setCellValue("A1", $this->title);
        $row = 2;
        foreach ($this->headerExcel as $key => $val) {
            $sheet->mergeCells("A{$row}:W{$row}");
            $sheet->setCellValue("A{$row}", $key . " : {$val}");
            $row++;
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
        $sheet->getStyle('A1:W1')->applyFromArray($styleTitle);

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
            'A7'
        );
        
        $writer = new Xlsx($spreadsheet);
        $filePath = 'web/'.'uploads/'. $this->unique_str .'.xlsx';
        $writer->save($filePath);
        $foo = 'elapsed time: '. round(microtime(true) - $timer, 3). ' sec';
        Yii::error($foo);
    }
}