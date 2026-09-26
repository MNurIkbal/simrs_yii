<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-27 11:15:57
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-07-27 11:18:44
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "stokobatpasien_v".
 *
 * @property string $tipe_pemberian
 * @property int $pendaftaran_id
 * @property string $nomor
 */
class StokObatPasienView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stokobatpasien_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipe_pemberian', 'nomor'], 'string'],
            [['pendaftaran_id'], 'default', 'value' => null],
            [['pendaftaran_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tipe_pemberian' => 'Tipe Pemberian',
            'pendaftaran_id' => 'Pendaftaran ID',
            'nomor' => 'Nomor',
        ];
    }
}
