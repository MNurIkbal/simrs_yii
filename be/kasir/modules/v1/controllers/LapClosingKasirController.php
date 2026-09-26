<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanClosingKasirView;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;

class LapClosingKasirController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanClosingKasirView';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
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

    public function actionIndex()
    {
        $model = new LaporanClosingKasirView;
        $query = $model::find(true);

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_closingkasir'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_closingkasir']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_closingkasir']); // Unset Advanced Filter  date range
                $between = true;
            }
        }
        // if($between) {
            $query->andWhere(['between', 'tgl_closingkasir', $start, $end]);
        // }
        /**
         * End Special Condition date range
        **/

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    protected $_title = "Laporan Closing Kasir";
    public function actionExportExcel()
    {
        $model = new LaporanClosingKasirView;
        $query = $model::find(true);

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_closingkasir'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_closingkasir']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_closingkasir']); // Unset Advanced Filter  date range
                $between = true;
            }
        }
        // if($between) {
            $query->andWhere(['between', 'tgl_closingkasir', $start, $end]);
        // }
        /**
         * End Special Condition date range
        **/

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $result = [];

        foreach ($query->asArray()->all() as $key => $value) {
            // Data Selection
            $value['tgl_closingkasir'] = date("j M Y", strtotime($value['tgl_closingkasir']));

            $newValue = [];
            $newValue[\Yii::t('app', 'Tanggal closing')] = $value['tgl_closingkasir'];
            $newValue[\Yii::t('app', 'No closing')] = $value['no_struksetor'];
            $newValue[\Yii::t('app', 'Pegawai closing')] = $value['nama_pegawai'];
            $newValue[\Yii::t('app', 'Shift')] = $value['shift_nama'];
            $newValue[\Yii::t('app', 'Instalasi akhir')] = $value['instalasi_nama'];
            $newValue[\Yii::t('app', 'Ruangan akhir')] = $value['ruangan_nama'];
            $newValue[\Yii::t("app", "Total closing")." (IDR)"] = $value['nilai_closingtransaksi'];
            $result[$key] = $newValue;
        }

        // return $result;
        // Directory Creation
        $header = array(
            Yii::t("app", "Tanggal closing") => ((date('d-M-Y', strtotime($start))." - ".date('d-M-Y', strtotime($end)))),
            Yii::t("app", "Nama pegawai") => (@$_GET['advanced-filter']['nama_pegawai']),
            Yii::t("app", "Shift") => (@$_GET['advanced-filter']['shift_nama']),
            Yii::t("app", "Instalasi") => (@$_GET['advanced-filter']['instalasi_nama']),
            Yii::t("app", "Ruangan") => (@$_GET['advanced-filter']['ruangan_nama']),
        );

        $footer = [];
        $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, [], $footer, [], true);
        $filePath->save('php://output');
        die;
    }
    /**
    * @controller actionExportPdf
    * @attribute #table_data# => table
    * @attribute #tgl_pembayaran# => tanggal pembayaran
    **/
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        try {
            $model = new LaporanClosingKasirView;
            $query = $model::find(true);

            /**
             * Begin Special Condition date range
             * DocoRestActiveFilter cannot handle
            **/
            $between = false;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:59');

            if(isset($_GET['advanced-filter'])) {
                if(isset($_GET['advanced-filter']['tgl_closingkasir'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_closingkasir']);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_closingkasir']); // Unset Advanced Filter  date range
                    $between = true;
                }
            }
            // if($between) {
                $query->andWhere(['between', 'tgl_closingkasir', $start, $end]);
            // }
            /**
             * End Special Condition date range
            **/

            $query->orderBy($request->get('order'));
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            $print = new DocoPrint();
            $print->attributes = [
                '#table_data#' => $this->renderPartial('print_pdf',['data'=>$query->asArray()->all()]),
                '#tgl_pembayaran#' => date('d M Y', strtotime($start)).' - '.date('d M Y', strtotime($end)),
            ];
            $print->Output();
        } catch (\yii\db\Exception $e) {
            return "Terjadi Kesalahan";
        }
         catch (\yii\db\Exception $e) {
            return "Terjadi Kesalahan";
        }
    }
    public function actionGetApi()
    {
        $result['instalasi'] = [];
        $result['shift'] = [];
        try {
            $result['instalasi'] = Yii::$app->runAction('v1/allow/get-instalasi');
            $result['instalasi'] = $result['instalasi']['response'];
            $result['shift'] = Yii::$app->runAction('v1/allow/get-shift');
            $result['shift'] = $result['shift']['response'];
            return $result;
        } catch (\yii\db\Exception $e) {
            return $result;
        } catch (\yii\db\Exception $e) {
            return $result;
        }
    }
}