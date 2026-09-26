<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\LaporanPerbandinganKlaimView;
use app\modules\v1\models\SyKunjunganPasien;
use app\modules\v1\models\SyKunjunganView;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Instalasi;

class LaporanPerbandinganKlaimController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanPerbandinganKlaimView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    public function actionGetDataFilter()
    {
        
        $request = Yii::$app->request;
        $instalasi = $ruangan = $status_kunjungan = $penjamin = [];

        $dataRuangan = Ruangan::find()->all();
        if ($dataRuangan) {
            foreach ($dataRuangan as $key => $value) {
                if (!isset($ruangan[$value->ruangan_namalainnya])) {
                    $ruangan[$value->ruangan_namalainnya] = [
                        'id' => $value->ruangan_namalainnya,
                        'label' => $value->ruangan_nama
                    ];
                }
            }
        }

        $dataInstalasi = Instalasi::find()->all();
        if ($dataInstalasi) {
            foreach ($dataInstalasi as $key => $value) {
                if (!isset($instalasi[$value->instalasi_singkatan])) {
                    $ruangan[$value->instalasi_singkatan] = [
                        'id' => $value->instalasi_singkatan,
                        'label' => $value->instalasi_nama
                    ];
                }
            }
        }

        return $response = [
            'instalasi' => ArrayHelper::map($instalasi, 'id', 'label'),
            'ruangan'   => ArrayHelper::map($ruangan, 'id', 'label'),
            'status_verifikasi'    => DocoConstants::$status_verifikasi,
        ];
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $model = new LaporanPerbandinganKlaimView;
        $query = $model::find();
        
        $start = date('Y-m-d');
        $end = date('Y-m-d');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_masuk'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_masuk']);
                if(count($explode) == 2) {
                    $date_start = date('Y-m-d', strtotime($explode[0]));
                    $date_end = date('Y-m-d', strtotime($explode[1]));

                    $query->andWhere(['between', 'tgl_masuk', $date_start, $date_end]);
                }
                unset($_GET['advanced-filter']['tgl_masuk']);
                
            }
            if(isset($_GET['advanced-filter']['tgl_pulang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pulang']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pulang']);
            }
            if(isset($_GET['advanced-filter']['tgl_verifikasi'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_verifikasi']);
                if(count($explode) == 2) {
                    $date_start = date('Y-m-d', strtotime($explode[0]));
                    $date_end = date('Y-m-d', strtotime($explode[1]));

                    $query->andWhere(['between', 'tgl_verifikasi', $date_start, $date_end]);
                }
                unset($_GET['advanced-filter']['tgl_verifikasi']);   
            }
        }
        $query->andWhere(['between', 'tgl_pulang', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionExportExcel()
    {
        $result = [];
        $header = [];
        $footer = [];
        $dataFoot = [];

        $request = Yii::$app->request;
        $model = new LaporanPerbandinganKlaimView;
        $query = $model::find();

        $start = date('Y-m-d');
        $end = date('Y-m-d');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_masuk'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_masuk']);
                if(count($explode) == 2) {
                    $date_start = date('Y-m-d', strtotime($explode[0]));
                    $date_end = date('Y-m-d', strtotime($explode[1]));

                    $query->andWhere(['between', 'tgl_masuk', $date_start, $date_end]);
                }
                unset($_GET['advanced-filter']['tgl_masuk']);
                
            }
            if(isset($_GET['advanced-filter']['tgl_pulang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pulang']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pulang']);
            }
            if(isset($_GET['advanced-filter']['tgl_verifikasi'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_verifikasi']);
                if(count($explode) == 2) {
                    $date_start = date('Y-m-d', strtotime($explode[0]));
                    $date_end = date('Y-m-d', strtotime($explode[1]));

                    $query->andWhere(['between', 'tgl_verifikasi', $date_start, $date_end]);
                }
                unset($_GET['advanced-filter']['tgl_verifikasi']);   
            }
        }

        $query->andWhere(['between', 'tgl_pulang', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        $data = $query->all();
        if ($data) {
            foreach ($data as $key => $value) {
                $topup = '';
                if ($value['topup_1']) {
                    $topup .= $value['topup_1'];
                }
                if ($value['topup_2']) {
                    if ($topup) {
                        $topup .= ', ';
                    } 
                    $topup .= $value['topup_2'];
                }
                if ($value['topup_3']) {
                    if ($topup) {
                        $topup .= ', ';
                    }
                    $topup .= $value['topup_3'];
                }
                if ($value['topup_4']) {
                    if ($topup) {
                        $topup .= ', ';
                    }
                    $topup .= $value['topup_4'];
                }

                $item = [
                    'tgl_masuk'     => date('d-m-Y', strtotime($value['tgl_masuk'])),
                    'tgl_pulang'    => date('d-m-Y', strtotime($value['tgl_pulang'])),
                    'no_rm'         => $value['no_rm'],
                    'nama_pasien'   => $value['nama_pasien'],
                    'nomor_sep'     => $value['nomor_sep'],
                    'inacbg'        => $value['inacbg'],
                    'topup'         => $topup ? $topup : '',
                    'total_tarif_(Diajukan_Rp.)'   => $value['tarif_diajukan'],
                    'tarif_rumah_sakit_(Rp.)'      => $value['tarif_rs'],
                    'disetujui_(Rp.)'     => $value['tarif_disetujui'],
                    'tgl_verifikasi'=> $value['tgl_verifikasi'] ? date('d-m-Y', strtotime($value['tgl_verifikasi'])) : '',
                    'status'        => $value['status_verifikasi'],
                ];
                array_push($result, $item);
            }
        }
        
        $header['periode'] = date('d-m-Y', strtotime($start)) .' Sampai dengan '. date('d-m-Y', strtotime($end));
        $filePath = DocoHelpers::exportExcel('Laporan Perbandingan Pembayaran', $result, $header, [], [], [], true);
        $filePath->save('php://output');
        die;
    }

    public function actionGetSep()
    {
        $request = Yii::$app->request;
        $arr_sep = $request->get('sep');
        $exist = [];

        $klaim = SyKunjunganPasien::find()->where(['in', 'no_sep', $arr_sep])->all();
        if ($klaim) {
            foreach ($klaim as $key => $value) {
                $exist[] = $value['no_sep'];        
            }
        }

        return $exist;
    }

    public function actionSaveData() 
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $data = $request->post('data');

        try{
            if ($data) {
                foreach ($data as $key => $value) {
                    $model = SyKunjunganPasien::find()->where(['no_sep'=>$value['no_sep'], 'is_deleted' => false])->one();
                    $model->is_verifikasi = true;
                    $model->total_verifikasi = $value['disetujui'];
                    $model->tgl_verifikasi = date('Y-m-d', strtotime($value['tgl_verifikasi']));
                    $model->save();
                }
                $transaction->commit();
                $response = [
                    'text' => 'Data berhasil disimpan',
                    'title' => 'Proses berhasil !',
                ];
                
                return $response;
            }
        }
        catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionDownloadExcel()
    {
        $result = [];
        $header = [];
        $footer = [];
        $dataFoot = [];

        $request = Yii::$app->request;
        $model = new LaporanPerbandinganKlaimView;
        $query = $model::find();

        $start = date('Y-m-d');
        $end = date('Y-m-d');
        $instalasi = 'Rawat Jalan dan Rawat Inap';

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_masuk'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_masuk']);
                if(count($explode) == 2) {
                    $date_start = date('Y-m-d', strtotime($explode[0]));
                    $date_end = date('Y-m-d', strtotime($explode[1]));

                    $query->andWhere(['between', 'tgl_masuk', $date_start, $date_end]);
                }
                unset($_GET['advanced-filter']['tgl_masuk']);
                
            }
            if(isset($_GET['advanced-filter']['tgl_pulang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pulang']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pulang']);
            }
            if(isset($_GET['advanced-filter']['tgl_verifikasi'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_verifikasi']);
                if(count($explode) == 2) {
                    $date_start = date('Y-m-d', strtotime($explode[0]));
                    $date_end = date('Y-m-d', strtotime($explode[1]));

                    $query->andWhere(['between', 'tgl_verifikasi', $date_start, $date_end]);
                }
                unset($_GET['advanced-filter']['tgl_verifikasi']);   
            }
            if(isset($_GET['advanced-filter']['instalasi'])) {
                if ($_GET['advanced-filter']['instalasi']) {
                    $name = $_GET['advanced-filter']['instalasi'];
                    if ($name == 'RINP') {
                        $instalasi = 'Rawat Inap';
                    } else if($name == 'RJAL') {
                        $instalasi = 'Rawat Jalan';
                    } 
                }
            }
        }
        $query->andWhere(['between', 'tgl_pulang', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->all();
        if ($data) {
            $no = 1;
            foreach ($data as $key => $value) {
                if (!$value['is_verifikasi']) {
                    $item = [
                        'no'            => $no,
                        'nomor_sep'     => $value['nomor_sep'],
                        'tgl_verifikasi'=> '',
                        'rill_rs'       => $value['tarif_rs'] ? $value['tarif_rs'] : '',
                        'diajukan'      => $value['tarif_diajukan'] ? $value['tarif_diajukan'] : '',
                        'disetujui'     => $value['tarif_disetujui'] ? $value['tarif_disetujui'] : '',
                    ];
                    array_push($result, $item);
                    $no++;
                }
            }
        } else {
            $item = [
                'no'            => 1,
                'nomor_sep'     => '',
                'tgl_verifikasi'=> '',
                'rill_rs'       => '',
                'diajukan'      => '',
                'disetujui'     => '',
            ];
            array_push($result, $item);
        }
        
        $custHeader = [
                [
                     [
                        'label'=>'No',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Nomor sep',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Tanggal Verifikasi (dd/mm/yyyy)',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Biaya',
                        'colspan'=>3,
                    ],
                ],
                [
                    [
                        'label'=>'Rill RS',
                        'startfrom'=>4
                    ],
                    [
                        'label'=>'Diajukan',
                    ],
                    [
                        'label'=>'Disetujui',
                    ],
                ]
            ];
        $header = [
            'instalasi' => $instalasi,
            'Periode' => date('d-m-Y', strtotime($start)) .' Sampai dengan '. date('d-m-Y', strtotime($end)),
        ];

        $filePath = DocoHelpers::exportExcel('RINCIAN DATA HASIL VERIFIKASI', $result, $header,  array(
                "skipIncrement" => true,
                "skipHeader"    => true,
                'customHeader' => $custHeader,
            ),$footer,[], true);

        // return $filePath
        $filePath->save('php://output');
        die;
    }
}
