<?php

namespace app\modules\v1\actions\TransaksiPengajuanKlaim;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\LookupTransaksi;
use app\modules\v1\models\PengajuanKlaim;
use app\modules\v1\models\PengajuanKlaimView;
use Doco\models\CaraBayar;
use Doco\models\Penjamin;

class CetakListAction extends BaseCurrentAction
{
    private function getPegawaiKabag()
    {
        $lookupTransaksi = LookupTransaksi::find()->where(['kode_transaksi' => DocoConstants::KABAG_KEUANGAN])->one();
        $pegawai = PegawaiView::find()
            ->findByJabatanId($lookupTransaksi->kode_id)
            ->findLastUpdated()
            ->orderByLastUpdated()
            ->one();
        return $pegawai;
    }

    private function getPengajuanKlaimDatas($penjaminId, $caraBayarId, $advancedFilters)
    {
        $helpers = new DocoHelpers;
        $model = new PengajuanKlaimView;
        $query = $model::find()
            ->findByPenjaminId($penjaminId)
            ->findByCaraBayarId($caraBayarId);
        $query->andWhere([
            'not',
            ['no_pembayaran' => null]
        ]);
        $query->andWhere(['pengajuanklaim_id' => null]);
        $tglAwal  = date('Y-m-d') . ' 00:00:00';
        $tglAkhir = date('Y-m-d') . ' 23:59:59';
        if ($advancedFilters) {
            $tglPasienPulang = ArrayHelper::getValue($advancedFilters, 'tglpasienpulang');
            $instalasiNama = ArrayHelper::getValue($advancedFilters, 'instalasi_nama');
            $ruanganNama = ArrayHelper::getValue($advancedFilters, 'ruangan_nama');
            $title = ArrayHelper::getValue($advancedFilters, 'title');
            // Tanggal Keluar
            if ($tglPasienPulang) {
                $tglPasienPulangRange = $helpers->parsingRangeDate($advancedFilters['tglpasienpulang']);
                $tglAwal  = $tglPasienPulangRange['startDate'];
                $tglAkhir = $tglPasienPulangRange['endDate'];
            }
            // Instalasi
            if ($instalasiNama) {
                $instalasi_id = $instalasiNama;
                $query->andWhere(['instalasi_id' => $instalasi_id]);
            }
            // Ruangan
            if ($ruanganNama) {
                $ruangan_id = $ruanganNama;
                $query->andWhere(['ruangan_id' => $ruangan_id]);
            }
        }
        $query->andWhere(['between', 'tglpasienpulang', $tglAwal, $tglAkhir]);
        $datas = $query->asArray()->all();
        return $this->convertPengajuanKlaimDatas($datas);
    }

    private function convertPengajuanKlaimDatas($pengajuanKlaimDatas)
    {
        $no = 1;
        $sumdata = [
            'total_tagihan'   => 0,
            'total_sdh_bayar' => 0,
            'total_discount'  => 0,
            'total_pengajuan' => 0,
            'total_tagihan_label'   => '',
            'total_sdh_bayar_label' => '',
            'total_discount_label'  => '',
            'total_pengajuan_label' => '',
        ];
        foreach ($pengajuanKlaimDatas as $key => $value) {
            $no++;
            $primaryKey                        = DocoHelpers::encrypt($value['pendaftaran_id'] . "-" . $value['pasienadmisi_id']);
            $value['pasienadmisi_id']          = $value['pasienadmisi_id'] ? $value['pasienadmisi_id'] : 0;
            $value['admisi']                   = DocoHelpers::encrypt($value['pasienadmisi_id']);
            $value['primary']                  = $primaryKey;
            $value['rowNum']                   = $no;
            $value['tgl_pendaftaran']          = date('d M Y', strtotime($value['tgl_pendaftaran']));
            $value['tglpasienpulang']          = !empty($value['tglpasienpulang']) ? date('d M Y', strtotime($value['tglpasienpulang'])) : '';
            $value['total_tagihan_label']      = DocoHelpers::rupiahDisplayWithoutSpace($value['total_tagihan']);
            $value['total_sdh_bayar_label']    = DocoHelpers::rupiahDisplayWithoutSpace($value['total_sdh_bayar']);
            $value['total_discount_label']     = DocoHelpers::rupiahDisplayWithoutSpace($value['total_discountpembayaran']);
            $value['total_asuransi_label']     = DocoHelpers::rupiahDisplayWithoutSpace($value['total_asuransi']);
            $value['total_sisa_tagihan_label'] = DocoHelpers::rupiahDisplayWithoutSpace($value['total_sisa_tagihan']);
            $value['jumlah_inacbg_label']      = DocoHelpers::rupiahDisplayWithoutSpace($value['jumlah_inacbg']);
            $value['total_pengajuan']          = $value['carabayar_id'] == 6 ? $value['jumlah_inacbg'] : $value['total_asuransi'];
            $value['total_pengajuan_label']    = $value['carabayar_id'] == 6 ? $value['jumlah_inacbg'] : $value['total_asuransi'];
            $value['total_pengajuan_label']    = DocoHelpers::rupiahDisplayWithoutSpace($value['total_pengajuan_label']);
            $data[$key] = $value;
            $sumdata['total_tagihan']         += $value['total_tagihan'];
            $sumdata['total_sdh_bayar']       += $value['total_sdh_bayar'];
            $sumdata['total_discount']        += empty($value['total_discountpembayaran']) ? 0 : $value['total_discountpembayaran'];
            $sumdata['total_pengajuan']       += $value['total_pengajuan'];
            $sumdata['total_tagihan_label']    = DocoHelpers::rupiahDisplayWithoutSpace($sumdata['total_tagihan']);
            $sumdata['total_sdh_bayar_label']  = DocoHelpers::rupiahDisplayWithoutSpace($sumdata['total_sdh_bayar']);
            $sumdata['total_discount_label']   = DocoHelpers::rupiahDisplayWithoutSpace($sumdata['total_discount']);
            $sumdata['total_pengajuan_label']  = DocoHelpers::rupiahDisplayWithoutSpace($sumdata['total_pengajuan']);
        }
        return [
            'data' => $data,
            'sumdata' => $sumdata
        ];
    }

    private function getCaraBayarNama($caraBayarId)
    {
        $caraBayar = CaraBayar::find()->where(['carabayar_id' => $caraBayarId])->asArray()->one();
        $caraBayarNama = ArrayHelper::getValue($caraBayar, 'carabayar_nama');
        return $caraBayarNama;
    }

    private function getPenjaminNama($penjaminId)
    {
        $penjamin = Penjamin::find()->where(['penjamin_id' => $penjaminId])->asArray()->one();
        $penjaminNama = ArrayHelper::getValue($penjamin, 'penjamin_nama');
        return $penjaminNama;
    }

    public function run()
    {
        $helpers = new DocoHelpers;
        $request = Yii::$app->request;
        $caraBayarId = $request->get('carabayar_id');
        $penjaminId = $request->get('penjamin_id');
        $advancedFilters = $request->get('advanced-filter');
        $defaultTglPasienPulang = date('j-M-Y') . ' - ' . date('j-M-Y');
        $tglPasienPulang = ArrayHelper::getValue($advancedFilters, 'tglpasienpulang');
        if(!$tglPasienPulang) $tglPasienPulang = $defaultTglPasienPulang;
        if (empty($caraBayarId)) {
            return $this->controller->responseJson(400, 'Cara bayar id tidak ditemukan');
            // \Yii::$app->response->statusCode = 400;
            // return ['error' => 'Cara bayar id tidak ditemukan'];
        }
        if (empty($penjaminId)) {
            return $this->controller->responseJson(400, 'Penjamin id tidak ditemukan');
            // \Yii::$app->response->statusCode = 400;
            // return ['error' => 'Penjamin id tidak ditemukan'];
        }
        $resultData = $this->getPengajuanKlaimDatas($penjaminId, $caraBayarId, $advancedFilters);
        $sumdata = $resultData['sumdata'];
        $data = $resultData['data'];
        $pegawaiData = $this->getPegawaiKabag();
        $namaPegawai = ArrayHelper::getValue($pegawaiData, 'nama_pegawai');
        $namaJabatan = ArrayHelper::getValue($pegawaiData, 'jabatan_nama');
        $nipPegawai = ArrayHelper::getValue($pegawaiData, 'nomorindukpegawai');
        $tanggalKeluar = $tglPasienPulang;
        $caraBayarNama = $this->getCaraBayarNama($caraBayarId);
        $penjaminNama = $this->getPenjaminNama($penjaminId);
        $totalPengajuan = ArrayHelper::getValue($sumdata, 'total_pengajuan');
        $totalPengajuanRp = DocoHelpers::rupiahDisplay($totalPengajuan);
        $print = new DocoPrint();
        $print->shrink_tables_to_fit  = 1;
        $print->attributes = [
            '#tanggal_keluar_range#' => $tanggalKeluar,
            '#cara_bayar#' => $caraBayarNama,
            '#penjamin#' => $penjaminNama,
            '#total_pengajuan#' => $totalPengajuanRp,
            '#datatable#' => $this->controller->renderPartial('_cetak', [
                'data' => $data,
                'summaries' => $sumdata
            ]),
            '#tanggal#'   => date('d M Y H:i:s'),
            '#jabatan#'   => $namaJabatan,
            '#pegawai#'   => $namaPegawai,
            '#nip#'       => $nipPegawai,
        ];
        $print->Output();
    }
}
