<?php

/**
 * @author Randy Vianda Putra
 * @todo Trasaksi Retur
 * @copyright 8 Maret 2018 aweutist
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use app\components\ApotekComponent;
use app\modules\v1\models\InfoKartuStok2View;
use app\modules\v1\models\InfoKartuStokObatNewView;
use app\modules\v1\models\KartuStokObatFn;

class InfKartuStokController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoKartuStok2View';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        $action = [
            'index' => 'app\modules\v1\actions\InfKartuStok\RecalculateAction',
            'excel' => 'app\modules\v1\actions\InfKartuStok\ExcelRecalculateAction',
            'export-excel-bgprocess' => 'app\modules\v1\actions\InfKartuStok\ExportExcelBgProcessAction',
            'drop-file' => 'app\modules\v1\actions\DropFileAction',
            'download-excel' => 'app\modules\v1\actions\DownloadExcelAction',
        ];
        $actions = array_merge($actions,$action);
        return $actions;
    }

    public function getData()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $range_tanggal = ArrayHelper::getValue($get,'advanced-filter.tanggal_transaksi');
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');
        if(!is_null($range_tanggal)){
            $range_explode = explode(' - ', $range_tanggal);
            if (count($range_explode) == 2) {
                $start_date = date('Y-m-d',strtotime($range_explode[0]));
                $end_date = date('Y-m-d',strtotime($range_explode[1]));
            }
        }
        $ruangan_id = ArrayHelper::getValue($get,'advanced-filter.ruangan_id');
        $obatalkes_id = ArrayHelper::getValue($get,'advanced-filter.obatalkes_id');
        if(empty($ruangan_id) || empty($obatalkes_id)){
            throw new \Exception("Parameter Ruangan atau Obat Kosong", 1);
        }
        $model = new KartuStokObatFn(['extParam'=>[$start_date,$end_date,$ruangan_id,$obatalkes_id]]);

        return $model;
    }

    public function actionGetTglkadaluarsa($id)
    {
        $request = Yii::$app->request;
        try{
            $data = InfoKartuStok2View::find()->select(['tglkadaluarsa', 'obatalkes_id'])->where(['obatalkes_id'=>$id,'ruangan_id'=>$request->get('ruangan_id')])->asArray()->all();
            $result = $data;
        }catch(\yii\db\Exception $e){
            $result = [];
        }
        return $result;
    }

    public function actionDataObat()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = $this->dataObat();
        $result->select(['obatalkes_id','obatalkes_kode','obatalkes_nama','ruangan_id','is_active']);
        if(!empty($post['term'])){
            $term = $post['term'];
            $result->where(['ILIKE','obatalkes_nama',$term]);
            $result->orWhere(['ILIKE','obatalkes_kode',$term]);
        }
        if(!empty($post['ruangan_id'])){
            $term = $post['ruangan_id'];
            $result->andWhere(['=','ruangan_id',$term]);
        }
        return $result->asArray()->all();
    }

    public function dataObat()
    {
        $data = InfoKartuStok2View::find();
        return $data;
    }

    public function actionExportExcel()
    {
        $data = [];
        $model = new InfoKartuStokObatNewView;
        $query = $model::find(true);

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
         **/
        $tgl_transaksi = false;
        $obat_alkes = 0;
        $kode_obat_alkes = '';
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['tanggal_transaksi'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tanggal_transaksi']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tanggal_transaksi']); // Unset Advanced Filter  date range
                $tgl_transaksi = true;
            }

            if (isset($_GET['advanced-filter']['obatalkes_id'])) {
                $obat_alkes = $_GET['advanced-filter']['obatalkes_id'];
                unset($_GET['advanced-filter']['obatalkes_id']);
            }

            if (isset($_GET['advanced-filter']['obatalkes_kode'])) {
                $kode_obat_alkes = $_GET['advanced-filter']['obatalkes_kode'];
            }
        }

        $query->andWhere(['between', 'tanggal_transaksi', $start, $end]);

        $query->andWhere([
            'obatalkes_id' => $obat_alkes
        ]);
        /**
         * End Special Condition date range
        **/
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $query->orderBy([
            'stokobatalkes_id' => SORT_ASC
        ]);

        $data = $query->asArray()->all();
        $namaObat = null;
        $data_baru = [];
        $stok_awal = null;
        foreach ($data as $key => $value) {
            $namaObat = $value['obatalkes_nama'];
            if ($key == 0) {
                $stok_awal = $value['stok'];
                $stok_out = $value['qtystok_out'];
                $stok_in = $value['qtystok_in'];
                $stok_awal += $stok_out - $stok_in;
            }
            $data_baru[] = [
                'Tanggal Transaksi' => date('d-M-Y',strtotime($value['tanggal_transaksi'])),
                'Kode Obat/Alkes' => $value['obatalkes_kode'],
                'Nama Obat/Alkes' => $namaObat,
                'Tanggal Kadaluarsa' => date('d-M-Y',strtotime($value['tglkadaluarsa'])),
                'No Transaksi' => $value['no_transaksi'],
                'Keterangan' => $value['keterangan'],
                'Ruangan Asal' => $value['ruangan_asal_nama'],
                'Ruangan Tujuan' => $value['ruangan_tujuan_nama'],
                'Qty Masuk' => $value['qtystok_in'],
                'Qty Keluar' => $value['qtystok_out'],
                'Stok' => $value['stok'],
                'Satuan' => $value['satuanunit_nama']
            ];
        }

        $header = [
            'Tanggal Transakasi' => date('d-M-Y',strtotime($start)) . ' s/d ' . date('d-M-Y',strtotime($end)),
            'Nama Obat/Alkes' => $namaObat,
            'Kode Obat/Alkes' => $kode_obat_alkes,
            'Stok Awal' => $stok_awal
        ];

        $options = [
            "titleStyle" => [
                "fontSize" => 11,
                "alignment" => "left"
            ],
            "customFormatCode" => [
                [
                    'selectColumn' => 'B',
                    'formatCode' => 'date',
                ],
                [
                    'selectColumn' => 'C',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'D',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'E',
                    'formatCode' => 'date',
                ],
                [
                    'selectColumn' => 'F',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'G',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'H',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'I',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'J',
                    'formatCode' => 'number',
                ],
                [
                    'selectColumn' => 'K',
                    'formatCode' => 'number',
                ],
                [
                    'selectColumn' => 'L',
                    'formatCode' => 'number',
                ],
                [
                    'selectColumn' => 'M',
                    'formatCode' => 'general',
                ]
            ]
        ];

        $filePath = DocoHelpers::exportExcel("Informasi Kartu Stok", $data_baru, $header, $options,null,null,true);

        $filePath->save('php://output');
        die;
    }

    public function actionGetStokObat($obatalkes_id, $ruangan_id)
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if (isset($get['tgl_transaksi'])) {
            $explode_date = explode(" - ", $get['tgl_transaksi']);
            if (count($explode_date) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($explode_date[0]));
                $end = date('Y-m-d 23:59:59', strtotime($explode_date[1]));
            }
        }

        $stok_oa = (new KartuStokObatFn(
            ['extParam' => [
                $start, 
                $end, 
                $ruangan_id, 
                $obatalkes_id]]
        ))->find()
        ->orderBy(['row_number' => SORT_DESC])
        ->asArray()
        ->one();
        
        if (!empty($stok_oa) && isset($stok_oa['stok_tersedia']) && isset($stok_oa['satuanunit_nama'])) {
            return DocoHelpers::formatNumber($stok_oa['stok_tersedia'], true, false, 3) ." ". $stok_oa['satuanunit_nama'];
        }

        return "-";

    }
}
