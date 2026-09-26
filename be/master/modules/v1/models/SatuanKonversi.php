<?php

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\models\SatuanUnit;
/**
 * This is the model class for table "satuankonversi_m".
 *
 * @property int $satuankonversi_id
 * @property int $satuanbesar_id
 * @property int $satuankecil_id
 * @property double $nilai_konversi
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
 * @property bool $is_generik
 */
class SatuanKonversi extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    
    public $obatalkes_nama;
    public static function tableName()
    {
        return 'satuankonversi_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['obatalkes_id', 'satuanbesar_id', 'satuankecil_id', 'nilai_konversi'], 'required'],
            [['satuanbesar_id', 'satuankecil_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['satuanbesar_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['nilai_konversi'], 'number'],
            [['additional_data'], 'string'],
            [['satuanbesar_id','satuankecil_id','nilai_konversi', 'obatalkes_id','created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['is_deleted'], 'default', 'value' => false],
            [['satuanbesar_id'], 'checkUnique'],
            [['nilai_konversi'], 'number', 'min' => 1],
        ];
    }

    public function checkUnique()
    {
        $satuanbesar_id = $this->satuanbesar_id;
        $model = self::find()->where([
            'satuanbesar_id' => $satuanbesar_id,
            'satuankecil_id' => $this->satuankecil_id,
            'obatalkes_id' => $this->obatalkes_id
        ])->one();
        if (!empty($model) && $model->satuankonversi_id != $this->satuankonversi_id) {
            $this->addError('satuanbesar_id', 'Satuan besar sudah di pakai pada obat ini');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'satuankonversi_id' => 'Satuankonversi ID',
            'satuanbesar_id' => 'Satuanbesar ID',
            'satuankecil_id' => 'Satuankecil ID',
            'nilai_konversi' => 'Nilai Konversi',
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

    public function getSatuanbesar()
    {
        return $this->hasOne(SatuanUnit::className(), ['satuanunit_id' => 'satuanbesar_id']);
    }

    public function getSatuankecil()
    {
        return $this->hasOne(SatuanUnit::className(), ['satuanunit_id' => 'satuankecil_id']);
    }
}
