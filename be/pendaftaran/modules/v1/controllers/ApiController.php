<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use app\modules\v1\cache\Cache;
use app\modules\v1\components\Wilayah;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;
use Doco\components\DocoRestActiveFilter;
use yii\base\DynamicModel;

use app\modules\v1\models\PasienReplikasi;
use app\modules\v1\models\PasienReplikasiView;
use app\modules\v1\models\PendaftaranReplikasi;
use app\modules\v1\models\PendaftaranReplikasiView;
use app\modules\v1\models\PenjualanResepReplikasi;
use app\modules\v1\models\PasienV;
use app\modules\v1\models\Diagnosa;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\MasterTarifTindakanView;
use app\modules\v1\models\PaketDetailView;
use app\modules\v1\models\InfPasienPenunjang;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\LoginJknR;
use app\modules\v1\models\InfoDataPendaftaranView;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\InfopendaftaranV;
use app\modules\v1\models\Antrian;
use app\modules\v1\models\DokterView;
use app\modules\v1\models\JadwalDokter;
use app\modules\v1\models\InfoKuotaDokterView;
use app\modules\v1\models\InfoJadwalDokterView;
use Doco\models\pendaftaran\PendaftaranOnline;
use app\modules\v1\models\Bpjs;
use app\modules\v1\models\BpjsJkn;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\InfoPendaftaranOnlineView;
use Doco\models\KonsulPoli;


use app\modules\v1\payload\ApiPasienPayload;
use app\modules\v1\payload\TipePasien;
use app\modules\v1\payload\Kunjungan;
use app\modules\v1\payload\Antrian as PayloadAntrian;
use app\modules\v1\payload\ApiBslPayload;
use app\modules\v1\payload\Pasien as PayloadPasien;
use app\modules\v1\payload\Rujukan as PayloadRujukan;
use app\modules\v1\payload\PjPasien;
use app\modules\v1\payload\AsuransiForm;
use app\modules\v1\payload\BpjsNewForm;
use app\modules\v1\payload\WilayahPayload;

use app\modules\v1\components\BpjsController;
use app\modules\v1\models\InfoPendaftaranOlView;
use Doco\Services\KasirService;

use Doco\components\DocoAccessRule;
use Doco\components\DocoActiveController;
use Doco\components\DocoJwtHttpBearerAuth;
use Doco\components\PendaftaranHelpers;
use yii\db\Expression;

class ApiController extends BpjsController
{
    /**
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @todo Model class
     */
    public $modelClass = '';
    const KTP = 'KTP';
    const PASPOR = 'PASPOR';
    const LAINNYA = 'LAINNYA';
    public static $jenisIdentitas = [
        self::KTP => 94,
        self::PASPOR => 99,
        self::LAINNYA => 100
    ];
    const DATA_NULL = 'Data tidak ditemukan.';
    const IDENTITAS = 'identitas';
    const JENISIDENTITAS = 'jenisidentitas';
    const ADDITIONAL_PASIEN = 'additional_pasien';
    const TINDAKAN = 'tindakan';
    const PAKET = 'paket';

    const PASIEN_R = 'pasien_r';
    const PENDAFTARAN_R = 'pendaftaran_r';
    // public static $date = date('Y-m-d H:i:s');
    // public static $waktu = DocoHelpers::generateTimeStamp(self::$date);

    public $messageBroker = [
        'batal-antrian-jkn' => [
            'services' => [
                'Sirs' => [
                    'CancelReservasi' => [
                        'payload' => ['no_pendaftaranol' => 'kodebooking'],
                        'successProcess' => true,
                        'state' => 'cancel',
                        'result' => true
                    ]
                ]
            ]
        ],
        'check-in-jkn' => [
            'services' => [
                'Sirs' => [
                    'StatusUpdateJkn' => [
                        'payload' => ['kodebooking' => 'kodebooking','waktu' => 'waktu'],
                        'successProcess' => true,
                        'result' => true
                    ]
                ]
            ]
        ],
        'update-admisi-jkn' => [
            'services' => [
                'Sirs' => [
                    'StatusUpdateJkn' => [
                        'payload' => ['pendaftaranol_id' => 'pendaftaranol_id','waktu' => 'waktu'],
                        'successProcess' => true,
                        'taskid' => ['1', '2'],
                        'result' => true
                    ]
                ]
            ]
        ],
        'auto-pulang-pasien-jkn' => [
            'services' => [
                'Sirs' => [
                    'AutoPulangJkn' => [
                        'successProcess' => true,
                        'result' => true
                    ]
                ]
            ]
        ]
    ];

    /**
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @todo Fungsi custom verbs
     * @return array $results
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => DocoJwtHttpBearerAuth::className(),
            'except' => ['sync-registration', 'callback-sync-registration', 'update-sync-registration', 'callback-update-sync-registration', 'update-sync-patient', 'callback-update-sync-patient'
                        ,'auto-pulang-pasien-jkn'
                        ,'referensi-jadwal-dokter-jkn'
                        ,'check-in-pasien-routing'
                        ,'check-in-jkn'
                    ],
        ];

        $behaviors['access'] = [
            'class' => DocoAccessRule::className(),
            'except' => ['sync-registration', 'callback-sync-registration', 'update-sync-registration', 'callback-update-sync-registration', 'update-sync-patient', 'callback-update-sync-patient','update-antrian-jkn'
                        ,'auto-pulang-pasien-jkn'
                        ,'referensi-jadwal-dokter-jkn'
                        ,'check-in-pasien-routing'
                        ,'check-in-jkn'
                    ]
        ];

        return $behaviors;
    }

    /**
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @todo Function untuk get data pendaftaran
     * @return array $results
     */
    public function actionSyncRegistration()
    {
        $limit = DocoConstansId::actionGetAdditional('odoo_admission_size') ?: 100;
        $pasien = $this->dataPasien($limit);
        $pendaftaran = $this->dataPendaftaran($limit - count($pasien));

        return array(
            'pasien' => $pasien,
            'pendaftaran' => $pendaftaran
        );
    }

    /**
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @todo Function untuk get data update pendaftaran
     * @return array $results
     */
    public function actionUpdateSyncRegistration()
    {
        $pendaftaran = $this->dataUpdatePendaftaran();

        return $pendaftaran;
    }

    /**
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @todo Function untuk get data update pasien
     * @return array $results
     */
    public function actionUpdateSyncPatient()
    {
        $pasien = $this->dataUpdatePasien();

        return $pasien;
    }

    /**
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @todo Function untuk update status pasien_r.is_sent = true & pendaftaran_r.is_sent = true
     * @return array $results
     */
    public function actionCallbackSyncRegistration()
    {
        $pasien = array();
        $idPasien = array();
        $pendaftaran = array();
        $idPendaftaran = array();
        $idPenjualanResep = array();
        $data = Yii::$app->request->post('data');
        $uidSercon = Yii::$app->request->post('uid');

        if ($data && array_key_exists('patient', $data)) {
            $pasien = $data['patient'];
        }

        if ($data && array_key_exists('registration', $data)) {
            $pendaftaran = $data['registration'];
        }

        if (!empty($pasien)) {
            foreach ($pasien as $key => $value) {
                $id = $value['id'];
                $uid = $uidSercon;
                $syncResponse = json_encode($value['response']);
                $syncPayload = json_encode($value['payload']);

                $model = PasienReplikasi::find()->where([
                    'pasien_id' => $id,
                    'is_sending' => true,
                    'keterangan' => 'INSERT'
                ])->one();

                if ($model) {
                    if ($value['is_error']) {
                        // $model->is_sending = false;
                    } else {
                        $model->is_sent = true;
                    }

                    $model->id_sync_sercon = $uid;
                    $model->sync_response = $syncResponse;
                    $model->sync_payload = $syncPayload;
                    $model->save();
                }
            }
        }

        if (!empty($pendaftaran)) {
            foreach ($pendaftaran as $key => $value) {
                $id = $value['id'];
                $uid = $uidSercon;
                $syncResponse = json_encode($value['response']);
                $syncPayload = json_encode($value['payload']);

                if ($value['is_penjualanresep']) {
                    $id = substr($id, 4);

                    $model = PenjualanResepReplikasi::find()->where([
                        'penjualanresep_id' => $id,
                        'is_sending' => true
                    ])->one();
                } else {
                    $model = PendaftaranReplikasi::find()->where([
                        'pendaftaran_id' => $id,
                        'is_sending' => true,
                        'keterangan' => 'INSERT'
                    ])->one();
                }

                if ($model) {
                    if ($value['is_error']) {
                        // $model->is_sending = false;
                    } else {
                        $model->is_sent = true;
                    }

                    $model->id_sync_sercon = $uid;
                    $model->sync_response = $syncResponse;
                    $model->sync_payload = $syncPayload;
                    $model->save();
                }
            }
        }

        return [
            'message' => 'Callback success.'
        ];
    }

    /**
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @todo Function untuk update status pasien_r.is_sent = true & pendaftaran_r.is_sent = true
     * @return array $results
     */
    public function actionCallbackUpdateSyncRegistration()
    {
        $pendaftaran = Yii::$app->request->post('data');
        $uidSercon = Yii::$app->request->post('uid');

        if (!empty($pendaftaran)) {
            foreach ($pendaftaran as $key => $value) {
                $id = $value['id'];
                $uid = $uidSercon;
                $syncResponse = json_encode($value['response']);
                $syncPayload = json_encode($value['payload']);

                $model = PendaftaranReplikasi::find(true)->where([
                    'id' => $id
                ])->one();

                if ($model) {
                    if ($value['is_error']) {
                        $model->is_sending = false;
                    } else {
                        $model->is_sent = true;
                    }

                    $model->id_sync_sercon = $uid;
                    $model->sync_response = $syncResponse;
                    $model->sync_payload = $syncPayload;
                    $model->save();
                }
            }
        }

        return [
            'message' => 'Callback success.'
        ];
    }

    /**
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @todo Function untuk update status pasien_r.is_sent = true & pendaftaran_r.is_sent = true
     * @return array $results
     */
    public function actionCallbackUpdateSyncPatient()
    {
        $pasien = Yii::$app->request->post('data');
        $uidSercon = Yii::$app->request->post('uid');

        if (!empty($pasien)) {
            foreach ($pasien as $key => $value) {
                $id = $value['id'];
                $uid = $uidSercon;
                $syncResponse = json_encode($value['response']);
                $syncPayload = json_encode($value['payload']);

                $model = PasienReplikasi::find(true)->where([
                    'id' => $id
                ])->one();

                if ($model) {
                    if ($value['is_error']) {
                        $model->is_sending = false;
                    } else {
                        $model->is_sent = true;
                    }

                    $model->id_sync_sercon = $uid;
                    $model->sync_response = $syncResponse;
                    $model->sync_payload = $syncPayload;
                    $model->save();
                }
            }
        }

        return [
            'message' => 'Callback success.'
        ];
    }

    /**
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @todo Function untuk get data pasien sekaligus update status pasien_r.is_sending = true
     * @return array
     */
    private function dataPasien($limit = 100)
    {
        $ids = array();
        $stringIds = '';
        $return = array();

        $model = PasienReplikasiView::find()
        ->where(['is_sending' => false, 'is_sent' => false, 'keterangan' => 'INSERT'])
        ->orderBy(['pasien_id' => SORT_ASC])
        ->limit(ceil($limit / 3))
        ->asArray()
        ->all();


        // Rollback ================= Start
        $dataToRollback = PasienReplikasi::find()
        ->where(['is_sending' => true, 'is_sent' => false, 'keterangan' => 'INSERT'])
        ->andWhere(['IS', 'id_sync_sercon', new \yii\db\Expression('null')])
        ->andWhere(['<', 'tgl_proses', date('Y-m-d H:i:s', strtotime('-1 HOUR'))])
        ->orderBy(['pasien_id' => SORT_ASC])
        ->limit(30)
        ->all();

        // Ambil data yang kemungkinan nyangkut dengan kondisi is_sending = true, tp is_sent nya masih false setelah 1 hari

        if (!empty($dataToRollback)) {
            $rollBackIds = array();
            foreach ($dataToRollback as $key => $value) {
                $rollBackIds[] = $value->id;
            }
            if (!empty($rollBackIds)) {
                $stringRollBackIds = implode(',', $rollBackIds);
                Yii::$app->db->createCommand("UPDATE pasien_r SET is_sending = false WHERE id IN ({$stringRollBackIds})")->execute();
            }
        }
        // Rollback ================= End

        if (!empty($model)) {
            foreach ($model as $key => $value) {
                $ids[] = $value['id'];
                $return[$key]['sync_id_api'] = (string)$value['sync_id_api'];
                $return[$key]['registration_code'] = $value['registration_code'] ? (string)$value['registration_code'] : '-';
                $return[$key]['name'] = $value['name'] ? (string)$value['name'] : '-';
                $return[$key]['display_name'] = $value['display_name'] ? (string)$value['display_name'] : '-';
                $return[$key]['title_name'] = $value['title_name'] ? (string)$value['title_name'] : '-';
                $return[$key]['date_of_birth'] = $value['date_of_birth'] ? (string)$value['date_of_birth'] : '-';
                $return[$key]['gender'] = $value['gender'] ? $value['gender'] : '-';
                $return[$key]['phone'] = $value['phone'] ? (string)$value['phone'] : '-';
                $return[$key]['mobile'] = $value['mobile'] ? (string)$value['mobile'] : '-';
                $return[$key]['fax'] = $value['fax'] ? (string)$value['fax'] : '-';
                $return[$key]['email'] = $value['email'] ? (string)$value['email'] : '-';
                $return[$key]['street'] = $value['street'] ? (string)$value['street'] : '-';
                $return[$key]['street2'] = $value['street2'] ? (string)$value['street2'] : '-';
                $return[$key]['street3'] = $value['street3'] ? (string)$value['street3'] : '-';
                $return[$key]['city'] = $value['city'] ? (string)$value['city'] : '-';
                $return[$key]['zip'] = $value['zip'] ? (string)$value['zip'] : '-';
                $return[$key]['passport'] = $value['passport'] ? (string)$value['passport'] : '-';
                $return[$key]['ktp'] = $value['ktp'] ? (string)$value['ktp'] : '-';
                $return[$key]['wipro_block'] = $value['wipro_block'];
                $return[$key]['patient'] = $value['patient'];
                $return[$key]['active'] = $value['active'];
                $return[$key]['sync_type'] = (string)$value['sync_type'];
                $return[$key]['penjualanresep'] = false;

                if (!empty($value['no_identitas'])) {
                    if ($value['no_identitas'] != "-") {
                        foreach (json_decode($value['no_identitas']) as $k => $v) {
                             if ($v->jenisidentitas == 94 || $v->jenisidentitas == "94") { // ktp
                                  $return[$key]['ktp'] = (string)$v->no_identitas_pasien;
                             } else if ($v->jenisidentitas == 99 || $v->jenisidentitas == "99") { // passport
                                  $return[$key]['passport'] = (string)$v->no_identitas_pasien;
                             }
                        }
                    }
                }
            }

            $stringIds = implode(',', $ids);

            Yii::$app->db->createCommand("
                UPDATE pasien_r SET is_sending = true WHERE id IN ({$stringIds})
            ")->execute();

        } else {
            return [];
        }

        return $return;
    }

    /**
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @todo Function untuk get data pendaftaran sekaligus update status pendaftaran_r.is_sending = true
     * @return array
     */
    private function dataPendaftaran($limit = 100)
    {
        $ids = array();
        $idsPenjualanResep = array();
        $siaPenjualanResep = array();
        $stringIds = '';
        $stringIdsPenjualanResep = '';
        $pendaftaran = array();
        $penjualanResep = array();
        $return = array();

        $modelPenjualanResep = PendaftaranReplikasiView::find()
        ->where(['is_sending' => false, 'is_sent' => false, 'keterangan' => 'INSERT', 'jenis' => ['BEBAS', 'KARYAWAN']])
        ->orderBy(['id' => SORT_ASC])
        ->limit(ceil($limit / 2))
        ->asArray()
        ->all();

        $model = PendaftaranReplikasiView::find()
        ->where(['is_sending' => false, 'is_sent' => false, 'keterangan' => 'INSERT', 'jenis' => 'RUMAH_SAKIT'])
        ->orderBy(['id' => SORT_ASC])
        ->limit($limit - count($modelPenjualanResep))
        ->asArray()
        ->all();


        // Rollback ================= Start

        $dataPendaftaranToRollback = PendaftaranReplikasi::find()
        ->where(['is_sending' => true, 'is_sent' => false, 'keterangan' => 'INSERT'])
        ->andWhere(['<', 'tgl_proses', date('Y-m-d H:i:s', strtotime('-1 HOUR'))])
        ->orderBy(['id' => SORT_ASC])
        ->limit(25)
        ->all();

        $dataPenjualanToRollback = PenjualanResepReplikasi::find()
        ->where(['is_sending' => true, 'is_sent' => false, 'keterangan' => 'INSERT'])
        ->andWhere(['<', 'tgl_proses', date('Y-m-d H:i:s', strtotime('-1 HOUR'))])
        ->orderBy(['id' => SORT_ASC])
        ->limit(25)
        ->all();

        // Ambil data yang kemungkinan nyangkut dengan kondisi is_sending = true, tp is_sent nya masih false setelah 1 hari

        if (!empty($dataPendaftaranToRollback)) {
            $rollBackIds = array();
            foreach ($dataPendaftaranToRollback as $key => $value) {
                $rollBackIds[] = $value->id;
            }
            if (!empty($rollBackIds)) {
                $stringRollBackIds = implode(',', $rollBackIds);
                Yii::$app->db->createCommand("UPDATE pendaftaran_r SET is_sending = false WHERE id IN ({$stringRollBackIds})")->execute();
            }
        }

        if (!empty($dataPenjualanToRollback)) {
            $rollBackIds = array();
            foreach ($dataPenjualanToRollback as $key => $value) {
                $rollBackIds[] = $value->id;
            }
            if (!empty($rollBackIds)) {
                $stringRollBackIds = implode(',', $rollBackIds);
                Yii::$app->db->createCommand("UPDATE penjualanresep_r SET is_sending = false WHERE id IN ({$stringRollBackIds})")->execute();
            }
        }

        // Rollback ================= End


        if (empty($model) && empty($modelPenjualanResep)) {
            return [];
        }

        if (!empty($model)) {
            foreach ($model as $key => $value) {
                $ids[] = $value['id'];
                $return[] = [
                    'sync_id_api' => (string)$value['sync_id_api'],
                    'name' => $value['name'] ? (string)$value['name'] : '-',
                    'billno' => $value['billno'] ? (string)$value['billno'] : '-',
                    'confirmation_date' => $value['confirmation_date'] ? (string)$value['confirmation_date'] : '',
                    'partner_id' => $value['partner_id'] ? (string)$value['partner_id'] : '-',
                    'date_order' => $value['date_order'] ? (string)$value['date_order'] : '-',
                    'patient_type' => $value['patient_type'] ? (string)$value['patient_type'] : '-',
                    'payer_id' => $value['payer_id'] ? (string)$value['payer_id'] : '-',
                    'payer_code' => $value['payer_code'] ? (string)$value['payer_code'] : '-',
                    'payer_type' => $value['payer_type'] ? (string)$value['payer_type'] : '-',
                    'sync_type' => (string)$value['sync_type'],
                    'state' => (string)$value['state'],
                    'nama_asuransi' => (string)$value['nama_asuransi'],
                    'no_asuransi' => (string)$value['no_asuransi'],
                    'personal_amount' => (string)$value['personal_amount'],
                    'payer_amount' => (string)$value['payer_amount'],
                    'total_amount' => (string)$value['total_amount'],
                    'base_price_unit' => array_key_exists('base_price_unit', $value) ? $value['base_price_unit'] : null,
                    'base_price_total' => array_key_exists('base_price_total', $value) ? $value['base_price_total'] : null,
                    'brand' => array_key_exists('brand', $value) ? $value['brand'] : null,
                    'manufacturer' => array_key_exists('manufacturer', $value) ? $value['manufacturer'] : null,
                    'discharge_date' => array_key_exists('discharge_date', $value) ? $value['discharge_date'] : null,
                    'discharge_reason' => array_key_exists('discharge_reason', $value) ? $value['discharge_reason'] : null,
                    'referral_number' => array_key_exists('referral_number', $value) ? $value['referral_number'] : null,
                    'referral_doc_id' => array_key_exists('referral_doc_id', $value) ? $value['referral_doc_id'] : null,
                    'penjualanresep' => false
                ];
            }

            if (!empty($ids)) {
                $stringIds = implode(',', $ids);

                Yii::$app->db->createCommand("
                    UPDATE pendaftaran_r SET is_sending = true WHERE id IN ({$stringIds})
                ")->execute();
            }
        }

        if (!empty($modelPenjualanResep)) {
            foreach ($modelPenjualanResep as $key => $value) {
                $idsPenjualanResep[] = $value['id'];
                $return[] = [
                    'sync_id_api' => (string)$value['sync_id_api'],
                    'name' => $value['name'] ? (string)$value['name'] : '-',
                    'billno' => $value['billno'] ? (string)$value['billno'] : '-',
                    'confirmation_date' => $value['confirmation_date'] ? (string)$value['confirmation_date'] : '',
                    'partner_id' => (string)$value['partner_id'],
                    'date_order' => $value['date_order'] ? (string)$value['date_order'] : '-',
                    'patient_type' => $value['patient_type'] ? (string)$value['patient_type'] : '-',
                    'payer_id' => $value['payer_id'] ? (string)$value['payer_id'] : '-',
                    'payer_code' => $value['payer_code'] ? (string)$value['payer_code'] : '-',
                    'payer_type' => $value['payer_type'] ? (string)$value['payer_type'] : '-',
                    'sync_type' => (string)$value['sync_type'],
                    'state' => (string)$value['state'],
                    'nama_asuransi' => (string)$value['nama_asuransi'],
                    'no_asuransi' => (string)$value['no_asuransi'],
                    'personal_amount' => (string)$value['personal_amount'],
                    'payer_amount' => (string)$value['payer_amount'],
                    'total_amount' => (string)$value['total_amount'],
                    'base_price_unit' => array_key_exists('base_price_unit', $value) ? $value['base_price_unit'] : null,
                    'base_price_total' => array_key_exists('base_price_total', $value) ? $value['base_price_total'] : null,
                    'brand' => array_key_exists('brand', $value) ? $value['brand'] : null,
                    'manufacturer' => array_key_exists('manufacturer', $value) ? $value['manufacturer'] : null,
                    'discharge_date' => array_key_exists('discharge_date', $value) ? $value['discharge_date'] : null,
                    'discharge_reason' => array_key_exists('discharge_reason', $value) ? $value['discharge_reason'] : null,
                    'referral_number' => array_key_exists('referral_number', $value) ? $value['referral_number'] : null,
                    'referral_doc_id' => array_key_exists('referral_doc_id', $value) ? $value['referral_doc_id'] : null,
                    'penjualanresep' => true
                ];
            }

            if (!empty($idsPenjualanResep)) {
                $stringIdsPenjualanResep = implode(',', $idsPenjualanResep);

                Yii::$app->db->createCommand("
                    UPDATE penjualanresep_r SET is_sending = true WHERE id IN ({$stringIdsPenjualanResep})
                ")->execute();
            }
        }

        return $return;
    }

    private function getDataPasien()
    {
        $result = PasienV::find();
        return $result;
    }

    private function getPasienPenunjang($pendaftaranId)
    {
        $data = InfPasienPenunjang::find()->select(['pendaftaran_id','pasienmasukpenunjang_id','no_pendaftaran','instalasi_id', 'ruangan_id'])->where(['pendaftaran_id' => $pendaftaranId])->one();

        return $data;
    }

    /**
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @todo Function untuk get data update pendaftaran sekaligus update status pendaftaran_r.is_sending = true
     * @return array
     */
    private function dataUpdatePendaftaran()
    {
        $ids = array();
        $stringIds = '';
        $return = array();

        $model = PendaftaranReplikasiView::find()
        ->where([
            'is_sending' => false,
            'is_sent' => false,
            'keterangan' => ['UPDATE', 'DELETE'],
            'jenis' => 'RUMAH_SAKIT'
        ])
        ->orderBy([
            'id' => SORT_ASC
        ])
        ->limit(DocoConstansId::actionGetAdditional('odoo_admission_size') ?: 100)
        ->asArray()
        ->all();

        if (!empty($model)) {
            foreach ($model as $key => $value) {
                $ids[] = $value['id'];
                $return[$key]['id'] = (string)$value['id'];
                $return[$key]['sync_id_api'] = (string)$value['sync_id_api'];
                $return[$key]['name'] = $value['name'] ? (string)$value['name'] : '-';
                $return[$key]['billno'] = $value['billno'] ? (string)$value['billno'] : '-';
                $return[$key]['confirmation_date'] = $value['confirmation_date'] ? (string)$value['confirmation_date'] : '';
                $return[$key]['partner_id'] = $value['partner_id'] ? (string)$value['partner_id'] : '-';
                $return[$key]['date_order'] = $value['date_order'] ? (string)$value['date_order'] : '-';
                $return[$key]['patient_type'] = $value['patient_type'] ? (string)$value['patient_type'] : '-';
                $return[$key]['payer_id'] = $value['payer_id'] ? (string)$value['payer_id'] : '-';
                $return[$key]['payer_code'] = $value['payer_code'] ? (string)$value['payer_code'] : '-';
                $return[$key]['payer_type'] = $value['payer_type'] ? (string)$value['payer_type'] : '-';
                $return[$key]['sync_type'] = (string)$value['sync_type'];
                $return[$key]['state'] = (string)$value['state'];
                $return[$key]['nama_asuransi'] = (string)$value['nama_asuransi'];
                $return[$key]['nomor_asuransi'] = (string)$value['no_asuransi'];
                $return[$key]['base_price_unit'] = array_key_exists('base_price_unit', $value) ? $value['base_price_unit'] : null;
                $return[$key]['base_price_total'] = array_key_exists('base_price_total', $value) ? $value['base_price_total'] : null;
                $return[$key]['brand'] = array_key_exists('brand', $value) ? $value['brand'] : null;
                $return[$key]['manufacturer'] = array_key_exists('manufacturer', $value) ? $value['manufacturer'] : null;
                $return[$key]['discharge_date'] = array_key_exists('discharge_date', $value) ? $value['discharge_date'] : null;
                $return[$key]['discharge_reason'] = array_key_exists('discharge_reason', $value) ? $value['discharge_reason'] : null;
                $return[$key]['referral_number'] = array_key_exists('referral_number', $value) ? $value['referral_number'] : null;
                $return[$key]['referral_doc_id'] = array_key_exists('referral_doc_id', $value) ? $value['referral_doc_id'] : null;
                if ($value['keterangan'] === 'DELETE') {
                    $return[$key]['wipro_block'] = true;
                }
            }

            $stringIds = implode(',', $ids);

            Yii::$app->db->createCommand("
                UPDATE pendaftaran_r SET is_sending = true WHERE id IN ({$stringIds})
            ")->execute();

        } else {
            return [];
        }

        return $return;
    }

    /**
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @todo Function untuk get data update pasien sekaligus update status pasien_r.is_sending = true
     * @return array
     */
    private function dataUpdatePasien()
    {
        $ids = [];
        $stringIds = '';
        $return = array();

        $model = PasienReplikasiView::find()
        ->where([
            'is_sending' => false,
            'is_sent' => false,
            'keterangan' => ['UPDATE', 'DELETE']
        ])
        ->orderBy([
            'id' => SORT_ASC
        ])
        ->asArray()
        ->limit(25)
        ->all();

        if (!empty($model)) {
            foreach ($model as $key => $value) {
                $ids[] = $value['id'];
                $return[$key]['id'] = (string)$value['id'];
                $return[$key]['sync_id_api'] = (string)$value['sync_id_api'];
                $return[$key]['registration_code'] = $value['registration_code'] ? (string)$value['registration_code'] : '-';
                $return[$key]['name'] = $value['name'] ? (string)$value['name'] : '-';
                $return[$key]['display_name'] = $value['display_name'] ? (string)$value['display_name'] : '-';
                $return[$key]['title_name'] = $value['title_name'] ? (string)$value['title_name'] : '-';
                $return[$key]['date_of_birth'] = $value['date_of_birth'] ? (string)$value['date_of_birth'] : '-';
                $return[$key]['gender'] = $value['gender'] ? $value['gender'] : '-';
                $return[$key]['phone'] = $value['phone'] ? (string)$value['phone'] : '-';
                $return[$key]['mobile'] = $value['mobile'] ? (string)$value['mobile'] : '-';
                $return[$key]['fax'] = $value['fax'] ? (string)$value['fax'] : '-';
                $return[$key]['email'] = $value['email'] ? (string)$value['email'] : '-';
                $return[$key]['street'] = $value['street'] ? (string)$value['street'] : '-';
                $return[$key]['street2'] = $value['street2'] ? (string)$value['street2'] : '-';
                $return[$key]['street3'] = $value['street3'] ? (string)$value['street3'] : '-';
                $return[$key]['city'] = $value['city'] ? (string)$value['city'] : '-';
                $return[$key]['zip'] = $value['zip'] ? (string)$value['zip'] : '-';
                $return[$key]['passport'] = $value['passport'] ? (string)$value['passport'] : '-';
                $return[$key]['ktp'] = $value['ktp'] ? (string)$value['ktp'] : '-';
                $return[$key]['wipro_block'] = $value['wipro_block'];
                $return[$key]['patient'] = $value['patient'];
                $return[$key]['active'] = $value['active'];
                $return[$key]['sync_type'] = (string)$value['sync_type'];
                $return[$key]['penjualanresep'] = false;

                if (!empty($value['no_identitas'])) {
                    if ($value['no_identitas'] != "-") {
                        foreach (json_decode($value['no_identitas']) as $k => $v) {
                            if ($v->jenisidentitas == 94 || $v->jenisidentitas == "94") {
                                $return[$key]['ktp'] = (string)$v->no_identitas_pasien;
                            } else if ($v->jenisidentitas == 99 || $v->jenisidentitas == "99") {
                                $return[$key]['passport'] = (string)$v->no_identitas_pasien;
                            }
                        }
                    }
                }
            }

            $stringIds = implode(',', $ids);

            Yii::$app->db->createCommand("
                UPDATE pasien_r SET is_sending = true WHERE id IN ({$stringIds})
            ")->execute();

        } else {
            return [];
        }

        return $return;
    }

    /*
    * Get Data Pasien
    * @params => norm,nama_pasien,tgl_lahir,jeniskelamin
    * @return array
    */
    public function actionFindPatient()
    {
        $request = Yii::$app->request;
        $params = $request->get();
        $model = new ApiPasienPayload;
        $model->attributes = $params;
        $model->no_identitas_pasien = $request->get('no_identitas_pasien');

        if($model->validate()) {
            $result = $this->getDataPasien();

            if(!empty($model->norm)){
                $result->andWhere(['like', 'LOWER(no_rekam_medik)', $model->norm]);
            }

            if(!empty($model->no_identitas_pasien)){
                $ktpId = DocoConstants::CONS_ID_KTP;
                $result->andWhere(['no_identitas_pasien' => $model->no_identitas_pasien]);
                $result->orWhere(new Expression("
                    additional_pasien ILIKE '%\"jenisidentitas\":\"{$ktpId}\"%'
                    AND additional_pasien LIKE '%\"no_identitas_pasien\":\"{$model->no_identitas_pasien}\"%'
                "));
            }

            if(!empty($model->nama_pasien)){
                $result->andWhere(['like', 'LOWER(nama_pasien)', strtolower($model->nama_pasien)]);
            }

            if(!empty($model->tgl_lahir)){
                $result->andWhere(['tanggal_lahir' => $model->tgl_lahir]);
            }

            if(!empty($model->jeniskelamin)){
                $result->andWhere(['jeniskelamin' => $model->jeniskelamin]);
            }

            if(!empty($model->tgl_awal) && !empty($model->tgl_akhir)){
                $tgl_awal = date('Y-m-d', strtotime($model->tgl_awal));
                $tgl_akhir = date('Y-m-d', strtotime($model->tgl_akhir));
                $result->andWhere(['between', 'date(tgl_pembuatan)', $tgl_awal, $tgl_akhir]);
                $result = $result->asArray()->all();
            } else {

                $result = $result->limit(50)->asArray()->all();
            }

            if(empty($result)){
                $errorMessage = self::DATA_NULL;
                return $this->responseJson(200, $errorMessage, $model);
            }else{
                foreach($result as $k => $v){
                    $cek_json = DocoHelpers::isJson($result[$k][self::ADDITIONAL_PASIEN]);
                    if(!empty($result[$k][self::ADDITIONAL_PASIEN]) && $cek_json){
                        $result[$k][self::ADDITIONAL_PASIEN] = json_decode($result[$k][self::ADDITIONAL_PASIEN], true);
                        foreach($result[$k][self::ADDITIONAL_PASIEN] as $kk => $vv){
                            switch ($result[$k][self::ADDITIONAL_PASIEN][$kk][self::JENISIDENTITAS]) {
                                case self::$jenisIdentitas[self::KTP]:
                                    $result[$k][self::ADDITIONAL_PASIEN][$kk][self::IDENTITAS] = self::KTP;
                                    break;
                                case self::$jenisIdentitas[self::PASPOR]:
                                    $result[$k][self::ADDITIONAL_PASIEN][$kk][self::IDENTITAS] = self::PASPOR;
                                    break;
                                case self::$jenisIdentitas[self::LAINNYA]:
                                    $result[$k][self::ADDITIONAL_PASIEN][$kk][self::IDENTITAS] = self::LAINNYA;
                                    break;
                            }
                        }
                    }
                }
                return $result;
            }
        }
        else {
            return [
                'status' => 422,
                'data' => $model->errors,
            ];
        }
    }

    /*
    * Pendaftaran Penunjang BSL
    * @return data pendaftaran
    */
    public function actionSavePenunjang()
    {
        $request = Yii::$app->request;
        $post = $request->post();

        $isBpjs = $isIndoLab = false;
        $pasienBaru = $isBsl = true;
        $tagihanKarcis = $tagihanPenunjang = false;

        //** Validasi Error */
        $errorParse = [];

        //** Tindakan Karcis */
        $listTindakan = $tmp = $sepNew = $komponen = [];
        $tmpPenunjang = $listTagihan = [];

        //** Payload */
        $rujukan = [];
        $pjPasien = [];
        $dataAntrian = [];
        $asuransi = [];
        $additional_identitas = [];
        $bpjs = [];
        $response = [];
        $tmpData = [];

        $payload = new TipePasien;
        $payloadKunjungan = new Kunjungan;
        $payloadAntrian = new PayloadAntrian;
        $pasienPayLoad = new PayloadPasien;

        $pasienPayLoad->attributes = !empty($post['pasien']) ? $post['pasien'] : [];
        $payload->attributes = !empty($post['tipe_pasien']) ? $post['tipe_pasien'] : [];
        $payloadKunjungan->attributes = !empty($post['kunjungan']) ? $post['kunjungan'] : [];

        //** Set Asuransi */
        if (!empty($post['asuransi'])) {
            $payloadAsuransi = new AsuransiForm;
            $payloadAsuransi->attributes = $post['asuransi'];
            $payloadAsuransi->tgl_konfirmasi = !empty($payloadAsuransi->tgl_konfirmasi) ? date('Y-m-d', strtotime($payloadAsuransi->tgl_konfirmasi)) : null;
            $asuransi = $payloadAsuransi->attributes;
            if (!$payloadAsuransi->validate()) $errorParse['asuransi'] = $payloadAsuransi->errors;
        }

        //** Set BPJS */
        if (!empty($post['bpjs'])) {
            $payloadBpjs = new BpjsNewForm;
            $payloadBpjs->attributes = $post['bpjs'];
            $payload->no_rekam_medik = $payloadBpjs->no_rekam_medik;
            $pasienPayLoad->nopeserta_bpjs = $payloadBpjs->no_kartu;
            $isBpjs = true;
            /** Statis Rujukan WIP */
            $diagnosa = Diagnosa::find()->select([
                'diagnosa_id'
            ])->where([
                'diagnosa_kode' => $payloadBpjs->diagnosa_awal
            ])->one();
            $payload->asalrujukan_id = 2;
            $post['rujukan'] = [
                'rujukandari_id' => 1,
                'no_rujukan' => $payloadBpjs->no_rujukan,
                'nama_perujuk' => 'BPJS',
                'tanggal_rujukan' => date('Y-m-d', strtotime($payloadBpjs->tanggal_rujukan)),
                'kodediagnosa_rujukan' => $payloadBpjs->diagnosa_awal,
                'diagnosa_id' => !empty($diagnosa->diagnosa_id) ? $diagnosa->diagnosa_id : null,
            ];
            if (!$payloadBpjs->validate()) $errorParse['bpjs'] = $payloadBpjs->errors;
        }

        //** Set Rujukan */
        if (!empty($post['rujukan'])) {
            $payloadRujukan = new PayloadRujukan;
            $payloadRujukan->attributes = $post['rujukan'];
            $payloadRujukan->asalrujukan_id = $payload->asalrujukan_id;
            $payloadRujukan->tanggal_rujukan = date('Y-m-d', strtotime($payloadRujukan->tanggal_rujukan));
            $rujukan = $payloadRujukan->attributes;
            if (!$payloadRujukan->validate()) $errorParse['rujukan'] = $payloadRujukan->errors;
        }

        //** Set PJ Pasien */
        if (!empty($post['pj_pasien'])) {
            $payloadPjPasien = new PjPasien;
            $payloadPjPasien->attributes = $post['pj_pasien'];
            $payloadPjPasien->pj_tanggal_lahir = !empty($payloadPjPasien->pj_tanggal_lahir)
                                        ? date('Y-m-d',strtotime($payloadPjPasien->pj_tanggal_lahir)) : null;
            $pjPasien = $payloadPjPasien->attributes;
            if (!$payloadPjPasien->validate()) $errorParse['pj_pasien'] = $payloadPjPasien->errors;
        }

        //** Get Golongan Umur Id */
        $pasienPayLoad->tgl_rekam_medik = date('Y-m-d');
        $pasienPayLoad->is_aps = !empty($payload->is_aps) ? true : false;
        $pasienPayLoad->tanggal_lahir = !empty($pasienPayLoad->tanggal_lahir) ?  date('Y-m-d', strtotime($pasienPayLoad->tanggal_lahir)) : null;

        //** set pasien baru / lama */
        if (!empty($payload->no_rekam_medik) && empty($post['pasien'])) {
            $pasien = Pasien::find()->where([
                'no_rekam_medik' => $payload->no_rekam_medik
            ])->asArray()->one();
            if (empty($pasien)) {
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal!',
                    'text' => 'Pasien tidak ditemukan'
                ];
            }
            $pasienPayLoad->attributes = $pasien;
            $pasienBaru = false;
        }

        //** Append jenis identitas ke JSON */
        if(!empty($pasienPayLoad->additional_identitas) && $this->isJson($pasienPayLoad->additional_identitas)){
            $additional_identitas = json_decode($pasienPayLoad->additional_identitas);
        }
        $tmpIdentitas = [
            self::JENISIDENTITAS => (int) $pasienPayLoad->jenisidentitas,
            'no_identitas_pasien' => (int) $pasienPayLoad->no_identitas_pasien
        ];
        array_unshift($additional_identitas, $tmpIdentitas);
        $pasienPayLoad->additional_identitas = json_encode($additional_identitas);

         //** Validate Form */
         if (!$pasienPayLoad->validate()) $errorParse['pasien'] = $pasienPayLoad->errors;
         if (!$payload->validate()) $errorParse['tipe_pasien'] = $payload->errors;
         if (!$payloadKunjungan->validate()) $errorParse['kunjungan'] = $payloadKunjungan->errors;

         if (!empty($errorParse)) {
             return [
                 'status' => 422,
                 'data' => $errorParse
             ];
         }
         //** Get Tarif Tindakan */
         $tindakanPenunjang = json_decode($payloadKunjungan->list_penunjang, true);

         if (empty($tindakanPenunjang)) {
            $errorMessage = 'Tindakan / Paket tidak boleh kosong.';
            return $this->responseJson(422, $errorMessage, $post);
        }

        foreach($tindakanPenunjang as $k => $v) {
            if($k == self::TINDAKAN){
                if(!empty($v)){
                    for ($i=0; $i < count($v); $i++) {
                        $modelTindakan = MasterTarifTindakanView::find()->where([
                            'daftartindakan_kode' => $v[$i],
                            'komponentarif_id' => 6
                        ])->one();

                        if(empty($modelTindakan)){
                            $errorMessage = 'Tindakan tidak ditemukan.';
                            return $this->responseJson(422, $errorMessage, $post);
                        }

                        $tmpPenunjang[] = [
                            'daftartindakan_id' => $modelTindakan->daftartindakan_id,
                            'pasienmasukpenunjang_id' => null
                        ];
                    }
                }
            } else {
                if(!empty($v)){
                    for ($i=0; $i < count($v); $i++) {
                        $modelPaket = PaketDetailView::find()->where([
                            'tipepaket_kode' => $v[$i],
                        ])->one();

                        if(empty($modelPaket)){
                            $errorMessage = 'Paket tidak ditemukan.';
                            return $this->responseJson(422, $errorMessage, $post);
                        }

                        $tmpPenunjang[] = [
                            'tipepaket_id' => $modelPaket->tipepaket_id,
                            'pasienmasukpenunjang_id' => null
                        ];
                    }
                }
            }
        }

        $umur = ucwords(DocoHelpers::getUmur($pasienPayLoad->tanggal_lahir));

        $statusPeriksa = 1;
        $isKarcis = false;

        $statusPasien = !empty($payload->no_rekam_medik) ? DocoConstants::VAR_PAS_L : DocoConstants::VAR_PAS_B;
        $getTotalhari = DocoHelpers::convertToHari($pasienPayLoad->tanggal_lahir);
        $getGolongan = Cache::getGolonganUmur();
        $getGolUmurId = 1;

        foreach ($getGolongan as $value) {
            if ($value['golonganumur_minimal'] <= $getTotalhari
                    && $value['golonganumur_maksimal'] >= $getTotalhari) {
                $getGolUmurId = $value['golonganumur_id'];
                break;
            }
        }

        $pasienPayLoad->golonganumur_id = $getGolUmurId;

        //** Constant BSL */
        // $pasienPayLoad->propinsi_id =  $pasienPayLoad->kabupaten_id = $pasienPayLoad->kecamatan_id = $pasienPayLoad->kelurahan_id = DocoConstants::ALAMAT_LAINNYA;
        $pasienPayLoad->statusperkawinan = DocoConstants::UNKNOWN;

        if(isset($post['additional_indolab']) && !empty($post['additional_indolab'])) {
            $dataIndolab = json_encode($post['additional_indolab']);
            $isBsl = false;
            $isIndoLab = true;
        }

        $additionals = [
            'tarif' => $listTagihan,
            'rujukan' => $rujukan,
            'antrian' => $dataAntrian,
            'penanggung_jawab' => $pjPasien,
            'asuransi' => $asuransi,
            'no_rekam_medik' => null,
        ];

        if (!empty($tmpPenunjang)) {
            $additionals['tarif_penunjang'] = $tmpPenunjang;
        } else {
            $errorMessage = 'Tindakan / Paket tidak boleh kosong.';
            return $this->responseJson(422, $errorMessage, $post);
        }

        if ($pasienBaru || !empty($post['pasien'])) {
            $additionals['pasien'] = $pasienPayLoad->attributes;
        }

        if(!empty($payload->no_rekam_medik)){
            $tmpData['no_rekam_medik'] = $payload->no_rekam_medik;
            $additionals['no_rekam_medik'] = $tmpData;
        }

        if(isset($dataIndolab)) {
            $additionals['indolab'] = $dataIndolab;
        }

        $dataPendaftaran = [
            'tgl_pendaftaran' => date('Y-m-d H:i:s'),
            'penjamin_id' => $payload->penjamin_id,
            'pasien_id' => $pasienPayLoad->pasien_id,
            'pegawai_id' => $payloadKunjungan->dokter_id,
            'instalasi_id' => $payloadKunjungan->instalasi_id,
            'jeniskasuspenyakit_id' => $payloadKunjungan->jeniskasuspenyakit_id,
            'kelaspelayanan_id' => $payloadKunjungan->kelaspelayanan_id,
            'carabayar_id' => $payload->carabayar_id,
            'golonganumur_id' => $getGolUmurId,
            'umur' => $umur,
            'rujukan_id' => null,
            'antrian_id' => $payload->antrian_id,
            'kunjungan' => !empty($payload->no_rekam_medik) ? DocoConstants::VAR_K_L : DocoConstants::VAR_K_B,
            'ruangan_id' => $payloadKunjungan->ruangan_id,
            'transportasi' => $payloadKunjungan->transportasi,
            'keadaan_masuk' => $payloadKunjungan->keadaan_masuk,
            'status_periksa' => $statusPeriksa,
            'status_pasien' => $statusPasien,
            'status_masuk' => !empty($rujukan) ? DocoConstants::VAR_SM_R : DocoConstants::VAR_SM_NR,
            'keterangan_pendaftaran' => $payloadKunjungan->keterangan,
            'is_karcis' => $isKarcis,
            'additional_data' => json_encode($additionals),
            'is_aps' => true,
            'is_bsl' => $isBsl,
            'is_skd' => $payloadKunjungan->is_skd,
            'is_indolab' => $isIndoLab,
            'additional_indolab' => isset($dataIndolab) ? $dataIndolab : null
        ];

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $pendaftaran = new Pendaftaran;
            $pendaftaran->attributes = $dataPendaftaran;
            if ($pendaftaran->save()) {
                $idPendaftaran = $pendaftaran->pendaftaran_id;

                $transaction->commit();

                $infoDaftar = $this->getPasienPenunjang($idPendaftaran);
                $pendaftaran->no_pendaftaran = $infoDaftar->no_pendaftaran;
                $penunjangId = $infoDaftar->pasienmasukpenunjang_id;

                if(!empty($tindakanPenunjang)){

                    foreach($tmpPenunjang as $k => $v){
                        $tmpPenunjang[$k]['pasienmasukpenunjang_id'] = isset($penunjangId) ? $penunjangId : null;
                    }

                    $tagihanPenunjang =  (new KasirService)->tagihanPenunjang([
                        'pendaftaran_id' => $idPendaftaran,
                        'no_pendaftaran' =>$infoDaftar->no_pendaftaran,
                        'instalasi_id' =>$infoDaftar->instalasi_id,
                        'ruangan_id' =>$infoDaftar->ruangan_id,
                        'penjamin_id' => $payload->penjamin_id,
                        'kelas_pelayanan_id' => $payloadKunjungan->kelaspelayanan_id
                    ], $tmpPenunjang);

                    if (isset($tagihanPenunjang['meta']['result']) && $tagihanPenunjang['meta']['result'] == 'failed') {
                        $transaction->rollBack();
                        return isset($tagihanPenunjang['message']) ? $tagihanPenunjang['message'] : 'Terjadi Kesalahan API';
                    }
                }

                $response = $this->getInfoPendaftaran()
                ->select(['pendaftaran_id', 'ruangan_nama', 'pasien_id', 'kelaspelayanan_id', 'no_pendaftaran', 'tgl_pendaftaran', 'no_rekam_medik', 'nama_pasien', 'tanggal_lahir', 'umur', 'carabayar_nama', 'penjamin_nama', 'no_telepon_pasien', 'alamatemail', 'alamat_pasien', 'jenisidentitas', 'no_identitas_pasien', 'additional_pasien'])
                ->where(['pendaftaran_id' => $idPendaftaran])->asArray()->all();

                foreach($response as $k => $v) {
                    if(!empty($response[$k][self::JENISIDENTITAS])){
                        switch ($response[$k][self::JENISIDENTITAS]) {
                            case self::$jenisIdentitas[self::KTP]:
                                $response[$k][self::IDENTITAS] = self::KTP;
                                break;
                            case self::$jenisIdentitas[self::PASPOR]:
                                $response[$k][self::IDENTITAS] = self::PASPOR;
                                break;
                            default:
                                $response[$k][self::IDENTITAS] = self::LAINNYA;
                            break;
                        }
                    }
                    if(!empty($response[$k][self::ADDITIONAL_PASIEN])){
                        $response[$k][self::ADDITIONAL_PASIEN] = json_decode($response[$k][self::ADDITIONAL_PASIEN], true);
                        foreach($response[$k][self::ADDITIONAL_PASIEN] as $kk => $vv){
                            switch ($response[$k][self::ADDITIONAL_PASIEN][$kk][self::JENISIDENTITAS]) {
                                case self::$jenisIdentitas[self::KTP]:
                                    $response[$k][self::ADDITIONAL_PASIEN][$kk][self::IDENTITAS] = self::KTP;
                                    break;
                                case self::$jenisIdentitas[self::PASPOR]:
                                    $response[$k][self::ADDITIONAL_PASIEN][$kk][self::IDENTITAS] = self::PASPOR;
                                    break;
                                default:
                                    $response[$k][self::ADDITIONAL_PASIEN][$kk][self::IDENTITAS] = self::LAINNYA;
                                break;
                            }
                        }
                    }
                }

                return $this->responseJson(200, 'Proses Pendaftaran berhasil', $response);
            }

            $errors = DocoHelpers::parseError(['data' => $pendaftaran->errors], 'KunjunganForm');
            return [
                'data' => $errors,
                'status' => 422
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $exception = preg_match('/(?<=ERROR:  )(.*)/',$e->getMessage(),$out);
            $message = $e->getMessage();
            if (isset($out[1])) {
                $message = 'Query :' . $out[1];
            }
            return [
                'status' => 422,
                'title' => 'Proses Gagal!',
                'text' => $message
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'status' => 422,
                'title' => 'Proses Gagal!',
                'text' => $e->getMessage()
            ];
        }
    }

    /*
    * Get list dokter Penunjang BSL
    * @return data dokter
    */
    public function actionGetListDoctor($ruangan_id = null, $is_online = false)
    {
        $model = new ApiBslPayload;
        $model->scenario = 'get-list-doctor';
        $model->ruangan_id = $ruangan_id;

        if($model->validate()) {
            $result = $this->getDataPegawai()
            ->select(['pegawai_id', 'nama_pegawai','is_online', 'photopegawai_blob as pegawai_gambar', 'kode_dokter_bpjs', 'nama_dokter_bpjs']);

            if(!empty($model->ruangan_id)){
                $result = $result->where(['ruangan_id' => $model->ruangan_id]);
            }

            // penambahan param untuk adhyaksa
            if(!empty($is_online)){
                $result = $result->andWhere(['is_online' => $is_online]);
            }

            $result = $result->andWhere(['kelompokpegawai_id' => 1])->asArray()->all();
            if(empty($result)){
                $errorMessage = self::DATA_NULL;
                return $this->responseJson(200, $errorMessage, $model);
            }else{
                return $result;
            }
        }
        else {
            return [
                'status' => 422,
                'data' => $model->errors,
            ];
        }
    }

    private function getDataPegawai()
    {
        return PegawaiView::find();
    }

    private function getInfoPendaftaran()
    {
        return InfoDataPendaftaranView::find();
    }

    private function isJson($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

    /*
    * Get list carabayar Penunjang BSL
    * @return data carabayar
    */
    public function actionListPayment()
    {
        $paymentId = Yii::$app->request->get('payment_id', null);
        $paymentName = Yii::$app->request->get('payment_name', null);
        $data = $this->getCaraBayar()
        ->select(['carabayar_id', 'carabayar_nama']);

        if(!is_null($paymentId)){
            $data->andWhere(['carabayar_id' => $paymentId]);
        }

        if(!is_null($paymentName)){
            $data->orWhere(['like', 'LOWER(carabayar_nama)', $paymentName]);
        }

        $data =  $data->orderBy('carabayar_id')->asArray()->all();
        if(empty($data)){
            $errorMessage = self::DATA_NULL;
            return $this->responseJson(200, $errorMessage, '');
        } else {
            return $data;
        }
    }

    private function getCaraBayar()
    {
        return CaraBayar::find()->where(['is_active' => 't']);
    }

    /*
    * Get list penjamin Penunjang BSL
    * @return data penjamin
    */
    public function actionListGuarantor($paymentId = null)
    {
        $paymentId = Yii::$app->request->get('payment_id', null);

        $data = $this->getListPenjamin()
        ->select(['penjamin_id', 'carabayar_id', 'penjamin_nama']);

        if(!is_null($paymentId)){
            $data->andWhere(['carabayar_id' => $paymentId]);
        }

        $data =  $data->orderBy('penjamin_id')->asArray()->all();

        if(empty($data)){
            $errorMessage = self::DATA_NULL;
            return $this->responseJson(200, $errorMessage, $paymentId);
        } else {
            return $data;
        }
    }

    private function getListPenjamin()
    {
        return Penjamin::find()->where(['is_active' => 't', 'is_deleted' => 'f']);
    }

    public function actionGetDataSyncAdmission($model, $tipe = null, $id = null)
    {
        try {
            if ($model) {
                $strquery = "query";
                $strstatus = "status";
                $strtitle = "title";
                $strtext = "text";

                if ($model == 'patient') {
                    if ($id == null) {
                        $model = new PasienReplikasiView;
                        $query = $model::find(true);
                        $query = DocoRestActiveFilter::advancedFilter($model, $query);

                        return new ActiveDataProvider([
                            $strquery => $query,
                        ]);
                    } else {
                        $model = new PasienReplikasi;
                        $query = $model::find()->where(['id' => $id])->one();

                        return $query;
                    }
                } elseif ($model == 'saleOrder'){
                    if ($id == null) {
                        $model = new PendaftaranReplikasiView;
                        $query = $model::find(true);

                        $start = date('Y-m-d 00:00:00');
                        $end = date('Y-m-d 23:59:00');

                        if(isset($_GET['advanced-filter'])) {
                            if(isset($_GET['advanced-filter']['date_order'])) {
                                $explode = explode(" - ", $_GET['advanced-filter']['date_order']);
                                if(count($explode) == 2) {
                                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                                }
                                unset($_GET['advanced-filter']['date_order']);
                            }
                        }

                        $query->andWhere(['between', 'date_order', $start, $end]);

                        $query = DocoRestActiveFilter::advancedFilter($model, $query);
                        return new ActiveDataProvider([
                            $strquery => $query,
                        ]);

                    } else {
                        $model = new PendaftaranReplikasi;
                        $query = $model::find()->where(['id' => $id])->one();

                        return $query;
                    }
                } else {
                    return [
                        $strstatus => 200,
                        $strtitle => 'Proses Gagal',
                        $strtext => 'Model transaksi tidak ditemukan'
                    ];
                }
            }
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;

            return $response['response'] = [
                'message' => $e->getMessage(),
                'status'  => 500
            ];
        }
    }

    public function actionResyncAdmission($model)
    {
        $request = Yii::$app->request;
        $listId = $request->post('id', []);
        $listId = !empty($listId) && is_array($listId) ? $listId : [];
        $listError = [];

        if (!empty($listId)) {
            $attributes = [
                'is_sending' => false,
                'id_sync_sercon' => null,
                'sync_response' => null
            ];

            $baseCond = [
                'id' => $listId
            ];
        } else {
            return 'No data to sync';
        }

        if ($model == 'patient') {
            $table = self::PASIEN_R;
        } else {
            $table = self::PENDAFTARAN_R;
        }

        $execute = Yii::$app->db->createCommand()->update($table, $attributes, $baseCond)->execute();

        if ($execute) {
            return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM, [
                'title' => 'Proses Berhasil!',
                'text' => 'Sync ulang berhasil.',
            ]);
        } else {
            return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM_DATA, [
                'data' => [
                    'failed' => $execute
                ]
            ]);
        }
    }

    /**
     * @function : get all nopendaftaran
     * @return array [infopendaftaran]
     */
    public function actionGetInfoPatient()
    {
        $request = Yii::$app->request;
        $data = [];
        $noPendaftaran = $request->get('no_pendaftaran', null);
        if(empty($noPendaftaran)) {
            $errorMessage = 'No Pendaftaran tidak boleh kosong';
            return $this->responseJson(422, $errorMessage, '');
        }

        $data = $this->getInfoDaftar()
        ->where(['no_pendaftaran' => $noPendaftaran])
        ->asArray()
        ->one();

        return $data;
    }

    private function getInfoDaftar()
    {
        return InfopendaftaranV::find();
    }

    /**
     * @method : list propinsi data kemendagri
     */
    public function actionPropinsi()
    {
        return (new Wilayah)->getPropinsi();
    }

    /**
     * @method : list kabupaten data kemendagri
     */
    public function actionKabupaten()
    {
        return (new Wilayah)->getKabupaten();
    }

    /**
     * @method : list kecmatan data kemendagri
     */
    public function actionKecamatan()
    {
        return (new Wilayah)->getKecamatan();
    }

    /**
     * @method : list kelurahan data kemendagri
     */
    public function actionKelurahan()
    {
        return (new Wilayah)->getKelurahan();
    }

    /**
     * @method : list negara
     */
    public function actionNegara()
    {
        return (new Wilayah)->getNegara();
    }

    public function actionJknStatusAntrian() {
        $request = Yii::$app->request;
        $post = $request->post();

        // Validation date format to yyyy-mm-dd
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $post['tanggalperiksa'])) {
            return [
                'status' => 201,
                'title' => 'Proses Gagal!',
                'text' => 'Format Tanggal Tidak Sesuai, format yang benar adalah yyyy-mm-dd'
            ];
        }

        $kodeDokter = ArrayHelper::getValue($post, 'kodedokter', null);
        $kodePoli = ArrayHelper::getValue($post, 'kodepoli', null);
        $tglPeriksa = isset($post['tanggalperiksa']) ? date('Y-m-d', strtotime($post['tanggalperiksa'])) : null;
        $today = date('Y-m-d');
        $hariPeriksa = DocoConstants::$look_hari[date('N', strtotime($tglPeriksa))];

        if ($tglPeriksa < $today) {
            return [
                'status' => 201,
                'title' => 'Proses Gagal!',
                'text' => 'Tanggal Periksa Tidak Berlaku'
            ];
        }

        $jamPraktek = isset($post['jampraktek']) ? explode("-", $post['jampraktek']) : null;
        $jamPraktekMulai = empty($jamPraktek) ? null : date('H:i:s', strtotime($jamPraktek[0]));
        $jamPraktekTutup = empty($jamPraktek) ? null : date('H:i:s', strtotime($jamPraktek[1]));

        $poli = Ruangan::find()->select([
            'kode_ruangan_bpjs',
        ])->where([
            'kode_ruangan_bpjs' => $kodePoli,
        ])->asArray()->one();

        if (empty($poli)) {
            return [
                'status' => 201,
                'title' => 'Proses Gagal!',
                'text' => 'Poli Tidak Ditemukan'
            ];
        }

        $jadwalDokter = InfoJadwalDokterView::find()->select([
            'jadwaldokter_id',
            'ruangan_id',
            // 'nama_pegawai',
            // 'kuotadokter_id',
            // 'kuota_bpjs_online',
            // 'kuota_nonbpjs_online',
        ])->where([
            'kode_dokter_bpjs' => $kodeDokter,
            'kode_ruangan_bpjs' => $kodePoli,
            'hari_jadwalbuka' => $hariPeriksa,
            'waktu_mulai' => $jamPraktekMulai,
            'waktu_selesai' => $jamPraktekTutup,
        ])->asArray()->one();

        if (empty($jadwalDokter)) {
            return [
                'status' => 201,
                'title' => 'Proses Gagal!',
                'text' => 'Jadwal dokter tidak ditemukan!'
            ];
        }

        $kuotaDokter = (new PendaftaranOnline)->kuotaJkn($jadwalDokter['jadwaldokter_id'], $jadwalDokter['ruangan_id'], $tglPeriksa);


        // $kuotaDokter = InfoKuotaDokterView::find()->select([
        //     'kuotadokter_id',
        //     'kuota_bpjs_online',
        //     'kuota_nonbpjs_online',
        // ])->where([
        //     'jadwaldokter_id' => $jadwalDokter['jadwaldokter_id'],
        //     'is_online' => true,
        // ])->asArray()->one();

        if (empty($kuotaDokter)) {
            return [
                'status' => 201,
                'title' => 'Proses Gagal!',
                'text' => 'Kuota dokter tidak tersedia!'
            ];
        }

        $antrian = InfoPendaftaranOnlineView::find()->select([
            'antrian_id',
            'no_antrian',
            'status_antrian',
            'status_daftar_ol',
        ])->where([
            'jadwaldokter_id' => $jadwalDokter['jadwaldokter_id'],
            'carabayar_id' => DocoConstants::VAR_ID_CARABAYAR_BPJS,
            '"tgl_kunjungan"::date' => $tglPeriksa
        ])->andWhere([
            '!=', 'status_daftar_ol', DocoConstants::VAR_STATUS_DAFTAR_OL_DITOLAK,
        ])->orderBy([
            'antrian_id'=> SORT_DESC
        ])->asArray()->all();

        $sisaAntrian = 0;
        $antrianPanggil = '';
        // untuk jkn kolom status_antrian di gunakan untuk tracing pasien sudah dimana
        foreach ($antrian as $key => $value) {
            if ($value['status_antrian'] == DocoConstants::STATUS_PERIKSA) {
                $antrianPanggil = $value['no_antrian'];
            } else if ($value['status_antrian'] == DocoConstants::ANTRIAN_STATUS_BELUM_PANGGIL) {
            // } else if ($value['status_antrian'] == DocoConstants::STATUS_ANTRIAN_POLI) {
                $sisaAntrian++;
            }
        }
        $kuotaJkn = ArrayHelper::getValue($kuotaDokter, 'master_kuota_bpjs_online', 0);
        $sisaKuotaJkn = ArrayHelper::getValue($kuotaDokter, 'kuota_bpjs_online', 0);
        $totalAntrian = $kuotaJkn - $sisaKuotaJkn;

        $results = [
            'namapoli' => $kuotaDokter['ruangan_nama'],
            'namadokter' => $kuotaDokter['nama_pegawai'],
            'totalantrean' => $totalAntrian,
            'sisaantrean' => $sisaAntrian,
            'antreanpanggil' => $antrianPanggil,
            'sisakuotajkn' => $sisaKuotaJkn,
            'kuotajkn' => $kuotaJkn,
            'sisakuotanonjkn' => ArrayHelper::getValue($kuotaDokter, 'kuota_nonbpjs_online', 0),
            'kuotanonjkn' => ArrayHelper::getValue($kuotaDokter, 'master_kuota_nonbpjs_online', 0),
            'keterangan' => '',
        ];

        return $results;
    }

    public function actionBatalAntrianJkn(){
        $request = Yii::$app->request;
        $kodebooking = $request->post('kodebooking', null);
        $keterangan = $request->post('keterangan', null);
        $taskid = 99;
        $date = date('Y-m-d H:i:s');
        $waktu = DocoHelpers::generateTimeStamp($date);  //Convert to milliseconds

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $pendaftaranol = PendaftaranOnline::find()->where(['no_pendaftaranol' => $kodebooking])->one();
        if (!empty($pendaftaranol)){
            if ($pendaftaranol->status_daftar_ol == DocoConstants::VAR_STATUS_DAFTAR_OL_DISETUJUI) {
                return [
                    'status' => 201,
                    'title' => 'Proses Gagal!',
                    'text' => 'Pasien Sudah Dilayani, Antrean Tidak Dapat Dibatalkan'
                ];
            } else if ($pendaftaranol->status_daftar_ol == DocoConstants::VAR_STATUS_DAFTAR_OL_DITOLAK) {
                return [
                    'status' => 201,
                    'title' => 'Proses Gagal!',
                    'text' => 'Antrean Tidak Ditemukan atau Sudah Dibatalkan'
                ];
            }

            $tglReservasi = date('Y-m-d', strtotime($pendaftaranol->tgl_pendaftaranol));
            if ($tglReservasi < date('Y-m-d')) {
                return [
                    'status' => 201,
                    'title' => 'Proses Gagal!',
                    'text' => 'Antrean Tidak Ditemukan'
                ];
            }

            $pendaftaranol->status_daftar_ol = DocoConstants::VAR_STATUS_DAFTAR_OL_DITOLAK;
            if (!$pendaftaranol->save()) {
                $errors = DocoHelpers::parseError($pendaftaranol->errors,'PendaftaranOnline');
                return [
                    'data' => $errors,
                    'status' => 201
                ];
            }
        } else {
            return [
                'status' => 201,
                'title' => 'Proses Gagal!',
                'text' => 'Antrean Tidak Ditemukan'
            ];
        }

        $pattern = "/postman/i";

        if ($request->post('http_agent') !== null && preg_match($pattern, $request->post('http_agent')) == 1) { //dari postman
            $model = new BpjsJkn;
            $model->antrian_jkn = [
                'kodebooking' => $kodebooking,
                'taskid' => $taskid,
                'waktu' => $waktu,
                'keterangan' => $keterangan,
            ];

            $batalAntrian = $model->batalAntrianJkn();
            $responseCode = ArrayHelper::getValue($batalAntrian, 'metadata', null);
            if ($responseCode) {
                if ($batalAntrian['metadata']['code'] !== 200) {
                    $transaction->rollBack();
                    return [
                        'status' => 201,
                        'title' => 'Proses Gagal!',
                        'text' => $batalAntrian['metadata']['message'],
                    ];
                }
            }

            // $updateAntrian = $model->updateAntrianJkn();
            // $responseCode = ArrayHelper::getValue($updateAntrian, 'metadata', null);
            // if ($responseCode) {
            //     if ($updateAntrian['metadata']['code'] !== 200) {
            //         $transaction->rollBack();
            //         return [
            //             'status' => 201,
            //             'title' => 'Proses Gagal!',
            //             'text' => $updateAntrian['metadata']['message'],
            //         ];
            //     }
            // }

            $transaction->commit();
            return $batalAntrian;
        } else {
            $transaction->commit();
            return [
                'status' => 200,
                'title' => 'Ok',
                'text' => 'Ok'
            ];
        }
    }

    public function actionCheckInJkn(){
        $request = Yii::$app->request;
        $kodebooking = $request->post('kodebooking', null);
        $waktu = $request->post('waktu', null);
        $is_fingerprint_check = $request->post('is_fingerprint_checked', null);
        $taskid = '';
        $dateNow = date('Y-m-d H:i:s');

        $pendaftaranol = InfoPendaftaranOnlineView::find()->where(['no_pendaftaranol' => $kodebooking])
                ->andWhere(['status_daftar_ol'=>DocoConstants::VAR_STATUS_DAFTAR_OL_BELUM_DIPROSES])
                ->one();

        if (!empty($pendaftaranol)){
            if ($pendaftaranol['status_pasien'] == DocoConstants::VAR_PAS_L){
                $taskid = 3;
            } else {
                $taskid = 1;
            }
            if(empty($waktu)){
                $waktu = PendaftaranHelpers::getEstimasiDilayani($pendaftaranol['pendaftaranol_id']);
            }
        } else {
            return DocoHelpers::callBack(DocoMessages::KEY_DYNAMIC_STATUS, [
                'title' => 'Proses Gagal!',
                'text' => 'Kode Booking Tidak Ditemukan. Silakan mengambil antrian Pasien On Site BPJS.'
            ], 422);
        }

        $tanggal_reservasi = ArrayHelper::getValue($pendaftaranol,'tgl_kunjungan',date('Y-m-d H:i:s'));
        $date_tanggal_reservasi = (new \DateTime($tanggal_reservasi))->format('Y-m-d');
        $date_current = date('Y-m-d');
        if($date_current < $date_tanggal_reservasi){
            return DocoHelpers::callBack(DocoMessages::KEY_DYNAMIC_STATUS, [
                'title' => 'Proses Gagal!',
                'text' => "Silahkan Melakukan Checkin pada tanggal {$date_tanggal_reservasi}"
            ], 422);
        }

        $hasCheckedIn = Yii::$app->db->createCommand("
            SELECT * FROM antrianjkn_r WHERE is_checkin = true AND pendaftaranol_id IN ({$pendaftaranol['pendaftaranol_id']})
        ")->queryOne();
        $tgl_checkin = isset($hasCheckedIn['tgl_checkin']) ? ArrayHelper::getValue($hasCheckedIn,'tgl_checkin') : ArrayHelper::getValue($hasCheckedIn,'last_modified_date');
        $tglCheckin = date('d-m-Y', strtotime($tgl_checkin));
        $jamCheckin = date('H:i', strtotime($tgl_checkin));

        if(!empty($hasCheckedIn)){
            return DocoHelpers::callBack(DocoMessages::KEY_DYNAMIC_STATUS, [
                'title' => 'Proses Gagal!',
                'text' => 'Pasien telah melakukan checkin pada tanggal '. $tglCheckin .' jam '. $jamCheckin .'!'
            ], 422);
        }

        if (ArrayHelper::getValue($pendaftaranol, 'no_bpjs', null) == null) {
            return DocoHelpers::callBack(DocoMessages::KEY_DYNAMIC_STATUS, [
                'title' => 'Proses Gagal!',
                'text' => 'Pasien bukan Pendaftaran BPJS'
            ], 422);
        }
        /*
        Yii::$app->db->createCommand("
            UPDATE antrianjkn_r SET is_checkin = true, last_modified_date = '{$dateNow}' WHERE pendaftaranol_id IN ({$pendaftaranol['pendaftaranol_id']})
        ")->execute();
        Yii::$app->db->createCommand("
            UPDATE pendaftaranol_t SET is_checkin = true, last_modified_date = '{$dateNow}' WHERE pendaftaranol_id IN ({$pendaftaranol['pendaftaranol_id']})
        ")->execute();
        */

        $result = [
            'kodebooking' => $kodebooking,
            'waktu' => $waktu,
            'taskid' => $taskid,
            'pendaftaran' => [
                'pendaftaran_id' => ArrayHelper::getValue($pendaftaranol, 'pendaftaran_id'),
                'pasien_id' => ArrayHelper::getValue($pendaftaranol, 'pasien_id'),
                'pasienmasukpenunjang_id' => ArrayHelper::getValue($pendaftaranol, 'pendaftaran_id'),
                'antrian_id' => ArrayHelper::getValue($pendaftaranol, 'antrian_id'),
            ]
        ];

        $nokartu = ArrayHelper::getValue($pendaftaranol, 'no_bpjs');
        $tgl_periksa = date('Y-m-d', strtotime(ArrayHelper::getValue($pendaftaranol, 'tgl_kunjungan', null)));
        $infoPeserta = (new Bpjs)->peserta($nokartu, $tgl_periksa);

        $umur_format_text = DocoHelpers::getUmur(ArrayHelper::getValue($infoPeserta, 'response.peserta.tglLahir'));
        $arr_umur = DocoHelpers::getUmur($umur_format_text, false, true);
        $is_above_17 = $arr_umur['tahun'] >= 17 && $arr_umur['bulan'] >= 0 && $arr_umur['hari'] >= 1;
        $result['is_above_17'] = $is_above_17;

        // TODO: check menggunakan fingerprint bpjs
        if ($is_fingerprint_check && $is_above_17) {
            $cek_finger_print = (new Bpjs)->pencarianFingerprint(ArrayHelper::getValue($pendaftaranol, 'no_bpjs'), date('Y-m-d'));
            if (ArrayHelper::getValue($cek_finger_print, 'metaData.code', 500) == 200) {
                if (ArrayHelper::getValue($cek_finger_print, 'response.kode') == 1) {
                    $result['is_fingerprint_checked'] = true;
                    /**
                    $model = new BpjsJkn;
                    $model->antrian_jkn = [
                        'kodebooking' => $kodebooking,
                        'taskid' => $taskid,
                        'waktu' => $waktu
                    ];
                    $response = $model->updateAntrianJkn();
                    if (ArrayHelper::getValue($response, 'metadata.code', 500) != 200) {
                        return DocoHelpers::callBack(DocoMessages::KEY_DYNAMIC_STATUS, [
                            'title' => 'Proses BPJS Gagal',
                            'text' => ArrayHelper::getValue($response, 'metadata.message')
                        ], 500);
                    }
                    */
                    $this->updateCheckin($dateNow,$pendaftaranol['pendaftaranol_id']);
                    // Yii::$app->db->createCommand("
                    //     UPDATE antrianjkn_r SET is_checkin = true, tgl_checkin = '{$dateNow}', last_modified_date = '{$dateNow}' WHERE pendaftaranol_id IN ({$pendaftaranol['pendaftaranol_id']})
                    // ")->execute();

                    // $pendaftaranol['is_checkin'] = true;
                    // $pendaftaranol['tgl_checkin'] = $dateNow;
                    // $pendaftaranol->save();
                    $result['pendaftaranol'] = $pendaftaranol;

                    return DocoHelpers::response($result);
                } else {
                    $result['is_fingerprint_checked'] = false;
                    return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM_DATA, [
                        'title' => 'Kode Booking ditemukan',
                        'text' => ArrayHelper::getValue($cek_finger_print, 'response.status').", Silahkan validasi finger print terlebih dahulu melalui aplikasi yang akan tampil",
                        'data' => $result,
                    ], 200);
                }
            } else {
                return DocoHelpers::callBack(DocoMessages::KEY_DYNAMIC_STATUS, [
                    'title' => 'Terjadi kesalahan pada BPJS',
                    'text' => ArrayHelper::getValue($cek_finger_print, 'metaData.message'),
                    'data' => []
                ], 500);
            }

            return DocoHelpers::response($result);

        } else {
            $this->updateCheckin($dateNow,$pendaftaranol['pendaftaranol_id']);
            $result['pendaftaranol'] = $pendaftaranol;
            $result['kodebooking'] = $kodebooking;
            $result['waktu'] = $waktu;
            $result['taskid'] = $taskid;

            return DocoHelpers::response($result);
        }
    }

    private function updateCheckin($date,$pendaftaranol_id)
    {
        if(isset($date) && !empty($date) && isset($pendaftaranol_id) && !empty($pendaftaranol_id)){
            Yii::$app->db->createCommand("
                UPDATE antrianjkn_r SET is_checkin = true, tgl_checkin = :date, last_modified_date = :date 
                WHERE is_checkin = false and pendaftaranol_id IN (:pendaftaranol_id)
            ")
            ->bindValue(':date',$date)
            ->bindValue(':pendaftaranol_id',$pendaftaranol_id)
            ->execute();
            Yii::$app->db->createCommand("
                UPDATE pendaftaranol_t SET is_checkin = true, tgl_checkin = :date, last_modified_date = :date 
                WHERE is_checkin = false and pendaftaranol_id IN (:pendaftaranol_id)
            ")
            ->bindValue(':date',$date)
            ->bindValue(':pendaftaranol_id',$pendaftaranol_id)
            ->execute();
        }
    }

    public function actionSisaAntrianJkn(){
        $request = Yii::$app->request;
        $kodebooking = $request->post('kodebooking', null);

        $query = "
            SELECT *,
                slt.jam_mulai as jam_mulai_slot
            FROM pendaftaranol_t po
            JOIN antrian_t at on at.antrian_id = po.antrian_id
            JOIN jadwaldokter_m jm on po.jadwaldokter_id = jm.jadwaldokter_id
            JOIN slotjadwaldokter_m slt on po.jadwaldokter_id = slt.jadwaldokter_id AND at.slot_sequence = slt.slot_sequence
            WHERE po.no_pendaftaranol = '$kodebooking'
        ";

        $pendaftaranol = Yii::$app->db->createCommand($query)->queryOne();

        if (empty($pendaftaranol)){
            return [
                'status' => 201,
                'title' => 'Proses Gagal!',
                'text' => 'Antrean Tidak Ditemukan'
            ];
        }

        $pegawai_id = isset($pendaftaranol['pegawai_id']) ? $pendaftaranol['pegawai_id'] : null;
        $tgl_pendaftaranol = isset($pendaftaranol['tgl_pendaftaranol']) ? date('Y-m-d', strtotime($pendaftaranol['tgl_pendaftaranol'])) : null;
        $waktu_slot = ArrayHelper::getValue($pendaftaranol, 'jam_mulai_slot', null);
        $antrian_id = isset($pendaftaranol['antrian_id']) ? $pendaftaranol['antrian_id'] : null;
        $jadwaldokter_id = isset($pendaftaranol['jadwaldokter_id']) ? $pendaftaranol['jadwaldokter_id'] : null;
        $spm = ArrayHelper::getValue($pendaftaranol, 'jumlah_loaddokter', null);
        $pendaftaranOl = ArrayHelper::getValue($pendaftaranol, 'pendaftaranol_id', null);
        $jadwaldokter_mulai = ArrayHelper::getValue($pendaftaranol, 'jadwaldokter_mulai', null);

        if (empty($spm)){
            return [
                'status' => 201,
                'title' => 'Proses Gagal!',
                'text' => 'SPM dokter belum di set!'
            ];
        }

        $query1 = "
            SELECT
                po.pendaftaranol_id,
                at.status_antrian
            FROM antrianjkn_r ar
            JOIN pendaftaranol_t po on ar.pendaftaranol_id = po.pendaftaranol_id
            JOIN antrian_t at on at.antrian_id = po.antrian_id
            WHERE po.pegawai_id = '$pegawai_id' AND po.tgl_pendaftaranol::DATE = '$tgl_pendaftaranol' AND po.jadwaldokter_id = '$jadwaldokter_id' and po.status_daftar_ol != 566
            ORDER BY po.pendaftaranol_id ASC
            ";

        // $query1 .= 'AND at.status_antrian = 0';
        // $query1 .= 'AND at.status_antrian = 1';

        $antrianjkn = Yii::$app->db->createCommand($query1)->queryAll();
        $sisaantrian = 0;
        $waktutunggu = date('Y-m-d H:i:s.u',strtotime($tgl_pendaftaranol . ' ' . $jadwaldokter_mulai));
        $spm *= 60;
        foreach ($antrianjkn as $value) {
            $transId = ArrayHelper::getValue($value, 'pendaftaranol_id', null);
            if ($pendaftaranOl == $transId) {
                break;
            }
            $waktutunggu = date('Y-m-d H:i:s.u', strtotime($waktutunggu. "+$spm sec"));
            if ($value['status_antrian'] == 0) {
                $sisaantrian++;
            }
        }
        $waktuTungguDetik = $spm * (!empty($sisaantrian) ? ($sisaantrian - 1) : 1);

        $query2 = "
            SELECT at.no_antrian
            FROM antrianjkn_r ar
            JOIN pendaftaranol_t po on ar.pendaftaranol_id = po.pendaftaranol_id
            JOIN antrian_t at on at.antrian_id = po.antrian_id
            WHERE po.pegawai_id = '$pegawai_id' AND po.tgl_pendaftaranol::DATE = '$tgl_pendaftaranol' AND po.jadwaldokter_id = '$jadwaldokter_id' and is_selesai_periksa = false and at.status_antrian = 2
            order by temp_urutan DESC";

        $antrianpanggiljkn = Yii::$app->db->createCommand($query2)->queryOne();

        $jadwalDokter = InfoJadwalDokterView::find()->select([
            'ruangan_nama',
            'nama_pegawai',
            'kuotadokter_id',
            'kuota_bpjs_online',
            'kuota_nonbpjs_online',
        ])->where([
            'jadwaldokter_id' => $pendaftaranol['jadwaldokter_id']
        ])->asArray()->one();

        // $sisaantrian = isset($antrianjkn['sisaantrean']) ? $antrianjkn['sisaantrean'] : 0;

        // $waktutunggu = isset($pendaftaranol['jumlah_loaddokter']) ? $pendaftaranol['jumlah_loaddokter'] : 0;
        // $waktutunggu = ($waktutunggu * ($sisaantrian - 1)) * 60;
        $tgltunggu = date('Y-m-d', strtotime($tgl_pendaftaranol));
        $jamtunggu = date('H:i:s', strtotime($waktu_slot));
        // $waktutunggu = $tgltunggu . " " . $jamtunggu;

        // if($sisaantrian < 1) {
        //     $waktutunggu = $spm * 60;
        // } else {
        //     $waktutunggu = ($spm * $sisaantrian) * 60;
        // }

        $results = [
            'nomorantrean' => isset($pendaftaranol['no_antrian']) ? $pendaftaranol['no_antrian'] : null,
            'namapoli' => $jadwalDokter['ruangan_nama'],
            'namadokter' => $jadwalDokter['nama_pegawai'],
            'sisaantrean' => $sisaantrian,
            'antreanpanggil' => isset($antrianpanggiljkn['no_antrian']) ? $antrianpanggiljkn['no_antrian'] : "-",
            'waktutunggu' => $waktuTungguDetik,
            'keterangan' => isset($pendaftaranol['keterangan']) ? $pendaftaranol['keterangan'] : "",
        ];

        return $results;
    }

    public function actionUpdateAntrianJkn()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        $status_antrian_jkn = $request->get('status_antrian_jkn', null);
        $pendaftaranol_id = $request->post('pendaftaranol_id', null);
        $status_antrian_jkn = !empty($request->post('status_antrian_jkn')) ? $request->post('status_antrian_jkn') : $status_antrian_jkn;
        $date = date('Y-m-d H:i:s');
        $waktu = DocoHelpers::generateTimeStamp($date);  //Convert to milliseconds
        $kodebooking = '';
        $status_antrian = '';

        if (!empty($pendaftaranol_id)){
            $pendaftaranol = PendaftaranOnline::find()->where(['pendaftaranol_id' => $pendaftaranol_id])
                    ->andWhere(['status_pasien'=> DocoConstants::VAR_PAS_B])
                    ->andWhere(['jenis_reservasi'=> DocoConstants::JENIS_RESERVASI_JKN])
                    ->asArray()
                    ->one();
        } else {
            $pendaftaranol = PendaftaranOnline::find()->where(['pendaftaran_id' => $pendaftaran_id])
                    ->asArray()
                    ->one();
        }

        if(!empty($pendaftaranol)){
            $kodebooking = $pendaftaranol['no_pendaftaranol'];
        } else {
            return [
                'status' => 201,
                'title' => 'Proses Gagal!',
                'text' => 'Kode booking tidak ditemukan!'
            ];
        }

        $model = new BpjsJkn;
        $model->antrian_jkn = [
            'kodebooking' => $kodebooking,
            'taskid' => $status_antrian_jkn,
            'waktu' => $waktu
        ];

        $response = $model->updateAntrianJkn();

        if ($status_antrian_jkn == DocoConstants::STATUS_TUNGGU_POLI){
            $status_antrian = DocoConstants::STATUS_PERIKSA;
        } else if ($status_antrian_jkn == DocoConstants::STATUS_PULANG_POLI) {
            $status_antrian = DocoConstants::STATUS_PULANG;
        }

        if (!empty($pendaftaran_id)){
            Yii::$app->db->createCommand("
                UPDATE antrian_t SET status_antrian = ({$status_antrian}) WHERE pendaftaran_id IN ({$pendaftaran_id})
            ")->execute();
        }

        return $response;

        // $pendaftaranol_id = $request->post('pendaftaranol_id', null);
        // $waktu = $request->post('waktu', null);
        // $taskid = $request->post('taskid', null);

        // $response = $this->updateAntrianJkn($pendaftaranol_id, $waktu, $taskid);
        // if (is_array($response)) {
        //     return $this->responseJson(400, $response);
        // }

        // return [
        //     'status' => 200,
        //     'message' => 'Berhasil update waktu antrian.',
        // ];
    }

    public function actionReferensiPoliJkn()
    {
        $model = new BpjsJkn;
        $response = $model->referensiPoliJkn();

        return $response;
    }

    public function actionReferensiDokterJkn()
    {
        $model = new BpjsJkn;
        $response = $model->referensiDokterJkn();

        return $response;
    }

    public function actionSimpanAntrianJkn()
    {
        $payload = Yii::$app->request->post();
        return (new BpjsJkn)->simpanAntrianJkn($payload);
    }

    public function actionUpdateJadwalDokter()
    {
        $payload = Yii::$app->request->post();
        return (new BpjsJkn)->updateJadwalDokter($payload);
    }

    public function actionBatalAntrol()
    {
        $payload = Yii::$app->request->post();
        $model = new BpjsJkn;
        $model->antrian_jkn = [
            'kodebooking' => ArrayHelper::getValue($payload,'kodebooking'),
            'keterangan' => ArrayHelper::getValue($payload,'keterangan'),
        ];
        return $model->batalAntrianJkn();
    }

    public function actionLoginJkn(){
        $payload = Yii::$app->request->get();
        
        // simpan ke logjkn_r
        $userIdentity = Pegawai::find()
            ->select(['pegawai_id', 'nama_pegawai'])
            ->where(['pegawai_id' => Yii::$app->user->identity->pegawai_id])
            ->asArray()->one();
        
        $model = new LoginJknR;
        $model->pendaftaranol_id = ArrayHelper::getValue($payload,'pendaftaran_id');
        $model->state = isset($payload['taskId']) ? $payload['taskId'] : null;
        $model->created_date = date('Y-m-d H:i:s');
        $model->created_by = isset($userIdentity['pegawai_id']) ? $userIdentity['pegawai_id'] : null;
        $model->payload = isset($payload['data']) ? json_encode($payload['data']) : null;
        $model->sync_respon = isset($payload['response']) ? json_encode($payload['response']) : null;
    
        $model->save();
    }

    public function actionShowConfigBpjsJkn()
    {
        return (new BpjsJkn)->showConfig();
    }

    public function actionReferensiJadwalDokterJkn()
    {
        $request = Yii::$app->request;
        $kdpoli = $request->post('kdpoli', null);
        $tgl = $request->post('tgl', null);
        $model = new BpjsJkn;
        $response = $model->referensiJadwalDokterJkn($kdpoli, $tgl);

        return $response;
    }

    public function actionGetListJkn()
    {
        $payload = Yii::$app->request->post();
        return (new BpjsJkn)->getListTaskJkn($payload);
    }

    /**
     * @method Purposed only for validate response jkn
     *
     * @return array
     */
    public function actionUpdateWaktuAntrian()
    {
        $request = Yii::$app->request;
        $kodebooking = $request->post('kodebooking', null);
        $taskId = $request->post('taskid', null);
        $date = date('Y-m-d H:i:s');
        $waktu = DocoHelpers::generateTimeStamp($date);

        $payload = new DynamicModel(compact('kodebooking', 'taskId'));
        $payload->addRule(['kodebooking', 'taskId'], 'required');

        if (!$payload->validate()) {
            return [
                'status' => 201,
                'title' => 'Proses Gagal!',
                'text' => $payload->errors
            ];
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, ['data' => $payload->errors]);
        }
        $model = new BpjsJkn;
        $model->antrian_jkn = [
            'kodebooking' => $kodebooking,
            'taskid' => $taskId,
            'waktu' => $waktu
        ];

        return $model->updateAntrianJkn();
    }

    public function actionUpdateAdmisiJkn()
    {
        $request = Yii::$app->request;
        $pendaftaranol_id = $request->post('pendaftaranol_id', null);

        return [
            'pendaftaranol_id' => $pendaftaranol_id
        ];
    }

    public function actionAutoPulangPasienJkn()
    {
        return [
            'message' => 'Pemulangan Pasien Mobile JKN Berhasil',
        ];
    }

    public function actionDashboardPerTanggal()
    {
        $request = Yii::$app->request;
        $tanggal = $request->post('tanggal', null);
        $waktu = $request->post('waktu', null);
        $model = new BpjsJkn;
        $response = $model->dashboardPerTanggal($tanggal, $waktu);

        return $response;
    }

    public function actionDashboardPerBulan()
    {
        $request = Yii::$app->request;
        $bulan = $request->post('bulan', null);
        $tahun = $request->post('tahun', null);
        $waktu = $request->post('waktu', null);
        $model = new BpjsJkn;
        $response = $model->dashboardPerBulan($bulan,$tahun, $waktu);

        return $response;
    }

    public function actionAntrianPerTanggal()
    {
        $request = Yii::$app->request;
        $tanggal = $request->post('tanggal', null);
        $model = new BpjsJkn;
        $response = $model->antrianPerTanggal($tanggal);

        return $response;
    }

    public function actionAntrianPerKodeBooking()
    {
        $request = Yii::$app->request;
        $kodebooking = $request->post('kodebooking', null);
        $model = new BpjsJkn;
        $response = $model->antrianPerKodeBooking($kodebooking);

        return $response;
    }

    public function actionCheckInPasienRouting(){
        $request = Yii::$app->request;
        $konsulpoli_id = $request->post('konsulpoli_id', null);
        $waktu = $request->post('waktu', null);
        $is_fingerprint_check = $request->post('is_fingerprint_checked', null);

        $konsulpoli = KonsulPoli::find()->select(['konsulpoli_id', 'pt.pendaftaran_id', 'pt.pasien_id', 'tgl_konsulpoli', 'konsulpoli_t.antrian_id', 'bt.nokartuasuransi as no_bpjs'])
                    ->leftJoin('pendaftaran_t pt', 'pt.pendaftaran_id = konsulpoli_t.pendaftaran_id')
                    ->leftJoin('bpjs_t bt', 'pt.pendaftaran_id = bt.pendaftaran_id')
                    ->where(['konsulpoli_id' => $konsulpoli_id])
                    ->asArray()
                    ->one();

        if (empty($konsulpoli)) {
            return DocoHelpers::callBack(DocoMessages::KEY_DYNAMIC_STATUS, [
                'title' => 'Proses Gagal!',
                'text' => 'Konsul Poli tidak ditemukan!'
            ], 422);
        }

        $result = [
            'pendaftaran' => [
                'konsulpoli_id' => ArrayHelper::getValue($konsulpoli, 'konsulpoli_id'),
                'pendaftaran_id' => ArrayHelper::getValue($konsulpoli, 'pendaftaran_id'),
                'pasien_id' => ArrayHelper::getValue($konsulpoli, 'pasien_id'),
                'antrian_id' => ArrayHelper::getValue($konsulpoli, 'antrian_id'),
            ]
        ];

        if (ArrayHelper::getValue($konsulpoli, 'no_bpjs', null) == null) {
            return DocoHelpers::callBack(DocoMessages::KEY_DYNAMIC_STATUS, [
                'title' => 'Proses Gagal!',
                'text' => 'Pasien bukan Pendaftaran BPJS'
            ], 422);
        }

        // TODO: check menggunakan fingerprint bpjs
        if ($is_fingerprint_check) {
            $cek_finger_print = (new Bpjs)->pencarianFingerprint(ArrayHelper::getValue($konsulpoli, 'no_bpjs'), ArrayHelper::getValue($konsulpoli, 'tgl_pendaftaran'));
            if (ArrayHelper::getValue($cek_finger_print, 'metaData.code', 500) == 200) {
                if (ArrayHelper::getValue($cek_finger_print, 'response.kode') == 1) {
                    $result['is_fingerprint_checked'] = true;
                    if (ArrayHelper::getValue($cek_finger_print, 'metaData.code', 500) != 200) {
                        return DocoHelpers::callBack(DocoMessages::KEY_DYNAMIC_STATUS, [
                            'title' => 'Proses BPJS Gagal',
                            'text' => ArrayHelper::getValue($cek_finger_print, 'metaData.message')
                        ], 500);
                    }
                    return DocoHelpers::response($result);
                } else {
                    $result['is_fingerprint_checked'] = false;
                    return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM_DATA, [
                        'title' => 'Konsul Poli ditemukan',
                        'text' => ArrayHelper::getValue($cek_finger_print, 'response.status').", Silahkan validasi finger print terlebih dahulu melalui aplikasi yang akan tampil",
                        'data' => $result,
                    ], 200);
                }
            } else {
                return DocoHelpers::callBack(DocoMessages::KEY_DYNAMIC_STATUS, [
                    'text' => ArrayHelper::getValue($cek_finger_print, 'metaData.message')
                ], 500);
            }

            return DocoHelpers::response($result);

        }
    }

    public function actionCreateAntrianJkn()
    {
        $post = Yii::$app->request->post();

        $create_antrian_jkn = (new BpjsJkn)->simpanAntrianJkn(ArrayHelper::getValue($post, 'payload', []));

        if (!empty(ArrayHelper::getValue($post, 'is_logged', false))) {
            $this->setJknLog($create_antrian_jkn, ArrayHelper::getValue($post, 'payload', []), ArrayHelper::getValue($post, 'ids', []));
        }

        return $create_antrian_jkn;
    }

    private function setJknLog($response, $payload, $ids = [])
    {
        $model = new LoginJknR;
        $model->pendaftaranol_id = ArrayHelper::getValue($ids, 'pendaftaranol_id');
        $model->pendaftaran_id = ArrayHelper::getValue($ids, 'pendaftaran_id');
        $model->created_date = date('Y-m-d H:i:s');
        $model->created_by = null;
        $model->payload = isset($payload) ? json_encode($payload) : null;
        $model->sync_respon = isset($response) ? json_encode($response) : null;

        $model->save();
    }
}
