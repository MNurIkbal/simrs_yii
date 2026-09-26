<?php

namespace Doco\Notifications;

use Yii;
use Doco\components\DocoConstants;
use Doco\models\Notifikasi;
use Doco\models\User;
use Doco\models\Modul;

class PembantaranNotification extends BaseNotification
{
    const CHANNEL = 'pembantaran-notification';
    const TYPE_RUJUKAN = 'pembantaran-rujukan';

    protected static $moduleId;

    /**
     * Kirim notifikasi ketika rujukan pembantaran baru diterima.
     *
     * @param array|\app\modules\v1\models\RujukanBantaran $rujukan
     * @param array $documents
     * @return void
     */
    public static function newRujukan($rujukan, $documents = [])
    {
        try {
            $moduleId = self::resolveModuleId();
            if (!$moduleId) {
                return;
            }

            $users = User::usersIdByModule($moduleId);
            if (empty($users)) {
                return;
            }

            // Handle unserialized/detached ActiveRecord objects
            if (is_object($rujukan) && method_exists($rujukan, 'getAttributes')) {
                $rujukanData = $rujukan->getAttributes();
            } elseif (is_array($rujukan)) {
                $rujukanData = $rujukan;
            } else {
                Yii::error('Invalid rujukan data type', __METHOD__);
                return;
            }
            
            $now = date('Y-m-d H:i:s');
            $userId = isset(Yii::$app->user) && !Yii::$app->user->isGuest ? Yii::$app->user->id : null;

            $additionalData = [
                'rujukanbantaran_id' => $rujukanData['rujukanbantaran_id'] ? $rujukanData['rujukanbantaran_id'] : null,
                'no_identitas_pasien' => $rujukanData['no_identitas_pasien'] ? $rujukanData['no_identitas_pasien'] : null,
                'nama_pasien' => $rujukanData['nama_pasien'] ?  $rujukanData['nama_pasien'] : null,
                'uptasal_nama' => $rujukanData['uptasal_nama'] ? $rujukanData['uptasal_nama'] :  null,
                'keterangan_rujukan' => $rujukanData['keterangan_rujukan'] ? $rujukanData['keterangan_rujukan'] : null,
                'dokumen' => $documents,
                'created_date' => $now,
            ];

            $message = sprintf(
                '%s (%s) dari %s menunggu verifikasi.',
                $additionalData['nama_pasien'] ?: '-',
                $additionalData['no_identitas_pasien'] ?: '-',
                $additionalData['uptasal_nama'] ?: '-'
            );

            $defaultNotificationPayload = [
                'instalasi_id' => DocoConstants::INST_ID_PDF,
                'type' => self::TYPE_RUJUKAN,
                'modul_id' => $moduleId,
                'tglnotifikasi' => $now,
                'judulnotifikasi' => 'Rujukan Bantaran Baru',
                'isi_notifikasi' => $message,
                'message' => $message,
                'is_read' => false,
                'additional_data' => json_encode($additionalData),
                'created_date' => $now,
                'created_by' => $userId,
            ];

            $payloadNotifications = [];
            $userIds = [];
            foreach ($users as $user) {
                $userIds[] = (int) $user['loginpemakai_id'];
                $payload = $defaultNotificationPayload;
                $payload['user_id'] = (int) $user['loginpemakai_id'];
                $payloadNotifications[] = $payload;
            }

            if (!empty($payloadNotifications)) {
                Notifikasi::batchInsert($payloadNotifications);
                $payloadRedis = [
                    'newNotification' => $defaultNotificationPayload,
                    'users' => $userIds,
                ];
                Yii::error(self::publish('pembantaran-notification', $payloadRedis));
            }
        } catch (\Throwable $exception) {
            Yii::error([
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
        }
    }

    protected static function resolveModuleId()
    {
        self::$moduleId = Modul::find()
                ->select('modul_id')
                ->andWhere(['is_deleted' => false])
                ->andWhere(['url_modul' => '/pendaftaran/rujukan-bantaran/'])
                ->scalar();

        return self::$moduleId;
    }
}
