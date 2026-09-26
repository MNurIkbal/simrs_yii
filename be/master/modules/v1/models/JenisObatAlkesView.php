<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "jenisobatalkes_v".
 *
 * @property integer $jenisobatalkes_id
 * @property string $jenisobatalkes_kode
 * @property string $jenisobatalkes_nama
 * @property string $jenisobatalkes_namalain 
 * @property boolean $is_sync
 * @property integer $group_jenisobat
 */
class JenisObatAlkesView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jenisobatalkes_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenisobatalkes_id', 'group_jenisobat'], 'integer'],
            [['jenisobatalkes_nama', 'jenisobatalkes_kode', 'group_jenisobat_nama', 'jenisobatalkes_namalain'], 'string'],
            [['is_sync'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jenisobatalkes_id' => 'Jenisobatalkes ID',
            'group_jenisobat' => 'Grup Jenis Obat',
            'jenisobatalkes_nama' => 'Nama Jenis Obat Alkes',
            'jenisobatalkes_namalain' => 'Nama Jenis Obat Alkes Lain',
            'jenisobatalkes_kode' => 'Kode Jenis Obat Alkes',
        ];
    }
}
