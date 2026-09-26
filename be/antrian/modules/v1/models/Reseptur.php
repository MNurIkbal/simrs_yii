<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "reseptur_t".
 *
 * @property integer $reseptur_id
 * @property integer $pasienadmisi_id
 * @property integer $ruangan_id
 * @property integer $pasien_id
 * @property integer $pegawai_id
 * @property integer $pendaftaran_id
 * @property integer $penjualanresep_id
 * @property string $tglreseptur
 * @property string $noresep
 * @property integer $ruanganreseptur_id
 * @property string $fileresep
 * @property string $create_time
 * @property string $update_time
 * @property integer $create_loginpemakai_id
 * @property integer $update_loginpemakai_id
 * @property integer $create_ruangan
 * @property integer $unitdosis_id
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
            [['reseptur_id'], 'required'],
            [['reseptur_id', 'pasienadmisi_id', 'ruangan_id', 'pasien_id', 'pegawai_id', 'pendaftaran_id', 'penjualanresep_id', 'ruanganreseptur_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tglreseptur', 'create_time', 'update_time', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['noresep'], 'string', 'max' => 50],
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
            'create_loginpemakai_id' => 'Create Loginpemakai ID',
            'update_loginpemakai_id' => 'Update Loginpemakai ID',
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
