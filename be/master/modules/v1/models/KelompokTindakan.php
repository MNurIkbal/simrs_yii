<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kelompoktindakan_m".
 *
 * @property int $kelompoktindakan_id
 * @property string $kelompoktindakan_nama
 * @property string $kelompoktindakan_namalainnya
 * @property double $kelompoktindakan_persencyto
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
 * @property double $kelompoktindakan_persendiskon
 * @property string $kelompoktindakan_kode
 * @property string $catatan
 *
 * @property DaftartindakanM[] $daftartindakanMs
 * @property KomponenjasaM[] $komponenjasaMs
 */
class KelompokTindakan extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kelompoktindakan_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kelompoktindakan_nama', 'kelompoktindakan_kode'], 'required'],
            [['kelompoktindakan_persencyto', 'kelompoktindakan_persendiskon'], 'number'],
            [['additional_data', 'catatan'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kelompoktindakan_nama', 'kelompoktindakan_namalainnya'], 'string', 'max' => 50],
            [['kelompoktindakan_kode'], 'string', 'max' => 100],
            [['kelompoktindakan_kode'], 'chkKode'],
            [['kelompoktindakan_nama'], 'chkNama'],
            [['kelompoktindakan_namalainnya'], 'chkNamaLainnya'],
            // [['kelompoktindakan_kode'], 'unique'],
        ];
    }

    public function chkKode($params, $attributes)
    {
        $kelompoktindakan_kode = $this->kelompoktindakan_kode;
        $rest = substr($this->kelompoktindakan_kode, 0, 1);
        if ($rest == " ") {
            $this->addError("kelompoktindakan_kode", "Kode Kelompok mengandung spasi di awal kata");
            return false;
        }
        else {
            $model = self::find()->where(['LOWER (kelompoktindakan_kode)' => strtolower($this->kelompoktindakan_kode), 'is_deleted' => false])->one();
            if(!empty($model) && $model->kelompoktindakan_id != $this->kelompoktindakan_id ){
                $this->addError("kelompoktindakan_kode","Kode Kelompok Sudah Dipakai");
                return false;
            }
        }
        
        return true;
    }

    public function chkNama($params, $attributes)
    {
        $kelompoktindakan_nama = $this->kelompoktindakan_nama;
        $rest = substr($this->kelompoktindakan_nama, 0, 1);
        if ($rest == " ") {
            $this->addError("kelompoktindakan_nama", "Nama Kelompok mengandung spasi di awal kata");
            return false;
        }
        else {
            $model = self::find()->where(['LOWER (kelompoktindakan_nama)' => strtolower($this->kelompoktindakan_nama), 'is_deleted' => false])->one();
            if(!empty($model) && $model->kelompoktindakan_id != $this->kelompoktindakan_id ){
                $this->addError("kelompoktindakan_nama","Nama Kelompok Sudah Dipakai");
                return false;
            }
        }
    
        return true;
    }

    public function chkNamaLainnya($params, $attributes)
    {
        $rest = substr($this->kelompoktindakan_namalainnya, 0, 1);
        if ($rest == " ") {
            $this->addError("kelompoktindakan_namalainnya", "Nama Lainnya mengandung spasi di awal kata");
            return false;
        }
    
        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kelompoktindakan_id' => 'Kelompoktindakan ID',
            'kelompoktindakan_nama' => 'Kelompoktindakan Nama',
            'kelompoktindakan_namalainnya' => 'Kelompoktindakan Namalainnya',
            'kelompoktindakan_persencyto' => 'Kelompoktindakan Persencyto',
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
            'kelompoktindakan_persendiskon' => 'Kelompoktindakan Persendiskon',
            'kelompoktindakan_kode' => 'Kelompoktindakan Kode',
            'catatan' => 'Catatan',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDaftartindakanMs()
    {
        return $this->hasMany(DaftartindakanM::className(), ['kelompoktindakan_id' => 'kelompoktindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKomponenjasaMs()
    {
        return $this->hasMany(KomponenjasaM::className(), ['kelompoktindakan_id' => 'kelompoktindakan_id']);
    }
}
