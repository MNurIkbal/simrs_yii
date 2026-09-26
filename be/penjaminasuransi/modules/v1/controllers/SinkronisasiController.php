<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoAccessRule;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoJwtHttpBearerAuth;
use Doco\components\DocoSyp;
use app\modules\v1\models\Cron;
use app\modules\v1\models\SyKunjungan;
use app\modules\v1\models\SyKunjunganDetail;
use app\modules\v1\models\SyKunjunganPasien;
use app\modules\v1\models\SyKunjunganTagihan;
use app\modules\v1\models\SyKunjunganPotonganTagihan;
use app\modules\v1\models\Tindakan;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\SyBagian;
use app\modules\v1\models\SyNota;
use app\modules\v1\models\LogError;
use app\modules\v1\models\Lookup as ModelsLookup;
use app\modules\v1\models\SyKunjunganAdjusmentHeader;
use app\modules\v1\models\SyKunjunganAdjusmentDetail;
use Doco\rabbitmq\RabbitBgProcess;
use Doco\Services\InternalService;

/**
 * @function : Set Limit exec
 */
ini_set('max_execution_time', '0');
ini_set("memory_limit", "-1");
class SinkronisasiController extends DocoActiveController
{
    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => DocoJwtHttpBearerAuth::className(),
            'except' => ['sinkron', 'sinkron-layanan', 'sinkron-pegawai', 'sinkron-bagian', 'sinkron-nota'],
        ];

        $behaviors['access'] = [
            'class' => DocoAccessRule::className(),
            'except' => ['sinkron', 'sinkron-layanan', 'sinkron-pegawai', 'sinkron-bagian', 'sinkron-nota'],
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        return $actions;
    }

    /**
     * @todo Function sinkronisasi kunjungan
     * @return message
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionSinkron($command = false)
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        $randString = isset($get['randString']) ? $get['randString'] : null;
        $countData = 1000;
        $totalPerPage = 1000;
        $cache = Yii::$app->cache;
        
        try {
            $service = 'sinkron';
            $message = '';
            $cache_sync = $cache->get($service);
            $cache_sync = false;
            $cron = Cron::find()->where(['cron_id' => DocoConstants::VAR_CRON_KUNJUNGAN])->one();
            $lookup = ModelsLookup::find()
            ->where(['lookup_type' => 'djamil_api', 'is_active' => true])
            ->where(['lookup_name' => "is_api"])
            ->one();

            $apiEnv = isset($lookup->lookup_value) ? $lookup->lookup_value : "false";
            $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
            if (!$cache_sync) {
                // $sync = true;
                // $cache->set($service, $sync);
                if(isset($get['tgl_pendaftaran'])) {
                    $tgl_pendaftaran = $get['tgl_pendaftaran'];
                    $jam_pendaftaran = $get['jam_pendaftaran'];
                }else{
                    $tgl_pendaftaran = date('Y-m-d', strtotime($cron->cron_tgl_mulai));
                    $jam_pendaftaran = date('H:i:s', strtotime($cron->cron_tgl_mulai));
                }

                (new InternalService)->sendTo([
                    'Sirs' => [
                        'SinkronDataBpjs\SinkronKunjungan' => [
                            'tgl_pendaftaran' => $tgl_pendaftaran,
                            'jam_pendaftaran' => $jam_pendaftaran,
                            'user_id' => !empty($jwt->loginpemakai_id) ? $jwt->loginpemakai_id : null,
                            'token' => $auth,
                            'xOwner' => $xOwner,
                            'unique_str' => $randString,
                            'service' => $service,
                            'is_api' => $apiEnv,
                        ]
                    ]
                ], true);

                (new InternalService)->sendTo([
                    'Sirs' => [
                        'SinkronDataBpjs\SinkronDiagnosa' => [
                            'tgl_pendaftaran' => $tgl_pendaftaran,
                            'jam_pendaftaran' => $jam_pendaftaran,
                            'user_id' => !empty($jwt->loginpemakai_id) ? $jwt->loginpemakai_id : null,
                            'token' => $auth,
                            'xOwner' => $xOwner,
                            'unique_str' => $randString,
                            'service' => $service,
                            'is_api' => $apiEnv
                        ]
                    ]
                ], true);

                (new InternalService)->sendTo([
                    'Sirs' => [
                        'SinkronDataBpjs\SinkronTagihan' => [
                            'tgl_pendaftaran' => $tgl_pendaftaran,
                            'jam_pendaftaran' => $jam_pendaftaran,
                            'user_id' => !empty($jwt->loginpemakai_id) ? $jwt->loginpemakai_id : null,
                            'token' => $auth,
                            'xOwner' => $xOwner,
                            'unique_str' => $randString,
                            'service' => $service,
                            'is_api' => $apiEnv
                        ]
                    ]
                ], true);

                (new InternalService)->sendTo([
                    'Sirs' => [
                        'SinkronDataBpjs\SinkronAdjustment' => [
                            'tgl_pendaftaran' => $tgl_pendaftaran,
                            'jam_pendaftaran' => $jam_pendaftaran,
                            'user_id' => !empty($jwt->loginpemakai_id) ? $jwt->loginpemakai_id : null,
                            'token' => $auth,
                            'xOwner' => $xOwner,
                            'unique_str' => $randString,
                            'is_api' => $apiEnv
                        ]
                    ]
                ], true);

                $cron->is_sync = false;
                $cron->save();
                $cache->delete($service);

                return [
                    'totalPerPage' => $totalPerPage,
                    'randString' => $randString,
                    'countData' => $countData,
                ];
            } else {
                $message = Yii::t('app', 'Sinkronisasi Sedang Berjalan.');
                $this->appendError($service, 500, json_encode($message));
                return [
                    'status' => 500,
                    'message' => $message
                ];
            }
        } catch (\yii\db\Exception $e) {
            $cache->delete($service);
            if (!$command) {
                \Yii::$app->response->statusCode = 500;
            }
            $this->appendError($service, 500, json_encode($e->getMessage()));
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $cache->delete($service);
            if (!$command) {
                \Yii::$app->response->statusCode = 500;
            }
            $this->appendError($service, 500, json_encode($e->getMessage()));
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @todo Function sinkronisasi layanan
     * @return message
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionSinkronLayanan()
    {
        $sync = false;
        $connection = Yii::$app->db;
        $cache = Yii::$app->cache;
        $transaction = $connection->beginTransaction();
        try {
            $layanan = array();
            $compareTindakan = array();
            $insert = false;
            $delete = false;
            $results = DocoSyp::getDataLayanan([], 'GET');
            $service = 'sinkron-layanan';
            $cron = Cron::find()->where(['cron_id' => DocoConstants::VAR_CRON_LAYANAN])->one();
            $sync = $cron->is_sync;
            $cache_sync = $cache->get($service);
            if (!$cache_sync) {
                if (!empty($results)) {
                    $sync = true;
                    $cache->set($service, $sync);
                    // $this->setSync(DocoConstants::VAR_CRON_LAYANAN);
                    $tindakan = Tindakan::find()->where(['is not', 'groupinacbg_id', null])->all();

                    if (!empty($tindakan)) {
                        foreach ($tindakan as $value) {
                            $compareTindakan[] = $value['daftartindakan_kode'];
                        }
                    }

                    foreach ($results as $key => $value) {
                        $kodeTindakan = (isset($value['daftartindakan_kode'])) ? $value['daftartindakan_kode'] : $value['DAFTARTINDAKAN_KODE'];
                        if (!in_array($kodeTindakan, $compareTindakan)) {
                            $layanan[] = [
                                'daftartindakan_kode' => $kodeTindakan,
                                'daftartindakan_nama' => (isset($value['daftartindakan_nama'])) ? $value['daftartindakan_nama'] : $value['DAFTARTINDAKAN_NAMA']
                            ];
                        }
                    }
                }

                if (!empty($layanan)) {
                    $delete = Tindakan::deleteAll(['is', 'groupinacbg_id', null]);
                    $insert = Tindakan::batchInsert($layanan);
                    if ($insert || $delete) {
                        $cron->cron_tgl_mulai = date('Y-m-d H:i:s', strtotime('NOW'));
                        $cron->cron_tgl_akhir = date('Y-m-d H:i:s', strtotime('NOW'));
                        $cron->save();
                    }
                }

                if ($insert || $delete) {
                    $transaction->commit();
                    $cache->delete($service);
                    $cron->is_sync = false;
                    $cron->save();
                    return [
                        'status' => 200,
                        'message' => Yii::t('app', 'Data Berhasil Trsinkronisasi.'),
                        'tgl_sinkron' => date('d-m-Y H:i:s', strtotime('NOW'))
                    ];
                } else {
                    $transaction->rollBack();
                    $cache->delete($service);
                    $message = Yii::t('app', 'Data Gagal Tersinkronisasi.');
                    $this->appendError($service, 422, json_encode($message));
                    return [
                        'status' => 422,
                        'message' => $message
                    ];
                }
            } else {
                $message = Yii::t('app', 'Sinkronisasi Sedang Berjalan.');
                $this->appendError($service, 500, json_encode($message));
                return [
                    'status' => 500,
                    'message' => $message
                ];
            }
        } catch (\yii\db\Exception $e) {
            $cache->delete($service);
            $transaction->rollBack();
            // \Yii::$app->response->statusCode = 500;
            $this->appendError($service, 500, json_encode($e->getMessage()));
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $cache->delete($service);
            $transaction->rollBack();
            $this->appendError($service, 500, json_encode($e->getMessage()));
            // \Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => $e->getMessage(),
                'error' => $results
            ];
        }
    }

    /**
     * @todo Function sinkronisasi pegawai
     * @return message
     * @author Erlangga <Erlangga@docotel.com>
     */
    public function actionSinkronPegawai()
    {
        $sync = false;
        $connection = Yii::$app->db;
        $cache = Yii::$app->cache;
        $transaction = $connection->beginTransaction();
        try {
            $service = 'sync-pegawai';
            $message = '';
            $dokter = [];
            $Updatedokter = [];
            $compareDokter = [];
            $tmpDokter = [];
            $update = false;
            $insert = false;
            $results = DocoSyp::getDataPegawai([], 'GET');
            $cron = Cron::find()->where(['cron_id' => DocoConstants::VAR_CRON_PEGAWAI])->one();
            $sync = $cron->is_sync;
            $cache_sync = $cache->get($service);
            if (!$cache_sync) {
                if (!empty($results)) {
                    $sync = true;
                    $cache->set($service, $sync);
                    // $this->setSync(DocoConstants::VAR_CRON_PEGAWAI);
                    $tmpDokter = Pegawai::find()->select([
                        'dokter_id', 'nama_pegawai', 'alamat_pegawai', 'notelp_pegawai', 'nomobile_pegawai'
                    ])->where([
                        'kelompokpegawai_id' => 1
                    ])->andWhere([
                        'not', ['dokter_id' => null]
                    ])->all();

                    if (!empty($tmpDokter)) {
                        foreach ($tmpDokter as $key => $value) {
                            $compareDokter[] = $value['dokter_id'];
                        }
                    }

                    foreach ($results['listdokter'] as $key => $value) {
                        $kodeDokter = (isset($value['kode'])) ? $value['kode'] : $value['KODE'];
                        if (!in_array($kodeDokter, $compareDokter)) {
                            $dokter[] = [
                                'dokter_id' => $kodeDokter,
                                'nama_pegawai' => (isset($value['nama'])) ? $value['nama'] : $value['NAMA'],
                                'alamat_pegawai' => (isset($value['alamat'])) ? $value['alamat'] : $value['ALAMAT'],
                                'notelp_pegawai' => (isset($value['no_telp'])) ? $value['no_telp'] : $value['NO_TELP'],
                                'nomobile_pegawai' => (isset($value['no_hp'])) ? $value['no_hp'] : $value['NO_HP'],
                                'kelompokpegawai_id' => (isset($value['kelompok_pegawai'])) ? $value['kelompok_pegawai'] : $value['KELOMPOK_PEGAWAI']
                            ];
                        }
                        if (in_array($kodeDokter, $compareDokter)) {
                            $Updatedokter[] = [
                                'dokter_id' => $kodeDokter,
                                'nama_pegawai' => (isset($value['nama'])) ? $value['nama'] : $value['NAMA'],
                                'alamat_pegawai' => (isset($value['alamat'])) ? $value['alamat'] : $value['ALAMAT'],
                                'notelp_pegawai' => (isset($value['no_telp'])) ? $value['no_telp'] : $value['NO_TELP'],
                                'nomobile_pegawai' => (isset($value['no_hp'])) ? $value['no_hp'] : $value['NO_HP'],
                                'kelompokpegawai_id' => (isset($value['kelompok_pegawai'])) ? $value['kelompok_pegawai'] : $value['KELOMPOK_PEGAWAI']
                            ];
                        }
                    }
                    if (!empty($dokter)) {
                        $insert = Pegawai::batchInsert($dokter);
                    }

                    if (!empty($Updatedokter)) {
                        foreach ($Updatedokter as $key => $value) {
                            $update = Pegawai::find()->select([
                                'dokter_id', 'nama_pegawai', 'alamat_pegawai', 'notelp_pegawai', 'nomobile_pegawai'
                            ])->where([
                                'kelompokpegawai_id' => 1,
                                'dokter_id' => $value['dokter_id']
                            ])->One();
                            $update->nama_pegawai = $value['nama_pegawai'];
                            $update->alamat_pegawai = $value['alamat_pegawai'];
                            $update->notelp_pegawai = $value['notelp_pegawai'];
                            $update->nomobile_pegawai = $value['nomobile_pegawai'];
                            $update->save();
                        }
                    }

                    if ($insert || $update) {
                        $transaction->commit();
                        $cache->delete($service);
                        $cron->is_sync = false;
                        $cron->save();
                        return [
                            'status' => 200,
                            'message' => Yii::t('app', 'Data Berhasil Tersinkronisasi.')
                        ];
                    } else {
                        $cache->delete($service);
                        $message = Yii::t('app', 'Data Gagal Tersinkronisasi.');
                        $this->appendError($service, 422, json_encode($message));
                        return [
                            'status' => 422,
                            'message' => $message
                        ];
                    }
                }
            } else {
                $message = Yii::t('app', 'Sinkronisasi Sedang Berjalan.');
                $this->appendError($service, 500, json_encode($message));
                return [
                    'status' => 500,
                    'message' => $message
                ];
            }
        } catch (\yii\db\Exception $e) {
            $cache->delete($service);
            $transaction->rollBack();
            $this->appendError($service, 500, json_encode($e->getMessage()));
            // \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $cache->delete($service);
            $transaction->rollBack();
            $this->appendError($service, 500, json_encode($e->getMessage()));
            // \Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => $e->getMessage(),
                'error' => $results
            ];
        }
    }

    /**
     * @todo Function sinkronisasi data bagian
     * @return message
     * @author Erlangga <Erlangga@docotel.com>
     */
    public function actionSinkronBagian()
    {
        $sync = false;
        $connection = Yii::$app->db;
        $cache = Yii::$app->cache;
        $transaction = $connection->beginTransaction();
        try {
            $bagian = [];
            $updateBagian = [];
            $compareBagian = [];
            $tmpBagian = [];
            $insert = false;
            $update = false;
            $service = 'sinkron-bagian';
            $message = '';
            $results = DocoSyp::getDataBagian([], 'GET');
            $cron = Cron::find()->where(['cron_id' => DocoConstants::VAR_CRON_BAGIAN])->one();
            $sync = $cron->is_sync;
            $cache_sync = $cache->get($service);
            if (!$cache_sync) {
                if (!empty($results)) {
                    $sync = true;
                    $cache->set($service, $sync);
                    // $this->setSync(DocoConstants::VAR_CRON_BAGIAN);
                    $tmpBagian = SyBagian::find()->select([
                        'bagian_id', 'bagian_kode', 'bagian_nama', 'kelompoktindakan_id'
                    ])->all();

                    if (!empty($tmpBagian)) {
                        foreach ($tmpBagian as $key => $value) {
                            $compareBagian[] = $value['bagian_kode'];
                        }
                    }

                    foreach ($results as $key => $value) {
                        $kodeBagian = (isset($value['bagian_kode'])) ? $value['bagian_kode'] : $value['BAGIAN_KODE'];
                        if (!in_array($kodeBagian, $compareBagian)) {
                            $bagian[] = [
                                'bagian_kode' => $kodeBagian,
                                'bagian_nama' => (isset($value['bagian_nama'])) ? $value['bagian_nama'] : $value['BAGIAN_NAMA']
                            ];
                        }
                        if (in_array($kodeBagian, $compareBagian)) {
                            $updateBagian[] = [
                                'bagian_kode' => $kodeBagian,
                                'bagian_nama' => (isset($value['bagian_nama'])) ? $value['bagian_nama'] : $value['BAGIAN_NAMA']
                            ];
                        }
                    }

                    if (!empty($bagian)) {
                        $insert = SyBagian::batchInsert($bagian);
                    }

                    if (!empty($updateBagian)) {
                        foreach ($updateBagian as $key => $value) {
                            $update = SyBagian::find()->select([
                                'bagian_id', 'bagian_kode', 'bagian_nama', 'kelompoktindakan_id'
                            ])->where(['ILIKE', 'LOWER(bagian_kode)', strtolower($value['bagian_kode'])])->One();
                            $update->bagian_kode = $value['bagian_kode'];
                            $update->bagian_nama = $value['bagian_nama'];
                            $update->save();
                        }
                    }

                    if ($bagian || $updateBagian) {
                        $cron->cron_tgl_mulai = date('Y-m-d H:i:s', strtotime('NOW'));
                        $cron->cron_tgl_akhir = date('Y-m-d H:i:s', strtotime('NOW'));
                        $cron->save();
                    }

                    if ($insert || $update) {
                        $transaction->commit();
                        $cache->delete($service);
                        return [
                            'status' => 200,
                            'message' => Yii::t('app', 'Data Berhasil Tersinkronisasi.'),
                            'tgl_sinkron' => date('d-m-Y H:i:s', strtotime('NOW'))
                        ];
                    } else {
                        $cache->delete($service);
                        $message = Yii::t('app', 'Data Gagal Tersinkronisasi.');
                        $this->appendError($service, 422, json_encode($message));
                        return [
                            'status' => 422,
                            'message' => $message
                        ];
                    }
                }
            } else {
                $message = Yii::t('app', 'Sinkronisasi Sedang Berjalan.');
                $this->appendError($service, 500, json_encode($message));
                return [
                    'status' => 500,
                    'message' => $message
                ];
            }
        } catch (\yii\db\Exception $e) {
            $cache->delete($service);
            // \Yii::$app->response->statusCode = 500;
            $transaction->rollBack();
            $this->appendError($service, 500, json_encode($e->getMessage()));
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $cache->delete($service);
            // \Yii::$app->response->statusCode = 500;
            $transaction->rollBack();
            $this->appendError($service, 500, json_encode($e->getMessage()));
            return [
                'status' => 500,
                'message' => $e->getMessage(),
                'error' => $results
            ];
        }
    }

    /**
     * @todo Function sinkronisasi data nota
     * @return message
     * @author Erlangga <Erlangga@docotel.com>
     */
    public function actionSinkronNota()
    {
        $sync = false;
        $connection = Yii::$app->db;
        $cache = Yii::$app->cache;
        $transaction = $connection->beginTransaction();
        try {
            $nota = [];
            $updateNota = [];
            $compareNota = [];
            $tmpNota = [];
            $insert = false;
            $update = false;
            $results = DocoSyp::getDataNota([], 'GET');
            $service = 'sinkron-nota';
            $message = '';
            $cron = Cron::find()->where(['cron_id' => DocoConstants::VAR_CRON_NOTA])->one();
            $sync = $cron->is_sync;
            $cache_sync = $cache->get($service);
            if (!$sync) {
                if (!empty($results)) {
                    $sync = true;
                    $cache->set($service, $sync);
                    // $this->setSync(DocoConstants::VAR_CRON_NOTA);
                    $tmpNota = SyNota::find()->select([
                        'kodenota_id', 'kode_nota', 'nama_nota', 'kelompoktindakan_id'
                    ])->all();

                    if (!empty($tmpNota)) {
                        foreach ($tmpNota as $key => $value) {
                            $compareNota[] = $value['kode_nota'];
                        }
                    }

                    foreach ($results as $key => $value) {
                        $kodeNota = (isset($value['kode_nota'])) ? $value['kode_nota'] : $value['KODE_NOTA'];
                        if (!in_array($kodeNota, $compareNota)) {
                            $nota[] = [
                                'kode_nota' => $kodeNota,
                                'nama_nota' => (isset($value['nama_nota'])) ? $value['nama_nota'] : $value['NAMA_NOTA']
                            ];
                        }
                        if (in_array($kodeNota, $compareNota)) {
                            $updateNota[] = [
                                'kode_nota' => $kodeNota,
                                'nama_nota' => (isset($value['nama_nota'])) ? $value['nama_nota'] : $value['NAMA_NOTA']
                            ];
                        }
                    }

                    if (!empty($nota)) {
                        $insert = SyNota::batchInsert($nota);
                    }

                    if (!empty($updateNota)) {
                        foreach ($updateNota as $key => $value) {
                            $update = SyNota::find()->select([
                                'kodenota_id', 'kode_nota', 'nama_nota', 'kelompoktindakan_id'
                            ])->where(['ILIKE', 'LOWER(kode_nota)', strtolower($value['kode_nota'])])->One();
                            $update->kode_nota = $value['kode_nota'];
                            $update->nama_nota = $value['nama_nota'];
                            $update->save();
                        }
                    }

                    if ($nota || $updateNota) {
                        $cron->cron_tgl_mulai = date('Y-m-d H:i:s', strtotime('NOW'));
                        $cron->cron_tgl_akhir = date('Y-m-d H:i:s', strtotime('NOW'));
                        $cron->save();
                    }

                    if ($insert || $update) {
                        $transaction->commit();
                        $cache->delete($service);
                        $cron->is_sync = false;
                        $cron->save();
                        return [
                            'status' => 200,
                            'message' => Yii::t('app', 'Data Berhasil Tersinkronisasi.'),
                            'tgl_sinkron' => date('d-m-Y H:i:s', strtotime('NOW'))
                        ];
                    } else {
                        $message = Yii::t('app', 'Data Gagal Tersinkronisasi.');
                        $this->appendError($service, 422, json_encode($message));
                        return [
                            'status' => 422,
                            'message' => $message
                        ];
                    }
                }
            } else {
                $message = Yii::t('app', 'Sinkronisasi Sedang Berjalan.');
                $cache->delete($service);
                $this->appendError($service, 500, json_encode($message));
                return [
                    'status' => 500,
                    'message' => $message
                ];
            }
        } catch (\yii\db\Exception $e) {
            $cache->delete($service);
            // \Yii::$app->response->statusCode = 500;
            $transaction->rollBack();
            $this->appendError($service, 500, json_encode($e->getMessage()));
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $cache->delete($service);
            // \Yii::$app->response->statusCode = 500;
            $transaction->rollBack();
            $this->appendError($service, 500, json_encode($e->getMessage()));
            return [
                'status' => 500,
                'message' => $e->getMessage(),
                'error' => $results
            ];
        }
    }

    private function appendError($service, $status, $msg)
    {
        $model = new LogError;
        $model->nama = $service;
        $model->pesan = $status . " - " . $msg;
        $model->tgl_error = date('Y-m-d H:i:s', strtotime('NOW'));
        $model->save(false);

        return true;
    }

    private function setSync($cron)
    {
        $cron = Cron::find()->where(['cron_id' => $cron])->one();
        $cron->is_sync = true;
        $cron->save();
    }
    /**
     * Function untuk bypass ketika terjadi kendala pada trigger kasir
     */
    public function actionTriggerSinkron()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        (new RabbitBgProcess())->send([
            'type_sinkron' => "sinkron",
            'pendaftaran_id' => $get['pendaftaran_id'],
            'pembayaran_id' => $get['pembayaran_id'],
            'instalasi' => $get['instalasi']
        ], 'trigger_sinkron_eklaim', 'sync_data');

        return [
            'status' => 200,
            'message' => "Sinkronisasi Berhasil"
        ];
    }

    /**
     * Function untuk bypas ketika terjadi kendala pada trigger kasir
     */
    public function actionTriggerSinkronHapus()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        if(isset($get['pendaftaran_id'])) {

            (new RabbitBgProcess())->send([
                'type_sinkron' => "hapus",
                'pendaftaran_id' => $get['pendaftaran_id'],
                'pembayaran_id' => $get['pembayaran_id'],
            ], 'trigger_sinkron_eklaim', 'sync_data');
        }

        return [
            'status' => 200,
            'message' => "Sinkronisasi Berhasil"
        ];
    }

    /**
     * Function untuk bypas ketika terjadi kendala pada trigger kasir
     */
    public function actionTriggerSinkronGlobal()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $randString = isset($get['randString']) ? $get['randString'] : null;
        
        (new RabbitBgProcess())->send([
            'type_sinkron' => "global",
            'unique_str' => $randString,
        ], 'trigger_sinkron_eklaim', 'sync_data');

        return [
            'status' => 200,
            'message' => "Sinkronisasi Berhasil"
        ];
    }

    /**
     * Function untuk melakukan freeze billing
     * 
     * @author Maulana Muhammad Rizky
     */
    public function actionPenjaminFreezeBilling()
    {
        // $kunjunganId bisa dari parameter
        $request = Yii::$app->request;
        $kunjunganId = $request->get("kunjungan_id");
        $typeFreeze = $request->get("tipe_freeze");

        (new InternalService)->sendTo([
            'Sirs' => [
                    'SinkronDataBpjs\TriggerFreezeBilling' => [
                        'type_sinkron' => $typeFreeze,
                        'kunjungan_id' => $kunjunganId
                    ]
                ]
            ], true);

        return [
            'status' => 200,
            'message' => "Sinkronisasi Berhasil"
        ];
    }
}
