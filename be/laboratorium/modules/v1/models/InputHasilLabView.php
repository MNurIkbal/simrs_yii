<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "inputhasillab_v".
 *
 * @property int $tindakanpelayanan_id
 * @property int $pasienmasukpenunjang_id
 * @property string $catatan_dokterpengirim
 * @property string $nama_sample
 * @property string $daftartindakan_nama
 * @property bool $is_expertise
 */
class InputHasilLabView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inputhasillab_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tindakanpelayanan_id', 'pasienmasukpenunjang_id'], 'default', 'value' => null],
            [['tindakanpelayanan_id', 'pasienmasukpenunjang_id'], 'integer'],
            [['catatan_dokterpengirim'], 'string'],
            [['is_expertise'], 'boolean'],
            [['nama_sample'], 'string', 'max' => 100],
            [['daftartindakan_nama'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tindakanpelayanan_id' => 'Tindakanpelayanan ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'catatan_dokterpengirim' => 'Catatan Dokterpengirim',
            'nama_sample' => 'Nama Sample',
            'daftartindakan_nama' => 'Daftartindakan Nama',
            'is_expertise' => 'Is Expertise',
        ];
    }
}
