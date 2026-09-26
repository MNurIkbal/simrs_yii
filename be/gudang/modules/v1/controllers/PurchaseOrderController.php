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

class PurchaseOrderController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\PoManual';
    
    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["generate-api"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        return $actions;
    }

    public function actionGenerateApi($pegawai_id)
    {
        $instalasi = Instalasi::find()->where(['is_active' => true, 'instalasi_id' => [15,66]])->all();
        $pegawai = Pegawai::find()->where(['is_active' => true])->all();
        $payterm = Payterm::find()->where(['is_active' => true])->all();
        $pajak = Pajak::find()->where(['is_active' => true])->all();
        $supplier = Supplier::find()->where(['is_active' => true])->all();
        $pegawaiLogin = Pegawai::findOne($pegawai_id);

        return [
            'instalasi' => ArrayHelper::map($instalasi, 'instalasi_id', 'instalasi_nama'),
            'pegawai' => ArrayHelper::map($pegawai, 'pegawai_id', 'nama_pegawai'),
            'payterm' => ArrayHelper::map($payterm, 'payterm_id', 'jumlah_hari'),
            'pajak' => ArrayHelper::map($pajak, 'pajak_id', 'pajak_persen'),
            'supplier' => ArrayHelper::map($supplier, 'supplier_id', 'supplier_nama'),
            'pegawaiLogin' => ($pegawaiLogin) ? $pegawaiLogin->nama_pegawai : '',
        ];
    }

    public function actionSimpan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $model = new PoManual;
        $modelDetail = new PoManualDetail;
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $detail = $post['detail'];
        try {
            $postHeader = $post['header'];
            $model->attributes = $postHeader;
            $model->tgl_rencanaterima = date('Y-m-d', strtotime($model->tgl_rencanaterima));
            $model->ppn_nilai = preg_replace('/[^0-9]/', '', $postHeader['ppn_nilai']);
            $model->sub_total = preg_replace('/[^0-9]/', '', $postHeader['sub_total']);
            $model->total_discount = preg_replace('/[^0-9]/', '', $postHeader['total_discount']);
            $model->total = preg_replace('/[^0-9]/', '', $postHeader['total']);

            if($flag = $model->save()) {
                $idParent = $model->pomanual_id;
                $supplier_id = $model->supplier_id;

                $arrInsert = [];
                foreach ($detail['item_id'] as $details) {
                    list($item_id, $tipe) = explode('-', $details['item_id']);
                    $obatalkes_id = ($tipe == 'B') ? null : $item_id;
                    $barang_id = ($tipe == 'B') ? $item_id : null;
                    $s_konversiobt_id = ($tipe == 'B') ? null : $details['satuan_id'];
                    $s_konversibrg_id = ($tipe == 'B') ? $details['satuan_id'] : null;
                    if($tipe == 'B') {
                        $konversi = $connection->createCommand("
                            SELECT * FROM satuankonversibrg_m where satuankonversibrg_id = {$s_konversibrg_id}
                        ")->queryOne();
                    }
                    else {
                        $konversi = $connection->createCommand("
                            SELECT * FROM satuankonversi_m where satuankonversi_id = {$s_konversiobt_id}
                        ")->queryOne();
                    }
                    
                    $total_konversi = $konversi['nilai_konversi'] * $details['qty'];

                    $arrInsert[] = [
                        'pomanual_id' => $idParent,
                        'obatalkes_id' => $obatalkes_id,
                        'barang_id' => $barang_id,
                        'qty' => $total_konversi,
                        'qty_input' => $details['qty'],
                        's_konversiobt_id' => $s_konversiobt_id,
                        's_konversibrg_id' => $s_konversibrg_id,
                        'harga' => $details['harga'],
                        'discount' => $details['discount_rp'],
                        'jumlah' => $details['total_harga'],
                    ];
                }

                PoManualDetail::batchInsert($arrInsert);
                foreach ($detail['item_id'] as $details) {
                    list($item_id, $tipe) = explode('-', $details['item_id']);
                    $obatalkes_id = ($tipe == 'B') ? null : $item_id;
                    $barang_id = ($tipe == 'B') ? $item_id : null;
                    if($tipe == 'O') {
                        $obat = ObatAlkes::findOne($obatalkes_id);
                        $obat->supplier_id = $supplier_id;
                        $obat->save(false);
                    }
                    else {
                        $barang = Barang::findOne($barang_id);
                        $barang->supplier_id = $supplier_id;
                        $barang->save(false);
                    }
                }

                $transaction->commit();
                $maxPo = PoManual::find()
                        ->select(['MAX(pomanual_id)'])
                        ->scalar();

                $po = PoManual::find()
                    ->select(['no_pomanual'])
                    ->where(['pomanual_id' => $maxPo])
                    ->one();

                $response = [
                    'text' => 'PO Manual berhasil disimpan',
                    'title' => 'Proses berhasil !',
                    'no_pomanual' => $po->no_pomanual
                ];
            
                return $response;
            }


        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } 
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
        $model = new PoManual;
        $header = $model::find()->where(['no_pomanual' => $no_pomanual])->one();
        $viewHeader = InfoPoView::find()->where(['nomor' => $no_pomanual])->one();
        $ruangan_nama = $viewHeader->ruangan_nama;
        $instalasi_nama = $viewHeader->instalasi_nama;
        $supplier_nama = $viewHeader->supplier_nama;
        $payterm = Payterm::findOne($header->payterm_id);
        $payterm = ($payterm) ? $payterm->jumlah_hari.' D' : '';
        $catatan1 = $header->catatan1;
        $catatan2 = $header->catatan2;
        $tgl_rencanaterima = date('d M Y', strtotime($header->tgl_rencanaterima));
        $sub_total = $header->sub_total;
        $total_discount = $header->total_discount;
        $ppn_nilai = $header->ppn_nilai;
        $total = $header->total;

        $pegawai_mengetahui = Pegawai::findOne($header->peg_mengetahui_id);
        $pegawai_menyetujui = Pegawai::findOne($header->peg_menyetujui_id);
        $diorder_oleh = Pegawai::findOne($header->diorder_oleh);
        $diorder_oleh = ($diorder_oleh) ? $diorder_oleh->nama_pegawai : '';
        $detail = [];

        $print = new DocoPrint();
        $print->attributes = [
            '#no_pomanual#' => $no_pomanual,
            '#tgl_pomanual#' => date('d M Y', strtotime($header->tgl_pomanual)),
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