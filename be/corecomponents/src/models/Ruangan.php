<?php

namespace Doco\models;

use Yii;

/**
 * This is the model class for table "ruangan_m".
 *
 * @property int $ruangan_id
 * @property int $instalasi_id
 * @property string $ruangan_nama
 * @property string $ruangan_namalainnya
 * @property string $ruangan_jenispelayanan
 * @property string $ruangan_singkatan
 * @property string $ruangan_fasilitas
 * @property string $ruangan_lokasi
 * @property string $ruangan_image
 * @property int $ruangan_urutan
 * @property string $ruangan_filesuara
 * @property int $estimasipelayanan
 * @property string $image_mobile
 * @property int $warnadokrm_id
 * @property string $kode_ruanganpoli
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
 * @property int $unitkerja_id
 *
 */
class Ruangan extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ruangan_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['instalasi_id', 'ruangan_urutan', 'estimasipelayanan', 'warnadokrm_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'unitkerja_id'], 'default', 'value' => null],
            [['instalasi_id', 'ruangan_urutan', 'estimasipelayanan', 'warnadokrm_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'unitkerja_id'], 'integer'],
            [['ruangan_nama', 'unitkerja_id'], 'required'],
            [['ruangan_fasilitas', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date','is_modul'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ruangan_id' => 'Ruangan ID',
            'instalasi_id' => 'Instalasi ID',
            'ruangan_nama' => 'Ruangan Nama',
            'ruangan_namalainnya' => 'Ruangan Namalainnya',
            'ruangan_jenispelayanan' => 'Ruangan Jenispelayanan',
            'ruangan_singkatan' => 'Ruangan Singkatan',
            'ruangan_fasilitas' => 'Ruangan Fasilitas',
            'ruangan_lokasi' => 'Ruangan Lokasi',
            'ruangan_image' => 'Ruangan Image',
            'ruangan_urutan' => 'Ruangan Urutan',
            'ruangan_filesuara' => 'Ruangan Filesuara',
            'estimasipelayanan' => 'Estimasipelayanan',
            'image_mobile' => 'Image Mobile',
            'warnadokrm_id' => 'Warnadokrm ID',
            'kode_ruanganpoli' => 'Kode Ruanganpoli',
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
            'unitkerja_id' => 'Unitkerja ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstalasi()
    {
        return $this->hasOne(\Doco\models\Instalasi::className(), ['instalasi_id' => 'instalasi_id']);
    }
}