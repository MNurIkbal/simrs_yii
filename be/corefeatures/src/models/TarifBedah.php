<?php

namespace SirsCore\models;

use Yii;

class TarifBedah extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tarifbedah_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kegiatanoperasi_id', 'kelaspelayanan_id', 'perdatarif_id', 'persen_cyto', 'tarif'], 'required'],
            [['kegiatanoperasi_id', 'kelaspelayanan_id', 'perdatarif_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tarifbedah_id' => 'Tarif Bedah ID',
            'kegiatanoperasi_id' => 'Kegiatan Operasi ID',
            'kelaspelayanan_id' => 'Kelas Pelayanan ID',
            'perdatarif_id' => 'Perda Tarif ID',
            'persen_cyto' => 'Persen Cyto',
            'tarif' => 'Tarif',
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

    public function getOperasi()
    {
        return $this->hasOne(Operasi::className(), ['kegiatanoperasi_id' => 'kegiatanoperasi_id']);
    }
}