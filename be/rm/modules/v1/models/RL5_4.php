<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rl5_4_10besarpenyakitrj_v".
 *
 * @property int $diagnosa_id
 * @property string $diagnosa_kode
 * @property string $diagnosa_nama
 * @property string $jml_laki
 * @property string $jml_perempuan
 * @property string $jumlah
 */
class RL5_4 extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rl5_4_10besarpenyakitrj_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['diagnosa_id', 'jml_laki', 'jml_perempuan', 'jumlah'], 'default', 'value' => null],
            [['diagnosa_id', 'jml_laki', 'jml_perempuan', 'jumlah'], 'integer'],
            [['diagnosa_kode'], 'string', 'max' => 10],
            [['diagnosa_nama'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'diagnosa_id' => 'Diagnosa ID',
            'diagnosa_kode' => 'Diagnosa Kode',
            'diagnosa_nama' => 'Diagnosa Nama',
            'jml_laki' => 'Jml Laki',
            'jml_perempuan' => 'Jml Perempuan',
            'jumlah' => 'Jumlah',
        ];
    }
}
