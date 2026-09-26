<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporanmorbiditas_v".
 *
 * @property int $pasien_id
 * @property int $pendaftaran_id
 * @property string $tglmorbiditas
 * @property string $kasusdiagnosa
 * @property int $umur_0_6hr
 * @property int $umur_7_28hr
 * @property int $umur_28hr_<1thn
 * @property int $umur_1_4thn
 * @property int $umur_15_24thn
 * @property int $umur_25_44thn
 * @property int $umur_45_64thn
 * @property int $umur_>65thn
 * @property int $diagnosa_id
 * @property string $diagnosa_kode
 * @property string $diagnosa_nama
 * @property string $diagnosa_namalainnya
 * @property int $diagnosa_nourut
 * @property int $golonganumur_id
 * @property string $jeniskelamin
 * @property string $golonganumur_nama
 */
class LaporanMorbiditasView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporanmorbiditas_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pasien_id', 'pendaftaran_id', 'umur_0_6hr', 'umur_7_28hr', 'umur_28hr_<1thn', 'umur_1_4thn', 'umur_15_24thn', 'umur_25_44thn', 'umur_45_64thn', 'umur_>65thn', 'diagnosa_id', 'diagnosa_nourut', 'golonganumur_id'], 'default', 'value' => null],
            [['pasien_id', 'pendaftaran_id', 'umur_0_6hr', 'umur_7_28hr', 'umur_28hr_<1thn', 'umur_1_4thn', 'umur_15_24thn', 'umur_25_44thn', 'umur_45_64thn', 'umur_>65thn', 'diagnosa_id', 'diagnosa_nourut', 'golonganumur_id'], 'integer'],
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
            'umur_0_6hr' => 'Umur 0 6hr',
            'umur_7_28hr' => 'Umur 7 28hr',
            'umur_28hr_<1thn' => 'Umur 28hr <1thn',
            'umur_1_4thn' => 'Umur 1 4thn',
            'umur_15_24thn' => 'Umur 15 24thn',
            'umur_25_44thn' => 'Umur 25 44thn',
            'umur_45_64thn' => 'Umur 45 64thn',
            'umur_>65thn' => 'Umur >65thn',
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
