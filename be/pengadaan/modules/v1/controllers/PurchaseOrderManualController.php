<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;

use app\modules\v1\models\Pegawai;
use app\modules\v1\models\Barang;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Payterm;
use app\modules\v1\models\Pajak;
use app\modules\v1\models\Supplier;
use app\modules\v1\models\PoManual;
use app\modules\v1\models\PoManualDetail;
use app\modules\v1\models\InfoPoView;
use app\modules\v1\models\InfoPoDetailView;
use app\modules\v1\models\LogActivityR;

use app\modules\v1\models\ValidasiPoBarang;
use app\modules\v1\models\ValidasiPoBarangDetail;
use app\modules\v1\models\ValidasiPoObat;
use app\modules\v1\models\ValidasiPoObatDetail;

class PurchaseOrderManualController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\PoManual';
    public $actionPath = 'app\modules\v1\actions\PurchaseOrderManual';
    
    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["generate-api"] = ["GET"];
        return $verbs;
    }

    public function actions() {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        $action = [
            'simpan'        => $this->actionPath . '\SimpanAction',
            'generate-api'  => $this->actionPath . '\GenerateApiAction',
        ];

        $actions = array_merge($actions, $action);
        return $actions;
    }

    /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #tgl_pomanual# => tanggal po
    * @attribute #no_pomanual# => nomor po
    * @attribute #ruangan_nama# => ruangan
    * @attribute #instalasi_nama# => instalasi
    * @attribute #supplier_nama# => supplier
    * @attribute #payterm# => payterm
    * @attribute #catatan1# => catatan1
    * @attribute #catatan2# => catatan2
    * @attribute #tgl_rencanaterima# => catatan2
    * @attribute #sub_total# => catatan2
    * @attribute #total_discount# => catatan2
    * @attribute #ppn_nilai# => catatan2
    * @attribute #total# => catatan2
    * @attribute #diorder_oleh# => catatan2
    * @attribute #title# => title 
    * @attribute #pegawai_mengetahui# => pegawai mengetahui
    * @attribute #pegawai_menyetujui# => pegawai menyetujui
    */
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $title = 'Purchase Order';
        $no_pomanual = $request->get('no_pomanual');
        $viewHeader = InfoPoView::find()->where(['no_transaksi' => $no_pomanual])->one();
        $viewDetail = InfoPoDetailView::find()->where(['no_transaksi' => $no_pomanual])->all();
        $ruangan_nama = $viewHeader->ruangan_nama;
        $instalasi_nama = $viewHeader->instalasi_nama;
        $supplier_nama = $viewHeader->supplier_nama;
        $payterm = Payterm::findOne($viewHeader->payterm_id);
        $payterm = ($payterm) ? $payterm->jumlah_hari.' D' : '';
        $catatan1 = $viewHeader->catatan1;
        $catatan2 = $viewHeader->catatan2;
        $tgl_rencanaterima = date('d M Y', strtotime($viewHeader->tgl_rencanaterima));
        $sub_total = $viewHeader->sub_total;
        $total_discount = $viewHeader->total_discount;
        $ppn_nilai = $viewHeader->ppn_nilai;
        $total = $viewHeader->total;

        $pegawai_mengetahui = Pegawai::findOne($viewHeader->peg_mengetahui_id);
        $pegawai_menyetujui = Pegawai::findOne($viewHeader->peg_menyetujui_id);
        $diorder_oleh = Pegawai::findOne($viewHeader->diorder_oleh);
        $diorder_oleh = ($diorder_oleh) ? $diorder_oleh->nama_pegawai : '';
        $detail = $viewDetail;

        $print = new DocoPrint();
        $print->attributes = [
            '#no_pomanual#' => $no_pomanual,
            '#tgl_pomanual#' => date('d M Y', strtotime($viewHeader->tanggal_po)),
            '#title#' => $title,
            '#ruangan_nama#' => $ruangan_nama,
            '#instalasi_nama#' => $instalasi_nama,
            '#supplier_nama#' => $supplier_nama,
            '#payterm#' => $payterm,
            '#catatan1#' => $catatan1,
            '#catatan2#' => $catatan2,
            '#tgl_rencanaterima#' => $tgl_rencanaterima,
            '#sub_total#' => $sub_total,
            '#total_discount#' => $total_discount,
            '#ppn_nilai#' => $ppn_nilai,
            '#total#' => $total,
            '#diorder_oleh#' => $diorder_oleh,
            '#pegawai_mengetahui#' => ($pegawai_mengetahui) ? $pegawai_mengetahui->nama_pegawai : '',
            '#pegawai_menyetujui#' => ($pegawai_menyetujui) ? $pegawai_menyetujui->nama_pegawai : '',
            '#datatable#' => $this->renderPartial('_cetak', [
                'data' => $detail,
                'sub_total' => $sub_total,
                'total_discount' => $total_discount,
                'ppn_nilai' => $ppn_nilai,
                'total' => $total,
            ]),
        ];

        $print->Output();
    }
}
