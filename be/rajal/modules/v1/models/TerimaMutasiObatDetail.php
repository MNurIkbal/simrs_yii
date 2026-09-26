<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "terimamutasiobatdetail_t".
 *
 * @property int $terimamutasiobatdetail_id
 * @property int $terimamutasiobat_id
 * @property int $mutasiobatdetail_id
 * @property int $satuankecil_id
 * @property int $ruangan_id
 * @property int $obatalkes_id
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
class TerimaMutasiObatDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'terimamutasiobatdetail_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['terimamutasiobat_id', 'mutasiobatdetail_id', 'satuankecil_id', 'ruangan_id', 'obatalkes_id', 'jmlterima'], 'required'],
            [['terimamutasiobat_id', 'mutasiobatdetail_id', 'satuankecil_id', 'ruangan_id', 'obatalkes_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['terimamutasiobat_id', 'mutasiobatdetail_id', 'satuankecil_id', 'ruangan_id', 'obatalkes_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
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
            'terimamutasiobatdetail_id' => 'Terimamutasiobatdetail ID',
            'terimamutasiobat_id' => 'Terimamutasiobat ID',
            'mutasiobatdetail_id' => 'Mutasiobatdetail ID',
            'satuankecil_id' => 'Satuankecil ID',
            'ruangan_id' => 'Ruangan ID',
            'obatalkes_id' => 'Obatalkes ID',
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
