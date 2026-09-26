<?php

namespace app\modules\antrian\models;

use Yii;

/**
 *
 * @property string $kode_booking
 *
 */

class AntrianCheckinMjkn extends \yii\base\Model
{
    public $kode_booking;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'kode_booking',
            ], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
            [[ 
                'kode_booking',
            ], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kode_booking' => 'Kode Booking'
        ];
    }

}