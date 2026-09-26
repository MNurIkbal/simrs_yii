<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace Doco\processes;

use app\modules\v1\models\InfoSatuanKonversi;
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
use app\modules\v1\models\ProfilRumahSakit;
use Doco\Repositories\KonfigRepositories;
use yii\db\Expression;

class CetakRincianPoProcess extends \Doco\components\DocoBaseProcessExtension
{
    protected $no_transaksi = [];
    protected $type_po = [];
    protected $result = [];
    protected $is_bgProcess = false;

    /**
     * is_bgProcess true untuk process cetak background process
     * printManual untuk cetak tanpa background process
     */
    protected function cetakRincian()
    {
        $printManual = $this->is_bgProcess ? null : new DocoPrint();
        $listInfoPo = InfoPoView::find()
            ->where(['IN', 'no_transaksi', $this->no_transaksi])
            ->andWhere(['IN', 'type_po',  $this->type_po])
            ->asArray()->all();

        $details = InfoPoDetailView::find()
            ->where(['IN', 'no_transaksi', ArrayHelper::getColumn($listInfoPo, 'no_transaksi')])
            ->andWhere(['IN', 'jenis', ArrayHelper::getColumn($listInfoPo, 'type_po')])
            ->orderBy(['nomor' => SORT_ASC, 'obat_barang_id' => SORT_ASC])
            ->asArray()->all();
        $datailsIndex = ArrayHelper::index($details, null, 'transaksi_id');

        $jenis = ArrayHelper::getColumn($details, 'jenis', []);
        $filterItem = ArrayHelper::getColumn($details, 'obat_barang_id', []);
        $satuanKonversi = InfoSatuanKonversi::find()->where(['jenis' => $jenis, 'obatalkes_id' => $filterItem])->all();
        $hasilKonversi = $labelKonversi = [];
        foreach ($satuanKonversi as $value) {
            $obatalkesId = ArrayHelper::getValue($value, 'obatalkes_id');
            $satuankonversiId = ArrayHelper::getValue($value, 'satuankonversi_id');
            $hasilKonversi[$obatalkesId][$satuankonversiId] = ArrayHelper::getValue($value, 'nilai_konversi');
            $labelKonversi[$obatalkesId][$satuankonversiId] = [
                "kecil" => ArrayHelper::getValue($value, 'satuan_kecil'),
                "besar" => ArrayHelper::getValue($value, 'satuan_besar')
            ];
        }

        $pajakId = ArrayHelper::getColumn($listInfoPo, 'pajak_id');
        $pajaks = Pajak::find()->select(['pajak_id', 'pajak_name', 'pajak_persen'])
        ->where(['IN', 'pajak_id', $pajakId])->andWhere(['is_active' => true])->asArray()->all();
        $pajaks = ArrayHelper::index($pajaks, 'pajak_id');
        
        $supplierId = ArrayHelper::getColumn($listInfoPo, 'supplier_id');
        $getSuppliers = Supplier::find()->select(['supplier_id','supplier_alamat', 'no_tlp', 'no_fax'])
            ->where(['IN', 'supplier_id',  $supplierId])->andWhere(['is_active' => true])->asArray()->all();
        $getSuppliers = ArrayHelper::index($getSuppliers, 'supplier_id');
        
        $tipe = [];
        foreach ($this->type_po as $key => $value) {
            $tipe[$key] = strtolower($value == 'obat' ? 'PO' : 'PONONMEDIS');
        }
        $jumlahPerubahan = LogActivityR::find()->select([
            'transaksi_id', 
            new Expression('count(*) as jumlah_perubahan')
        ])
            ->where(['IN', 'LOWER(tipe)', $tipe])
            ->andWhere(['IN', 'transaksi_id', ArrayHelper::getColumn($listInfoPo, 'transaksi_id')])
            ->andWhere(['<>', 'aksi', DocoConstants::LA_AKSI_TAMBAH])
            ->groupBy(['transaksi_id'])->asArray()->all();
        $jumlahPerubahan = ArrayHelper::index($jumlahPerubahan, 'transaksi_id');
        $profil_rs = ProfilRumahSakit::find()->select(['nama_rumahsakit'])->where(['profilrs_id' => 1])->one();

        foreach($listInfoPo as $key => $value) {
            $pajak = isset($pajaks[ArrayHelper::getValue($value, 'pajak_id')]) ? $pajaks[ArrayHelper::getValue($value, 'pajak_id')] : []; 
            $supplier = isset($getSuppliers[ArrayHelper::getValue($value, 'supplier_id')]) ? $getSuppliers[ArrayHelper::getValue($value, 'supplier_id')] : [];
            $jmlPerubahan = isset($jumlahPerubahan[ArrayHelper::getValue($value, 'transaksi_id')]) ? $jumlahPerubahan[ArrayHelper::getValue($value, 'transaksi_id')] : [];
            $listData = isset($datailsIndex[$value['transaksi_id']]) ? $datailsIndex[$value['transaksi_id']] : [];

            $sub_total = ArrayHelper::getValue($value, 'sub_total', 0);
            $total_discount = ArrayHelper::getValue($value, 'total_discount', 0);
            $pajak_persen = ArrayHelper::getValue($pajak, 'pajak_persen', 0);
            
            $total_ppn_nilai = ($sub_total - $total_discount) * $pajak_persen/100;
            $total_amount_discount = $sub_total - $total_discount;

            $nama_jabatan = [
                DocoConstants::DIREKTUR,
                DocoConstants::HEAD_OF_PURCHASING,
                DocoConstants::HEAD_OF_APOTEKER,
                DocoConstants::HEAD_OF_FINANCE
            ];
            $jabatan = $this->getIdJabatan($nama_jabatan);
            $pegawai = $this->getPegawaiByJabatan($jabatan);
            $sign_index = $this->getSignIndex($pegawai, $jabatan);

            if (ArrayHelper::getValue($value, 'type_po') == DocoConstants::JENIS_OBAT) {
                $verificator = DocoConstants::HEAD_OF_APOTEKER;
                $no_sipa = is_int($sign_index[$verificator]) ? "SIP . " . $pegawai[$sign_index[$verificator]]['suratizinpraktek'] : "";
            } else {
                $verificator = DocoConstants::HEAD_OF_FINANCE;
                $no_sipa = "";
            }

            $konfig = KonfigRepositories::getKonfigFarmasi();
            $expired_days = $konfig['po_expired'];

            $this->result[] = [
                '#nomor_po#' => ArrayHelper::getValue($value, 'no_transaksi') && ArrayHelper::getValue($value, 'is_validasi', false) ? ArrayHelper::getValue($value, 'no_transaksi', '-') : '-',
                '#no_transaksi#' => ArrayHelper::getValue($value, 'no_transaksi', '-'),
                '#tanggal_po#' => !empty(ArrayHelper::getValue($value, 'tanggal_po')) ? date('d M Y H:i:s', strtotime(ArrayHelper::getValue($value, 'tanggal_po'))) : '-',
                '#tanggal_expired#' => !empty(ArrayHelper::getValue($value, 'tanggal_po')) ? date('d M Y', strtotime(ArrayHelper::getValue($value, 'tanggal_po') . " + {$expired_days} days")) : '-',
                '#paymen_term#' => ArrayHelper::getValue($value, 'payment_term', '-'),
                '#pajak#' => ArrayHelper::getValue($pajak, 'pajak_name', '-'),
                '#supplier#' => isset($value['supplier_nama']) ?  $this->capwords($value['supplier_nama']) : '-',
                '#alamat_supplier#' => ArrayHelper::getValue($supplier, 'supplier_alamat', '-'),
                '#no_telp#' => ArrayHelper::getValue($supplier, 'no_tlp', '-'),
                '#no_fax#' => ArrayHelper::getValue($supplier, 'no_fax', '-'),
                '#rencana_terima#' => !empty(ArrayHelper::getValue($value, 'tgl_rencanaterima')) ? date('d M Y H:i:s', strtotime(ArrayHelper::getValue($value, 'tgl_rencanaterima'))) : '-',
                '#ruangan#' => ArrayHelper::getValue($value, 'ruangan_nama', '-'),
                '#jumlah_perubahan#' => ArrayHelper::getValue($jmlPerubahan, 'jumlah_perubahan', '-'),
                '#mengetahui#' => ArrayHelper::getValue($value, 'peg_mengetahui', '...'),
                '#catatan2#' => isset($value['catatan2']) ? nl2br($value['catatan2']) : '-',
                '#dibuat_oleh#' => ArrayHelper::getValue($value, 'diorder_oleh_nama', '...'),
                '#menyetujui#' => ArrayHelper::getValue($value, 'peg_menyetujui', '...'),
                '#jenis_po#' => ArrayHelper::getValue($value, 'po_cito', '-'),
                '#po_consigment#' => ArrayHelper::getValue($value, 'po_consigment', '-'),
                '#po_admin#' => ArrayHelper::getValue($value, 'po_admin', '-'),
                '#tabel#' => Yii::$app->controller->renderPartial('_detail', [
                    'data' => $listData,
                    'sub_total' => $sub_total,
                    'total_discount' => $total_discount,
                    'ppn_nilai' => $total_ppn_nilai,
                    'total' => $total_ppn_nilai + $total_amount_discount,
                    'total_amount_discount' => $total_amount_discount,
                    'pajak_persen' => $pajak_persen,
                    'hasil_konversi' => $hasilKonversi,
                    'label_konversi' => $labelKonversi,
                    'font' => $this->getFont()
                ]),
                '#nama_direktur#' => is_int($sign_index[DocoConstants::DIREKTUR]) ? $this->capwords($pegawai[$sign_index[DocoConstants::DIREKTUR]]['nama_pegawai']) : "",
                '#nama_head_purchasing#' => is_int($sign_index[DocoConstants::HEAD_OF_PURCHASING]) ? $this->capwords($pegawai[$sign_index[DocoConstants::HEAD_OF_PURCHASING]]['nama_pegawai']) : "",
                '#nama_head_apoteker#' => is_int($sign_index[$verificator]) ? $this->capwords($pegawai[$sign_index[$verificator]]['nama_pegawai']) : "",
                '#jabatan_direktur#' => is_int($sign_index[DocoConstants::DIREKTUR]) ? $this->capwords($pegawai[$sign_index[DocoConstants::DIREKTUR]]['jabatan_nama']) : "",
                '#jabatan_head_purchasing#' => is_int($sign_index[DocoConstants::HEAD_OF_PURCHASING]) ? $this->capwords($pegawai[$sign_index[DocoConstants::HEAD_OF_PURCHASING]]['jabatan_nama']) : "",
                '#jabatan_head_apoteker#' => is_int($sign_index[$verificator]) ? $this->capwords($pegawai[$sign_index[$verificator]]['jabatan_nama']) : "",
                '#no_sipa#' => $no_sipa,
                '#nama_rs#' => $profil_rs->nama_rumahsakit
            ];
            /** untuk print manual */
            if (!$this->is_bgProcess) {
                $printManual->attributes = $this->result[$key];
                $break = $key >= (count($this->no_transaksi) - 1) ? false : true; // jika key sekarang sama dengan key terakhir dari foreach, maka tidak usah menggunakan break
                $printManual->generateHtml($break, null, true);
            }
        }
        if (!$this->is_bgProcess) $printManual->Output(true);
    }

    protected function getFont() {
        return [
            'family' => 'Tahoma',
            'size' => '10px'
        ];
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
        $request = Yii::$app->request;
        $this->no_transaksi = $request->get('no_transaksi', []);
        $this->type_po = $request->get('type_po', []);
        $this->is_bgProcess = $request->get('is_bgprocess', false);

        $this->cetakRincian();
        if ($this->is_bgProcess) {
            return $this->result;
        }
    }
}
