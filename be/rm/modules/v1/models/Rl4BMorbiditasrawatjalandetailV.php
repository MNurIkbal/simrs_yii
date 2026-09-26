<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rl4_b_morbiditasrawatjalandetail_v".
 *
 * @property int $pendaftaran_id
 * @property string $no_pendaftaran
 * @property string $tgl_pendaftaran
 * @property int $pasien_id
 * @property string $nama_pasien
 * @property int $golonganumur_id
 * @property string $golonganumur_namalainnya
 * @property string $jeniskelamin
 * @property string $jenis_kelamin
 * @property int $diagnosa_id
 * @property string $diagnosa_nama
 * @property int $carakeluar_id
 * @property string $carakeluar_nama
 */
class Rl4BMorbiditasrawatjalandetailV extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rl4_b_morbiditasrawatjalandetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasien_id', 'golonganumur_id', 'diagnosa_id', 'carakeluar_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasien_id', 'golonganumur_id', 'diagnosa_id', 'carakeluar_id'], 'integer'],
            [['tgl_pendaftaran'], 'safe'],
            [['jenis_kelamin'], 'string'],
            [['no_pendaftaran', 'jeniskelamin'], 'string', 'max' => 20],
            [['nama_pasien'], 'string', 'max' => 50],
            [['golonganumur_namalainnya'], 'string', 'max' => 25],
            [['diagnosa_nama'], 'string', 'max' => 200],
            [['carakeluar_nama'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'no_pendaftaran' => 'No Pendaftaran',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'pasien_id' => 'Pasien ID',
            'nama_pasien' => 'Nama Pasien',
            'golonganumur_id' => 'Golonganumur ID',
            'golonganumur_namalainnya' => 'Golonganumur Namalainnya',
            'jeniskelamin' => 'Jeniskelamin',
            'jenis_kelamin' => 'Jenis Kelamin',
            'diagnosa_id' => 'Diagnosa ID',
            'diagnosa_nama' => 'Diagnosa Nama',
            'carakeluar_id' => 'Carakeluar ID',
            'carakeluar_nama' => 'Carakeluar Nama',
        ];
    }
}
