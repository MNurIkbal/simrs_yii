<?php

namespace app\modules\v1\models;

use Yii;

class PemeriksaanLab extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pemeriksaanlab_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenispemeriksaanlab_id', 'daftartindakan_id', 'kelompokpemeriksaanlab_id'], 'required'],
            [['jenispemeriksaanlab_id', 'daftartindakan_id', 'kelompokpemeriksaanlab_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['jenispemeriksaanlab_id', 'daftartindakan_id', 'kelompokpemeriksaanlab_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['pemeriksaanlab_kode'], 'string', 'max' => 10],
            [['pemeriksaanlab_nama'], 'string', 'max' => 500],
            [['daftartindakan_id'], 'exist', 'skipOnError' => true, 'targetClass' => DaftartindakanM::className(), 'targetAttribute' => ['daftartindakan_id' => 'daftartindakan_id']],
            [['jenispemeriksaanlab_id'], 'exist', 'skipOnError' => true, 'targetClass' => JenispemeriksaanlabM::className(), 'targetAttribute' => ['jenispemeriksaanlab_id' => 'jenispemeriksaanlab_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pemeriksaanlab_id' => 'Pemeriksaanlab ID',
            'jenispemeriksaanlab_id' => 'Jenispemeriksaanlab ID',
            'daftartindakan_id' => 'Daftartindakan ID',
            'pemeriksaanlab_kode' => 'Pemeriksaanlab Kode',
            'pemeriksaanlab_nama' => 'Pemeriksaanlab Nama',
            'kelompokpemeriksaanlab_id' => 'Kelompokpemeriksaanlab ID',
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

    public function getDaftarTindakan()
    {
        return $this->hasOne(Daftartindakan::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }
}
