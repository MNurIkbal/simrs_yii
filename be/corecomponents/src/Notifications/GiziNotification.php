<?php

namespace Doco\Notifications;

use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\models\Notifikasi;
use Doco\models\PasienRanapView;
use Doco\models\WorklistPasien;
use Doco\models\ReminderPuasa;
use Doco\models\User;
use Yii;

class GiziNotification extends BaseNotification
{
    /**
     * Publish new notification of fasting reminder
     * 
     * @param Array $option
     * @return Boolean
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static function fastingReminder($option)
    {
        $date = isset($option['tgl_instruksi']) ? $option['tgl_instruksi'] : null;
        $registrationId = isset($option['pendaftaran_id']) ? $option['pendaftaran_id'] : null;
        $instructionId = isset($option['instruksi_id']) ? $option['instruksi_id'] : null;
        if (!empty($date) && !empty($registrationId) && !empty($instructionId)) {
            $registrationData = PasienRanapView::find()
                ->select([
                    'pendaftaran_id',
                    'no_pendaftaran',
                    'no_rekam_medik',
                    'nama_pasien',
                    'jenis_kelamin',
                    'umur',
                    'ruangan_nama'
                ])
                ->andWhere([
                    'pendaftaran_id' => $registrationId
                ])
                ->asArray()
                ->one();
            if (!empty($registrationData)) {
                $fastingDate = date("Y-m-d H:i:s", strtotime($date . ' -1 day'));
                $typeTransaction = isset($option['typeTransaction']) ? $option['typeTransaction'] : '';
                $message = 'Reminder puasa untuk pasien ' . $registrationData['nama_pasien'] . ' (' . $registrationData['no_rekam_medik'] . ') - ' . $registrationData['no_pendaftaran'] . ' - ' . $registrationData['jenis_kelamin'] . ' [' . $registrationData['umur'] . '] pada tanggal ' . (new DocoHelpers)->convertDate($fastingDate) . ' disebabkan adanya pemeriksaan ' . $typeTransaction . ' yang akan dilaksanakan pada ' . (new DocoHelpers)->convertDate($date) . '.';
                $dataPayload = array_merge($registrationData, [
                    'tgl_instruksi' => $date,
                    'tgl_puasa' => $fastingDate,
                    'pendaftaran_id' => $registrationId,
                    'typeTransaction' => $typeTransaction,
                    'instruksi_id' => $instructionId,
                    'message' => $message
                ]);

                $userId = Yii::$app->jwt->user->loginpemakai_id;
                ReminderPuasa::batchInsert([[
                    'pendaftaran_id' => $registrationId,
                    'instruksi_id' => $instructionId,
                    'tgl_awal_puasa' => $fastingDate,
                    'tgl_akhir_puasa' => $date,
                    'tgl_tindakan' => $date,
                    'created_date' => date("Y-m-d H:i:s"),
                    'created_by' => $userId
                ]]);
                // get all of user
                $users = User::usersIdByModule(DocoConstants::GIZI_MODULE_ID);
                if (!empty($users)) {
                    $arrayOfUsers = [];
                    $payloadNotification = [];
                    $defaultNotificationPayload = [
                        'instalasi_id' => DocoConstants::INST_ID_RI,
                        'type' => 'fasting-reminder',
                        'modul_id' => DocoConstants::GIZI_MODULE_ID,
                        'tglnotifikasi' => $fastingDate,
                        'judulnotifikasi' => 'Reminder Puasa untuk pasien ' . $registrationData['nama_pasien'],
                        'isi_notifikasi' => $message,
                        'is_read' => false,
                        'user_id' => null,
                        'additional_data' => json_encode($dataPayload),
                        'created_date' => date("Y-m-d H:i:s"),
                        'created_by' => $userId
                    ];
                    foreach ($users as $user) {
                        $payloadNotification[] = array_merge($defaultNotificationPayload, [
                            'user_id' => $user['loginpemakai_id']
                        ]);
                        $arrayOfUsers[] = $user['loginpemakai_id'];
                    }
                    Notifikasi::batchInsert($payloadNotification);
                    // $notificationPayload = Notifikasi::generalUserNotification();
                    // $notificationBucket = $notificationPayload['records'];
                    // $totalUnread = $notificationPayload['totalUnread'];
                    $payloadRedis = [
                        'newNotification' => array_merge($dataPayload, $defaultNotificationPayload),
                        // 'notificationBucket' => $notificationBucket,
                        // 'totalUnread' => $totalUnread,
                        'users' => $arrayOfUsers
                    ];
                    if (date("Y-m-d", strtotime($fastingDate)) == date("Y-m-d")) {
                        self::publish('new-notification', $payloadRedis);
                    } else {
                        self::publish('update-notification', $payloadRedis);
                    }
                }
            }
            return true;
        } else {
            return false;
        }
    }

    public static function dietNotification($pasien, $permintaan, $permintaanDetail) {
        $userId = Yii::$app->jwt->user->loginpemakai_id;
        $dataPayload = [
            'peg_pemesan_id' => $permintaan->peg_pemesan_id,
            'tgl_permintaanmakan' => $permintaan->tgl_permintaanmakan,
            'no_permintaanmakan' => $permintaan->no_permintaanmakan,
            'details' => []
        ];
        $listMakan = [];
        foreach ($permintaanDetail as $detail) {
            $listMakan[] = $detail['makanandiet_nama'];
            $dataPayload['details'][] = [
                'makanandiet_id' => $detail['makanandiet_id'],
                'makanandiet_nama' => $detail['makanandiet_nama'],
                'jenisdiet_id' => $detail['jenisdiet_id'],
                'jenisdiet_nama' => $detail['jenisdiet_nama'],
                'waktu' => $detail['waktu'],
                'waktu_diet' => $detail['waktu_diet'],
                'jumlah' => $detail['jumlah'],
                'keterangan' => $detail['keterangan'],
            ];
        }
        $tgl = date('d/m/Y', strtotime($permintaan->tgl_permintaanmakan));
        $message =  $tgl. ' - ' . $pasien->no_rekam_medik . ' - ' . $pasien->nama_pasien . ' - ' . $pasien->ruangan_nama . ' / ' . $pasien->kamarruangan_nokamar . ' / ' . $pasien->no_tempattidur . ' - ' . implode(', ', $listMakan);

        $users = User::usersIdByModule(DocoConstants::GIZI_MODULE_ID);
        if (!empty($users)) {
            $defaultNotificationPayload = [
                'instalasi_id' => DocoConstants::INST_ID_RI,
                'type' => 'permintaan-makan',
                'modul_id' => DocoConstants::GIZI_MODULE_ID,
                'tglnotifikasi' => $permintaan->tgl_permintaanmakan,
                'judulnotifikasi' => 'Permintaan Makan ' . $pasien['nama_pasien'],
                'isi_notifikasi' => $message,
                'is_read' => false,
                'message' => $message,
                'additional_data' => json_encode($dataPayload),
                'created_date' => date("Y-m-d H:i:s"),
                'created_by' => $userId
            ];
            $arrayOfUsers = [];
            $payloadNotifications = [];
            foreach ($users as $user) {
                $notificationPayload = $defaultNotificationPayload;
                $arrayOfUsers[] = $notificationPayload['user_id'] = $user['loginpemakai_id'];
                $payloadNotifications[] = $notificationPayload;
            }
            Notifikasi::batchInsert($payloadNotifications);
            $payloadRedis = [
                'newNotification' => $defaultNotificationPayload,
                'users' => $arrayOfUsers
            ];
            self::publish('permintaan-makan-notification', $payloadRedis);
        }
    }

    public static function catatanDietNotification($modelCatatan) {
        $userId = Yii::$app->jwt->user->loginpemakai_id;
        $dataPayload = [
            'pendaftaran_id' => $modelCatatan->pendaftaran_id,
            'pasienadmisi_id' => $modelCatatan->pasienadmisi_id,
            'catatan_diet' => $modelCatatan->catatan_diet,
            'peg_pemesan_id' => $modelCatatan->peg_pemesan_id,
            'tgl_permintaanmakan' => $modelCatatan->tgl_permintaanmakan,
        ];
        $tgl = date('d/m/Y', strtotime($modelCatatan->tgl_permintaanmakan));

        if(empty($modelCatatan->pasienadmisi_id)) {
            $registrationData = WorklistPasien::find()
                ->select([
                    'no_rekam_medik',
                    'nama_pasien',
                    'ruangan_nama',
                ])
                ->andWhere([
                    'pendaftaran_id' => $modelCatatan->pendaftaran_id,
                ])
                ->asArray()
                ->one();
            if(empty($registrationData)) {
                throw new \Exception("Data pasien ".json_encode([
                    'pendaftaran_id' => $modelCatatan->pendaftaran_id,
                    'pasienadmisi_id' => $modelCatatan->pasienadmisi_id,
                ]));
            }
            $message =  $tgl. ' - ' . $registrationData['no_rekam_medik'] . ' - ' . $registrationData['nama_pasien'] . ' - ' . $registrationData['ruangan_nama'] . ' - ' . $modelCatatan->catatan_diet;
        } else {
            $registrationData = PasienRanapView::find()
                ->select([
                    'no_rekam_medik',
                    'nama_pasien',
                    'ruangan_nama',
                    'kamarruangan_nokamar',
                    'no_tempattidur',
                ])
                ->andWhere([
                    'pendaftaran_id' => $modelCatatan->pendaftaran_id,
                    'pasienadmisi_id' => $modelCatatan->pasienadmisi_id,
                ])
                ->asArray()
                ->one();
            if(empty($registrationData)) {
                throw new \Exception("Data pasien ".json_encode([
                    'pendaftaran_id' => $modelCatatan->pendaftaran_id,
                    'pasienadmisi_id' => $modelCatatan->pasienadmisi_id,
                ]));
            }
            $message =  $tgl. ' - ' . $registrationData['no_rekam_medik'] . ' - ' . $registrationData['nama_pasien'] . ' - ' . $registrationData['ruangan_nama'] . ' / ' . $registrationData['kamarruangan_nokamar'] . ' / ' . $registrationData['no_tempattidur'] . ' - ' . $modelCatatan->catatan_diet;
        }


        $users = User::usersIdByModule(DocoConstants::GIZI_MODULE_ID);
        if (!empty($users)) {
            $defaultNotificationPayload = [
                'instalasi_id' => DocoConstants::INST_ID_RI,
                'type' => 'permintaan-makan',
                'modul_id' => DocoConstants::GIZI_MODULE_ID,
                'tglnotifikasi' => $modelCatatan->tgl_permintaanmakan,
                'judulnotifikasi' => 'Permintaan Makan ' . $registrationData['nama_pasien'],
                'isi_notifikasi' => $message,
                'is_read' => false,
                'message' => $message,
                'additional_data' => json_encode($dataPayload),
                'created_date' => date("Y-m-d H:i:s"),
                'created_by' => $userId
            ];
            $arrayOfUsers = [];
            $payloadNotifications = [];
            foreach ($users as $user) {
                $notificationPayload = $defaultNotificationPayload;
                $arrayOfUsers[] = $notificationPayload['user_id'] = $user['loginpemakai_id'];
                $payloadNotifications[] = $notificationPayload;
            }
            Notifikasi::batchInsert($payloadNotifications);
            $payloadRedis = [
                'newNotification' => $defaultNotificationPayload,
                'users' => $arrayOfUsers
            ];
            self::publish('permintaan-makan-notification', $payloadRedis);
        }
    }
}
