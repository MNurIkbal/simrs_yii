<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;
use SirsCore\features\IntegrasiAkunting;

use app\modules\v1\models\InfoPenerimaanObatDetail;
use app\modules\v1\models\InfoPenerimaanObat;
use app\modules\v1\models\PenerimaanObatDoc;
use app\modules\v1\models\InfoPenerimaanBarangDetail;
use app\modules\v1\models\InfoPenerimaanBarang;
use app\modules\v1\models\PenerimaanBarangDoc;

class InformasiPenerimaanController extends DocoActiveController {
    public $modelClass = '';
    protected static $range = '';

    public function verbs() {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    /**
    * @controller actionPrintGrn
    * @attribute #dataTable# => Menampilkan Data cetak GRN
    * @attribute #no_penerimaan# => Menampilkan Nomor Penerimaan
    * @attribute #no_faktur# => Menampilkan Nomor Faktur
    * @attribute #no_po# => Menampilkan Nomor PO
    * @attribute #no_sj# => Menampilkan Nomor Surat Jalan
    * @attribute #tgl_sj# => Menampilkan Tanggal Surat Jalan
    * @attribute #tgl_penerimaan# => Menampilkan Tanggal Penerimaan
    * @attribute #nama_supplier# => Menampilkan Nama Supplier
    * @attribute #mengetahui# => Menampilkan Nama Pegawai Mengetahui
    * @attribute #menyetujui# => Menampilkan Nama Pegawai Menyetujui
    * @attribute #menerima# => Menampilkan Nama Pegawai Menerima
    * @attribute #catatan# => Menampilkan Catatan
    * @attribute #alamat_supplier# => Menampilkan alamat supplier
    * @attribute #no_telp# => Menampilkan alamat no telp
    * @attribute #no_fax# => Menampilkan alamat no fax
    **/
    public function actionPrintGrn() {
        try {
            $request = Yii::$app->request;
            $id = $request->get('id');
            $type = $request->get('type');
            $data_detail = self::getDataDetailPenerimaan($id, $type);
            $no_po = isset($data_detail["header"]["no_poobat"]) ? $data_detail["header"]["no_poobat"] : $data_detail["header"]["no_pobarang"];

            $sub_total = $data_detail["header"]['sub_total'];
            $total_discount = $data_detail["header"]['total_discount'];
            $ppn_nilai = $data_detail["header"]['ppn_nilai'];
            $total = $data_detail["header"]['total'];
            $total_ppn_nilai = ($sub_total - $total_discount) * $data_detail["header"]['pajak_persen']/100;
            $total_amount_discount = $sub_total - $total_discount;

            $subTotal = 0;
            $totalDiscount = 0;
            
            foreach ($data_detail["detail"] as $value) {
                $harga = ArrayHelper::getValue($value[0], 'harga', 0);
                $discount = ArrayHelper::getValue($value[0], 'discount', 0);
                $discount_rp = ArrayHelper::getValue($value[0], 'discount_rp', 0);
                $sum_qty_diterima = array_sum(array_column($value, 'qty_diterima'));
                
                $subTotal += ($sum_qty_diterima * $harga);

                if(isset($value[0]['is_disc_nominal']) && $value[0]['is_disc_nominal'] == true) {
                    $totalDiscount += $discount_rp;
                } else if ($discount > 0) {
                    $totalDiscount += ($harga * $sum_qty_diterima) * $discount / 100;
                }
            }

            $subTotalDiscount = $subTotal - $totalDiscount;
            $hargaPPN = $subTotalDiscount * $data_detail["header"]['pajak_persen']/100;
            $totalNet = $subTotalDiscount + $hargaPPN;
            $print = new DocoPrint;
            $print->attributes = [
                '#dataTable#' => $this->renderPartial('cetak_grn',[
                    'data'=> $data_detail["detail"],
                    'pajak_persen'=> $data_detail["header"]['pajak_persen'],
                    'subTotal' => @$subTotal,
                    'totalDiscount' => @$totalDiscount,
                    'subTotalDiscount' => @$subTotalDiscount,
                    'hargaPPN' => @$hargaPPN,
                    'totalNet' => @$totalNet,
                ]),
                '#nama_supplier#' => $data_detail["header"]["supplier_nama"] ,
                '#alamat_supplier#' => $data_detail["header"]["supplier_alamat"] ,
                '#no_telp#' => $data_detail["header"]["no_tlp"] ,
                '#no_fax#' => $data_detail["header"]["no_fax"] ,
                '#no_po#' => $no_po,
                '#no_faktur#' => $data_detail["header"]["no_faktur"] ,
                '#no_penerimaan#' => $data_detail["header"]["no_penerimaan"] ,
                '#no_sj#' => $data_detail["header"]["no_suratjalan"] ,
                '#tgl_sj#' => date("d-M-Y", strtotime($data_detail["header"]["tgl_suratjalan"])) ,
                '#tgl_penerimaan#' => date("d M Y H:i:s", strtotime($data_detail["header"]["tgl_penerimaan"])) ,
                '#mengetahui#' => !empty($data_detail["header"]["menerima"]) ? $data_detail["header"]["mengetahui"] : '...' ,
                '#menyetujui#' => !empty($data_detail["header"]["menerima"]) ? $data_detail["header"]["menyetujui"] : '...' ,
                '#menerima#' => !empty($data_detail["header"]["menerima"]) ? $data_detail["header"]["menerima"] : '...' ,
                '#catatan#' => !empty($data_detail["header"]["catatan"]) ? $data_detail["header"]["catatan"] : "-",
            ];
            $print->Output();
        }catch (\Yii\db\Exception $e) {
            return [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ];
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e){
            return [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ];
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    private function getDataDetailPenerimaan($id, $type) {
        if($type == 'obat') {
            $model_penerimaan = new InfoPenerimaanObat;
            $detail = new InfoPenerimaanObatDetail;
            $doc = new PenerimaanObatDoc;
            $pk_id = 'penerimaanobat_id';
            $validasidetail_id = 'validasipoobatdetail_id';
            $item_id = 'obatalkes_id';
            $item_nama = 'obatalkes_nama';
            $s_konversi_id = 's_konversiobt_id';
        } else {
            $model_penerimaan = new InfoPenerimaanBarang;
            $detail = new InfoPenerimaanBarangDetail;
            $doc = new PenerimaanBarangDoc;
            $pk_id = 'penerimaanbarang_id';
            $validasidetail_id = 'validasipobarangdetail_id';
            $item_id = 'barang_id';
            $item_nama = 'barang_nama';
            $s_konversi_id = 's_konversibrg_id';
        }

        $query_penerimaan = $model_penerimaan->find()
        ->where([
            $pk_id => $id
        ])->one();

        $list_pegawai = [
            "menyetujui" => [$query_penerimaan->peg_menyetujui => $query_penerimaan->menyetujui],
            "mengetahui" => [$query_penerimaan->peg_mengetahui => $query_penerimaan->mengetahui],
        ];

        $detail_penerimaan = $detail->find()->where([
            $pk_id => $id
        ])->orderby([
            $item_id => SORT_ASC,
            "tgl_kadaluarsa" => SORT_ASC
        ])
        ->all();

        $detail_rows = [];
        foreach ($detail_penerimaan as $row => $value) {
            $detail_rows[$value[$item_id]][] = [
                $item_id => $value[$item_id],
                $item_nama => $value[$item_nama],
                "qty_po" => $value["qty_po"],
                "po_balance" => $value["po_balance"],
                "qty_input" => $value["qty_diterima"],
                "qty_diterima" => $value["qty_diterima"],
                "satuan_besar" => $value["satuan_besar"],
                "satuan_kecil" => $value["satuan_kecil"],
                "tgl_kadaluarsa" => $value["tgl_kadaluarsa"],
                "no_batch" => $value["no_batch"],
                "keterangan" => $value["keterangan"],
                $s_konversi_id => $value[$s_konversi_id],
                "harga" => $value["harga"],
                "discount" => $value["discount"],
                "discount_rp" => $value["discount_rp"],
                "jumlah" => $value["jumlah"],
                $validasidetail_id => $value[$validasidetail_id],
                "kode_item" => $value["kode_item"],
                "satuanunit_nama" => $value["satuanunit_nama"],
            ];
        }

        $query_doc = $doc->find()->where([
            $pk_id => $id,
            "is_deleted" => false
        ])->all();

        return [
            'header' => $query_penerimaan,
            'detail' => $detail_rows,
            'list_pegawai' => $list_pegawai,
            'document'=> $query_doc,
            "detail_penerimaan" => $detail_penerimaan
        ];
    }
}
