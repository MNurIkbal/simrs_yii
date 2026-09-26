<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanSepuluhBesarPenyakitView;
use app\modules\v1\models\Diagnosa;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use yii\helpers\Url;

class LapSepuluhBesarPenyakitController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanSepuluhBesarPenyakitView';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex()
    {
        $model = new LaporanSepuluhBesarPenyakitView;
        $query = $model::find()->select(['tglmorbiditas', 'instalasi_nama', 'ruangan_nama', 
                'diagnosa_nama', 'diagnosa_kode', 'klasifikasidiagnosa_nama', 'COUNT(diagnosa_id) as total']);

        $between = false;
        $start = date('Y-m-01 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglmorbiditas'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglmorbiditas']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglmorbiditas']);
                $between = true;
            }
        }
        if($between) {
            $query->andWhere(['between', 'tglmorbiditas', $start, $end]);
        }

        $query->groupBy(['instalasi_nama', 'ruangan_nama', 'diagnosa_nama', 'diagnosa_kode', 'klasifikasidiagnosa_nama', 
            'tglmorbiditas']);

        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionListDiagnosa()
    {
        try {
            $request = Yii::$app->request;

            $data = Diagnosa::find()->select([
                "diagnosa_id",
                "diagnosa_kode",
            ])->asArray()->all();

            return [
                'data' => $data
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGenerateApi()
    {
        $modelInstalasi = new Instalasi;
        $queryInstalasi = $modelInstalasi::find();

        $queryInstalasi = DocoRestActiveFilter::advancedFilter($modelInstalasi, $queryInstalasi);
        $queryInstalasi = new ActiveDataProvider([
            'query' => $queryInstalasi,
        ]);

        // ruangan
        $modelRuangan = new Ruangan;
        $queryRuangan = $modelRuangan::find();

        $queryRuangan = DocoRestActiveFilter::advancedFilter($modelRuangan, $queryRuangan);
        $queryRuangan = new ActiveDataProvider([
            'query' => $queryRuangan,

        ]);

        // diagnosa
        $modelDiagnosa = new Diagnosa;
        $queryDiagnosa = $modelDiagnosa::find();

        $queryDiagnosa = DocoRestActiveFilter::advancedFilter($modelDiagnosa, $queryDiagnosa);
        $queryDiagnosa = new ActiveDataProvider([
            'query' => $queryDiagnosa,

        ]);

        return [
            'instalasi' => $queryInstalasi->getModels(),
            'ruangan' => $queryRuangan->getModels(),
            'diagnosa' => $queryDiagnosa->getModels(),
        ];
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $body = json_decode($request->post('body'), true);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(25);

        $styleExcel = array(
            [
                'font' => [
                    'bold' => true,
                    'size' => 10,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['rgb' => 'dddddd']
                ],
                'borders' => [
                    'allborders' => [
                        'style' => \PhpOffice\PhpSpreadsheet\Style\BORDER::BORDER_THICK
                    ]
                ]
            ],
            [
                'font' => [
                    'bold' => true,
                    'size' => 10,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                ],
                'borders' => [
                    'allborders' => [
                        'style' => \PhpOffice\PhpSpreadsheet\Style\BORDER::BORDER_THICK
                    ]
                ]
            ],
            [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['rgb' => 'aaaaaa']
                ],
                'borders' => [
                    'allborders' => [
                        'style' => \PhpOffice\PhpSpreadsheet\Style\BORDER::BORDER_THICK
                    ]
                ]
            ],
            [
                'font' => [
                    'bold' => true,
                    'size' => 20,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                ],
                'borders' => [
                    'allborders' => [
                        'style' => \PhpOffice\PhpSpreadsheet\Style\BORDER::BORDER_THICK
                    ]
                ]
            ]
        );

        $sheet->mergeCells('C2:F2')
                 ->setCellValue('C2', 'Laporan 10 Besar Penyakit')
                 ->getStyle('C2')
                 ->applyFromArray($styleExcel[3]);

        $sheet->setCellValue('A4', 'NO')
                 ->getStyle('A4')
                 ->applyFromArray($styleExcel[2]);
        $sheet->setCellValue('B4', 'Kode Diagnosa')
                 ->getStyle('B4')
                 ->applyFromArray($styleExcel[2]);
        $sheet->setCellValue('C4', 'Nama Diagnosa')
                 ->getStyle('C4')
                 ->applyFromArray($styleExcel[2]);
        $sheet->setCellValue('D4', 'Klasifikasi Diagnosa')
                 ->getStyle('D4')
                 ->applyFromArray($styleExcel[2]);
        $sheet->setCellValue('E4', 'Jumlah Kasus')
                 ->getStyle('E4')
                 ->applyFromArray($styleExcel[2]);
        
        $content = $spreadsheet->setActiveSheetIndex(0);
        $no = 1;
        $counter = 5;

        if (!empty($body)) {
            foreach ($body as $key => $value) {
                $content->setCellValue("A" . $counter, $no)->getStyle('A' . $counter)->applyFromArray($styleExcel);
                $content->setCellValueExplicit("B" . $counter, $value['diagnosa_kode'], 
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)->getStyle('B' . $counter)
                    ->applyFromArray($styleExcel);

                $content->setCellValueExplicit("C" . $counter, $value['diagnosa_nama'], 
                        \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)->getStyle('C' . $counter)
                        ->applyFromArray($styleExcel);

                $content->setCellValueExplicit("D" . $counter, $value['klasifikasidiagnosa_nama'],
                        \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)->getStyle('D' . $counter)
                        ->applyFromArray($styleExcel);

                $content->setCellValueExplicit("E" . $counter, $value['total'],
                        \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC)->getStyle('E' . $counter)
                        ->applyFromArray($styleExcel);
                $no++;
                $counter++;
            }
        }

        $filename = 'Laporan-Sepuluh-Besar-Penyakit.xls';
        $writer = new Xlsx($spreadsheet);
        
        // $path = Yii::getAlias('@rm').'\\web\\download';

        // if(!file_exists($path)) {
        //     mkdir($path, 0775, true);
        // }

        $writer->save($filename);

        $link = 'http://rm.localhost/'.$filename;

        return $link;
    }

    
}