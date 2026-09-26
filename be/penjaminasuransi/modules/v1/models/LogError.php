<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "listerror_r".
 *
 * @property int $listerror_id
 * @property string $nama
 * @property string $tgl_error
 * @property string $pesan

 */
class LogError extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'listerror_r';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tgl_error', 'pesan'], 'safe'],
            [['nama'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'error_id' => 'Error ID',
            'nama' => 'Nama',
            'tgl_error' => 'Tgl Error',
            'pesan' => 'Pesan',
        ];
    }
}
