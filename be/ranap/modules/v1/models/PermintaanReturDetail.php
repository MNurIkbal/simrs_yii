<?php

/**
 * @Author: Sigit
 * @Date:   2018-08-09 16:59:05
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-08-09 19:28:18
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "permintaanreturdetail_t".
 *
 * @property int $permintaanreturdetail_id
 * @property int $permintaanretur_id
 * @property int $obatalkespasien_id
 * @property int $obatalkes_id
 * @property int $qty_retur
 * @property int $signa
 * @property double $harga_satuan
 * @property double $harga_jumlah
 * @property string $alasan
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
class PermintaanReturDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'permintaanreturdetail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['obatalkespasien_id', 'obatalkes_id', 'qty_retur', 'signa', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['permintaanretur_id', 'obatalkespasien_id', 'obatalkes_id', 'qty_retur', 'signa', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['harga_satuan', 'harga_jumlah'], 'number'],
            [['alasan', 'additional_data'], 'string'],
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
            'permintaanreturdetail_id' => 'Permintaanreturdetail ID',
            'permintaanretur_id' => 'Permintaanretur ID',
            'obatalkespasien_id' => 'Obatalkespasien ID',
            'obatalkes_id' => 'Obatalkes ID',
            'qty_retur' => 'Qty Retur',
            'signa' => 'Signa',
            'harga_satuan' => 'Harga Satuan',
            'harga_jumlah' => 'Harga Jumlah',
            'alasan' => 'Alasan',
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
