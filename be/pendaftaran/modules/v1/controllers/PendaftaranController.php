<?php

/**
 * @Author: Naufal Ziyad L
 * @Date:   2018-01-31 13:25
 * @Mod: Ardi Pratama Septiadi
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\data\Sort;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\controllers\AllowController;
use Doco\components\DocoMessages;

use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\KasusPenyakitRuangan;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\JenisKasusPenyakit;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\Propinsi;
use app\modules\v1\models\Suku;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\Pekerjaan;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\Rujukan;
use app\modules\v1\models\Antrian;
use app\modules\v1\models\PenanggungJawab;
use app\modules\v1\models\Loket;
use app\modules\v1\models\LoketMp;
use app\modules\v1\models\PasienAdmisi;
use app\modules\v1\models\MasukKamar;
use app\modules\v1\models\PasienPulangRdRjView;
use app\modules\v1\models\InfoKunjunganRajal;
use app\modules\v1\models\InfoTarifRsView;
use app\modules\v1\models\TarifTindakan;
use app\modules\v1\models\TindakanPelayanan;
use app\modules\v1\models\TindakanKomponen;
use app\modules\v1\models\KamarTempatTidur;
use app\modules\v1\models\AsuransiPasien;
use app\modules\v1\models\TraPemesananKamar;
use app\modules\v1\models\PasienMasukPenunjang;
use app\modules\v1\models\InfoKunjunganRsView;
use app\modules\v1\models\DiagnosaView;
use app\modules\v1\models\BpjsView;
use app\modules\v1\models\DokterV;
use app\modules\v1\models\SyncPendaftaran;
use app\modules\v1\models\SyncPendaftaranTransaksi;
use app\modules\v1\models\SyncPasien;
use app\modules\v1\models\PasienKunjunganAkhirView;
use GuzzleHttp\Exception\RequestException;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\PendaftaranOnline;
use app\modules\v1\models\Konsulpoli;
use app\modules\v1\models\TarifTotalRsFn;
use app\modules\v1\models\InfoDataPendaftaranView;
use app\modules\v1\models\TindakanSpesialis;
use app\modules\v1\payload\TarifPayload;

use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use SirsCore\features\IntegrasiAkunting;
class PendaftaranController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Pendaftaran';
    const JENIS_IDENTITAS = 'jenis_identitas';
    const NAMA_DEPAN = 'nama_depan';
    const JENIS_KELAMIN = 'jenis_kelamin';
    const STATUS_PERKAWINAN = 'status_perkawinan';
    const WARGA_NEGARA = 'warga_negara';
    const AGAMA = 'agama';
    const PENGANTAR = 'pengantar';
    const HUBUNGAN = 'hubungan_keluarga';

    const IGD = 'igd';
    const RANAP = 'ranap';
    const RAJAL = 'rajal';
    const MCU = 'mcu';
    const PENUNJANG = 'penunjang';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        $verbs['cari-pendaftaran-by-id'] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        $newActions = [
            'get-tarif-karcis-pendaftaran' => 'app\modules\v1\actions\Pendaftaran\GetTarifKarcisPendaftaranAction',
        ];
        $actions = array_merge($actions, $newActions);
        return $actions;
    }

    public function actionPilihLoket($jenisantrian_id = null)
    {
        try {
            $loket = new Loket;
            // $fungsiAntrian = DocoConstants::LOOKUP_TYPE_FUNGSI_ANTRIAN;
            // $defaultPendaftaran = DocoConstants::LOOKUP_NAME_DEFAULT_PENDAFTARAN;
            $post = Yii::$app->request->post();
            $ja_dftr = DocoConstants::VAR_JA_PD;
            $sql = "
                SELECT
                    t.loket_id,
                    t.loket_nama,
                    t.loket_namalain,
                    x.loginpemakai_id,
                    x.nama_pemakai,
                    t.jenisantrian_id
                FROM loket_m t
                    RIGHT JOIN loket_mp loket_mp ON t.loket_id = loket_mp.loket_id AND loket_mp.is_deleted = false AND loket_mp.is_active = true
                    LEFT JOIN loginpemakai_k x ON x.loginpemakai_id = t.loginpemakai_id
                WHERE 1=1
                -- loginpemakai_id IS NULL
                AND t.is_deleted = false
                AND t.is_active = true
                AND t.jenisantrian_id = {$jenisantrian_id}
                GROUP BY t.loket_id, x.loginpemakai_id
                ORDER BY t.loket_nama
            ";
            $result = Yii::$app->db->createCommand($sql)->queryAll();
            // $result = $loket->findBySql($sql)->all();
            // $result = $this->getOrSetCache(DocoConstants::VAR_CACHE_LOKET_PENDAFTARAN, $query, true);
            return $result;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionSetLoket()
    {
        try {
            $post = Yii::$app->request->post();
            $loginpemakai_id = $post['loginpemakai_id'];
            $loket_id = $post['loket_id'];

            // Loket::updateAll(['loginpemakai_id' => null], "loginpemakai_id = {$loginpemakai_id}");

            $loket = Loket::findOne($loket_id);
            if ($loket && $loket->loginpemakai_id != null) {
                return [
                    'status'=>500,
                    'message'=>'Loket sudah digunakan. Silahkan pilih loket lain.'
                ];
            }
            $loket->loginpemakai_id = $loginpemakai_id;
            $loket->save(false);
            return $loket;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }

    }

    public function actionIndex()
    {
        $model = new Pendaftaran;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        $request = Yii::$app->request;
        $advancedFilters = $request->get('advanced-filter', []);

        if (isset($advancedFilters['tgl_pendaftaran_awal']) && isset($advancedFilters['tgl_pendaftaran_akhir']))
        {
            $tgl_awal = $advancedFilters['tgl_pendaftaran_awal'];
            $tgl_akhir = $advancedFilters['tgl_pendaftaran_akhir'];
            $query->andWhere(['between', 'tgl_jadwal', $tgl_awal, $tgl_akhir]);
        }

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetKunjunganRajal($id = null){
        try {
            $request = Yii::$app->request;
            $model = new InfoKunjunganRajal;
            $query = $model::find();
            if(!empty($id)){
                $query->where(['=','pasien_id', $id]);
            }
            return new ActiveDataProvider([
                'query' => $query,
            ]);

        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetTarifTotal($ruangan_id, $kp_id,$penjamin_id, $tarifgroup = 17)
    {

        $data = $this->getTarifKarcis($ruangan_id, $kp_id, '', $tarifgroup, $penjamin_id);
        // $data->select(['jenistarif_nama','harga_tariftindakan','daftartindakan_id','komponentarif_id']);
        $data->select(['daftartindakan_nama','harga_tariftindakan','daftartindakan_id','komponentarif_id','is_default','is_konsultasi']);
        return new ActiveDataProvider([
                'query' => $data,
                'pagination'=>false,
            ]);
    }

    public function getTarifKarcis($ruangan_id, $kp_id, $komponentarif_id = null, $kelompoktindakan_id = null,$penjamin_id){
        $model = new InfoTarifRsView;
        $query = $model::find();
        $query->where(['ruangan_id'=>$ruangan_id, 'kelaspelayanan_id'=>$kp_id,'penjamin_id'=>$penjamin_id]);
        if(!empty($komponentarif_id)){
            $query->andWhere(['komponentarif_id'=>$komponentarif_id]);
        }
        if(!empty($kelompoktindakan_id)){
            $query->andWhere(['kelompoktindakan_id'=>$kelompoktindakan_id]);
        }
        return $query;
    }

    public function actionListRuangan($jeniskasuspenyakit_id) {
        $data = KasusPenyakitRuangan::find()
            ->joinWith(['ruangan'])
            ->where(['kasuspenyakitruangan_mp.is_active' => 't', 'kasuspenyakitruangan_mp.is_deleted' => 'f',
                'kasuspenyakitruangan_mp.jeniskasuspenyakit_id' => $jeniskasuspenyakit_id ]);

        $items = ArrayHelper::map($data->all(), 'ruangan.ruangan_id', 'ruangan.ruangan_nama');

        return $items;
    }

    public function actionCreateRanap() {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $request = Yii::$app->request;
            $modelPenanggungjawab = new PenanggungJawab;
            $modelRanap = new PasienAdmisi;
            $modelPasien = new PasienPulangRdRjView;
            $modelPendaftaran = new Pendaftaran();

            if ($request->post()) {
                $post = $request->post();

                $postPj = $post['PenanggungjawabForm'];
                $postRanap = $post['PasienAdmisiForm'];
                $postPasien = $post['PasienForm'];
                $postPendaftaran = $post['PendaftaranForm'];
                $postRujukan = $post['RujukanForm'];

                $modelPenanggungjawab->attributes = $postPj;
                $modelRanap->attributes = $postRanap;
                $modelPasien->attributes = $postPasien;
                $modelPendaftaran->attributes = $postPendaftaran;

                $modelRanap->pasien_id = $postPasien['pasien_id'];
                $modelRanap->ruangan_id = DocoHelpers::decrypt($postRanap['ruangan_id']);
                $modelRanap->pendaftaran_id = $modelPasien->pendaftaran_id;
                $modelRanap->tgl_pendaftaran = $modelPasien->tgl_pendaftaran;
                $modelRanap->kunjungan = $postPendaftaran['kunjungan'];
                if($modelRanap->validate()) {
                    $modelRanap->save();
                }
                else {
                $error_ranap = DocoHelpers::parseError($modelRanap->errors, 'PasienAdmisiForm');
                    return [
                        'data' => $error_ranap,
                        'status' => 422
                    ];
                }

                $masukKamar = new MasukKamar;
                $masukKamar->ruangan_id = $modelRanap->ruangan_id;
                $masukKamar->carabayar_id = $modelRanap->carabayar_id;
                $masukKamar->pasienadmisi_id = $modelRanap->pasienadmisi_id;
                $masukKamar->penjamin_id = $modelRanap->penjamin_id;
                $masukKamar->pegawai_id = $modelRanap->pegawai_id;
                $masukKamar->kelaspelayanan_id = $modelRanap->kelaspelayanan_id;
                $masukKamar->kamartempattidur_id = $modelRanap->kamartempattidur_id;
                $masukKamar->kamarruangan_id = $modelRanap->kamarruangan_id;
                $masukKamar->tgl_masukkamar = $modelRanap->tgl_admisi;
                $masukKamar->no_masukkamar = $modelRanap->ruangan_id;
                $masukKamar->jam_masukkamar = date('H:i:s', strtotime($modelRanap->tgl_admisi));
                if($masukKamar->validate()) {
                    $masukKamar->save();
                }

                $jenis_isi = ($postPasien['jeniskelamin'] == 15) ? 4 : 3;
                $modelKamarTempatTidur = KamarTempatTidur::findOne($modelRanap->kamartempattidur_id);
                $modelKamarTempatTidur->status_isi = true;
                $modelKamarTempatTidur->kettempattidur_id = $jenis_isi;
                $modelKamarTempatTidur->save(false);

                $modelPenanggungjawab->pasien_id = $postPasien['pasien_id'];
                if($modelPenanggungjawab->validate()) {
                    $modelPenanggungjawab->save();
                }
                else {
                $error_pj = DocoHelpers::parseError($modelPenanggungjawab->errors, 'PenanggungjawabForm');
                    return [
                        'data' => $error_pj,
                        'status' => 422
                    ];
                }

                if($postPendaftaran['instalasi_id'] == 1) {
                    $modelPendaftaran->penanggungjawab_id = $modelPenanggungjawab->penanggungjawab_id;
                    $modelPendaftaran->tgl_pendaftaran = date('Y-m-d H:i:s');
                    $modelPendaftaran->penjamin_id = $postRanap['penjamin_id'];
                    $modelPendaftaran->pasien_id = $postPasien['pasien_id'];
                    $modelPendaftaran->pegawai_id = $postRanap['pegawai_id'];
                    $modelPendaftaran->instalasi_id = $postPendaftaran['instalasi_id'];
                    $modelPendaftaran->jeniskasuspenyakit_id = $postRanap['jeniskasuspenyakit_id'];
                    $modelPendaftaran->kelaspelayanan_id = $postRanap['kelaspelayanan_id'];
                    $modelPendaftaran->carabayar_id = $postRanap['carabayar_id'];
                    $modelPendaftaran->pasienadmisi_id = $modelRanap->pasienadmisi_id;
                    $modelPendaftaran->golonganumur_id = $postPendaftaran['golonganumur_id'];
                    $modelPendaftaran->ruangan_id = DocoHelpers::decrypt($postRanap['ruangan_id']);
                    $modelPendaftaran->status_periksa = $postPendaftaran['status_periksa'];
                    $modelPendaftaran->status_pasien = $postPendaftaran['status_pasien'];
                    $modelPendaftaran->kunjungan = $postPendaftaran['kunjungan'];
                    $modelPendaftaran->status_masuk = $postPendaftaran['status_masuk'];
                    if($modelPendaftaran->validate()) {
                        $modelPendaftaran->save();
                    }
                    else {
                    $error_pendaftaran = DocoHelpers::parseError($modelPendaftaran->errors, 'PendaftaranForm');
                        return [
                            'data' => $error_pendaftaran,
                            'status' => 422
                        ];
                    }
                }

                $arr_insert_pelayanan = [];
                $arr_insert_komponen = [];
                foreach ($post['status'] as $karcis) {
                    $cek = InfoTarifRsView::find()
                        ->where([
                            'daftartindakan_id' => $karcis,
                            'kelaspelayanan_id' => $postRanap['kelaspelayanan_id'],
                            'instalasi_id' => 3,
                            'ruangan_id' => DocoHelpers::decrypt($postRanap['ruangan_id']),
                        ])->all();

                    foreach ($cek as $key => $value) {
                        if($value['komponentarif_id'] == 6) {
                            $modelTindakanPelayanan = new TindakanPelayanan;
                            $modelTindakanPelayanan->kelaspelayanan_id = $postRanap['kelaspelayanan_id'];
                            $modelTindakanPelayanan->pasien_id = $postPasien['pasien_id'];
                            $modelTindakanPelayanan->instalasi_id = 3;
                            $modelTindakanPelayanan->daftartindakan_id = $value['daftartindakan_id'];
                            $modelTindakanPelayanan->carabayar_id = $postRanap['carabayar_id'];
                            $modelTindakanPelayanan->pendaftaran_id = $modelPendaftaran->pendaftaran_id;
                            $modelTindakanPelayanan->jeniskasuspenyakit_id = $postRanap['jeniskasuspenyakit_id'];
                            $modelTindakanPelayanan->ruangan_id = DocoHelpers::decrypt($postRanap['ruangan_id']);
                            $modelTindakanPelayanan->penjamin_id = $postRanap['penjamin_id'];
                            $modelTindakanPelayanan->tgl_tindakan = date('Y-m-d H:i:s');
                            $modelTindakanPelayanan->tarif_satuan = $value['harga_tariftindakan'];
                            $modelTindakanPelayanan->tarif_tindakan = $value['harga_tariftindakan'];
                            $modelTindakanPelayanan->tarifcyto_tindakan = 0;
                            $modelTindakanPelayanan->satuan_tindakan = 424;
                            $modelTindakanPelayanan->qty_tindakan = 1;
                            $modelTindakanPelayanan->cyto_tindakan = false;
                            $modelTindakanPelayanan->discount_tindakan = 0;
                            $modelTindakanPelayanan->save(false);
                        }

                        $cekmodelTindakanPelayanan = TindakanPelayanan::find()->where([
                            'daftartindakan_id' => $karcis,
                            'kelaspelayanan_id' => $postRanap['kelaspelayanan_id'],
                            'instalasi_id' => 3,
                            'ruangan_id' => DocoHelpers::decrypt($postRanap['ruangan_id']),
                        ])->one();

                        if($cekmodelTindakanPelayanan) {
                            $modelTindakanKomponen = new TindakanKomponen;
                            $modelTindakanKomponen->komponentarif_id = $value['komponentarif_id'];
                            $modelTindakanKomponen->tindakanpelayanan_id = $cekmodelTindakanPelayanan->tindakanpelayanan_id;
                            $modelTindakanKomponen->tarif_kompsatuan = $value['harga_tariftindakan'];
                            $modelTindakanKomponen->tarif_tindakankomp = $value['harga_tariftindakan'];
                            $modelTindakanKomponen->tarifcyto_tindakankomp = 0;
                            $modelTindakanKomponen->subsidiasuransikomp = 0;
                            $modelTindakanKomponen->subsidipemerintahkomp = 0;
                            $modelTindakanKomponen->subsidirumahsakitkomp = 0;
                            $modelTindakanKomponen->iurbiayakomp = 0;
                            $modelTindakanKomponen->save(false);
                        }
                    }
                }

                $transaction->commit();
                return ['message' => 'Data Berhasil di simpan'];

            }

        } catch (\yii\db\Exception $e) {

        }
    }

    public function actionSavePendaftaran($params)
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $post = $request->post();
        try {
            $form_pasien = !empty($post['PasienForm']) ? $post['PasienForm'] : [];
            $data_kunjungan = !empty($post['KunjunganForm']) ? $post['KunjunganForm'] : [];
            $data_admisi = !empty($post['PasienAdmisiForm']) ? $post['PasienAdmisiForm'] : []; // ranap
            $data_tarif = !empty($post['TarifKarcis']) ? $post['TarifKarcis'] : [];
            $data_pasien = !empty($post['DataPasien']) ? $post['DataPasien'] : [];
            $data_lab = !empty($post['periksalab']) ? json_decode($post['periksalab'], true) : [];
            $data_pj = !empty($post['PenanggungJawabForm']) ? $post['PenanggungJawabForm'] : [];

            if (empty($data_pasien['pasien_id'])) {
                $pasien_baru = $this->savePasien($form_pasien);
                if(isset($pasien_baru['status'])) {
                    return $pasien_baru;
                }
                else {
                    $data_pasien['pasien_id'] = $pasien_baru['pasien_id']; 
                    $data_pasien['no_rekam_medik'] = $pasien_baru['no_rekam_medik']; 
                    $data_pasien['nama_pasien'] = $pasien_baru['nama_pasien'];
                }
            }

            $penjamin_id = isset($data_kunjungan['penjamin_id']) ? $data_kunjungan['penjamin_id'] : null;
            $isPasienAktif = $this->checkPasienAktif($data_kunjungan, $data_pasien);
            if (!$isPasienAktif) {
                $data_pj['pasien_id'] = $data_pasien['pasien_id'];
                $savePj = $this->savePj($data_pj);
                if(isset($savePj['status'])) {
                    return $savePj;
                }
                else {
                    $data_kunjungan['penanggungjawab_id'] = $savePj;
                    $data_kunjungan['pasien_id'] = $data_pasien['pasien_id'];
                    $data_kunjungan['antrian_id'] = $data_pasien['antrian_id'];
                    $data_kunjungan['is_aps'] = $data_pasien['is_aps'];
                    $data_kunjungan['caramasuk_id'] = DocoConstants::VAR_CM_RAJAL;
                }
            } 

            $saveKunjungan = $this->saveKunjungan($data_kunjungan, $data_pasien, $data_tarif);
            if(isset($saveKunjungan['status'])) {
                return $saveKunjungan;
            }
            else {
                $carabayar_id = $saveKunjungan->carabayar_id;
                $data_kunjungan['pendaftaran_id'] = $saveKunjungan->getPrimaryKey();
                $data_kunjungan['kunjungan'] = $saveKunjungan->kunjungan;
                $data_kunjungan['status_pasien'] = $saveKunjungan->status_pasien;
            }

            $save_antrian = $this->updateAntrianPendaftaran($data_kunjungan, $data_pasien, $data_tarif);
            if(!$save_antrian){
                throw new \yii\base\ErrorException("Simpan Antrian Gagal", 500);
            }

            $save_pasienpenunjang = $this->savePasienPenunjang($data_kunjungan);
            $data_kunjungan['pasienmasukpenunjang_id'] = $save_pasienpenunjang->getPrimaryKey();

            $saveTindakanPelayanan = $this->saveTindakanPelayanan($data_tarif, $data_kunjungan, $data_lab);

            $pasien_id = $data_pasien['pasien_id'];
            $no_rekam_medik = $data_pasien['no_rekam_medik'];
            $nama_pasien = $data_pasien['nama_pasien'];
            $pendaftaran_id = $data_kunjungan['pendaftaran_id'];
            $asalrujukan_id = $data_kunjungan['asalrujukan_id'];
            $carabayar_id = $carabayar_id;

            $responseMessage = [
                'message' => 'Simpan data berhasil!',
                'pendaftaran_id' => DocoHelpers::encrypt($data_kunjungan['pendaftaran_id']),
                'no_rekam_medik' => $no_rekam_medik,
                'nama_pasien' => $nama_pasien,
                'carabayar_id' => $carabayar_id,
                'asalrujukan_id' => $asalrujukan_id,
                'pasien_id' => $pasien_id,
                'penjamin_id' => $penjamin_id,
            ];

            $transaction->commit();

            // Integrasi Akunting
            IntegrasiAkunting::integrateKarcisPasien($data_kunjungan['pendaftaran_id']);
            return $responseMessage;
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 422;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 422;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\yii\base\ErrorException $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 422;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionSavePendaftaranBayi($params)
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $post = $request->post();
        // return $post;
        try {
            $data_kunjungan = !empty($post['KunjunganForm']) ? $post['KunjunganForm'] : [];
            $data_admisi = !empty($post['PasienAdmisiForm']) ? $post['PasienAdmisiForm'] : [];
            $data_tarif = !empty($post['TarifKarcis']) ? $post['TarifKarcis'] : [];
            $data_pasien = !empty($post['DataPasien']) ? $post['DataPasien'] : [];
            $data_pj = !empty($post['PenanggungJawabForm']) ? $post['PenanggungJawabForm'] : [];
            // $data_rujukan = !empty($post['RujukanForm']) ? $post['RujukanForm'] : [];
            // $data_asuransi = !empty($post['AsuransiForm']) ? $post['AsuransiForm'] : [];
            $dataBayi = !empty($post['PasienForm']) ? $post['PasienForm'] : [];
            $data_lab = [];
            $dataPasien = [];
            $modelPasien = new Pasien;
            $modelPasien->attributes = $dataBayi;
            $tanggal_lahir = !empty($dataBayi['tanggal_lahir']) ? date('Y-m-d', strtotime($dataBayi['tanggal_lahir'])) : date('Y-m-d');
            $statusperkawinan = isset($dataBayi['statusperkawinan']) ? $dataBayi['statusperkawinan'] : 9999;

            $modelPasien->tgl_rekam_medik = date('Y-m-d');
            $modelPasien->tanggal_lahir = $tanggal_lahir;
            $modelPasien->statusperkawinan = $statusperkawinan;
            $modelPasien->golonganumur_id = 1;
            $modelPasien->statusrekammedis = DocoConstants::STAT_RM_AKTIF;
            $modelPasien->namadepan = DocoConstants::NAMA_BIN_BAYI;
            $modelPasien->nama_panggilan = $dataBayi['namadepan'];

            if(!$modelPasien->validate()){
                $error = "Gagal Validasi";
                if ($modelPasien->hasErrors()) {
                    $error = implode(' ', array_map(function ($errors) {
                        return implode(' ', $errors);
                    }, $modelPasien->getErrors()));
                }
                throw new \yii\base\ErrorException($error, 500);
            }
            else {
                $modelPasien->save();
                $pasien_id = $modelPasien->pasien_id;
            }


            $modelPendaftaran = new Pendaftaran;
            $modelPendaftaran->attributes = $data_admisi;
            $modelPendaftaran->tgl_pendaftaran = date('Y-m-d H:i:s');
            $modelPendaftaran->pasien_id = $pasien_id;
            $modelPendaftaran->status_periksa = DocoConstants::VAR_SP_AD;
            $modelPendaftaran->status_pasien = DocoConstants::VAR_PAS_B;
            $modelPendaftaran->kunjungan = DocoConstants::VAR_K_B;
            $modelPendaftaran->status_masuk = DocoConstants::VAR_KUN_SM;
            $modelPendaftaran->statusdok_rekammedik = DocoConstants::STAT_RM_AKTIF;
            $modelPendaftaran->golonganumur_id = 1;

            if(!$modelPendaftaran->validate()){
                $error = "Gagal Validasi";
                if ($modelPendaftaran->hasErrors()) {
                    $error = implode(' ', array_map(function ($errors) {
                        return implode(' ', $errors);
                    }, $modelPendaftaran->getErrors()));
                }
                throw new \yii\base\ErrorException($error, 500);
            }
            else {
                $modelPendaftaran->save();
                $pendaftaran_id = $modelPendaftaran->pendaftaran_id;
                $carabayar_id = $modelPendaftaran->carabayar_id;

            }

            if($post['HiddenPenanggungJawab'] == 1) {
                $data_pj['pasien_id'] = $pasien_id;
                $savePj = $this->savePjBayi($data_pj);
                if(!$savePj){
                    throw new \yii\base\ErrorException("Simpan Penanggung Jawab Gagal", 500);
                }
            }

            $asalrujukan_id = $data_admisi['asalrujukan_id'];
            $pendaftaranibu_id = $data_admisi['pendaftaran_id'];

            $pendaftaran = [];
            $data_admisi['pendaftaran_id'] = $pendaftaran_id;
            $data_admisi['tgl_pendaftaran'] = $modelPendaftaran->tgl_pendaftaran;
            $data_admisi['kunjungan'] = $modelPendaftaran->kunjungan;
            $data_admisi['bookingkamar_id'] = $data_admisi['bookingkamar_id'];
            $data_admisi['kamarruangan_id'] = $data_admisi['kamarruangan_id'];
            $data_admisi['kamartempattidur_id'] = $data_admisi['kamartempattidur_id'];
            $data_admisi['jk'] = $modelPasien->jeniskelamin;
            // $data_admisi['bpjs_id'] = @$data_admisi['bpjs_id'];
            $data_admisi['pasien_id'] = $pasien_id;
            $saveKunjunganRanap = $this->saveKunjunganRanap($data_admisi);

            if ($saveKunjunganRanap) {
                $pasienadmisi_id = $saveKunjunganRanap->getPrimaryKey();
                $this->updatePendaftaranBayi($pendaftaran_id, $pendaftaranibu_id, $pasienadmisi_id);
                $this->updateStatusKamar($data_admisi['kamartempattidur_id'], $modelPasien->jeniskelamin);
                $this->updateKelahiranBayi($data_admisi['kelahiranbayi_id'],$pendaftaran_id);
                $this->saveRujukan($data_admisi);

                $data_kunjungan = $data_admisi;
                $data_kunjungan['tgl_pendaftaran'] = $data_admisi['tgl_admisi'];
                $data_kunjungan['dokter_id'] = $saveKunjunganRanap['pegawai_id'];
            } else {
                throw new \yii\base\ErrorException("Simpan Rawat Inap Gagal", 500);
            }

            // return $data_kunjungan;
            $dataPendaftaranBaru = pendaftaran::findOne($pendaftaran_id);
            $dataPasienBaru = Pasien::findOne($dataPendaftaranBaru->pasien_id);

            $saveTindakanPelayanan = $this->saveTindakanPelayanan($data_tarif, $data_kunjungan, $data_lab);
            $responseMessage = [
                'message' => 'Simpan data berhasil!',
                'pendaftaran_id' => $pendaftaran_id,
                'no_rekam_medik' => $dataPasienBaru->no_rekam_medik,
                'nama_pasien' => $dataPasienBaru->nama_pasien,
                'carabayar_id' => $carabayar_id,
                'asalrujukan_id' => $asalrujukan_id,
            ];

            $transaction->commit();
            $mSyncPendaftaran = SyncPendaftaran::find()->where(['pendaftaran_id'=>$pendaftaran_id])->asArray()->one();
            if(!is_null($mSyncPendaftaran)){
                $responseMessage['sync'] = 'true';
                $sync = $this->sinkronKunjungan($mSyncPendaftaran);
                if($sync[0] == false){
                    $responseMessage['errorSync'] = $sync[1];
                    if(!$this->saveTempSinkron($mSyncPendaftaran)){
                        $responseMessage['errorMessage'] = 'Gagal Menyimpan Data Sinkronisasi';
                    }
                }
            } else {
                $responseMessage['sync'] = 'false';
            }

            return $responseMessage;
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 422;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 422;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\yii\base\ErrorException $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 422;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    private function checkPasienAktif($data, $pasien) {
        // cek instalasi igd

        $except_ids = [ // deprecated
            DocoConstants::STATUS_PERIKSA_PLG, // 4
            DocoConstants::STATUS_PERIKSA_RJK_RNP, // 433
        ];
        $start = date('Y-m-d') . ' 00:00:00';
        $end = date('Y-m-d') . ' 23:59:59';
        $model = InfoKunjunganRsView::find()
            ->andWhere(['pasien_id' => $pasien['pasien_id']])
            ->andWhere(['between', 'tgl_pendaftaran', $start, $end]);

        if ($data['instalasi_id'] == DocoConstants::INST_ID_RJ) {
            $model = $model->asArray()->all();
            foreach ($model as $datum) {
                // cek kunjungan masih aktif
                if (!in_array($datum['status_periksa_id'], $except_ids)) {
                    // cek apakah dia sudah pulang ranap / belum
                    if ($datum['pasienadmisi_id'] && !$datum['pulang_ri']) {
                        return [
                            'message'=>'Pasien masih dirawat inap.'
                        ];
                        // break
                    } else if (!$datum['pulang_rj_rd']) {
                        return [
                            'message'=>'Pasien masih ada di hari yang sama.'
                        ];
                        // break
                    }
                } elseif ($datum['status_periksa_id'] == DocoConstants::STATUS_PERIKSA_RJK_RNP) {
                    if (!$datum['is_ranap']) {
                        return [
                            'message'=>'Pasien masih ada di hari yang sama.'
                        ];
                        // break
                    } elseif (!($datum['pulang_ri'])) {
                        return [
                            'message'=>'Pasien masih dirawat inap.'
                        ];
                        // break
                    } else {
                        continue;
                    }
                }

                // cek jaminan bpjs di hari yang sama
                if ($datum['carabayar_id'] == DocoConstants::CARA_BAYAR_BPJS && $datum['carabayar_id'] == $data['carabayar_id']) {
                    return [
                        'message'=>'Pasien menggunakan jaminan BPJS di hari yang sama.'
                    ];
                    // break
                }
            }
        } elseif ($data['instalasi_id'] == DocoConstants::INST_ID_RD) {
            $model = $model->asArray()->all();
            // cek kunjungan masih aktif
            foreach ($model as $datum) {
                if ($datum['status_periksa_id'] != DocoConstants::STATUS_PERIKSA_PLG) {
                    if ($datum['status_periksa_id'] == DocoConstants::STATUS_PERIKSA_RJK_RNP) {
                        // cek apakah dia sudah pulang ranap / belum
                        if (!$datum['pulang_ri']) {
                            return [
                                'message'=>'Pasien masih dirawat inap.'
                            ];
                            // break
                        } else {
                            // return false;
                            continue;
                        }
                    } else {
                        return [
                            'message'=>'Pasien masih ada di hari yang sama.'
                        ];
                    }
                }
            }
        } elseif ($data['instalasi_id'] == DocoConstants::INST_ID_RI) {

        } else {
            $model = $model->asArray()->all();
            foreach ($model as $datum) {
                // cek kunjungan masih aktif
                if (!in_array($datum['status_periksa_id'], $except_ids)) {
                    // cek apakah dia sudah pulang ranap / belum
                    if ($datum['pasienadmisi_id'] && !$datum['pulang_ri']) {
                        return [
                            'message'=>'Pasien masih dirawat inap.'
                        ];
                        // break
                    } else if (!$datum['pulang_rj_rd']) {
                        return [
                            'message'=>'Pasien masih ada di hari yang sama.'
                        ];
                        // break
                    }
                } elseif ($datum['status_periksa_id'] == DocoConstants::STATUS_PERIKSA_RJK_RNP) {
                    if (!$datum['is_ranap']) {
                        return [
                            'message'=>'Pasien masih ada di hari yang sama.'
                        ];
                        // break
                    } elseif (!($datum['pulang_ri'])) {
                        return [
                            'message'=>'Pasien masih dirawat inap.'
                        ];
                        // break
                    } else {
                        continue;
                    }
                }

                // cek jaminan bpjs di hari yang sama
                if ($datum['carabayar_id'] == DocoConstants::CARA_BAYAR_BPJS && $datum['carabayar_id'] == $data['carabayar_id']) {
                    return [
                        'message'=>'Pasien menggunakan jaminan BPJS di hari yang sama.'
                    ];
                    // break
                }
            }
        }

        return false;
    }


    /*
    * author: Rizqi Febian
    * created date: 19-04-2018
    * desc: partial save rujukan
    * params needed: Array data
    * return: rujukan_id
    */
    public function actionSaveRujukan($params){
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            if($params == 'rujukan'){
                $post = $request->post();
                $post = $post['RujukanForm'];
                $save = $this->saveRujukan($post);
                $transaction->commit();
                return $save;
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 422;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 422;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
    /*
    * author: Rizqi Febian
    * created date: 17-04-2018
    * desc: partial save penanggungjawab_t
    * params needed: Array data
    * return: penanggungjawab_id
    */
    public function savePj($data){
        $model = new PenanggungJawab();
        $model->pengantar = !empty($data['pengantar']) ? $data['pengantar'] : null;
        $model->jenisidentitas = !empty($data['jenisidentitas']) ? $data['jenisidentitas'] : null;
        $model->no_identitas = !empty($data['no_identitas']) ? $data['no_identitas'] : null;
        $model->penanggungjawab_nama = !empty($data['penanggungjawab_nama']) ? $data['penanggungjawab_nama'] : null;
        $model->penanggungjawab_tempatlahir = !empty($data['penanggungjawab_tempatlahir']) ? $data['penanggungjawab_tempatlahir'] : null;
        $model->penanggungjawab_tgllahir = !empty($data['penanggungjawab_tgllahir']) ? $data['penanggungjawab_tgllahir'] : null;
        $model->penanggungjawab_jeniskelamin = !empty($data['penanggungjawab_jeniskelamin']) ? $data['penanggungjawab_jeniskelamin'] : null;
        $model->hubungankeluarga = !empty($data['hubungankeluarga']) ? $data['hubungankeluarga'] : null;
        $model->penanggungjawab_alamat = !empty($data['penanggungjawab_alamat']) ? $data['penanggungjawab_alamat'] : null;
        $model->penanggungjawab_notelp = !empty($data['penanggungjawab_notelp']) ? $data['penanggungjawab_notelp'] : null;
        $model->pasien_id = !empty($data['pasien_id']) ? $data['pasien_id'] : null;

        if ($model->validate() && $model->save()) {

            return $model->penanggungjawab_id;
        }
        else {
            return [
                'status' => 422,
                'data' => $model->errors,
                'model' => 'PenanggungJawabForm'
            ];
        }
    }

    /*
    * author: Budi
    * created date: 2-07-2019
    * desc: partial save penanggungjawab_t
    * params needed: Array data
    * return: penanggungjawab_id
    */
    public function savePjBayi($data){
        $model = new PenanggungJawab();
        $model->pengantar = !empty($data['pengantar']) ? $data['pengantar'] : null;
        $model->jenisidentitas = !empty($data['jenisidentitas']) ? $data['jenisidentitas'] : null;
        $model->no_identitas = !empty($data['no_identitas']) ? $data['no_identitas'] : null;
        $model->penanggungjawab_nama = !empty($data['penanggungjawab_nama']) ? $data['penanggungjawab_nama'] : null;
        $model->penanggungjawab_tempatlahir = !empty($data['penanggungjawab_tempatlahir']) ? $data['penanggungjawab_tempatlahir'] : null;
        $model->penanggungjawab_tgllahir = !empty($data['penanggungjawab_tgllahir']) ? $data['penanggungjawab_tgllahir'] : null;
        $model->penanggungjawab_jeniskelamin = !empty($data['penanggungjawab_jeniskelamin']) ? $data['penanggungjawab_jeniskelamin'] : null;
        $model->hubungankeluarga = !empty($data['hubungankeluarga']) ? $data['hubungankeluarga'] : null;
        $model->penanggungjawab_alamat = !empty($data['penanggungjawab_alamat']) ? $data['penanggungjawab_alamat'] : null;
        $model->penanggungjawab_notelp = !empty($data['penanggungjawab_notelp']) ? $data['penanggungjawab_notelp'] : null;
        $model->pasien_id = !empty($data['pasien_id']) ? $data['pasien_id'] : null;

        if(!$model->validate()){
            throw new \yii\base\ErrorException("Gagal Validasi Penanggung Jawab", 500);
        }
        if(!$model->save()){
            throw new \yii\base\ErrorException("Gagal Simpan Penanggung Jawab", 500);
        }

        return !empty($model->getPrimaryKey()) ? $model->getPrimaryKey() : null;
    }

    /*
    * author: Rizqi Febian
    * created date: 18-04-2018
    * desc: partial save pendaftaran_t
    * params needed: Array data
    * return: pendaftaran_id
    */
    public function saveKunjungan($data, $data_pasien, $data_tarif, $isranap=false)
    {
        $model = new Pendaftaran();
        $model->attributes = $data;
        $model->keterangan_pendaftaran = isset($data['keterangan_pendaftaran']) ? $data['keterangan_pendaftaran'] : '';
        $model->status_periksa = $isranap ? "2" : "1";
        if($model->penjamin_id == DocoConstants::VAR_P_P){ // penjamin perorangan
            if ($data['instalasi_id'] == DocoConstants::INST_ID_RD) {
                $model->status_periksa = DocoConstants::VAR_SP_BP;
                // is karcis
                $model->is_karcis = false;
            } else {
                if ($data_tarif) {
                    $model->status_periksa = DocoConstants::VAR_SP_AK; // status periksa antrian kasir
                    // $model->status_bayar = DocoConstants::BELUM_LUNAS; // set status jadi tidak lunas
                } else {
                    $model->status_periksa = DocoConstants::VAR_SP_AP; // status periksa antrian poli
                    // is karcis
                    if(!in_array($data['instalasi_id'], DocoConstants::$exceptPenunjang)){
                        $model->is_karcis = true;
                    } elseif ($data['instalasi_id'] == DocoConstants::INST_ID_RD) {
                        $model->is_karcis = false;
                    }
                }
            }
        }else{
            // is karcis
            $model->is_karcis = false;
            if(!in_array($data['instalasi_id'], DocoConstants::$exceptPenunjang)){
                $model->status_periksa = DocoConstants::VAR_SP_PEN; //status periksa penunjang
            } elseif ($data['instalasi_id'] == DocoConstants::INST_ID_RD) {
                $model->status_periksa = DocoConstants::VAR_SP_BP;
            }
        }
        $model->status_masuk = DocoConstants::VAR_SM_R;
        if(empty($model->rujukan_id)) {
            $model->status_masuk = DocoConstants::VAR_SM_NR;
        }
        $model->status_pasien = !empty($data_pasien['pasien_id']) ? DocoConstants::VAR_PAS_L : DocoConstants::VAR_PAS_B;
        $model->kunjungan = DocoConstants::VAR_K_L;
        if(isset($data['pendaftaranibu_id'])) {
            $model->pendaftaranibu_id = $data['pendaftaranibu_id'];
        }

        $tanggalLahir = isset($data_pasien['tanggal_lahir']) ? $data_pasien['tanggal_lahir'] : date('Y-m-d');

        $model->umur = isset($data_pasien['umur']) ? $data_pasien['umur'] : 0;
        $model->golonganumur_id = (DocoHelpers::getGolonganUmurPasien($tanggalLahir)) ? DocoHelpers::getGolonganUmurPasien($tanggalLahir)->golonganumur_id : 1;
        $jwt = Yii::$app->jwt->user;
        $by = !empty($jwt->loginpemakai_id) ? $jwt->loginpemakai_id : '1';
        $model->created_by = $by;
        $model->is_aps = ($model->is_aps == 'true') ? true : false;
        // is_ranap
        $model->is_ranap = $isranap;

        if (isset($data['dokter_id'])) {
            $model->pegawai_id  = $data['dokter_id'];
        }

        if($model->validate() && $model->save()) {
            return $model;
        }
        else {
            return [
                'status' => 422,
                'data' => $model->errors,
                'model' => 'KunjunganForm'
            ];
        }
    }

    public function saveReservasiPoli($data, $pendaftaran_id)
    {
        $model = PendaftaranOnline::find()
            ->andWhere(['pendaftaranol_id'=>$data['pendaftaranol_id']])
            ->one();
        if ($model) {
            $model->pendaftaran_id = $pendaftaran_id;
            $model->status_daftar_ol = DocoConstants::VAR_STATUS_DAFTAR_OL_DISETUJUI;
            $model->save(false);
        }
        return true;
    }


    /*
    * author: Rizal Faidin
    * created date: 06-06-2018
    * desc: partial save pasienadmisi_t
    * params needed: Array data
    * return: pendaftaran_id
    */
    public function saveKunjunganRanap($data)
    {
            $model = new PasienAdmisi();
            $model->attributes = $data;
            // return $model;
            if(!$model->validate()){
                $error = "Gagal Validasi Kunjungan Ranap";
                if ($model->hasErrors()) {
                    $error = implode(' ', array_map(function ($errors) {
                        return implode(' ', $errors);
                    }, $model->getErrors()));
                }
                throw new \yii\base\ErrorException($error, 500);
            }
            if(!$model->save()){
                $error = "Gagal Simpan Kunjungan Ranap";
                if ($model->hasErrors()) {
                    $error = implode(' ', array_map(function ($errors) {
                        return implode(' ', $errors);
                    }, $model->getErrors()));
                }
                throw new \yii\base\ErrorException($error, 500);
            }

            // add rizal masuk kamar
            $masukkamar = new MasukKamar();
            $masukkamar->pasienadmisi_id = $model->pasienadmisi_id;
            $masukkamar->ruangan_id = $data['ruangan_id'];
            $masukkamar->carabayar_id = $data['carabayar_id'];
            $masukkamar->penjamin_id = $data['penjamin_id'];
            $masukkamar->pegawai_id = $data['pegawai_id'];
            $masukkamar->kelaspelayanan_id = $data['kelaspelayanan_id'];
            $masukkamar->kamartempattidur_id = $data['kamartempattidur_id'];
            $masukkamar->kamarruangan_id = $data['kamarruangan_id'];
            $masukkamar->tgl_masukkamar = $data['tgl_admisi'];
            $masukkamar->jam_masukkamar = date('H:i:s', strtotime($data['tgl_admisi']));
            if(!$masukkamar->validate()){
                $error = "Gagal Validasi Masuk Kamar";
                if ($masukkamar->hasErrors()) {
                    $error = implode(' ', array_map(function ($errors) {
                        return implode(' ', $errors);
                    }, $masukkamar->getErrors()));
                }
                throw new \yii\base\ErrorException($error, 500);
            }
            if(!$masukkamar->save()){
                $error = "Gagal Simpan Masuk Kamar";
                if ($masukkamar->hasErrors()) {
                    $error = implode(' ', array_map(function ($errors) {
                        return implode(' ', $errors);
                    }, $masukkamar->getErrors()));
                }
                throw new \yii\base\ErrorException($error, 500);
            }
            // end masuk kamar

            if (!empty($data['bookingkamar_id'])) {
                $transaksi_kamar = TraPemesananKamar::findOne($data['bookingkamar_id']);
                if ($transaksi_kamar && !empty($transaksi_kamar)) {
                    $transaksi_kamar->status_booking = DocoConstants::VAR_STJ;
                    if (!$transaksi_kamar->save()) {
                        $errors = DocoHelpers::parseError($transaksi_kamar->errors,'TraPemesananKamar');
                        return [
                            'data' => $errors,
                            'status' => 422
                        ];
                    }
                }
            }
            return $model;

    }

    public function savePasienBayi($data)
    {
            $model = new Pasien();
            $model->attributes = $data;

            if(!$model->validate()){
                $error = "Gagal Validasi";
                if ($model->hasErrors()) {
                    $error = implode(' ', array_map(function ($errors) {
                        return implode(' ', $errors);
                    }, $model->getErrors()));
                }
                throw new \yii\base\ErrorException($error, 500);
            }
            if(!$model->save()){
                $error = "Gagal Simpan";
                if ($model->hasErrors()) {
                    $error = implode(' ', array_map(function ($errors) {
                        return implode(' ', $errors);
                    }, $model->getErrors()));
                }
                throw new \yii\base\ErrorException($error, 500);
            }

            return $model;

    }

    /*
    * author: Rizal Faidin
    * created date: 11-06-2018
    * desc: update pasienadmisi_id in pendaftaran_t
    * params needed: pendaftaran_id, integer pasienadmisi_id
    * return: pendaftaran_id
    */
    public function updatePendaftaranAdmisi($pendaftaran_id, $pasienadmisi_id)
    {
            $model = Pendaftaran::findOne($pendaftaran_id);
            $model->pasienadmisi_id = $pasienadmisi_id;
            if(!$model->save(false)){
                throw new \yii\base\ErrorException("Gagal Ubah Pendaftaran Admisi", 500);
            }
            return $model->getPrimaryKey();
    }

    /*
    * author: Budi
    * created date: 02-07-2019
    * desc: update pendaftaranibu_id in pendaftaran_t
    * params needed: pendaftaranibu_id
    * return: pendaftaranibu_id
    */
    public function updatePendaftaranBayi($pendaftaran_id, $pendaftaranibu_id, $pasienadmisi_id)
    {
            $model = Pendaftaran::findOne($pendaftaran_id);
            $model->pendaftaranibu_id = $pendaftaranibu_id;
            $model->pasienadmisi_id = $pasienadmisi_id;
            if(!$model->save(false)){
                throw new \yii\base\ErrorException("Gagal Ubah Pendaftaran", 500);
            }
            return $model->getPrimaryKey();
    }

    /*
    * author: Budi
    * created date: 08-07-2019
    * desc: update bpjs_id in pendaftaran_t
    * params needed: bpjs_id
    * return: bpjs_id
    */
    public function updateAdmisiBayi($bpjs_id, $pasienadmisi_id)
    {
            $model = PasienAdmisi::findOne($pasienadmisi_id);
            $model->bpjs_id = $bpjs_id;
            if(!$model->save(false)){
                throw new \yii\base\ErrorException("Gagal Ubah Admisi", 500);
            }
            return $model->getPrimaryKey();
    }

    /*
    * author: Budi
    * created date: 28-05-2019
    * desc: update pendaftaranibu_id in kelahiranbayi_t
    * params needed: pendaftaran_id, integer kelahiranbayi_id
    * return: pendaftaran_id
    */
    public function updateKelahiranBayi($kelahiranbayi_id, $pendaftaran_id)
    {
            $model = KelahiranBayi::findOne($kelahiranbayi_id);
            $model->pendaftaranbaru_id = $pendaftaran_id;
            if(!$model->save(false)){
                throw new \yii\base\ErrorException("Gagal Ubah Kelahiran Bayi", 500);
            }
            return $model->getPrimaryKey();
    }

    public function saveRujukanBpjs($bpjs_id)
    {
        $bpjs = BpjsView::find()
            ->andWhere(['bpjs_id'=>$bpjs_id])
            ->asArray()
            ->one();
        if ($bpjs) {
            $data = [];
            // $rujukandari_id = null;
            // if ($bpjs['asal_rujukan']) {
            $rujukandari_id = $bpjs['asal_rujukan'] == 2 ? 38 : 37;
            // }
            $diagnosa = DiagnosaView::find()
                ->andWhere(['diagnosa_kode' => $bpjs['diagnosaawal']])
                ->asArray()->one();

            $additionals = json_decode($bpjs['additional_data'], true);
            $nama_perujuk = isset($additionals['sep']['peserta']['nama']) ? $additionals['sep']['peserta']['nama'] : '';

            $data['asalrujukan_id'] = 27;
            $data['rujukandari_id'] = $rujukandari_id;
            $data['diagnosa_id'] = $diagnosa ? $diagnosa['diagnosa_id'] : null;
            $data['nama_perujuk'] = $nama_perujuk;
            $data['no_rujukan'] = $bpjs['norujukan'];
            $data['tanggal_rujukan'] = $bpjs['tglrujukan'];

            return $this->saveRujukan($data);
        } else {
            return true;
        }
    }

    /*
    * author: Rizqi Febian
    * created date: 18-04-2018
    * desc: partial save rujukan_t
    * params needed: Array data
    * return: rujukan_id
    */
    public function saveRujukan($data)
    {
        $model = new Rujukan();
        $model->attributes = $data;
        $model->asalrujukan_id = isset($data['asalrujukan_id']) ? $data['asalrujukan_id'] : null;
        $model->rujukandari_id = isset($data['rujukandari_id']) ? $data['rujukandari_id'] : 2;
        $model->diagnosa_id = isset($data['diagnosa_id']) ? $data['diagnosa_id'] : null;
        $model->nama_perujuk = isset($data['nama_perujuk']) ? $data['nama_perujuk'] : null;
        $model->no_rujukan = isset($data['no_rujukan']) ? $data['no_rujukan'] : '-';
        $model->tanggal_rujukan = isset($data['tanggal_rujukan']) ? $data['tanggal_rujukan'] : null;

        if(!$model->validate()){
            throw new \yii\base\ErrorException("Gagal Validasi Rujukan", 422);
        }
        if(!$model->save()){
            throw new \yii\base\ErrorException("Gagal Simpan Rujukan", 422);
        }
        return $model->getPrimaryKey();
    }
    /*
    * author: Rizqi Febian
    * created date: 19-04-2018
    * desc: partial save tindakanpelayanan_t
    * params needed: Array data tindakan pelayanan, data pasien
    * return: tindakanpelayanan_id
    */
    public function saveTindakanPelayanan($data, $dataPasien, $datalab = []) {
        $arrLab = [];
        $arrLab['tindakanhead'] = [];
        $arrLab['komponen'] = [];
        if(count($datalab) > 0){
            $arrLab = $this->populateLab($datalab, $dataPasien);
        }
        $komponen = [];
        $data_tindakanpelayanan = [];
        $i = 0;
        $y = 0;
        $primary_column = [];
        foreach ($data as $key => $value) {
            $newData = [];
            if(count($primary_column) == 0){
                $primary_column = ['pasien_id'=>$dataPasien['pasien_id'],'pendaftaran_id'=>$dataPasien['pendaftaran_id']];
            }
            $newData['kelaspelayanan_id'] = $dataPasien['kelaspelayanan_id'];
            $newData['pasien_id'] = $dataPasien['pasien_id'];
            $newData['instalasi_id'] = $dataPasien['instalasi_id'];
            $newData['carabayar_id'] = $dataPasien['carabayar_id'];
            $newData['pendaftaran_id'] = $dataPasien['pendaftaran_id'];
            $newData['jeniskasuspenyakit_id'] = $dataPasien['jeniskasuspenyakit_id'];
            $newData['ruangan_id'] = $dataPasien['ruangan_id'];
            $newData['penjamin_id'] = $dataPasien['penjamin_id'];
            $newData['tgl_tindakan'] = date('Y-m-d H:i:s', strtotime($dataPasien['tgl_pendaftaran']));
            $newData['tarif_satuan'] = $data[$key]['harga_tariftindakan'];
            $newData['qty_tindakan'] = 1;
            $newData['tarif_tindakan'] = $data[$key]['harga_tariftindakan']*$newData['qty_tindakan'];
            $newData['tarifcyto_tindakan'] = 0;
            $newData['cyto_tindakan'] = false;
            $newData['discount_tindakan'] = 0;
            $newData['satuan_tindakan'] = DocoConstants::VAR_ST;
            $newData['daftartindakan_id'] = isset($data[$key]['daftartindakan_id']) ? $data[$key]['daftartindakan_id'] : '';
            $newData['tipepaket_id'] = isset($data[$key]['tipepaket_id']) ? $data[$key]['tipepaket_id'] : '';
            $newData['dokterpenanggungjawab_id'] = isset($dataPasien['dokter_id']) ? $dataPasien['dokter_id'] : '';
            $newData['pasienmasukpenunjang_id'] = isset($dataPasien['pasienmasukpenunjang_id']) ? $dataPasien['pasienmasukpenunjang_id'] : '';
            $data_tindakanpelayanan[$i] = $newData;

            if (isset($data[$key]['komponen'])) {
                foreach (json_decode($data[$key]['komponen'], true) as $keyx => $valuex) {
                    $valuex['tindakanpelayanan_id'] = '';
                    $komponen[$y] = $valuex;
                    $y++;
                }
            }
            $i++;
        }
        if(count($arrLab['tindakanhead']) > 0){
            $data_tindakanpelayanan = array_merge($data_tindakanpelayanan, $arrLab['tindakanhead']);
        }
        try {
            $model = TindakanPelayanan::batchInsert($data_tindakanpelayanan, true);
        } catch (\yii\db\Exception $e) {
            throw new \yii\base\ErrorException("Gagal Validasi Tindakan Pelayanan", 500);
        }

        if ($komponen) {
            $save_komponen = $this->saveTindakanKomponen($komponen, $primary_column, $arrLab['komponen']);
            return $save_komponen;
        } else {
            return $model;
        }
    }
    /*
    * author: Rizqi Febian
    * created date: 19-04-2018
    * desc: partial save tindakankomponen_t
    * params needed: Array data
    * return: tindakanpelayanan_id
    */
    public function saveTindakanKomponen($komponen,$primary_column, $datalab = [])
    {
        $column = [];
        $select = TindakanPelayanan::find()->select(['tindakanpelayanan_id','daftartindakan_id','tipepaket_id'])->where($primary_column);
        $select = $select->asArray()->all();
        foreach ($select as $key => $value) {
            if(!empty($value['tipepaket_id'])){
                $primary = $value['tipepaket_id'];
            }else{
                if(!empty($value['daftartindakan_id'])){
                    $primary = $value['daftartindakan_id'];
                }
            }
            $column[$primary] = $value['tindakanpelayanan_id'];
        }
        if(count($datalab) > 0){
            $komponen = array_merge($komponen, $datalab);
        }
        foreach ($komponen as $key => $value) {
            if(!empty($value['tipepaket_id'])){
                $primary = $value['tipepaket_id'];
            }else{
                if(!empty($value['daftartindakan_id'])){
                    $primary = $value['daftartindakan_id'];
                }
            }
            $komponen[$key]['tindakanpelayanan_id'] = isset($column[$primary]) ? $column[$primary] : '';
            $komponen[$key]['tarif_kompsatuan'] = $komponen[$key]['harga_tariftindakan'];
            $komponen[$key]['tarif_tindakankomp'] = $komponen[$key]['harga_tariftindakan']*1;
            $komponen[$key]['tarifcyto_tindakankomp'] = 0;
            $komponen[$key]['subsidiasuransikomp'] = 0;
            $komponen[$key]['subsidipemerintahkomp'] = 0;
            $komponen[$key]['subsidirumahsakitkomp'] = 0;
            $komponen[$key]['iurbiayakomp'] = 0;
            unset($komponen[$key]['jenistarif_nama']);
            unset($komponen[$key]['harga_tariftindakan']);
        }
        $model = TindakanKomponen::batchInsert($komponen, true);
        return $model;
    }

    /*
    * DEPRECATED
    * author: Rizqi Febian
    * created date: 23-04-2018
    * desc: action save asuransipasien_m
    * params needed: Array data
    * return: rujukan_id,tindakanpelayanan_id
    */
    public function actionSaveAsuransi()
    {
        try {
            $post = $request->post();
            $post = json_decode($post['post'], true);
            $data = $post['AsuransiForm'];
            $data['diagnosa_id'] = '';
            $save_asuransi = $this->saveAsuransi($data);
            return $save_asuransi;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];

        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];

            $transaction->rollBack();
        }
    }


    /*
    * author: Rizqi Febian
    * created date: 23-04-2018
    * desc: partial save asuransipasien_m
    * params needed: Array data
    * return: rujukan_id,tindakanpelayanan_id
    */
    public function saveAsuransi($data)
    {
        if ($data && $data['carabayar_id'] == DocoConstants::PENJAMIN_ASURANSI) {
            $model = new AsuransiPasien();
            $query = $model->find()
                ->andWhere([
                    'pasien_id'=>$data['pasien_id'],
                    'penjamin_id'=>$data['penjamin_id'],
                ])
                ->orderBy('asuransipasien_id DESC')
                ->one();
            $query = $query ? : new AsuransiPasien;
            $query->attributes = $data;
           if(!$query->validate()){
                throw new \yii\base\ErrorException("Gagal Validasi Asuransi", 422);
            }
            if(!$query->save()){
                throw new \yii\base\ErrorException("Gagal Simpan Asuransi", 422);
            }
            return $query->getPrimaryKey();

        } else {
            return null;
        }
    }
    /*
    * author: Rizqi Febian
    * created date: 24-04-2018
    * desc: partial save and update antrian_t
    * params needed: Array data
    * return: true || false
    */
    public function updateAntrianPendaftaran($data, $dataPasien, $data_tarif){
        $model_antrian = new Antrian();
        $dataPasien['antrian_id'] = false;
        if(!$dataPasien['antrian_id']){
            // create antrian poli when without antrian pendaftaran
            $antrianPoli = new Antrian();
            $antrianPoli->no_antrian = 'x';
            $antrianPoli->pendaftaran_id = $data['pendaftaran_id'];
            $antrianPoli->pasien_id = $data['pasien_id'];
            $antrianPoli->carabayar_id = $data['carabayar_id'];
            $antrianPoli->ruangan_id = $data['ruangan_id'];
            $antrianPoli->tgl_antrian = date('Y-m-d H:i:s');
            if (isset($data['dokter_id'])) {
                $antrianPoli->pegawai_id = $data['dokter_id'];
            }
            $antrianPoli->status_pasien = DocoConstants::VAR_PAS_B;
            $antrianPoli->jenisantrian_id = DocoConstants::VAR_JA_PEN;
            if ($data['carabayar_id'] != DocoConstants::CB_PEN_UMUM || !$data_tarif) {
                $antrianPoli->is_active = true;
            } else {
                $antrianPoli->is_active = false;
            }
            if ($antrianPoli->save()) {
                return true;
            }
            $err = json_encode($antrianPoli->errors);
            throw new \yii\base\ErrorException($err, 500);
        }
        $data_antrian = $model_antrian::find()->where(['antrian_id'=>$dataPasien['antrian_id']])->one();
        $data_antrian->pendaftaran_id = $data['pendaftaran_id'];
        $data_antrian->pasien_id = $data['pasien_id'];
        if($data_antrian->save()){
            // set is_active antrian poli = true
            $antrianPoli = Antrian::find(true)
                ->where(['antrianasal_id'=>$data_antrian->antrian_id])
                ->one();
            $antrianPoli->pendaftaran_id = $data['pendaftaran_id'];
            $antrianPoli->pasien_id = $data['pasien_id'];
            $antrianPoli->ruangan_id = $data['ruangan_id'];
            $antrianPoli->carabayar_id = $data['carabayar_id'];
            if ($data['carabayar_id'] != DocoConstants::CB_PEN_UMUM || !$data_tarif) {
                $antrianPoli->is_active = true;
            }
            $antrianPoli->save();
            return true;
        }else{
            throw new \yii\base\Exception(json_encode($data_antrian->getErrors()),500);
        }
            // return $data_antrian;
    }

    /**
    * @author Rizal F. <rizal@docotel.com> DEPRECATED
    * @since 2018-06-25
    * @param array data data kunjungan
    * @return object or throw exception
    */
    public function saveAntrianKasirPoli($data, $data_tarif) {
        $antrian = new Antrian();
        $antrian->pasien_id = $data['pasien_id'];
        $antrian->ruangan_id = $data['ruangan_id'];
        $antrian->carabayar_id = $data['carabayar_id'];
        $antrian->pendaftaran_id = $data['pendaftaran_id'];
        $antrian->tgl_antrian = date('Y-m-d H:i:s');
        $antrian->no_antrian = '00';
        $antrian->pasien_id = $data['pasien_id'];
        $antrian->penjamin_id = $data['penjamin_id'];
        // $antrian->pegawai_id = $data['pegawai_id'];
        // $antrian->status_pasien = $data['status_pasien'];
        $antrian->pegawai_id = isset($data['pegawai_id']) ? $data['pegawai_id'] : isset($data['dokter_id']) ? $data['dokter_id'] : '';
        $antrian->status_pasien = $data['status_pasien'];
        $updateAntrian = false;
        $updatePenunjang = false;
        if ($data['carabayar_id'] == DocoConstants::CB_PEN_UMUM && $data_tarif) {
            $antrian->jenisantrian_id = DocoConstants::VAR_JA_KSR;//178;
        } else {
            $updateAntrian = true;
            if(!in_array($data['instalasi_id'], DocoConstants::$exceptPenunjang) && $data['carabayar_id'] != DocoConstants::CB_PEN_UMUM){
                $antrian->jenisantrian_id = DocoConstants::VAR_JA_PEN;//179;
                $updatePenunjang = true;
            }else if(!in_array($data['instalasi_id'], DocoConstants::$exceptPenunjang) && $data['carabayar_id'] == DocoConstants::CB_PEN_UMUM){
                $antrian->jenisantrian_id = DocoConstants::VAR_JA_KSR;//178;
                $updateAntrian = false;
            }else{
                $antrian->jenisantrian_id = DocoConstants::VAR_JA_P;//312;
            }
        }
        if($antrian->save()) {
            if ($updateAntrian) {
                // update antrian_id with antrian jenis antrian poli
                $pendaftaran = Pendaftaran::findOne($data['pendaftaran_id']);
                $pendaftaran->antrian_id = $antrian->antrian_id;
                $pendaftaran->save(false);
            }
            if($updatePenunjang){
                $pendaftaranPenunjang = PasienMasukPenunjang::findOne($data['pasienmasukpenunjang_id']);
                $getAntrian = Antrian::findOne($antrian->antrian_id);
                $pendaftaranPenunjang->no_antrian = $getAntrian->no_antrian;
                $pendaftaranPenunjang->save(false);
            }
            return $antrian;
        } else {
            throw new \yii\base\ErrorException(json_encode($antrian->getErrors()), 500);
        }
    }

    /**
    * @author Rizqi Febian
    * @since 2018-06-05
    * @param array $data_kunjungan
    * @return integer pasienmasukpenunjang_id
    */
    public function savePasienPenunjang($data_kunjungan)
    {
        $model = new PasienMasukPenunjang;
        $model->attributes = $data_kunjungan;
        $model->pegawai_id = $data_kunjungan['dokter_id'];
        $model->ruanganasal_id = $model->ruangan_id;
        $model->kunjungan = $data_kunjungan['kunjungan']; //sama kaya di pendaftaran_t
        $model->status_periksa = "";
        $model->is_bayar = false;
        if($data_kunjungan['penjamin_id'] != DocoConstants::VAR_P_P){
            $getAntrian = Antrian::find()->select([
                'no_antrian'
            ])->where([
                'pendaftaran_id' => $model->pendaftaran_id,
                'jenisantrian_id' => DocoConstants::VAR_JA_PEN
            ])->one();
            $model->status_periksa = DocoConstants::VAR_SL_BP;
            $model->no_antrian = $getAntrian->no_antrian;
            // $model->is_bayar = "true";
        }

        $model->tglmasukpenunjang = $data_kunjungan['tgl_pendaftaran'];
        $model->instalasiasal_id = $data_kunjungan['instalasi_id'];
        if($model->save()){
            return $model;
        }else{
            throw new \yii\base\ErrorException(json_encode($model->getErrors()), 500);
        }
    }

    /**
    * @author Rizqi Febian
    * @since 2018-06-05
    * @param array $data_lab //data rencana pemeriksaan lab
    * @return array tindakan header dan komponen
    */
    public function populateLab($datalab, $datapasien){
        $data_pasien = $datapasien;
        $result = [
            'tindakanhead'=>[],
            'komponen'=>[]
        ];
        if(count($datalab) > 0){
            $tindakanId = [];
            $conditions = [];
            foreach ($datalab as $key => $value) {
                $newData = [];
                $newData['daftartindakan_id'] = isset($value['daftartindakan_id']) ? $value['daftartindakan_id'] : '';
                $newData['tipepaket_id'] = isset($value['tipepaket_id']) ? $value['tipepaket_id'] : '';
                $tindakanId[] = $newData;
                // $conditions['ruangan_id'] = isset($value['ruangan_id']) ? $value['ruangan_id'] : '';
                $conditions['kelaspelayanan_id'] = isset($value['kelaspelayanan_id']) ? $value['kelaspelayanan_id'] : '';
                $conditions['penjamin_id'] = isset($value['penjamin_id']) ? $value['penjamin_id'] : '';
                if(isset($value['daftartindakan_id']) && !empty($value['daftartindakan_id'])){
                    $conditions['daftartindakan_id'][] = $value['daftartindakan_id'];
                }
                if(isset($value['tipepaket_id']) && !empty($value['tipepaket_id'])){
                    $conditions['tipepaket_id'][] = $value['tipepaket_id'];
                }
            }
            $listDaftarTindakan = isset($conditions['daftartindakan_id']) ? $conditions['daftartindakan_id'] : [];
            $listTipePaket = isset($conditions['tipepaket_id']) ? $conditions['tipepaket_id'] : [];
            if(isset($conditions['daftartindakan_id'])){
                unset( $conditions['daftartindakan_id'] );
            }
            if(isset($conditions['tipepaket_id'])){
                unset( $conditions['tipepaket_id'] );
            }

            $getKomponen = TarifTindakan::find()->where($conditions);
            if(!empty($listDaftarTindakan) && !empty($listTipePaket)){
                $getKomponen->andWhere(['or',['daftartindakan_id'=>$listDaftarTindakan], ['tipepaket_id'=>$listTipePaket] ]);
            }else if( !empty($listDaftarTindakan) ){
                $getKomponen->andWhere(['daftartindakan_id'=>$listDaftarTindakan]);
            }else if( !empty($listTipePaket) ){
                $getKomponen->andWhere(['tipepaket_id'=>$listTipePaket]);
            }
            $getKomponen->andWhere(['<>', 'komponentarif_id', DocoConstants::KOMPONEN_TARIF]);
            $getKomponen = $getKomponen->asArray()->all();
            foreach ($datalab as $key => $value) {
                $newData = [];
                $newData['kelaspelayanan_id'] = $data_pasien['kelaspelayanan_id'];
                $newData['pasien_id'] = $data_pasien['pasien_id'];
                $newData['instalasi_id'] = $data_pasien['instalasi_id'];
                $newData['carabayar_id'] = $data_pasien['carabayar_id'];
                $newData['pendaftaran_id'] = $data_pasien['pendaftaran_id'];
                $newData['jeniskasuspenyakit_id'] = $data_pasien['jeniskasuspenyakit_id'];
                $newData['ruangan_id'] = $data_pasien['ruangan_id'];
                $newData['penjamin_id'] = $data_pasien['penjamin_id'];
                $newData['tgl_tindakan'] = date('Y-m-d H:i:s', strtotime($data_pasien['tgl_pendaftaran']));
                $newData['tarif_satuan'] = $value['harga_tariftindakan'];
                $newData['qty_tindakan'] = 1;
                $newData['cyto_tindakan'] = isset($value['is_cyto']) ? ($value['is_cyto'] == 'true') ? true : false : false;
                $newData['tarifcyto_tindakan'] = 0;
                if($newData['cyto_tindakan']){
                    $persencyto = $newData['tarif_satuan'] * ($value['persencyto_tindakan'] / 100);
                    $newData['tarifcyto_tindakan'] = $persencyto;
                }
                $newData['tarif_tindakan'] = ($newData['tarif_satuan']*$newData['qty_tindakan']) + $newData['tarifcyto_tindakan'];
                $newData['discount_tindakan'] = "0";
                $newData['satuan_tindakan'] = DocoConstants::VAR_ST;
                $newData['daftartindakan_id'] = isset($value['daftartindakan_id']) ? $value['daftartindakan_id'] : '';
                $newData['tipepaket_id'] = isset($value['tipepaket_id']) ? $value['tipepaket_id'] : '';
                $newData['dokterpenanggungjawab_id'] = $data_pasien['dokter_id'];
                $newData['pasienmasukpenunjang_id'] = isset($data_pasien['pasienmasukpenunjang_id']) ? $data_pasien['pasienmasukpenunjang_id'] : '';
                $result['tindakanhead'][] = $newData;
            }
            if(count($getKomponen) > 0){
                foreach ($getKomponen as $key => $value) {
                    $newData = [];
                    $newData['harga_tariftindakan'] = $value['harga_tariftindakan'];
                    $newData['daftartindakan_id'] = $value['daftartindakan_id'];
                    $newData['tipepaket_id'] = $value['tipepaket_id'];
                    $newData['tindakanpelayanan_id'] = '';
                    $newData['komponentarif_id'] = $value['komponentarif_id'];
                    $result['komponen'][] = $newData;
                }
            }
        }
        return $result;
    }
    /**
    * @author Rizal F. <rizal@docotel.com>
    * @since 2018-06-05
    * @param integer pendaftaran_id
    * @return array pendaftaran
    */
    public function getPendaftaran($pendaftaran_id) {
        return Pendaftaran::find()
            ->andWhere(['pendaftaran_id'=>$pendaftaran_id])
            ->asArray()->one();
    }

    public function updateStatusperiksa($pendaftaran_id, $data_tarif) {
        $model = Pendaftaran::findOne($pendaftaran_id);
        $model->status_periksa = DocoConstants::STATUS_PERIKSA_RJK_RNP; // rujuk rawat inap 433
        $model->is_ranap = true;
        $model->status_bayar = $data_tarif ? DocoConstants::BELUM_LUNAS : $model->status_bayar;
        if(!$model->save(false)){
            throw new \yii\base\ErrorException("Gagal Ubah Status Periksa", 500);
        }
        return $model;
    }

    public function updateStatusKamar($kamartempattidur_id, $jk) {
        $isiLaki = DocoConstants::KET_TT_ISI_L;
        $isiPere = DocoConstants::KET_TT_ISI_P;
        $jenis_isi = ($jk == DocoConstants::VAR_LK) ? $isiLaki : $isiPere;
        $model = KamarTempatTidur::findOne($kamartempattidur_id);
        $model->status_isi = true;
        $model->kettempattidur_id = $jenis_isi;

        if(!$model->save(false)){
            throw new \yii\base\ErrorException("Gagal Ubah Status Kamar", 500);
        }
        return $model;
    }

    public function actionGenerateApi()
    {
        // ruangan
        $modelRuangan = new Ruangan;
        $queryRuangan = $modelRuangan::find();
        $queryRuangan = DocoRestActiveFilter::advancedFilter($modelRuangan, $queryRuangan);
        $queryRuangan = new ActiveDataProvider([
            'query' => $queryRuangan,
        ]);

        // instalasi
        $modelInstalasi = new Instalasi;
        $queryInstalasi = $modelInstalasi::find();
        $queryInstalasi = DocoRestActiveFilter::advancedFilter($modelInstalasi, $queryInstalasi);
        $queryInstalasi = new ActiveDataProvider([
            'query' => $queryInstalasi,
        ]);

        // cara bayar
        $modelCaraBayar = new CaraBayar;
        $queryCaraBayar = $modelCaraBayar::find();
        $queryCaraBayar = DocoRestActiveFilter::advancedFilter($modelCaraBayar, $queryCaraBayar);
        $queryCaraBayar = new ActiveDataProvider([
            'query' => $queryCaraBayar,
        ]);

        // penjamin
        $modelPenjamin = new Penjamin;
        $queryPenjamin = $modelPenjamin::find();
        $queryPenjamin = DocoRestActiveFilter::advancedFilter($modelPenjamin, $queryPenjamin);
        $queryPenjamin = new ActiveDataProvider([
            'query' => $queryPenjamin,
        ]);

        // dokter PJ
        $modelDokter = new Pegawai;
        $queryDokter = $modelDokter::find()->where(['kelompokpegawai_id' => Pegawai::KELOMPOK_DOKTER]);
        $queryDokter = DocoRestActiveFilter::advancedFilter($modelDokter, $queryDokter);
        $queryDokter = new ActiveDataProvider([
            'query' => $queryDokter,
        ]);

        // jenis identitas
        $modelJenisIdentitas = new Lookup;
        $queryJenisIdentitas = $modelJenisIdentitas::find()->where(['lookup_type' => self::JENIS_IDENTITAS]);
        $queryJenisIdentitas = DocoRestActiveFilter::advancedFilter($modelJenisIdentitas, $queryJenisIdentitas);
        $queryJenisIdentitas = new ActiveDataProvider([
            'query' => $queryJenisIdentitas,
        ]);

        // nama depan
        $modelNamaDepan = new Lookup;
        $queryNamaDepan = $modelNamaDepan::find()->where(['lookup_type' => self::NAMA_DEPAN]);
        $queryNamaDepan = DocoRestActiveFilter::advancedFilter($modelNamaDepan, $queryNamaDepan);
        $queryNamaDepan = new ActiveDataProvider([
            'query' => $queryNamaDepan,
        ]);

        // jenis kelamin
        $modelJenisKelamin = new Lookup;
        $queryJenisKelamin = $modelJenisKelamin::find()->where(['lookup_type' => self::JENIS_KELAMIN]);
        $queryJenisKelamin = DocoRestActiveFilter::advancedFilter($modelJenisKelamin, $queryJenisKelamin);
        $queryJenisKelamin = new ActiveDataProvider([
            'query' => $queryJenisKelamin,
        ]);

        // status perkawinan
        $modelStatusPerkawinan = new Lookup;
        $queryStatusPerkawinan = $modelStatusPerkawinan::find()->where(['lookup_type' => self::STATUS_PERKAWINAN]);
        $queryStatusPerkawinan = DocoRestActiveFilter::advancedFilter($modelStatusPerkawinan, $queryStatusPerkawinan);
        $queryStatusPerkawinan = new ActiveDataProvider([
            'query' => $queryStatusPerkawinan,
        ]);

        // warga negara
        $modelWargaNegara = new Lookup;
        $queryWargaNegara = $modelWargaNegara::find()->where(['lookup_type' => self::WARGA_NEGARA]);
        $queryWargaNegara = DocoRestActiveFilter::advancedFilter($modelWargaNegara, $queryWargaNegara);
        $queryWargaNegara = new ActiveDataProvider([
            'query' => $queryWargaNegara,
        ]);

        // agama
        $modelAgama = new Lookup;
        $queryAgama = $modelAgama::find()->where(['lookup_type' => self::AGAMA]);
        $queryAgama = DocoRestActiveFilter::advancedFilter($modelAgama, $queryAgama);
        $queryAgama = new ActiveDataProvider([
            'query' => $queryAgama,
        ]);

        // pengantar
        $modelPengantar = new Lookup;
        $queryPengantar = $modelPengantar::find()->where(['lookup_type' => self::PENGANTAR]);
        $queryPengantar = DocoRestActiveFilter::advancedFilter($modelPengantar, $queryPengantar);
        $queryPengantar = new ActiveDataProvider([
            'query' => $queryPengantar,
        ]);

        // hubungan
        $modelHubungan = new Lookup;
        $queryHubungan = $modelHubungan::find()->where(['lookup_type' => self::HUBUNGAN]);
        $queryHubungan = DocoRestActiveFilter::advancedFilter($modelHubungan, $queryHubungan);
        $queryHubungan = new ActiveDataProvider([
            'query' => $queryHubungan,
        ]);

        // pekerjaan
        $modelPekerjaan = new Pekerjaan;
        $queryPekerjaan = $modelPekerjaan::find();
        $queryPekerjaan = DocoRestActiveFilter::advancedFilter($modelPekerjaan, $queryPekerjaan);
        $queryPekerjaan = new ActiveDataProvider([
            'query' => $queryPekerjaan,
        ]);

        // provinsi
        $modelPropinsi = new Propinsi;
        $queryPropinsi = $modelPropinsi::find();
        $queryPropinsi = DocoRestActiveFilter::advancedFilter($modelPropinsi, $queryPropinsi);
        $queryPropinsi = new ActiveDataProvider([
            'query' => $queryPropinsi,
        ]);

        // suku
        $modelSuku = new Suku;
        $querySuku = $modelSuku::find();
        $querySuku = DocoRestActiveFilter::advancedFilter($modelSuku, $querySuku);
        $querySuku = new ActiveDataProvider([
            'query' => $querySuku,
        ]);

        // pasien
        $modelPasien = new Pasien;
        $queryPasien = $modelPasien::find();
        $queryPasien = DocoRestActiveFilter::advancedFilter($modelPasien, $queryPasien);
        $queryPasien = new ActiveDataProvider([
            'query' => $queryPasien,
        ]);

        // jenis kasus penyakit
        $modelJenisKasus = new JenisKasusPenyakit;
        $queryJenisKasus = $modelJenisKasus::find();
        $queryJenisKasus = DocoRestActiveFilter::advancedFilter($modelJenisKasus, $queryJenisKasus);
        $queryJenisKasus = new ActiveDataProvider([
            'query' => $queryJenisKasus,
        ]);

        // kelas pelayanan
        $modelKelasPelayanan = new KelasPelayanan;
        $queryKelasPelayanan = $modelKelasPelayanan::find();
        $queryKelasPelayanan = DocoRestActiveFilter::advancedFilter($modelKelasPelayanan, $queryKelasPelayanan);
        $queryKelasPelayanan = new ActiveDataProvider([
            'query' => $queryKelasPelayanan,
        ]);

        return [
            'carabayar' => $queryCaraBayar->getModels(),
            'instalasi' => $queryInstalasi->getModels(),
            'penjamin' => $queryPenjamin->getModels(),
            'ruangan' => $queryRuangan->getModels(),
            'dokter' => $queryDokter->getModels(),
            'jenisidentitas' => $queryJenisIdentitas->getModels(),
            'nama_depan' => $queryNamaDepan->getModels(),
            'jenis_kelamin' => $queryJenisKelamin->getModels(),
            'status_perkawinan' => $queryStatusPerkawinan->getModels(),
            'warga_negara' => $queryWargaNegara->getModels(),
            'agama' => $queryAgama->getModels(),
            'pengantar' => $queryPengantar->getModels(),
            'hubungan' => $queryHubungan->getModels(),
            'pekerjaan' => $queryPekerjaan->getModels(),
            'propinsi' => $queryPropinsi->getModels(),
            'suku' => $querySuku->getModels(),
            'pasien' => $queryPasien->getModels(),
            'jeniskasus' => $queryJenisKasus->getModels(),
            'kelaspelayanan' => $queryKelasPelayanan->getModels(),
        ];
    }

    // DUPLICATED FROM ANTRIAN
    public function actionUbahPengambilanAntrian($id)
    {
        try {
            $request = Yii::$app->request;
            $model = Loket::findOne($id);
            $post = $request->post();
            if ($request->post() && !empty($model)) {
                if ($model->save()) {
                    $insertMp = [];
                    $lastloket_id = $model->loket_id;
                    $delete = (new LoketMp)->delete(['loket_id' => $lastloket_id]);
                    if (isset($post['konfigantrian_id'])) {
                        $konfig = $post['konfigantrian_id'];
                        foreach ($konfig as $value) {
                            $insertMp[] = [
                                'konfigantrian_id' => $value,
                                'loket_id' => $lastloket_id
                            ];
                        }
                    }
                    LoketMp::batchInsert($insertMp,false);
                    return ['message' => 'Data Berhasil di simpan'];
                } else {
                    return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                }
            }
            throw new Exception("Data Tidak Di Temukan");
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    // DUPLICATE FROM ANTRIAN
    public function actionViewLoket($id)
    {
        $dataRuangan = Ruangan::find()->where(['instalasi_id'=>DocoConstants::INSTALASI_FARMASI]);
        $data_ruangan_farmasi = ArrayHelper::map($dataRuangan->all(),'ruangan_id','ruangan_nama');

        $option = AllowController::getLookupByType('jenis_antrian')->all();
        $loket = Loket::find()->select([
            'loket_m.loket_id',
            'loket_m.ruangan_id',
            'nama_loket' => 'loket_m.loket_nama',
            'loket_m.loket_namalain',
            'loket_m.loket_fungsi',
            'loket_m.loket_formatnomor',
            'loket_m.fungsiantrian_id',
            'loket_m.jenisantrian_id',
            'loket_m.layarantrian_id',
            'is_active' => 'loket_m.is_active',
            'no_loket' => 'loket_m.loket_nourut',
        ])->where([
            'loket_m.loket_id' => $id
        ])->joinWith([
            'detail' => function ($query) {
                $query->select([
                    'loket_mp.loket_id',
                    'loket_mp.konfigantrian_id',
                ]);
            }
        ])->asArray()->one();
        $valKonfig = [];
        if (!empty($loket['detail'])) {
            foreach ($loket['detail'] as $value) {
                $valKonfig[] = $value['konfigantrian_id'];
            }
        }
        return [
            'option' => $option,
            'loket' => $loket,
            'val_konfig' => $valKonfig,
            'ruangan' => $data_ruangan_farmasi
        ];
    }

    /*
        Trigger Menyimpan Data Sinkronisasi Kunjungan di DB tujuan
    *   return: boolean
    */
    private function sinkronKunjungan($attribut_kunjungan)
    {
        try{
            $restSync = Yii::$app->docoRest->sinkronisasi;
            $request = $restSync->post('sync/pendaftaran', [
                'json'=>$attribut_kunjungan
            ]);

            $response = json_decode($request->getBody(),true);
            return $response;
        }catch(RequestException $e){
            return $e->getMessage();
        } catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /*
        Trigger Menyimpan Data Temporari Sinkronisasi Kunjungan yang gagal
    */
    private function saveTempSinkron($attribut_kunjungan)
    {
        try{
            $temp_data = json_encode($attribut_kunjungan);
            $mTemp = new SyncPendaftaranTransaksi;
            $mTemp->pendaftaran_id = @$attribut_kunjungan['pendaftaran_id'];
            $mTemp->is_sync = FALSE;
            $mTemp->additional_sync = $temp_data;
            if($mTemp->save()){
                return true;
            }
            return false;
        }catch(\Exception $e){
            return false;
        } catch(\yii\db\Exception $e){
            return false;
        }
    }

    /**
     * @todo Fungsi untuk mendapatkan list data pasien
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataPasien()
    {
        $request = Yii::$app->request;

        $model = new PasienKunjunganAkhirView;
        $query = $model::find();
        // if (!empty($_GET['is_aps'])) {
        //     $query->andWhere([
        //         'is_aps' => true
        //     ]);
        // }
        if (isset($_GET['advanced-filter']['tanggal_lahir']) && $_GET['advanced-filter']['tanggal_lahir'] != '') {
            $query->andWhere(['tanggal_lahir' => $_GET['advanced-filter']['tanggal_lahir']]);
        }
        $query->orderBy(['nama_pasien' => 'ASC']);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
     * @todo Fungsi untuk mendapatkan konfig sistem
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetKonfigSystem()
    {
        $konfig = KonfigSystem::find()->limit(1)->one();

        return $konfig;
    }

    /**
     * @todo Fungsi untuk mendapatkan data kunjungan pasien
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataKunjunganPasien()
    {
        $request = Yii::$app->request;
        $pasien_id = $request->get('pasien_id');

        $model = new InfoKunjunganRsView;
        $query = $model::find();

        if ($pasien_id != '') {
            $query->andWhere(['pasien_id' => $pasien_id]);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }


    /**
     * @todo Fungsi untuk resinkronisasi
     * @author Rizal <rizal@docotel.com>
     */
    public function actionResync()
    {
        try{
            $response = $lists = [];
            $jsonLists = '';
            $listUnsyncPasien = SyncPasien::find()->where(['is_sync' => false])->all();
            if ($listUnsyncPasien) {
                foreach ($listUnsyncPasien as $key => $unsyncPasien) {
                    $lists[] = $unsyncPasien['additional_sync'];
                }
                $jsonLists = json_encode($lists);

                $restSync = Yii::$app->docoRest->sinkronisasi;
                $request = $restSync->post('sync/pasien-many', [
                    'json'=>$jsonLists
                ]);

                $response = json_decode($request->getBody(),true);

                // return $response['successList'];
                if ($response['successList']) {
                    $succList = $response['successList'];
                    $models = SyncPasien::find()->where(["(additional_sync::json->>'Kode_CM')::int" => $succList])->all();
                    foreach ($models as $model) {
                        $model->is_sync = true;
                        $model->save(false);
                    }
                }
            }

            $response = $lists = [];
            $jsonLists = '';
            $listUnsyncPendaftaran = SyncPendaftaranTransaksi::find()->where(['is_sync' => false])->all();
            if ($listUnsyncPendaftaran) {
                foreach ($listUnsyncPendaftaran as $key => $unsyncPendaftaran) {
                    $lists[] = $unsyncPendaftaran['additional_sync'];
                }
                $jsonLists = json_encode($lists);

                $restSync = Yii::$app->docoRest->sinkronisasi;
                $request = $restSync->post('sync/pendaftaran-many', [
                    'json'=>$jsonLists
                ]);

                $response = json_decode($request->getBody(),true);

                if ($response['successList']) {
                    $succList = $response['successList'];
                    $models = SyncPendaftaranTransaksi::find()->where(["(additional_sync::json->>'pendaftaran_id')::int" => $succList])->all();
                    foreach ($models as $model) {
                        $model->is_sync = true;
                        $model->save(false);
                    }
                }
            }
            return true;
        }catch(RequestException $e){
            return $e->getMessage();
        } catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /*
    * author: Budi
    * created date: 24-06-2019
    * desc: save pasien_m
    * params needed: Array data
    * return: no_rekam_medik
    */
    private function savePasien($data)
    {
        $model = new Pasien;
        if (!empty($data)) {
            $model->attributes = $data;
            $model->tanggal_lahir = date('Y-m-d',strtotime($model->tanggal_lahir));
            $model->tgl_rekam_medik = date('Y-m-d');
            $golonganumurpasien = $model->getGolonganUmurPasien();
            $model->golonganumur_id = $golonganumurpasien->golonganumur_id;
            if ($model->validate() && $model->save()) {
                $pasien_id = $model->pasien_id;
                $getPasien = Pasien::find()->select([
                    'pasien_id',
                    'no_rekam_medik'
                ])->where([
                    'pasien_id' => $pasien_id
                ])->one();
                $data['pasien_id'] = $pasien_id;
                $data['no_rekam_medik'] = $getPasien->no_rekam_medik;
                $data['nama_pasien'] = $data['nama_pasien'];
            } else {
                return [
                    'status' => 422,
                    'data' => $model->errors,
                    'model' => 'PasienForm',
                ];
            }
        }
        return $data;
    }

    public function actionSaveDataAsuransi($pendaftaran_id = null)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            if($post['is_rujukan'] == 1) {
                $modelRujukan = new Rujukan();
                $modelRujukan->attributes = $post;
                $modelRujukan->asalrujukan_id = $post['asalrujukan_id'];

                if($modelRujukan->validate() && $modelRujukan->save()) {
                    $rujukId = $modelRujukan->rujukan_id;
                    $getPendaftaran = Pendaftaran::find()->where([
                        'pendaftaran_id' => $pendaftaran_id
                    ])->one();
                    if (!empty($getPendaftaran)) {
                        $getPendaftaran->rujukan_id = $rujukId;
                        if (!$getPendaftaran->save()) {
                            throw new \Exception("Update Rujukan Gagal");
                        }
                    }
                }
                else {
                    $errors = DocoHelpers::parseError($modelRujukan->errors, 'RujukanForm');
                    return [
                        'data' => $errors,
                        'status' => 422,
                    ];
                }
            }

            $model = new AsuransiPasien();
            $query = $model->find()
                ->andWhere([
                    'pasien_id'=>$post['pasien_id'],
                    'penjamin_id'=>$post['penjamin_id'],
                ])
                ->orderBy('asuransipasien_id DESC')
                ->one();

            $query = $query ? : new AsuransiPasien;
            $query->attributes = $post;
            $query->pasien_id = $post['pasien_id'];
            $query->penjamin_id = $post['penjamin_id'];
            $query->carabayar_id = $post['carabayar_id'];

            if($query->validate() && $query->save()) {
                $transaction->commit();
                return ['message' => 'Data Berhasil di simpan'];
            }
            else {
                $errors = DocoHelpers::parseError($query->errors,'AsuransiForm');
                return [
                    'data' => $errors,
                    'status' => 422
                ];
            }

        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @function : cari pendaftaran by id
     */
    public function actionCariPendaftaranById()
    {
        $request = Yii::$app->request;
        $id = $request->get('pendaftaran_id');
        $model = new Pendaftaran;
        $query = $model::find();
        if($id) {
            $query = $query->where([Pendaftaran::tableName().'.pendaftaran_id' => $id])
            ->joinWith(['bpjs' => function($q) {
                $q->select([
                    'nosep', 'tglsep', 'nokartuasuransi as nomorkartu', 'norujukan', 'ppkrujukan', 'ppkpelayanan', 'catatansep', 'diagnosaawal', 'politujuan', 'lakalantas', 'lokasilaka', 'is_cob', 'kode_dpjp_melayani', 'nama_dpjp_melayani', 'nama_ppk_perujuk', 'kode_ppk_perujuk','tujuan_kunj', 'flag_procedure', 'kd_penunjang', 'assesment_pel', 'no_surat_meninggal', 'tgl_meninggal_bpjs', 'no_lp_manual', 'kode_dpjp_spri', 'nama_dpjp_spri', 'no_surat_kontrol', 'additional_data','additional_request as log_request', 'info_response as log_response'
                ]);
            }])
            ->asArray()->one();
        } else {
            return [
                'data' => 'Params pendaftaran_id tidak ada',
                'status' => 422
            ];
        }
        return $query;
    }

    public function actionGetKunjungan()
    {
        $request = Yii::$app->request;
        $no_rekam_medik = $request->get('no_rekam_medik', null);
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $query = InfoDataPendaftaranView::find()
            ->where([
                'no_rekam_medik' => $no_rekam_medik
            ]);
        $query->andWhere(['or', 
            ['and', ['between', 'tglpasienpulang', $start, $end], ['or', ['in', 'status_periksa', [4, 433]], ['status_ranap' => 487]]],
            ['not in', 'status_periksa', [4, 433]] 
            ]);

        $query->andWhere(['date(tgl_pendaftaran)' => date('Y-m-d')]);
        $query->andWhere(['pasienbatalperiksa_id' => null]);
        $query->orderBy(['tgl_pendaftaran' => SORT_DESC]);

        return [
            'data' => $query->one()
        ];
    }

    public function actionGetKunjunganByRekamMedik()
    {
        $request = Yii::$app->request;
        $no_rekam_medik = $request->get('no_rekam_medik', null);

        return [
            'data' => Pendaftaran::validasiKunjunganPasien($no_rekam_medik)
        ];
    }
}
