<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kasuspenyakitruangan_v".
 *
 * @property int $jeniskasuspenyakit_id
 * @property string $jeniskasuspenyakit_nama
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $instalasi_id
 * @property string $instalasi_nama
 */
class KasusPenyakitRuanganView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kasuspenyakitruangan_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jeniskasuspenyakit_id', 'ruangan_id', 'instalasi_id'], 'default', 'value' => null],
            [['jeniskasuspenyakit_id', 'ruangan_id', 'instalasi_id'], 'integer'],
            [['instalasi_nama'], 'string'],
            [['jeniskasuspenyakit_nama'], 'string', 'max' => 255],
            [['ruangan_nama'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jeniskasuspenyakit_id' => 'Jenis Kasus Penyakit ID',
            'jeniskasuspenyakit_nama' => 'Nama Jenis Kasus Penyakit',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Nama Ruangan',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_nama' => 'Nama Instalasi'
        ];
    }
}
