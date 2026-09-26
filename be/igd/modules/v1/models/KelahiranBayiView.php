<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kelahiranbayi_v".
 *
 * @property int $kelahiranbayi_id
 * @property int $pendaftaran_id
 * @property int $bayi_urut
 * @property double $berat_badan
 * @property double $tinggi_badan
 * @property string $jenis_kelamin
 * @property string $penilaian
 * @property int $kondisi_bayi
 * @property bool $is_asi
 * @property string $keterangan_asi
 * @property string $masalah_lain
 * @property string $hasil
 */
class KelahiranBayiView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kelahiranbayi_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kelahiranbayi_id', 'pendaftaran_id', 'bayi_urut', 'kondisi_bayi'], 'default', 'value' => null],
            [['kelahiranbayi_id', 'pendaftaran_id', 'bayi_urut', 'kondisi_bayi'], 'integer'],
            [['berat_badan', 'tinggi_badan'], 'number'],
            [['is_asi'], 'boolean'],
            [['masalah_lain', 'hasil'], 'string'],
            [['jenis_kelamin', 'penilaian'], 'string', 'max' => 200],
            [['keterangan_asi'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kelahiranbayi_id' => 'Kelahiranbayi ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'bayi_urut' => 'Bayi Urut',
            'berat_badan' => 'Berat Badan',
            'tinggi_badan' => 'Tinggi Badan',
            'jenis_kelamin' => 'Jenis Kelamin',
            'penilaian' => 'Penilaian',
            'kondisi_bayi' => 'Kondisi Bayi',
            'is_asi' => 'Is Asi',
            'keterangan_asi' => 'Keterangan Asi',
            'masalah_lain' => 'Masalah Lain',
            'hasil' => 'Hasil',
        ];
    }
}
