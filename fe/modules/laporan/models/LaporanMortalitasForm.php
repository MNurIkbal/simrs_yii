<?php

namespace app\modules\laporan\models;

use Yii;

/**
 * This is the model class for table "laporanmortalitas_v".
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
 * @property string $no_pendaftaran
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $kelompokdiagnosa_nama
 * @property string $diagnosa_katakunci
 * @property int $klasifikasidiagnosa_id
 * @property string $klasifikasidiagnosa_nama
 * @property string $dtd_kode
 * @property string $dtd_nama
 */
class LaporanMortalitasForm extends \yii\base\Model
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporanmortalitas_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pasien_id', 'pendaftaran_id', 'umur_0_6hr', 'umur_7_28hr', 'umur_28hr_<1thn', 'umur_1_4thn', 'umur_15_24thn', 'umur_25_44thn', 'umur_45_64thn', 'umur_>65thn', 'diagnosa_id', 'diagnosa_nourut', 'golonganumur_id', 'klasifikasidiagnosa_id'], 'default', 'value' => null],
            [['pasien_id', 'pendaftaran_id', 'umur_0_6hr', 'umur_7_28hr', 'umur_28hr_<1thn', 'umur_1_4thn', 'umur_15_24thn', 'umur_25_44thn', 'umur_45_64thn', 'umur_>65thn', 'diagnosa_id', 'diagnosa_nourut', 'golonganumur_id', 'klasifikasidiagnosa_id'], 'integer'],
            [['tglmorbiditas'], 'safe'],
            [['kasusdiagnosa', 'jeniskelamin', 'no_pendaftaran'], 'string', 'max' => 20],
            [['diagnosa_kode', 'no_rekam_medik', 'dtd_kode'], 'string', 'max' => 10],
            [['diagnosa_nama', 'diagnosa_namalainnya'], 'string', 'max' => 200],
            [['golonganumur_nama'], 'string', 'max' => 25],
            [['nama_pasien', 'kelompokdiagnosa_nama'], 'string', 'max' => 50],
            [['diagnosa_katakunci'], 'string', 'max' => 100],
            [['klasifikasidiagnosa_nama'], 'string', 'max' => 500],
            [['dtd_nama'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pasien_id' => Yii::t('fe','Pasien ID'),
            'pendaftaran_id' => Yii::t('fe','Pendaftaran ID'),
            'tglmorbiditas' => Yii::t('fe','Tglmorbiditas'),
            'kasusdiagnosa' => Yii::t('fe','Kasusdiagnosa'),
            'umur_0_6hr' => Yii::t('fe','Umur 0 6hr'),
            'umur_7_28hr' => Yii::t('fe','Umur 7 28hr'),
            'umur_28hr_<1thn' => Yii::t('fe','Umur 28hr <1thn'),
            'umur_1_4thn' => Yii::t('fe','Umur 1 4thn'),
            'umur_15_24thn' => Yii::t('fe','Umur 15 24thn'),
            'umur_25_44thn' => Yii::t('fe','Umur 25 44thn'),
            'umur_45_64thn' => Yii::t('fe','Umur 45 64thn'),
            'umur_>65thn' => Yii::t('fe','Umur >65thn'),
            'diagnosa_id' => Yii::t('fe','Diagnosa ID'),
            'diagnosa_kode' => Yii::t('fe','Diagnosa Kode'),
            'diagnosa_nama' => Yii::t('fe','Diagnosa Nama'),
            'diagnosa_namalainnya' => Yii::t('fe','Diagnosa Namalainnya'),
            'diagnosa_nourut' => Yii::t('fe','Diagnosa Nourut'),
            'golonganumur_id' => Yii::t('fe','Golonganumur ID'),
            'jeniskelamin' => Yii::t('fe','Jeniskelamin'),
            'golonganumur_nama' => Yii::t('fe','Golonganumur Nama'),
            'no_pendaftaran' => Yii::t('fe','No Pendaftaran'),
            'no_rekam_medik' => Yii::t('fe','No Rekam Medik'),
            'nama_pasien' => Yii::t('fe','Nama Pasien'),
            'kelompokdiagnosa_nama' => Yii::t('fe','Kelompokdiagnosa Nama'),
            'diagnosa_katakunci' => Yii::t('fe','Diagnosa Katakunci'),
            'klasifikasidiagnosa_id' => Yii::t('fe','Klasifikasidiagnosa ID'),
            'klasifikasidiagnosa_nama' => Yii::t('fe','Klasifikasidiagnosa Nama'),
            'dtd_kode' => Yii::t('fe','Dtd Kode'),
            'dtd_nama' => Yii::t('fe','Dtd Nama'),
        ];
    }
}
