<?php

/**
 * @author Randy Vianda Putra
 * @todo Info Kartu Stok Barang
 * @copyright 17 April 2018 aweutist
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use app\modules\v1\models\Ruangan;
use Doco\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\KartuStokBarangFn;

class InfKartuStokController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoKartuStokBarangView';

    public $_range;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["GET"];
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
        return $actions;
    }

    public function getData() {
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
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $barang_id = ArrayHelper::getValue($get,'advanced-filter.barang_id');
        if(empty($ruangan_id) || empty($barang_id)){
            throw new \Exception("Parameter Ruangan atau Obat Kosong", 1);
        }
        $this->_range = date("d M Y", strtotime($start_date)) . ' - ' . date("d M Y", strtotime($end_date));
        $model = new KartuStokBarangFn(['extParam'=>[$start_date,$end_date,$ruangan_id,$barang_id]]);

        return $model;
    }

    public function actionIndex()
    {
        try {
            $model = self::getData();
            return new ActiveDataProvider(['query' => $model::find()]);
    	} catch (\Exception $e){
    		$this->logError($e);
    		return [
    				'message' => $e->getMessage(),
            		'data' => [],
            		'_meta' => [
                        'totalCount'=>0
                    ]
            	];
        } catch (\yii\db\Exception $e) {
        	$this->logError($e);
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionUnduhExcel()
    {
        $model = self::getData();
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $ruangan = Ruangan::find()->where([
            "ruangan_id" => $ruangan_id
        ])->one();
        $rows = [];
        $stok_awal = 0;
        $query = $model::find()->asArray()->all();

        foreach ($query as $key => $value) {
            if ($key == 0) {
                $stok_awal = $value['total'];
                $stok_out = $value['qtystok_out'];
                $stok_in = $value['qtystok_in'];
                $stok_awal += $stok_out - $stok_in;
            }
            $newRow = [];
            $newRow[\Yii::t('app', 'Tanggal Transaksi')] = date("d-M-Y", strtotime($value["tanggal_transaksi"]));
            $newRow[\Yii::t('app', 'Nama Barang')] = $value["barang_nama"];
            $newRow[\Yii::t('app', 'Tanggal Kadaluarsa')] = !is_null($value["tglkadaluarsa"]) ? date("d-M-Y", strtotime($value["tglkadaluarsa"])) : "-";
            $newRow[\Yii::t('app', 'No. Transaksi')] = $value["no_transaksi"];
            $newRow[\Yii::t('app', 'Keterangan')] = $value["keterangan"];
            $newRow[\Yii::t('app', 'Ruangan Asal')] = $value["ruangan_asal_nama"];
            $newRow[\Yii::t('app', 'Ruangan Tujuan')] = $value["ruangan_tujuan_nama"];
            $newRow[\Yii::t('app', 'Qty Masuk')] = $value["qtystok_in"];
            $newRow[\Yii::t('app', 'Qty Keluar')] = $value["qtystok_out"];
            $newRow[\Yii::t('app', 'Stok')] = $value["total"];
            $newRow[\Yii::t('app', 'Satuan')] = $value["satuanunit_nama"];
            $rows[$key] = $newRow;
        }

        $header = [
            Yii::t("app", "Periode Transaksi") => $this->_range,
            Yii::t("app", "Tanggal Diunduh") => (date("d F Y")),
            Yii::t("app", "Stok Awal") => $stok_awal,
        ];

        $nama_ruangan = !empty($ruangan) ? ucfirst($ruangan->ruangan_nama) : "";

        $filePath = DocoHelpers::exportExcel("KARTU STOK BARANG ".$nama_ruangan, $rows, $header, [],[],[],true);

        $filePath->save('php://output');
        die;
    }

}