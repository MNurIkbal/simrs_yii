<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "inpostoperasi1_v".
 *
 * @property int $inpostoperasi_id
 * @property int $pasienmasukpenunjang_id
 * @property int $timoperasi_id
 * @property int $posisi_tim
 * @property string $posisi
 * @property int $pegawai_id
 * @property string $nama_pegawai
 */
class TimOperasiView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inpostoperasi1_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inpostoperasi_id', 'pasienmasukpenunjang_id', 'timoperasi_id', 'posisi_tim', 'pegawai_id'], 'default', 'value' => null],
            [['inpostoperasi_id', 'pasienmasukpenunjang_id', 'timoperasi_id', 'posisi_tim', 'pegawai_id'], 'integer'],
            [['posisi'], 'string', 'max' => 200],
            [['nama_pegawai'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'inpostoperasi_id' => 'Inpostoperasi ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'timoperasi_id' => 'Timoperasi ID',
            'posisi_tim' => 'Posisi Tim',
            'posisi' => 'Posisi',
            'pegawai_id' => 'Pegawai ID',
            'nama_pegawai' => 'Nama Pegawai',
        ];
    }
}
