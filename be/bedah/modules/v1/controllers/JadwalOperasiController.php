<?php

/**
    * @author yaya
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoConstansId;
use Doco\components\constans\LookupConstans;

use app\modules\v1\models\InfoRencanaOperasi;
use app\modules\v1\models\InfoPasienOperasiView;
use app\modules\v1\models\InfoRencanaDetailOperasi;
use app\modules\v1\models\BatalOrderPenunjang;
use app\modules\v1\models\HistoriRencanaOperasiView;
use app\modules\v1\models\PasienKirimUnitlain;
use app\modules\v1\models\RencanaOperasi;
use app\modules\v1\models\InfoOrderanBedahView;
use app\modules\v1\models\InfoOrderanBedahDetailView;
use app\modules\v1\models\KamarRuanganView;
use app\modules\v1\models\PasienMasukPenunjangT;
use app\modules\v1\models\TarifPenunjangView;
use app\modules\v1\models\TindakanPelayananT;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\RencanaOperasiView;
use app\modules\v1\models\KamarMasterRuangan;
use app\modules\v1\models\Lookup;
use yii\helpers\ArrayHelper;
use app\modules\v1\cache\Cache;

class JadwalOperasiController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoRencanaOperasi';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["terima"] = ["POST"];
        $verbs["tolak"] = ["POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
        $list_ruangan = Ruangan::find()->select([
            'id' => 'ruangan_id',
            'title' => 'ruangan_nama',
        ])->where([
            'instalasi_id' => DocoConstants::INSTALASI_BEDAH
        ]);

        $request = Yii::$app->request;
        $jadwal_data = InfoRencanaOperasi::find();
        if ($dokter_id = $request->get('dokter_id')) {
            $jadwal_data->andWhere([
                'dr_operator_id' => $dokter_id
            ]);
        }
        $filter_date = date('Y-m-d');
        if ($tanggal_operasi = $request->get('tanggal_operasi')) {
            $tanggal_operasi = date('Y-m-d',strtotime($tanggal_operasi));
            $filter_date = $tanggal_operasi;
        }

        $jadwal_data->andWhere([
            'DATE(tgl_permintaan)' => $filter_date
        ]);

        if ($ruangan_id = $request->get('ruangan_id')) {
            $list_ruangan->andWhere([
                'ruangan_id' => $ruangan_id
            ]);
            $jadwal_data->andWhere([
                'ruangan_id' => $ruangan_id
            ]);
        }

        if ($nomor_request = $request->get('nomor_request')) {
            $jadwal_data->andWhere([
                'ILIKE','no_orderkeunitlain', $nomor_request
            ]);
        }
        
        $data = [];
        $countJadwal = count($jadwal_data->asArray()->all());
        foreach ($jadwal_data->asArray()->all() as $value) {
            $color = '#FFFfff';
            $kosong = null;
            $tgl = date('Y-m-d',strtotime($value['tgl_permintaan']));
            switch ($value['status_penunjang']) {
                case DocoConstants::BTL_APPROVE:
                    $color = 'rgb(255, 15, 33)';
                    break;
                case DocoConstants::DI_TOLAK:
                    $color = 'rgb(255, 15, 33)';
                    break;
                case DocoConstants::DISETUJUI:
                    $color = 'hsl(145, 82%, 61%)';
                    $kosong = "test";
                    $statusPeriksa = isset($value['status_periksa']) ? $value['status_periksa'] : null;
                    if ($statusPeriksa == DocoConstants::ST_P_PEN_BTL) {
                        $color = 'hsl(0, 0%, 80%)';
                        $value['status'] = 'DIBATALKAN';
                    }
                    break;
                case DocoConstants::RESCHEDULE:
                    $color = 'rgb(255, 237, 0)';
                    break;
                default:
                    break;
            }
            $rencanaOperasi = RencanaOperasi::find()->select([
                'catatan_klinis',
                'pemakaian_implant',
                'sewa_alat_rs',
                'sewa_vendor',
                'jenis_operasi_cyto',
                'jenis_operasi_elektif',
                'jenis_operasi_odc',
            ])->where([
                'pendaftaran_id' => $value['pendaftaran_id'],
                'rencanaoperasi_id' => $value['rencanaoperasi_id'],
            ])->asArray()->one();
           
            $is_cyto = RencanaOperasi::find()
            ->select(['jenis_operasi_cyto'])
            ->where([
                'pendaftaran_id' => $value['pendaftaran_id'],
                'rencanaoperasi_id' => $value['rencanaoperasi_id'],
            ]);
            $is_cyto = $is_cyto->andWhere([
                'jenis_operasi_cyto' => 't',
            ])->asArray()->one();

            $end = $value['jam_rencana_selesai'];
            $exp = explode(":", $end);
            $hour = $exp[0] + 1;
            $end = $hour.':'.$exp[1].':'.$exp[2];
            $jamRencanaMulai = !empty($value['jam_rencana_mulai']) ? date('H:i', strtotime($value['jam_rencana_mulai'])) : null;
            $jamRencanaSelesai = !empty($value['jam_rencana_selesai']) ? date('H:i', strtotime($value['jam_rencana_selesai'])) : null;
            $operasi_odc = !empty($rencanaOperasi['jenis_operasi_odc'] == "t") ? 1 : 0;
            $textColor = 'rgb(0, 0, 0)';
            if($operasi_odc){
                $textColor = 'rgb(0 82 230)';
            }
            $data[] = [
                'id' => DocoHelpers::encrypt($value['rencanaoperasi_id']),
                'resourceId' => $value['ruangan_id'],
                'catatan_klinis' => !empty($rencanaOperasi['catatan_klinis']) ? $rencanaOperasi['catatan_klinis'] : " - " ,
                'pemakaian_implant' => !empty($rencanaOperasi['pemakaian_implant']) ? $rencanaOperasi['pemakaian_implant'] : " - ",
                'sewa_alat_rs' => !empty($rencanaOperasi['sewa_alat_rs']) ? $rencanaOperasi['sewa_alat_rs'] : " - ",
                'sewa_vendor' => !empty($rencanaOperasi['sewa_vendor']) ? $rencanaOperasi['sewa_vendor'] : " - ", 
                'operasi_cyto' => !empty($rencanaOperasi['jenis_operasi_cyto'] == "t") ? 1 : 0,
                'operasi_elektif' => !empty($rencanaOperasi['jenis_operasi_elektif'] == "t") ? 1 : 0,
                'operasi_odc' => $operasi_odc,
                'kamar' => $value['kamarruangan_nokamar'],
                'start' => $value['jam_rencana_mulai'],
                'jam_rencana_mulai' => $value['jam_rencana_mulai'],
                'end' => $end,
                'jam_rencana_selesai' => $value['jam_rencana_selesai'],
                'title' => $value['no_orderkeunitlain'] . ' - ' . $value['nama_pasien'] . ' - ' . $value['status'],
                'className' => 'text-center',
                'label' => $value['no_orderkeunitlain'] . ' - ' . $value['nama_pasien'],
                'nama_dokter' => $value['dok_operator'],
                'backgroundColor' => $color,
                'textColor' => $textColor,
                'status' => $value['status_penunjang'],
                'allDay' => false,
                'mulai_operasi' => is_null($value['mulai_operasi']) ? '-' : $value['mulai_operasi'],
                'selesai_operasi' => is_null($value['selesai_operasi']) ? '-' : $value['selesai_operasi'],
                'rencanaoperasi_id' => $value['rencanaoperasi_id'],
                'jam_rencana' => !is_null($jamRencanaMulai) && !is_null($jamRencanaSelesai) ? $jamRencanaMulai.' - '.$jamRencanaSelesai : '-',
                'status_order_ot' => ucfirst($value['status']),
                'kegiatan_operasi_nama' => $value['kegiatanoperasi_nama'],
                'ruangan_nama' => $value['ruangan_nama'],
                'pegawai_approve' => !empty($value['pegawai_approve']) ? $value['pegawai_approve']: '-',
                'tgl_approve' =>  !empty($value['tgl_approve']) ? $value['tgl_approve']: '-',
                'status_periksa' => isset($value['status_periksa']) ? $value['status_periksa'] : null,
            ];
        }

        return [
            'data_ruangan' => $list_ruangan->asArray()->all(),
            'jadwal_data' => $data
        ];
    }


    public function actionGetDataPemeriksaan($id)
    {
        $model = new InfoRencanaDetailOperasi;
        $query = $model::find(true);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $query->where([
           'rencanaoperasi_id' => $id
        ]);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionTerima($id)
    {
        $request = Yii::$app->request;
        $dataInstalasi = json_decode(DocoConstansId::actionGetAdditional('pendaftaran_penunjang'), true);
        $post = \Yii::$app->request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = [];
        $is_konfirm = $request->get('is_konfirm', null);
        $pendaftaran_id = $request->post('pendaftaran_id', null);
        $is_konfirm = true;
        $kamarruangan_id = isset($post['kamarruangan_id']) ? $post['kamarruangan_id'] : null;
        
        try {
            $rencana = RencanaOperasiView::find()
                ->select(['pasien_m.pasien_id', 'rencanaoperasi_v.nama_pasien', 'rencanaoperasi_v.pendaftaran_id', 
                        'rencanaoperasi_v.no_rekam_medik', 'rencanaoperasi_v.no_pendaftaran',
                        'rencanaoperasi_v.tgl_permintaan', 'rencanaoperasi_v.ruangan_id', 
                        'rencanaoperasi_v.pasienkirimkeunitlain_id','rencanaoperasi_v.jam_rencana_mulai', 
                        'rencanaoperasi_v.jam_rencana_selesai', 'rencanaoperasi_v.dr_operator_id','rencanaoperasi_v.kamarruangan_nokamar'])
                ->join('INNER JOIN', 'pasien_m', "rencanaoperasi_v.no_rekam_medik=pasien_m.no_rekam_medik")
                ->where(['rencanaoperasi_v.rencanaoperasi_id' => $id])->asArray()->one();

            if (empty($rencana)) {
                $result['status'] = 500;
                $result['title'] = 'Proses Gagal!';
                $result['text'] = 'Proses terima gagal';
            }
            
            $pasien_id = isset($rencana['pasien_id']) ? $rencana['pasien_id'] : null;
            $newPendaftaranId = $pendaftaran_id;
            $oldPendaftaranId =  isset($rencana['pendaftaran_id']) ? $rencana['pendaftaran_id'] : null;
            $oldNoPendaftaran = isset($rencana['no_pendaftaran']) ? $rencana['no_pendaftaran'] : null;
            
            if(!$is_konfirm) {
                if($newPendaftaranId != $oldPendaftaranId) {
                    $dataOrder = RencanaOperasiView::find()
                        ->select(['pendaftaran_id', 'no_orderkeunitlain', 'tgl_permintaan'])
                        ->where(['pendaftaran_id' => $oldPendaftaranId])
                        ->one();
                    
                    $transaction->rollBack();
                    return [
                        'is_registrasi_terbaru' => true,
                        'data' => [
                            'no_rekam_medik' => isset($rencana['no_rekam_medik']) ? $rencana['no_rekam_medik'] : null,
                            'nama_pasien' => isset($rencana['nama_pasien']) ? $rencana['nama_pasien'] : null,
                            'no_pendaftaran_baru' => $newPendaftaranId,
                            'no_pendaftaran_lama' => $oldNoPendaftaran,
                            'tgl_permintaan' => isset($dataOrder['tgl_permintaan']) ? date('d-M-Y H:i:s', strtotime($dataOrder['tgl_permintaan'])) : null,
                        ],
                    ];
                }
            }
            
            $tanggalRencana = isset($rencana['tgl_permintaan']) ? date('d-M-Y H:i:s', strtotime($rencana['tgl_permintaan'])) : null;
            $ruangan_id = $rencana['ruangan_id'];
            $jamRencanaMulai = $rencana['jam_rencana_mulai'];
            $jamRencanaSelesai = $rencana['jam_rencana_selesai'];
            $statusSetuju = DocoConstants::DISETUJUI;
            $dataDisetujui = Yii::$app->db->createCommand("
                SELECT rencanaoperasi_t.pasienkirimkeunitlain_id, 
                rencanaoperasi_t.pasienmasukpenunjang_id,
                rencanaoperasi_t.ruangan_id,
                pasienmasukpenunjang_t.kamarruangan_id 
                FROM rencanaoperasi_t 
                INNER JOIN pasienkirimkeunitlain_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = rencanaoperasi_t.pasienkirimkeunitlain_id
                INNER JOIN pasienmasukpenunjang_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = pasienkirimkeunitlain_t.pasienmasukpenunjang_id
                WHERE (rencanaoperasi_t.jam_rencana_selesai >= '{$jamRencanaMulai}' 
                AND rencanaoperasi_t.jam_rencana_mulai <= '{$jamRencanaSelesai}') 
                AND DATE(rencanaoperasi_t.tgl_permintaan) = '{$tanggalRencana}'
                AND pasienmasukpenunjang_t.kamarruangan_id = {$kamarruangan_id}
                AND pasienkirimkeunitlain_t.status_penunjang = '{$statusSetuju}'
            ")->queryOne();
            
            $konfigSystem = $this->getKonfigSystem();
            $lepasValidasiJadwalOperasi = ArrayHelper::getValue($konfigSystem, 'lepas_validasi_jadwal_operasi', false);
            if (!empty($dataDisetujui) && !$lepasValidasiJadwalOperasi) {
                $transaction->rollBack();
                return [
                    'status' => 422,
                    'title' => 'Approval Gagal !',
                    'text' => 'Telah diproses pada Jam dan Kamar yang sama'
                ];
            }

            $pasienkirimkeunitlain_id = isset($rencana['pasienkirimkeunitlain_id']) ? $rencana['pasienkirimkeunitlain_id'] : null;
            
            $unitLain = PasienKirimUnitlain::findOne([
                'pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id
            ]);
            
            $rencanaOperasi = RencanaOperasi::findOne([
                'pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id
            ]);

            if (!empty($unitLain)) {
                $infoOrderan = InfoOrderanBedahView::findOne([
                    'pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id
                ]);
                    /** proses input pasienmasukkepenunjang **/
                $inputPasienMasuk = []; /** array untuk input data ke pasien masuk penunjang **/
                $inputPasienMasuk = [
                    'pasienkirimkeunitlain_id' => $infoOrderan->pasienkirimkeunitlain_id,
                    'kelaspelayanan_id' => $infoOrderan->kelaspelayanan_id,
                    'jeniskasuspenyakit_id' => $infoOrderan->jeniskasuspenyakit_id,
                    'pasienadmisi_id' => $infoOrderan->pasienadmisi_id,
                    'pegawai_id' => isset($rencana['dr_operator_id']) ? $rencana['dr_operator_id'] : null,
                    // 'ruangan_id' => $infoOrderan->ruanganpenunjang_id,
                    'ruangan_id' => isset($post['ruangan_id']) ?  $post['ruangan_id'] : '',
                    'pasien_id' => $infoOrderan->pasien_id,
                    'pendaftaran_id' => $infoOrderan->pendaftaran_id,
                    'ruanganasal_id' => $infoOrderan->ruangan_id,
                    'tglmasukpenunjang' => date('Y-m-d H:i:s'),
                    'kunjungan' => $infoOrderan->kunjungan,
                    'panggil_antrian' => false,
                    'instalasiasal_id' => $infoOrderan->instalasi_id,
                    'catatan' => $post['catatan'],
                    'kamarruangan_id' => $post['kamarruangan_id'],
                    'status_periksa' => DocoConstants::BLM_OPERASI
                ];
                /** bila pembayaran nya perorangan. maka status periksa akn menjadi null **/
                if (empty($inputPasienMasuk['kamarruangan_id'])) {
                    return $this->responseJson(400, 'Kamar ruangan tidak boleh kosong');
                }
                $masukPenunjang = new PasienMasukPenunjangT;
                $masukPenunjang->attributes = $inputPasienMasuk;
                $registrasiId = !empty($newPendaftaranId) ? $newPendaftaranId : $oldPendaftaranId;
                $masukPenunjang->pendaftaran_id = $registrasiId;
                if ($masukPenunjang->save()) {
                    /** update pasien kirim unit lain **/
                    $rencanaOperasi->pendaftaran_id = $registrasiId;
                    $rencanaOperasi->pasienmasukpenunjang_id = $masukPenunjang->pasienmasukpenunjang_id;
                    $rencanaOperasi->ruangan_id = isset($post['ruangan_id']) ?  $post['ruangan_id'] : '';
                    $rencanaOperasi->save();

                    $unitLain->pendaftaran_id = $registrasiId;
                    $unitLain->pasienmasukpenunjang_id = $masukPenunjang->pasienmasukpenunjang_id;
                    $unitLain->status_penunjang = $statusSetuju;
                    $unitLain->ruangan_id = isset($post['ruangan_id']) ?  $post['ruangan_id'] : '';
                    $unitLain->save();
                    /** update pasien kirim unit lain **/

                    /** loop tindakan medis **/
                    $orderBedahDetail = InfoOrderanBedahDetailView::find()->where([
                        'pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id
                    ])->asArray()->all();

                    $inputTindakanPelayanan = []; // digunakan untuk menampung data yang akan di input ke tindakan pelayanan
                    $tempDaftarTindakan = []; // digunakan untuk menampung daftar tindakan dan tipepaket id dari $inputTindakanPelayanan. untuk di updatekan ke permintaanPenunjang

                    $ruangan_id = $infoOrderan->ruanganpenunjang_id;
                    $kelas_pelayanan = $infoOrderan->kelaspelayanan_id;
                    $penjamin = $infoOrderan->penjamin_id;
                    $pendaftaranId = null;
                    if (!empty($orderBedahDetail)) {
                        $listTindakan = $listMapping = [];
                        $dataRadDetail = $orderBedahDetail;
                        foreach ($dataRadDetail as $kt => $vt) {
                            $idParent = isset($vt['permintaankepenunjang_id']) ? $vt['permintaankepenunjang_id'] : null;
                            if (empty($idParent)) continue;
                            /** Populate paket atau bukan paket **/
                            if (empty($vt['tipepaket_id'])) {
                                $listTindakan[$vt['daftartindakan_id']] = $idParent;
                            }
                            $pendaftaranId = $infoOrderan->pendaftaran_id;
                            $inputTindakanPelayanan[$idParent] = array(
                                'kelaspelayanan_id' => $infoOrderan->kelaspelayanan_id,
                                'pasien_id' => $infoOrderan->pasien_id,
                                'instalasi_id' => $infoOrderan->instalasipen_id,
                                'daftartindakan_id' => empty($vt['tipepaket_id']) ? $vt['daftartindakan_id'] : null,
                                'tipepaket_id' => isset($vt['tipepaket_id']) ? $vt['tipepaket_id'] : null,
                                'carabayar_id' => $infoOrderan->carabayar_id,
                                'pendaftaran_id' => $infoOrderan->pendaftaran_id,
                                'jeniskasuspenyakit_id' => $infoOrderan->jeniskasuspenyakit_id,
                                'ruangan_id' => $infoOrderan->ruanganpenunjang_id,
                                'pasienmasukpenunjang_id' => $masukPenunjang->pasienmasukpenunjang_id,
                                'penjamin_id' => $infoOrderan->penjamin_id,
                                'pasienadmisi_id' => $infoOrderan->pasienadmisi_id,
                                'tgl_tindakan' => date('Y-m-d H:i:s'),
                                'tarif_rsakomodasi' => 0,
                                'tarif_medis' => 0,
                                'tarif_paramedis' => 0,
                                'tarif_bhp' => 0,
                                'tarif_satuan' => isset($vt['tarif_pelayanan']) ? $vt['tarif_pelayanan'] : 0,
                                'tarif_tindakan' => ($vt['tarif_pelayanan'] * $vt['qtypermintaan']) + $vt['tarif_cytotindakan'],
                                'tarifcyto_tindakan' => isset($vt['tarif_cytotindakan']) ? $vt['tarif_cytotindakan'] : 0,
                                'satuan_tindakan' => isset($vt['satuan_tindakan']) ? $vt['satuan_tindakan'] : 0,
                                'qty_tindakan' => isset($vt['qtypermintaan']) ? $vt['qtypermintaan'] : 0,
                                'cyto_tindakan' => isset($vt['is_cyto']) ? $vt['is_cyto'] : false,
                                'dokterpenanggungjawab_id' => isset($rencana['dr_operator_id']) ? $rencana['dr_operator_id'] : null,
                                'discount_tindakan' => 0,
                                'pembebasan_tindakan' => 0,
                                'subsidiasuransi_tindakan' => 0,
                                'subsidipemerintah_tindakan' => 0,
                                'subsisidirumahsakit_tindakan' => 0,
                                'uangditerima_tindakan' => 0,
                                'additional_data' => json_encode([
                                    'permintaankepenunjang_id' => isset($vt['permintaankepenunjang_id']) ? $vt['permintaankepenunjang_id'] : null,
                                    'list_komponen' => []
                                ]),
                                'pembulatan' => 0,

                            );
                        }
                        
                        if ($listTindakan) {
                            $condTindakan = array_keys($listTindakan);
                            $paket = TarifPenunjangView::find()->where([
                                'ruangan_id' => $ruangan_id,
                                'penjamin_id' => $penjamin,
                                'kelaspelayanan_id' => $kelas_pelayanan,
                                'daftartindakan_id' => $condTindakan
                            ])->asArray()->all();

                            foreach ($paket as $value) {
                                if ($value['komponentarif_id'] == 6) continue;
                                $idDaftar = $value['daftartindakan_id'];
                                $idPermintaan = isset($listTindakan[$idDaftar]) ? $listTindakan[$idDaftar] : null;
                                if (isset($inputTindakanPelayanan[$idPermintaan])) {
                                    $remakeAdditional = 
                                            json_decode($inputTindakanPelayanan[$idPermintaan]['additional_data'],true);
                                    $hargaCyto = ($value['persencyto_tindakan']/100) * $value['harga_tariftindakan'];
                                    $remakeAdditional['list_komponen'][] = [
                                        'komponentarif_id' => isset($value['komponentarif_id']) ? $value['komponentarif_id'] : null,
                                        'tindakanpelayanan_id' => null,
                                        'tarif_kompsatuan' => isset($value['harga_tariftindakan']) ? $value['harga_tariftindakan'] : 0,
                                        'tarif_tindakankomp' => isset($value['harga_tariftindakan']) ? $value['harga_tariftindakan'] : 0,
                                        'tarifcyto_tindakankomp' => $hargaCyto,
                                        'subsidiasuransikomp' => 0,
                                        'subsidipemerintahkomp' => 0,
                                        'subsidirumahsakitkomp' => 0,
                                        'iurbiayakomp' => 0
                                    ];
                                    $inputTindakanPelayanan[$idPermintaan]['additional_data'] = json_encode($remakeAdditional);
                                }
                            }
                        }
                    }

                    $dataBatal = Yii::$app->db->createCommand("
                        SELECT pasienkirimkeunitlain_id FROM rencanaoperasi_t 
                        WHERE  (jam_rencana_selesai >= '{$jamRencanaMulai}' 
                        AND jam_rencana_mulai <= '{$jamRencanaSelesai}') 
                        AND DATE(tgl_permintaan) = '{$tanggalRencana}'
                        AND  rencanaoperasi_id != {$id}
                        AND ruangan_id = {$ruangan_id}
                    ")->queryAll();
                    $listId = [];
                    foreach ($dataBatal as $key => $value) {
                        $listId[] = $value['pasienkirimkeunitlain_id'];
                    }
                    // PasienKirimUnitlain::updateAll(['status_penunjang' => DocoConstants::RESCHEDULE],[
                    //     'pasienkirimkeunitlain_id' => $listId
                    // ]);
                    $statusLunas = DocoConstants::BELUM_LUNAS;
                    if (!empty($pendaftaranId)) {
                        Yii::$app->db->createCommand("
                            UPDATE pendaftaran_t SET status_bayar = {$statusLunas}
                            WHERE pendaftaran_id = {$pendaftaranId}
                        ")->execute();
                    }

                    $historyRencanOperasi = [
                        'rencanaoperasi_id'   => $id,
                        'ruangan_id'          => ArrayHelper::getValue($post, 'ruangan_id', null),
                        'kamarruangan_id'     => ArrayHelper::getValue($post, 'kamarruangan_id', null),
                        'tgl_permintaan'      => ArrayHelper::getValue($rencana, 'tgl_permintaan', null),
                        'tgl_perubahan'       => date('Y-m-d H:i:s'),
                        'jam_rencana_mulai'   => ArrayHelper::getValue($rencana, 'jam_rencana_mulai', null),
                        'jam_rencana_selesai' => ArrayHelper::getValue($rencana, 'jam_rencana_selesai', null),
                        'status_operasi'      => $statusSetuju,
                        'keterangan'          => ArrayHelper::getValue($post, 'catatan', null),
                    ];
    
                    $this->historyRencanaOperasi($historyRencanOperasi);

                    $transaction->commit();
                    $result = [
                        'status' => 200,
                        'title' => 'Input Berhasil',
                        'text' => 'Aprroval Pasien Berhasil'
                    ];
                } else {
                    $transaction->rollBack();
                    $result['status'] = 500;
                    $result['title'] = 'Gagal insert';
                    $result['text'] = $masukPenunjang->getErrors();
                }
            } else {
                $transaction->rollBack();
                $result = [
                    'status' => 500,
                    'title' => 'Terjadi kesalahan',
                    'text' => 'Data tidak ditemukan'
                ];
            }
            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            \Yii::error([
                "File" => $e->getFile(),
                "Message" => $e->getMessage(),
                "Line" => $e->getLine(),
            ]);
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionTolak($id)
    {
        $post = Yii::$app->request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = [];
        try {
            $rencana = RencanaOperasi::find()->where([
                'rencanaoperasi_id' => $id
            ])->one();

            if (empty($rencana)) {
                $result['status'] = 500;
                $result['title'] = 'Proses Gagal!';
                $result['text'] = 'Proses pembatalan gagal';
            }

            $kirimPasien = PasienKirimUnitlain::findOne([
                'pasienkirimkeunitlain_id' => $rencana->pasienkirimkeunitlain_id
            ]);
            
            if (!empty($kirimPasien)) {
                    $tanggal_batal = date('Y-m-d H:i:s');
                    $pegawai_id = Yii::$app->jwt->user->pegawai_id;
                    $inputBatalOrder = array(
                        'pasienkirimkeunitlain_id' => $rencana->pasienkirimkeunitlain_id,
                        'tgl_batalorder' => $tanggal_batal,
                        'peg_menyetujui_id' => $pegawai_id,
                        'alasan' => $post['catatan'],
                        'additional_data' => json_encode(['pasienkirimkeunitlain_id' => $rencana->pasienkirimkeunitlain_id]),
                    );
                    
                    $batalOrder = new BatalOrderPenunjang;
                    $batalOrder->attributes = $inputBatalOrder;
                    if ($batalOrder->save()) {
                        $kirimPasien->status_penunjang = DocoConstants::DI_TOLAK;
                        $kirimPasien->save();

                        $historyRencanOperasi = [
                            'rencanaoperasi_id'   => $id,
                            'ruangan_id'          => ArrayHelper::getValue($rencana, 'ruangan_id', null),
                            'kamarruangan_id'     => ArrayHelper::getValue($post, 'kamarruangan_id', null),
                            'tgl_permintaan'      => ArrayHelper::getValue($rencana, 'tgl_permintaan', null),
                            'tgl_perubahan'       => date('Y-m-d H:i:s'),
                            'jam_rencana_mulai'   => ArrayHelper::getValue($rencana, 'jam_rencana_mulai', null),
                            'jam_rencana_selesai' => ArrayHelper::getValue($rencana, 'jam_rencana_selesai', null),
                            'status_operasi'      => DocoConstants::DI_TOLAK,
                            'keterangan'          => ArrayHelper::getValue($post, 'catatan', null),
                        ];

                        $this->historyRencanaOperasi($historyRencanOperasi);

                        $transaction->commit();
                        $result = [
                            'status' => 200,
                            'title' => 'Input Berhasil',
                            'text' => 'Pembatalan Order Berhasil'
                        ];
                    } else {
                        $transaction->rollBack();
                        $result['status'] = 500;
                        $result['title'] = 'Gagal insert';
                        $result['text'] = $batalOrder->getErrors();
                       
                    }
            } else {
                $transaction->rollBack();
                $result = [
                    'status' => 500,
                    'title' => 'Terjadi kesalahan',
                    'text' => 'Data tidak ditemukan'
                ];
            }

            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
    public function actionNamaRuangan()
    {
        $result = [];
        try {
            $query = InfoRencanaOperasi::find()->select(['ruangan_nama'])->distinct()->asArray()->all();
            $result = $query;
        } catch (\yii\db\Exception $e) {
            $result = [];
        }
        return [ 
            'data' => $result, 
        ];

    }

    public function actionView($id)
    {
        $result = [];
        
        try {
            
            $ruangan_odc = DocoConstansId::actionGetId(LookupConstans::RUANGAN_ODC);

            $query = RencanaOperasiView::find()
            ->select(['pasien_m.pasien_id', 'rencanaoperasi_v.*', 'rencanaoperasi_t.*'])
            ->join('INNER JOIN', 'pasien_m', "rencanaoperasi_v.no_rekam_medik=pasien_m.no_rekam_medik")
            ->join('INNER JOIN', 'rencanaoperasi_t', "rencanaoperasi_v.rencanaoperasi_id=rencanaoperasi_t.rencanaoperasi_id")
            ->where(['rencanaoperasi_v.rencanaoperasi_id' => $id])->asArray()->one();
            $result = $query;
            $rencanaOperasi = $list_pendaftaran = [];
            
            $pasien_id = !empty($result['pasien_id']) ? $result['pasien_id'] : null;
            if(!empty($pasien_id)){
                $list_pendaftaran = Pendaftaran::find()
                    ->select(['pendaftaran_id', 'no_pendaftaran', 'tgl_pendaftaran'])
                    ->where(['pasien_id' => $pasien_id])
                    ->andWhere(['ruangan_id' => $ruangan_odc])
                    ->andWhere(['<>','status_periksa', DocoConstants::STATUS_PERIKSA_BTL_KUNJ])
                    ->orderBy(['tgl_pendaftaran' => SORT_DESC])
                    ->asArray()->all();
            }
            $data = [ 
                'data' => $result, 
                'list_pendaftaran' => $list_pendaftaran,
            ];

        } catch (\yii\db\Exception $e) {
            \Yii::error([$e->getMessage()]);
            return $data = [];
        }
        return $data;

    }

    /**
     * This function to return data kamar ruangan
     * 
     * @param String var
     * @return JSON
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionKamarRuangan()
    {
        $term = Yii::$app->request->get('term', null);
        $ruangan = Yii::$app->request->get('ruangan_id', null);
        $kelasPelayanan = Yii::$app->request->get('kelaspelayanan_id', null);
        $page = Yii::$app->request->get('page', 1);
        $instalasi = DocoConstansId::actionGetId('IBS');

        $query = KamarMasterRuangan::find()
        ->select([
            'masterkamarruangan_v.kamarruangan_id as id',
            'masterkamarruangan_v.kamarruangan_nokamar as text',
        ])->where([
            'masterkamarruangan_v.is_active' => 't'
        ])
        ->join('JOIN','ruangan_m', 'ruangan_m.ruangan_id=masterkamarruangan_v.ruangan_id')
        ->andWhere([
            'ruangan_m.instalasi_id' => $instalasi
        ]);
        
        if (!empty($term)) {
        $query->andWhere([
            'ilike',
            'masterkamarruangan_v.kamarruangan_nokamar',
            $term
        ]);
        }

        if (!empty($ruangan)) {
            $query->andWhere([
                'masterkamarruangan_v.ruangan_id' => $ruangan
                
            ]);
        }

        $limit = DocoConstants::LIMIT_INFINITY_SCROLL;
        return $query->limit($limit + 1)
            ->offset(($page - 1) * $limit)
            ->asArray()
            ->all();
    }

    public function actionGetJadwalOperasi()
    {
        $request = Yii::$app->request;
        $result = [];
        try {
            $id = $request->get('id', null);
            $result = InfoRencanaOperasi::find()
                ->select([
                    'rencanaoperasi_id',
                    'dr_operator_id',
                    'dok_operator',
                    'dr_anastesi_id',
                    'dok_anastesi',
                    'ruangan_id'
                ])
                ->where(['rencanaoperasi_id' => $id])->asArray()->one();
        } catch (\yii\db\Exception $e) {
            $result = [];
        }
        return $result;
    }

    public function actionReschedule()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = [];
        try {
            $id = $request->get('id', null);
            $rencana = RencanaOperasi::find()->where([
                'rencanaoperasi_id' => $id
            ])->one();

            if (empty($rencana)) {
                $result['status'] = 500;
                $result['title'] = 'Proses Gagal!';
                $result['text'] = 'Proses Reschedule Gagal';
            }
            
            // $statusSetuju = DocoConstants::DISETUJUI;
            $statusSetuju = DocoConstants::RESCHEDULE;
            $ruangan_id = $post['ruangan_id'];
            $jamRencanaMulai = $post['jam_mulai'];
            $jamRencanaSelesai = $post['jam_selesai'];
            $drOperatorId = $post['dr_operator_id'];
            $drAnestesiId = $post['dr_anestesi_id'];
            $tanggalRencana = date('Y-m-d', strtotime($post['tgl_kirimpasien']));
            $dataBatal = Yii::$app->db->createCommand("
                SELECT rencanaoperasi_t.pasienkirimkeunitlain_id FROM rencanaoperasi_t 
                INNER JOIN pasienkirimkeunitlain_t 
                    ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = rencanaoperasi_t.pasienkirimkeunitlain_id
                WHERE  (rencanaoperasi_t.jam_rencana_selesai >= '{$jamRencanaMulai}' 
                AND rencanaoperasi_t.jam_rencana_mulai <= '{$jamRencanaSelesai}') 
                AND DATE(rencanaoperasi_t.tgl_permintaan) = '{$tanggalRencana}'
                AND rencanaoperasi_t.ruangan_id = {$ruangan_id}
                AND pasienkirimkeunitlain_t.status_penunjang = '{$statusSetuju}'
            ")->queryOne();
            

            if (!empty($dataBatal)) {
                $transaction->rollBack();
                return [
                    'status' => 422,
                    'title' => 'Reschedule Gagal !',
                    'text' => 'Telah diproses pada jam dan ruangan yang sama'
                ];
            }

            $kirimPasien = PasienKirimUnitlain::findOne([
                'pasienkirimkeunitlain_id' => $rencana->pasienkirimkeunitlain_id
            ]);

            if(!empty($kirimPasien)) {
                $kirimPasien->status_penunjang = DocoConstants::RESCHEDULE;
                $rencana->tgl_permintaan = $tanggalRencana;
                $rencana->jam_rencana_mulai = $jamRencanaMulai;
                $rencana->jam_rencana_selesai = $jamRencanaSelesai;
                $rencana->dr_operator_id = $drOperatorId;
                $rencana->dr_anastesi_id = $drAnestesiId;

                $historyRencanOperasi = [
                    'rencanaoperasi_id'   => $id,
                    'ruangan_id'          => ArrayHelper::getValue($post, 'ruangan_id', null),
                    'kamarruangan_id'     => ArrayHelper::getValue($post, 'kamarruangan_id', null),
                    'tgl_permintaan'      => ArrayHelper::getValue($post, 'tgl_kirimpasien', null),
                    'tgl_perubahan'       => date('Y-m-d H:i:s'),
                    'jam_rencana_mulai'   => ArrayHelper::getValue($post, 'jam_mulai', null),
                    'jam_rencana_selesai' => ArrayHelper::getValue($post, 'jam_selesai', null),
                    'status_operasi'      => $statusSetuju,
                    'keterangan'          => ArrayHelper::getValue($post, 'alasan', null),
                ];

                $this->historyRencanaOperasi($historyRencanOperasi);

                if($rencana->save() && $kirimPasien->save()) {

                    $transaction->commit();
                    $result = [
                        'status' => 200,
                        'title' => 'Update Berhasil',
                        'text' => 'Reschedule Jadwal Operasi Berhasil'
                    ];
                }
            }

            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetDataJadwal()
    {
        $request = Yii::$app->request;
        $result = [];
        $konfigSystem = $this->getKonfigSystem();
        try {
            $tanggal_operasi = $request->get('tanggal_operasi', null);
            $ruangan_id = $request->get('ruangan_id', null);
            $filter_date = date('Y-m-d');
            if(!empty($tanggal_operasi) && !empty($ruangan_id)) {
                $jadwal_data = InfoRencanaOperasi::find();
                $tanggal_operasi = date('Y-m-d',strtotime($tanggal_operasi));
                $filter_date = $tanggal_operasi;
                $jadwal_data->andWhere([
                    'DATE(tgl_permintaan)' => $filter_date
                ]);
                $jadwal_data->andWhere([
                    'ruangan_id' => $ruangan_id
                ]);
                foreach ($jadwal_data->asArray()->all() as $value) {
                    $color = '#FFFfff';
                    $tgl = date('Y-m-d',strtotime($value['tgl_permintaan']));
                    switch ($value['status_penunjang']) {
                        case DocoConstants::BTL_APPROVE:
                            $color = 'rgb(255, 15, 33)';
                            break;
                        case DocoConstants::DI_TOLAK:
                            $color = 'rgb(255, 15, 33)';
                            break;
                        case DocoConstants::DISETUJUI:
                            $color = 'hsl(145, 82%, 61%)';
                            break;
                        case DocoConstants::RESCHEDULE:
                            $color = 'rgb(255, 195, 0)';
                            break;
                        default:
                            break;
                    }

                    $result[] = [
                        'id' => DocoHelpers::encrypt($value['rencanaoperasi_id']),
                        'resourceId' => $value['ruangan_id'],
                        'start' => $value['jam_rencana_mulai'],
                        'jam_rencana_mulai' => $value['jam_rencana_mulai'],
                        'end' => $value['jam_rencana_selesai'],
                        'jam_rencana_selesai' => $value['jam_rencana_selesai'],
                        'title' => $value['no_orderkeunitlain'] . ' - ' . $value['nama_pasien'] . ' - ' . $value['status'],
                        'className' => 'text-center',
                        'label' => $value['no_orderkeunitlain'] . ' - ' . $value['nama_pasien'],
                        'nama_dokter' => $value['dok_operator'],
                        'backgroundColor' => $color,
                        'textColor' => 'rgb(0, 0, 0)',
                        'status' => $value['status_penunjang'],
                        'allDay' => false,
                        'mulai_operasi' => is_null($value['mulai_operasi']) ? '-' : $value['mulai_operasi'],
                        'selesai_operasi' => is_null($value['selesai_operasi']) ? '-' : $value['selesai_operasi'],
                        'rencanaoperasi_id' => $value['rencanaoperasi_id'],
                    ];
                }
            }
            return [
                'lepas_validasi_jadwal_operasi' => ArrayHelper::getValue($konfigSystem, 'lepas_validasi_jadwal_operasi', false),
                'results' => $result,
            ];
        } catch (Exception $e) {
            return $result;
        }
    }

    public function actionRuangan()
    {
        return Ruangan::find()->select([
            'id' => 'ruangan_id',
            'text' => 'ruangan_nama',
        ])->where([
            'instalasi_id' => DocoConstants::INSTALASI_BEDAH
        ])->asArray()->all();
    }
    private function historyRencanaOperasi($data) 
    {
        $connection = Yii::$app->db;
        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;

        $userId = !empty($jwt->loginpemakai_id) ? $jwt->loginpemakai_id : null;

        $connection->createCommand()->insert('historirencanaoperasi_r', [
            'rencanaoperasi_id'   => ArrayHelper::getValue($data, 'rencanaoperasi_id', null),
            'ruangan_id'          => ArrayHelper::getValue($data, 'ruangan_id', null),
            'kamarruangan_id'     => ArrayHelper::getValue($data, 'kamarruangan_id', null),
            'tgl_permintaan'      => ArrayHelper::getValue($data, 'tgl_permintaan', null),
            'tgl_perubahan'       => ArrayHelper::getValue($data, 'tgl_perubahan', null),
            'jam_rencana_mulai'   => ArrayHelper::getValue($data, 'jam_rencana_mulai', null),
            'jam_rencana_selesai' => ArrayHelper::getValue($data, 'jam_rencana_selesai', null),
            'status_operasi'      => ArrayHelper::getValue($data, 'status_operasi', null),
            'keterangan'          => ArrayHelper::getValue($data, 'keterangan', null),
            'created_date'        => date('Y-m-d H:i:s'),
            'created_by'          => $userId
        ])->execute();
    }

    public function actionRiwayatOperasiGetData()
	{
		$request = Yii::$app->request;
		$pasienkirimkeunitlain_id = $request->post('pasienkirimkeunitlain_id');
		$query = HistoriRencanaOperasiView::find()->where(['pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id])->orderBy(['tgl_perubahan' => SORT_ASC]);
		return new ActiveDataProvider([
			'query' => $query,
		]);
	}

    private function getKonfigSystem()
    {
        return Cache::getKonfigSistem();
    }
}