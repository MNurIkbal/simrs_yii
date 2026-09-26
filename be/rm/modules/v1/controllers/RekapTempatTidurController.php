<?php

/**
 * @Author: Anggoro <tri.anggoro@docotel.com>
 * @Date:   2019-04-25
 */

namespace app\modules\v1\controllers;

// use app\modules\v1\models\HistoryTempatTidurView;
use app\modules\v1\models\Lookup;
// use app\modules\v1\models\KamarRuangan;
// use app\modules\v1\models\MasterKamarRuanganView;
// use app\modules\v1\models\Ruangan;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\RekapTempatTidurFn;

use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;

use Yii;
use yii\data\ActiveDataProvider;

class RekapTempatTidurController extends DocoActiveController
{
    /**
     * @todo Public vars
     * @author Anggoro <tri.anggoro@docotel.com>
     */
    public $modelClass = 'app\modules\v1\models\RekapTempatTidurFn';
    protected $tahun;
    protected $_months = [];

    /**
     * @todo Verbs function
     * @author Anggoro <tri.anggoro@docotel.com>
     */
    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    /**
     * @todo Actions function
     * @author Anggoro <tri.anggoro@docotel.com>
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function init()
    {
        parent::init();
        $this->tahun = date('Y');
        for ($i=1; $i <= 12; $i++) {
            $this->_months[$i] = date('M', mktime(0, 0, 0, $i, 10));
        }
    }

    private function getRekapData()
    {
        $request = Yii::$app->request;
        $tahun = date('Y');
        $where = $order = "";
        $advancedFilter = [];
        try {
            if($request->get('advanced-filter')) {
                $advancedFilter = $request->get('advanced-filter');

                if(isset($advancedFilter['tahun'])) {
                    $tahun = (int) $advancedFilter['tahun'];
                }

                 if(isset($advancedFilter['ruangan_nama'])) {
                    $where .= "WHERE ruangan_nama ILIKE '%".strtolower($advancedFilter['ruangan_nama'])."%'";
                }
            }

            $query = "
            SELECT * FROM rekaptempattidur_f('{$tahun}')
            {$where}
            {$order}
            ";

            $result = Yii::$app->db->createCommand($query)->queryAll();
            return [
                "result" => $result,
                "tahun" => $tahun,
                "advancedFilter" => $advancedFilter
            ];
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * @todo Index
     * @author Anggoro <tri.anggoro@docotel.com>
     */
    public function actionIndex()
    {
        $data = $this->getRekapData();
        return $data;
    }

    /**
    *
    * @controller actionExportPdf
    * @attribute #tahun# => Data Tahun
    * @attribute #tgl_cetak# => Tanggal Cetak
    * @attribute #kepala_ruangan# =>  Nama Kepala Ruangan
    * @attribute #data_ruangan# => Tabel Rekap Tempat Tidur
    *
    **/

    public function actionExportPdf()
    {
        $data = $this->getRekapData();
        $print = new DocoPrint;
        $total_ruangan = [];

        try {
            $tahun = isset($data["tahun"]) ? $data["tahun"] : date('Y');
            $ruangan_id = Yii::$app->jwt->ruangan_id;
            $pegawai_ruangan = PegawaiView::find()
                ->where(["ruangan_id" => $ruangan_id, "jabatan_id" => DocoConstants::VAR_J_K_R])->one();

            foreach ($data["result"] as $index => $value) {
                for ($i=1; $i <= 12; $i++) {
                    $month_key = ($i <= 9) ? (string) "0".$i : $i;
                    if (isset($total_ruangan[$month_key])) {
                        $total_ruangan[$month_key] += $value[$month_key];
                    }else{
                        $total_ruangan[$month_key] = $value[$month_key];
                    }
                }
            }
            $print_attributes = [
                "#tahun#" => $tahun,
                "#tgl_cetak#" => date("d-M-Y"),
                "#kepala_ruangan#" => is_null($pegawai_ruangan) ? "-" : $pegawai_ruangan->nama_pegawai,
                "#data_ruangan#" => $this->renderPartial("pdf",[
                    "data" => $data["result"],
                    "total" => $total_ruangan
                ])
            ];

            $print->attributes = $print_attributes;
            $print->Output();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function actionExportExcel()
    {
        $data = $this->getRekapData();
        $total_ruangan = [];
        $title = Yii::t('app', 'Rekap Laporan Tempat Tidur');

        $tahun = isset($data["tahun"]) ? $data["tahun"] : date('Y');
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $pegawai_ruangan = PegawaiView::find()
            ->where(["ruangan_id" => $ruangan_id, "jabatan_id" => DocoConstants::VAR_J_K_R])->one();

        foreach ($data["result"] as $index => $value) {
            for ($i=1; $i <= 12; $i++) {
                $month_key = ($i <= 9) ? (string) "0".$i : $i;
                if (isset($total_ruangan[$month_key])) {
                    $total_ruangan[$month_key] += $value[$month_key];
                }else{
                    $total_ruangan[$month_key] = $value[$month_key];
                }
            }
        }
        $data["result"][] = [
            "ruangan_id" => "000",
            "ruangan_nama" => "Total",
            "01" => $total_ruangan["01"],
            "02" => $total_ruangan["02"],
            "03" => $total_ruangan["03"],
            "04" => $total_ruangan["04"],
            "05" => $total_ruangan["05"],
            "06" => $total_ruangan["06"],
            "07" => $total_ruangan["07"],
            "08" => $total_ruangan["08"],
            "09" => $total_ruangan["09"],
            10 => $total_ruangan[10],
            11 => $total_ruangan[11],
            12 => $total_ruangan[12],

        ];

        $header = [];
        $footer = [];
        $result = [];
        $options = ["subTitle" => "Total Tempat Tidur Aktif Tahun :".$tahun];

        if (!empty($data["result"])) {
            $no = 1;
            foreach ($data["result"] as $key => $value) {
                $newValue = [];
                $newValue[\Yii::t('app', 'Nama Ruangan')] = $value['ruangan_nama'];
                for ($i=1; $i <= 12; $i++) {
                    $month_key = ($i <= 9) ? (string) "0".$i : $i;
                    $newValue[\Yii::t('app', $this->_months[(int)$month_key])] = $value[$month_key];
                }
                $result[$key] = $newValue;
                $no++;
            }
        }

        $filePath = DocoHelpers::exportExcel($title, $result, $header, $options, $footer, [], true);
        $filePath->save('php://output');
        die;
    }
}