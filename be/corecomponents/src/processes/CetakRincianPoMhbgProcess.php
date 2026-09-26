<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace Doco\processes;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\exceptions\ValidationException;
use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;

use app\modules\v1\models\Pajak;
use app\modules\v1\models\InfoPoView;
use app\modules\v1\models\InfoPoDetailView;
use app\modules\v1\models\Supplier;
use app\modules\v1\models\PegawaiMasterView;
use app\modules\v1\models\LogActivityR;

class CetakRincianPoMhbgProcess extends \Doco\components\DocoBaseProcessExtension
{
    protected function cetakRincian()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $type_po = $request->get('type_po');

        $model = InfoPoView::find()->where([
            'transaksi_id' => $id,
            'type_po' => $type_po
        ])->one();

        $detail = InfoPoDetailView::find()->where([
            'transaksi_id' => $id,
            'jenis' => $type_po
        ])->all();

        $pajak = Pajak::find()->where([
            'is_active' => true,
            'pajak_id' => $model->pajak_id
        ])->one();

        $getSupplier = Supplier::find()->where([
            'is_active' => true,
            'supplier_id' => $model->supplier_id
        ])->one();

        $jumlah_perubahan = LogActivityR::find()
            ->where([
                'tipe'=>$type_po == 'obat' ? 'PO' : 'PONONMEDIS',
                'transaksi_id'=>$id
            ])->count();

        $print = new DocoPrint();
        $sub_total = $model->sub_total;
        $total_discount = $model->total_discount;
        $pajak_persen = !is_null($pajak) ? $pajak->pajak_persen : 0;

        $nama_jabatan = [
            DocoConstants::DIREKTUR,
            DocoConstants::HEAD_OF_PURCHASING,
            DocoConstants::HEAD_OF_APOTEKER,
            DocoConstants::HEAD_OF_FINANCE
        ];

        $jabatan = $this->getIdJabatan($nama_jabatan);
        $pegawai = $this->getPegawaiByJabatan($jabatan);
        $sign_index = $this->getSignIndex($pegawai, $jabatan);

        $total_ppn_nilai = ($sub_total - $total_discount) * $pajak_persen/100;
        $total_amount_discount = $sub_total - $total_discount;

        if($type_po == DocoConstants::JENIS_OBAT) {
            $verificator = DocoConstants::HEAD_OF_APOTEKER;
            $no_sipa = is_int($sign_index[$verificator]) ? "SIP . " . $pegawai[$sign_index[$verificator]]['suratizinpraktek'] : "";
        } else {
            $verificator = DocoConstants::HEAD_OF_FINANCE;
            $no_sipa = "";
        }

        $print->attributes = [
            '#nomor_po#' => !empty($model->no_transaksi) && !empty($model->is_validasi)
                                        ? $model->no_transaksi : '-',
            '#tanggal_po#' => !empty($model->tanggal_po) ? date('d M Y H:i:s', strtotime($model->tanggal_po)) : '-',
            '#paymen_term#' => !empty($model->payment_term) ? $model->payment_term : '-',
            '#pajak#' => !empty($pajak->pajak_name) && !is_null($pajak) ? $pajak->pajak_name : '-',
            '#supplier#' => !empty($model->supplier_nama) ? $this->capwords($model->supplier_nama) : '-',
            '#alamat_supplier#' => !empty($getSupplier->supplier_alamat) ? $getSupplier->supplier_alamat : '-',
            '#no_telp#' => !empty($getSupplier->no_tlp) ? $getSupplier->no_tlp : '-',
            '#no_fax#' => !empty($getSupplier->no_fax) ? $getSupplier->no_fax : '-',
            '#rencana_terima#' => !empty($model->tgl_rencanaterima)
                                        ? date('d M Y', strtotime($model->tgl_rencanaterima)) : '-',
            '#ruangan#' => !empty($model->ruangan_nama) ? $model->ruangan_nama : '-',
            '#jumlah_perubahan#' => $jumlah_perubahan >0 ? $jumlah_perubahan : '-',
            '#mengetahui#' => !empty($model->peg_mengetahui) ? $model->peg_mengetahui : '...',
            '#catatan1#' => !empty($model->catatan1) ? nl2br($model->catatan1) : '-',
            '#catatan2#' => !empty($model->catatan2) ? nl2br($model->catatan2) : '-',
            '#dibuat_oleh#' => !empty($model->diorder_oleh_nama) ? $model->diorder_oleh_nama : '...',
            '#menyetujui#' => !empty($model->peg_menyetujui) ? $model->peg_menyetujui : '...',
            '#tabel#' => Yii::$app->controller->renderPartial('_detail_mhbg', [
                'data' => $detail,
                'sub_total' => $sub_total,
                'total_discount' => $total_discount,
                'ppn_nilai' => $total_ppn_nilai,
                'total' => $total_ppn_nilai + $total_amount_discount,
                'total_amount_discount' => $total_amount_discount,
                'pajak_persen' => $pajak_persen
            ]),
            '#nama_direktur#'        => is_int($sign_index[DocoConstants::DIREKTUR]) ?
                                        $this->capwords($pegawai[$sign_index[DocoConstants::DIREKTUR]]['nama_pegawai']) : "",
            '#nama_head_purchasing#' => is_int($sign_index[DocoConstants::HEAD_OF_PURCHASING]) ?
                                        $this->capwords($pegawai[$sign_index[DocoConstants::HEAD_OF_PURCHASING]]['nama_pegawai']) : "",
            '#nama_head_apoteker#'   => is_int($sign_index[$verificator]) ?
                                        $this->capwords($pegawai[$sign_index[$verificator]]['nama_pegawai']) : "",
            '#jabatan_direktur#'     => is_int($sign_index[DocoConstants::DIREKTUR]) ?
                                        $this->capwords($pegawai[$sign_index[DocoConstants::DIREKTUR]]['jabatan_nama']) : "",
            '#jabatan_head_purchasing#' => is_int($sign_index[DocoConstants::HEAD_OF_PURCHASING]) ?
                                           $this->capwords($pegawai[$sign_index[DocoConstants::HEAD_OF_PURCHASING]]['jabatan_nama']) : "",
            '#jabatan_head_apoteker#' => is_int($sign_index[$verificator]) ?
                                         $this->capwords($pegawai[$sign_index[$verificator]]['jabatan_nama']) : "",
            '#no_sipa#' => $no_sipa
        ];
        $print->Output();
    }

    protected function capwords($str) {
        return ucwords(strtolower($str));
    }

    protected function getIdJabatan($nama_jabatan){
        $list_id_jabatan = [];
        foreach ($nama_jabatan as $value) {
            $list_id_jabatan[$value] = DocoConstansId::actionGetId($value);
        }
        return $list_id_jabatan;
    }

    protected function getPegawaiByJabatan($jabatan) {
        return PegawaiMasterView::find()->select([
            'distinct on (jabatan_id) nama_pegawai', 'jabatan_nama', 'jabatan_id', 'suratizinpraktek'
        ])->where([
            'IN', 'jabatan_id', $jabatan
        ])->asArray()->all();
    }

    protected function getSignIndex($pegawai, $jabatan) {
        $list_index = [];
        foreach ($jabatan as $key => $value) {
            $list_index[$key] = array_search($jabatan[$key], array_column($pegawai, 'jabatan_id'));
        }
        return $list_index;
    }

    protected function processFlow()
    {
        $this->cetakRincian();
    }
}
