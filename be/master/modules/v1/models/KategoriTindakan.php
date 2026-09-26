<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kategoritindakan_m".
 *
 * @property int $kategoritindakan_id
 * @property string $kategoritindakan_nama
 * @property string $kategoritindakan_namalainnya
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
 * @property string $kategori_kode
 * @property string $catatan
 *
 * @property DaftartindakanM[] $daftartindakanMs
 */
class KategoriTindakan extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kategoritindakan_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['additional_data', 'catatan'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kategoritindakan_nama', 'kategoritindakan_namalainnya'], 'string', 'max' => 150],
            [['kategori_kode'], 'string', 'max' => 100],
            [['kategori_kode'], 'chkKode'],
            [['kategoritindakan_nama'], 'chkNama'],
            [['kategoritindakan_namalainnya'], 'chkNamaLainnya'],
            // [['kategori_kode'], 'unique'],
        ];
    }

    public function chkKode($params, $attributes)
    {
        $kategori_kode = $this->kategori_kode;
        $rest = substr($this->kategori_kode, 0, 1);
        if ($rest == " ") {
            $this->addError("kategori_kode", "Kode Kategori mengandung spasi di awal kata");
            return false;
        }
        else {
            $model = self::find()->where(['LOWER (kategori_kode)' => strtolower($this->kategori_kode), 'is_deleted' => false])->one();
            if(!empty($model) && $model->kategoritindakan_id != $this->kategoritindakan_id ){
                $this->addError("kategori_kode","Kode Kategori Sudah Dipakai");
                return false;
            }
        }
        
        return true;
    }

    public function chkNama($params, $attributes)
    {
        $kategoritindakan_nama = $this->kategoritindakan_nama;
        $rest = substr($this->kategoritindakan_nama, 0, 1);
        if ($rest == " ") {
            $this->addError("kategoritindakan_nama", "Nama Kategori mengandung spasi di awal kata");
            return false;
        }
        else {
            $model = self::find()->where(['LOWER (kategoritindakan_nama)' => strtolower($this->kategoritindakan_nama), 'is_deleted' => false])->one();
            if(!empty($model) && $model->kategoritindakan_id != $this->kategoritindakan_id ){
                $this->addError("kategoritindakan_nama","Nama Kategori Sudah Dipakai");
                return false;
            }
        }
    
        return true;
    }

    public function chkNamaLainnya($params, $attributes)
    {
        $rest = substr($this->kategoritindakan_namalainnya, 0, 1);
        if ($rest == " ") {
            $this->addError("kategoritindakan_namalainnya", "Nama Lainnya mengandung spasi di awal kata");
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
            'kategoritindakan_id' => 'kategoritindakan ID',
            'kategoritindakan_nama' => 'kategoritindakan Nama',
            'kategoritindakan_namalainnya' => 'kategoritindakan Namalainnya',
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
            'kategori_kode' => 'Kategori Kode',
            'catatan' => 'Catatan',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDaftartindakan()
    {
        return $this->hasMany(Daftartindakan::className(), ['kategoritindakan_id' => 'kategoritindakan_id']);
    }

    public function extraFields()
    {
        return [
            'daftartindakan_m' => function($item){
                return $item->daftartindakan;
            }
        ];
    }
}
