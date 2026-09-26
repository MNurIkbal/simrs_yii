<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rl4_b_morbiditasrawatjalan_v".
 *
 * @property string $kolom
 * @property int $dtd_id
 * @property string $no_dtd
 * @property string $dtd_noterperinci
 * @property int $klasifikasidiagnosa_id
 * @property string $klasifikasidiagnosa_kode
 * @property int $diagnosa_id
 * @property string $diagnosa_kode
 * @property string $diagnosa_nama
 * @property int $golonganumur_id
 * @property string $golonganumur_nama
 * @property string $golonganumur_namalainnya
 */
class Rl4BMorbiditasrawatjalanV extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rl4_b_morbiditasrawatjalan_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kolom', 'no_dtd', 'dtd_noterperinci', 'klasifikasidiagnosa_kode', 'diagnosa_kode', 'diagnosa_nama', 'golonganumur_nama', 'golonganumur_namalainnya'], 'string'],
            [['dtd_id', 'klasifikasidiagnosa_id', 'diagnosa_id', 'golonganumur_id'], 'default', 'value' => null],
            [['dtd_id', 'klasifikasidiagnosa_id', 'diagnosa_id', 'golonganumur_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kolom' => 'Kolom',
            'dtd_id' => 'Dtd ID',
            'no_dtd' => 'No Dtd',
            'dtd_noterperinci' => 'Dtd Noterperinci',
            'klasifikasidiagnosa_id' => 'Klasifikasidiagnosa ID',
            'klasifikasidiagnosa_kode' => 'Klasifikasidiagnosa Kode',
            'diagnosa_id' => 'Diagnosa ID',
            'diagnosa_kode' => 'Diagnosa Kode',
            'diagnosa_nama' => 'Diagnosa Nama',
            'golonganumur_id' => 'Golonganumur ID',
            'golonganumur_nama' => 'Golonganumur Nama',
            'golonganumur_namalainnya' => 'Golonganumur Namalainnya',
        ];
    }
}
