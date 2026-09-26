<?php

namespace app\modules\v1\models;

use Yii;

class PendaftaranReplikasi extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pendaftaran_r';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasien_id'], 'integer'],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSyncedPatient()
    {
        return $this->hasOne(
            PasienReplikasi::className(), ['pasien_id' => 'pasien_id']
        )
        ->andWhere([
            PasienReplikasi::tableName().'.keterangan' => 'INSERT',
            PasienReplikasi::tableName().'.is_sending' => true,
            PasienReplikasi::tableName().'.is_sent' => true
        ]);
    }
}
?>