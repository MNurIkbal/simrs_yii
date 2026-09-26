<?php

/**
 * @author Rizal
 * @description backend transaksi pasien rajal
**/

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoMessages;
use Doco\Services\KasirService;

use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\LaporanKunjunganRawatJalanView;
use app\modules\v1\models\LaporanKunjunganRawatDaruratView;
use app\modules\v1\models\InfPasienPenunjang;
use app\modules\v1\models\InfoPasienMcuView;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\JenisKasusPenyakit;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\LoginPemakai;
use app\modules\v1\models\Bpjs;
use app\modules\v1\models\PasienBatalPeriksa;
use app\modules\v1\models\InfoPasienRiView;
use app\modules\v1\models\DokterView;
use app\modules\v1\models\AsuransiPasien;
use app\modules\v1\models\PenanggungBiaya;
use app\modules\v1\models\TindakanPelayanan;
use app\modules\v1\models\InfoTagihanPasien;
use app\modules\v1\models\PasienKirimUnitLain;
use app\modules\v1\models\PasienMasukPenunjang;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\Antrian;
use app\modules\v1\models\SyncsantoyusupR;
use app\modules\v1\models\SyPendaftaranView;
use app\modules\v1\models\SyPasienView;
use app\modules\v1\models\SyRuanganCaraBayarV;
use app\modules\v1\models\SyncEditsantoyusupR;
use app\modules\v1\models\Lookup;

use app\modules\v1\payload\PasienBatalKunjungan;
use app\modules\v1\cache\Cache;
use app\modules\v1\models\Pembayaran;
use Doco\Services\Vendors\PendaftaranService;

class TraPasienRawatJalanController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Pendaftaran';
    const PENDAFTARAN_ID = 'pendaftaran_id';
    const RUANGAN_ID = 'ruangan_id';
    const PENJAMIN_ID = 'penjamin_id';
    const NO_PENDAFTARAN = 'no_pendaftaran';
    const ERR_GAGAL_BATAL = 'Pendaftaran tidak bisa dibatalkan.';
    const STRING_RANAP = 'ranap';

    public $messageBroker = [
        'batal' => [
            'services' => [
                'Mhg' => [
                    'UpdateAppointment' => [
                        // 'payload' => ['id' => 'pendaftaran_id'],
                        'result' => true,
                        'successProcess'=>true,
                    ],
                ],
                'Sirs' => [
                    'StatusUpdateJkn' => [
                        'payload' => ['pendaftaran_id' => 'pendaftaran_id'],
                        'successProcess' => true,
                        'result' => true,
                        'taskid' => ['99'],
                    ]
                ]
            ]
        ]
    ];

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["view"] = ["POST", "GET"];
        $verbs["get-total-tagihan-pasien"] = ["GET"];
        $verbs["update"] = ["POST", "GET", 'PUT'];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['update']);
        return $actions;
    }

    public function actionView($id, $jenis='rajal')
    {
        if ($jenis == 'ranap') {
            $data_update = InfoPasienRiView::find()
            ->where([self::PENDAFTARAN_ID=>$id])
            ->asArray()
            ->one();
            $ruangan = Cache::getRuanganByInstalasi(DocoConstants::INST_ID_RI);
        } elseif ($jenis == 'igd') {
            $data_update = LaporanKunjunganRawatDaruratView::find()
            ->where([self::PENDAFTARAN_ID=>$id])
            ->asArray()
            ->one();
            $ruangan = Cache::getRuanganByInstalasi(DocoConstants::INST_ID_RD);
        } elseif($jenis == 'penunjang') {
            $data_update = InfPasienPenunjang::find()
            ->where([self::PENDAFTARAN_ID=>$id])
            ->asArray()
            ->one();
            $ruangan = Cache::getRuanganByInstalasi(json_encode(DocoConstants::INST_ID_PENUNJANG));
        } elseif($jenis == 'mcu') {
            $data_update = InfoPasienMcuView::find()
            ->where([self::PENDAFTARAN_ID=>$id])
            ->asArray()
            ->one();
            $ruangan = Cache::getRuanganByInstalasi(DocoConstants::INST_ID_MCU);
        } else {
            $data_update = LaporanKunjunganRawatJalanView::find()
            ->where([self::PENDAFTARAN_ID=>$id])->andWhere(['tipe'=>'pendaftaran'])
            ->asArray()
            ->one();
            $ruangan = Cache::getRuanganByInstalasi(DocoConstants::INST_ID_RJ);
        }
        if ($jenis == 'rajal') {
            $ruangan_id = $data_update['ruangan_id'];
        } else {
            $ruangan_id = $data_update[self::RUANGAN_ID];
        }
        $pendaftaran_referral = Pendaftaran::getValueReferal($id);
        $data_update['referal_pegawai_id'] = ArrayHelper::getValue($pendaftaran_referral,'referal_pegawai_id');
        $data_update['referal_luar'] = ArrayHelper::getValue($pendaftaran_referral,'referal_luar');
        $pasien_id = $data_update['pasien_id'];
        $carabayar_id = $data_update['carabayar_id'];
        $penjamin_id = $data_update[self::PENJAMIN_ID];
        $nosep = ArrayHelper::getValue($data_update,'nosep');
        $asuransipasien_id = isset($data_update['asuransipasien_id']) ? $data_update['asuransipasien_id'] : null;
        $penanggungbiaya_id = isset($data_update['penanggungbiaya_id']) ? $data_update['penanggungbiaya_id'] : null;

        $data_dokter = $this->ListDokter($ruangan_id);
        $data_penyakit = $this->ListPenyakit();
        $list_carabayar = $this->ListCaraBayar();
        if (!empty($list_carabayar)) {
            $carabayarOptions = [];
            foreach ($list_carabayar as $key => $value) {
                $carabayarOptions[$value['carabayar_id']] = ['data-id' => $value['groupcarabayar_id']];
            }
            $list_carabayar = ArrayHelper::map($list_carabayar, 'carabayar_id', 'carabayar_nama');
        }
        $data_penjamin = $this->ListPenjamin();
        $data_kelas = $this->ListKelasPelayanan();
        $dataAsuransi = $this->listDataAsuransi($asuransipasien_id);
        $dataPenanggungBiaya = $this->listDataPenanggungBiaya($penanggungbiaya_id);
        $dataBpjs = $this->listDataBpjs($nosep);
        $konfigSystem = $this->getKonfigSystem();
        $selectedNomorUrut = $this->getSelectedNomorUrut($data_update['pendaftaran_id']);
        $isOnlyCreateSep = $this->validateOnlyCreateSep($data_update, $jenis);
        $data_additional_payer = $this->getAdditionalPayer($data_update['pendaftaran_id']);

        if ($jenis == 'ranap') {
            $dokter_id = $data_update['dokter_admisi_id'];
        } else {
            $dokter_id = $data_update['pegawai_id'];
        }

        if ($selectedNomorUrut && $selectedNomorUrut->no_antrian) {
            $nomorUrutList = $this->getNomorUrut($ruangan_id, $dokter_id, $selectedNomorUrut->no_antrian);
        } else {
            $nomorUrutList = $this->getNomorUrut($ruangan_id, $dokter_id, '');
        }

        if($jenis == 'penunjang') {
            $data_dokter = $this->listDokterByPendaftaran($id);
        }

        $data['data_update'] = $data_update;
        $data['data_dokter'] = $data_dokter;
        $data['data_penyakit'] = $data_penyakit;
        $data['data_carabayar'] = $list_carabayar;
        $data['data_penjamin'] = $data_penjamin;
        $data['data_kelas'] = $data_kelas;
        $data['carabayarOptions'] = $carabayarOptions;
        $data['data_asuransi'] = $dataAsuransi;
        $data['data_penanggungbiaya'] = $dataPenanggungBiaya;
        $data['data_bpjs'] = $dataBpjs;
        $data['nomorUrutList'] = $nomorUrutList['response'];
        $data['selectedNomorUrut'] = $selectedNomorUrut ? $selectedNomorUrut->no_antrian : null;
        $data['konfig'] = $konfigSystem;
        $data['data_ruangan'] = $ruangan;
        $data['isOnlyCreateSep'] = $isOnlyCreateSep;
        $data['data_additional_payer'] = $data_additional_payer;
        $data['referral_marketing'] = $this->getReferralMarketing();
        $data['pegawai'] = Cache::getMaster('master-pegawai',['pegawai' => [
                    'model' => 'Pegawai',
                    'select' => [
                        'pegawai_id',
                        'nama_pegawai'
                    ],
                    'order_by' => [
                        'nama_pegawai' => SORT_ASC
                    ]
                ]]);

        return $data;
    }

    public function actionViewSty($id, $jenis='rajal')
    {
        $rujukandari = $instalasi = $bagian = $prosedurMasuk = [];
        $isInstalasi = false;

        if ($jenis == 'ranap') {
            $data_update = InfoPasienRiView::find()
            ->where([self::PENDAFTARAN_ID=>$id])
            ->asArray()
            ->one();

            $ruangan = Cache::getRuanganByInstalasi(DocoConstants::INST_ID_RI);
            $bagianSty = Cache::getListBagianSty();
            $prosedurMasuk = array_filter($bagianSty, function($v, $k) {
                return $v['instalasi_id'] == 1 || $v['instalasi_id'] == 2;
            }, ARRAY_FILTER_USE_BOTH);
        } elseif ($jenis == 'igd') {
            $data_update = LaporanKunjunganRawatDaruratView::find()
            ->where([self::PENDAFTARAN_ID=>$id])
            ->asArray()
            ->one();
            $ruangan = Cache::getRuanganByInstalasi(DocoConstants::INST_ID_RD);
        } elseif($jenis == 'penunjang') {
            $data_update = InfPasienPenunjang::find()
            ->where([self::PENDAFTARAN_ID=>$id])
            ->asArray()
            ->one();
            $ruangan = Cache::getRuanganByInstalasi($data_update['styrujukaninstalasi_id']);
            $rujukandari = Cache::getRujukanStyp();
            $instalasi = Cache::getInstalasi('true');
            $isInstalasi = true;
        } elseif($jenis == 'mcu') {
            $data_update = InfoPasienMcuView::find()
            ->where([self::PENDAFTARAN_ID=>$id])
            ->asArray()
            ->one();
            $ruangan = Cache::getRuanganByInstalasi(DocoConstants::INST_ID_MCU);
        } else {
            $data_update = LaporanKunjunganRawatJalanView::find()
            ->where([self::PENDAFTARAN_ID=>$id])
            ->asArray()
            ->one();
            $ruangan = Cache::getRuanganByInstalasi(DocoConstants::INST_ID_RJ);
        }

        $ruangan_id = $data_update[self::RUANGAN_ID];
        if ($jenis == 'penunjang') {
            $ruangan_id = $data_update['instalasiasal_id'];
        }
        $pasien_id = $data_update['pasien_id'];
        $carabayar_id = $data_update['carabayar_id'];
        $penjamin_id = $data_update[self::PENJAMIN_ID];
        $nosep = array_key_exists('nosep', $data_update) ? $data_update['nosep'] : '';
        $asuransipasien_id = isset($data_update['asuransipasien_id']) ? $data_update['asuransipasien_id'] : null;
        $penanggungbiaya_id = isset($data_update['penanggungbiaya_id']) ? $data_update['penanggungbiaya_id'] : null;

        // $data_dokter = $this->ListDokter($ruangan_id);
        $data_all_dokter = $this->ListDokter();
        $data_dokter = $this->getRuanganJamDokter($ruangan_id, $isInstalasi);
        $data_penyakit = $this->ListPenyakit();
        $list_carabayar = $this->ListCaraBayar();
        if (!empty($list_carabayar)) {
            $carabayarOptions = [];
            foreach ($list_carabayar as $key => $value) {
                $carabayarOptions[$value['carabayar_id']] = ['data-id' => $value['groupcarabayar_id']];
            }
            $list_carabayar = ArrayHelper::map($list_carabayar, 'carabayar_id', 'carabayar_nama');
        }
        $data_penjamin = $this->ListPenjamin();
        $data_kelas = $this->ListKelasPelayanan();
        $dataAsuransi = $this->listDataAsuransi($asuransipasien_id);
        $dataPenanggungBiaya = $this->listDataPenanggungBiaya($penanggungbiaya_id);
        $dataBpjs = $this->listDataBpjs($nosep);
        $konfigSystem = $this->getKonfigSystem();
        $selectedNomorUrut = $this->getSelectedNomorUrut($data_update['pendaftaran_id']);
        $dataBagian = $this->getListBagian($carabayar_id);

        if ($jenis == 'ranap') {
            $dokter_id = $data_update['dokter_admisi_id'];
        } else {
            $dokter_id = $data_update['pegawai_id'];
        }

        if ($selectedNomorUrut && $selectedNomorUrut->no_antrian) {
            $nomorUrutList = $this->getNomorUrut($ruangan_id, $dokter_id, $selectedNomorUrut->no_antrian);
        } else {
            $nomorUrutList = $this->getNomorUrut($ruangan_id, $dokter_id, '');
        }

        $data['data_update'] = $data_update;
        $data['data_dokter'] = $data_dokter;
        $data['data_all_dokter'] = $data_all_dokter;
        $data['data_penyakit'] = $data_penyakit;
        $data['data_carabayar'] = $list_carabayar;
        $data['data_penjamin'] = $data_penjamin;
        $data['data_kelas'] = $data_kelas;
        $data['carabayarOptions'] = $carabayarOptions;
        $data['data_asuransi'] = $dataAsuransi;
        $data['data_penanggungbiaya'] = $dataPenanggungBiaya;
        $data['data_bpjs'] = $dataBpjs;
        $data['nomorUrutList'] = $nomorUrutList['response'];
        $data['selectedNomorUrut'] = $selectedNomorUrut ? $selectedNomorUrut->no_antrian : null;
        $data['konfig'] = $konfigSystem;
        $data['data_ruangan'] = $ruangan;
        $data['data_rujukandari'] = $rujukandari;
        $data['data_instalasi'] = $instalasi;
        $data['data_bagian'] = $dataBagian;
        $data['data_prosedur_masuk'] = $prosedurMasuk;

        return $data;
    }

    public function actionUpdate($id)
    {
        try {
            $request = Yii::$app->request;
            $model = Pendaftaran::findOne($id);
            if ($request->post() && !empty($model)) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'PendaftaranForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
            throw new \Exception("Data Tidak Di Temukan");
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
    * @author sunarko
    * @edited ali.padilah@docotel.com // 8-8-2018
    * @return list data dokter rajal
    * @desc
    */
    private function ListDokterRajal($ruangan_id = null)
    {
        $hari_id = DocoHelpers::getIdHariIni();
        $waktu_sekarang = date('H:i:s');
        $sql = '
            select
                pegawai_id,"Dokter",hari_id,jadwaldokter_mulai,jadwaldokter_tutup
            from
                cetakjadwaldokter_v
            where
                ruangan_id = '.$ruangan_id.'
                    and "Kuota" >= 1 and hari_id = '.$hari_id.' and jadwaldokter_tutup >= "'.$waktu_sekarang.'"
                    and jadwaldokter_mulai <= "'.$waktu_sekarang.'";
        ';
        $query = Yii::$app->db->createCommand($sql)->queryAll();
        $items = ArrayHelper::map($query, 'pegawai_id', 'Dokter');
        return $items;
    }



    /**
    * @author sunarko
    * @return list data penyakit
    * @desc
    */
    private function ListPenyakit($mapping=true) {
        $data = JenisKasusPenyakit::find()
        ->andWhere([
            'is_active' => true,
            'is_deleted' => false
            ])
        ->orderBy('jeniskasuspenyakit_nama');
        if ($mapping) {
            $items = ArrayHelper::map($data->all(), 'jeniskasuspenyakit_id', 'jeniskasuspenyakit_nama');
        } else {
            $items = $data->all();
        }

        return $items;
    }

    /**
    * @author sunarko
    * @return list data cara bayar
    * @desc
    */
    private function ListCaraBayar() {
        $data = CaraBayar::find()->where([
            'is_active' => true,
            'is_deleted' => false
            ])->orderBy('carabayar_id');
        $items = $data->asArray()->all();
        // $items = ArrayHelper::map($data->all(), 'carabayar_id', 'carabayar_nama');
        return $items;
    }
    /**
    * @author sunarko
    * @return list data penjamin
    * @desc
    */
    private function ListPenjamin() {
        $data = Penjamin::find();
        $data->where(
            [
                'is_active' => true,
                'is_deleted' => false
            ]
        );
        $data->orderBy(self::PENJAMIN_ID);

        $items = ArrayHelper::map($data->all(), self::PENJAMIN_ID, 'penjamin_nama');
        return $items;
    }

    private function getReferralMarketing() {
        $data = Lookup::find();
        $data->where(
            [
                'lookup_type' => 'referral_marketing',
                'is_active' => true,
                'is_deleted' => false
            ]
        );
        $data->orderBy('lookup_name');

        $items = ArrayHelper::map($data->all(), 'lookup_name', 'lookup_name');
        if(!empty($items) && is_array($items) && count($items) > 0) {
            $items = array_map('strtoupper',$items);
            $items = array_change_key_case($items, CASE_UPPER);
        }
        return $items;
    }

    public function actionBatal()
    {
        return Yii::$app->docoPlugin->execute('batal_pendaftaran');
    }

    public function actionGetTotalTagihanPasien($no_pendaftaran)
    {
        $model = InfoTagihanPasien::find()->select([
            'COALESCE(SUM(sub_total), 0) AS tagihan'
        ])->andWhere([
            self::NO_PENDAFTARAN => $no_pendaftaran
        ])->scalar();

        return [
            self::NO_PENDAFTARAN => $no_pendaftaran,
            'total_tagihan' => $model
        ];
    }

    public function actionGetTagihanSudahBayar($pendaftaran_id)
    {
        //Cek Pembayaran
        $countPembayaran = TindakanPelayanan::find()
                ->where([self::PENDAFTARAN_ID => $pendaftaran_id])
                ->andWhere(['is not', 'tindakansudahbayar_id', null])
                ->andWhere(['is_deleted' => false])
                ->count();

        return [
            'count_pembayaran' => $countPembayaran
        ];
    }

    public function actionGetPembayaran($pendaftaran_id)
    {
        // Cek Pembayaran dari pembayaran_t
        $countPembayaran = Pembayaran::find()
            ->where([self::PENDAFTARAN_ID => $pendaftaran_id])
            ->andWhere(['is_deleted' => false])
            ->count();
        return [
            'count_pembayaran' => $countPembayaran
        ];
    }

    public function actionUpdateStatus()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $statPeriksa = $post['status_periksa'];
        $jenis = $post['jenis'];

        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;

        $check = $jwt->katakunci_pemakai;
        $valid = Yii::$app->security->validatePassword($post['password'], $check);
        if (!$valid) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                'text' => 'Password salah.'
            ]);
        }

        if ($jenis != 'rajal' && $jenis != 'igd') {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                'text' => 'Fitur belum tersedia untuk instalasi ini.'
            ]);
        }

        $model = Pendaftaran::find()->andWhere([
            self::NO_PENDAFTARAN => $post['no_pendaftaran']
        ])->one();

        if (!empty($model)) {
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            try {
                $model->status_periksa = $statPeriksa;
                $petugas_id = $jwt->loginpemakai_id;
                $petugas_tgl_pembuat = date('Y-m-d H:i:s');

                $model->petugas_id = $petugas_id;
                $model->petugas_tgl_pembuat = $petugas_tgl_pembuat;

                if ($model->save()) {
                    $transaction->commit();

                    switch($jenis){
                        case 'igd':
                            $route = 'app/update-status-kunjungan-igd';
                            break;
                        default:
                            $route = 'app/update-status-kunjungan-rajal';
                            break;
                    }

                    $idPendaftaran = $model->pendaftaran_id;
                    $params['route'] = $route;
                    $params['data'] = $this->syncPendaftaran($idPendaftaran);
                    // return $params['data'];
                    (new PendaftaranService)->syncPendaftaranSty($params);

                    return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM, [
                        'text' => 'Status Kunjungan berhasil diubah.'
                    ]);
                } else {
                    $transaction->rollBack();
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                        'data' => $model->errors
                    ]);
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
        throw new \Exception("Data Tidak Di Temukan");
    }

    private function syncPendaftaran($idPendaftaran)
    {
        $result = $asuransi = $keluarga = $penanggung = $pasien = $penjamin = $penanggungJawab = [];
        $pdftrn = SyPendaftaranView::find()
        ->where(['pendaftaran_id' => $idPendaftaran])
        ->asArray()
        ->one();

        if(!empty($pdftrn)) {
            $add = json_decode($pdftrn['additional_data'], true);

            $pasien = SyPasienView::find()
            ->where(['pasien_id' => $pdftrn['pasien_id']])
            ->asArray()
            ->one();

            if(isset($pdftrn['umur']) && !empty($pdftrn['umur'])) {
                $umurExp = explode(" ",$pdftrn['umur']);
                $pdftrn['umur_hari'] = $umurExp[4];
                $pdftrn['umur_bulan'] = $umurExp[2];
                $pdftrn['umur_tahun'] = $umurExp[0];
            }

            if (isset($pasien['additional_pasien']) && !empty($pasien['additional_pasien'])) {
                $additionalPasien = json_decode($pasien['additional_pasien']);
                if (!empty($additionalPasien)) {
                    foreach ($additionalPasien as $key => $value) {
                        if (isset($value->jenisidentitas) && $value->jenisidentitas == DocoConstants::CONS_ID_KTP) {
                            $ktp = $value->no_identitas_pasien;
                        }
                    }
                    if(isset($ktp)) {
                        $pasien['nik'] = $ktp;
                    }
                }
            }
        }

        $result = [
            'pendaftaran' => $pdftrn,
            'pasien' => $pasien,
        ];

        $modelSync = new SyncEditsantoyusupR;
        $modelSync->pendaftaran_id = $pdftrn['pendaftaran_id'];
        $modelSync->pasien_id = $pdftrn['pasien_id'];
        $modelSync->created_date = date("Y-m-d H:i:s");
        $modelSync->count_sync = 0;
        $modelSync->payload = json_encode($result);
        $modelSync->state = 'update status';
        $modelSync->save(false);

        return $result;
    }

    private function deleteSep($bpjs_id)
    {
        if (!$bpjs_id) return '';

        $model = Bpjs::findOne($bpjs_id);
        $model->is_deleted = true;
        $model->norujukan = '-';
        $sep = $model->nosep;
        if($model->validate() && $model->save()) {
            $result = $model->deleteSep($sep);
            return $result;
        }
        else {
            return [
                'data' => $model->errors,
                'status' => 422
            ];
        }
    }

    private function ListKelasPelayanan() {
        $data = KelasPelayanan::find();
        $data->where(
            [
                'is_active' => true,
                'is_deleted' => false
            ]
        );
        $data->orderBy('kelaspelayanan_nama');
        $items = ArrayHelper::map($data->all(), 'kelaspelayanan_id', 'kelaspelayanan_nama');

        return $items;
    }

    /**
    * @author Budi
    * @return list data dokter ranap & rajal
    * @desc
    */
    private function ListDokter($ruangan_id = null, $instalasi_id = null)
    {
        $data = DokterView::find();
        if($ruangan_id) {
            $data = $data->where([self::RUANGAN_ID => $ruangan_id]);
        }
        $data->orderBy(['nama_pegawai'=> SORT_ASC ]);

        $items = ArrayHelper::map($data->all(), 'pegawai_id', 'nama_pegawai');

        return $items;
    }

    private function getRuanganJamDokter($id, $isInstalasi = false)
    {
        $where = "";
        $today_id = date('N') + 74;
        $now = date('H:i');

        $where .= " ";
            if($isInstalasi == true) {
                $tipe = 'instalasi_id';
            } else {
                $tipe = 'ruangan_id';
            }

        $query = "
            select pegawai_id, nama_pegawai
            from pegawai_v
            WHERE {$tipe} = {$id} AND kelompokpegawai_id = 1
            ORDER BY nama_pegawai
        ";

        $data = Yii::$app->db->createCommand($query)->queryAll();
        return ArrayHelper::map($data,'pegawai_id','nama_pegawai');
    }

    /**
    * @author Budi
    * @return list data asuransi pasien
    * @desc
    */
    private function listDataAsuransi($asuransipasien_id)
    {
        $data = AsuransiPasien::findOne($asuransipasien_id);

        return $data;
    }

    /**
    * @author Budi
    * @return list data bpjs
    * @desc
    */
    private function listDataBpjs($nosep)
    {
        $result = [];
        if(!empty($nosep)) {
            $data = Bpjs::find();
            $data = $data->where([
                'nosep' => $nosep,
            ])->one();

            if($data) {
                $additional_data = json_decode($data->additional_data, true);
                $result = $additional_data['sep'];
            }
        }

        return $result;
    }

    private function listDataPenanggungBiaya($penanggungbiaya_id)
    {
        $data = PenanggungBiaya::find();
        $data = $data->where([
            'penanggungbiaya_id' => $penanggungbiaya_id,
        ])->one();

        return $data;
    }

    private function findPasienKeUnitLain()
    {
        return PasienKirimUnitLain::find();
    }

    private function findPasienPasienMasukPenunjang()
    {
        return PasienMasukPenunjang::find();
    }

    private function getKonfigSystem()
    {
        return KonfigSystem::find()->one();
    }

    private function getNomorUrut($ruangan_id, $pegawai_id, $no_antrian = '')
    {
        return Yii::$app->runAction('/v1/allow/get-nomor-urut', [
            'ruangan_id' => $ruangan_id,
            'dokter_id' => $pegawai_id,
            'antrianExcepted' => $no_antrian
        ]);
    }

    private function getSelectedNomorUrut($antrian_id)
    {
        if ($antrian_id) {
            $antrian = Antrian::find()->where([
                'pendaftaran_id' => $antrian_id
            ])->all();

            if (!empty($antrian)) {
                if (count($antrian) == 2) {
                    foreach ($antrian as $value) {
                        if ($value['jenisantrian_id'] == DocoConstants::VAR_JA_P) {
                            return $value;
                        }
                    }
                } else {
                    return $antrian[0];
                }
            }
        }

        return null;
    }

    private function getListBagian($carabayar_id) {
        $model = SyRuanganCaraBayarV::find()
            ->select(['ruangancarabayar_id', 'ruangcarabayar_nama'])
            ->where(['carabayar_id' => $carabayar_id])
            ->orderBy('ruangcarabayar_nama');

        return $model->asArray()->all();
    }

    private function validateOnlyCreateSep($data, $jenis)
    {
        $pendaftaran_id = ArrayHelper::getValue($data, 'pendaftaran_id');
        if ($data['carabayar_id'] == DocoConstants::PENJAMIN_BPJS && $pendaftaran_id != null) {
            $status_periksa = $jenis == self::STRING_RANAP ? $data['status_ranap'] : $data['status_periksa_id'];
            if (($status_periksa == DocoConstants::STATUS_PULANG || $status_periksa == DocoConstants::STATUS_RANAP_PULANG)
                && $data['status_bayar'] == DocoConstants::LUNAS) {
                $data_bpjs = Bpjs::find()
                 ->select(['nosep'])
                ->where(['pendaftaran_id' => $pendaftaran_id])
                ->asArray()->one();
                $nosep = ArrayHelper::getValue($data_bpjs, 'nosep');

                if (!empty($data_bpjs)) {
                    if (!$nosep) return true;
                } else {
                    return true;
                }
            }
        }

        return false;
    }

    private function listDokterByPendaftaran($pendaftaran_id)
    {
        $pendaftaran = Pendaftaran::find()
        ->where([self::PENDAFTARAN_ID => $pendaftaran_id])
        ->asArray()
        ->one();

        $dokter = DokterView::find();
        $ruangan = ArrayHelper::getValue($pendaftaran, 'ruangan_id');

        if($ruangan) {
            $dokter = $dokter->where([self::RUANGAN_ID => $ruangan]);
        }
        $dokter->orderBy(['nama_pegawai'=> SORT_ASC ]);

        return ArrayHelper::map($dokter->all(), 'pegawai_id', 'nama_pegawai');
    }

    private function getAdditionalPayer($pendaftaran_id)
    {
        $query_additional_payer = "
            select pt.asuransipasien_id, am.*, pt.carabayar_id, pt.penjamin_id from pendaftaran_multipayer_t pt
            join asuransipasien_m am on am.asuransipasien_id = pt.asuransipasien_id
            where pt.pendaftaran_id = '{$pendaftaran_id}' and
            pt.is_deleted = false and pt.is_active = true and
            am.is_deleted = false and am.is_active = true

        ";
        $additional_payer = Yii::$app->db->createCommand($query_additional_payer)->queryOne();
        return $additional_payer;
    }
}
