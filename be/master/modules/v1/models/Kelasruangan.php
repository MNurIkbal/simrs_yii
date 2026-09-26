<?php

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\models\Ruangan;
/**
 * This is the model class for table "kelasruangan_mp".
 *
 * @property int $ruangan_id
 * @property int $kelaspelayanan_id
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
class Kelasruangan extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kelasruangan_mp';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ruangan_id', 'kelaspelayanan_id'], 'required'],
            [['ruangan_id', 'kelaspelayanan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [[/*'ruangan_id', */'kelaspelayanan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['is_deleted', 'deleted_by', 'deleted_date', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            // [['ruangan_id', 'kelaspelayanan_id'], 'unique', 'targetAttribute' => ['ruangan_id', 'kelaspelayanan_id'], 
            //     'message' => 'Kelas Pelayanan {value} sudah ada.'],
            [['ruangan_id'/*, 'kelaspelayanan_id'*/], 'customUnique'],
        ];
    }

    public function customUnique()
    {
        $model = self::find()
            ->joinWith(['ruangan'])
            ->where([
                'kelasruangan_mp.kelaspelayanan_id' => $this->kelaspelayanan_id,
                'kelasruangan_mp.is_deleted' => false,
            ])->andWhere(['IN', 'kelasruangan_mp.ruangan_id', $this->ruangan_id])->all();

        $idx = [];
        foreach ($model as $key => $value) {
            $idx[] = $value->ruangan->ruangan_nama;
        }

        $ruangan = implode(',', $idx);
        if($model) {
            $message = 'Ruangan '.$ruangan.' sudah termaping.';
            $this->addError('ruangan_id', $message);
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ruangan_id' => 'Ruangan ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
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

    public function getKelasPelayanan()
    {
        return $this->hasMany(KelasPelayanan::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }

    public function getRuangan()
    {
        return $this->hasOne(Ruangan::className(), ['ruangan_id' => 'ruangan_id']);
    }

    public static function primaryKey()
    {
        return ['kelaspelayanan_id', 'ruangan_id'];
    }

    public function beforeValidate()
    {
        if(parent::beforeValidate()) {
            return true;
        }
        return false;
    }
}
