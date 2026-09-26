<?php

namespace Doco\models\pendaftaran;

use Yii;

use \Doco\components\DocoConstants;

/**
 * This is the model class for table "infojadwaldokter_v".
 */
class InfoJadwalDokterView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infojadwaldokter_v';
    }

    public function getJadwalDokterIdByWaktu($pegawai_id, $ruangan_id, $date = null, $waktu = null)
    {
        $date = empty($date) ? date('Y-m-d') : $date;
        $waktu = empty($waktu) ? date('H:i:s') : $waktu;
        $hari = DocoConstants::$look_hari[date('N', strtotime($date))];

        $result = InfoJadwalDokterView::find()->select([
            'jadwaldokter_id'
        ])->where([
            'pegawai_id' => $pegawai_id,
            'ruangan_id' => $ruangan_id,
            'hari_jadwalbuka' => $hari,
        ])->andWhere(['<=','waktu_mulai', $waktu])
        ->andWhere(['>=','waktu_selesai', $waktu])
        ->asArray()->one();

        return $result;
    }
}
