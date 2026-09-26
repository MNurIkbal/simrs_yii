<?php

/**
 * @author : Budi (budi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Extensions\kasir;

use Yii;
use app\modules\v1\cache\Cache;
use app\modules\v1\models\ValidateInjention;
use app\modules\v1\models\RincianPasienView;
use app\modules\v1\models\PemberianPiutang;
use app\modules\v1\models\PendaftaranPenjamin;
use app\modules\v1\models\RincianPasienDetail2View;
use app\modules\v1\models\SummaryKelompokTindakanView;
use app\modules\v1\models\PasienBelumBayar;
use app\modules\v1\models\InfoDataPendaftaran;
use app\modules\v1\models\BayarUangMuka;
use Doco\models\KonfigSystem;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;
use Doco\models\Pegawai;
use app\modules\v1\businessLogic\TagihanHelper;
use app\modules\v1\models\DaftarTindakan;

class CetakRincianKramat extends \Doco\processes\CetakRincianProcess
{
    protected $dokPath = 'rincian-kramat';
    public function rincian()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id', null);
        $header = RincianPasienView::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
        if (!is_null($header['pasienadmisi_id'])){
            return $this->printRincian();
        } else {
            return $this->printRincianNonRanap($pendaftaran_id);
        }
    }

    protected function printRincianNonRanap($pendaftaran_id)
    {   
        error_reporting(0);
        $qDetailVal = $data = $qDetail = [];
        $totalRincian = 0;
        $header = InfoDataPendaftaran::find()
            ->select([
                'tgl_pendaftaran',
                'no_pendaftaran',
                'pendaftaran_id',
                'no_rekam_medik',
                'nama_pasien',
                'tanggal_lahir',
                'nama_dok_rj_rd',
                'penjamin_nama',
                'pasienadmisi_id',
                'rua_nama',
                'ruangan_nama'
            ])
            ->andWhere(['pendaftaran_id' => $pendaftaran_id])
            ->asArray()
            ->one();

        $tglPendaftaran = !empty($header['tgl_pendaftaran']) ? date('d-m-Y', strtotime($header['tgl_pendaftaran'])) : '-';
        $tglLahir = !empty($header['tanggal_lahir']) ? date('d-M-Y', strtotime($header['tanggal_lahir'])) : '-';
        $noPendaftaran = !empty($header['no_pendaftaran']) ? $header['no_pendaftaran'] : '-';
        $dokterPraktek = !empty($header['nama_dok_rj_rd']) ? $header['nama_dok_rj_rd'] : '-';
        $noRekamMedik = !empty($header['no_rekam_medik']) ? $header['no_rekam_medik'] : '-';
        $namaPasien = !empty($header['nama_pasien']) ? $header['nama_pasien'] : '-';
        $penjaminNama = !empty($header['penjamin_nama']) ? $header['penjamin_nama'] : '-';
        $ruangan_nama = !empty($header['ruangan_nama']) ? $header['ruangan_nama'] : '-';
        $qDetail = $this->getDataDetailTagihan($pendaftaran_id);
        $dataTotal = $this->getTotalHeaderPembayaran($pendaftaran_id, $header);
        $uang_muka = isset($dataTotal['uang_muka']) ? $dataTotal['uang_muka'] : 0;
        $total_discount = isset($dataTotal['total_discount']) ? $dataTotal['total_discount'] : 0;
        $total_dijamin = isset($dataTotal['total_dijamin']) ? $dataTotal['total_dijamin'] : 0;
        $total_administrasi = $this->generateTotalAdministrasi($pendaftaran_id, $dataTotal['total_tagihan']);
        foreach ($qDetail as $key => $value){
            $qDetailVal ['kelompok'] = isset($value['kelompoktindakan_nama']) ? $value['kelompoktindakan_nama'] : '-';
            $qDetailVal ['total'] = isset($value['sub_total']) ? $value['sub_total'] : 0;
            $qDetailVal ['qty'] = isset($value['qty']) ? $value['qty'] : 0;
            $qDetail[$key] = $qDetailVal;
            $totalRincian += $value['sub_total'];     
        }
        $tagihanPasien = $totalRincian + $total_administrasi - $uang_muka;
        $pembulatanTagihan = $this->getPembulatan($tagihanPasien);
        $total_pembulatan = isset($pembulatanTagihan['nominalPembulatan']) ?  $pembulatanTagihan['nominalPembulatan'] : 0;
        $total_ditagihkan = isset($pembulatanTagihan['total_ditagihkan']) ? (int) $pembulatanTagihan['total_ditagihkan'] : (int) $tagihanPasien;
        $pegawai_id = Yii::$app->jwt->user->pegawai_id;
        $data_pegawai = $this->getDataPegawai($pegawai_id)->asArray()->one();
        $pegawai_login = isset($data_pegawai['nama_pegawai']) ? $data_pegawai['nama_pegawai'] : '';
        $print = new DocoPrint('invoice-kramat');
        $print->attributes = [
            '#tgl_invoice#' => date('d-m-Y H:i'),
            '#no_transkasi#' => '-',
            '#nama_pasien#' => $noRekamMedik .' '. $namaPasien,
            '#nama_pasien_origin#' => $namaPasien,
            '#no_rekam_medik#' => $noRekamMedik,
            '#tgl_lahir#' => $tglLahir,
            '#dokter#' => $dokterPraktek,
            '#tgl_pelayanan#' => $tglPendaftaran,
            '#penjamin#' => $penjaminNama,
            '#nama_kasir#' => $pegawai_login,
            '#title#' => 'PERINCIAN BIAYA DAN PEMBAYARAN',
            '#ruangan_nama#' => $ruangan_nama,
            '#no_pendaftaran#' => $noPendaftaran,
            '#printed_by#' => $pegawai_login,
            '#subTotal#' => DocoHelpers::formatNumber($totalRincian),
            '#total_administrasi#' => DocoHelpers::formatNumber($total_administrasi),
            '#total_discount#' => DocoHelpers::formatNumber($total_discount),
            '#penggunaan_uangmuka#' => DocoHelpers::formatNumber($uang_muka),
            '#total_dijamin#' => DocoHelpers::formatNumber($total_dijamin),
            '#nominalPembulatan#' => DocoHelpers::formatNumber($total_pembulatan),
            '#total_ditagihkan#' => DocoHelpers::formatNumber($total_ditagihkan),
            '#datatable#' => Yii::$app->controller->renderPartial('invoice-kramat', [
                'dataTindakan' => $qDetail,
            ]),
        ];
        
        $print->Output();
    }

    protected function printRincian()
    {
        error_reporting(0);
        $nama_pemakai = Yii::$app->jwt->user->nama_pemakai;
        $qDetailVal = $qDetail = [];
        $totalRincian = 0;   
        $request = Yii::$app->request;
        $db = Yii::$app->db;
        $pendaftaran_id = $request->get('id', null);
        $modelVal = new ValidateInjention;
        $modelVal->pendaftaran_id = $pendaftaran_id;
        $infoPasien = $this->getDataPendaftaranRincian($pendaftaran_id);
        if(!$modelVal->validate()){
            return [
                'status' => 422,
                'data' => $modelVal->errors
            ];
        }

        $header = $this->getKunjunganRanap($pendaftaran_id);
        $historyKamar = $this->getHistoryPindahKamar($pendaftaran_id);
        $pasienadmisi_id = isset($header['pasienadmisi_id']) ? $header['pasienadmisi_id'] : '';
        $detailTindakan = $this->getDataDetailTagihan($pendaftaran_id);
        $detailAkomodasi = [];
        $totalAkomodasiSementara = 0;
        if(!empty($pasienadmisi_id)) {
            $data_akomodasi = $this->getAkomodasi($pasienadmisi_id);
            $tindakan_akomodasi = isset($data_akomodasi['tindakan_akomodasi']) ? $data_akomodasi['tindakan_akomodasi'] : [];
            $total_akomodasi = isset($data_akomodasi['total_akomodasi']) ? $data_akomodasi['total_akomodasi'] : 0;
            $qtyAkomodasi = 0;
            $totalTarifTindakan = 0;
            $paramsAkomodasi = [
                'penjamin_id' => null,
                'kelaspelayanan_id' => null,
                'type' => 'kamar',
                'kamarruangan_id' => null,
                'carabayar_id' => null,
                'instalasi_id' => null,
                'ruangan_id' => null,
            ];
            if(!empty($tindakan_akomodasi)) {
                foreach ($tindakan_akomodasi as $key => $value) {
                    $kamarruangan_id = isset($value['kamarruangan_id']) ? $value['kamarruangan_id'] : '';
                    $kelaspelayanan_id = isset($value['kelaspelayanan_id']) ? $value['kelaspelayanan_id'] : '';
                    $penjamin_id = isset($value['penjamin_id']) ? $value['penjamin_id'] : '';
                    $carabayar_id = isset($value['carabayar_id']) ? $value['carabayar_id'] : '';
                    $instalasi_id = isset($value['instalasi_id']) ? $value['instalasi_id'] : '';
                    $ruangan_id = isset($value['ruangan_id']) ? $value['ruangan_id'] : '';
                    $kamartempattidur_id = isset($value['kamartempattidur_id']) ? $value['kamartempattidur_id'] : '';
                    $paramsAkomodasi = [
                        'penjamin_id' => $penjamin_id,
                        'kelaspelayanan_id' => $kelaspelayanan_id,
                        'type' => 'kamar',
                        'kamarruangan_id' => $kamarruangan_id,
                        'carabayar_id' => $carabayar_id,
                        'instalasi_id' => $instalasi_id,
                        'ruangan_id' => $ruangan_id,
                    ];
                    $tempat_tidur = $no_kamar = '';
                    $harga_satuan = $this->getTarifkamar($paramsAkomodasi);
                    if(!empty($kamartempattidur_id)) {
                        $tempat_tidur = $this->getTempatTidur($kamartempattidur_id);
                    }
                    if(!empty($kamarruangan_id)) {
                        $no_kamar = $this->getNokamar($kamarruangan_id);
                    }
                    $kelaspelayanan_nama = isset($value['kelaspelayanan_nama']) ? $value['kelaspelayanan_nama'] : '';
                    $ruangan_nama = isset($value['ruangan_nama']) ? $value['ruangan_nama'] : '-';
                    $additionalData = isset($value['additional_data']) ? $value['additional_data'] : [];
                    $additionalData = json_decode($additionalData, true);
                    $detail_akomodasi = isset($additionalData['detail_akomodasi']) ? $additionalData['detail_akomodasi'] : [];
                    $persentase = isset($detail_akomodasi['persentase']) ? $detail_akomodasi['persentase'] : 0;
                    $tgl_masuk = isset($tindakan_akomodasi[0]['tgl_tindakan']) ? date('d/m/Y', strtotime($tindakan_akomodasi[0]['tgl_tindakan'])) : '-';
                    $tarif_tindakan = isset($value['tarif_tindakan']) ? $value['tarif_tindakan'] : 0;
                    $qty_tindakan = isset($value['qty_tindakan']) ? $value['qty_tindakan'] : 1;
                    if($persentase == 50) {
                        $qty_tindakan = 0.5;
                    }
                    $qtyAkomodasi += $qty_tindakan;
                    $totalTarifTindakan += $tarif_tindakan;
                    $totalAkomodasiSementara = $totalTarifTindakan;
                    $detailAkomodasi[$kamarruangan_id][$kelaspelayanan_id]['lama_rawat'] = $qtyAkomodasi;
                    $detailAkomodasi[$kamarruangan_id][$kelaspelayanan_id]['tarif_tindakan'] = $totalTarifTindakan;
                    $detailAkomodasi[$kamarruangan_id][$kelaspelayanan_id]['kamar'] = $ruangan_nama.' - '.$no_kamar;
                    $detailAkomodasi[$kamarruangan_id][$kelaspelayanan_id]['kelas'] = $kelaspelayanan_nama;
                    $detailAkomodasi[$kamarruangan_id][$kelaspelayanan_id]['tgl_masuk'] = $tgl_masuk;
                    $detailAkomodasi[$kamarruangan_id][$kelaspelayanan_id]['tgl_keluar'] = date('d/m/Y');
                    $detailAkomodasi[$kamarruangan_id][$kelaspelayanan_id]['tempat_tidur'] = $tempat_tidur;
                    $detailAkomodasi[$kamarruangan_id][$kelaspelayanan_id]['harga_satuan'] = $harga_satuan;
                }
            }
        }
        $dokter = "nama_dok_rj_rd";
        $tempat_tidur = isset($header['tempat_tidur']) ? $header['tempat_tidur'] : '-';
        $no_pendaftaran = isset($header['no_pendaftaran']) ? $header['no_pendaftaran'] : '-';
        $no_rekam_medik = isset($header['no_rekam_medik']) ? $header['no_rekam_medik'] : null;
        $nama_pasien = isset($header['nama_pasien']) ? $header['nama_pasien'] : '-';
        $nama_depan = isset($header['nama_depan']) ? $header['nama_depan'] : '-';
        $tgl_masuk = isset($header['tgl_admisi']) ? date('d M Y', strtotime($header['tgl_admisi'])) : '-';
        $penjaminId = isset($header['penjamin_id']) ? $header['penjamin_id'] : '';
        $kelasPelayananId = isset($header['kelaspelayanan_id']) ? $header['kelaspelayanan_id'] : '';
        $bed_no = isset($header['kamarruangan_nokamar']) ? $header['kamarruangan_nokamar'] : '';
        $bed = isset($header['no_tempattidur']) ? $header['no_tempattidur'] : '-';
        $dokter_nama = isset($header['nama_pegawai']) ? $header['nama_pegawai'] : '';
        $tgl_pulang = !empty($header['tgl_stopakomodasi']) ? date('d/M/Y H:i', strtotime($header['tgl_stopakomodasi'])) : '-';
        $tgl_admisi = !empty($header['tgl_admisi']) ? date('d/M/Y H:i', strtotime($header['tgl_admisi'])) : '-';
        $nama_ruangan = isset($header['ruangan_nama']) ? $header['ruangan_nama'] : '';
        $kelas_pelayanan = isset($header['kelaspelayanan_nama']) ? $header['kelaspelayanan_nama'] : '';
        $ruangan_pindah = $kelas_ditagihkan_nama = '';
        if(!empty($historyKamar)) {
            $kamar_pindah = isset($historyKamar['kamar_pindah']) ? $historyKamar['kamar_pindah'] : '';
            $tempattidur_pindah = isset($historyKamar['tempattidur_pindah']) ? $historyKamar['tempattidur_pindah'] : '';
            $dokter_admisi = isset($historyKamar['dokter_admisi']) ? $historyKamar['dokter_admisi'] : '';
            $ruangan_pindah = isset($historyKamar['ruangan_pindah']) ? $historyKamar['ruangan_pindah'] : '';
            $kelasPelayananId = isset($historyKamar['kelaspelayanan_id']) ? $historyKamar['kelaspelayanan_id'] : '';
            $kelas_ditagihkan_id = isset($historyKamar['kelas_ditagihkan_id']) ? $historyKamar['kelas_ditagihkan_id'] : '';
            $kelas_ditagihkan_nama = isset($historyKamar['kelas_ditagihkan_nama']) ? $historyKamar['kelas_ditagihkan_nama'] : '';
            if(!empty($ruangan_pindah)) {
                $nama_ruangan = $ruangan_pindah;
            }
            if(!empty($dokter_admisi)) {
                $dokter_nama = $dokter_admisi;
            }
            if(!empty($tempattidur_pindah)) {
                $bed = $tempattidur_pindah;
            }
            if(!empty($kamar_pindah)) {
                $bed_no = $kamar_pindah;
            }
            if(!empty($kelas_ditagihkan_id)) {
                $kelasPelayananId = $kelas_ditagihkan_id;
            }
            if(!empty($kelas_ditagihkan_nama)) {
                $kelas_pelayanan = $kelas_ditagihkan_nama;
            }
        }
        
        $carabayar_nama = isset($header['carabayar_nama']) ? $header['carabayar_nama'] : '';
        $penjamin_nama = isset($header['penjamin_nama']) ? $header['penjamin_nama'] : '';
        $alamat_pasien = isset($header['alamat_pasien']) ? $header['alamat_pasien'] : '';
        $umur = isset($header['umur']) ? $header['umur'] : '';
        $jenis_kelamin = isset($header['jenis_kelamin']) ? $header['jenis_kelamin'] : '';
        $hakKelas = isset($header['klsrawathak']) ? $header['klsrawathak'] : '';
        $kelasHak = isset($header['kelas_hak']) ? $header['kelas_hak'] : '';
        $hakKelas = empty($hakKelas) ? $kelasHak : $hakKelas;

        $statusKelas = isset($header['status_kelas']) ? $header['status_kelas'] : '';
        $dataTotal = $this->getTotalHeaderPembayaran($pendaftaran_id, $header);
        
        $total_asuransi = isset($dataTotal['total_asuransi']) ? $dataTotal['total_asuransi'] : 0;
        $uang_muka = isset($dataTotal['uang_muka']) ? $dataTotal['uang_muka'] : 0;
        $total_discount = isset($dataTotal['total_discount']) ? $dataTotal['total_discount'] : 0;
        $biayaAdminSudahBayar = $this->getBiayaAdmSudahBayar($pendaftaran_id);
		$biayaAdminSudahBayar = isset($biayaAdminSudahBayar['total_administrasi']) ? $biayaAdminSudahBayar['total_administrasi'] : 0;
        $total_administrasi = TagihanHelper::getBiayaAdmin($pendaftaran_id, $penjaminId, $kelasPelayananId, $dataTotal['total_tagihan'], $pasienadmisi_id);
        $dataPembulatanPenjamin = $this->getPembulatan($total_asuransi);
        $nominalPembulatanPenjamin = isset($dataPembulatanPenjamin['nominalPembulatan']) ? $dataPembulatanPenjamin['nominalPembulatan'] : 0;
        $total_asuransi = isset($dataPembulatanPenjamin['total_ditagihkan']) ? $dataPembulatanPenjamin['total_ditagihkan'] : 0;
        foreach ($detailTindakan as $key => $value) {
            $total = isset($value['sub_total']) ? $value['sub_total'] : 0;
            $totalRincian += $total;
        }
        $subTotal = $totalRincian + $totalAkomodasiSementara;
        $tagihanPasien = $totalRincian + $total_administrasi - $uang_muka - $total_asuransi;
        $dataPembulatan = $this->getPembulatan($tagihanPasien);
        $total_pembulatan = isset($dataPembulatan['nominalPembulatan']) ? $dataPembulatan['nominalPembulatan'] : 0;
        $total_ditagihkan = isset($dataPembulatan['total_ditagihkan']) ? $dataPembulatan['total_ditagihkan'] : 0;
        $print = new DocoPrint('cetak-rincian-tagihan-ranap');
        $strNamaDepan = '';
        if(!empty($nama_depan) && $nama_depan != '-') {
            $strNamaDepan = $nama_depan;
        }
        $print->attributes = [
            '#admission#' => $tgl_admisi,
            '#no_rekam_medik#' => $no_rekam_medik,
            '#no_pendaftaran#' => $no_pendaftaran,
            '#nama_pasien#' => $strNamaDepan.' '.$nama_pasien,
            '#nama_dok_rj_rd#'=> $infoPasien[$dokter],
            '#rua_nama#'=> $nama_ruangan,
            '#kelaspelayanan_nama#'=> $kelas_pelayanan,
            '#payer#'=> $penjamin_nama,
            '#carabayar_nama#'=> $carabayar_nama,
            '#status_bayar#'=> isset($infoPasien['status_bayar']) ? $infoPasien['status_bayar'] : '-',
            '#printed_by#' => Yii::$app->jwt->user->nama_pemakai,
            '#printed_date#' => date('d/M/Y H:i'),
            '#bill_no#' => '-',
            '#bill_date#' => '-',
            '#address#' => $alamat_pasien,
            '#age#' => $umur,
            '#gender#' => $jenis_kelamin,
            '#ward#' => $nama_ruangan,
            '#bed_no#' => $bed_no.'/' . $bed,
            '#bed_type#' => $kelas_pelayanan,
            '#primary_doctor#' => $dokter_nama,
            '#discharge_date#' => $tgl_pulang,
            '#title#' => 'REKAPITULASI PEMBAYARAN',
            '#printed_date#' => date('d-m-Y H:i'),
            '#printed_by#' => $nama_pemakai,
            '#pegawai#' => $nama_pemakai,
            '#ruangan_titipan_nama#' => $ruangan_pindah,
            '#kelas_ditagihkan_nama#' => $kelas_ditagihkan_nama,
            '#hakKelas#' => $hakKelas,
            '#statusKelas#' => $statusKelas,
            '#nama_depan#' => $nama_depan,
            '#subTotal#' => DocoHelpers::formatNumber($subTotal),
            '#total_administrasi#' => DocoHelpers::formatNumber($total_administrasi),
            '#total_discount#' => DocoHelpers::formatNumber($total_discount),
            '#penggunaan_uangmuka#' => DocoHelpers::formatNumber($uang_muka),
            '#total_dijamin#' => DocoHelpers::formatNumber($total_asuransi),
            '#total_pembulatan#' => DocoHelpers::formatNumber($total_pembulatan),
            '#total_ditagihkan#' => DocoHelpers::formatNumber($total_ditagihkan + $totalAkomodasiSementara),
            '#nominalPembulatanPenjamin#' => $nominalPembulatanPenjamin,
            '#kelompok_tindakan#' => Yii::$app->controller->renderPartial($this->dokPath, [
                'detailTindakan' => !empty($detailTindakan) ? $detailTindakan : [],
                // 'dataTindakan' => $qDetail,
                'detailAkomodasi' => $detailAkomodasi,
            ]),
        ];
        $print->Output();
    }

    private function getTotalHeaderPembayaran($pendaftaran_id, $header)
    {
        $result = [];
        $totalAkomodasi = 0;
        if($pendaftaran_id) {
            $pasienadmisi_id = isset($header['pasienadmisi_id']) ? $header['pasienadmisi_id'] : $pendaftaran_id;
            $gabungBilling = $this->gabungBilling($pendaftaran_id);
            $pendaftaranIdGabung = isset($gabungBilling['pendaftaran_id']) ? $gabungBilling['pendaftaran_id'] : null;
            $rincianDetail = RincianPasienDetail2View::find();
            $bayarUangMuka = BayarUangMuka::find();
            $piutang = PemberianPiutang::find();
            if(!empty($pendaftaranIdGabung)) {
                $rincianDetail->select(['SUM(total_tagihan) AS total_tagihan']);
                $rincianDetail->andWhere(['pendaftaran_id' => [$pendaftaran_id, $pendaftaranIdGabung]]);
                $bayarUangMuka->select(['SUM(jumlah_uangmuka) AS jumlah_uangmuka']);
                $bayarUangMuka->andWhere(['pendaftaran_id' => [$pendaftaran_id, $pendaftaranIdGabung]]);
                $piutang->andWhere(['pendaftaran_id' => [$pendaftaran_id, $pendaftaranIdGabung]]);
            }
            else {
                $rincianDetail->select(['total_tagihan']);
                $rincianDetail->andWhere(['pendaftaran_id' => $pendaftaran_id]);
                $bayarUangMuka->andWhere(['pendaftaran_id' => $pendaftaran_id]);
                $piutang->andWhere(['pendaftaran_id' => $pendaftaran_id]);
            }
            $rincianDetail = $rincianDetail->one();
            $bayarUangMuka = $bayarUangMuka->one();
            $piutang = $piutang->one();
            $total_tagihan = isset($rincianDetail['total_tagihan']) ? $rincianDetail['total_tagihan'] : 0;
            $modelPembayaran = Yii::$app->db->createCommand("SELECT 
                    COALESCE(SUM(total_administrasi), 0) AS total_administrasi,
                    COALESCE(SUM(total_tagihan), 0) AS total_tagihan,
                    COALESCE(SUM(total_dijamin), 0) AS total_dijamin,
                    COALESCE(SUM(penggunaan_uangmuka), 0) AS penggunaan_uangmuka,
                    COALESCE(SUM(total_discount), 0) AS total_discount
                    FROM pembayaran_t
                    WHERE pendaftaran_id = {$pendaftaran_id} AND pembayaran_t.is_deleted = false")->queryOne();
            
            // Uang Muka
            // $infoPasien = PasienBelumBayar::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
            $uang_muka = ($bayarUangMuka) ? $bayarUangMuka->jumlah_uangmuka : 0;
            
            $total_piutang = ($piutang) ? $piutang->total_piutang : 0;
            $total_administrasi = $modelPembayaran['total_administrasi'];
            $total_terbayar = $modelPembayaran['total_tagihan'];
            $total_asuransi = $modelPembayaran['total_dijamin'];
            $total_discount = $modelPembayaran['total_discount'];
            $total_uang_muka = isset($header['is_pulang']) ? $modelPembayaran['penggunaan_uangmuka'] : $uang_muka;

            $total_asuransi = ($total_asuransi > $total_tagihan) ? $total_tagihan : $total_asuransi;

            $pendaftaranPenjamin = PendaftaranPenjamin::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
            $nominal_dijamin = ($pendaftaranPenjamin) ? $pendaftaranPenjamin['nominal_dijamin'] : 0;
            if (isset($header['status_bayar'])){
                $total_asuransi = ($header['status_bayar'] == DocoConstants::BELUM_LUNAS) ? $nominal_dijamin : $total_asuransi;
            }

            // Pastikan ini sudah sesuai
            $total_uang_masuk = $total_uang_muka + $total_terbayar + $total_administrasi + $total_piutang;

            $sisa_tagihan = ($total_tagihan - $total_asuransi - $total_uang_masuk) + $totalAkomodasi;

            $sisa_tagihan = ($sisa_tagihan < 0) ? 0 : $sisa_tagihan;

            $result = [
                'total_tagihan' => $total_tagihan,
                'total_asuransi' => $total_asuransi,
                // 'uang_muka' => $infoPasien['uang_muka'],
                'uang_muka' => $uang_muka,
                'sisa_tagihan' => $sisa_tagihan,
                'total_akomodasi' => $totalAkomodasi,
                'total_admin' => $total_administrasi,
                'total_discount' => $total_discount
            ];
        }
        
        return $result;
    }

    private function generateTotalAdministrasi($idPendaftaran, $totalTagihan)
    {
        $persenAdmin = 0;
        $maxAdmin = 0;
        $penjualanResepAdmin = 0;
        $totalAdmin = 0;
        $type = 'pelayanan';

        $penjualanResep = (new \yii\db\Query())
        ->from('penjualanresep_t')
        ->where([
            'pendaftaran_id' => $idPendaftaran,
            'is_active' => true,
            'is_deleted' => false
        ]);
        $penjualanResepAdmin = $penjualanResep->sum('biayaadministrasi');
        $penjualanResepAdmin = $penjualanResepAdmin ? $penjualanResepAdmin : 0;

        $konfigAdmin = (new \yii\db\Query())
        ->from('konfigsystem_k')
        ->select([
            'adm_persen',
            'adm_tindakan_id'
        ])
        ->one();

        if ($penjualanResepAdmin === 0 && $konfigAdmin['adm_persen'] === 0) {
            return $totalAdmin;
        }

        $pasien = (new \yii\db\Query())
        ->from('infopasienri_v')
        ->select([
            'penjamin_id',
            'is_pasientitipan',
            'is_stoptitipan',
            'pindahkamar_id',
            'is_pasientitipan_pk',
            'is_stoppasientitipan',
            'kelaspelayanan_id',
            'kelas_ditagihkan_id'
        ])
        ->where([
            'pendaftaran_id' => $idPendaftaran
        ])
        ->one();

        if (empty($pasien)) {
            return $totalAdmin +  $penjualanResepAdmin;
        }

        $idPenjamin = $pasien['penjamin_id'];
        $idKelas = $pasien['kelaspelayanan_id'];

        if ($pasien['pindahkamar_id']) {
            if ($pasien['is_stoppasientitipan'] == false) {
                    if ($pasien['is_pasientitipan_pk'] == true) {
                        $idKelas = $pasien['kelas_ditagihkan_id'];
                    }
            }
        } else {
            if ($pasien['is_stoptitipan'] == false) {
                    if ($pasien['is_pasientitipan'] == true) {
                        $idKelas = $pasien['kelas_ditagihkan_id'];
                    }
            }
        }

        $tarifAdmin = Yii::$app->db->createCommand('
            SELECT 
                    daftartindakan_id,
                    komponentarif_id,
                    penjamin_id,
                    kelaspelayanan_id,
                    daftartindakan_nama,
                    harga_tariftindakan
            FROM totaltarifnaikkelas_fn(:idPenjamin,:idKelas,:type) 
            WHERE daftartindakan_id = :idTindakan
        ')
        ->bindParam(':idPenjamin', $idPenjamin)
        ->bindParam(':idKelas', $idKelas)
        ->bindParam(':type', $type)
        ->bindParam(':idTindakan', $konfigAdmin['adm_tindakan_id'])
        ->queryOne();

        if (!empty($tarifAdmin)) {
            $maxAdmin = $tarifAdmin['harga_tariftindakan'];
        }

        // Perhitugan total administrasi
        // Maksimum total tagihan admin = total tagihan * persen admin / 100 (maksimum tarif admin)
        // Total admin = maksimum total tagihan admin + total penjualan resep admin
        $persenAdmin = $konfigAdmin['adm_persen'];

        if ($persenAdmin > 0) {
            $totalAdmin = $totalTagihan * $persenAdmin / 100;

            if ($totalAdmin > $maxAdmin) {
                    $totalAdmin = $maxAdmin;
            }
        }

        return $totalAdmin + $penjualanResepAdmin;
    }

    private function getDataPegawai($id = null)
    {
        $model = Pegawai::find();
        if ($id) {
            $model->andWhere(['pegawai_id' => $id]);
        }

        return $model;
    }

    private function getTarifkamar($paramsAkomodasi)
    {
        $kamarruangan_id = $paramsAkomodasi['kamarruangan_id'];
        $defaultTindakan = $this->defaultTindakan($paramsAkomodasi);
        $daftartindakan_id = isset($defaultTindakan['daftartindakan_id']) ? $defaultTindakan['daftartindakan_id'] : '';
        $data = Yii::$app->db->createCommand("SELECT daftartindakan_id,
        daftartindakan_nama, harga_tariftindakan, is_akomodasi,
        persencyto_tindakan, tariftindakan_id, komponentarif_id, kelaspelayanan_id,
        carabayar_id, instalasi_id, kamarruangan_id, ruangan_id, penjamin_id
        FROM totaltarifnaikkelas_fn(:penjamin_id,:kelaspelayanan_id,:type) 
        WHERE daftartindakan_id = {$daftartindakan_id} AND kamarruangan_id = {$kamarruangan_id}
        ORDER BY daftartindakan_nama ASC")
        ->bindParam(':penjamin_id', $paramsAkomodasi['penjamin_id'])
        ->bindParam(':kelaspelayanan_id', $paramsAkomodasi['kelaspelayanan_id'])
        ->bindParam(':type', $paramsAkomodasi['type']);

        $data = $data->queryOne();
        $harga_tariftindakan = isset($data['harga_tariftindakan']) ? $data['harga_tariftindakan'] : 0;
        return $harga_tariftindakan;
    }

    private static function defaultTindakan($paramsAkomodasi)
    {
        $kamarRuanganId = isset($paramsAkomodasi['kamarruangan_id']) ? $paramsAkomodasi['kamarruangan_id'] : null;
        $kelasPelayananId = isset($paramsAkomodasi['kelaspelayanan_id']) ? $paramsAkomodasi['kelaspelayanan_id'] : null;
        $carabayar_id = isset($paramsAkomodasi['carabayar_id']) ? $paramsAkomodasi['carabayar_id'] : null;
        $instalasi_id = isset($paramsAkomodasi['instalasi_id']) ? $paramsAkomodasi['instalasi_id'] : null;
        $penjamin_id = isset($paramsAkomodasi['penjamin_id']) ? $paramsAkomodasi['penjamin_id'] : null;
        $ruangan_id = isset($paramsAkomodasi['ruangan_id']) ? $paramsAkomodasi['ruangan_id'] : null;

        $komponenTotal = (new DocoConstansId)->actionGetId('komponen_total');
        $tindakanAkomodasi = DaftarTindakan::find()->where(['is_akomodasi' => true, 'is_active' => true])->one();

        return [
            'daftartindakan_id' => ($tindakanAkomodasi) ? $tindakanAkomodasi['daftartindakan_id'] : null,
            'daftartindakan_nama' => ($tindakanAkomodasi) ? $tindakanAkomodasi['daftartindakan_nama'] : null,
            'harga_tariftindakan' => 0,
            'is_akomodasi' => true,
            'persencyto_tindakan' => 0,
            'tariftindakan_id' => null,
            'kamarruangan_id' => $kamarRuanganId,
            'komponentarif_id' => $komponenTotal,
            'kelaspelayanan_id' => $kelasPelayananId,
            'carabayar_id' => $carabayar_id,
            'instalasi_id' => $instalasi_id,
            'ruangan_id' => $ruangan_id,
            'penjamin_id' => $penjamin_id,
        ]; 
    }

    protected function getPasienRanap($pasienadmisi_id)
    {
        return Yii::$app->db->createCommand("
            SELECT
            pasienadmisi_id,
            tgl_admisi,
            dokter_admisi,
            ruangan_nama,
            kamarruangan_nokamar,
            no_tempattidur,
            tgl_stopakomodasi,
            kelaspelayanan_nama AS kelas_admisi,
            kelas_ditagihkan_nama,
            ruangan_titipan_nama,
            kelaspelayanan_id,
            penjamin_id,
            tagihan_rs,
            tgl_pulang
        FROM
            infopasienri_v 
            WHERE pasienadmisi_id = {$pasienadmisi_id}")
        ->queryOne();
    }

    private function getDataPendaftaranRincian($pendaftaran_id)
    {
        return InfoDataPendaftaran::find()
        ->select(['infodatapendaftaran_v.tgl_pendaftaran', 
            'infodatapendaftaran_v.pendaftaran_id', 
            'infodatapendaftaran_v.no_rekam_medik', 
            'infodatapendaftaran_v.no_pendaftaran', 
            'infodatapendaftaran_v.nama_pasien', 
            'infodatapendaftaran_v.alamat_pasien', 
            'infodatapendaftaran_v.nama_dok_rj_rd', 
            'infodatapendaftaran_v.nama_dok_ri', 
            'infodatapendaftaran_v.rua_nama', 
            'infodatapendaftaran_v.kelaspelayanan_nama', 
            'infodatapendaftaran_v.kelas_ditagihkan', 
            'infodatapendaftaran_v.penjamin_nama', 
            'infodatapendaftaran_v.carabayar_nama', 
            'infodatapendaftaran_v.pasienadmisi_id', 
            'infodatapendaftaran_v.kelaspelayanan_id', 
            'infodatapendaftaran_v.penjamin_id', 
            'infodatapendaftaran_v.tagihan_belumbayar', 
            'infodatapendaftaran_v.tanggal_lahir', 
            'lookup_m.lookup_name AS status_bayar', 
            'infobayaruangmuka_v.sisa_uangmuka AS sisa_uangmuka',
            'pendaftaranpenjamin_t.nominal_dijamin AS nominal_dijamin',
            'infodatapendaftaran_v.rua_nama',
            'infodatapendaftaran_v.ruangan_nama',
            'infodatapendaftaran_v.ruangan_titipan_nama',
            'infodatapendaftaran_v.umur',
            'infodatapendaftaran_v.jenis_kelamin',
        ])
        ->join('JOIN', 'lookup_m', 'lookup_m.lookup_id = infodatapendaftaran_v.status_bayar')
        ->leftJoin('pendaftaranpenjamin_t', 'pendaftaranpenjamin_t.pendaftaran_id = infodatapendaftaran_v.pendaftaran_id')
        ->leftJoin('infobayaruangmuka_v', 'infobayaruangmuka_v.pendaftaran_id = infodatapendaftaran_v.pendaftaran_id')
        ->where(['infodatapendaftaran_v.pendaftaran_id' => $pendaftaran_id])->asArray()->one();
    }

    private function getDataDetailTagihan($id)
    {
        $db = Yii::$app->db;
        return  $db->createCommand("
            SELECT kelompoktindakan_nama, SUM(qty) AS qty, SUM(sub_total) AS sub_total
            FROM infotagihanpasien_v 
            WHERE ref_pendaftaran_id = {$id} and sub_total > 0
            GROUP BY kelompoktindakan_nama
            ORDER BY kelompoktindakan_nama ASC
        ")->queryAll();
    }

    protected function getKunjunganRanap($pendaftaran_id)
    {
        $result = [];
        $status_kamar = '-';
        if(!empty($pendaftaran_id)){
            $result = Yii::$app->db->createCommand("
            SELECT pendaftaran_id,no_pendaftaran,no_rekam_medik,nama_pasien,nama_depan,alamat_pasien,rt,rw,tgl_pendaftaran,tgl_admisi,tgl_pulang,tgl_stopakomodasi,
			jenis_kelamin,tanggal_lahir,umur,pasienadmisi_id,carabayar_id,klsrawat,is_aps,is_pasientitipan,is_pasientitipan_pk,penjamin_id,
			is_stoppasientitipan,bpjs_kelas,kelaspelayanan_id,kelaspelayanan_nama,nama_pegawai,
			carabayar_nama,penjamin_nama,ruangan_nama,kamarruangan_nokamar,no_tempattidur,
			kelas_hak,status_kelas,
			(((((additional_data::json)->>'sep')::json)->>'klsRawat')::json)->>'klsRawatHak' as klsrawathak
			FROM infokunjunganri_v 
			WHERE pendaftaran_id = {$pendaftaran_id}")
            ->queryOne();
        }
        return $result;
    }

    protected function getTempatTidur($kamartempattidur_id)
    {
        $tempatTidur = '';
        if(!empty($kamartempattidur_id)) {
            $data = Yii::$app->db->createCommand("
                SELECT * FROM kamartempattidur_m WHERE kamartempattidur_id = {$kamartempattidur_id}
            ")->queryOne();

            $tempatTidur = isset($data['no_tempattidur']) ? $data['no_tempattidur'] : '-';
        }

        return $tempatTidur;
    }

    protected function getNokamar($kamarruangan_id)
    {
        $data = Yii::$app->db->createCommand("
            SELECT * FROM kamarruangan_m WHERE kamarruangan_id = {$kamarruangan_id}
        ")->queryOne();

        return isset($data['kamarruangan_nokamar']) ? $data['kamarruangan_nokamar'] : '-';
    }

    protected function getHistoryPindahKamar($pendaftaran_id)
    {
        if ( is_null($pendaftaran_id) || empty($pendaftaran_id) ) {
            return [];
        }
        $data = Yii::$app->db->createCommand("
                SELECT * FROM infopindahkamar_v WHERE pendaftaran_id = :pendaftaran_id 
                ORDER BY pindahkamar_id DESC LIMIT 1
        ")->bindValues([
            ':pendaftaran_id' => $pendaftaran_id
        ])->queryOne();

        return $data;
    }

    protected function getPembulatan($total_ditagihkan)
    {
        $konfigSystem = $this->getKonfigSistem();
        $helpers = new DocoHelpers;
        $isPembulatan = isset($konfigSystem['is_pembulatankeatas']) ? $konfigSystem['is_pembulatankeatas'] : false;
        $satuanPembulatan = !empty($konfigSystem['satuanpembulatan']) ? $konfigSystem['satuanpembulatan'] : 0;
        $pembulatanTagihan = $helpers->pembulatan(round($total_ditagihkan,2), $isPembulatan, $satuanPembulatan);
        $nominalPembulatan = isset($pembulatanTagihan['pembulatan']) ? $pembulatanTagihan['pembulatan'] : 0;
        $total_ditagihkan = isset($pembulatanTagihan['total']) ? (int) $pembulatanTagihan['total'] : (int) $total_ditagihkan;
        return [
            'nominalPembulatan' => $nominalPembulatan,
            'total_ditagihkan' => $total_ditagihkan,
        ];
    }

    private function getKonfigSistem()
    {
        return Yii::$app->cache->getOrSet(DocoConstants::VAR_K_S , function ($cache) {
            return KonfigSystem::find()->asArray()->one();
        });
    }

    protected function getBiayaAdmSudahBayar($pendaftaran_id)
	{
		return Yii::$app->db->createCommand("
			SELECT COALESCE(SUM(total_administrasi), 0) AS total_administrasi FROM pembayaran_t WHERE pendaftaran_id = {$pendaftaran_id} 
		")->queryOne();
	}

    protected function gabungBilling($pendaftaran_id)
    {
        if(empty($pendaftaran_id)) {
            return [];
        }
        
        return Yii::$app->db->createCommand("
            SELECT * FROM gabungpelayanandetail_t WHERE ref_pendaftaran_id = {$pendaftaran_id} AND is_deleted = FALSE
        ")->queryOne();
    }

    protected function processFlow()
    {
        return $this->rincian();
    }
}