<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kasuspenyakitruangan_v".
 *
 * @property integer $jeniskasuspenyakit_id
 * @property string $jeniskasuspenyakit_nama
 * @property integer $ruangan_id
 * @property string $ruangan_nama
 */
class RuanganPelayananV extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ruanganpelayanan_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jeniskasuspenyakit_id', 'ruangan_id', 'kelaspelayanan_id'], 'integer'],
            [['jeniskasuspenyakit_nama'], 'string', 'max' => 100],
            [['kelaspelayanan_nama'], 'string', 'max' => 100],
            [['ruangan_nama'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'jeniskasuspenyakit_nama' => 'Jeniskasuspenyakit Nama',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
        ];
    }
}
