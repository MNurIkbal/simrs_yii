<?php

namespace app\modules\master\models;

use Yii;
use app\modules\master\models\SatuanUnit;

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
class SatuanKonversiForm extends \yii\base\Model
{
    /**
     * @inheritdoc
     */
    
    public $obatalkes_nama;
    public $obatalkes_id;
    public $satuankonversi_id;
    public $satuanbesar_id;
    public $satuankecil_id;
    public $satuankecil_nama;
    public $nilai_konversi;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;
    public $is_generik;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['obatalkes_id', 'satuanbesar_id', 'nilai_konversi'], 'required', 'message'=>'{attribute} Tidak boleh kosong'],
            [['satuanbesar_id', 'satuankecil_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['satuankonversi_id', 'satuanbesar_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['nilai_konversi'], 'double'],
            [['additional_data'], 'string'],
            [['satuanbesar_id','satuankecil_id','nilai_konversi','obatalkes_id','created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['nilai_konversi'], 'double', 'min' => 0.0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'satuankonversi_id' => 'Satuankonversi ID',
            'obatalkes_id' => 'Nama Obat Alkes',
            'satuanbesar_id' => 'Satuan Besar',
            'satuankecil_id' => 'Satuan Terkecil / Penyimpanan',
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
