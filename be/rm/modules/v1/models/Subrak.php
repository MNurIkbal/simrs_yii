<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "subrak_m".
 *
 * @property int $subrak_id
 * @property string $subrak_nama
 * @property string $subrak_namalainnya
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
 * @property int $lokasirak_id
 *
 * @property DokrekammedisM[] $dokrekammedisMs
 * @property LokasirakM $lokasirak
 */
class SubRak extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'subrak_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subrak_nama'], 'required'],
            [['subrak_nama'], 'chkKode'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'lokasirak_id'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'lokasirak_id'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['subrak_nama', 'subrak_namalainnya'], 'string', 'max' => 30],
            [['lokasirak_id'], 'exist', 'skipOnError' => true, 'targetClass' => LokasiRakRekamMedik::className(), 'targetAttribute' => ['lokasirak_id' => 'lokasirak_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'subrak_id' => 'Subrak ID',
            'subrak_nama' => 'Subrak Nama',
            'subrak_namalainnya' => 'Subrak Namalainnya',
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
            'lokasirak_id' => 'Lokasirak ID',
        ];
    }

    public function chkKode($params, $attributes)
    {
        $subrak_nama = $this->subrak_nama;
        $rest = substr($this->subrak_nama, 0, 1);
        if ($rest == " ") {
            $this->addError("subrak_nama", "Subrak mengandung spasi di awal kata");
            return false;
        } else {
            $model = self::find()->where(['LOWER (subrak_nama)' => strtolower($this->subrak_nama), 'is_deleted' => false])->one();
            if (!empty($model) && $model->subrak_id != $this->subrak_id) {
                $this->addError("subrak_nama", "Subrak Sudah Dipakai");
                return false;
            }
        }

        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDokrekammedisMs()
    {
        return $this->hasMany(DokrekammedisM::className(), ['subrak_id' => 'subrak_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLokasirak()
    {
        return $this->hasOne(LokasiRakRekamMedik::className(), ['lokasirak_id' => 'lokasirak_id']);
    }

    public function extraFields()
    {
        return [
            'lokasirak_m' => function($item){
                return $item->lokasirak;
            }
        ];
    }
}
