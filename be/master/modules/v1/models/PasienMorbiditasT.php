<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "suku_m".
 *
 * @property integer $pasienmorbiditas_id

 * @property string $additional_data
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 */
class PasienMorbiditasT extends \Doco\components\DocoActiveRecord
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
            [['pasienmorbiditas_id', 'morfologineoplasma_id', 'instalasi_id', 'kamarruangan_id','jenisketunaan_id', 'jeniskasuspenyakit_id','ruangan_id','diagnosaicdix_id','pegawai_id','sebabdiagnosa_id','kelompokumur_id','diagnosa_id','sebabin_id','pasien_id','kelompokdiagnosa_id','golonganumur_id','pendaftaran_id','penyebabluarcedera_id','pasienadmisi_id','tglmorbiditas','kasusdiagnosa','umur_0_6hr','umur_7_28hr','umur_28hr_<1thn','umur_1_4thn','umur_15_24thn','umur_25_44thn','umur_45_64thn','umur_>65thn','infeksinosokomial','laki_laki','perempuan
            ','umur_5_14thn','diagnosa_pasien'], 'default', 'value' => null],

            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return[
            'pasienmorbiditas_id' => 'Pasienmorbiditas ID',
            'morfologineoplasma_id' => 'Morfologineoplasma ID',  
            'instalasi_id'=> 'Instalasi ID',
            'kamarruangan_id'=> 'Kamar Ruangan ID',
            'jenisketunaan_id' => 'Jenis Keturunaan ID',
            'jeniskasuspenyakit_id'=> 'Jenis Kasus Penyakit ID',
            'ruangan_id'=> 'Ruangan ID',
            'diagnosaicdix_id'=> 'Diagnosaicdix ID',   
            'pegawai_id'=> 'Pegawai ID',
            'sebabdiagnosa_id'=> 'Sebab Diagnosa ID',   
            'kelompokumur_id'=> 'Kelompok Umur ID',
            'diagnosa_id'=> 'Diagnosa ID',
            'sebabin_id'=> 'Sebabin ID',
            'pasien_id'  => 'Pasien ID',
            'jenisin_id' => 'Jenisin ID',
            'kelompokdiagnosa_id'=> 'Kelompok Diagnosa ID',
            'golonganumur_id'=> 'Golongan Umur ID',
            'pendaftaran_id'=> 'Pendaftaran ID',
            'penyebabluarcedera_id'=> 'Penyebab Luar Cedera ID',  
            'pasienadmisi_id' => 'Pasien Admisi ID',
            'tglmorbiditas' =>'Tanggal Morbiditas',
            'kasusdiagnosa' =>  'Kasus Diagnosa',
            'umur_0_6hr' => 'Umur 0 - 6 Hari',
            'umur_7_28hr' => 'Umur 7 - 28 Hari',
            'umur_28hr_<1thn' => 'Umur 28 < 1 tahun',
            'umur_1_4thn'=> 'Umur 1 < 4 tahun',
            'umur_15_24thn'=> 'Umur 15 < 24 tahun',
            'umur_25_44thn' =>  'Umur 25 < 44 tahun',
            'umur_45_64thn' =>  'Umur 45 < 64 tahun',
            'umur_>65thn' => 'Umur > 65 tahun',
            'infeksinosokomial' => 'Infeksinosokomial',
            'laki_laki' =>  'Laki-laki',
            'perempuan' => 'Perempuan',
            'umur_5_14thn' => 'Umur 5 - 14 tahun',
            'diagnosa_pasien' => 'Diagnosa Pasien',

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
        ];
    }
}
