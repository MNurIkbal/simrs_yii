<?php

namespace app\modules\antrian\models;

use Yii;

/**
 *
 * @property string $kode_booking
 *
 */

class ReservasiNonMJKN extends \yii\base\Model
{
    public $kode_booking;
    public $penjamin_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'kode_booking',
                'penjamin_id',
            ], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
            [[ 
                'kode_booking',
                'penjamin_id',
            ], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kode_booking' => 'Kode Booking',
            'penjamin_id' => 'Penjamin'
        ];
    }

}