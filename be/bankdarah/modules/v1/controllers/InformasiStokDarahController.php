<?php

namespace app\modules\v1\controllers;

use Yii;
use app\modules\v1\models\InfoStokDarahView;
use app\modules\v1\models\InfoStokDarahDetailView;

use Doco\components\ConfigTrait;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use Doco\models\Modul;
use yii\data\ActiveDataProvider;

use yii\helpers\ArrayHelper;
use yii\db\Query;

class InformasiStokDarahController extends \Doco\components\DocoActiveController
{

    public $modelClass = '';
    public static $range;
    public static $filter;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["get-konfigantrian-by-jenis"] = ["POST", "GET"];
        $verbs["get-layarantrian-by-jenis"] = ["POST", "GET"];
        $verbs["get-group-konfig"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function getData(){
        $model = new InfoStokDarahView;
        $query = $model::find(true);

        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if (isset($_GET['advanced-filter'])) {
            self::$filter = $_GET['advanced-filter'];
            if (isset($_GET['advanced-filter']['tgl_kadaluarsa'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_kadaluarsa']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_kadaluarsa']);
                $query->andWhere(['between', 'tgl_kadaluarsa', $start, $end]);
            }

            if (isset($_GET['advanced-filter']['jenis_darah']) && !empty($_GET['advanced-filter']['jenis_darah'])) {
                unset($_GET['advanced-filter']['jenis_darah']);
            }

            if (isset($_GET['advanced-filter']['golongan_darah']) && !empty($_GET['advanced-filter']['golongan_darah'])) {
                unset($_GET['advanced-filter']['golongan_darah']);
            }
        }
        self::$range = date("d-M-Y", strtotime($start))." - ".date("d-M-Y", strtotime($end));

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query;
    }

    public function actionIndex()
    {
        $query = self::getData();
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionDetail($id)
    {
        $m = new InfoStokDarahView;
        $stokdarah = $m->find()->where([
            "stokdarahr_id" => $id
        ])->one();

        if (empty($stokdarah)) {
            return [
                "status" => false,
                "title" => "Stok Darah tidak ditemukan"
            ];
        }

        $stokdarahdetail = InfoStokDarahDetailView::find()->where([
            "jenisdarah_id" => $stokdarah->jenisdarah_id,
            "golongandarah_id" => $stokdarah->golongandarah_id,
        ]);

        return new ActiveDataProvider([
            'query' => $stokdarahdetail,
        ]);
    }

    public function actionExportExcel($id)
    {
        $m = new InfoStokDarahView;
        $stokdarah = $m->find()->where([
            "stokdarahr_id" => $id
        ])->one();

        if (empty($stokdarah)) {
            return [
                "status" => false,
                "title" => "Stok Darah tidak ditemukan"
            ];
        }

        $stokdarahdetail = InfoStokDarahDetailView::find()->where([
            "jenisdarah_id" => $stokdarah->jenisdarah_id,
            "golongandarah_id" => $stokdarah->golongandarah_id,
        ])->all();

        $no = 1;
        foreach ($stokdarahdetail as $row => $value) {
            $table[] = [
                "No." => $no,
                "No. Kantong" => $value->no_kantongdarah,
                "Jenis Darah" => $value->jenisdarah_nama,
                "Golongan Darah" => $value->golongandarah,
                "Rhesus" => $value->rhesus,
                "Tanggal Kadaluarsa" => date("d-M-Y", strtotime($value->tgl_kadaluarsa)),
                "Suhu Penyimpanan (c)" => $value->suhu_penyimpanan,
            ];
        }

        $filePath = DocoHelpers::exportExcel("Informasi Stok Darah", $table, [], [],null,null,true);
        $filePath->save('php://output');
        die;
    }

    public function actionExcel()
    {
        $query = self::getData();
        $data = $query->all();
        $filter = self::$filter;
        $no = 1;

        foreach ($data as $row => $value) {
            $table[] = [
                "No." => $no,
                "Jenis Darah" => $value->jenisdarah_nama,
                "Golongan Darah" => $value->golongandarah,
                "Rhesus" => $value->rhesus,
                "Tanggal Kadaluarsa" => date("d-M-Y", strtotime($value->tgl_kadaluarsa)),
                "Suhu Penyimpanan (c)" => $value->suhu_penyimpanan,
                "Stok Tersedia" => $value->qty_tersedia,
            ];
            $no++;
        }

        $filePath = DocoHelpers::exportExcel("Informasi Ketersediaan Stok Darah", $table, [], [],null,null,true);
        $filePath->save('php://output');
        die;
    }

}