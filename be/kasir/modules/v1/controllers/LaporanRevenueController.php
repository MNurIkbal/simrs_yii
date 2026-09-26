<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanRevenueView;
use app\modules\v1\models\LaporanDetailRevenueView;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;
use Doco\Services\InternalService;
use app\modules\v1\models\UploadForm;
use yii\web\UploadedFile;

class LaporanRevenueController extends DocoActiveController
{
    const COVID_PCR = '*     PCR_COVID19';
    const COVID_NONPCR = '*     NON_PCR';
    public $modelClass = 'app\modules\v1\models\LaporanRevenueView';
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
        $request = Yii::$app->request;
        $model = new LaporanRevenueView;
        $query = $model::find();
        $start = date('Y-m-01');
        $end = date('Y-m-d');

        if($request->get('jenis_periode') == "date_range") {
            if($request->get('range_tanggal')) {
                $explode = explode(" - ", $request->get('range_tanggal'));
                if(count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }
            }
        }
        else {
            if($request->get('range_bulan')) {
                $rangeBulan = $request->get('range_bulan');
                $explode = explode("-", $rangeBulan);
                $maxDate = cal_days_in_month(CAL_GREGORIAN, $explode[1], $explode[0]);
                $start = "01-".$explode[1].'-'.$explode[0];
                $start = date('Y-m-d', strtotime($start));
                $end = $maxDate.'-'.$explode[1].'-'.$explode[0];
                $end = date('Y-m-d', strtotime($end));
            }
        }
        
        if($request->get('kategori') && !empty($request->get('kategori'))) {
            $query->andWhere(['tipe' => $request->get('kategori')]);
        }

        if($request->get('unit') && $request->get("unit") != "null") {
            $query->andWhere(['unit' => $request->get('unit')]);
        }
        
        $query->andWhere(['between', 'tanggal', $start, $end]);
        return $query->asArray()->all();
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $title = 'Laporan Revenue';
        try {
            $jenis_periode = $request->get('jenis_periode', "date_range");
            $range_tanggal = $request->get('range_tanggal', null);
            $range_bulan = $request->get('range_bulan', null);
            $kategori = $request->get('kategori', null);
            $unit = $request->get('unit', null);

            $model = new LaporanRevenueView;
            $query = $model::find();
            $start = date('Y-m-01');
            $end = date('Y-m-d');

            if($jenis_periode == "date_range") {
                if($range_tanggal) {
                    $explode = explode(" - ", $range_tanggal);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d', strtotime($explode[0]));
                        $end = date('Y-m-d', strtotime($explode[1]));
                    }
                }
            }
            else {
                if($range_bulan) {
                    $rangeBulan = $range_bulan;
                    $explode = explode("-", $rangeBulan);
                    $maxDate = cal_days_in_month(CAL_GREGORIAN, $explode[1], $explode[0]);
                    $start = "01-".$explode[1].'-'.$explode[0];
                    $start = date('Y-m-d', strtotime($start));
                    $end = $maxDate.'-'.$explode[1].'-'.$explode[0];
                    $end = date('Y-m-d', strtotime($end));
                }
            }

            $subTitle = date('d-M-Y', strtotime($start)) .' s/d '. date('d-M-Y', strtotime($end));

            if($kategori && !empty($kategori)) {
                $query->andWhere(['tipe' => $kategori]);
            }

            if($unit && $unit != "null") {
                $query->andWhere(['unit' => $unit]);
            }
            
            $query->andWhere(['between', 'tanggal', $start, $end]);
            $query = $query->all();

            $data = $footer = $result = [];
            $header = [
                'Kategori' => $kategori,
                'Unit' => $unit,
            ];
            $options = ["subTitle" => "Periode : ".$subTitle];
            $listHeader = $request->get('listHeader');

            if (!empty($query)) {
                $counter = 0;
                foreach ($query as $index => $value) {
                    $tanggal = $value['tanggal'];
                    $exp = explode("-", $tanggal);
                    $str = $exp[0].'-'.$exp[1].'-';
                    $date = $exp[2];
                    if(!empty($value['unit'])) {
                        $newData[$value['unit']][$date] = $value['total'];
                        if($value['unit'] == self::COVID_NONPCR || $value['unit'] == self::COVID_PCR) {
                            $value['total'] = 0;
                        }
                        $groupedData[$date][$value['tipe']][$value['unit']] = $value['total'];
                    }
                }
            }

            foreach ($listHeader as $kHeader => $vHeader) {
                $row['code'] = $vHeader['code'];
                $row['title'] = $vHeader['title'];
                $row['unit'] = $vHeader['title'];
                $row['is_group'] = $vHeader['is_group'];
                $row['is_kategori'] = $vHeader['is_kategori'];
                $row['is_total'] = $vHeader['is_total'];

                for($j=1; $j <= 31; $j++) {
                    $k = ($j < 10) ? "0".$j : $j;
                    if($vHeader['is_group']) {
                        $total = "";
                        if($vHeader['code'] == "total_lob") {
                            $totalLob = "";
                            if(isset($groupedData[$k][$vHeader['parent']])) {
                                $summaryLob = $groupedData[$k][$vHeader['parent']];
                                foreach ($summaryLob as $vSumLob) {
                                    $totalLob += $vSumLob;
                                }
                            }
                            $total = $totalLob; // total LOB
                            $groupedData[$k]['summaryLob'] = $total;
                        }
                        elseif($vHeader['code'] == 'total_los') {
                            $totalLos = "";
                            if(isset($groupedData[$k][$vHeader['parent']])) {
                                $summaryLos = $groupedData[$k][$vHeader['parent']];
                                foreach ($summaryLos as $vSumLos) {
                                    $totalLos += $vSumLos;
                                }
                            }
                            $total = $totalLos; // total LOS
                            $groupedData[$k]['summaryLos'] = $total;
                        }
                        elseif($vHeader['code'] == 'discount') {
                            $totalDiscount = 0;
                            $total = $totalDiscount; // discount
                            if($total == 0) {
                                $total = "-";
                            }
                        }
                        elseif($vHeader['code'] == 'total_revenue') {
                            $totalRevenue = (($groupedData[$k]['summaryLob'] + $groupedData[$k]['summaryLos']));
                            $total = $totalRevenue; // total revenue
                            $groupedData[$k]['summaryLobLos'] = $total;
                            if($total == 0) {
                                $total = "-";
                            }
                        }
                        elseif($vHeader['code'] == 'total_payer') {
                            $totalPayer = "";
                            if(isset($groupedData[$k][$vHeader['parent']])) {
                                $summaryPayer = $groupedData[$k][$vHeader['parent']];
                                foreach ($summaryPayer as $vSumPayer) {
                                    $totalPayer += $vSumPayer;
                                }
                            }
                            $total = $totalPayer; // total PAYER
                            $groupedData[$k]['summaryPayer'] = $total;
                        }
                    }
                    else {
                        $total = "-";
                        if(isset($newData[$vHeader['code']][$k])) {
                            $total = $newData[$vHeader['code']][$k];
                        }
                    }

                    $row[$k] = $total;
                }

                $data[$kHeader] = $row;
            }
            
            if (!empty($data)) {
                $no = 1;
                foreach ($data as $index => $value) {
                    $newValue = [];
                    $newValue['DESCRIPTIONS'] = $value['title'];
                    for ($i=1; $i <= 31; $i++) {
                        $k = ($i < 10) ? "0".$i : $i;
                        $totalRevenue = is_numeric($value[$k]) ? DocoHelpers::formatNumber($value[$k]). " " : $value[$k];
                        $totalRevenue = $value[$k]; // Untuk kebutuhan SUM excel
                        $newValue[$i] = $totalRevenue;
                    }
                    $result[$index] = $newValue;
                    $no++;
                }
            }

            $filePath = DocoHelpers::exportExcel($title, $result, $header, $options, $footer, [], true);
            $filePath->save('php://output');
            die;
        } catch (Exception $e) {
            
        }
        return $request->get();
    }

    public function actionExportExcelDetail()
    {
        $model = new LaporanDetailRevenueView;
        $query = $model::find(true);
        $start = date('Y-m-01');
        $end = date('Y-m-d');
        $title = 'Laporan Revenue Detail';
        $request = Yii::$app->request;

        $jenis_periode = $request->get('jenis_periode', "date_range");
        $range_bulan = $request->get('range_bulan', null);
        $kategori = $request->get('kategori', null);
        $unit = $request->get('unit', null);
        $range_tanggal = $request->get('range_tanggal', null);

        if($jenis_periode == "date_range") {
            if($range_tanggal) {
                $explode = explode(" - ", $range_tanggal);
                if(count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }
            }
        }
        else {
            if($range_bulan) {
                $rangeBulan = $range_bulan;
                $explode = explode("-", $rangeBulan);
                $maxDate = cal_days_in_month(CAL_GREGORIAN, $explode[1], $explode[0]);
                $start = "01-".$explode[1].'-'.$explode[0];
                $start = date('Y-m-d', strtotime($start));
                $end = $maxDate.'-'.$explode[1].'-'.$explode[0];
                $end = date('Y-m-d', strtotime($end));
            }
        }

        $subTitle = date('d-M-Y', strtotime($start)) .' s/d '. date('d-M-Y', strtotime($end));

        if($kategori && !empty($kategori)) {
            $query->andWhere(['tipe' => $kategori]);
        }

        if($unit && $unit != "null") {
            $query->andWhere(['unit' => $unit]);
        }
        
        $query->andWhere(['between', 'tanggal', $start, $end]);
        //$query = $query->all();

        $data = $footer = $result = [];
        $header = [
            'Kategori' => $kategori,
            'Unit' => $unit,
        ];
        $options = ["subTitle" => "Periode : ".$subTitle];
        $listHeader = $request->get('listHeader');

        $data = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->asArray()->all();
        foreach ($data as $key => $value) {
            $value['tanggal'] = date("j M Y", strtotime($value['tanggal']));
            $newValue = [];
            $newValue[\Yii::t('app', 'Tipe')] = $value['tipe'];
            $newValue[\Yii::t('app', 'Unit')] = $value['unit'];
            $newValue[\Yii::t('app', 'Tanggal')] = $value['tanggal'];
            $newValue[\Yii::t('app', 'No Pendaftaran')] = $value['no_pendaftaran'];
            $newValue[\Yii::t('app', 'No Rekam Medik')] = $value['no_rekam_medik'];
            $newValue[\Yii::t('app', 'Nama Pasien')] = $value['nama_pasien'];
            $newValue[\Yii::t('app', 'Cara Bayar')] = $value['carabayar_nama'];
            $newValue[\Yii::t('app', 'Nama Tindakan/Obat')] = $value['tindakan_obat_paket'];
            $newValue[\Yii::t('app', 'Satuan')] = $value['satuanunit_nama'];
            $newValue[\Yii::t('app', 'Harga Satuan')] = $value['harga_satuan'];
            $newValue[\Yii::t('app', 'Qty')] = $value['qty'];
            $newValue[\Yii::t('app', 'Total')] = $value['total'];
            $newValue[''] = '';
            $result[$key] = $newValue;
        }

        $unit =  ($unit != 'null') ? $unit : '-';
        $header = [
            Yii::t("app", "Periode") => $start . ' - ' . $end,
            Yii::t("app", "Unit") => $unit,
        ];

        $filePath = DocoHelpers::exportExcel($title, $result, $header, array(
            "uploadPath" => "./uploads",
        ), [], [], true);

        $filePath->save('php://output');
        die;
    }

    public function actionExportExcelBgprocess() 
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        $randString = isset($get['randString']) ? $get['randString'] : null;
        $headerExcel = [];

        $model = new LaporanDetailRevenueView;
        $query = $model::find(true);
        $start = date('Y-m-01');
        $end = date('Y-m-d');
        $title = 'Laporan Revenue Detail';
        $periodeHeader = '';

        $jenis_periode = $request->get('periode_tanggal', "date_range");
        $range_bulan = $request->get('periode_bulan', null);
        $kategori = $request->get('kategori', null);
        $unit = $request->get('unit', null);
        $range_tanggal = $request->get('range_tanggal', null);
        if($jenis_periode == "date_range") {
            if($range_tanggal) {
                $explode = explode(" - ", $range_tanggal);
                if(count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }
            }
        }
        else {
            if($range_bulan) {
                $rangeBulan = $range_bulan;
                $explode = explode("-", $rangeBulan);
                $maxDate = cal_days_in_month(CAL_GREGORIAN, $explode[1], $explode[0]);
                $start = "01-".$explode[1].'-'.$explode[0];
                $start = date('Y-m-d', strtotime($start));
                $end = $maxDate.'-'.$explode[1].'-'.$explode[0];
                $end = date('Y-m-d', strtotime($end));
            }
        }


        $subTitle = date('d-M-Y', strtotime($start)) .' s/d '. date('d-M-Y', strtotime($end));
        $periodeHeader = ($jenis_periode == "date_range") ? $subTitle : $range_bulan;
        $jenisPeriodeHeader = ($jenis_periode == "date_range") ? 'Per Tanggal(Date Range)' : 'Per Bulan & Tahun';

        if($kategori && !empty($kategori)) {
            $query->andWhere(['tipe' => $kategori]);
        }

        if($unit && $unit != "null") {
            $query->andWhere(['unit' => $unit]);
        }
        
        $query->andWhere(['between', 'tanggal', $start, $end]);
        $data = $query->asArray()->all();

        $headerExcel = [
            'Periode' => $jenisPeriodeHeader,
            'Periode Bulan/Tanggal' => $periodeHeader,
            'Kategori' => $kategori,
            'Unit' => $unit,
        ];
        $options = ["subTitle" => "Periode : ".$subTitle];
        $listHeader = $request->get('listHeader');
        
        $countData = count($data);
        $totalPerPage = count($data);
        $options = [
            "skipIncrement" => true,
            "customHeader" => [],
        ];
        
        $uri_kasir = Yii::$app->docoRest->getBaseUri('kasir');
        $params = [
            'sendToUrl' => 'laporan-revenue/drop-file',
            'base_uri' => $uri_kasir,
        ];

        (new InternalService)->sendTo([
            'Sirs' => [
                'DataExportExcelRevenue' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $get,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'ExportExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'filter' => $get,
                    'title' => 'Laporan Detail Revenue',
                    'headerExcel' => $headerExcel,
                    'footer' => [],
                    'options' => $options,
                    'customData' => true,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'UploadExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'params' => $params
                ]
            ]
        ], true);

        return [
            'totalPerPage' => $totalPerPage,
            'unique_str' => $randString,
            'countData' => $countData,
        ];
    }

    public function actionDropFile()
    {
        $request = Yii::$app->request;
        $model = new UploadForm;
        
        $filePath = $request->get('filePath', null);
        if ($request->isPost) 
        {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName.'.'.$ext;
            
            $path = "uploads/";
            // if (!file_exists($path)) mkdir($path, 0755, true);
            // $path = $filePath;

        $nameFile = $path .'/'. $model->file;
            if ($files->saveAs($nameFile)) {
                return [
                    'path' => $path,
                    'message' => 'upload file berhasil!'
                ];
            }
        }
        return [
            'status' => 422,
            'message' => 'upload file gagal!'
        ];
    }

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        // $dir = $rootPath.'/'.$no_request;
        $fileName = $rootPath.'/' . $no_request . '.xlsx';
        DocoHelpers::downloadFileExcel($fileName);
    }
}
