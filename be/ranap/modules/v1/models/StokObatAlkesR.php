<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-03-09 15:10:16
 * @Last Modified by:   afil
 * @Last Modified time: 2018-03-09 15:13:17
 * @Description: 
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "stokobatalkes_r".
 *
 * @property int $stokobatr_id
 * @property int $ruangan_id
 * @property int $obatalkes_id
 * @property int $qty_awal
 * @property int $qty_masuk
 * @property int $qty_keluar
 * @property int $qty_sisa
 * @property int $periodestokobat_id
 * @property int $qty_tersedia
 * @property int $qty_dipesan
 * @property int $lokasiobat_id
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
 * @property bool $is_periode
 */
class StokObatAlkesR extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stokobatalkes_r';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ruangan_id', 'obatalkes_id', 'qty_awal', 'qty_masuk', 'qty_tersedia', 'qty_dipesan'], 'required'],
            [['ruangan_id', 'obatalkes_id', 'qty_awal', 'qty_masuk', 'periodestokobat_id', 'qty_tersedia', 'qty_dipesan', 'lokasiobat_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruangan_id', 'obatalkes_id', 'qty_awal', 'qty_masuk', 'periodestokobat_id', 'qty_tersedia', 'qty_dipesan', 'lokasiobat_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active', 'is_periode'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'stokobatr_id' => 'Stokobatr ID',
            'ruangan_id' => 'Ruangan ID',
            'obatalkes_id' => 'Obatalkes ID',
            'qty_awal' => 'Qty Awal',
            'qty_masuk' => 'Qty Masuk',
            'qty_keluar' => 'Qty Keluar',
            'qty_sisa' => 'Qty Sisa',
            'periodestokobat_id' => 'Periodestokobat ID',
            'qty_tersedia' => 'Qty Tersedia',
            'qty_dipesan' => 'Qty Dipesan',
            'lokasiobat_id' => 'Lokasiobat ID',
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
            'is_periode' => 'Is Periode',
        ];
    }
}
