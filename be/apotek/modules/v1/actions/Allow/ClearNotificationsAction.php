<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\Allow;

use Yii;
use yii\base\Action;
use Doco\Notifications\FarmasiNotification;
use Doco\models\NotifikasiFarmasi;

class ClearNotificationsAction extends Action
{
    public function run()
    {
        $judulnotifikasi = Yii::$app->request->get('judulnotifikasi', null);
        if (!empty($judulnotifikasi)) {
            NotifikasiFarmasi::updateAll(['is_read' => true], [
                'and',
                ['judulnotifikasi' => $judulnotifikasi],
                ['is_read' => false]
            ]);
            FarmasiNotification::updateNotif();
            return $this->controller->responseJson(200, 'Status Notifikasi berhasil diperbarui');
        } else {
            return $this->controller->responseJson(400, 'Judul Notifikasi tidak boleh kosong');
        }
    }
}
