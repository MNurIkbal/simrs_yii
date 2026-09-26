<?php

/**
 * @Author: afil
 * @Date:   2018-01-19 09:20:43
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-01-21 11:41:32
 * @Description: 
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pasienmorbiditas_t".
 *
 * @property int $pasienmorbiditas_id
 * @property int $morfologineoplasma_id
 * @property int $instalasi_id
 * @property int $kamarruangan_id
 * @property int $jenisketunaan_id
 * @property int $jeniskasuspenyakit_id
 * @property int $ruangan_id
 * @property int $diagnosaicdix_id
 * @property int $pegawai_id
 * @property int $sebabdiagnosa_id
 * @property int $kelompokumur_id
 * @property int $diagnosa_id
 * @property int $sebabin_id
 * @property int $pasien_id
 * @property int $jenisin_id
 * @property int $kelompokdiagnosa_id
 * @property int $golonganumur_id
 * @property int $pendaftaran_id
 * @property int $penyebabluarcedera_id
 * @property int $pasienadmisi_id
 * @property string $tglmorbiditas
 * @property string $kasusdiagnosa
 * @property string $diagnosa_pasien
 * @property int $umur_0_28hr
 * @property int $umur_28hr_1thn
 * @property int $umur_1_4thn
 * @property int $umur_5_14thn
 * @property int $umur_15_24thn
 * @property int $umur_25_44thn
 * @property int $umur_45_64thn
 * @property int $umur_65
 * @property bool $infeksinosokomial
 * @property int $laki_laki
 * @property int $perempuan
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 */
class PasienMorbiditas extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pasienmorbiditas_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['morfologineoplasma_id', 'instalasi_id', 'kamarruangan_id', 'jenisketunaan_id', 'jeniskasuspenyakit_id', 'ruangan_id', 'diagnosaicdix_id', 'pegawai_id', 'sebabdiagnosa_id', 'kelompokumur_id', 'diagnosa_id', 'sebabin_id', 'pasien_id', 'jenisin_id', 'kelompokdiagnosa_id', 'golonganumur_id', 'penyebabluarcedera_id', 'pasienadmisi_id', 'laki_laki', 'perempuan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['umur_0_6hr', 'umur_28hr_<1thn', 'umur_1_4thn', 'umur_15_24thn', 'umur_25_44thn', 'umur_45_64thn', 'umur_>65thn'], 'default', 'value' => null],
            [['infeksinosokomial'], 'default', 'value' => false],
            [['morfologineoplasma_id', 'instalasi_id', 'kamarruangan_id', 'jenisketunaan_id', 'jeniskasuspenyakit_id', 'ruangan_id', 'diagnosaicdix_id', 'pegawai_id', 'sebabdiagnosa_id', 'kelompokumur_id', 'diagnosa_id', 'sebabin_id', 'pasien_id', 'jenisin_id', 'kelompokdiagnosa_id', 'golonganumur_id', 'penyebabluarcedera_id', 'pasienadmisi_id', 'umur_0_6hr', 'umur_28hr_<1thn', 'umur_1_4thn', 'umur_15_24thn', 'umur_25_44thn', 'umur_45_64thn', 'umur_>65thn', 'laki_laki', 'perempuan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['jeniskasuspenyakit_id', 'ruangan_id', 'pegawai_id', 'pasien_id', 'kelompokdiagnosa_id', 'pendaftaran_id', 'tglmorbiditas', 'kasusdiagnosa'], 'required'],
            [['tglmorbiditas', 'created_date', 'last_modified_date', 'deleted_date', 'diagnosa_pasien'], 'safe'],
            [['infeksinosokomial', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['kasusdiagnosa'], 'string', 'max' => 20],
            // [['diagnosa_pasien'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pasienmorbiditas_id' => 'Pasienmorbiditas ID',
            'morfologineoplasma_id' => 'Morfologineoplasma ID',
            'instalasi_id' => 'Instalasi ID',
            'kamarruangan_id' => 'Kamarruangan ID',
            'jenisketunaan_id' => 'Jenisketunaan ID',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'ruangan_id' => 'Ruangan ID',
            'diagnosaicdix_id' => 'Diagnosaicdix ID',
            'pegawai_id' => 'Pegawai ID',
            'sebabdiagnosa_id' => 'Sebabdiagnosa ID',
            'diagnosa_id' => 'Diagnosa ID',
            'sebabin_id' => 'Sebabin ID',
            'pasien_id' => 'Pasien ID',
            'jenisin_id' => 'Jenisin ID',
            'kelompokdiagnosa_id' => 'Kelompokdiagnosa ID',
            'golonganumur_id' => 'Golonganumur ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'penyebabluarcedera_id' => 'Penyebabluarcedera ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'tglmorbiditas' => 'Tglmorbiditas',
            'kasusdiagnosa' => 'Kasusdiagnosa',
            'umur_0_6hr' => 'Umur 0 28hr',
            'umur_28hr_' => 'Umur 28hr 1thn',
            'umur_1_4thn' => 'Umur 1 4thn',
            'umur_5_14thn' => 'Umur 5 14thn',
            'umur_25_44thn' => 'Umur 25 44thn',
            'umur_45_64thn' => 'Umur 45 64thn',
            'umur_>65thn' => 'Umur 65',
            'infeksinosokomial' => 'Infeksinosokomial',
            'laki_laki' => 'Laki Laki',
            'perempuan' => 'Perempuan',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
            'diagnosa_pasien' => 'Diagnosa Pasien'
        ];
    }

    public function getKelompokdiagnosa()
    {
        return $this->hasOne(KelompokDiagnosa::className(), ['kelompokdiagnosa_id' => 'kelompokdiagnosa_id']);
    }
}
