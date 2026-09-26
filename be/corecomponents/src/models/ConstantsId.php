<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 * 
 * Penggunaan ID Constant
 */

namespace Doco\models;

use Yii;

/**
 * This is the model class for table "lookuptransaksi_m".
 *
 * @property string $kode_transaksi
 * @property int $kode_id
 * @property string $kode_fungsi
 */
class ConstantsId extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lookuptransaksi_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kode_transaksi', 'kode_id'], 'required'],
            [['kode_fungsi'], 'default', 'value' => null],
            [['kode_id'], 'integer'],
            [['kode_fungsi'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kode_transaksi' => 'Kode Transaksi',
            'kode_id' => 'Kode ID',
            'kode_fungsi' => 'Fungsi Kode',
        ];
    }
}
