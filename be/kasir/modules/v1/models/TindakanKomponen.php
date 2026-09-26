<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "tindakankomponen_t".
 *
 * @property int $tindakankomponen_id
 * @property int $komponentarif_id
 * @property int $tindakanpelayanan_id
 * @property double $tarif_kompsatuan
 * @property double $tarif_tindakankomp
 * @property double $tarifcyto_tindakankomp
 * @property double $tarifpenyulit_komponen
 * @property double $subsidiasuransikomp
 * @property double $subsidipemerintahkomp
 * @property double $subsidirumahsakitkomp
 * @property double $iurbiayakomp
 * @property int $pembayaranjasa_id
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
class TindakanKomponen extends \Doco\components\DocoActiveRecord
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
