<?php

/**
 * @Author: Sigit
 * @Date:   2018-09-24 16:56:34
 */

namespace app\modules\v1\controllers;

use Yii;

use app\modules\v1\models\Antrian;
use app\modules\v1\models\KonfigAntrian;
use app\modules\v1\models\AntrianView;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\GolonganUmur;
use app\modules\v1\models\InfoJadwalDokterView;
use app\modules\v1\models\InfoPendaftaranOnlineView;
use app\modules\v1\models\JadwalDokter;
use app\modules\v1\models\JadwalPoliklinik;
use app\modules\v1\models\JenisPasien;
use app\modules\v1\models\Kabupaten;
use app\modules\v1\models\Kecamatan;
use app\modules\v1\models\Kelurahan;
use app\modules\v1\models\KlasifikasiPasien;
use app\modules\v1\models\KuotaDokterR;
use app\modules\v1\models\Loket;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\PasienV;
use app\modules\v1\models\PasienMobile;
use app\modules\v1\models\PegawaiMasterView;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\Pekerjaan;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\PendaftaranOnline;
use app\modules\v1\models\Pendidikan;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\ProfilRumahSakit;
use app\modules\v1\models\Propinsi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\InfoJamKunjunganDokterView;
use app\modules\v1\models\InfoJamKunjunganPoliView;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\InfoPendaftaranOlView;
use app\modules\v1\models\InfoKuotaDokterView;
use Da\QrCode\QrCode;
use Doco\components\DocoAccessRule;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoJwtHttpBearerAuth;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\v1\cache\Cache;

class PendaftaranOnlineController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\PendaftaranOnline';
    public $gorupCarabayar;
    public $prefixKonfig;

    public $messageBroker = [
        'daftar-online' => [
            'services' => [
                'Mhg' => [
                    'Appointment' => [
                        'payload' => ['id' => 'pendaftaranol_id'],
                        'successProcess' => true,
                        'state' => 'create',
                        'result' => true
                    ]
                ]
            ]
        ]
    ];

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    // public function behaviors()
    // {
    //     $behaviors = parent::behaviors();

        // $behaviors['authenticator'] = [
        //     'class' => DocoJwtHttpBearerAuth::className(),
        //     'except' => ['daftar-online', 'get-pasien-by-rm', 'get-kelurahan', 'get-kecamatan', 'get-kabupaten', 'get-propinsi', 'get-agama', 'get-jenis-kelamin', 'get-cara-bayar', 'get-ruangan', 'get-dokter', 'get-tanggal-kunjungan', 'get-jam-kunjungan', 'get-klasifikasi-pasien', 'update-status', 'get-pendidikan', 'get-jenis-identitas', 'get-status-perkawinan', 'get-referensi-rs', 'get-pekerjaan', 'get-nrp', 'get-penjamin'],
        // ];

        // $behaviors['access'] = [
        //     'class' => DocoAccessRule::className(),
        //     'except' => ['daftar-online', 'get-pasien-by-rm', 'get-kelurahan', 'get-kecamatan', 'get-kabupaten', 'get-propinsi', 'get-agama', 'get-jenis-kelamin', 'get-cara-bayar', 'get-ruangan', 'get-dokter', 'get-tanggal-kunjungan', 'get-jam-kunjungan', 'get-klasifikasi-pasien', 'update-status', 'get-pendidikan', 'get-jenis-identitas', 'get-status-perkawinan', 'get-referensi-rs', 'get-pekerjaan', 'get-nrp', 'get-penjamin'],
        // ];

        // return $behaviors;
    // }

    /**
     * @todo Action untuk mendapatkan data pendaftaran online
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new InfoPendaftaranOnlineView;
            $advancedFilters = $request->get('advanced-filter', []);
            $query = $model::find();
            $query->orderBy(['pendaftaranol_id' => SORT_DESC]);

            if (isset($advancedFilters['tgl_kunjungan_awal']) && isset($advancedFilters['tgl_kunjungan_akhir'])) {
                $tgl_awal = $advancedFilters['tgl_kunjungan_awal'];
                $tgl_akhir = $advancedFilters['tgl_kunjungan_akhir'];
                unset($advancedFilters['tgl_kunjungan']);

                $query->andWhere(['between', 'tgl_kunjungan', $tgl_awal, $tgl_akhir]);
            } else {
                $query->andWhere(['tgl_kunjungan' => date('Y-m-d', strtotime('NOW'))]);
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);

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

    /**
     * @todo Action untuk menyimpan data pendaftaran online
     * @author Rizal Faidin <rizal@docotel.com>
     * @method POST
     * tgl_pendaftaranol
     * jam_kunjungan
     * pasien_id
     * carabayar_id
     * penjamin_id
     * ruangan_id
     * pegawai_id\
     * no_asuransi
     * no_rujukan
     * jadwaldokter_id
     * jam_mulai
     * jam_tutup
     * no_bpjs
     */
    public function actionDaftarOnline()
    {
        return Yii::$app->docoPlugin->execute('pendaftaran_online');
    }

    /**
     * @param Array post
     * @return boolean available / not available
     **/
    public function checkKuotaAntrian($jadwal_id)
    {
        // cek konfig
        $konfig = $this->actionGetSettingKuota();
        $query = 'jadwaldokter_id';
        switch ($konfig) {
            case DocoConstants::VAR_ID_KUOTA_ANTRIAN_POLIKLINIK:
                $query = 'jadwalbukapoli_id';
                break;
            case DocoConstants::VAR_ID_KUOTA_ANTRIAN_DOKTER:
                $query = 'jadwaldokter_id';
                break;
            case DocoConstants::VAR_ID_TANPA_KUOTA:
                $query = 'jadwaldokter_id';
                break;
            default:
                $query = 'jadwaldokter_id';
        }

        $kuotaDokter = InfoKuotaDokterView::find()
            ->andWhere(['>', 'kuota_tersedia', '0'])
            ->andWhere([
                $query => $jadwal_id,
                'is_online' => true
            ])
            ->one();
        return $kuotaDokter ? true : false;

    }

    /**
     * @todo Action untuk memproses pendaftaran online
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionProses()
    {

        // return directly for sample
        return [
            'status' => 200,
            'data' => Yii::t('app', 'Data berhasil disimpan.')
        ];


        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $post = Yii::$app->request->post();
            $pasien = $post['pasien'];
            $modelPendaftaran = new Pendaftaran();

            if (isset($pasien['no_rekam_medik'])) {
                if (!isset($pasien['pasien_id'])) {
                    return [
                        'status' => 500,
                        'data' => Yii::t('app', 'Data pasien tidak ditemukan.'),
                    ];
                }

                $modelPasien = PasienMobile::findOne($pasien['pasien_id']);

                if ($modelPasien != '') {
                    $modelPasien->attributes = $pasien;
                    $modelPasien->no_rekam_medik = $pasien['no_rekam_medik'];
                    $modelPasien->nama_pasien = $pasien['nama_pasien'];
                    $modelPasien->tempat_lahir = $pasien['tempat_lahir'];
                    $modelPasien->tanggal_lahir = date('Y-m-d', strtotime($pasien['tanggal_lahir']));

                    $klasifikasiPasien = '';
                    // if ($modelPasien->stat_pasien == '') {
                    //     if (isset($pasien['stat_pasien']) && $pasien['stat_pasien'] != '') {
                    //         $modelPasien->stat_pasien = $pasien['stat_pasien'];

                    //         $klasifikasiPasien = JenisPasien::find()->where(['statuspasien_id' => $pasien['stat_pasien']])->one();
                    //     }
                    // }

                    if ($modelPasien->validate()) {
                        if ($modelPasien->save()) {
                            if (!isset($pasien['pendaftaranol_id'])) {
                                return [
                                    'status' => 500,
                                    'data' => Yii::t('app', 'Data pendaftaran online tidak ditemukan.'),
                                ];
                            }

                            $modelPendaftaranOnline = PendaftaranOnline::findOne($pasien['pendaftaranol_id']);

                            if (!empty($modelPendaftaranOnline)) {
                                $modelPendaftaranOnline->status_daftar_ol = isset($pasien['status_daftar_ol']) ? $pasien['status_daftar_ol'] : null;
                                $modelPendaftaranOnline->klasifikasipasien_id = isset($klasifikasiPasien['jenispasien_id']) ? $klasifikasiPasien['jenispasien_id'] : null;
                                $modelPendaftaranOnline->carabayar_id = isset($pasien['carabayar_id']) ? $pasien['carabayar_id'] : null;
                                $modelPendaftaranOnline->penjamin_id = isset($pasien['penjamin_id']) ? $pasien['penjamin_id'] : null;
                                $modelPendaftaranOnline->no_rujukan = isset($pasien['no_rujukan']) ? $pasien['no_rujukan'] : null;
                                $modelPendaftaranOnline->no_asuransi = isset($pasien['no_asuransi']) ? $pasien['no_asuransi'] : null;

                                if ($pasien['status_daftar_ol'] == DocoConstants::VAR_STATUS_DAFTAR_OL_DISETUJUI) {
                                    $modelPendaftaran->tgl_pendaftaran = $modelPendaftaranOnline->tgl_pendaftaranol;
                                    $modelPendaftaran->pasien_id = $modelPendaftaranOnline->pasien_id;
                                    $modelPendaftaran->pegawai_id = $modelPendaftaranOnline->pegawai_id;
                                    $modelPendaftaran->instalasi_id = DocoConstants::VAR_I_RJ;
                                    $modelPendaftaran->carabayar_id = isset($pasien['carabayar_id']) ? $pasien['carabayar_id'] : null;
                                    $modelPendaftaran->penjamin_id = isset($pasien['penjamin_id']) ? $pasien['penjamin_id'] : null;
                                    $modelPendaftaran->antrian_id = $modelPendaftaranOnline->antrian_id;
                                    $modelPendaftaran->ruangan_id = $modelPendaftaranOnline->ruangan_id;
                                    $modelPendaftaran->status_periksa = $modelPendaftaranOnline->status_daftar_ol == DocoConstants::VAR_STATUS_DAFTAR_OL_DISETUJUI ? DocoConstants::STATUS_PERIKSA_ANTRIAN_POLI : DocoConstants::STATUS_PERIKSA_BTL_PRKS;
                                    $modelPendaftaran->status_pasien = DocoConstants::VAR_PAS_B;
                                    $modelPendaftaran->golonganumur_id = $modelPasien->golonganumur_id;
                                    $modelPendaftaran->umur = DocoHelpers::convertDateToAge($modelPasien->tanggal_lahir, 'all');
                                    $modelPendaftaran->shift_id = $modelPendaftaranOnline->shift_id;

                                    if ($modelPendaftaran->save()) {
                                        $modelAntrian = Antrian::findOne($modelPendaftaranOnline->antrian_id);

                                        if ($modelAntrian != '') {
                                            $modelAntrian->pendaftaran_id = $modelPendaftaran->pendaftaran_id;

                                            if ($modelAntrian->save()) {
                                                $modelPendaftaranOnline->pendaftaran_id = $modelPendaftaran->pendaftaran_id;

                                                if ($modelPendaftaranOnline->save()) {
                                                    $transaction->commit();
                                                    return [
                                                        'data' => Yii::t('app', 'Data berhasil disimpan.'),
                                                        'status' => 200
                                                    ];
                                                } else {
                                                    $transaction->rollBack();
                                                    return [
                                                        'status' => 422,
                                                        'data' => $modelPendaftaranOnline->errors,
                                                    ];
                                                }
                                            } else {
                                                $transaction->rollBack();
                                                return [
                                                    'status' => 422,
                                                    'data' => $modelAntrian->errors,
                                                ];
                                            }
                                        } else {
                                            $transaction->rollBack();
                                            return [
                                                'status' => 500,
                                                'data' => Yii::t('app', 'Data antrian tidak ditemukan.'),
                                            ];
                                        }
                                    } else {
                                        $transaction->rollBack();
                                        return [
                                            'status' => 422,
                                            'data' => $modelPendaftaran->errors,
                                        ];
                                    }
                                } else {
                                    if ($modelPendaftaranOnline->save()) {
                                        $transaction->commit();
                                        return [
                                            'status' => 200,
                                            'data' => Yii::t('app', 'Data berhasil disimpan.'),
                                        ];
                                    } else {
                                        $transaction->rollBack();
                                        return [
                                            'status' => 422,
                                            'data' => $modelPendaftaranOnline->errors,
                                        ];
                                    }
                                }
                            } else {
                                $transaction->rollBack();
                                return [
                                    'status' => 500,
                                    'data' => Yii::t('app', 'Data pendaftaran online tidak ditemukan.'),
                                ];
                            }
                        } else {
                            $transaction->rollBack();
                            return [
                                'status' => 422,
                                'data' => $modelPasien->errors,
                            ];
                        }
                    } else {
                        $transaction->rollBack();
                        return [
                            'status' => 422,
                            'data' => $modelPasien->errors,
                        ];
                    }
                }
            } else {
                if (!isset($pasien['pendaftaranol_id'])) {
                    return [
                        'status' => 500,
                        'data' => Yii::t('app', 'Data pendaftaran online tidak ditemukan.'),
                    ];
                }

                $modelPasien = PasienMobile::findOne($pasien['pasien_id']);
                // $modelPasien = $this->getPasien($pasien['pasien_id']);
                $modelPendaftaranOnline = PendaftaranOnline::findOne($pasien['pendaftaranol_id']);

                if ($modelPasien == '') {
                    $transaction->rollBack();
                    return [
                        'status' => 500,
                        'data' => Yii::t('app', 'Data pasien tidak ditemukan.'),
                    ];
                }

                $klasifikasiPasien = '';
                // if ($modelPasien->stat_pasien == '') {
                //     if (isset($pasien['stat_pasien']) && $pasien['stat_pasien'] != '') {
                //         $modelPasien->stat_pasien = $pasien['stat_pasien'];

                //         if (!$modelPasien->save()) {
                //             $transaction->rollBack();
                //             return [
                //                 'status' => 500,
                //                 'data' => Yii::t('app', 'Data pasien Gagal Disimpan.'),
                //             ];
                //         }

                //         $klasifikasiPasien = JenisPasien::find()->where(['statuspasien_id' => $pasien['stat_pasien']])->one();
                //     }
                // }

                if (!empty($modelPendaftaranOnline)) {
                    $modelPendaftaranOnline->status_daftar_ol = isset($pasien['status_daftar_ol']) ? $pasien['status_daftar_ol'] : null;
                    $modelPendaftaranOnline->klasifikasipasien_id = isset($klasifikasiPasien['jenispasien_id']) ? $klasifikasiPasien['jenispasien_id'] : null;
                    $modelPendaftaranOnline->carabayar_id = isset($pasien['carabayar_id']) ? $pasien['carabayar_id'] : null;
                    $modelPendaftaranOnline->penjamin_id = isset($pasien['penjamin_id']) ? $pasien['penjamin_id'] : null;
                    $modelPendaftaranOnline->no_rujukan = isset($pasien['no_rujukan']) ? $pasien['no_rujukan'] : null;
                    $modelPendaftaranOnline->no_asuransi = isset($pasien['no_asuransi']) ? $pasien['no_asuransi'] : null;

                    if ($pasien['status_daftar_ol'] == DocoConstants::VAR_STATUS_DAFTAR_OL_DISETUJUI) {
                        $modelPendaftaran->tgl_pendaftaran = $modelPendaftaranOnline->tgl_pendaftaranol;
                        $modelPendaftaran->pasien_id = $modelPendaftaranOnline->pasien_id;
                        $modelPendaftaran->pegawai_id = $modelPendaftaranOnline->pegawai_id;
                        $modelPendaftaran->instalasi_id = DocoConstants::VAR_I_RJ;
                        $modelPendaftaran->carabayar_id = isset($pasien['carabayar_id']) ? $pasien['carabayar_id'] : null;
                        $modelPendaftaran->penjamin_id = isset($pasien['penjamin_id']) ? $pasien['penjamin_id'] : null;
                        $modelPendaftaran->antrian_id = $modelPendaftaranOnline->antrian_id;
                        $modelPendaftaran->ruangan_id = $modelPendaftaranOnline->ruangan_id;
                        $modelPendaftaran->status_periksa = $modelPendaftaranOnline->status_daftar_ol == DocoConstants::VAR_STATUS_DAFTAR_OL_DISETUJUI ? DocoConstants::STATUS_PERIKSA_ANTRIAN_POLI : DocoConstants::STATUS_PERIKSA_BTL_PRKS;
                        $modelPendaftaran->status_pasien = DocoConstants::VAR_PAS_L;
                        $modelPendaftaran->golonganumur_id = $modelPasien->golonganumur_id;
                        $modelPendaftaran->umur = DocoHelpers::convertDateToAge($modelPasien->tanggal_lahir, 'all');
                        $modelPendaftaran->shift_id = $modelPendaftaranOnline->shift_id;

                        if ($modelPendaftaran->save()) {
                            $modelAntrian = Antrian::findOne($modelPendaftaranOnline->antrian_id);

                            if ($modelAntrian != '') {
                                $modelAntrian->pendaftaran_id = $modelPendaftaran->pendaftaran_id;

                                if ($modelAntrian->save()) {
                                    $modelPendaftaranOnline->pendaftaran_id = $modelPendaftaran->pendaftaran_id;

                                    if ($modelPendaftaranOnline->save()) {
                                        $transaction->commit();
                                        return [
                                            'status' => 200,
                                            'data' => Yii::t('app', 'Data berhasil disimpan.')
                                        ];
                                    } else {
                                        $transaction->rollBack();
                                        return [
                                            'status' => 422,
                                            'data' => $modelPendaftaranOnline->errors,
                                        ];
                                    }
                                } else {
                                    $transaction->rollBack();
                                    return [
                                        'status' => 422,
                                        'data' => $modelAntrian->errors,
                                    ];
                                }
                            } else {
                                $transaction->rollBack();
                                return [
                                    'status' => 500,
                                    'data' => Yii::t('app', 'Data antrian tidak ditemukan.'),
                                ];
                            }
                        } else {
                            $transaction->rollBack();
                            return [
                                'status' => 422,
                                'data' => $modelPendaftaran->errors,
                            ];
                        }
                    } else {
                        if ($modelPendaftaranOnline->save()) {
                            $transaction->commit();
                            return [
                                'data' => Yii::t('app', 'Data berhasil disimpan.'),
                                'status' => 200
                            ];
                        } else {
                            $transaction->rollBack();
                            return [
                                'status' => 422,
                                'data' => $modelPendaftaranOnline->errors,
                            ];
                        }
                    }
                } else {
                    $transaction->rollBack();
                    return [
                        'status' => 500,
                        'data' => Yii::t('app', 'Data pendaftaran online tidak ditemukan.'),
                    ];
                }
            }


        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @todo Action untuk mengubah status pendaftaran online yang telah melewati batas jam buka poliklinik
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionUpdateStatus() {
        try {
            $message = [];
            $data = $this->updateStatus();

            if (!empty($data['data'])) {
                if (is_array($data['data'])) {
                    foreach ($data['data'] as $key => $value) {
                        $message[] = DocoHelpers::encrypt($value);
                    }
                } else {
                    $message[] = $data['data'];
                }
            }

            $new_message['data']["pendaftaran_online"] = $message;
            $mode = Yii::$app->params['mode'];
            return Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'display-antrian-'.$mode,
                'message' => json_encode($new_message),
            ]);
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    /**
    * @controller actionCetakBukti
    * @attribute #logo# => logo
    * @attribute #no_rekam_medik# => no_rekam_medik
    * @attribute #no_identitas_pasien# => no_identitas_pasien
    * @attribute #nama_pasien# => nama_pasien
    * @attribute #ttl_umur# => ttl_umur
    * @attribute #alamat# => alamat
    * @attribute #no_telepon_pasien# => no_telepon_pasien
    * @attribute #jeniskelamin# => jeniskelamin
    * @attribute #agama_nama# => agama_nama
    * @attribute #statusperkawinan# => statusperkawinan
    * @attribute #pendidikan_nama# => pendidikan_nama
    * @attribute #pekerjaan_nama# => pekerjaan_nama
    * @attribute #nama_ayah# => nama_ayah
    * @attribute #nama_ibu# => nama_ibu
    * @attribute #nama_pasangan# => nama_pasangan
    * @attribute #nrp# => nrp
    * @attribute #kesatuan# => kesatuan
    * @attribute #referensirs_nama# => referensirs_nama
    * @attribute #tanggal_sekarang# => tanggal_sekarang
    **/
    public function actionCetakBukti()
    {
        try {
            $id = Yii::$app->request->get('id');

            $pendaftaranOnline = InfoPendaftaranOnlineView::find()->where([
                'pendaftaranol_id' => $id,
            ])
            ->one();

            $profileRs = ProfilRumahSakit::findOne(1);

            if ($pendaftaranOnline == '') {
                \Yii::$app->response->statusCode = 400;
                return [
                    'status' => 500,
                    'message' => Yii::t('app', 'Data pendaftaran online tidak ditemukan.')
                ];
            }

            if ($profileRs == '') {
                \Yii::$app->response->statusCode = 400;
                return [
                    'status' => 500,
                    'message' => Yii::t('app', 'Data profil rumah sakit tidak ditemukan.')
                ];
            }

            $print = new DocoPrint();
            $print->attributes = [
                '#logo#' => Html::img(Yii::$app->urlManagerFrontend->createUrl('').'media/img/profil-rs/'.$profileRs->logo_rumahsakit, ['width' => '200', 'alt' => 'Logo Rumah Sakit']),
                '#no_rekam_medik#' => isset($pendaftaranOnline->no_rekam_medik) ? $pendaftaranOnline->no_rekam_medik : '-',
                '#no_identitas_pasien#' => isset($pendaftaranOnline->no_identitas_pasien) ? $pendaftaranOnline->no_identitas_pasien : '-',
                '#nama_pasien#' => isset($pendaftaranOnline->nama_pasien) ? $pendaftaranOnline->nama_pasien : '-',
                '#ttl_umur#' => $pendaftaranOnline->tanggal_lahir.' / '.DocoHelpers::convertDateToAge($pendaftaranOnline->tanggal_lahir, 'tahun'),
                '#alamat#' => $pendaftaranOnline->alamat_pasien.' '.$pendaftaranOnline->kelurahan.' '.$pendaftaranOnline->kecamatan.' '.$pendaftaranOnline->kabupaten.' '.$pendaftaranOnline->propinsi,
                '#no_telepon_pasien#' => isset($pendaftaranOnline->no_telepon_pasien) ? $pendaftaranOnline->no_telepon_pasien : '-',
                '#jk#' => isset($pendaftaranOnline->jk) ? $pendaftaranOnline->jk : '-',
                '#agama_nama#' => isset($pendaftaranOnline->agama_nama) ? $pendaftaranOnline->agama_nama : '-',
                '#statusperkawinan_nama#' => isset($pendaftaranOnline->statusperkawinan_nama) ? $pendaftaranOnline->statusperkawinan_nama : '-',
                '#pendidikan_nama#' => isset($pendaftaranOnline->pendidikan_nama) ? $pendaftaranOnline->pendidikan_nama : '-',
                '#pekerjaan_nama#' => isset($pendaftaranOnline->pekerjaan_nama) ? $pendaftaranOnline->pekerjaan_nama : '-',
                '#nama_ayah#' => isset($pendaftaranOnline->nama_ayah) ? $pendaftaranOnline->nama_ayah : '-',
                '#nama_ibu#' => isset($pendaftaranOnline->nama_ibu) ? $pendaftaranOnline->nama_ibu : '-',
                '#nama_pasangan#' => $pendaftaranOnline->nama_suami != '' ? $pendaftaranOnline->nama_suami : $pendaftaranOnline->nama_istri,
                '#nrp#' => $pendaftaranOnline->pangkat.' / '.$pendaftaranOnline->nrp_nama.' / '.$pendaftaranOnline->nrp_lainnya,
                '#kesatuan#' => isset($pendaftaranOnline->kesatuan) ? $pendaftaranOnline->kesatuan : '-',
                '#referensirs_nama#' => isset($pendaftaranOnline->referensirs_nama) ? $pendaftaranOnline->referensirs_nama : '-',
                '#tanggal_sekarang#' => DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime('NOW')), false, false),
            ];
            $print->Output();
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
    * @controller actionExportPdf
    * @attribute #tabel# => tabel
    **/
    public function actionExportPdf()
    {
        try {
            $request = Yii::$app->request;
            $model = new InfoPendaftaranOnlineView;
            $advancedFilters = $request->get('advanced-filter', []);
            $query = $model::find();

            if (isset($advancedFilters['tgl_kunjungan_awal']) && isset($advancedFilters['tgl_kunjungan_akhir'])) {
                $tgl_awal = $advancedFilters['tgl_kunjungan_awal'];
                $tgl_akhir = $advancedFilters['tgl_kunjungan_akhir'];
                unset($advancedFilters['tgl_kunjungan']);

                $query->andWhere(['between', 'tgl_kunjungan', $tgl_awal, $tgl_akhir]);
            } else {
                $query->andWhere(['tgl_kunjungan' => date('Y-m-d', strtotime('NOW'))]);
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $data = $query->all();

            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $data[$key]['jam_kunjungan'] = date('h:i', strtotime($value['jam_kunjungan']));
                }
            }

            $print = new DocoPrint();
            $print->attributes = [
                '#tabel#' => $this->renderPartial('pdf', [
                    'data' => $data,
                ]),
            ];
            $print->Output();
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
     * @todo Action untuk exxport excel
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionExportExcel()
    {
        try {
            $request = Yii::$app->request;
            $model = new InfoPendaftaranOnlineView;
            $advancedFilters = $request->get('advanced-filter', []);
            $query = $model::find();

            if (isset($advancedFilters['tgl_kunjungan_awal']) && isset($advancedFilters['tgl_kunjungan_akhir'])) {
                $tgl_awal = $advancedFilters['tgl_kunjungan_awal'];
                $tgl_akhir = $advancedFilters['tgl_kunjungan_akhir'];
                unset($advancedFilters['tgl_kunjungan']);

                $query->andWhere(['between', 'tgl_kunjungan', $tgl_awal, $tgl_akhir]);
            } else {
                $query->andWhere(['tgl_kunjungan' => date('Y-m-d', strtotime('NOW'))]);
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $model = $query->all();
            $data = [];

            if (!empty($model)) {
                foreach ($model as $key => $value) {
                    $data[$key]['no_pendaftaran'] = $value['no_pendaftaranol'];
                    $data[$key]['no_rekam_medik'] = $value['no_rekam_medik'];
                    $data[$key]['nama_pasien'] = $value['nama_pasien'];
                    $data[$key]['no_asuransi'] = $value['no_pendaftaranol'];
                    $data[$key]['poli_tujuan'] = $value['ruangan_nama'];
                    $data[$key]['dokter'] = $value['nama_pegawai'];
                    $data[$key]['cara_bayar'] = $value['carabayar_nama'];
                    $data[$key]['jam_mulai_pelayanan'] = date('h:i', strtotime($value['jam_kunjungan']));
                    $data[$key]['tanggal_daftar'] = date('j F Y', strtotime($value['tgl_pendaftaran']));
                    $data[$key]['tanggal_kunjungan'] = date('j F Y', strtotime($value['tgl_kunjungan']));
                }
            }

            $filePath = DocoHelpers::exportExcel(Yii::t('app', 'Data Pendaftaran Online'), $data, [], array("uploadPath" => "./uploads"),[],[],true);

            $filePath->save('php://output');
            die;
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
     * @todo Action untuk mendapatkan bundle data untuk kebutuhan portal pendaftaran online
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetBundleData()
    {
        try {
            $listPoliTujuan = Ruangan::find()->where(['instalasi_id' => DocoConstants::VAR_I_RJ])->all();
            $listDokter = PegawaiMasterView::find()->where(['kelompokpegawai_id' => DocoConstants::KELOMPOK_PEGAWAI_DOKTER])->all();
            $listCaraBayar = CaraBayar::find()->all();
            $listStatus = $this->getLookup(DocoConstants::VAR_LOOKUP_TYPE_STATUS_DAFTAR_OL);

            return [
                'listPoliTujuan' => $listPoliTujuan,
                'listDokter' => $listDokter,
                'listCaraBayar' => $listCaraBayar,
                'listStatus' => $listStatus,
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

    /**
     * @todo Action untuk mendapatkan bundle data untuk kebutuhan portal pendaftaran online
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetBundleDataVerifikasi($id = null)
    {
        try {
            $pendaftaranOnline = $this->actionGetPendaftaranOnline($id);

            $pasien = '';
            if ($pendaftaranOnline != '') {
                if ($pendaftaranOnline['pasien_id'] != '') {
                    $pasien_id = $pendaftaranOnline['pasien_id'];

                    $pasien = PasienV::find()->where(['pasien_id' => $pasien_id])->one();
                }
            }

            $klasifikasiPasien = JenisPasien::find()->all();
            $caraBayar = CaraBayar::find()->all();

            return [
                'pendaftaranOnline' => $pendaftaranOnline,
                'klasifikasiPasien' => $klasifikasiPasien,
                'caraBayar' => $caraBayar,
                'pasien' => $pasien,
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

    /**
     * @todo Action untuk mendapatkan bundle data untuk kebutuhan scanner pendaftaran online
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetBundleDataScanner($no = null)
    {
        try {
            $pendaftaranOnline = $this->actionGetPendaftaranOnlineByNoPendaftaran($no);
            $klasifikasiPasien = KlasifikasiPasien::find()->all();

            return [
                'pendaftaranOnline' => $pendaftaranOnline,
                'klasifikasiPasien' => $klasifikasiPasien,
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

    /**
     * @todo Action untuk mendapatkan pendaftaran online by no pendaftaran
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetPendaftaranOnlineByNoPendaftaran($no = null)
    {
        try {
            $model = (new \yii\db\Query())
            ->select([
                InfoPendaftaranOnlineView::tableName().'.*',
                PasienV::tableName().'.kelurahan_nama',
                PasienV::tableName().'.kecamatan_nama',
                PasienV::tableName().'.kabupaten_nama',
                PasienV::tableName().'.jenis_kelamin',
            ])
            ->from(InfoPendaftaranOnlineView::tableName())
            ->where([InfoPendaftaranOnlineView::tableName().'.status_daftar_ol' => DocoConstants::VAR_STATUS_DAFTAR_OL_BELUM_DIPROSES])
            ->leftJoin(PasienV::tableName(), PasienV::tableName().'.pasien_id = '.InfoPendaftaranOnlineView::tableName().'.pasien_id')
            ->orderBy([InfoPendaftaranOnlineView::tableName().'.pendaftaranol_id' => SORT_DESC]);

            if ($no) {
                $model->andWhere([InfoPendaftaranOnlineView::tableName().'.no_pendaftaranol' => strtoupper($no)]);
            }

            $pendaftaranOnline = $model->all();

            if (!empty($pendaftaranOnline)) {
                if (count($pendaftaranOnline) == 1) {
                    $pendaftaranOnline = $pendaftaranOnline[0];
                }

                return $pendaftaranOnline;
            } else {
                return [
                    'status' => 500,
                    'message' => Yii::t('app', 'Data pendaftaran online tidak ditemukan.'),
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
     * @todo Action untuk mendapatkan data penjamin portal
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetPenjaminPortal()
    {
        try {
            $get = Yii::$app->request->get();

            $model = Penjamin::find()->andWhere(['is_online' => true]);

            if (isset($get['penjamin_id']) && $get['penjamin_id'] != '') {
                $model->andWhere(['penjamin_id' => $get['penjamin_id']]);
            }

            if (isset($get['carabayar_id']) && $get['carabayar_id'] != '') {
                $model->andWhere(['carabayar_id' => $get['carabayar_id']]);
            }

            $penjamin = $model->all();

            if (!empty($penjamin)) {
                return $penjamin;
            } else {
                return [
                    'status' => 500,
                    'message' => Yii::t('app', 'Data penjamin tidak ditemukan.')
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
     * @todo Action untuk mendapatkan data pasien berdasarkan nomor rekam medik dan tanggal lahir
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetPasienByRm($no_rekam_medik, $tanggal_lahir)
    {
        try {
            if ($no_rekam_medik == '') {
                \Yii::$app->response->statusCode = 400;
                return [
                    'message' => Yii::t('app', 'Nomor Rekam Medik tidak boleh kosong.'),
                    'status' => 400
                ];
            }

            if ($tanggal_lahir == '') {
                \Yii::$app->response->statusCode = 400;
                return [
                    'message' => Yii::t('app', 'Tanggal Lahir tidak boleh kosong.'),
                    'status' => 400
                ];
            }

            $pasien = (new \yii\db\Query())
            ->select([
                PasienV::tableName().'.pasien_id',
                PasienV::tableName().'.no_rekam_medik',
                PasienV::tableName().'.nama_pasien',
                PasienV::tableName().'.tanggal_lahir',
                PasienV::tableName().'.alamat_pasien',
                PasienV::tableName().'.propinsi_id',
                PasienV::tableName().'.propinsi_nama',
                PasienV::tableName().'.kabupaten_id',
                PasienV::tableName().'.kabupaten_nama',
                PasienV::tableName().'.kecamatan_id',
                PasienV::tableName().'.kecamatan_nama',
                PasienV::tableName().'.kelurahan_id',
                PasienV::tableName().'.kelurahan_nama',
                PasienV::tableName().'.no_mobile_pasien',
                PasienV::tableName().'.no_telepon_pasien',
                PasienV::tableName().'.agama AS agama_id',
                PasienV::tableName().'.agama_pasien AS agama_nama',
                PasienV::tableName().'.jeniskelamin AS jenis_kelamin_id',
                PasienV::tableName().'.jenis_kelamin AS jenis_kelamin_nama',
                PasienV::tableName().'.nama_ayah',
                PasienV::tableName().'.nama_ibu',
            ])
            ->from(PasienV::tableName())
            ->andWhere(['no_rekam_medik' => $no_rekam_medik, 'tanggal_lahir' => $tanggal_lahir])
            ->one();

            if ($pasien) {
                return $pasien;
            } else {
                \Yii::$app->response->statusCode = 422;
                return Yii::t('app', 'Data pasien tidak ditemukan.');
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
     * @todo Action untuk mendapatkan data pendaftaran online
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetPendaftaranOnline($id = null)
    {
        try {
            $model = (new \yii\db\Query())
            ->select([
                InfoPendaftaranOnlineView::tableName().'.*',
                PasienV::tableName().'.kelurahan_nama',
                PasienV::tableName().'.kecamatan_nama',
                PasienV::tableName().'.kabupaten_nama',
                PasienV::tableName().'.jenis_kelamin',
                PasienV::tableName().'.tempat_lahir',
            ])
            ->from(InfoPendaftaranOnlineView::tableName())
            ->leftJoin(PasienV::tableName(), PasienV::tableName().'.pasien_id = '.InfoPendaftaranOnlineView::tableName().'.pasien_id')
            ->orderBy([InfoPendaftaranOnlineView::tableName().'.pendaftaranol_id' => SORT_DESC]);

            if ($id) {
                $model->andWhere([InfoPendaftaranOnlineView::tableName().'.pendaftaranol_id' => $id]);
            }

            $pendaftaranOnline = $model->all();

            if (!empty($pendaftaranOnline)) {
                if (count($pendaftaranOnline) == 1) {
                    $pendaftaranOnline = $pendaftaranOnline[0];
                }

                return $pendaftaranOnline;
            } else {
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => Yii::t('app', 'Data kabupaten tidak ditemukan.'),
                    'status' => 500
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
     * @todo Action untuk mendapatkan data kelurahan bisa berdasarkan id atau tidak berdasarkan id atau berdasarkan kecamatan id
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetKelurahan($id = null, $kecamatan_id = null)
    {
        try {
            $model = Kelurahan::find();

            if ($id) {
                $model->andWhere(['kelurahan_id' => $id]);
            }

            if ($kecamatan_id) {
                $model->andWhere(['kecamatan_id' => $kecamatan_id]);
            }

            $kelurahan = $model->all();

            if (!empty($kelurahan)) {
                return $kelurahan;
            } else {
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => Yii::t('app', 'Data kelurahan tidak ditemukan.'),
                    'status' => 500
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
     * @todo Action untuk mendapatkan data kecamatan bisa berdasarkan id atau tidak berdasarkan id atau berdasarkan kabupaten id
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetKecamatan($id = null, $kabupaten_id = null)
    {
        try {
            $model = Kecamatan::find();

            if ($id) {
                $model->andWhere(['kecamatan_id' => $id]);
            }

            if ($kabupaten_id) {
                $model->andWhere(['kabupaten_id' => $kabupaten_id]);
            }

            $kecamatan = $model->all();

            if (!empty($kecamatan)) {
                return $kecamatan;
            } else {
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => Yii::t('app', 'Data kecamatan tidak ditemukan.'),
                    'status' => 500
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
     * @todo Action untuk mendapatkan data kabupaten bisa berdasarkan id atau tidak berdasarkan id atau berdasarkan propinsi id
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetKabupaten($id = null, $propinsi_id = null)
    {
        try {
            $model = Kabupaten::find();

            if ($id) {
                $model->andWhere(['kabupaten_id' => $id]);
            }

            if ($propinsi_id) {
                $model->andWhere(['propinsi_id' => $propinsi_id]);
            }

            $kabupaten = $model->all();

            if (!empty($kabupaten)) {
                return $kabupaten;
            } else {
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => Yii::t('app', 'Data kabupaten tidak ditemukan.'),
                    'status' => 500
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
     * @todo Action untuk mendapatkan data propinsi bisa berdasarkan id atau tidak berdasarkan id
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetPropinsi($id = null)
    {
        try {
            $model = Propinsi::find();

            if ($id) {
                $model->andWhere(['propinsi_id' => $id]);
            }

            $propinsi = $model->all();

            if (!empty($propinsi)) {
                return $propinsi;
            } else {
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => Yii::t('app', 'Data propinsi tidak ditemukan.'),
                    'status' => 500
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
     * @todo Action untuk mendapatkan data agama bisa berdasarkan id atau tidak berdasarkan id
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetAgama($id = null)
    {
        try {
            $model = (new \yii\db\Query())
            ->select([
                Lookup::tableName().'.lookup_id AS agama_id',
                Lookup::tableName().'.lookup_name AS agama_nama',
            ])
            ->from(Lookup::tableName())
            ->andWhere(['lookup_type' => DocoConstants::VAR_LOOKUP_TYPE_AGAMA]);

            if ($id) {
                $model->andWhere(['lookup_id' => $id]);
            }

            $agama = $model->all();

            if (!empty($agama)) {
                return $agama;
            } else {
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => Yii::t('app', 'Data agama tidak ditemukan.'),
                    'status' => 500
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
     * @todo Action untuk mendapatkan data jenis kelamin bisa berdasarkan id atau tidak berdasarkan id
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetJenisKelamin($id = null)
    {
        try {
            $model = (new \yii\db\Query())
            ->select([
                Lookup::tableName().'.lookup_id AS jenis_kelamin_id',
                Lookup::tableName().'.lookup_name AS jenis_kelamin_nama',
            ])
            ->from(Lookup::tableName())
            ->andWhere(['lookup_type' => DocoConstants::VAR_LOOKUP_TYPE_JENIS_KELAMIN]);

            if ($id) {
                $model->andWhere(['lookup_id' => $id]);
            }

            $jenis_kelamin = $model->all();

            if (!empty($jenis_kelamin)) {
                return $jenis_kelamin;
            } else {
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => Yii::t('app', 'Data jenis kelamin tidak ditemukan.'),
                    'status' => 500
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
     * @todo Action untuk mendapatkan data cara bayar bisa berdasarkan id atau tidak berdasarkan id
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetCaraBayar($id = null)
    {
        try {
            $model = CaraBayar::find();

            if ($id) {
                $model->andWhere(['carabayar_id' => $id, 'is_online' => 1]);
            }

            $cara_bayar = $model->all();

            if (!empty($cara_bayar)) {
                return $cara_bayar;
            } else {
                \Yii::$app->response->statusCode = 500;
                return Yii::t('app', 'Data cara bayar tidak ditemukan.');
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
     * @todo Action untuk mendapatkan data penjamin
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetPenjamin($penjamin_id = null, $carabayar_id = null, $is_online = true)
    {
        try {
            $model = Penjamin::find();

            if ($is_online) {
                $model->andWhere(['is_online' => true]);
            }

            if ($penjamin_id) {
                $model->andWhere(['penjamin_id' => $penjamin_id]);
            }

            if ($carabayar_id) {
                $model->andWhere(['carabayar_id' => $carabayar_id]);
            }

            $penjamin = $model->all();

            if (!empty($penjamin)) {
                return $penjamin;
            } else {
                \Yii::$app->response->statusCode = 500;
                return Yii::t('app', 'Data penjamin tidak ditemukan.');
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
     * @todo Action untuk mendapatkan data klinik (ruangan) bisa berdasarkan id atau tidak berdasarkan id
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetRuanganLama($id = null, $hari_id = null)
    {
        try {
            $model = InfoKuotaDokterView::find()->where([
                'instalasi_id' => DocoConstants::VAR_I_RJ, //VAR_I_RJ = 1
                'is_active' => true,
                'is_online' => true,
            ]);
            $model->andWhere(['!=', 'kuota_masuk', 0]);

            if ($id) {
                $model->andWhere(['ruangan_id' => $id]);
            }

            if ($hari_id) {
                $model->andWhere(['hari' => $hari_id]);
            }

            $ruangan = $model->all();

            if (!empty($ruangan)) {
                $modelRuangan = Ruangan::find();

                foreach ($ruangan as $key => $value) {
                    $ruanganIds[] = $value['ruangan_id'];
                }
                $modelRuangan->andWhere(['ruangan_id' => $ruanganIds]);

                $ruangan = $modelRuangan->all();

                if (count($ruangan) == 0) {
                    \Yii::$app->response->statusCode = 200;
                    return Yii::t('app', 'Data ruangan tidak ditemukan.');
                } else {
                    return $ruangan;
                }
            } else {
                \Yii::$app->response->statusCode = 200;
                return Yii::t('app', 'Data ruangan tidak ditemukan.');
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 200;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 200;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @todo Action untuk mendapatkan data klinik (ruangan) berdasarkan tanggal
     * @author Aris Munandar <aris.m@docotel.com>
     */
    public function actionGetRuangan($hari_id = null, $hari_tgl = null)
    {
        return Yii::$app->docoPlugin->execute('pendaftaran_online_get_ruangan');
    }

    /**
     * @todo Action untuk mendapatkan data dokter dengan parameter ruangan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDokterLama($id = null, $ruangan_id = null, $hari_id = null, $flagHari = false)
    {
        try {
            $model = InfoJadwalDokterView::find()->where([
                'is_active' => true
            ])->andWhere(['!=', 'kuota_online', 0]);

            if ($id) {
                $model->andWhere(['pegawai_id' => $id]);
            }

            if ($ruangan_id) {
                $model->andWhere(['ruangan_id' => $ruangan_id]);
            }

            if ($hari_id) {
                $model->andWhere(['hari_jadwalbuka' => $hari_id]);
                // cek hari pesan, ketika sama, batasi dengan waktu selesai jadwal dokter
                if ((date('N') + 74) == $hari_id) {
                    $model->andWhere(['>=', 'waktu_selesai', date('H:i:s')]);
                }
            }

            $dokter = $model->all();

            if (!empty($dokter)) {
                if (count($dokter) == 0) {
                    \Yii::$app->response->statusCode = 422;
                    return Yii::t('app', 'Data dokter tidak ditemukan.');
                } else {
                    $list_hari = $data_dokter = [];
                    if($flagHari){
                        foreach ($dokter as $key => $value) { //buat groupping hari berdasarkan data
                            $list_hari[$value['hari_jadwalbuka']] = [
                                'hari_id' => $value['hari_jadwalbuka'],
                                'hari' => $value['hari'],
                                'dokter' => []
                            ];
                        }
                        foreach ($dokter as $key => $value) { //buat groupping dokter per hari
                            $list_hari[$value['hari_jadwalbuka']]['dokter'][$value['pegawai_id']] = [
                                'dokter_id' => $value['pegawai_id'],
                                'dokter' => $value['nama_pegawai'],
                                'waktu' => date('H:i', strtotime($value['waktu_mulai'])).'-'.date('H:i', strtotime($value['waktu_selesai'])),
                                'kuota_online' => $value['kuota_online'],
                                'jadwaldokter_id' => $value['jadwaldokter_id'],
                            ];
                        }
                        foreach ($list_hari as $key => $value) { //buat ngilangin key yg ada di array hari sama dokter
                            $newArr = [];
                            foreach ($value['dokter'] as $k => $v) {
                                $newArr[] = $v;
                            }
                            $value['dokter'] = $newArr;
                            $data_dokter[] = $value;
                        }
                        return $data_dokter;
                    }else{
                        foreach ($dokter as $key => $value) {
                            $data_dokter[$value['pegawai_id']] = $value;
                        }
                        foreach ($data_dokter as $key => $value) {
                            $list_dokter[] = $value;
                        }
                        return $list_dokter;
                    }
                }
            } else {
                \Yii::$app->response->statusCode = 422;
                return Yii::t('app', 'Data dokter tidak ditemukan.');
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
     * @todo Action untuk mendapatkan data dokter dengan parameter ruangan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDokter($id = null, $ruangan_id = null, $hari_id = null, $flagHari = false)
    {
        return Yii::$app->docoPlugin->execute('pendaftaran_online_get_dokter');
    }

    /**
     * @todo Action untuk mendapatkan data tanggal kunjungan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetTanggalKunjungan($tanggal_kunjungan = null)
    {
        try {
            $hari = [];

            if ($tanggal_kunjungan) {
                # code...
            } else {

                //TODO Aris ambil range hari dari konfig
                $konfig = KonfigSystem::find()->limit(1)->one();
                $awal   = $konfig->reservasi_awal;
                $akhir  = $konfig->reservasi_akhir;

                $lookup_hari = DocoConstants::$look_hari;

                for($x = $awal; $x<= $akhir; $x++){
                    $hari_id = date('Y-m-d h:i:s', strtotime('+'.$x.' day', strtotime('NOW')));
                    $hari_id_indonesia = DocoHelpers::getTanggalIndonesia($hari_id);
                    $hari[] = [
                        'hari_id' => $lookup_hari[$hari_id_indonesia['urutan_hari']],
                        'hari_nama' => $hari_id_indonesia['hari'].', '.date('d', strtotime('+'.$x.' day', strtotime('NOW'))).' '.$hari_id_indonesia['bulan'].' '.$hari_id_indonesia['tahun'],
                        'tanggal' => date('Y-m-d', strtotime('+'.$x.' day', strtotime('NOW')))
                    ];
                }

                // $lookup_hari = DocoConstants::$look_hari;
                // $hari_ini = date('Y-m-d h:i:s', strtotime('NOW'));
                // $besok = date('Y-m-d h:i:s', strtotime('+'.$akhir.' day', strtotime('NOW')));
                // $hari_ini_indonesia = DocoHelpers::getTanggalIndonesia($hari_ini);
                // $besok_indonesia = DocoHelpers::getTanggalIndonesia($besok);

                // if (isset($lookup_hari[$hari_ini_indonesia['urutan_hari']]) && isset($lookup_hari[$besok_indonesia['urutan_hari']])) {
                //     $hari[0]['hari_id'] = $lookup_hari[$hari_ini_indonesia['urutan_hari']];
                //     $hari[0]['hari_nama'] = $hari_ini_indonesia['hari'].', '.date('d', strtotime('NOW')).' '.$hari_ini_indonesia['bulan'].' '.$hari_ini_indonesia['tahun'];
                //     $hari[0]['tanggal'] = date('Y-m-d', strtotime('NOW'));

                //     $hari[1]['hari_id'] = $lookup_hari[$besok_indonesia['urutan_hari']];
                //     $hari[1]['hari_nama'] = $besok_indonesia['hari'].', '.date('d', strtotime('+'.$akhir.' day', strtotime('NOW'))).' '.$besok_indonesia['bulan'].' '.$besok_indonesia['tahun'];
                //     $hari[1]['tanggal'] = date('Y-m-d', strtotime('+'.$akhir.' day', strtotime('NOW')));
                // } else {
                //     \Yii::$app->response->statusCode = 422;
                //     return Yii::t('app', 'Data tanggal kunjungan tidak ditemukan.');
                // }
            }

            return $hari;
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

    // /**
    //  * @todo Action untuk mendapatkan data jam kunjungan
    //  * @author Sigit Arif Munandar <sigit@docotel.com>
    //  */
    // public function actionGetJamKunjungan($hari_id = null, $ruangan_id = null, $pegawai_id = null)
    // {
    //     try {
    //         $model = InfoJamKunjunganDokterView::find()->andWhere(['>', 'kuota_tersedia_online', 0]);

    //         if ($hari_id) {
    //             $model->andWhere(['hari_jadwalbuka' => $hari_id]);
    //         }

    //         if ($ruangan_id) {
    //             $model->andWhere(['ruangan_id' => $ruangan_id]);
    //         }

    //         if ($pegawai_id) {
    //             $model->andWhere(['pegawai_id' => $pegawai_id]);
    //         }

    //         $model->andWhere(['>=', 'waktu_selesai', date('H:i:s', strtotime('NOW'))]);

    //         $jam_kunjungan = $model->all();

    //         if (empty($jam_kunjungan)) {
    //             \Yii::$app->response->statusCode = 422;
    //             return Yii::t('app', 'Data jam kunjungan tidak ditemukan.');
    //         }

    //         foreach ($jam_kunjungan as $key => $value) {
    //             $range = explode('-', $value['waktu']);
    //             $jam_kunjungan[$key]['waktu'] = date('H:i', strtotime($range[0])).'-'.date('H:i', strtotime($range[1]));
    //             $jam_kunjungan[$key]['waktu_mulai'] = date('H:i', strtotime($value['waktu_mulai']));
    //             $jam_kunjungan[$key]['waktu_selesai'] = date('H:i', strtotime($value['waktu_selesai']));
    //         }

    //         return $jam_kunjungan;
    //     } catch (\yii\db\Exception $e) {
    //         \Yii::$app->response->statusCode = 500;
    //         return [
    //             'message' => $e->getMessage()
    //         ];
    //     } catch (\Exception $e) {
    //         \Yii::$app->response->statusCode = 500;
    //         return [
    //             'message' => $e->getMessage()
    //         ];
    //     }
    // }

    /**
     * @todo Action untuk mendapatkan data jam kunjungan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetJamKunjungan($hari_id = null, $ruangan_id = null, $pegawai_id = null, $tanggal = null)
    {
        //$tglPendaftaran = date('Y-m-d ')
        //$tanggal        = DocoHelpers::convDateTime($tanggal.' 23:59:59');
        $tanggal          = date('Y-m-d', strtotime($tanggal));
        $status_tolak = DocoConstants::VAR_STATUS_DAFTAR_OL_DITOLAK;

        try {
            
            $tanggal_saat_ini = date('Y-m-d', strtotime('NOW'));
            $jam_saat_ini     = date('H:i:s', strtotime('NOW'));

            $query = "
             SELECT 
                j.jadwaldokter_id,
                r.instalasi_id,
                i.instalasi_nama,
                j.ruangan_id,
                r.ruangan_nama,
                j.hari_jadwalbuka AS hari_id,
                l.lookup_name AS hari,
                j.waktu_mulai ||'-'|| j.waktu_selesai as waktu,
                j.waktu_mulai AS jam_mulai,
                j.waktu_selesai AS jam_tutup,
                j.shift_id,
                s.shift_nama,
                j.kuota_tersedia as kuota_offline,
                j.kuota_tersedia - coalesce(total_offline,0) AS kuota_tersedia_offline,
                case
                    when j.kuota_online is null then 0
                    else j.kuota_online
                end as kuota_online,
                case
                    when j.kuota_online is null then 0
                    else j.kuota_online
                end - coalesce(total_online,0) AS kuota_tersedia_online
            FROM infojadwaldokter_v j
            LEFT JOIN (
              SELECT jadwaldokter_id,COUNT(*) as total_online FROM pendaftaranol_t
              WHERE DATE(tgl_pendaftaranol) = '$tanggal'
              AND status_daftar_ol <> $status_tolak
              GROUP BY jadwaldokter_id
            ) count_jadwal ON count_jadwal.jadwaldokter_id = j.jadwaldokter_id
            LEFT JOIN ruangan_m r ON j.ruangan_id=r.ruangan_id
            LEFT JOIN instalasi_m i ON r.instalasi_id=i.instalasi_id
            LEFT JOIN shift_m s ON s.shift_id=j.shift_id
            LEFT JOIN lookup_m l ON j.hari_jadwalbuka=l.lookup_id
            LEFT JOIN (
              SELECT jadwaldokter_id,
              COUNT(*) as total_offline FROM antrian_t WHERE DATE(tgl_antrian) = '$tanggal'
              GROUP BY   jadwaldokter_id    
            ) a ON j.jadwaldokter_id=a.jadwaldokter_id
            WHERE j.ruangan_id = '$ruangan_id' AND j.hari_jadwalbuka = '$hari_id' AND j.pegawai_id = '$pegawai_id'
            ";  

            // pengecekan tanngal hari ini di tiadakan untuk memenuhi kebutuhan tetap menampilkan data tadi disable
            // if($tanggal == $tanggal_saat_ini){
            //     $query .= "
            //         AND j.waktu_selesai >= '$jam_saat_ini'
            //     ";  
            // }
            
            $queryAll = Yii::$app->db->createCommand($query)->queryAll();

            if (empty($queryAll)) {
                \Yii::$app->response->statusCode = 500;
                return Yii::t('app', 'Data jam kunjungan tidak ditemukan.');
            }

            return $queryAll;
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
     * @todo Action untuk mendapatkan data klasifikasi pasien
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetKlasifikasiPasien($id = null)
    {
        try {
            $model = KlasifikasiPasien::find();

            if ($id) {
                $model->andWhere(['klasifikasipasien_id' => $id]);
            }

            $klasifikasi_pasien = $model->all();

            if (!empty($klasifikasi_pasien)) {
                return $klasifikasi_pasien;
            } else {
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => Yii::t('app', 'Data klasifikasi pasien tidak ditemukan.'),
                    'status' => 500
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
     * @todo Action untuk mendapatkan data riwayat pendaftaran online
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetRiwayatPendaftaran($id = null, $user_id = null, $additional = null)
    {
        try {
            $model = InfoPendaftaranOnlineView::find()->select([
                'pendaftaranol_id',
                'pasien_id',
                'ruangan_id',
                'pegawai_id',
                'carabayar_id',
                'no_pendaftaranol',
                'tgl_pendaftaran',
                'tgl_kunjungan',
                'jam_kunjungan',
                'nama_pasien',
                'no_rekam_medik',
                'tanggal_lahir',
                'nama_pegawai',
                'carabayar_nama',
                'ruangan_nama',
                'status_daftar',
                'no_antrian AS no_antrian', //old antrian_dokter
                'antrian_dokter_id',
                'antrian_dokter',
                'nama_pasien_ol'
            ])
            ->orderBy(['pendaftaranol_id' => SORT_DESC])
            ->asArray();

            if ($id) {
                $model->andWhere(['pendaftaranol_id' => $id]);
            }

            if ($user_id) {
                $model->andWhere(['created_by' => $user_id]);
            }

            $pendaftaran_online = $model->all();

            if (!empty($pendaftaran_online)) {
                foreach ($pendaftaran_online as $key => $value) {
                    $pendaftaran_online[$key]['tanggal_lahir'] = DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value['tanggal_lahir'])), false, false);
                    $pendaftaran_online[$key]['tgl_pendaftaran'] = DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value['tgl_pendaftaran'])), false, false);
                    $pendaftaran_online[$key]['tgl_kunjungan'] = DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value['tgl_kunjungan'])), false, false);
                    $jam_kunjungan = explode('-', $value['jam_kunjungan']);
                    $pendaftaran_online[$key]['jam_kunjungan'] = date('H:i', strtotime($jam_kunjungan[0])).'-'.date('H:i', strtotime($jam_kunjungan[1]));

                    // Generate nomor antrian pendaftaran
                    $prefix = substr($value['no_pendaftaranol'], 0, 2);
                    $number = substr($value['no_pendaftaranol'], 10);
                    $no_antrian_pendaftaran = $prefix.$number;
                    $pendaftaran_online[$key]['no_antrian_pendaftaran'] = $no_antrian_pendaftaran;
                    $pendaftaran_online[$key]['additional_data'] = $additional;
                }

                return $pendaftaran_online;
            } else {
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => Yii::t('app', 'Data riwayat pendaftaran online tidak ditemukan.'),
                    'status' => 500
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
     * @todo Action untuk mendapatkan data pendidikan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetPendidikan($id = null)
    {
        try {
            $model = Pendidikan::find();

            if ($id) {
                $model->andWhere(['pendidikan_id' => $id]);
            }

            $pendidikan = $model->all();

            if (!empty($pendidikan)) {
                return $pendidikan;
            } else {
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => Yii::t('app', 'Data pendidikan tidak ditemukan.'),
                    'status' => 500
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
     * @todo Action untuk mendapatkan data jenis identitas
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetJenisIdentitas($id = null)
    {
        try {
            $jenis_identitas = [];
            $model = $this->getLookup('jenis_identitas');

            if (!empty($model)) {
                foreach ($model as $key => $value) {
                    $jenis_identitas[$key]['jenisidentitas_id'] = $value['lookup_id'];
                    $jenis_identitas[$key]['jenisidentitas_nama'] = $value['lookup_name'];
                }
            }

            if (!empty($jenis_identitas)) {
                return $jenis_identitas;
            } else {
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => Yii::t('app', 'Data jenis identitas tidak ditemukan.'),
                    'status' => 500
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
     * @todo Action untuk mendapatkan data status perkawinan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetStatusPerkawinan($id = null)
    {
        try {
            $status_perkawinan = [];
            $model = $this->getLookup('status_perkawinan');

            if (!empty($model)) {
                foreach ($model as $key => $value) {
                    $status_perkawinan[$key]['statusperkawinan_id'] = $value['lookup_id'];
                    $status_perkawinan[$key]['statusperkawinan_nama'] = $value['lookup_name'];
                }
            }

            if (!empty($status_perkawinan)) {
                return $status_perkawinan;
            } else {
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => Yii::t('app', 'Data status perkawinan tidak ditemukan.'),
                    'status' => 500
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
     * @todo Action untuk mendapatkan data referensi rumah sakit
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetReferensiRs($id = null)
    {
        try {
            $referensi_rs = [];
            $model = $this->getLookup('no_referensi');

            if (!empty($model)) {
                foreach ($model as $key => $value) {
                    $referensi_rs[$key]['referensirs_id'] = $value['lookup_id'];
                    $referensi_rs[$key]['referensirs_nama'] = $value['lookup_name'];
                }
            }

            if (!empty($referensi_rs)) {
                return $referensi_rs;
            } else {
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => Yii::t('app', 'Data referensi rumah sakit tidak ditemukan.'),
                    'status' => 500
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
     * @todo Action untuk mendapatkan data pekerjaan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetPekerjaan($id = null)
    {
        try {
            $model = Pekerjaan::find();

            if ($id) {
                $model->andWhere(['pekerjaan_id' => $id]);
            }

            $pekerjaan = $model->all();

            if (!empty($pekerjaan)) {
                return $pekerjaan;
            } else {
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => Yii::t('app', 'Data pekerjaan tidak ditemukan.'),
                    'status' => 500
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
     * @todo Action untuk mendapatkan data nrp
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetNrp($id = null)
    {
        try {
            $nrp = [];
            $model = $this->getLookup('nrp');

            if (!empty($model)) {
                foreach ($model as $key => $value) {
                    $nrp[$key]['nrp_id'] = $value['lookup_id'];
                    $nrp[$key]['nrp_nama'] = $value['lookup_name'];
                }
            }

            if (!empty($nrp)) {
                return $nrp;
            } else {
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => Yii::t('app', 'Data NRP tidak ditemukan.'),
                    'status' => 500
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
     * @todo Action untuk mendapatkan data pasien berdasarkan nomor rekam medik dan tanggal lahir
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    private function getPasien($id)
    {
        try {
            if ($id) {
                $pasien = PasienV::find()->where(['pasien_id' => $id])->one();

                if (!empty($pasien)) {
                    return $pasien;
                }
            }

            \Yii::$app->response->statusCode = 500;
            return [
                'message' => Yii::t('app', 'Data pasien tidak ditemukan.'),
                'status' => 500
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

    /**
     * @todo Action untuk mendapatkan data lookup by type
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    private function getLookup($type = null)
    {
        try {
            $lookup = Lookup::find();

            if ($type) {
                $lookup->where(['lookup_type' => $type]);
            }

            return $lookup->all();
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
     * @todo Action untuk mengubah error model yii menjadi array of errors
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    private function parseErrors($errors = [], $one_response = false)
    {
        try {
            $new_errors = [];

            if (!empty($errors)) {
                foreach ($errors as $key => $value) {
                    $new_errors[] = isset($value[0]) ? $value[0] : '-';
                }
            }

            if ($one_response) {
                $new_errors = isset($new_errors[0]) ? $new_errors[0] : [];
            }

            return $new_errors;
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
     * @todo Action untuk update status pendaftaran online
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    private function updateStatus()
    {
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        try {
            $status_tolak = DocoConstants::VAR_STATUS_DAFTAR_OL_DITOLAK;
            $status_belum_proses = DocoConstants::VAR_STATUS_DAFTAR_OL_BELUM_DIPROSES;
            $pendaftaranol_id = [];

            $lookup_hari = DocoConstants::$look_hari;
            $hari_ini = date('Y-m-d h:i:s', strtotime('NOW'));
            $hari_ini = DocoHelpers::getTanggalIndonesia($hari_ini);
            $hari_jadwalbuka = $lookup_hari[$hari_ini['urutan_hari']];

            $jadwal_dokter = InfoJadwalDokterView::find()
            ->andWhere(['<', 'waktu_selesai', date('H:i:s', strtotime('NOW'))])
            ->andWhere(['hari_jadwalbuka' => $hari_jadwalbuka])
            ->all();

            if (!empty($jadwal_dokter)) {
                foreach ($jadwal_dokter as $key => $value) {
                    $jadwaldokter_id = $value['jadwaldokter_id'];

                    $pendaftaranOnline = PendaftaranOnline::find()->select(['pendaftaranol_id'])->andWhere([
                        'jadwaldokter_id' => $value['jadwaldokter_id'],
                        'status_daftar_ol' => $status_belum_proses,
                    ])->asArray()->all();

                    if (!empty($pendaftaranOnline)) {
                        foreach ($pendaftaranOnline as $index => $content) {
                            $pendaftaranol_id[] = $content['pendaftaranol_id'];
                        }

                        $db->createCommand()->update(PendaftaranOnline::tableName(), [
                            'status_daftar_ol' => $status_tolak
                        ], [
                            'jadwaldokter_id' => $value['jadwaldokter_id'],
                            'status_daftar_ol' => $status_belum_proses,
                        ])->execute();
                    }
                }

                $transaction->commit();
                return [
                    'status' => 200,
                    'data' => $pendaftaranol_id,
                    'message' => Yii::t('app', 'Data berhasil diubah.'),
                ];
            } else {
                $transaction->rollback();
                \Yii::$app->response->statusCode = 500;
                return [
                    'status' => 500,
                    'data' => Yii::t('app', 'Data jadwal dokter tidak ditemukan.'),
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
     * @todo Action untuk get data antrian poli
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    private function getDataAntrianPoli()
    {
        $date = date('Y-m-d')." 00:00:00";

        $query = "
            SELECT ruangan_id,ruangan_nama,count(*)
            FROM antrian_v
            WHERE tgl_antrian >= '".$date."' and jenisantrian_id in (".DocoConstants::VAR_JA_P.",".DocoConstants::VAR_JA_PD.")
            GROUP BY ruangan_id,ruangan_nama
            ORDER BY ruangan_nama";

        $result = Yii::$app->db->createCommand($query)->queryAll();

        return $result;
    }

    // public function actionGetJamKunjunganPoli($pegawai_id = null, $hari_id = null, $ruangan_id = null)
    // {
    //     try {
    //         $model = InfoJamKunjunganPoliView::find()->where([
    //             '>',
    //             'kuota_tersedia_online',
    //             0
    //         ])->andWhere([
    //             'instalasi_id' => DocoConstants::INST_ID_RJ,
    //             'is_active' => true
    //         ]);

    //         if ($hari_id) {
    //             $model->andWhere(['hari_id' => $hari_id]);
    //         }

    //         if ($pegawai_id) {
    //             $model->andWhere(['pegawai_id' => $pegawai_id]);
    //         }

    //         if ($ruangan_id) {
    //             $model->andWhere(['ruangan_id' => $ruangan_id]);
    //         }

    //         // $model->andWhere(['>=', 'jam_tutup', date('H:i:s', strtotime('NOW'))]);

    //         $jam_kunjungan = $model->all();

    //         if (empty($jam_kunjungan)) {
    //             \Yii::$app->response->statusCode = 422;
    //             return Yii::t('app', 'Data jam kunjungan tidak ditemukan.');
    //         }

    //         foreach ($jam_kunjungan as $key => $value) {
    //             $range = explode('-', $value['waktu']);
    //             $jam_kunjungan[$key]['waktu'] = date('H:i', strtotime($range[0])).'-'.date('H:i', strtotime($range[1]));
    //             $jam_kunjungan[$key]['jam_mulai'] = date('H:i', strtotime($value['jam_mulai']));
    //             $jam_kunjungan[$key]['jam_tutup'] = date('H:i', strtotime($value['jam_tutup']));
    //         }

    //         return $jam_kunjungan;
    //     } catch (\yii\db\Exception $e) {
    //         \Yii::$app->response->statusCode = 500;
    //         return [
    //             'message' => $e->getMessage()
    //         ];
    //     } catch (\Exception $e) {
    //         \Yii::$app->response->statusCode = 500;
    //         return [
    //             'message' => $e->getMessage()
    //         ];
    //     }
    // }

    public function actionGetJamKunjunganPoli($pegawai_id = null, $hari_id = null, $ruangan_id = null, $tanggal = null)
    {
        //$tglPendaftaran = date('Y-m-d ')
        //$tanggal        = DocoHelpers::convDateTime($tanggal.' 23:59:59');
        $tanggal          = date('Y-m-d', strtotime($tanggal));

        try {
            
            $tanggal_saat_ini = date('Y-m-d', strtotime('NOW'));
            $jam_saat_ini     = date('H:i:s', strtotime('NOW'));

            $query = "
            SELECT 
                j.jadwalbukapoli_id,
                r.instalasi_id,
                i.instalasi_nama,
                j.ruangan_id,
                r.ruangan_nama,
                j.hari AS hari_id,
                l.lookup_name AS hari,
                j.waktu_pelayanan AS waktu,
                j.jam_mulai,
                j.jam_tutup,
                j.shift_id,
                s.shift_nama,
                j.maxantrian_poli as kuota_offline,
                j.maxantrian_poli - coalesce(total_offline,0) AS kuota_tersedia_offline,
                case
                    when j.kuota_online is null then 0
                    else j.kuota_online
                end,
                case
                    when j.kuota_online is null then 0
                    else j.kuota_online
                end - coalesce(total_online,0) AS kuota_tersedia_online
            FROM jadwalbukapoli_m j
            LEFT JOIN (
              SELECT jadwalbukapoli_id,COUNT(*) as total_online FROM pendaftaranol_t
              WHERE DATE(tgl_pendaftaranol) = '$tanggal'
              GROUP BY jadwalbukapoli_id
            ) count_jadwal ON count_jadwal.jadwalbukapoli_id = j.jadwalbukapoli_id
            LEFT JOIN ruangan_m r ON j.ruangan_id=r.ruangan_id
            LEFT JOIN instalasi_m i ON r.instalasi_id=i.instalasi_id
            LEFT JOIN shift_m s ON s.shift_id=j.shift_id
            LEFT JOIN lookup_m l ON j.hari=l.lookup_id
            LEFT JOIN (
              SELECT jadwalbukapoli_id,
              COUNT(*) as total_offline FROM antrian_t WHERE DATE(tgl_antrian) = '$tanggal'
              GROUP BY   jadwalbukapoli_id    
            ) a ON j.jadwalbukapoli_id=a.jadwalbukapoli_id
            WHERE j.ruangan_id = '$ruangan_id' AND j.hari = '$hari_id' 
            ";   

            if($tanggal == $tanggal_saat_ini){
                $query .= "
                    AND j.jam_tutup >= '$jam_saat_ini'
                ";  
            }


            $queryAll = Yii::$app->db->createCommand($query)->queryAll();

            if (empty($queryAll)) {
                \Yii::$app->response->statusCode = 500;
                return Yii::t('app', 'Data jam kunjungan tidak ditemukan.');
            }

            return $queryAll;
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

    public function actionGetSettingKuota()
    {
        try {
            $model = KonfigSystem::find();
            // $setting = $this->getOrSetCache('var_cache_konfig_system', $model, false);
            $setting = $model->one();

            return $setting['kuota_antrian'];
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
     * @todo Fungsi untuk menentukan id jadwal dokter atau id jadwal poli yang dipakai
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    private function getIdJadwal() {
        try {
            $konfig = $this->actionGetSettingKuota();

            switch ($konfig) {
                case DocoConstants::VAR_ID_KUOTA_ANTRIAN_POLIKLINIK:
                    $id = 'jadwalbukapoli_id';
                    break;
                case DocoConstants::VAR_ID_KUOTA_ANTRIAN_DOKTER:
                    $id = 'jadwaldokter_id';
                    break;
                case DocoConstants::VAR_ID_TANPA_KUOTA:
                    $id = 'jadwaldokter_id';
                    break;
                default:
                    $id = 'jadwaldokter_id';
                    break;
            }

            return $id;
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
     * @todo Fungsi untuk membuat antrian
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @edited fungsi set antrian manual
     */
    private function saveAntrianManual($data, $noUrut = null) {
        try {
            $model = new Antrian();
            $id = $this->getIdJadwal();

            $model->pendaftaran_id    = 0;
            $model->ruangan_id        = $data['ruangan_id'];
            $model->carabayar_id      = $data['carabayar_id'];
            // $model->tgl_antrian     = date('Y-m-d H:i:s');
            $model->tgl_antrian       = $data['tgl_pendaftaranol'];
            $model->no_antrian        = $noUrut;
            $model->pasien_id         = $data['pasien_id'];
            $model->pegawai_id        = $data['pegawai_id'];
            $model->status_pasien     = $data['status_pasien'];
            $model->jenisantrian_id   = DocoConstants::VAR_JA_P;
            $model->is_online         = true;
            $model->is_active         = true;
            $model->antrianasal_id    = $data['antrian_id'];
            $model->groupcarabayar_id = null;            
            $model->konfigantrian_id = null;

            if (!is_null($model->carabayar_id)) {
                $modelKonfig = CaraBayar::find()->where([
                    'carabayar_id' => $model->carabayar_id,
                ]);
                $konfig                   = $modelKonfig->one();
                $model->groupcarabayar_id = $konfig->groupcarabayar_id;

                $modelKonfig = KonfigAntrian::find()->where([
                    'jenisantrian_id'   => DocoConstants::VAR_JA_P,
                ]);
                $konfig                  = $modelKonfig->one();
                $model->konfigantrian_id = $konfig->konfigantrian_id;
            }

            if ($id == 'jadwaldokter_id') {
                $model->jadwaldokter_id = $data['jadwaldokter_id'];
            } else {
                $model->jadwalbukapoli_id = $data['jadwalbukapoli_id'];
            }

            if ($model->validate() && $model->save()) {
                /* hapus referensi antrian MANUAL (prevent double)*/
                // $qUpdateAntrian = "
                //     UPDATE antrian_t SET 
                //         is_active = false,
                //         is_deleted = true,
                //         jadwaldokter_id = NULL,
                //         jadwalbukapoli_id = NULL
                //     WHERE antrian_id = {$model->antrianasal_id}
                // ";
                // Yii::$app->db->createCommand($qUpdateAntrian)->execute();
                return $model;
            } else {
                return $model->errors;
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
     * @todo Fungsi untuk membuat antrian
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    private function saveAntrian($data, $dataSlot = null) {
        try {
            $model = new Antrian();
            $id = $this->getIdJadwal();
            $additional = [];

            $model->ruangan_id        = $data['ruangan_id'];
            $model->carabayar_id      = $data['carabayar_id'];
            // $model->tgl_antrian     = date('Y-m-d H:i:s');
            $model->tgl_antrian       = $data['tgl_pendaftaranol'];
            $model->no_antrian        = 'x';
            $model->pasien_id         = $data['pasien_id'];
            $model->pegawai_id        = $data['pegawai_id'];
            $model->status_pasien     = $data['status_pasien'];
            $model->jenisantrian_id   = DocoConstants::VAR_JA_P;
            $model->is_online         = true;
            $model->groupcarabayar_id = null;            
            $model->konfigantrian_id = null;
            $model->slot_sequence = !empty($dataSlot) ? $dataSlot['slot_sequence'] : null;

            if (!is_null($model->carabayar_id)) {
                $modelKonfig = CaraBayar::find()->where([
                    'carabayar_id' => $model->carabayar_id,
                ]);
                $konfig                   = $modelKonfig->one();
                $model->groupcarabayar_id = $konfig->groupcarabayar_id;

                $modelKonfig = KonfigAntrian::find()->where([
                    'jenisantrian_id'   => DocoConstants::VAR_JA_P,
                    'groupcarabayar_id' => $model->groupcarabayar_id
                ]);
                $konfig                  = $modelKonfig->one();
                $model->konfigantrian_id = $konfig->konfigantrian_id;
            } else if (is_null($model->carabayar_id) && !is_null($this->gorupCarabayar)) {
                $modelKonfig = KonfigAntrian::find()->where([
                    'jenisantrian_id'   => DocoConstants::VAR_JA_P,
                    'groupcarabayar_id' => $this->gorupCarabayar
                ]);
                $konfig                  = $modelKonfig->one();
                $model->konfigantrian_id = $konfig->konfigantrian_id;
            }

            if(!empty($modelKonfig)) {
                $this->prefixKonfig = $konfig->kode_antrian;
            }

            if ($id == 'jadwaldokter_id') {
                $model->jadwaldokter_id = $data['jadwaldokter_id'];
            } else {
                $model->jadwalbukapoli_id = $data['jadwalbukapoli_id'];
            }

            if ($model->validate() && $model->save()) {
                return $model;
            } else {
                return $model->errors;
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
    * @controller actionCetakKarcis
    * @attribute #no_pendaftaranol# => No Booking
    * @attribute #tgl_pendaftaranol# => Tanggal Booking
    * @attribute #ruangan_nama# => Poliklinik
    * @attribute #pegawai_nama# => Dokter
    * @attribute #jam_kunjungan# => Jam Kunjungan
    * @attribute #carabayar_nama# => Cara Bayar
    * @attribute #penjamin_nama# => Penjamin
    * @attribute #qrcode# => QR code
    * @attribute #no_rekam_medik# => Nomor Rekam Medik
    * @attribute #nama_pasien# => Nama Pasien
    * @attribute #tanggal_lahir# => Tanggal Lahir Pasien
    **/
    public function actionCetakKarcis()
    {
        try {
            $id = Yii::$app->request->get('id');

            $pendaftaranOnline = InfoPendaftaranOnlineView::find()->where([
                'pendaftaranol_id' => $id,
            ])->one();

            $print = new DocoPrint();

            $qrCode = (new QrCode($pendaftaranOnline->no_pendaftaranol))
            ->setSize(50)
            ->setMargin(3)
            ->useForegroundColor(0, 0, 0);

            $print->attributes = [
                '#no_pendaftaranol#' => isset($pendaftaranOnline->no_pendaftaranol) ? $pendaftaranOnline->no_pendaftaranol : '-',
                '#tgl_pendaftaranol#' => isset($pendaftaranOnline->tgl_pendaftaran) ? DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($pendaftaranOnline->tgl_pendaftaran)), false, false) : '-',
                '#ruangan_nama#' => isset($pendaftaranOnline->ruangan_nama) ? $pendaftaranOnline->ruangan_nama : '-',
                '#pegawai_nama#' => isset($pendaftaranOnline->nama_pegawai) ? $pendaftaranOnline->nama_pegawai : '-',
                '#jam_kunjungan#' => isset($pendaftaranOnline->jam_kunjungan) ? $pendaftaranOnline->jam_kunjungan : '-',
                '#carabayar_nama#' => isset($pendaftaranOnline->carabayar_nama) ? $pendaftaranOnline->carabayar_nama : '-',
                '#penjamin_nama#' => isset($pendaftaranOnline->penjamin_nama) ? $pendaftaranOnline->penjamin_nama : '-',
                '#qrcode#' => '<img src="data:image/png;base64,'.base64_encode($qrCode->writeString()).'">',
                '#no_rekam_medik#' => isset($pendaftaranOnline->no_rekam_medik) ? $pendaftaranOnline->no_rekam_medik : '-',
                '#nama_pasien#' => isset($pendaftaranOnline->nama_pasien) ? $pendaftaranOnline->nama_pasien : '-',
                '#tanggal_lahir#' => isset($pendaftaranOnline->tanggal_lahir) ? DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($pendaftaranOnline->tanggal_lahir)), false, false) : '-',
            ];
            $print->Output();
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

    public function actionGetAllJamKunjungan($tanggal = null)
    {
        //$tglPendaftaran = date('Y-m-d ')
        //$tanggal        = DocoHelpers::convDateTime($tanggal.' 23:59:59');
        // $tanggal          = date('Y-m-d', strtotime($tanggal));

        try {
            
            // $tanggal_saat_ini = date('Y-m-d', strtotime('NOW'));
            // $jam_saat_ini     = date('H:i:s', strtotime('NOW'));

            // $query = "
            // SELECT 
            //     j.jadwalbukapoli_id,
            //     r.instalasi_id,
            //     i.instalasi_nama,
            //     j.ruangan_id,
            //     r.ruangan_nama,
            //     j.hari AS hari_id,
            //     l.lookup_name AS hari,
            //     j.waktu_pelayanan AS waktu,
            //     j.jam_mulai,
            //     j.jam_tutup,
            //     j.shift_id,
            //     s.shift_nama,
            //     j.maxantrian_poli as kuota_offline,
            //     j.maxantrian_poli - coalesce(total_offline,0) AS kuota_tersedia_offline,
            //     case
            //         when j.kuota_online is null then 0
            //         else j.kuota_online
            //     end,
            //     case
            //         when j.kuota_online is null then 0
            //         else j.kuota_online
            //     end - coalesce(total_online,0) AS kuota_tersedia_online
            // FROM jadwalbukapoli_m j
            // LEFT JOIN (
            //   SELECT jadwalbukapoli_id,COUNT(*) as total_online FROM pendaftaranol_t
            //   WHERE DATE(tgl_pendaftaranol) = '$tanggal'
            //   GROUP BY jadwalbukapoli_id
            // ) count_jadwal ON count_jadwal.jadwalbukapoli_id = j.jadwalbukapoli_id
            // LEFT JOIN ruangan_m r ON j.ruangan_id=r.ruangan_id
            // LEFT JOIN instalasi_m i ON r.instalasi_id=i.instalasi_id
            // LEFT JOIN shift_m s ON s.shift_id=j.shift_id
            // LEFT JOIN lookup_m l ON j.hari=l.lookup_id
            // LEFT JOIN (
            //   SELECT jadwalbukapoli_id,
            //   COUNT(*) as total_offline FROM antrian_t WHERE DATE(tgl_antrian) = '$tanggal'
            //   GROUP BY   jadwalbukapoli_id    
            // ) a ON j.jadwalbukapoli_id=a.jadwalbukapoli_id ";   

            // if($tanggal == $tanggal_saat_ini){
            //     $query .= "
            //         AND j.jam_tutup >= '$jam_saat_ini'
            //     ";  
            // }

            $query = "SELECT * FROM jadwalbukapoli_v order by ruangan_id ASC,hari ASC, jam_mulai ASC";


            $queryAll = Yii::$app->db->createCommand($query)->queryAll();

            

            if (empty($queryAll)) {
                \Yii::$app->response->statusCode = 500;
                return Yii::t('app', 'Data jam kunjungan tidak ditemukan.');
            }

            return $queryAll;
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
}