<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pengangkatantphl_t".
 *
 * @property integer $pengangkatantphl_id
 * @property integer $pegawai_id
 * @property string $pengangkatantphl_noperjanjian
 * @property string $pengangkatantphl_tamat
 * @property string $pengangkatantphl_tugaspekerjaan
 * @property string $pengangkatantphl_nosk
 * @property string $pengangkatantphl_tglsk
 * @property string $pengangkatantphl_tamatsk
 * @property string $pengangkatantphl_noskterakhir
 * @property string $pengangkatantphl_keterangan
 * @property string $pimpinannama
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
 * @property PegawaiM[] $pegawaiMs
 * @property PegawaiM $pegawai
 */
class Pengangkatantphl extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pengangkatantphl_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pegawai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['pengangkatantphl_noperjanjian', 'pengangkatantphl_tamat', 'pengangkatantphl_tugaspekerjaan', 'pengangkatantphl_nosk', 'pengangkatantphl_tglsk', 'pengangkatantphl_tamatsk'], 'required'],
            [['pengangkatantphl_tamat', 'pengangkatantphl_tglsk', 'pengangkatantphl_tamatsk', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['pengangkatantphl_tugaspekerjaan', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['pengangkatantphl_noperjanjian', 'pengangkatantphl_nosk', 'pengangkatantphl_noskterakhir', 'pengangkatantphl_keterangan'], 'string', 'max' => 50],
            [['pimpinannama'], 'string', 'max' => 100],
            [['pegawai_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['pegawai_id' => 'pegawai_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pengangkatantphl_id' => 'Pengangkatantphl ID',
            'pegawai_id' => 'Pegawai ID',
            'pengangkatantphl_noperjanjian' => 'Pengangkatantphl Noperjanjian',
            'pengangkatantphl_tamat' => 'Pengangkatantphl Tamat',
            'pengangkatantphl_tugaspekerjaan' => 'Pengangkatantphl Tugaspekerjaan',
            'pengangkatantphl_nosk' => 'Pengangkatantphl Nosk',
            'pengangkatantphl_tglsk' => 'Pengangkatantphl Tglsk',
            'pengangkatantphl_tamatsk' => 'Pengangkatantphl Tamatsk',
            'pengangkatantphl_noskterakhir' => 'Pengangkatantphl Noskterakhir',
            'pengangkatantphl_keterangan' => 'Pengangkatantphl Keterangan',
            'pimpinannama' => 'Pimpinannama',
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
    public function getPegawaiMs()
    {
        return $this->hasMany(PegawaiM::className(), ['pengangkatantphl_id' => 'pengangkatantphl_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPegawai()
    {
        return $this->hasOne(PegawaiM::className(), ['pegawai_id' => 'pegawai_id']);
    }
}
