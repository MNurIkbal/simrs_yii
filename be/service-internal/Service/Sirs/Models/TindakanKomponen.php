<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;

class TindakanKomponen extends \Integrasi\Components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tindakankomponen_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['komponentarif_id', 'tindakanpelayanan_id'], 'required'],
            [['komponentarif_id', 'tindakanpelayanan_id', 'pembayaranjasa_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['komponentarif_id', 'tindakanpelayanan_id', 'pembayaranjasa_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tarif_kompsatuan', 'tarif_tindakankomp', 'tarifcyto_tindakankomp', 'tarifpenyulit_komponen', 'subsidiasuransikomp', 'subsidipemerintahkomp', 'subsidirumahsakitkomp', 'iurbiayakomp'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tindakankomponen_id' => 'Tindakankomponen ID',
            'komponentarif_id' => 'Komponentarif ID',
            'tindakanpelayanan_id' => 'Tindakanpelayanan ID',
            'tarif_kompsatuan' => 'Tarif Kompsatuan',
            'tarif_tindakankomp' => 'Tarif Tindakankomp',
            'tarifcyto_tindakankomp' => 'Tarifcyto Tindakankomp',
            'tarifpenyulit_komponen' => 'Tarif Penyulit Tindakankomp',
            'subsidiasuransikomp' => 'Subsidiasuransikomp',
            'subsidipemerintahkomp' => 'Subsidipemerintahkomp',
            'subsidirumahsakitkomp' => 'Subsidirumahsakitkomp',
            'iurbiayakomp' => 'Iurbiayakomp',
            'pembayaranjasa_id' => 'Pembayaranjasa ID',
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
