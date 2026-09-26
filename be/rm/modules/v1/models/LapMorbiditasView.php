<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporanrekapmorbiditas_v".
 *
 * @property int $pasien_id
 * @property int $pendaftaran_id
 * @property string $tglmorbiditas
 * @property string $kasusdiagnosa
 * @property int $diagnosa_id
 * @property string $diagnosa_kode
 * @property string $diagnosa_nama
 * @property string $diagnosa_namalainnya
 * @property int $diagnosa_nourut
 * @property int $golonganumur_id
 * @property string $jeniskelamin
 * @property string $golonganumur_nama
 */
class LapMorbiditasView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporanrekapmorbiditas_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pasien_id', 'pendaftaran_id', 'diagnosa_id', 'diagnosa_nourut', 'golonganumur_id'], 'default', 'value' => null],
            [['pasien_id', 'pendaftaran_id', 'diagnosa_id', 'diagnosa_nourut', 'golonganumur_id'], 'integer'],
            [['tglmorbiditas'], 'safe'],
            [['kasusdiagnosa', 'jeniskelamin'], 'string', 'max' => 20],
            [['diagnosa_kode'], 'string', 'max' => 10],
            [['diagnosa_nama', 'diagnosa_namalainnya'], 'string', 'max' => 200],
            [['golonganumur_nama'], 'string', 'max' => 25],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pasien_id' => 'Pasien ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'tglmorbiditas' => 'Tglmorbiditas',
            'kasusdiagnosa' => 'Kasusdiagnosa',
            'diagnosa_id' => 'Diagnosa ID',
            'diagnosa_kode' => 'Diagnosa Kode',
            'diagnosa_nama' => 'Diagnosa Nama',
            'diagnosa_namalainnya' => 'Diagnosa Namalainnya',
            'diagnosa_nourut' => 'Diagnosa Nourut',
            'golonganumur_id' => 'Golonganumur ID',
            'jeniskelamin' => 'Jeniskelamin',
            'golonganumur_nama' => 'Golonganumur Nama',
        ];
    }
}
