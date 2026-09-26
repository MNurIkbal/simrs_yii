<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-03-06 10:16:00
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2018-07-11 16:18:13
 * @Description: 
 */

namespace app\modules\ranap\models;

use Yii;

class TindakankomponenForm extends \yii\base\Model
{
    public $tindakankomponen_id;
    public $komponentarif_id;
    public $tindakanpelayanan_id;
    public $tarif_kompsatuan;
    public $tarif_tindakankomp;
    public $tarifcyto_tindakankomp;
    public $subsidiasuransikomp;
    public $subsidipemerintahkomp;
    public $subsidirumahsakitkomp;
    public $iurbiayakomp;
    public $pembayaranjasa_id;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tindakankomponen_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['komponentarif_id', 'tindakanpelayanan_id', 'tarif_kompsatuan', 'tarif_tindakankomp', 'tarifcyto_tindakankomp', 'subsidiasuransikomp', 'subsidipemerintahkomp', 'subsidirumahsakitkomp', 'iurbiayakomp'], 'required'],
            [['komponentarif_id', 'tindakanpelayanan_id', 'pembayaranjasa_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['komponentarif_id', 'tindakanpelayanan_id', 'pembayaranjasa_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tarif_kompsatuan', 'tarif_tindakankomp', 'tarifcyto_tindakankomp', 'subsidiasuransikomp', 'subsidipemerintahkomp', 'subsidirumahsakitkomp', 'iurbiayakomp'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
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
