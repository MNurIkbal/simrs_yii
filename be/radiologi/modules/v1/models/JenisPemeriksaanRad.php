<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "jenispemeriksaanrad_m".
 *
 * @property int $jenispemeriksaanrad_id
 * @property string $jenispemeriksaanrad_kode
 * @property string $jenispemeriksaanrad_nama
 * @property string $jenispemeriksaanrad_namalain
 * @property int $kelompokpemeriksaanrad_id
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
 * @property PemeriksaanradM[] $pemeriksaanradMs
 */
class JenisPemeriksaanRad extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jenispemeriksaanrad_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenispemeriksaanrad_kode', 'jenispemeriksaanrad_nama', 'kelompokpemeriksaanrad_id'], 'required'],
            [['kelompokpemeriksaanrad_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['kelompokpemeriksaanrad_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['jenispemeriksaanrad_kode'], 'string', 'max' => 10],
            [['jenispemeriksaanrad_nama', 'jenispemeriksaanrad_namalain'], 'string', 'max' => 100],
            [['jenispemeriksaanrad_kode'], 'chkKodeKelompok'],
            [['jenispemeriksaanrad_nama'], 'chkNamaPemeriksaan'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jenispemeriksaanrad_id' => 'Jenispemeriksaanrad ID',
            'jenispemeriksaanrad_kode' => 'Jenispemeriksaanrad Kode',
            'jenispemeriksaanrad_nama' => 'Jenispemeriksaanrad Nama',
            'jenispemeriksaanrad_namalain' => 'Jenispemeriksaanrad Namalain',
            'kelompokpemeriksaanrad_id' => 'Kelompokpemeriksaanrad ID',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPemeriksaanRad()
    {
        return $this->hasMany(PemeriksaanRad::className(), ['jenispemeriksaanrad_id' => 'jenispemeriksaanrad_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKelompokPemeriksaanRad()
    {
        return $this->hasOne(KelompokPemeriksaanRad::className(), ['kelompokpemeriksaanrad_id' => 'kelompokpemeriksaanrad_id']);
    }

    // Extra fields
    public function extraFields()
    {
        // Return
        return [
            'kelompokpemeriksaanrad_m' => function($item) {
                // Return
                return $item->kelompokPemeriksaanRad;
            }
        ];
    }

    public function chkKodeKelompok()
    {
        $jenispemeriksaanrad_kode = $this->jenispemeriksaanrad_kode;
        $model = self::find()->where(['LOWER (jenispemeriksaanrad_kode)'=>strtolower($this->jenispemeriksaanrad_kode),'is_deleted'=>false])->one();
        if (!empty($model) && ($model->jenispemeriksaanrad_id != $this->jenispemeriksaanrad_id)){
            $this->addError("jenispemeriksaanrad_kode","Kode Sudah Dipakai");
            return false;
        }
    
        return true;
    }

    public function chkNamaPemeriksaan()
    {
        $model = self::find()->where(['LOWER (jenispemeriksaanrad_nama)' => strtolower($this->jenispemeriksaanrad_nama), 'is_deleted' => false])->one();
        if (!empty($model) && ($model->jenispemeriksaanrad_id != $this->jenispemeriksaanrad_id)) {
            $this->addError("jenispemeriksaanrad_nama", "Nama Sudah Dipakai");
            return false;
        }
        return true;
    }
}
