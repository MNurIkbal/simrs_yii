<?php 

/**
 * @author : Budi (budi@sirs.co.id)
 * Powered by Sirs
 */

namespace Doco\processes;

use Yii;
use app\modules\v1\models\InfoDataPendaftaran;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\InfoPasienRiView;
use app\modules\v1\models\InfoPasienRdView;
use app\modules\v1\models\InfoPasienRjView;
use app\modules\v1\models\DaftarTindakan;
use app\modules\v1\models\Kamar;
use app\modules\v1\businessLogic\TagihanHelper;
use Doco\components\DocoConstansId;
use app\modules\v1\cache\Cache;
use app\modules\v1\models\ProfilRsView;
use Doco\models\kasir\PasienView;

class PathDetailRincianProcess extends \Doco\components\DocoBaseProcessExtension
{
    protected $dokPath = 'detail-rincian';
    protected $detail = [];
    protected $tindakan = [];
    protected $data_admin = '';
    protected $akomodasi = [];
    protected $biayaAdmin = 0;
    protected $total = 0 ;
    protected $infoPasien = [];
    protected $id;
    protected $kode_doc = 'cetak-detail-rincian';
    protected $no_pembayaran = '-';
    protected $is_admisi;
    protected $pembayaran;
    protected $data_ranap = [];

    protected function getDataPendaftaranRincian($id)
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
            'infodatapendaftaran_v.kelas_ditagihkan_id',
        ])
        ->join('JOIN', 'lookup_m', 'lookup_m.lookup_id = infodatapendaftaran_v.status_bayar')
        ->leftJoin('pendaftaranpenjamin_t', 'pendaftaranpenjamin_t.pendaftaran_id = infodatapendaftaran_v.pendaftaran_id')
        ->leftJoin('infobayaruangmuka_v', 'infobayaruangmuka_v.pendaftaran_id = infodatapendaftaran_v.pendaftaran_id')
        ->where(['infodatapendaftaran_v.pendaftaran_id' => $id])->asArray()->one();
        
    }

    protected function getDataDetailTagihan($id)
    {
        $db = Yii::$app->db;
        return  $db->createCommand("
            SELECT *
            FROM infotagihanpasien_v 
            WHERE ref_pendaftaran_id = {$id} 
            AND sub_total > 0
            order by tgl_pelayanan ASC
        ")->queryAll();

    }
    
    public function getAkomodasi($admisiId)
    {
        $db = Yii::$app->db;
        $result = [];
        $data = [];
        try {
            $response = Yii::$app->docoRest->ranap->get('api/get-akomodasi-sementara',[
                'query' => [
                    'admisiId' => $admisiId,
                ]
            ]);
            $body = json_decode($response->getBody(), True);
            $datas = isset($body['response']['tindakan_akomodasi']) ? $body['response']['tindakan_akomodasi'] : [];
            $tmpTindakan = [];
            $tmpRuangan = [];
            $tmpKelasPelayanan = [];
            $totalAkomodasi = 0;
            if(!empty($datas)) {
                foreach($datas as $key => $value) {
                    $value['qty_tindakan'] = 1;
                    $qtyTindakan = isset($value['qty_tindakan']) ? $value['qty_tindakan'] : 0;
                    $tarifSatuan = isset($value['tarif_satuan']) ? $value['tarif_satuan'] : 0;
                    $tarifTindakan = isset($value['tarif_tindakan']) ? $value['tarif_tindakan'] : 0;
                    $totalAkomodasi += $tarifTindakan;
                    $tindakanId = isset($value['daftartindakan_id']) ? $value['daftartindakan_id'] : null;
                    $ruanganId = isset($value['ruangan_id']) ? $value['ruangan_id'] : null;
                    $kelasPelayananId = isset($value['kelaspelayanan_id']) ? $value['kelaspelayanan_id'] : null;
        
                    if ((!isset($tmpRuangan[$ruanganId])) || (!isset($tmpKelasPelayanan[$kelasPelayananId]))) {
                        $kelaspelayanan_ruangan = Kamar::find()->select([
                            'ruangan_nama',
                            'kelaspelayanan_nama',
                        ])->where([
                            'ruangan_id' => $ruanganId,
                            'kelaspelayanan_id' => $kelasPelayananId,
                        ])->asArray()->one();
                        $tmpRuangan[$ruanganId] = $kelaspelayanan_ruangan['ruangan_nama'];
                        $tmpKelasPelayanan[$kelasPelayananId] = $kelaspelayanan_ruangan['kelaspelayanan_nama'];
                    }
        
                    if (!isset($tmpTindakan[$tindakanId])) {
                        $tindakan = DaftarTindakan::find()->select([
                            'daftartindakan_nama',
                            'kelompoktindakan_id',
                        ])->where([
                            'daftartindakan_id' => $tindakanId,
                        ])->asArray()->one();
                        $tmpTindakan[$tindakanId] = $tindakan['daftartindakan_nama'];
                        $kelompoktindakanId = $tindakan['kelompoktindakan_id'];
                    }
        
                    $value['kelompok_tindakan'] = $kelompoktindakanId;
                    $value['daftartindakan_nama'] = $tmpTindakan[$tindakanId];
                    $value['ruangan_nama'] = $tmpRuangan[$ruanganId];
                    $value['kelaspelayanan_nama'] = $tmpKelasPelayanan[$kelasPelayananId];
                    $data[$key] = $value;
                }
            }

            $kelompoktindakanId = $value['kelompok_tindakan'];
            $kelompoktindakanNama = $db->createCommand("
                SELECT 
                kelompoktindakan_nama
                FROM kelompoktindakan_m 
                WHERE kelompoktindakan_id = {$tindakan['kelompoktindakan_id']}
            ")->queryOne();

            return [
                'tindakan_akomodasi' => $data,
                'total_akomodasi' => $totalAkomodasi,
                'kelompok_tindakan' => $kelompoktindakanNama['kelompoktindakan_nama']
            ];
        }
        catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            Yii::error($e->getMessage());
            return [];
        }
    }

    private function getDataAdmin()
    {

        $confSistem = Cache::getKonfigSistem();
        $admTindakanId = !empty($confSistem['adm_tindakan_id']) ? $confSistem['adm_tindakan_id'] : null;
        $model = new DaftarTindakan;
        $query = $model::find()->where(['daftartindakan_id'=>$admTindakanId])->asArray()->one();
        return isset($query['daftartindakan_nama']) ? $query['daftartindakan_nama'] : 'Administration Fee';
    }

    protected function getData()
    {
        $request = $this->_requestData;
        $id = $request->get('id', null);
        $this->id = $id;
        $infoPasien = $this->getDataPendaftaranRincian($id);
        $penjaminId = !empty($infoPasien['penjamin_id']) ? $infoPasien['penjamin_id'] : null;
        $admisiId = !empty($infoPasien['pasienadmisi_id']) ? $infoPasien['pasienadmisi_id'] : null;
        $kelasPelayananId = !empty($infoPasien['kelaspelayanan_id']) ? $infoPasien['kelaspelayanan_id'] : null;
        $kelasDitagihkanId = !empty($infoPasien['kelas_ditagihkan_id']) ? $infoPasien['kelas_ditagihkan_id'] : null;
        $akomodasi = [
            'tindakan_akomodasi' => null,
            'total_akomodasi' => null,
            'kelompok_tindakan' => null
        ];
        if(!empty($admisiId)){
            $akomodasi = $this->getAkomodasi($admisiId);
            Yii::error($akomodasi);
        }
        $this->infoPasien = $infoPasien;
        $historyPindahKamar = $this->getHistoryPindahKamar($id);
        if(!empty($kelasDitagihkanId)) {
            $kelasPelayananId = $kelasDitagihkanId;
        }

        if(!empty($historyPindahKamar)) {
            $kelasPelayananId = isset($historyPindahKamar['kelaspelayanan_id']) ? $historyPindahKamar['kelaspelayanan_id'] : '';
            $kelasDitagihkanId = isset($historyPindahKamar['kelas_ditagihkan_id']) ? $historyPindahKamar['kelas_ditagihkan_id'] : '';
            $penjaminId = isset($historyPindahKamar['penjamin_id']) ? $historyPindahKamar['penjamin_id'] : '';
            if(!empty($kelasDitagihkanId)) {
                $kelasPelayananId = $kelasDitagihkanId;
            }
        }

        $total_jpk = 0;
        $jpk_id = json_decode((new DocoConstansId)->actionGetAdditional("JPK"));
        $tindakan = (new DocoConstansId)->actionGetAdditional('tindakan_keperawatan');
        $tindakan = json_decode($tindakan, true);
        $this->tindakan = $tindakan;
        $qDetail = $this->getDataDetailTagihan($id);
        $detail = $data_admin = [];
        $subTotal = 0;
        foreach($qDetail as $value){
            $total = isset($value['sub_total']) ? $value['sub_total'] : 0;
            $subTotal += $total;
            $pelayanan = !empty($value['pelayanan']) ? $value['pelayanan'] : null;
            $ruangan = !empty($value['ruangan_pelayanan']) ? $value['ruangan_pelayanan'] : null;
            $tindakan_obat_id = isset($value['tindakan_obat_id']) ? $value['tindakan_obat_id'] : null;
            $kelompokTindakanNama = !empty($value['kelompoktindakan_nama']) ? $value['kelompoktindakan_nama'] : null;
            if(!empty($pelayanan)) {
                $detail[$pelayanan][$ruangan][$kelompokTindakanNama][] = $value;
            }
            if(!empty($tindakan_obat_id) && in_array($tindakan_obat_id, $jpk_id)){
                $total_jpk += $total;
            }
        }
        
        $this->detail = $detail;
        $total_tagihan_admin = $subTotal - $total_jpk;
        $biayaAdmin = TagihanHelper::getBiayaAdmin($id, $penjaminId, $kelasPelayananId, $total_tagihan_admin, $admisiId);
        $data_admin = '';
        if( $biayaAdmin > 0 &&  !empty($admisiId)){
            $data_admin = $this->getDataAdmin();
        }
        $this->biayaAdmin = $biayaAdmin;
        $this->data_admin = $data_admin;
        $this->akomodasi = $akomodasi;
    }
    
    protected function getRender()
    {
        $infoPasien = $this->infoPasien;
        $nominal_dijamin = isset($infoPasien['nominal_dijamin']) ? $infoPasien['nominal_dijamin'] : 0;
        $sisa_uangmuka = isset($infoPasien['sisa_uangmuka']) ? $infoPasien['sisa_uangmuka'] : 0;
        $tagihan_belumbayar = isset($infoPasien['tagihan_belumbayar']) ? $infoPasien['tagihan_belumbayar'] : 0;
        return Yii::$app->controller->renderPartial($this->dokPath, [
            'tindakan' => $this->tindakan,
            'data' => $this->detail,
            'data_admin' => $this->data_admin,
            'data_akomodasi' => $this->akomodasi,
            'biayaAdmin' => $this->biayaAdmin,
            'total' => $this->total,
            'nominal_dijamin' => $nominal_dijamin,
            'sisa_uangmuka' => $sisa_uangmuka,
            'tagihan_belumbayar' => $tagihan_belumbayar,
        ]);
    }
    
    protected function detailPasien()
    {
        $infoPasien = $this->infoPasien;
        $detailPasien = PasienView::find()->select([
            'alamat_pasien','propinsi_nama', 'kabupaten_nama',
            'kecamatan_nama', 'kelurahan_nama', 'warganegara'
        ])->where([
            'no_rekam_medik' => $infoPasien['no_rekam_medik']
        ])->one();

        $kecamatan_nama = $kelurahan_nama = $kabupaten_nama = $propinsi_nama = $warganegara = '-';
        if(isset($detailPasien['kecamatan_nama'])) {
            $kecamatan_nama = $detailPasien['kecamatan_nama'];
        }
        if(isset($detailPasien['kelurahan_nama'])) {
            $kelurahan_nama = $detailPasien['kelurahan_nama'];
        }
        if(isset($detailPasien['kabupaten_nama'])) {
            $kabupaten_nama = $detailPasien['kabupaten_nama'];
        }
        if(isset($detailPasien['propinsi_nama'])) {
            $propinsi_nama = $detailPasien['propinsi_nama'];
        }

        if(isset($this->infoPasien['kecamatan_nama'])) {
            $this->infoPasien['kecamatan_nama'] = $kecamatan_nama;
        }
        if(isset($this->infoPasien['kelurahan_nama'])) {
            $this->infoPasien['kelurahan_nama'] = $kelurahan_nama;
        }
        if(isset($this->infoPasien['kabupaten_nama'])) {
            $this->infoPasien['kabupaten_nama'] = $kabupaten_nama;
        }
        if(isset($this->infoPasien['propinsi_nama'])) {
            $this->infoPasien['propinsi_nama'] = $propinsi_nama;
        }
        if(isset($this->infoPasien['warganegara'])) {
            $this->infoPasien['warganegara'] = $warganegara;
        }
    }

    protected function getPasienRanap($pasienadmisi_id)
    {
        $dataranap = Yii::$app->db->createCommand("
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

        $this->data_ranap = $dataranap;
    }

    protected function generateAttr()
    {
        $infoPasien = $this->infoPasien;
        $admisiId = !empty($infoPasien['pasienadmisi_id']) ? $infoPasien['pasienadmisi_id'] : null;
        $dokter = !empty($admisiId) ? "nama_dok_ri" : "nama_dok_rj_rd";
        $tgl_pendaftaran = !empty($infoPasien['tgl_pendaftaran']) ? date('d/M/Y', strtotime($infoPasien['tgl_pendaftaran'])) : '-';
        $pendaftaran_id = !empty($infoPasien['pendaftaran_id']) ? $infoPasien['pendaftaran_id'] : '';
        $no_rekam_medik = !empty($infoPasien['no_rekam_medik']) ? $infoPasien['no_rekam_medik'] : '';
        $no_pendaftaran = !empty($infoPasien['no_pendaftaran']) ? $infoPasien['no_pendaftaran'] : '';
        $nama_pasien = !empty($infoPasien['nama_pasien']) ? $infoPasien['nama_pasien'] : '';
        $penjamin_nama = !empty($infoPasien['penjamin_nama']) ? $infoPasien['penjamin_nama'] : '';
        $carabayar_nama = !empty($infoPasien['carabayar_nama']) ? $infoPasien['carabayar_nama'] : '';
        $status_bayar = !empty($infoPasien['status_bayar']) ? $infoPasien['status_bayar'] : '';
        $nama_ruangan = !empty($infoPasien['ruangan_nama']) ? $infoPasien['ruangan_nama'] : '';
        $kelas_pelayanan = !empty($infoPasien['kelaspelayanan_nama']) ? $infoPasien['kelaspelayanan_nama'] : '';
        $ruangan_titipan_nama = isset($infoPasien['ruangan_titipan_nama']) ? $infoPasien['ruangan_titipan_nama'] : '';
        $kelas_ditagihkan = isset($infoPasien['kelas_ditagihkan']) ? $infoPasien['kelas_ditagihkan'] : '';

        if(!empty($ruangan_titipan_nama)) {
            $nama_ruangan = $ruangan_titipan_nama;
        }
        if(!empty($kelas_ditagihkan)) {
            $kelas_pelayanan = $kelas_ditagihkan;
        }

        $historyPindahKamar = $this->getHistoryPindahKamar($pendaftaran_id);
        if(!empty($historyPindahKamar)) {
            $nama_ruangan = isset($historyPindahKamar['ruangan_nama']) ? $historyPindahKamar['ruangan_nama'] : '';
            $kelas_pelayanan = isset($historyPindahKamar['kelaspelayanan_nama']) ? $historyPindahKamar['kelaspelayanan_nama'] : '';
            $kelasDitagihkanNama = isset($historyPindahKamar['kelas_ditagihkan_nama_pk']) ? $historyPindahKamar['kelas_ditagihkan_nama_pk'] : '';
            $ruangan_pindah = isset($historyPindahKamar['ruangan_pindah']) ? $historyPindahKamar['ruangan_pindah'] : '';
            $carabayar_nama = isset($historyPindahKamar['carabayar_nama']) ? $historyPindahKamar['carabayar_nama'] : '';
            if(!empty($kelasDitagihkanNama)) {
                $kelas_pelayanan = $kelasDitagihkanNama;
            }
            if(!empty($ruangan_pindah)) {
                $nama_ruangan = $ruangan_pindah;
            }
        }
        return [
            '#tgl_pendaftaran#' => $tgl_pendaftaran,
            '#no_rekam_medik#' => $no_rekam_medik,
            '#no_pendaftaran#' => $no_pendaftaran,
            '#nama_pasien#' => $nama_pasien,
            '#nama_dok_rj_rd#'=> isset($infoPasien[$dokter]) ? $infoPasien[$dokter] : '',
            '#rua_nama#'=> $nama_ruangan,
            '#kelaspelayanan_nama#'=> $kelas_pelayanan,
            '#penjamin_nama#'=> $penjamin_nama,
            '#carabayar_nama#'=> $carabayar_nama,
            '#status_bayar#'=> $status_bayar,
            '#no_pembayaran#' => $this->no_pembayaran,
            'kode_doc' => 'cetak-detail-rincian',
            '#table#'=> $this->getRender(),
        ];
    }

    protected function getHistoryPindahKamar($pendaftaran_id)
	{
		$data = Yii::$app->db->createCommand("
            SELECT * FROM infopindahkamar_v WHERE pendaftaran_id = {$pendaftaran_id} 
            ORDER BY pindahkamar_id DESC LIMIT 1
		")->queryOne();

		return $data;
	}

    protected function processFlow()
    {
        $this->getData();
        return [
            "attributes" => $this->generateAttr(),
            "kode_doc" => $this->kode_doc,
        ];
    }

}