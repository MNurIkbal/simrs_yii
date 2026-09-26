<?php

namespace app\modules\v1\models;

use Yii;


/**
 * This is the model class for table "pemusnahanobatdetail_t".
 *
 * @property int $pemusnahanobatdetail_id
 * @property int $pemusnahanobat_id
 * @property int $obatalkes_id
 * @property double $jumlah
 * @property string $tglkadaluarsa
 * @property string $nobatch
 * @property string $kondisibarang
 * @property double $harganetto
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
class PemusnahanObatDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pemusnahanobatdetail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pemusnahanobat_id', 'obatalkes_id', 'tglkadaluarsa', 'satuan_id'], 'required'],
            [['pemusnahanobat_id', 'obatalkes_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pemusnahanobat_id', 'obatalkes_id', 'satuan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['jumlah', 'harganetto'], 'number'],
            [['tglkadaluarsa', 'satuan_id', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['kondisibarang', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['nobatch'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pemusnahanobatdetail_id' => 'Pemusnahanobatdetail ID',
            'pemusnahanobat_id' => 'Pemusnahanobat ID',
            'obatalkes_id' => 'Obatalkes ID',
            'jumlah' => 'Jumlah',
            'tglkadaluarsa' => 'Tglkadaluarsa',
            'nobatch' => 'Nobatch',
            'kondisibarang' => 'Kondisibarang',
            'harganetto' => 'Harganetto',
            'satuan_id' => 'Satuan ID',
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
