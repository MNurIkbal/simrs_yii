<?php

/**
 * @Author: afil
 * @Date:   2018-01-18 13:33:08
 * @Last Modified by:   afil
 * @Last Modified time: 2018-04-24 14:34:05
 * @Description: 
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "reseptur_t".
 *
 * @property int $reseptur_id
 * @property int $pasienadmisi_id
 * @property int $ruangan_id
 * @property int $pasien_id
 * @property int $pegawai_id
 * @property int $pendaftaran_id
 * @property int $penjualanresep_id
 * @property string $tglreseptur
 * @property string $noresep
 * @property int $ruanganreseptur_id
 * @property string $fileresep
 * @property string $create_time
 * @property string $update_time
 * @property int $status_reseptur
 * @property int $antrian_id
 * @property int $create_ruangan
 * @property int $unitdosis_id
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
 *
 * @property PenjualanresepT[] $penjualanresepTs
 */
class Reseptur extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reseptur_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pasienadmisi_id', 'ruangan_id', 'pasien_id', 'pegawai_id', 'pendaftaran_id', 'penjualanresep_id', 'ruanganreseptur_id', 'status_reseptur', 'antrian_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['reseptur_id', 'pasienadmisi_id', 'ruangan_id', 'pasien_id', 'pegawai_id', 'pendaftaran_id', 'penjualanresep_id', 'ruanganreseptur_id', 'status_reseptur', 'antrian_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tglreseptur', 'create_time', 'update_time', 'created_date', 'last_modified_date', 'deleted_date', 'status_reseptur','diagnosa_id'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['noresep'], 'string', 'max' => 50],
            // [['reseptur_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'reseptur_id' => 'Reseptur ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'ruangan_id' => 'Ruangan ID',
            'pasien_id' => 'Pasien ID',
            'pegawai_id' => 'Pegawai ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'penjualanresep_id' => 'Penjualanresep ID',
            'tglreseptur' => 'Tglreseptur',
            'noresep' => 'Noresep',
            'ruanganreseptur_id' => 'Ruanganreseptur ID',
            'fileresep' => 'Fileresep',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'status_reseptur' => 'Status Reseptur',
            'antrian_id' => 'Antrian ID',
            'create_ruangan' => 'Create Ruangan',
            'unitdosis_id' => 'Unitdosis ID',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenjualanresepTs()
    {
        return $this->hasMany(PenjualanresepT::className(), ['reseptur_id' => 'reseptur_id']);
    }
}
