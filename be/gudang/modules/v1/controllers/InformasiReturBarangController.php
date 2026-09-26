<?php
namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;

use app\modules\v1\models\InfoReturPenerimaanBarang;
use app\modules\v1\models\InfoReturPenerimaanBarangDetail;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\Ruangan;

class InformasiReturBarangController extends \Doco\components\DocoActiveController
{
    public $modelClass = '';
    protected $range;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        return $actions;
    }

    public function getData()
    {
        $model = new InfoReturPenerimaanBarangDetail;
        $query = $model::find(true);

        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if (isset($_GET['advanced-filter']))
        {
            if (isset($_GET['advanced-filter']['tgl_retur'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_retur']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_retur']);
            }
            if (isset($_GET['advanced-filter']['status_invoice'])) {
                $statusInvoice = (int) $_GET['advanced-filter']['status_invoice'];
                $query->andWhere(['status_invoice' => $statusInvoice]);
            }
        }

        $this->range = date('d-M-Y',strtotime($start)) . ' - ' . date('d-M-Y',strtotime($end));
        $query->andWhere(['between', 'tgl_retur', $start, $end]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return $query;
    }

    public function actionIndex()
    {
        $model = new InfoReturPenerimaanBarangDetail;
        $query = $model::find(true);

        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if (isset($_GET['advanced-filter']))
        {
            if (isset($_GET['advanced-filter']['tgl_retur']))
            {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_retur']);
                if (count($explode) == 2)
                {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_retur']);
            }
            if (isset($_GET['advanced-filter']['status_invoice']))
            {
                $statusInvoice = (int) $_GET['advanced-filter']['status_invoice'];
                $query->andWhere(['status_invoice' => $statusInvoice]);
            }
        }

        $this->range = date('d-M-Y',strtotime($start)) . ' - ' . date('d-M-Y',strtotime($end));
        $query->andWhere(['between', 'tgl_retur', $start, $end]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
    * @controller actionCetakTransaksi
    * @attribute #no_retur# => Menampilkan Nomor Retur
    * @attribute #tgl_retur# => Menampilkan Tanggal Retur
    * @attribute #nama_supplier# => Menampilkan Nama Supplier
    * @attribute #petugas_retur# => Menampilkan Nama Petugas Retur
    * @attribute #alasan_retur# => Menampilkan Alasan Retur
    * @attribute #detail_retur# => Data Detail Retur
    **/

    public function actionCetakTransaksi($id)
    {
        $print = new DocoPrint;

        $model = new InfoReturPenerimaanBarang;
        $model_detail = new InfoReturPenerimaanBarangDetail;

        $head = $model->find()->where([
            "returpenerimaanbarang_id" => $id
        ])->one();

        if (!empty($head))
        {
            $detail = $model_detail->find()->where([
                "returpenerimaanbarang_id" => $head["returpenerimaanbarang_id"]
            ])->all();

            if (!empty($detail))
            {
                $print_attributes = [
                    "#no_retur#" => $head["no_returpenerimaanbarang"] ,
                    "#tgl_retur#" => isset($head["tgl_retur"]) ? date("d-M-Y", strtotime($head["tgl_retur"])) : "-",
                    "#nama_supplier#" => $detail[0]["supplier_nama"],
                    "#petugas_retur#" => $head["pegawai_retur"],
                    "#alasan_retur#" => $head["alasan_retur"],
                    "#detail_retur#" => $this->renderPartial("table-retur", ["detail"=> $detail]),
                ];

                $print->attributes = $print_attributes;

                $print->Output();
            }else
            {
                return [
                    "status" => 402,
                    "text" => "No. Retur tidak ditemukan"
                ];
            }
        }else{
            return [
                "status" => 402,
                "text" => "No. Retur tidak ditemukan"
            ];
        }
    }

    /**
    * @controller actionCetakPdf
    * @attribute #tabel_detail# => Untuk Menampilkan tabel detail retur
    * @attribute #nama_ruangan# => Untuk Menampilkan nama ruangan
    * @attribute #range_date# => Untuk Menampilkan Range Date
    * @attribute #kepala_ruangan# => Untuk Menampilkan nama pegawai ruangan
    **/

    public function actionCetakPdf()
    {
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $model = new InfoReturPenerimaanBarangDetail;

        $query = $model::find(true);

        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if (isset($_GET['advanced-filter']))
        {
            if (isset($_GET['advanced-filter']['tgl_retur']))
            {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_retur']);
                if (count($explode) == 2)
                {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_retur']);
            }
            if (isset($_GET['advanced-filter']['status_invoice']))
            {
                $statusInvoice = (int) $_GET['advanced-filter']['status_invoice'];
                $query->andWhere(['status_invoice' => $statusInvoice]);
            }
        }

        $this->range = date('d-M-Y',strtotime($start)) . ' - ' . date('d-M-Y',strtotime($end));
        $query->andWhere(['between', 'tgl_retur', $start, $end]);

        $data = $query->all();

        $pegawai = PegawaiView::find()
            ->where(['ruangan_id' => $ruangan_id, 'jabatan_id' => DocoConstants::VAR_J_K_R])
            ->one();

        $ruangan = Ruangan::find()->where([
            "ruangan_id" => $ruangan_id
        ])->one();

        $range = date("d-M-Y", strtotime($start))." s/d ".date("d-M-Y", strtotime($end));

        $print = new DocoPrint();
        $print->attributes = [
            '#kepala_ruangan#' => !empty($pegawai->nama_pegawai) ? $pegawai->nama_pegawai : null,
            '#nama_ruangan#' => !empty($ruangan) ? $ruangan->ruangan_nama : "-",
            '#range_date#' => $range,
            '#tabel_detail#' => $this->renderPartial('index', [
                'query' => $data
            ]),
        ];

        $print->Output();
    }

    public function actionExportExcel()
    {
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $model = new InfoReturPenerimaanBarangDetail;

        $query = $model::find(true);

        $ruangan = Ruangan::find()->where([
            "ruangan_id" => $ruangan_id
        ])->one();

        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if (isset($_GET['advanced-filter']))
        {
            if (isset($_GET['advanced-filter']['tgl_retur']))
            {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_retur']);
                if (count($explode) == 2)
                {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_retur']);
            }
            if (isset($_GET['advanced-filter']['status_invoice']))
            {
                $statusInvoice = (int) $_GET['advanced-filter']['status_invoice'];
                $query->andWhere(['status_invoice' => $statusInvoice]);
            }
        }

        $this->range = date('d-M-Y',strtotime($start)) . ' - ' . date('d-M-Y',strtotime($end));
        $query->andWhere(['between', 'tgl_retur', $start, $end]);

        $data = $query->all();

        $rows = [];
        foreach ($query->all() as $key => $value)
        {
            $newRow = [];
            $newRow[\Yii::t('app', 'Tanggal Retur')] = isset($value["tgl_retur"]) ? date("d-M-Y", strtotime($value['tgl_retur'])) : '';
            $newRow[\Yii::t('app', 'No. Retur')] = isset($value["no_returpenerimaanbarang"]) ? $value["no_returpenerimaanbarang"] : '';
            $newRow[\Yii::t('app', 'No. Penerimaan')] = isset($value["no_penerimaan"]) ? $value["no_penerimaan"] : '';
            $newRow[\Yii::t('app', 'No. Faktur')] = isset($value["no_faktur"]) ? $value["no_faktur"] : '';
            $newRow[\Yii::t('app', 'Supplier')] = isset($value["supplier_nama"]) ? $value["supplier_nama"] : '';
            $newRow[\Yii::t('app', 'Nama Barang')] = isset($value["barang_nama"]) ? $value["barang_nama"] : '';
            $newRow[\Yii::t('app', 'Qty')] = isset($value["qty_input"]) && isset($value["satuanunit_nama"]) ? $value["qty_input"]." ".$value["satuanunit_nama"] : '';
            $newRow[\Yii::t('app', 'Alasan')] = isset($value["alasan_retur"]) ? $value["alasan_retur"] : '';
            $rows[$key] = $newRow;
        }

        $periode_tanggal = date("d-M-Y", strtotime($start))." s/d ".date("d-M-Y", strtotime($end));

        $header = array(
            Yii::t("app", "Periode ") => $periode_tanggal,
            Yii::t("app", "Tanggal Diunduh") => (date("d-M-Y")),
        );

        $nama_ruangan = !empty($ruangan) ? ucfirst($ruangan->ruangan_nama) : "";

        $filePath = DocoHelpers::exportExcel("INFORMASI RETUR BARANG ".$nama_ruangan, $rows, $header, [],[],[],true);

        $filePath->save('php://output');
        die;
    }
}
?>