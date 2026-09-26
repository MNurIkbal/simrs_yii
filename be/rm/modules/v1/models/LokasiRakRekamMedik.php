<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "lokasirak_m".
 *
 * @property int $lokasirak_id
 * @property string $lokasirak_nama
 * @property string $lokasirak_namalainnya
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
 *
 * @property DokrekammedisM[] $dokrekammedisMs
 */
class LokasiRakRekamMedik extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lokasirak_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lokasirak_nama'], 'required'],
            [['lokasirak_nama'], 'chkKode'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['lokasirak_nama', 'lokasirak_namalainnya'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'lokasirak_id' => 'Lokasirak ID',
            'lokasirak_nama' => 'Lokasirak Nama',
            'lokasirak_namalainnya' => 'Lokasirak Namalainnya',
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

    public function chkKode($params, $attributes)
    {
        $lokasirak_nama = $this->lokasirak_nama;
        $rest = substr($this->lokasirak_nama, 0, 1);
        if ($rest == " ") {
            $this->addError("lokasirak_nama", "Nama Rak mengandung spasi di awal kata");
            return false;
        } else {
            $model = self::find()->where(['LOWER (lokasirak_nama)' => strtolower($this->lokasirak_nama), 'is_deleted' => false])->one();
            if (!empty($model) && $model->lokasirak_id != $this->lokasirak_id) {
                $this->addError("lokasirak_nama", "Rak Sudah Dipakai");
                return false;
            }
        }

        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDokrekammedis()
    {
        return $this->hasMany(Dokrekammedis::className(), ['lokasirak_id' => 'lokasirak_id']);
    }
}
