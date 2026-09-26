<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "terimamutasibarangdetail_t".
 *
 * @property int $terimamutasibarangdetail_id
 * @property int $terimamutasibarang_id
 * @property int $mutasibarangdetail_id
 * @property int $satuankecil_id
 * @property int $ruangan_id
 * @property int $barang_id
 * @property double $jmlmutasi
 * @property double $jmlterima
 * @property double $harganettoterima
 * @property double $hargajualterima
 * @property double $persendiscount
 * @property string $tglkadaluarsa
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
class TerimaMutasiBarangDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'terimamutasibarangdetail_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['terimamutasibarang_id', 'mutasibarangdetail_id', 'ruangan_id', 'barang_id', 'jmlterima'], 'required'],
            [['terimamutasibarang_id', 'mutasibarangdetail_id', 'satuankecil_id', 'ruangan_id', 'barang_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['terimamutasibarang_id', 'mutasibarangdetail_id', 'satuankecil_id', 'ruangan_id', 'barang_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['jmlmutasi', 'jmlterima', 'harganettoterima', 'hargajualterima', 'persendiscount'], 'number'],
            [['tglkadaluarsa', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'terimamutasibarangdetail_id' => 'Terimamutasibarangdetail ID',
            'terimamutasibarang_id' => 'Terimamutasibarang ID',
            'mutasibarangdetail_id' => 'Mutasibarangdetail ID',
            'satuankecil_id' => 'Satuankecil ID',
            'ruangan_id' => 'Ruangan ID',
            'barang_id' => 'Barang ID',
            'jmlmutasi' => 'Jmlmutasi',
            'jmlterima' => 'Jmlterima',
            'harganettoterima' => 'Harganettoterima',
            'hargajualterima' => 'Hargajualterima',
            'persendiscount' => 'Persendiscount',
            'tglkadaluarsa' => 'Tglkadaluarsa',
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
