<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\KonfigAntrianView;
use app\modules\v1\models\KonfigAntrianFarmasiView;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\JadwalBukaPoliKlinik;
use app\modules\v1\models\InfoJadwalDokterView;
use app\modules\v1\models\Antrian;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\JenisAntrianDetail;
use app\modules\v1\models\Loket;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\KonfigSystemDetail;
use app\modules\v1\models\InfoReseptur;
use app\modules\v1\models\Reseptur;
use app\modules\v1\models\KonfigAntrian;
use app\modules\v1\payload\PayloadForm;
use Doco\components\DocoConstansId;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;
use Doco\components\DocoAntrian;
use Doco\components\DocoMessages;
use app\modules\v1\models\LayarAntrianView;
use app\modules\v1\models\LayarAntrian;
use app\modules\v1\models\JadwalCuti;

use app\modules\v1\controllers\AllowAntrianController;
use Doco\Services\RegistrationService;
use app\modules\v1\models\Profilrumahsakit;
use Doco\models\pendaftaran\PendaftaranOnline;
use Doco\models\pendaftaran\InfoPendaftaranOnlineView;
use app\modules\v1\models\AntrianView;
use Doco\models\KonsulPoli;
use Doco\models\Pendaftaran;
use Doco\components\PendaftaranHelpers;
use Doco\models\bpjs\Bpjs;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\modules\v1\cache\Cache;
use Mpdf\Tag\P;
use Doco\Services\Cache as DocoCache;


class DashboardController extends DocoActiveController
{
    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        $verbs['create-antrian'] = ['POST'];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex()
    {
        // $model = new Golonganpegawai;
        // $query = $model::find();
        // $query = DocoRestActiveFilter::advancedFilter($model, $query);
        // return new ActiveDataProvider([
        //     'query' => $query,
        // ]);
    }

    public function actionCreateAntrian()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $mAntrian = new Antrian;
        $transaction = $mAntrian->getDb()->beginTransaction();
        try{
            if(!isset($post)){
                throw new \yii\base\ErrorException("Data Tidak Ditemukan", 500);
            }
            if(!isset($post['jenisantrian_id'])){
                throw new \yii\base\ErrorException("Jenis Antrian Tidak Ditemukan", 500);
            }
            $jenisantrian_id = $post['jenisantrian_id'];
            $arr_data_antrian = [];
            $is_carabayar_bpjs = 0;

            switch ($jenisantrian_id) {
                case 177:
                    if (!in_array($request->post('pasienEnc', null), ['', null])) { // cek, kalo post ada ini berarti pasien lama
                        $model_konfig = KonfigAntrian::find()->andWhere(['statuspasien' => DocoConstants::VAR_PAS_L]);
                    } else {
                        $model_konfig = KonfigAntrian::find();
                        $model_konfig->andWhere(['jenisantrian_id' => $jenisantrian_id]);

                        if (array_key_exists('klasifikasi_pilih', $post)) {
                            $model_konfig->andWhere(['klasifikasipasien_id' => $post['klasifikasi_pilih']]);
                        }

                        if (array_key_exists('carabayar_pilih', $post)) {
                            $model_konfig->andWhere(['groupcarabayar_id' => $post['carabayar_pilih']]);

                            if ($post['carabayar_pilih'] == DocoConstants::GROUP_BPJS) {
                                $is_carabayar_bpjs = 1;
                            }
                        }
                    }

                    $konfig_antrian = $model_konfig->one();
                    $konfigantrian_id = $konfig_antrian['konfigantrian_id'];

                    if (isset($post['kuota_antrian']) && $post['kuota_antrian'] == DocoConstants::VAR_ID_KUOTA_ANTRIAN_POLIKLINIK) {
                        $arr_data_antrian = [
                            'jadwalbukapoli_id' => $post['poly_pilih'],
                            'ruangan_id' => $post['ruangan_id'],
                            'klasifikasipasien_id'=> array_key_exists('klasifikasi_pilih', $post) ? $post['klasifikasi_pilih'] : null,
                            'groupcarabayar_id'=> array_key_exists('carabayar_pilih', $post) ? $post['carabayar_pilih'] : null,
                            'tgl_antrian' => date('Y-m-d H:i:s'),
                            'no_antrian' => '000',
                            'jenisantrian_id' => $jenisantrian_id,
                            'status_pasien' => array_key_exists('status_pilih', $post) ? $post['status_pilih'] : null,
                            'status_antrian' => 0,
                            'konfigantrian_id' => $konfigantrian_id,
                            'jenisantriandetail_id' => empty($post['jenisantriandetail_id']) ? null : $post['jenisantriandetail_id']
                        ];
                    } else {
                        $arr_data_antrian = [
                            'ruangan_id' => $post['poly_pilih'],
                            'klasifikasipasien_id'=>array_key_exists('klasifikasi_pilih', $post) ? $post['klasifikasi_pilih'] : null,
                            'groupcarabayar_id'=> array_key_exists('carabayar_pilih', $post) ? $post['carabayar_pilih'] : null,
                            'tgl_antrian' => date('Y-m-d H:i:s'),
                            'no_antrian' => '000',
                            'jenisantrian_id' => $jenisantrian_id,
                            'status_pasien' => array_key_exists('status_pilih', $post) ? $post['status_pilih'] : null,
                            'pegawai_id' => $post['dokter_pilih'],
                            'status_antrian' => 0,
                            'jadwaldokter_id' => $post['jadwaldokter_pilih'],
                            'konfigantrian_id' => $konfigantrian_id,
                            'jenisantriandetail_id' => empty($post['jenisantriandetail_id']) ? null : $post['jenisantriandetail_id']
                        ];
                    }

                    if(isset($post['pasien_id']) && strlen($post['pasien_id'])){
                        $arr_data_antrian['pasien_id'] = $post['pasien_id'];
                    }elseif (!in_array($request->post('pasienEnc', null), ['', null])){
                        $arr_data_antrian['pasien_id'] = DocoHelpers::decrypt($request->post('pasienEnc'));
                    }
                    break;
                case 178 :
                    $arr_data_antrian = [
                        'ruangan_id' => 0,
                        'tgl_antrian' => date('Y-m-d H:i:s'),
                        'no_antrian' => '000',
                        'jenisantrian_id' => $post['id_jenis_antrian'],
                        'status_antrian' => 0,
                        'status_pasien' => 0,
                    ];
                    break;
                case 176 :
                    $arr_data_antrian = [
                        'ruangan_id' => $post['poly_pilih'],
                        'tgl_antrian' => date('Y-m-d H:i:s'),
                        'no_antrian' => '000',
                        'jenisantrian_id' => $jenisantrian_id,
                        'fungsiantrian_id' => $post['fungsiantrian_id'],
                        'instalasi_id' => $post['instalasi_id'],
                        'status_pasien' => 0,
                        'status_antrian' => 0
                    ];

                    break;
                case 179:
                $model_konfig = KonfigAntrian::find();
                $model_konfig->andWhere(['jenisantrian_id' => $jenisantrian_id]);
                $model_konfig->andWhere(['ruangan_id' => $post['instalasi_id']]);
                $konfig_antrian = $model_konfig->one();
                $konfigantrian_id = $konfig_antrian['konfigantrian_id'];
                    $arr_data_antrian = [
                        'ruangan_id' => $post['poly_pilih'],
                        'tgl_antrian' => date('Y-m-d H:i:s'),
                        'no_antrian' => '000',
                        'jenisantrian_id' => $jenisantrian_id,
                        'status_antrian' => 0,
                        'instalasi_id' => $post['instalasi_id']
                    ];
                    break;
                default:
                    $arr_data_antrian = [
                        'ruangan_id' => 0,
                        'tgl_antrian' => date('Y-m-d H:i:s'),
                        'no_antrian' => '000',
                        'status_antrian' => 0,
                        'jenisantrian_id' => 0
                    ];
                    break;
            }

            // penambahan kondisi khusus farmasi
            // if ($jenisantrian_id == 176) {
            //     $data_cetak = empty($post['cetak-antrian']) ? [] : $post['cetak-antrian'];
            //     $no_antr = [];
            //     $list_no_antr = [];
            //     $listantrian_id = [];

            //     foreach ($data_cetak as $key => $value) {
            //         $mAntrian = new Antrian;
            //         $data_resep = InfoReseptur::findOne(['reseptur_id' => $value]);
            //         if ($data_resep) {
            //             if ($data_resep->antrian_racikan == "2") {
            //                 $fungsiantrian_id_apotek = DocoConstants::ANT_NR;
            //             } else {
            //                 $fungsiantrian_id_apotek = DocoConstants::ANT_R;
            //             }

            //             $arr_data_antrian = [
            //                 'instalasi_id' => $data_resep->instalasi_tujuan_id,
            //                 'ruangan_id' => $data_resep->ruangan_id,
            //                 'tgl_antrian' => date('Y-m-d H:i:s'),
            //                 'no_antrian' => '000',
            //                 'jenisantrian_id' => $jenisantrian_id,
            //                 'fungsiantrian_id' => $fungsiantrian_id_apotek,
            //                 'status_pasien' => 0,
            //                 'status_antrian' => 0
            //             ];

            //             $mAntrian->attributes = $arr_data_antrian;

            //             if(!$mAntrian->validate()){
            //                 throw new \yii\db\Exception('Gagal Validasi Antrian', $mAntrian->getErrors(),500);
            //             }

            //             if(!$mAntrian->save()){
            //                 throw new \yii\db\Exception('Gagal Simpan Antrian', $mAntrian->getErrors(),500);
            //             }

            //             $saveAntrian = Antrian::findOne($mAntrian->getPrimaryKey());

            //             $dataReseptur = Reseptur::findOne($data_resep->reseptur_id);
            //             $dataReseptur->antrian_id = $mAntrian->getPrimaryKey();

            //             if(!$dataReseptur->save()){
            //                 throw new \yii\db\Exception('Gagal Simpan Antrian', $mAntrian->getErrors(),500);
            //             }

            //             if(isset($saveAntrian->no_antrian)){
            //                 $no_antr[] = [
            //                                 'jenis' => $data_resep->ruangan_tujuan,
            //                                 'no_antrian' => $saveAntrian->no_antrian
            //                             ];

            //                 $list_no_antr [] = $saveAntrian->no_antrian;
            //                 $listantrian_id [] = $saveAntrian->antrian_id;
            //             }

            //             unset($mAntrian);
            //         }

            //     }

            //     $transaction->commit();
            //     return [
            //         'message'=>'Proses Berhasil!',
            //         'text' => 'Antrian '.implode(",",$list_no_antr).' Dicetak',
            //         'data' => [
            //             'no_antrian' => $no_antr,
            //             'jenisantrian_id' => $jenisantrian_id,
            //             'antrian_id' => $listantrian_id
            //         ]
            //     ];

            // } else {
                $mAntrian->attributes = $arr_data_antrian;

                if(!$mAntrian->validate()){
                    throw new \yii\db\Exception('Gagal Validasi Antrian', $mAntrian->getErrors(),500);
                }

                if(!$mAntrian->save()){
                    throw new \yii\db\Exception('Gagal Simpan Antrian', $mAntrian->getErrors(),500);
                }

                if ($mAntrian->jenisantrian_id == DocoConstants::VAR_JA_PD) {
                    $modelAntrian = new Antrian();
                    $id = [];

                    if (!$modelAntrian->load($arr_data_antrian, '')) {
                        throw new \yii\db\Exception('Gagal Simpan Antrian.', $modelAntrian->getErrors(), 500);
                    }

                    if (!$modelAntrian->validate()) {
                        throw new \yii\db\Exception('Gagal Simpan Antrian.', $modelAntrian->getErrors(), 500);
                    }

                    $model_konfig = KonfigAntrian::find()
                            ->andWhere(['jenisantrian_id' => DocoConstants::VAR_JA_P])
                            ->one();

                    $modelAntrian->jenisantrian_id = DocoConstants::VAR_JA_P;
                    $modelAntrian->konfigantrian_id = !empty($model_konfig->konfigantrian_id) ? $model_konfig->konfigantrian_id : null;
                    // set antrianasal_id from antrian pendaftaran (rizal)
                    $modelAntrian->antrianasal_id = $mAntrian->getPrimaryKey();

                    // todo konfigantrian (aris)
                    $model_konfig = KonfigAntrian::find();
                    $model_konfig->andWhere(['jenisantrian_id' => $modelAntrian->jenisantrian_id]);
                    // $model_konfig->andWhere(['groupcarabayar_id' => $modelAntrian->groupcarabayar_id]);
                    $konfig_antrian = $model_konfig->one();
                    $konfigantrian_id = $konfig_antrian['konfigantrian_id'];
                    // end todo konfigantrian (aris)

                    $modelAntrian->konfigantrian_id = $konfigantrian_id;
                    $modelAntrian->is_active = false;

                    /** Get Slot Sequence */
                    $date = date('Y-m-d');
                    $time = date('H:i', strtotime('NOW'));
                    $jadwaldokter_id = ArrayHelper::getValue($post, 'jadwaldokter_pilih');
                    $getLastSequence = (new PendaftaranOnline)->getLastSequenceJknSameDate($jadwaldokter_id, $date, $time);
                    $slot_sequence = ArrayHelper::getValue($getLastSequence, 'slot_sequence');
                    $modelAntrian->slot_sequence = $slot_sequence;

                    if (!$modelAntrian->save()) {
                        throw new \yii\db\Exception('Gagal Simpan Antrian.', $modelAntrian->getErrors(), 500);
                    }

                    $no_antr_pendaftaran = '';
                    $no_antr_poli = '';
                    $antrianPendaftaran = Antrian::findOne($mAntrian->getPrimaryKey());
                    $antrianPoli = Antrian::findOne($modelAntrian->getPrimaryKey());
                    if(isset($antrianPendaftaran->no_antrian)){
                        $no_antr_pendaftaran = $antrianPendaftaran->no_antrian;
                    }
                    if(isset($antrianPoli->no_antrian)){
                        $no_antr_poli = $antrianPoli->no_antrian;
                    }

                    $id[] = $mAntrian->getPrimaryKey();
                    $id[] = $modelAntrian->getPrimaryKey();

                    // panggil antrian
                    $data = DocoAntrian::getSisaAntrian($jenisantrian_id);
                    $dataSisaAntrian['list_sisa_antrian'] = $data;
                    $mode = Yii::$app->params['mode'];
                    Yii::$app->redis->executeCommand('PUBLISH', [
                        'channel' => 'panggil-antrian-'.$mode,
                        'message' => json_encode(['data' => $dataSisaAntrian])
                    ]);

                    $transaction->commit();

                    $dataAntrian = $this->getDataCetakAntrian($id,$is_carabayar_bpjs);
                    $dataAntrianPoli = $this->getDataAntrianPoli();

                    return [
                        'message'=>'Proses Berhasil!',
                        'text' => 'Antrian '.((in_array($request->post('pasienEnc', null), ['', null])) ? $no_antr_pendaftaran : "").' Dicetak',
                        'data' => [
                            'no_antrian' => $no_antr_pendaftaran,
                            'no_antrian_poli' => $no_antr_poli,
                            'jenisantrian_id' => $jenisantrian_id,
                            'jenisantrian_poli_id' => $antrianPoli->jenisantrian_id,
                            'antrian_id'=>$mAntrian->getPrimaryKey(),
                            'antrian_poli_id'=>$modelAntrian->getPrimaryKey(),
                            'cetak' => $dataAntrian,
                            'list_antrian_poli' => $dataAntrianPoli, // penambahan return data poli hari : ali
                            'antrian_jenis_id' => $request->post('antrian_jenis_id'),
                        ]
                    ];
                } else {
                    $no_antr = '';
                    $saveAntrian = Antrian::findOne($mAntrian->getPrimaryKey());
                    if(isset($saveAntrian->no_antrian) && in_array($request->post('pasienEnc', null), ['', null]) ){ // kalo pasien baru, cetak no antrian pendaftaran, kalo lama tidak usah
                        $no_antr = $saveAntrian->no_antrian;
                    }

                    // antrian farmasi
                    if ($mAntrian->jenisantrian_id == DocoConstants::VAR_JA_F) {
                        $data_antrian = AllowAntrianController::getListAntrianFarmasi($mAntrian->ruangan_id);

                        $data_farmasi = AllowAntrianController::generateAntrianFarmasi($data_antrian);

                        // set ke display antrian
                        $data_display["proses_antrian_farmasi"] = [
                            'ruangan' => $mAntrian->ruangan_id,
                            'data' => [
                                'racikan' => $data_farmasi['racikan'],
                                'non_racikan' => $data_farmasi['non_racikan']
                            ]
                        ];
                        $mode = Yii::$app->params['mode'];
                        Yii::$app->redis->executeCommand('PUBLISH', [
                            'channel' => 'display-antrian-'.$mode,
                            'message' => json_encode(['data' => $data_display])
                        ]);
                        // end set display antrian
                    }
                    // end antrian farmasi

                    $transaction->commit();

                    $dataAntrian = $this->getDataCetakAntrian($mAntrian->getPrimaryKey(),$is_carabayar_bpjs);
                    return [
                        'message'=>'Proses Berhasil!',
                        'text' => 'Antrian '.$no_antr.' Dicetak',
                        'data' => [
                            'no_antrian' => $no_antr,
                            'jenisantrian_id' => $jenisantrian_id,
                            'antrian_id'=>$mAntrian->getPrimaryKey(),
                            'cetak' => $dataAntrian,
                            'antrian_jenis_id' => $request->post('antrian_jenis_id'),
                        ]
                    ];
                }
            // }

        } catch(\yii\db\Exception $e){
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'text' => 'Gagal Validasi Data',
                'errorInfo'=> $e->errorInfo
            ];
        } catch(\yii\base\ErrorException $e){
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage(),
                'text' => 'Kesalahan internal',
                'errorInfo'=>$e->getName()
            ];
        } catch(\yii\base\Exception $e){
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage(),
                'text' => 'Kesalahan internal',
                'errorInfo'=>$e->getName()
            ];
        }
    }

    public function actionCreateAntrianV2()
    {
        $request = Yii::$app->request;
        $post = $request->post();

        /** Model Antrian Pendaftaran */
        $mAntrian = new Antrian;

        /** Define Variable */
        $no_antr_pendaftaran = '';
        $statuspasien = ArrayHelper::getValue($post,'statuspasien');
        $antrian_jenis_id = ArrayHelper::getValue($post, 'antrian_jenis_id');
        $carabayar_id = ArrayHelper::getValue($post, 'carabayar_id');

        $transaction = $mAntrian->getDb()->beginTransaction();
        try{
            if(!isset($post) || empty($post)){
                throw new \yii\base\ErrorException("Data Tidak Ditemukan", 500);
            }
            if(!isset($post['jenisantrian_id'])){
                throw new \yii\base\ErrorException("Jenis Antrian Tidak Ditemukan", 500);
            }

            $data_antrian = [
                'ruangan_id' => ArrayHelper::getValue($post, 'ruangan_id'),
                'carabayar_id' => ArrayHelper::getValue($post, 'carabayar_id'),
                'tgl_antrian' => date('Y-m-d H:i:s'),
                'no_antrian' => '000',
                'jenisantrian_id' => DocoConstants::VAR_JA_PD,
                'status_antrian' => DocoConstants::ANTRIAN_STATUS_BELUM_PANGGIL,
                'status_pasien' => isset($statuspasien) && !empty($statuspasien) ? $statuspasien : DocoConstants::VAR_PAS_B,
            ];

            $mAntrian->attributes = $data_antrian;

            // set groupcarabayar dengan parameter carabayar diluar value dococonstant
            $carabayar = Cache::getCaraBayar();
            $carabayar = DocoHelpers::filterArray($carabayar, 'carabayar_id', $carabayar_id);
            $groupcarabayar_id = ArrayHelper::getValue($carabayar, 'groupcarabayar_id');

            $lookup_jenisantrian = DocoCache::getLookupByType('jenis_antrian'); // cache key => 'var_cache_lookup_by_type-jenis_antrian'
            $lookup_jenisantrian = DocoHelpers::filterArray($lookup_jenisantrian, 'lookup_id', $antrian_jenis_id);
            $klasifikasipasien_id = DocoHelpers::getVariableFromAdditionalData(ArrayHelper::getValue($lookup_jenisantrian, 'additional_data', null), 'klasifikasipasien_id', true);

            $konfig_antrian = KonfigAntrian::find()->andWhere([
                'jenisantrian_id' => DocoConstants::VAR_JA_PD
            ]);

            if ($groupcarabayar_id) {
                $konfig_antrian = $konfig_antrian->andWhere(['groupcarabayar_id'=> $groupcarabayar_id]);
            } else {
                throw new \yii\base\ErrorException("Cara Bayar Tidak Ditemukan", 500);
            }

            if ($klasifikasipasien_id) {
                $konfig_antrian = $konfig_antrian->andWhere(['klasifikasipasien_id'=> $klasifikasipasien_id]);
            }

            if(isset($statuspasien) && !empty($statuspasien)){
                $konfig_antrian->andWhere(['statuspasien'=>$statuspasien]);
            }

            $konfig_antrian = $konfig_antrian->orderBy(['konfigantrian_id' => SORT_ASC])->one();
            if(empty($konfig_antrian)){
                throw new \yii\base\ErrorException("Konfig Antrian Tidak Ditemukan", 500);
            }

            $mAntrian->konfigantrian_id = ArrayHelper::getValue($konfig_antrian, 'konfigantrian_id');

            if(!$mAntrian->validate()){
                throw new \yii\db\Exception('Gagal Validasi Antrian', $mAntrian->getErrors(),500);
            }

            if(!$mAntrian->save()){
                throw new \yii\db\Exception('Gagal Simpan Antrian', $mAntrian->getErrors(),500);
            }

            $antrianPendaftaran = Antrian::findOne($mAntrian->getPrimaryKey());
            if(isset($antrianPendaftaran->no_antrian)){
                $no_antr_pendaftaran = $antrianPendaftaran->no_antrian;
            }

            $antrianPoli = Antrian::find()->where(['antrianasal_id' => $antrianPendaftaran->antrian_id]);
            $no_antr_poli = "-";
            if(isset($antrianPoli->no_antrian)){
                $no_antr_poli = $antrianPendaftaran->no_antrian;
            }

            $id = $mAntrian->getPrimaryKey();

            // panggil antrian
            $data = DocoAntrian::getSisaAntrian(DocoConstants::VAR_JA_PD);
            $dataSisaAntrian['list_sisa_antrian'] = $data;
            $mode = Yii::$app->params['mode'];
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'panggil-antrian-'.$mode,
                'message' => json_encode(['data' => $dataSisaAntrian])
            ]);

            $transaction->commit();

            $dataAntrian = $this->getDataCetakAntrian($id, ($groupcarabayar_id == DocoConstants::GROUP_BPJS) ? true : false);
            return [
                'message'=>'Proses Berhasil!',
                'text' => 'Antrian '.$no_antr_poli.' Dicetak',
                'data' => [
                    'no_antrian' => $no_antr_pendaftaran,
                    'no_antrian_poli' => $no_antr_poli,
                    'jenisantrian_id' => DocoConstants::VAR_JA_PD,
                    'antrian_id'=>$mAntrian->getPrimaryKey(),
                    'cetak' => $dataAntrian,
                ]
            ];
        } catch(\yii\db\Exception $e){
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'text' => 'Gagal Validasi Data',
                'errorInfo'=> $e->errorInfo
            ];
        } catch(\yii\base\ErrorException $e){
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage(),
                'text' => 'Kesalahan internal',
                'errorInfo'=>$e->getName()
            ];
        } catch(\yii\base\Exception $e){
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage(),
                'text' => 'Kesalahan internal',
                'errorInfo'=>$e->getName()
            ];
        }
    }

    public function actionCariPasien($no_rm)
    {
        $data = Pasien::find()->where(['no_rekam_medik'=>$no_rm])->one();
        return ['data'=>$data];
    }

    public function actionViewKonfigAntrian($id)
    {
        $filter = ['t.konfigantrian_id' => $id];
        return $this->getDataKonfigAntrian($filter)->one();
    }

    public function actionKonfigAntrian()
    {
        try {
            $request = Yii::$app->request;
            $result = $this->getDataKonfigAntrian()
                            ->limit($request->post('length',10))
                            ->offset($request->post('start',0));

            if ($indexing = $request->post('konfigantrian')) {
                $result->andFilterWhere(['ILIKE', 'konfigantrian_m.layarantrian_nama', $indexing]);
            }

            $layarantrian_id = $request->post('layarantrian_id');
            if($layarantrian_id) {
                $result->andWhere(['t.layarantrian_id' => $layarantrian_id]);
            }

            $status = ($request->post('is_active') ? $request->post('is_active') : true);
            // if($status) {
                $status = $status ? true : false;
                $result->andWhere(['t.is_active' => $status]);
            // }

            // $status = $request->post('is_active');
            // $status = $status ? true : false;

            // $result->andWhere(['t.is_active' => $status]);

            if ($order = $request->post('orderby')) {
                $dir = (int) $request->post('dir');
                $result->orderby([$order => $dir]);
            }

            return [
                'data' => $result->all(),
                'count' => $result->count()
            ];
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

    private function getDataKonfigAntrian($filter = null)
    {
        $returnData = (new \yii\db\Query())
                        ->select([
                                't.konfigantrian_id',
                                't.layarantrian_id',
                                't.fungsiantrian_id',
                                't.kode_antrian',
                                't.additional_data',
                                't.created_date',
                                't.created_by',
                                't.modified_count',
                                't.last_modified_date',
                                't.last_modified_by',
                                't.is_deleted',
                                't.is_active',
                                't.is_default',
                                'lookupjenis.lookup_name as jenis_name',
                                'lookupjenis.lookup_value as jenis_value',
                                'lookupfungsi.lookup_name as fungsi_name',
                                'lookupfungsi.lookup_value as fungsi_value'])->from('konfigantrian_m t')
                        ->join('JOIN','layarantrian_m ly','ly.layarantrian_id = t.layarantrian_id')
                        ->join('JOIN', 'lookup_m lookupjenis','lookupjenis.lookup_id = ly.jenisantrian_id')
                        ->join('JOIN', 'lookup_m lookupfungsi','lookupfungsi.lookup_id = t.fungsiantrian_id')
                        ->orderBy([ 't.konfigantrian_id' => SORT_ASC ]);

        if ($filter) {
            if(is_array($filter))
            {
                foreach ($filter as $key => $value) {
                    $returnData->andWhere([$key => $value]);
                }
            }
        }

        return $returnData;
    }

    protected function getKonfigAntrianView()
    {
        // try {
        $request = Yii::$app->request;
        $model = new KonfigAntrianView;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query;
        // } catch (\yii\db\Exception $e) {
        //     \Yii::$app->response->statusCode = 500;
        //     return [
        //         'message' => $e->getMessage()
        //     ];
        // } catch (\Exception $e) {
        //     \Yii::$app->response->statusCode = 500;
        //     return [
        //         'message' => $e->getMessage()
        //     ];
        // }
    }

    public function actionGetKonfigAntrianById($id)
    {
        $query = $this->getKonfigAntrianView();
        return $query->where(['konfigantrian_id'=>$id])->all();
    }

    public function actionGetKonfigAntrianByLayarId($layar_id)
    {
        $query = $this->getKonfigAntrianView();
        return $query->where(['layarantrian_id'=>$layar_id])->asArray()->all();
    }

    public function actionLayarAntrian()
    {
        return [
            'data'=>$this->getDataLayarAntrian()->andWhere(['t.is_deleted'=>false,'t.is_active'=>true])->all()
        ];
    }

    public function actionIndexDashboard()
    {
        try{
            return [
                'data-layar' => $this->getDataLayarAntrian()->andWhere(['is_deleted'=>false,'is_active'=>true])->all(),
                'list-jenis' => $this->listJenisAntrian(),
                'konfig-system' => $this->actionGetKonfigSystem()
            ];
        } catch(Exception $e){
            return ['data'=>null,'errorMessage'=>$e->getMessage()];
        }
    }

    private function listJenisAntrian()
    {
        try {
            $result = Lookup::find();

            $result->andWhere(['lookup_type' => 'jenis_antrian']);
            $result->andWhere(['is_active' => TRUE]);

            return $result
                    ->select([
                        'jenisantrian_id'=>'lookup_id',
                        'jenisantrian_nama'=>'lookup_name'
                    ])
                    ->asArray()->all();
        } catch (Exception $e) {
            return ['message'=>'Kesalahan Internal'];
        }
    }

    public function actionLayarDashboard()
    {
        try {
            $request = Yii::$app->request;
            $id = $request->get('id');
            $data_layar = $this->actionViewLayarAntrian($id);
            $data_loket = $this->getDataLoket(['t.layarantrian_id' => $id]);
            $ruangan_id = empty($data_loket[0]['ruangan_id']) ? null : $data_loket[0]['ruangan_id'];
            $konfig_layar = $this->getKonfigLayarAntrian();

            if ($data_layar['jenisantrian_id'] == DocoConstants::VAR_JA_F) {
                $data_antrian_farmasi = AntrianView::getAntrianFarmasi($ruangan_id); //diganti pake function dari antrian view, karena yang sebelumnya salah ambil racikan_id + sudah di optimize
                $data_antrian = AllowAntrianController::generateAntrianFarmasiV2($data_antrian_farmasi);
            } else {
                $data_antrian = $this->getDataAntrianPoli();
            }

            // $trx_antrian = $this->getDataAntrian();
            // $data_jumlah_antrian = $trx_antrian->count();
            // $data_sisa_antrian = $this->getDataAntrian(['antrian_t.panggil_flag' => FALSE])->count();

            return [
                'info-layar' => $data_layar,
                'list-loket' => $data_loket,
                'list-antrian' => $data_antrian,
                'konfig-layar' => $konfig_layar,
                'konfig_jenisantriandetail' => (new DocoConstansId)->actionGetId('konfig_antrian_using_jenisantriandetail'),
                'ruangan_id' => $ruangan_id
                // 'jumlah-antrian' => $data_jumlah_antrian,
                // 'sisa-antrian' => $data_sisa_antrian
            ];
        } catch (Exception $e) {
            return ['message'=>'Kesalahan Internal'];
        }
    }

    public function actionGetDataAntrianFarmasi()
    {
        try {
            $request = Yii::$app->request;
            $ruangan_id = $request->get('id');
            // $data_antrian_farmasi = AllowAntrianController::getListAntrianFarmasi($ruangan_id); //hardcode
            $data_antrian_farmasi = AntrianView::getAntrianFarmasi($ruangan_id);
            $data_antrian = AllowAntrianController::generateAntrianFarmasiV2($data_antrian_farmasi);
            return [
                'list-antrian' => $data_antrian
            ];
        } catch (Exception $e) {
            return ['message'=>'Kesalahan Internal'];
        }
    }

    public function actionDeleteAntrianPoli($id = null)
    {
        if(empty($id)){
            return ['message'=>'Gagal'];
        }
        
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $antrian = Antrian::find()->where(['antrian_id'=>$id])->one();

        if(empty($antrian)){
            $transaction->rollback();
            return ['message'=>'Gagal'];
        }
        
        if(!empty($antrian->antrianasal_id)){
            $antrianAsal = Antrian::find()->where(['antrian_id'=>$antrian->antrianasal_id])->one();
            if(!empty($antrianAsal)){
                $antrianAsal->no_antrian = null;
                $antrianAsal->save(false);
            }
            if(!$antrianAsal->delete()){
                $transaction->rollback();
                return ['message'=>'Gagal'];
            }
        }

        $antrian->no_antrian = null;
        $antrian->save(false);
        if(!$antrian->delete()){
            $transaction->rollback();
            return ['message'=>'Gagal'];
        }

        $transaction->commit();
        return ['message'=>'Sukses'];
    }

    public function actionTransaksiAntrian()
    {
        try {
            $request = Yii::$app->request;
            $result = $this->getDataAntrian()
                            ->limit($request->post('length',10))
                            ->offset($request->post('start',0));

            if ($indexing = $request->post('antrian')) {
                $result->andFilterWhere(['ILIKE', 'loket_nama', $indexing]);
            }

            $layarantrian_id = $request->post('layarantrian_id');
            if($layarantrian_id) {
                $result->andWhere(['antrian_t.layarantrian_id' => $layarantrian_id]);
            }

            $loket_id = $request->post('loket_id');
            if($loket_id) {
                $result->andWhere(['antrian_t.loket_id' => $loket_id]);
            }

            $panggil_flag = $request->post('panggil_flag');
            if($panggil_flag !== null) {
                $panggil_flag = $panggil_flag === '0' ? false : true;
                $result->andWhere(['antrian_t.panggil_flag' => $panggil_flag]);
            }


            // $status = $request->post('is_active');
            // if($status === null) {
            //     $status = $status ? true : false;
            //     $result->andWhere(['antrian_t.is_active' => $status]);
            // }

            if ($order = $request->post('orderby')) {
                $dir = (int) $request->post('dir');
                $result->orderby([$order => $dir]);
            }

            return [
                'data' => $result->asArray()->all(),
                'count' => $result->count()
            ];
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

    private function getDataAntrian($filter = null)
    {
        $antrian = Antrian::find()
                        ->select([
                                'antrian_t.antrian_id',
                                'antrian_t.ruangan_id',
                                'antrian_t.carabayar_id',
                                'antrian_t.pendaftaran_id',
                                'antrian_t.layarantrian_id',
                                'antrian_t.loket_id',
                                'antrian_t.tgl_antrian',
                                'antrian_t.no_antrian',
                                'antrian_t.status_pasien',
                                'antrian_t.carabayar_loket',
                                'antrian_t.panggil_flag',
                                'antrian_t.is_active'
                        ]);
        if ($filter) {
            if(is_array($filter))
            {
                foreach ($filter as $key => $value) {
                    $antrian->andWhere([$key => $value]);
                }
            }

            // else {
            //     $antrian->where(['antrian_id'=>$filter]);
            // }

        }

        return $antrian;
    }

    public function actionViewLayarAntrian($id)
    {
        return $this->getDataLayarAntrian($id)->one();
    }

    private function getDataLayarAntrian($id = null)
    {
        $returnData = LayarAntrianView::find()->orderBy(['layarantrian_nama' => SORT_ASC]);

        if ($id) {
            $returnData->where(['layarantrian_id' => $id]);
        }

        return $returnData;
    }

    private function getDataLoket($arr_data = null)
    {
        // $returnData = (new \yii\db\Query())
        //                 ->select([
        //                         't.loket_id',
        //                         't.carabayar_id',
        //                         't.layarantrian_id',
        //                         't.loket_nama',
        //                         't.loket_namalain',
        //                         't.loket_fungsi',
        //                         't.loket_singkatan',
        //                         't.loket_nourut',
        //                         't.loket_formatnomor',
        //                         't.loket_maxantrian',
        //                         't.filesuara',
        //                         't.is_pendaftaran',
        //                         't.is_kasir',
        //                         't.additional_data',
        //                         't.is_active',
        //                         't.is_deleted',
        //                         'lookupjenis.lookup_id as lookupJenisId',
        //                         'lookupjenis.lookup_name as jenis_name',
        //                         'lookupjenis.lookup_value as jenis_value',
        //                         // 'lookupfungsi.lookup_name as fungsi_name',
        //                         // 'lookupfungsi.lookup_value as fungsi_value'
        //                     ])->from('loket_m t')
        //                 ->join('JOIN','layarantrian_m ly','ly.layarantrian_id = t.layarantrian_id')
        //                 ->join('JOIN', 'lookup_m lookupjenis','lookupjenis.lookup_id = ly.jenisantrian_id')
        //                 // ->join('JOIN', 'loket_mp lk', 'lk.loket_id = t.loket_id')
        //                 // ->join('JOIN', 'lookup_m lookupfungsi','lookupfungsi.lookup_id = t.fungsiantrian_id')
        //                 ->orderBy([ 't.loket_id' => SORT_ASC ])
        //                 ->where([ 't.is_deleted' => false ]);

        // $returnData = (new \yii\db\Query())
        //                 ->select([
        //                         't.loket_id',
        //                         'lm.loket_nama',
        //                         'lm.loket_namalain',
        //                         'lm.loket_nourut',
        //                         'lm.ruangan_id'
        //                     ])->from('layarantriandetail_m t')
        //                 ->join('JOIN','loket_m lm','lm.loket_id = t.loket_id')
        //                 ->groupBy(['t.loket_id','lm.loket_nama','lm.loket_namalain','lm.loket_nourut','lm.ruangan_id'])
        //                 // ->orderBy([ 't.loket_id' => SORT_ASC ])
        //                 ->where([ 't.is_deleted' => false, 't.is_active' => true ])
        //                 ->where([ 'lm.is_deleted' => false, 'lm.is_active' => true ]);
        //                 // ->where([ 't.is_deleted' => false ]);
        //
        //
        //                                               //

        $extend_query = "";
        if ($arr_data) {
            foreach ($arr_data as $key => $value) {
                $extend_query = " and ".$key." = ".$value;
            }
        }

        $sql = "
                SELECT
                    t.loket_id,
                    lm.loket_nama,
                    lm.loket_namalain,
                    lm.loket_nourut,
                    lm.ruangan_id
                from
                    layarantriandetail_m t
                inner join
                    loket_m lm on lm.loket_id = t.loket_id
                where
                    t.is_active = true and t.is_deleted = false and lm.is_active = true and lm.is_deleted = false
                ".$extend_query."
                group by
                    t.loket_id,lm.loket_nama,lm.loket_namalain,lm.loket_nourut,lm.ruangan_id
                order by lm.loket_nourut
                    ";


        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public function actionListPolyAntrian()
    {
        $date_now = DocoHelpers::getTanggalIndonesia(date('Y-m-d'));

        $jam = date('H:i:s');

        // Get hari from lookup
        $modelLookup = Lookup::find()->where(['lookup_value' => $date_now['hari']])->one();

        // Check model lookup
        if ($modelLookup != null) {
            // Assign hari
            $hari = $modelLookup->lookup_id;
        }
        else {
            $hari = 0;
        }

        $sql = "
            SELECT
                ruangan.ruangan_id,
                ruangan.ruangan_nama,
                ruangan.is_active,
                jadwal_ruangan.jam_mulai,
                jadwal_ruangan.jam_tutup,
                jadwal_ruangan.maxantiran_poli,
                CASE
            WHEN jadwal_ruangan.jadwalbukapoli_id IS NOT NULL THEN
                TRUE
            ELSE
                FALSE
            END AS is_buka,
             jadwal_ruangan_dokter.sisa_kuota AS sisa_kuota
            FROM
                ruangan_m ruangan
            LEFT JOIN (
                SELECT
                    jadwal_buka_poli.jadwalbukapoli_id,
                    jadwal_buka_poli.ruangan_id,
                    jadwal_buka_poli.jam_mulai,
                    jadwal_buka_poli.jam_tutup,
                    jadwal_buka_poli.maxantiran_poli
                FROM
                    jadwalbukapoli_m jadwal_buka_poli
                WHERE
                    jadwal_buka_poli.is_deleted = FALSE
                AND jadwal_buka_poli.hari = '{$hari}'
                AND jadwal_buka_poli.jam_mulai :: TIME < now() :: TIME
                AND jadwal_buka_poli.jam_tutup :: TIME >= now() :: TIME
            ) jadwal_ruangan ON jadwal_ruangan.ruangan_id = ruangan.ruangan_id
            LEFT JOIN (
                SELECT
                    jadwal_dokter.ruangan_id,
                    SUM (jadwal_kuota.sisa_kuota) AS sisa_kuota
                FROM
                    jadwaldokter_m jadwal_dokter
                LEFT JOIN (
                    SELECT
                        kuota_dokter.jadwaldokter_id,
                        SUM (
                            kuota_dokter.kuota_tersedia
                        ) AS sisa_kuota
                    FROM
                        kuotadokter_r kuota_dokter
                    GROUP BY
                        kuota_dokter.jadwaldokter_id
                ) jadwal_kuota ON jadwal_kuota.jadwaldokter_id = jadwal_dokter.jadwaldokter_id
                WHERE
                    jadwal_dokter.is_deleted = FALSE
                AND jadwal_dokter.jadwaldokter_tutup :: TIME >= now() :: TIME
                GROUP BY
                    jadwal_dokter.ruangan_id
            ) jadwal_ruangan_dokter ON jadwal_ruangan_dokter.ruangan_id = ruangan.ruangan_id
            WHERE
                ruangan.is_deleted = FALSE
            AND ruangan.instalasi_id = 1
            GROUP BY
                ruangan.ruangan_id,
                jadwal_ruangan.jam_mulai,
                jadwal_ruangan.jam_tutup,
                jadwal_ruangan.maxantiran_poli,
                jadwal_ruangan.jadwalbukapoli_id,
                jadwal_ruangan_dokter.sisa_kuota
        ";

        // return $sql;

        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public function actionListPolyDokter($id, $jadwalbukapoli_id)
    {
        // $date_now = DocoHelpers::getTanggalIndonesia(date('Y-m-d'));
        // $jam = date('H:i:s');

        // // Get hari from lookup
        // $modelLookup = Lookup::find()->where(['lookup_value' => $date_now['hari']])->one();

        // // Check model lookup
        // if ($modelLookup != null) {
        //     // Assign hari
        //     $hari = $modelLookup->lookup_id;
        // }
        // else {
        //     $hari = 0;
        // }

        // $sql = "
        //     SELECT
        //         dok.pegawai_id,
        //         gelardepan.lookup_name AS gelardpn,
        //         dok.nama_pegawai,
        //         gelarbelakang.gelarbelakang_nama,
        //         CONCAT (
        //             gelardepan.lookup_name,
        //             ' ',
        //             dok.nama_pegawai,
        //             ' ',
        //             gelarbelakang.gelarbelakang_nama
        //         ) AS nama_pegawai_lengkap,
        //         peg_jadwal.jadwaldokter_mulai,
        //         peg_jadwal.is_active,
        //         peg_jadwal.jadwaldokter_tutup,
        //         peg_jadwal.jadwaldokter_id,
        //         CASE WHEN peg_jadwal.jadwaldokter_id IS NOT NULL THEN TRUE ELSE FALSE END AS is_buka,
        //         peg_jadwal.sisa_kuota AS sisa_kuota
        //     FROM dokter_v dok
        //     LEFT JOIN lookup_m gelardepan ON dok.gelardepan :: INTEGER = gelardepan.lookup_id
        //     AND lookup_type = 'gelar_depan'
        //     LEFT JOIN gelarbelakang_m gelarbelakang ON dok.gelarbelakang :: INTEGER = gelarbelakang.gelarbelakang_id
        //     LEFT JOIN (
        //         SELECT
        //             jadwal_dokter.jadwaldokter_id,
        //             jadwal_dokter.ruangan_id,
        //             jadwal_dokter.pegawai_id,
        //             jadwal_dokter.is_active,
        //             jadwal_dokter.jadwaldokter_mulai,
        //             jadwal_dokter.jadwaldokter_tutup,
        //             SUM (jadwal_kuota.sisa_kuota) AS sisa_kuota
        //         FROM
        //             jadwaldokter_m jadwal_dokter
        //         LEFT JOIN (
        //             SELECT
        //                 kuota_dokter.jadwaldokter_id,
        //                 SUM (
        //                     kuota_dokter.kuota_tersedia
        //                 ) AS sisa_kuota
        //             FROM
        //                 kuotadokter_r kuota_dokter
        //             GROUP BY
        //                 kuota_dokter.jadwaldokter_id
        //         ) jadwal_kuota ON jadwal_kuota.jadwaldokter_id = jadwal_dokter.jadwaldokter_id
        //         LEFT JOIN jadwalbukapoli_m bukapoli ON bukapoli.jadwalbukapoli_id = jadwal_dokter.jadwalbukapoli_id
        //         LEFT JOIN lookup_m hari_lookup ON bukapoli.hari = hari_lookup.lookup_id AND hari_lookup.lookup_type = 'hari'
        //         WHERE
        //             jadwal_dokter.is_deleted = FALSE
        //         AND bukapoli.is_deleted = FALSE
        //         AND hari_lookup.lookup_id = '{$hari}'
        //         AND jadwal_dokter.jadwaldokter_tutup :: TIME >= now() :: TIME
        //         AND jadwal_dokter.ruangan_id = '{$id}'
        //         GROUP BY
        //             jadwal_dokter.ruangan_id,
        //             jadwal_dokter.pegawai_id,
        //             jadwal_dokter.is_active,
        //             jadwal_dokter.jadwaldokter_id,
        //             jadwal_dokter.jadwaldokter_mulai,
        //             jadwal_dokter.jadwaldokter_tutup
        //     ) peg_jadwal ON peg_jadwal.pegawai_id = dok.pegawai_id
        //     WHERE
        //         dok.instalasi_id = 1
        //     AND dok.ruangan_id = '{$id}'
        //     GROUP BY
        //         dok.pegawai_id,
        //         gelardepan.lookup_name,
        //         dok.nama_pegawai,
        //         gelarbelakang.gelarbelakang_nama,
        //         peg_jadwal.jadwaldokter_mulai,
        //         peg_jadwal.is_active,
        //         peg_jadwal.jadwaldokter_tutup,
        //         peg_jadwal.jadwaldokter_id,
        //         peg_jadwal.sisa_kuota
        // ";

        // new concept get poli antrian : ali.padilah@docotel.com
        $payload = new PayloadForm;
        $model_konfig = KonfigSystem::find()->one();
        $this_day = DocoHelpers::getIdHariIni();
        $payload->hari_id = $this_day;
        $payload->ruangan_id = $id;
        $payload->jadwalbukapoli_id = $jadwalbukapoli_id;
        if (!$payload->validate()) {
            return [
                'status' => 422,
                'data' => $payload->errors
            ];
        }
        if (empty($payload->hari_id) || empty($payload->ruangan_id) || empty($payload->jadwalbukapoli_id)) return [];
        // interval berapa jam dokter aktif dari jam sekarang
        $interval = empty($model_konfig->start_antrian) ? 0 : $model_konfig->start_antrian;
        $interval_status = empty($model_konfig->start_antrian_status) ? false : $model_konfig->start_antrian_status;

        $sql = "SELECT
                    CONCAT (
                            gelardepan.lookup_name,
                            ' ',
                            pg.nama_pegawai,
                            ' ',
                            gelarbelakang.gelarbelakang_nama
                        ) AS nama_dokter,
                     a.pegawai_id,a.jadwaldokter_mulai,a.jadwaldokter_tutup,b.jadwaldokter_id,b.kuota_tersedia
                 from
                    cetakjadwaldokter_v a
                 inner join
                    kuotadokter_r b on a.jadwaldokter_id = b.jadwaldokter_id
                 inner join
                    pegawai_m pg on a.pegawai_id = pg.pegawai_id
                 LEFT JOIN lookup_m gelardepan ON pg.gelardepan :: INTEGER = gelardepan.lookup_id
                            AND lookup_type = 'gelar_depan'
                            LEFT JOIN gelarbelakang_m gelarbelakang ON pg.gelarbelakang :: INTEGER = gelarbelakang.gelarbelakang_id
                 where
                    hari_id = {$this_day} and
                    ruangan_id = {$id} and
                    b.is_online = false and
                    a.jadwaldokter_tutup :: TIME >= now() :: TIME AND
                    a.jadwalbukapoli_id = {$jadwalbukapoli_id} and
                    ";

                    if ($interval_status) {
                        $sql .= "jam_mulai :: time <= now() :: time + interval '{$interval} minutes' and ";
                    }

                    $sql .="jam_tutup :: time >= now() :: time
                            ORDER BY pg.nama_pegawai ASC,
                            b.kuota_tersedia DESC
                        ";
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        $pegawaiCuti = (new RegistrationService)->getDokterCuti([
                            'id' => $id,
                            'data' => $data
                        ]);

        return $pegawaiCuti;
    }

    public function actionGetLayarByJenisId($id)
    {
        $query = $this->getKonfigAntrianView();
        if ($id) {
            $query->orderBy(['konfigantrian_id' => SORT_ASC]);
            // $query->orderBy(['fungsi_antrian' => SORT_ASC]);
            $query->where(['jenisantrian_id' => $id, 'is_default' => true]);
        }
        return $query->all();
    }

    protected function getKonfigAntrianFarmasiView()
    {
        $request = Yii::$app->request;
        $model = new KonfigAntrianFarmasiView;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return $query;
    }

    public function actionGetLayarAntrianFarmasi()
    {
        $query = $this->getKonfigAntrianFarmasiView();
        $query->where(['is_default' => true]);
        return $query->all();
    }
    public function actionListRuanganPenunjang($id)
    {
        try{
            $model = Ruangan::find()->select(['ruangan_id','ruangan_nama','is_active'])->where(['instalasi_id'=>$id,'is_deleted'=>false]);
            return [
                'data'=>$model->asArray()->all()
            ];
        } catch(\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    * @controller actionCetakAntrian
    * @attribute #no_antrian# => data no_antrian
    * @attribute #groupcarabayar# => data group cara bayar
    * @attribute #namadokter# => data nama dokter
    * @attribute #status_pasien# => data status pasien
    * @attribute #namaruangan# => data nama ruangan
    * @attribute #norekammedik# => data nomor rekam medik
    * @attribute #namapasien# => data nama pasien
    **/
    public function actionCetakAntrian()
    {
        $request = Yii::$app->request;
        $payload = new PayloadForm;
        $antrian_id = $request->get('antrian_id');
        $payload->antrian_id = $antrian_id;
        if (!$payload->validate()) {
            return [
                'status' => 422,
                'data' => $payload->errors
            ];
        }
        try {
            $where_ = '';
            if ($payload->antrian_id) {
              $where_ = " WHERE antrian_id = '{$payload->antrian_id}'";
            }
            $query = "SELECT * FROM antrian_v $where_";

            $result = Yii::$app->db->createCommand($query)->queryOne();
            // asd
            $print = new DocoPrint();
            $print->attributes = [
                '#no_antrian#' => @$result['no_antrian'],
                '#groupcarabayar#' => @$result['namagroupcarabayar'],
                '#namadokter#' => @$result['nama_pegawai_lengkap'],
                '#status_pasien#' => @$result['stat_pasien'],
                '#namaruangan#' => @$result['ruangan_nama'],
                '#norekammedik#' => @$result['no_rekam_medik'],
                '#namapasien#' => @$result['nama_pasien'],
            ];
            $ret = $print->OutputHtml();

            return $ret;
        } catch(\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    * @controller actionCetakAntrianBpjsOnsite
    * @attribute #no_antrian# => No Antrian
    * @attribute #nama_dokter# => Nama Dokter
    * @attribute #no_antrian_poli# => No Antrian Poli
    * @attribute #date# => Tanggal
    * @attribute #time# => Waktu
    * @attribute #nama_poli# => Nama Poli
    * @attribute #nama_rumah_sakit# => Nama RS
    * @attribute #estimasi_dilayani# => Estimasi Dilayani
    **/
    public function actionCetakAntrianBpjsOnsite()
    {
        $request = Yii::$app->request;
        $indonsian_date = DocoHelpers::getTanggalIndonesia();
        $antrian_id = $request->get('antrian_id');
        $no_antrian = $request->get('no_antrian');
        $payload = new PayloadForm;
        $payload->antrian_id = $antrian_id;
        if (!$payload->validate()) {
            return [
                'status' => 422,
                'data' => $payload->errors
            ];
        }
        try {
            $result = AntrianView::find()->where(['antrian_id' => $antrian_id])->one();
            $pendaftaranol = PendaftaranOnline::find()->where(['antrian_id' => $antrian_id])->one();
            $estimasidilayani = PendaftaranHelpers::getEstimasiDilayani($pendaftaranol['pendaftaranol_id'], false);

            $print = new DocoPrint();
            $profil_rs = Profilrumahsakit::find()->one();
            $print->attributes = [
                '#no_antrian#' => $no_antrian,
                '#nama_dokter#' => @$result['nama_pegawai'],
                '#no_antrian_poli#' => $no_antrian,
                '#date#' => date('d', strtotime('NOW')).' '.$indonsian_date['bulan'].' '.$indonsian_date['tahun'],
                '#time#' => date('H:i:s', strtotime('NOW')),
                '#nama_poli#' => @$result['ruangan_nama'],
                '#no_rekam_medik#' => @$result['no_rekam_medik'],
                '#nama_rumah_sakit#' => $profil_rs['nama_rumahsakit'],
                '#estimasi_dilayani#' => $estimasidilayani
            ];
            return $print->OutputHtml();
        } catch(\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetProfilRs()
    {
        $profil_rs = Profilrumahsakit::find()->one();

        return $profil_rs;
    }

    /**
    * @controller actionCetakAntrianBpjsOnsitePasienBaru
    * @attribute #nama_rumah_sakit# => Nama RS
    * @attribute #no_antrian# => No Antrian Pendaftaran
    * @attribute #date# => Tanggal
    * @attribute #time# => Waktu
    **/
    public function actionCetakAntrianBpjsOnsitePasienBaru()
    {
        $request = Yii::$app->request;
        $indonsian_date = DocoHelpers::getTanggalIndonesia();
        $antrian_id = $request->get('antrian_id');
        $no_antrian = $request->get('no_antrian');
        $nama_antrian = $request->get('nama_antrian');

        $payload = new PayloadForm;
        $payload->antrian_id = $antrian_id;
        if (!$payload->validate()) {
            return [
                'status' => 422,
                'data' => $payload->errors
            ];
        }
        try {
            // jenisantrian_id 312 poli
            if ($request->get('jenisantrian_id') == DocoConstants::VAR_JA_P) {
                return $this->getCetakanAntrianPoli($request->get());
            } else {
                $result = AntrianView::find()->where(['antrian_id' => $antrian_id])->one();
                $print = new DocoPrint();
                $profil_rs = Profilrumahsakit::find()->one();
                $logo = Yii::$app->urlManagerFrontend->createUrl('') . $profil_rs['path_logorumahsakit'] . $profil_rs['logo_rumahsakit'];
                $print->attributes = [
                    '#logo#' => $logo,
                    '#nama_rumah_sakit#' => $profil_rs['nama_rumahsakit'],
                    '#alamatlokasi_rumahsakit#' => $profil_rs['alamatlokasi_rumahsakit'],
                    '#no_antrian#' => $no_antrian,
                    '#nama_antrian#' => $nama_antrian,
                    '#date#' => date('d', strtotime('NOW')).' '.$indonsian_date['bulan'].' '.$indonsian_date['tahun'],
                    '#time#' => date('H:i:s', strtotime('NOW')),
                ];
                if ($request->get('pdf', null)) {
                    return $print->Output();
                }
                return $print->OutputHtml();
            }
        } catch(\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    * @controller actionCetakAntrianPendaftaran
    * @attribute #no_antrian# => data no_antrian
    * @attribute #groupcarabayar# => data group cara bayar
    * @attribute #namadokter# => data nama dokter
    * @attribute #status_pasien# => data status pasien
    * @attribute #namaruangan# => data nama ruangan
    * @attribute #norekammedik# => data nomor rekam medik
    * @attribute #namapasien# => data nama pasien
    * @attribute #no_antrian_poli# => data no antrian poli
    * @attribute #date# => data tanggal
    * @attribute #time# => data waktu
    * @attribute #nama_poli# => data nama poli
    **/
    public function actionCetakAntrianPendaftaran()
    {
        $request = Yii::$app->request;
        $indonsian_date = DocoHelpers::getTanggalIndonesia();
        $antrian_id = $request->get('antrian_id');
        $no_antrian_poli = $request->get('no_antrian_poli');
        $payload = new PayloadForm;
        $payload->antrian_id = $antrian_id;
        $konfigSystem = $this->actionGetKonfigSystem();
        if ($konfigSystem->is_nourut == false || $konfigSystem->is_nourut == true) {
            $no_antrian_poli = null;
        }
        if (!$payload->validate()) {
            return [
                'status' => 422,
                'data' => $payload->errors
            ];
        }
        try {
            $where_ = '';
            if ($antrian_id) {
              $where_ = " WHERE antrian_id = '{$antrian_id}'";
            }
            $query = "SELECT * FROM antrian_v $where_";

            $result = Yii::$app->db->createCommand($query)->queryOne();

            $print = new DocoPrint();
            $noantrian = @$result['no_antrian'];
            if ($result['lantai']) {
                $noantrian = $result['lantai'] .' - '. $result['no_antrian'];
            }
            $print->attributes = [
                '#no_antrian#' => $noantrian,
                '#groupcarabayar#' => @$result['namagroupcarabayar'],
                '#namadokter#' => @$result['nama_pegawai_lengkap'],
                '#status_pasien#' => @$result['stat_pasien'],
                '#namaruangan#' => @$result['ruangan_nama'],
                '#norekammedik#' => @$result['no_rekam_medik'],
                '#namapasien#' => @$result['nama_pasien'],
                '#no_antrian_poli#' => $no_antrian_poli,
                '#date#' => date('d', strtotime('NOW')).' '.$indonsian_date['bulan'].' '.$indonsian_date['tahun'],
                '#time#' => date('H:i:s', strtotime('NOW')),
                '#nama_poli#' => @$result['ruangan_nama'],
            ];
            $ret = $print->OutputHtml();

            return $ret;
        } catch(\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    * @controller actionCetakAntrianPendaftaranV2
    * @attribute #logo# => data logo
    * @attribute #nama_rumah_sakit# => data nama_rumah_sakit
    * @attribute #alamatlokasi_rumahsakit# => data alamatlokasi_rumahsakit
    * @attribute #no_antrian# => data no_antrian
    * @attribute #groupcarabayar# => data group cara bayar
    * @attribute #namadokter# => data nama dokter
    * @attribute #status_pasien# => data status pasien
    * @attribute #namaruangan# => data nama ruangan
    * @attribute #norekammedik# => data nomor rekam medik
    * @attribute #namapasien# => data nama pasien
    * @attribute #no_antrian_poli# => data no antrian poli
    * @attribute #date# => data tanggal
    * @attribute #time# => data waktu
    * @attribute #nama_poli# => data nama poli
    * @attribute #estimasi_dilayani# => data estimasi dilayani
    **/
    public function actionCetakAntrianPendaftaranV2()
    {
        $request = Yii::$app->request;
        $indonsian_date = DocoHelpers::getTanggalIndonesia();
        $antrian_id = $request->get('antrian_id');
        $no_antrian_poli = $request->get('no_antrian_poli');
        $pendaftaranol_id = $request->get('pendaftaranol_id');
        $isPasienBaru = $request->get('is_pasien_baru');
        if (isset($isPasienBaru)) {
            $isPasienBaru = filter_var($request->get('is_pasien_baru'), FILTER_VALIDATE_BOOLEAN);
        }

        $payload = new PayloadForm;
        $payload->antrian_id = $antrian_id;
        // $konfigSystem = $this->actionGetKonfigSystem();
        // if ($konfigSystem->is_nourut == false || $konfigSystem->is_nourut == true) {
        //     $no_antrian_poli = null;
        // }
        if (!$payload->validate()) {
            return [
                'status' => 422,
                'data' => $payload->errors
            ];
        }
        try {
            $column = [
                'pm.no_rekam_medik',
                'pm.nama_pasien',
                'cm.carabayar_nama',
                'rm.ruangan_nama',
                'jm.jadwaldokter_mulai',
                'jm.jadwaldokter_tutup',
                'antrian_t.tgl_antrian',
                'antrian_t.no_antrian',
                'jm1.nama as lantai',
                'CONCAT(nama_depan.lookup_value, pm1.nama_pegawai, nama_belakang.lookup_value) nama_pegawai_lengkap',
            ];

            if (!empty($pendaftaranol_id)) {
                $columnOl = [
                    'pt.no_pendaftaranol',
                    'pt.tanggal_lahir',
                    'pt.nama_pasien AS nama_pasien_ol',
                ]; 

                $column = array_merge($column, $columnOl);
            }

            $query = Antrian::find()->select($column)
                ->leftJoin('pasien_m pm', 'pm.pasien_id = antrian_t.pasien_id')
                ->leftJoin('carabayar_m cm', 'cm.carabayar_id = antrian_t.carabayar_id')
                ->leftJoin('ruangan_m rm', 'rm.ruangan_id = antrian_t.ruangan_id')
                ->leftJoin('jadwaldokter_m jm', 'jm.jadwaldokter_id = antrian_t.jadwaldokter_id')
                ->leftJoin('pegawai_m pm1', 'pm1.pegawai_id = antrian_t.pegawai_id')
                ->leftJoin('jenisantriandetail_m jm1', 'jm1.jenisantriandetail_id = antrian_t.jenisantriandetail_id')
                ->leftJoin('lookup_m nama_depan', 'nama_depan.lookup_id = pm1.gelardepan::int')
                ->leftJoin('lookup_m nama_belakang', 'nama_belakang.lookup_id = pm1.gelarbelakang::int');

            if ($antrian_id) {
                $query->where(['antrian_id' => $antrian_id]);
            }

            if ($pendaftaranol_id) {
                $query->leftJoin('pendaftaranol_t pt', 'pt.antrian_id = antrian_t.antrian_id');
                $query->where(['pt.pendaftaranol_id' => $pendaftaranol_id]);
            }

            $result = $query->asArray()->one();

            $print = new DocoPrint();
            $profil_rs = Profilrumahsakit::find()->one();
            $logo = Yii::$app->urlManagerFrontend->createUrl('') . $profil_rs['path_logorumahsakit'] . $profil_rs['logo_rumahsakit'];
            $noantrian = @$result['no_antrian'];
            if ($result['lantai']) {
                $noantrian = $result['lantai'] .' - '. $result['no_antrian'];
            }


            // perhitungan estimasi Dilayani
            $estimasi_dilayani = "";
            if (!empty($no_antrian_poli)) {
                $antrianPOli = Antrian::find()->where(['antrianasal_id' => $antrian_id])->one();
                $pendaftaranol_id = null;
                if(isset($antrianPOli->antrian_id)){
                    $pend_ol = PendaftaranOnline::find()->where(['antrian_id'=>$antrianPOli->antrian_id])->one();
                    if(isset($pend_ol->pendaftaranol_id)){
                        $pendaftaranol_id = $pend_ol->pendaftaranol_id;
                    }
                }
                $jadwalDokter = InfoJadwalDokterView::find()->where(['jadwaldokter_id' => $antrianPOli->jadwaldokter_id])->one();

                if(!empty($jadwalDokter)){
                    $spm = isset($jadwalDokter->jumlah_loaddokter) ? $jadwalDokter->jumlah_loaddokter : 6;
                    $no_urut = (int)$antrianPOli->temp_urutan;
                    $estimasi_menit = $spm * ($no_urut - 1); // karena no urut pertama dimulai dari 1, maka dibutuhkan pengurangan 1 untuk penggunakan waktu mulai
                    $jamMulai = strtotime($jadwalDokter->waktu_mulai);
                    $jamMulai = date("H:i", strtotime('+'.$estimasi_menit.' minutes', $jamMulai));

                    $jamAkhir = strtotime($jamMulai);
                    $jamAkhir = date("H:i", strtotime('+'.$spm.' minutes', $jamAkhir));
                    
                    if(isset($pendaftaranol_id) && !empty($pendaftaranol_id)){
                        $estimasi_dilayani = PendaftaranHelpers::getEstimasiDilayani($pendaftaranol_id, false);
                    }else{
                        $estimasi_dilayani = empty($spm) ? "- " : $jamMulai." - ".$jamAkhir;
                    }
                }else{
                    $estimasi_dilayani = "-";
                }

            }

            if (isset($result['no_pendaftaranol']) && isset($isPasienBaru)) {
                $no_reservasi = @$result['no_pendaftaranol'];
                $tanggalLahir = @$result['tanggal_lahir'];
                if (isset($result['tanggal_lahir'])) {
                    $tanggalLahir = date('d M Y', strtotime($result['tanggal_lahir']));
                }

                if ($isPasienBaru) {
                    $namapasienMerge = $tanggalLahir . ' - ' . @$result['nama_pasien_ol'];
                } else {
                    $namapasienMerge = @$result['no_rekam_medik'] . ' - ' . @$result['nama_pasien'];
                }

            } else {
                $no_reservasi = empty($no_antrian_poli) ? $noantrian : $no_antrian_poli;
                $namapasienMerge = @$result['no_rekam_medik'] . ' - ' . @$result['nama_pasien'];
            }

            $print->attributes = [
                '#logo#' => $logo, // default site
                '#nama_rumah_sakit#' => $profil_rs['nama_rumahsakit'], // default site (kecuali prima)
                '#alamatlokasi_rumahsakit#' => $profil_rs['alamatlokasi_rumahsakit'], // default site (except prima)
                '#no_reservasi#' => $no_reservasi, // default site
                '#no_antrian#' => empty($no_antrian_poli) ? $noantrian : $no_antrian_poli, // default site (termasuk cetakan prima)
                '#groupcarabayar#' => @$result['namagroupcarabayar'], // default site (kecuali prima)
                '#namadokter#' => @$result['nama_pegawai_lengkap'], // default site
                '#status_pasien#' => @$result['stat_pasien'], // default site (kecuali: prima)
                '#namaruangan#' => @$result['ruangan_nama'], // default site
                '#norekammedik#' => @$result['no_rekam_medik'], // default site
                '#namapasien#' => @$result['nama_pasien'], // default site
                '#namapasien_merge#' => $namapasienMerge, // default site
                '#no_antrian_poli#' => $no_antrian_poli, // default site
                '#date#' => date('d', strtotime('NOW')).' '.$indonsian_date['bulan'].' '.$indonsian_date['tahun'], //default site
                '#time#' => date('H:i:s', strtotime('NOW')), // default site
                '#nama_poli#' => ArrayHelper::getValue($result, 'ruangan_nama'), // default site
                '#estimasi_dilayani#' => $estimasi_dilayani, // default site
                '#cara_bayar#' => ArrayHelper::getValue($result, 'carabayar_nama'), // cetakan prima
                '#jam_mulai#' => ArrayHelper::getValue($result, 'jadwaldokter_mulai') ? date('H:i', strtotime(ArrayHelper::getValue($result, 'jadwaldokter_mulai'))) : '', // cetakan prima
                '#jam_tutup#' => ArrayHelper::getValue($result, 'jadwaldokter_tutup') ? date('H:i', strtotime(ArrayHelper::getValue($result, 'jadwaldokter_tutup'))) : '', // cetakan prima
                '#tanggal_kunjungan#' => DocoHelpers::convertDate(ArrayHelper::getValue($result, 'tgl_antrian'), 'd m Y'), // cetakan prima
            ];

            if ($request->get('pdf', null)) {
                return $print->Output();
            }

            $ret = $print->OutputHtml();

            return $ret;
        } catch(\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    * @controller actionCetakAntrianPenunjang
    * @attribute #no_antrian# => data no_antrian
    * @attribute #groupcarabayar# => data group cara bayar
    * @attribute #namadokter# => data nama dokter
    * @attribute #status_pasien# => data status pasien
    * @attribute #namaruangan# => data nama ruangan
    * @attribute #norekammedik# => data nomor rekam medik
    * @attribute #namapasien# => data nama pasien
    **/
    public function actionCetakAntrianPenunjang()
    {
        $request = Yii::$app->request;
        $payload = new PayloadForm;
        $antrian_id = $request->get('antrian_id');
        $payload->antrian_id = $antrian_id;
        if (!$payload->validate()) {
            return [
                'status' => 422,
                'data' => $payload->errors
            ];
        }
        try {
            $where_ = '';
            if ($payload->antrian_id) {
              $where_ = " WHERE antrian_id = '{$payload->antrian_id}'";
            }
            $query = "SELECT * FROM antrian_v $where_";

            $result = Yii::$app->db->createCommand($query)->queryOne();

            $print = new DocoPrint();
            $print->attributes = [
                '#no_antrian#' => @$result['no_antrian'],
                '#groupcarabayar#' => @$result['namagroupcarabayar'],
                '#namadokter#' => @$result['nama_pegawai_lengkap'],
                '#status_pasien#' => @$result['stat_pasien'],
                '#namaruangan#' => @$result['ruangan_nama'],
                '#norekammedik#' => @$result['no_rekam_medik'],
                '#namapasien#' => @$result['nama_pasien'],
            ];
            $ret = $print->OutputHtml();

            return $ret;
        } catch(\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    * @controller actionCetakAntrianFarmasi
    * @attribute #no_antrian# => data no_antrian
    **/
    public function actionCetakAntrianFarmasi()
    {
        $request = Yii::$app->request;
        $antrian_id = $request->get('antrian_id');
        $payload = new PayloadForm;
        $payload->antrian_id = $antrian_id;
        if (!$payload->validate()) {
            return [
                'status' => 422,
                'data' => $payload->errors
            ];
        }
        try {
            $where_ = '';
            if ($payload->antrian_id) {
                $where_ = " WHERE antrian_id = '{$payload->antrian_id}'";
            }
            $query = "SELECT * FROM antrian_v $where_";

            $result = Yii::$app->db->createCommand($query)->queryOne();

            $print = new DocoPrint();
            $print->attributes = [
                '#no_antrian#' => @$result['no_antrian'],
                '#groupcarabayar#' => @$result['namagroupcarabayar'],
                '#namadokter#' => @$result['nama_pegawai_lengkap'],
                '#status_pasien#' => @$result['stat_pasien'],
                '#namaruangan#' => @$result['ruangan_nama'],
                '#norekammedik#' => @$result['no_rekam_medik'],
                '#namapasien#' => @$result['nama_pasien'],
                '#instalasi#' => @$result['instalasi_nama'],
                '#fungsi#' => @$result['fungsi_nama'],
            ];
            $ret = $print->OutputHtml();

            return $ret;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    * @controller actionCetakAntrianKasir
    * @attribute #no_antrian# => data no_antrian
    **/
    public function actionCetakAntrianKasir()
    {
        $request = Yii::$app->request;
        $payload = new PayloadForm;
        $antrian_id = $request->get('antrian_id');
        $payload->antrian_id = $antrian_id;
        if (!$payload->validate()) {
            return [
                'status' => 422,
                'data' => $payload->errors
            ];
        }
        try {
            $where_ = '';
            if ($payload->antrian_id) {
              $where_ = " WHERE antrian_id = '{$payload->antrian_id}'";
            }
            $query = "SELECT * FROM antrian_v $where_";
            $result = Yii::$app->db->createCommand($query)->queryOne();

            $print = new DocoPrint();
            $print->attributes = [
                '#no_antrian#' => @$result['no_antrian'],
            ];
            $ret = $print->OutputHtml();

            return $ret;
        } catch(\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    // kebutuhan untuk panggil antrian

    public function actionPilihLoket($jenisantrian_id = null, $instalasi_id = null, $ruangan_id = null)
    {
        try {
            $payload = new PayloadForm;
            $payload->jenisantrian_id = $jenisantrian_id;
            if($payload->validate()) {
                $loket = new Loket;
                // $fungsiAntrian = DocoConstants::LOOKUP_TYPE_FUNGSI_ANTRIAN;
                // $defaultPendaftaran = DocoConstants::LOOKUP_NAME_DEFAULT_PENDAFTARAN;
                $post = Yii::$app->request->post();
                $ja_dftr = DocoConstants::VAR_JA_PD;
                $sql = "
                    SELECT
                        t.loket_id,
                        t.loket_nama,
                        t.loket_namalain
                    FROM loket_m t
                        LEFT JOIN loket_mp loket_mp ON t.loket_id = loket_mp.loket_id AND loket_mp.is_deleted = false AND loket_mp.is_active = true ";

                        if (!empty($instalasi_id)) {
                            if ($jenisantrian_id == DocoConstants::VAR_JA_PEN) { // penunjang
                                $sql .= "AND instalasi_id = {$instalasi_id} ";
                            }
                        }

                $sql.= "WHERE loginpemakai_id IS NULL
                    AND t.is_deleted = false
                    AND t.is_active = true
                    AND t.jenisantrian_id = {$jenisantrian_id}";

                if (!empty($ruangan_id)) {
                    if ($jenisantrian_id == DocoConstants::VAR_JA_F) { // penunjang
                        $sql .= "AND t.ruangan_id = {$ruangan_id} ";
                    }
                }

                $sql .=" GROUP BY t.loket_id
                        ORDER BY t.loket_nama";
                $result = $loket->findBySql($sql)->all();
                // $result = $this->getOrSetCache(DocoConstants::VAR_CACHE_LOKET_PENDAFTARAN, $query, true);
            }
            else {
                $result = [
                    'status' => 422,
                    'data' => $payload->errors,
                ];
            }

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

            Loket::updateAll(['loginpemakai_id' => null], "loginpemakai_id = {$loginpemakai_id}");

            $loket = Loket::findOne($loket_id);
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

    // kebutuhan untuk ambil data antrian secara langsung : Ali
    public function getDataCetakAntrian($antrian_id = null, $is_carabayar_bpjs = 0)
    {
        $indonsian_date = DocoHelpers::getTanggalIndonesia();
        $konfigKuota = $this->actionKonfigKuotaAntrian();

        if (is_array($antrian_id) && !empty($antrian_id)) {
            $antrian_id = json_encode($antrian_id);
            $antrian_id = 'array'.$antrian_id;
            $where_ = " WHERE antrian_id = ANY($antrian_id)";
            $query = "SELECT * FROM antrian_v $where_";
            $result = Yii::$app->db->createCommand($query)->queryAll();
            $data = [];

            if (!empty($result)) {
                foreach ($result as $key => $value) {
                    $detail = $this->actionGetAntrianDetail($value['jenisantriandetail_id']);
                    if ($value['jenisantrian_id'] == 177) {
                        $data['noantrian'] = $value['no_antrian'];
                        $data['namaruangan'] = $value['ruangan_nama'];
                        $data['namadokter'] = ($konfigKuota == DocoConstants::VAR_ID_KUOTA_ANTRIAN_DOKTER) ? $value['nama_pegawai_lengkap'] : '';
                        $data['fungsi_nama'] = $value['fungsi_nama'];
                        $data['first_footer'] = 'Silakan ke bagian pendaftaran';
                        $data['second_footer'] = 'Simpan antrian hingga transaksi selesai';
                        $data['date'] = date('d', strtotime('NOW')).' '.$indonsian_date['bulan'].' '.$indonsian_date['tahun'];
                        $data['time'] = date('H:i:s', strtotime('NOW'));
                        $data['nama_poli'] = $value['ruangan_nama'];
                        $data['tipe'] = $value['jenisantrian_id'];
                        $data['lantai'] = $detail['nama'];
                        $data['is_bpjs'] = $is_carabayar_bpjs;
                    } else if($value['jenisantrian_id'] == 312) {
                        $data['no_antrian_poli'] = $value['no_antrian'];
                    }
                }
            }
        } else if ($antrian_id) {
            $where_ = " WHERE antrian_id = '{$antrian_id}'";
            $query = "SELECT * FROM antrian_v $where_";
            $result = Yii::$app->db->createCommand($query)->queryOne();
            $detail = $this->actionGetAntrianDetail(@$result['jenisantriandetail_id']);

            $data = [
                "noantrian" => @$result['no_antrian'],
                "namaruangan" => @$result['ruangan_nama'],
                "namadokter" => ($konfigKuota == DocoConstants::VAR_ID_KUOTA_ANTRIAN_DOKTER) ? $result['nama_pegawai_lengkap'] : '',
                "fungsi_nama" => @$result['fungsi_nama'],
                "first_footer" => 'Silakan ke bagian pendaftaran',
                "second_footer" => 'Simpan antrian hingga transaksi selesai',
                "no_antrian_poli" => $result['no_antrian'],
                "date" => date('d', strtotime('NOW')).' '.$indonsian_date['bulan'].' '.$indonsian_date['tahun'],
                "time" => date('H:i:s', strtotime('NOW')),
                "nama_poli" => @$result['ruangan_nama'],
                "tipe" => @$result['jenisantrian_id'],
                "is_bpjs" => $is_carabayar_bpjs,
                "lantai" => $detail['nama']
            ];
        }

        return $data;
    }
    // kebutuhan untuk panggill antrian

    function getDataAntrianPoli()
    {
        $start = date('Y-m-d')." 00:00:00";
        $end = date('Y-m-d')." 23:59:59";
        $query = "SELECT
                    ruangan_id,ruangan_nama,count(*)

                FROM antrian_v where tgl_antrian >= '".$start."' and tgl_antrian <= '".$end."' and jenisantrian_id = ".DocoConstants::VAR_JA_P."
                GROUP BY ruangan_id,ruangan_nama
                ORDER BY ruangan_nama";
        $result = Yii::$app->db->createCommand($query)->queryAll();

        return $result;
    }

        /**
    * @todo Fungsi untuk mendapatkan konfig jenis kuota antrian
    * @author Sigit Arif Munandar <sigit@docotel.com>
    **/
    public function actionKonfigKuotaAntrian()
    {
        $konfig = KonfigSystem::find()->limit(1)->one();
        $defaultKuotaAntrian = DocoConstants::VAR_ID_KUOTA_ANTRIAN_DOKTER;
        if(!is_null($konfig)) {
            $defaultKuotaAntrian = isset($konfig->kuota_antrian) ? $konfig->kuota_antrian : DocoConstants::VAR_ID_KUOTA_ANTRIAN_DOKTER;
        }
        return $defaultKuotaAntrian;
    }


    public function actionJenisAntrianPoli()
    {
        // Try catch
        try {
            // Get request and expand the get
            $request = Yii::$app->request;
            $_GET['expand'] = $request->get('expand', 'lookup_m');

            // Define model
            $model = new Lookup();

            // Query
            $query = Lookup::find()->where(['lookup_type' => 'jenis_antrian']);


            // Doco active filter
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            // Return data
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @todo Fungsi ubtuk get konfig layar antrian
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    private function getKonfigLayarAntrian()
    {
        $konfig = KonfigSystem::find()->one();

        if ($konfig->is_slider == 0) {
            $konfig_detail = KonfigSystemDetail::find()
            ->where(['is_foto' => true])
            ->all();
        } else {
            $konfig_detail = KonfigSystemDetail::find()
            ->where(['is_foto' => false])
            ->all();
        }

        return [
            'header' => $konfig->header,
            'header_detail' => $konfig->header_detail,
            'footer' => $konfig->footer,
            'path_logoheader' => $konfig->path_logoheader,
            'is_slider' => $konfig->is_slider,
            'url_slider' => $konfig->url_slider,
            'is_banyakloket' => $konfig->is_banyakloket,
            'slides' => $konfig_detail
        ];
    }

    public function actionGetJenisAntrianDetail()
    {
        try{
            $request = Yii::$app->request;
            $get = $request->get();
            $model = JenisAntrianDetail::find()->select(['jenisantriandetail_id','jenisantrian_id','jenisantrian_nama', 'nama'])->where(['jenisantrian_id' => $get['jenisantrian_id'],'is_deleted'=>false]);
            return [
                'data' => $model->asArray()->all()
            ];

        } catch(\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetLantai($id = null)
    {
        try {
            $model = JenisAntrianDetail::find()
            ->where(['jenisantriandetail_id' => $id])
            ->one();

            return $model;
        } catch(\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetKonfigSystem()
    {
        try {
            $result =  KonfigSystem::find()->one();

            return $result;
        } catch (Exception $e) {
            return ['message'=>'Kesalahan Internal'];
        }
    }

    public function actionGetAntrianDetail($id)
    {
        try {
            $model = JenisAntrianDetail::findOne($id);
            return $model;
        } catch (Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }

    }

    public function actionCheckFingerPrint()
    {
        $request = Yii::$app->request;
        $noKartu = $request->post('no_kartu', null);
        $tglPelayanan = date('Y-m-d');
        $messageBpjs = '-';
        $is_finger = false;
        $resultFinger = 1;
        $infoPeserta = (new Bpjs)->peserta($noKartu, $tglPelayanan);
        $umur_format_text = DocoHelpers::getUmur(ArrayHelper::getValue($infoPeserta, 'response.peserta.tglLahir'));
        if (ArrayHelper::getValue($infoPeserta, 'metaData.code', 500) != '200') {
            $messageBpjs = ArrayHelper::getValue($infoPeserta, 'metaData.message', 500);
        } else {
            $arr_umur = DocoHelpers::getUmur($umur_format_text, false, true);
            if ($arr_umur['tahun'] >= 17 && $arr_umur['bulan'] >= 0 && $arr_umur['hari'] >= 1) {
                $bpjsRes = (new Bpjs)->pencarianFingerprint($noKartu,$tglPelayanan);
                if ($bpjsRes && ArrayHelper::getValue($bpjsRes, 'metaData.code') == 200) {
                    $resultFinger = ArrayHelper::getValue($bpjsRes, 'response.kode');
                    $messageBpjs = ArrayHelper::getValue($bpjsRes, 'response.status');
                    if($resultFinger == '0'){
                        $is_finger = true;
                    }
                }
                $url = Lookup::find()
                    ->select(['lookup_value'])
                    ->where(['lookup_type' => 'bpjs'])
                    ->andWhere(['lookup_name' => 'url_finger_bpjs'])
                    ->scalar();
            }
        }

        return (new DocoHelpers)->callback(DocoMessages::KEY_DYNAMIC_STATUS, [
            'title' => 'test',
            'text' => $messageBpjs,
            'data' => [
                'is_finger' => $is_finger,
                'resultFinger' => $resultFinger,
                'url' => isset($url) ? $url : '',
            ]
        ], 422);
    }

    public function actionCheckPasienHasKonsul()
    {
        try {
            $request = Yii::$app->request;
            if ($request->get('nomor_identitas')) {
                $konsul_poli = Pendaftaran::find()->select(['pendaftaran_t.pendaftaran_id', 'kt.konsulpoli_id'])
                    ->leftJoin('pasien_m as pm', 'pm.pasien_id = pendaftaran_t.pasien_id')
                    ->leftJoin('konsulpoli_t as kt', 'pendaftaran_t.pendaftaran_id = kt.pendaftaran_id and pendaftaran_t.tgl_pendaftaran::date=\''.date('Y-m-d').'\' and kt.tgl_konsulpoli::date=\''.date('Y-m-d').'\' and kt.status_periksa <> \''.DocoConstants::STATUS_PERIKSA_BTL_KONSUL.'\'')
                    ->leftJoin('bpjs_t bt', 'bt.pendaftaran_id = pendaftaran_t.pendaftaran_id')
                    ->where([
                              'or', 'pendaftaran_t.no_pendaftaran=\''.$request->get('nomor_identitas').'\'', 'pm.no_rekam_medik=\''.$request->get('nomor_identitas').'\'', 'bt.nokartuasuransi=\''.$request->get('nomor_identitas').'\''
                            ]);
                  $konsul_poli= $konsul_poli->asArray()->all();
                return DocoHelpers::response($konsul_poli);
            } else {
                throw new \Exception("Nomor Identitas tidak boleh kosong", 1);
            }

        } catch(\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetDatatableKonsulPoli()
    {
        try {
            $request = Yii::$app->request;
            $limit = Yii::$app->request->get('per-page', 10);
            $page = Yii::$app->request->get('page', 1);
            if ($request->get('nomor_identitas')) {
                $model = new Konsulpoli;
                $query = $model::find()->select([
                    'konsulpoli_t.konsulpoli_id', 'konsulpoli_t.tgl_konsulpoli', 'pm2.nama_pegawai as dokter_konsul', 'rm.ruangan_nama as nama_poli',
                    'asalpoli.ruangan_nama as nama_poli_asal', 'lm.lookup_value as status_konsul', 'lm2.lookup_value as status_periksa', 'pt.tgl_pendaftaran'
                ])
                ->leftJoin('pendaftaran_t pt', 'pt.pendaftaran_id = konsulpoli_t.pendaftaran_id')
                ->leftJoin('pasien_m pm', 'pm.pasien_id = pt.pasien_id')
                ->leftJoin('bpjs_t bt', 'bt.pendaftaran_id = pt.pendaftaran_id')
                ->leftJoin('pegawai_m pm2', 'pm2.pegawai_id = konsulpoli_t.pegawai_id')
                ->leftJoin('ruangan_m rm', 'rm.ruangan_id = konsulpoli_t.ruangan_id')
                ->leftJoin('ruangan_m asalpoli', 'asalpoli.ruangan_id = konsulpoli_t.asalpoliklinikkonsul_id')
                ->leftJoin('lookup_m lm', 'lm.lookup_id = konsulpoli_t.status_konsul::integer')
                ->leftJoin('lookup_m lm2', 'lm2.lookup_id = konsulpoli_t.status_periksa::integer')
                ->where(['and', 'pt.tgl_pendaftaran::date=\''.date('Y-m-d').'\'', 'konsulpoli_t.tgl_konsulpoli::date=\''.date('Y-m-d').'\'', 'konsulpoli_t.status_periksa <> \''.DocoConstants::STATUS_PERIKSA_BTL_KONSUL.'\'' , [
                              'or', 'pt.no_pendaftaran=\''.$request->get('nomor_identitas').'\'', 'pm.no_rekam_medik=\''.$request->get('nomor_identitas').'\'', 'bt.nokartuasuransi=\''.$request->get('nomor_identitas').'\''
                            ]])
                ->orderBy(['konsulpoli_t.tgl_konsulpoli' => SORT_DESC]);

                $query->offset(($page - 1) * $limit)->limit($limit);

                $query = DocoRestActiveFilter::advancedFilter($model, $query);
                $total_record = $query->count();
                $record = $query->asArray()->all();

                return [
                  'recordsFiltered' => $total_record,
                  'recordsTotal' => $total_record,
                  'data' => $record,
                ];

            } else {
                throw new \Exception("Nomor Identitas tidak boleh kosong", 1);
            }

        } catch(\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    private function getCetakanAntrianPoli($params)
    {
        $print = new DocoPrint('ce-antrian-poliklinik');
        $indonsian_date = DocoHelpers::getTanggalIndonesia();
        $profil_rs = Profilrumahsakit::find()->one();
        $logo = Yii::$app->urlManagerFrontend->createUrl('') . $profil_rs['path_logorumahsakit'] . $profil_rs['logo_rumahsakit'];
        $antrian = Antrian::find()
                  ->select(['antrian_t.antrian_id', 'no_antrian', 'pm.nama_pegawai as nama_dokter', 'rm.ruangan_nama as nama_poli', 'ar.tgl_checkin', 'psm.no_rekam_medik'])
                  ->leftJoin('pegawai_m pm', 'pm.pegawai_id = antrian_t.pegawai_id')
                  ->leftJoin('ruangan_m rm', 'rm.ruangan_id = antrian_t.ruangan_id')
                  ->leftJoin('antrianjkn_r ar', 'ar.antrian_id = antrian_t.antrian_id')
                  ->leftJoin('pasien_m psm', 'psm.pasien_id = antrian_t.pasien_id')
                  ->where(['antrian_t.antrian_id' => ArrayHelper::getValue($params, 'antrian_id')])->asArray()->one();

        $estimasidilayani = PendaftaranHelpers::getEstimasiTimeAntrian(['antrian_id' => ArrayHelper::getValue($params, 'antrian_id')]);
        $tgl_checkin = ArrayHelper::getValue($antrian, 'tgl_checkin') != null ? ArrayHelper::getValue($antrian, 'tgl_checkin') : date('Y-m-d H:i:s');

        $print->attributes = [
            '#logo#' => $logo,
            '#nama_rumah_sakit#' => ArrayHelper::getValue($profil_rs, 'nama_rumahsakit'),
            '#alamatlokasi_rumahsakit#' => ArrayHelper::getValue($profil_rs, 'alamatlokasi_rumahsakit'),
            '#no_antrian_poli#' => ArrayHelper::getValue($antrian, 'no_antrian'),
            '#nama_dokter#' => ArrayHelper::getValue($antrian, 'nama_dokter'),
            '#nama_poli#' => ArrayHelper::getValue($antrian, 'nama_poli'),
            '#no_rekam_medik#' => ArrayHelper::getValue($antrian, 'no_rekam_medik'),
            '#estimasi_dilayani#' => ArrayHelper::getValue($estimasidilayani, 'waktuestimasi_mulai'). ' - '.ArrayHelper::getValue($estimasidilayani, 'waktuestimasi_berakhir').' WIB',
            '#poli#' => date('d', strtotime($tgl_checkin)).' '.$indonsian_date['bulan'].' '.$indonsian_date['tahun'],
            '#date#' => date('d', strtotime($tgl_checkin)).' '.$indonsian_date['bulan'].' '.$indonsian_date['tahun'],
            '#time#' => date('H:i:s', strtotime($tgl_checkin)),
        ];
        return $print->OutputHtml();
    }

    /**
    * @controller actionCekPasien
    **/
    public function actionCekPasien()
    {
        $request = Yii::$app->request;
        $no_identitas_pasien = $request->get('no_identitas_pasien');

        try {
            $model = Pasien::find()->select(['pasien_m.*', 'lm.lookup_value as jenis_kelamin_nama', 'pt.pendaftaran_id as pendaftaran_hari_ini'])
            ->leftJoin('lookup_m as lm', 'lm.lookup_id = pasien_m.jeniskelamin::integer')
            ->leftJoin('pendaftaran_t as pt', 'pt.pasien_id = pasien_m.pasien_id and pt.tgl_pendaftaran::date = \''.date('Y-m-d').'\' and pt.pasienbatalperiksa_id is null')
            ->where(['pasien_m.no_identitas_pasien' => $no_identitas_pasien])->asArray()->all();

            if ($model) {
                foreach ($model as $key => $value) {
                    $pasienEnc = DocoHelpers::encrypt($value['pasien_id']);
                    $model[$key]['pasien_id'] = $pasienEnc;
                }
            }

            return $model;
        } catch(\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetPendaftaranOnlineById($pendaftaranol_id)
    {
        $pendaftaranol = InfoPendaftaranOnlineView::find()->where(['pendaftaranol_id' => $pendaftaranol_id]);
        $pendaftaranol = $pendaftaranol->asArray()->one();
        return $pendaftaranol;
    }

    public function actionGetKonfigAutoDaftar()
    {
        return (new DocoConstansId)->actionGetId('auto_daftar_apm');
    }

    public function actionGetLookupByType()
    {
        $request = Yii::$app->request;
        $type = $request->get('type');

        return DocoCache::getLookupByType($type);
    }
}
