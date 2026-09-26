<?php

/**
 * @author : Dede Herdiana (dede.herdiana@sirs.co.id)
 * Powered by Sirs
 */

namespace Doco\processes;

use app\modules\v1\models\InfoDataPendaftaran;
use app\modules\v1\models\DaftarTindakan;
use app\modules\v1\models\Kamar;
use app\modules\v1\models\RincianPasienView;
use app\modules\v1\businessLogic\TagihanHelper;
use Doco\components\DocoConstansId;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use app\modules\v1\cache\Cache;
use Yii;

class CetakRincianProcess extends \Doco\components\DocoBaseProcessExtension
{
    protected $_header;
    protected $_instalasi_id;
    protected $_pendaftaran_id;
    protected $_admisi_id;
    protected $_penjamin_id;
    protected $_kelas_id;
    protected $_kelas_nama;
    protected $_nama_ruangan;
    protected $_nama_pegawai;
    protected $_penjamin_nama;
    protected $_carabayar_nama;
    protected $_akomodasi;
    protected $_status_bayar;
    protected $_dokter;
    protected $_totalJpk;
    protected $_total;
    protected $_total_tagihan_admin;
    protected $_biaya_admin;
    protected $_data_admin;
    protected $_detail_tindakan;

    protected $dokPath = 'rincian';
    
    protected function populateData()
    {
        $request = Yii::$app->request;
        $db = Yii::$app->db;
        $pendaftaran_id = $request->get('id', null);
        $nama_pegawai = $request->get('nama_pegawai', null);
        $instalasi_id = $request->get('instalasi_id');

        $infoPasien = $this->getDataPendaftaranRincian($pendaftaran_id);
        $admisiId = !empty($infoPasien['pasienadmisi_id']) ? $infoPasien['pasienadmisi_id'] : null;
        $dokter = !empty($admisiId) ? "nama_dok_ri" : "nama_dok_rj_rd";
        $penjaminId = !empty($infoPasien['penjamin_id']) ? $infoPasien['penjamin_id'] : null;
        $kelasPelayananId = !empty($infoPasien['kelaspelayanan_id']) ? $infoPasien['kelaspelayanan_id'] : null;
        $kelasPelayananNama = !empty($infoPasien['kelaspelayanan_nama']) ? $infoPasien['kelaspelayanan_nama'] : '';
        $kelasDitagihkanId = !empty($infoPasien['kelas_ditagihkan_id']) ? $infoPasien['kelas_ditagihkan_id'] : null;
        $kelasDitagihkanNama = isset($infoPasien['kelas_ditagihkan']) ? $infoPasien['kelas_ditagihkan'] : '';
        $penjaminNama = !empty($infoPasien['penjamin_nama']) ? $infoPasien['penjamin_nama'] : null;
        $caraBayarNama = !empty($infoPasien['carabayar_nama']) ? $infoPasien['carabayar_nama'] : null;
        $nama_ruangan = !empty($infoPasien['ruangan_nama']) ? $infoPasien['ruangan_nama'] : '';
        $status_bayar = !empty($infoPasien['status_bayar']) ? $infoPasien['status_bayar'] : '';

        if(!empty($kelasDitagihkanId)) {
            $kelasPelayananId = $kelasDitagihkanId;
        }

        if(!empty($kelasDitagihkanNama)) {
            $kelasPelayananNama = $kelasDitagihkanNama;
        }

        $historyPindahKamar = $this->getHistoryPindahKamar($pendaftaran_id);
        if(!empty($historyPindahKamar)) {
            $kelasPelayananId = isset($historyPindahKamar['kelaspelayanan_id']) ? $historyPindahKamar['kelaspelayanan_id'] : '';
            $kelasPelayananNama = isset($historyPindahKamar['kelaspelayanan_nama']) ? $historyPindahKamar['kelaspelayanan_nama'] : '';
            $kelasDitagihkanId = isset($historyPindahKamar['kelas_ditagihkan_id']) ? $historyPindahKamar['kelas_ditagihkan_id'] : '';
            $kelasDitagihkanNama = isset($historyPindahKamar['kelas_ditagihkan_nama_pk']) ? $historyPindahKamar['kelas_ditagihkan_nama_pk'] : '';
            $penjaminId = isset($historyPindahKamar['penjamin_id']) ? $historyPindahKamar['penjamin_id'] : '';
            $nama_ruangan = isset($historyPindahKamar['ruangan_pindah']) ? $historyPindahKamar['ruangan_pindah'] : '';
            $caraBayarNama = isset($historyPindahKamar['carabayar_nama']) ? $historyPindahKamar['carabayar_nama'] : '';

            if(!empty($kelasDitagihkanId)) {
                $kelasPelayananId = $kelasDitagihkanId;
            }
    
            if(!empty($kelasDitagihkanNama)) {
                $kelasPelayananNama = $kelasDitagihkanNama;
            }
        }
        
        $akomodasi = !empty($admisiId)? $this->getAkomodasi($admisiId) : null;
        if($akomodasi == null){
            $akomodasi = [
                'tindakan_akomodasi' => null,
                'total_akomodasi' => null,
                'kelompok_tindakan' => null
            ];
        } 

        $this->_header = $infoPasien;
        $this->_instalasi_id = $instalasi_id;
        $this->_pendaftaran_id = $pendaftaran_id;
        $this->_admisi_id = $admisiId;
        $this->_akomodasi = $akomodasi;
        $this->_dokter = $dokter;
        $this->_penjamin_id = $penjaminId;
        $this->_kelas_id = $kelasPelayananId;
        $this->_kelas_nama = $kelasPelayananNama;
        $this->_nama_ruangan = $nama_ruangan;
        $this->_nama_pegawai = $nama_pegawai;
        $this->_nama_pegawai = $nama_pegawai;
        $this->_nama_pegawai = $nama_pegawai;
        $this->_penjamin_nama = $penjaminNama;
        $this->_carabayar_nama = $caraBayarNama;
        $this->_status_bayar = $status_bayar;

        /**
         * Untuk kebutuhan total tagihan yang dikurangi jasa pelayanan keperawatan
         */
        $detailTagihan = $this->getDataDetailTagihan($pendaftaran_id);
        $detailGrouping = isset($detailTagihan['detail']) ? $detailTagihan['detail'] : [];
        $subTotal = isset($detailTagihan['sub_total']) ? $detailTagihan['sub_total'] : 0;
        $total_jpk = isset($detailTagihan['total_jpk']) ? $detailTagihan['total_jpk'] : 0;
        $total_tagihan_admin = $subTotal - $total_jpk; 

        /**
         * Untuk perhitungan biaya admin sesuai kelas terakhir
         */
        $biayaAdmin = TagihanHelper::getBiayaAdmin($pendaftaran_id, $this->_penjamin_id, $this->_kelas_id, $total_tagihan_admin, $admisiId);
        $data_admin = '';
        if($biayaAdmin > 0 ){
            $data_admin = $this->getDataAdmin();
        }

        $this->_totalJpk = $total_jpk;
        $this->_total = $subTotal;
        $this->_total_tagihan_admin = $total_tagihan_admin;
        $this->_biaya_admin = $biayaAdmin;
        $this->_data_admin = $data_admin;
        $this->_detail_tindakan = $detailGrouping;
    }

    public function rincian()
    {
        $header = $this->_header;
        $tgl_pendaftaran = (isset($header['tgl_pendaftaran']) && !empty($header['tgl_pendaftaran'])) ? date('d/M/Y', strtotime($header['tgl_pendaftaran'])) : '-';
        $no_rekam_medik = isset($header['no_rekam_medik']) ? $header['no_rekam_medik'] : '-';
        $no_pendaftaran = isset($header['no_pendaftaran']) ? $header['no_pendaftaran'] : '-';
        $nama_pasien = isset($header['nama_pasien']) ? $header['nama_pasien'] : '-';
        $sisa_uangmuka = isset($header['sisa_uangmuka']) ? $header['sisa_uangmuka'] : 0;
        $nominal_dijamin = isset($header['nominal_dijamin']) ? $header['nominal_dijamin'] : 0;
        $dokter = isset($header[$this->_dokter]) ? $header[$this->_dokter] : '-';
        $print = new DocoPrint('cetak-rincian');
        $print->attributes = [
            '#tgl_pendaftaran#' => $tgl_pendaftaran,
            '#no_rekam_medik#' => $no_rekam_medik,
            '#no_pendaftaran#' => $no_pendaftaran,
            '#nama_pasien#' => $nama_pasien,
            '#nama_dok_rj_rd#'=> $dokter,
            '#rua_nama#'=> $this->_nama_ruangan,
            '#kelaspelayanan_nama#'=> $this->_kelas_nama,
            '#penjamin_nama#'=> $this->_penjamin_nama,
            '#carabayar_nama#'=> $this->_carabayar_nama,
            '#status_bayar#'=> $this->_status_bayar,
            '#table#' => Yii::$app->controller->renderPartial($this->dokPath, [
                'data' => $this->_detail_tindakan,
                'data_admin' => $this->_data_admin,
                'data_akomodasi' => $this->_akomodasi,
                'sisa_uangmuka' => $sisa_uangmuka,
                'nominal_dijamin' => $nominal_dijamin,
                'total' => $this->_total,
                'biayaAdmin' => $this->_biaya_admin,
            ]),
        ];
        $print->Output();
    }
    
    private function getDataPendaftaranRincian($id)
    {
        return InfoDataPendaftaran::find()
        ->select(['infodatapendaftaran_v.tgl_pendaftaran', 
            'infodatapendaftaran_v.pendaftaran_id', 
            'infodatapendaftaran_v.no_rekam_medik', 
            'infodatapendaftaran_v.no_pendaftaran', 
            'infodatapendaftaran_v.nama_pasien', 
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
            'lookup_m.lookup_name AS status_bayar', 
            'infobayaruangmuka_v.sisa_uangmuka AS sisa_uangmuka',
            'pendaftaranpenjamin_t.nominal_dijamin AS nominal_dijamin',
            'infodatapendaftaran_v.ruangan_nama',
            'infodatapendaftaran_v.ruangan_titipan_nama',
        ])
        ->join('JOIN', 'lookup_m', 'lookup_m.lookup_id = infodatapendaftaran_v.status_bayar')
        ->leftJoin('pendaftaranpenjamin_t', 'pendaftaranpenjamin_t.pendaftaran_id = infodatapendaftaran_v.pendaftaran_id')
        ->leftJoin('infobayaruangmuka_v', 'infobayaruangmuka_v.pendaftaran_id = infodatapendaftaran_v.pendaftaran_id')
        ->where(['infodatapendaftaran_v.pendaftaran_id' => $id])->asArray()->one();
        
    }

    private function getDataDetailTagihan($pendaftaran_id)
    {
        $db = Yii::$app->db;
        $data =  $db->createCommand("
            SELECT kelompoktindakan_nama, 
            SUM(sub_total) as total_amount
            FROM infotagihanpasien_v 
            WHERE pendaftaran_id = {$pendaftaran_id}
            GROUP BY kelompoktindakan_nama
            ORDER BY kelompoktindakan_nama ASC
        ")->queryAll();

        $subTotal = 0;
        if(!empty($data)) {
            foreach($data as $value){
                $total = isset($value['total_amount']) ? $value['total_amount'] : 0;
                $subTotal += $total;
            }
        }

        $jpk_id = json_decode((new DocoConstansId)->actionGetAdditional("JPK"));
        $total_jpk = 0;
        
        $detail = $db->createCommand("
            SELECT *
            FROM infotagihanpasien_v 
            WHERE pendaftaran_id = {$pendaftaran_id}
        ")->queryAll();

        if(!empty($detail)) {
            foreach ($detail as $key => $value) {
                $total = isset($value['sub_total']) ? $value['sub_total'] : 0;
                $tindakan_obat_id = isset($value['tindakan_obat_id']) ? $value['tindakan_obat_id'] : null;
                if(!empty($tindakan_obat_id) && in_array($tindakan_obat_id, $jpk_id)){
                    $total_jpk += $total;
                }
            }
        }

        return [
            'detail' => $data,
            'total_jpk' => $total_jpk,
            'sub_total' => $subTotal,
        ];
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
                    $qtyTindakan = isset($value['qty_tindakan']) ? $value['qty_tindakan'] : 0;
                    $tarifSatuan = isset($value['tarif_satuan']) ? $value['tarif_satuan'] : 0;
                    $tarifTindakan = isset($value['tarif_tindakan']) ? $value['tarif_tindakan'] : 0;
                    $totalAkomodasi += $tarifTindakan;
                    $tindakanId = isset($value['daftartindakan_id']) ? $value['daftartindakan_id'] : null;
                    $ruanganId = isset($value['ruangan_id']) ? $value['ruangan_id'] : null;
                    $kelasPelayananId = isset($value['kelaspelayanan_id']) ? $value['kelaspelayanan_id'] : null;
        
                    if ((!isset($tmpRuangan[$ruanganId]))&&(!isset($tmpKelasPelayanan[$kelasPelayananId]))) {
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
            return [];
        }
    }
    
    protected function getKunjunganRanap($pendaftaran_id)
    {
        $result = [];
        if(!empty($pendaftaran_id)){
            $result = Yii::$app->db->createCommand("
                SELECT pendaftaran_id,no_pendaftaran,no_rekam_medik,nama_pasien,nama_depan,alamat_pasien,rt,rw,tgl_pendaftaran,tgl_admisi,tgl_pulang,tgl_stopakomodasi,
                jenis_kelamin,tanggal_lahir,umur,pasienadmisi_id,carabayar_id,klsrawat,is_aps,is_pasientitipan,is_pasientitipan_pk,penjamin_id,
                is_stoppasientitipan,bpjs_kelas,kelaspelayanan_id,kelaspelayanan_nama,nama_pegawai,
                carabayar_nama,penjamin_nama,ruangan_nama,kamarruangan_nokamar,no_tempattidur,
                kelas_hak,status_kelas
                FROM infokunjunganri_v 
                WHERE pendaftaran_id = {$pendaftaran_id}")
            ->queryOne();
        }
        return $result;
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

    private function getDataAdmin()
    {

        $confSistem = Cache::getKonfigSistem();
        $admTindakanId = !empty($confSistem['adm_tindakan_id']) ? $confSistem['adm_tindakan_id'] : null;
        $model = new DaftarTindakan;
        $query = $model::find()->where(['daftartindakan_id'=>$admTindakanId])->asArray()->one();
        return isset($query['daftartindakan_nama']) ? $query['daftartindakan_nama'] : 'Administration Fee';
    }

    protected function processFlow()
    {
        $this->populateData();
        $this->rincian();
    }
}
