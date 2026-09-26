<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "cektagihan_v".
 *
 * @property int $pendaftaran_id
 * @property int $pasien_id
 * @property string $no_rekam_medik
 * @property double $total_tagihan
 * @property double $total_sdh_bayar
 */
class CekTagihanView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cektagihan_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasien_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasien_id'], 'integer'],
            [['total_tagihan', 'total_sdh_bayar'], 'number'],
            [['no_rekam_medik'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasien_id' => 'Pasien ID',
            'no_rekam_medik' => 'No Rekam Medik',
            'total_tagihan' => 'Total Tagihan',
            'total_sdh_bayar' => 'Total Sdh Bayar',
        ];
    }
}
