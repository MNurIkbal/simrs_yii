<?php

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\models\DaftarTindakanM;

/**
 * This is the model class for table "pemeriksaanrad_m".
 *
 * @property int $pemeriksaanradiologi_id
 * @property int $daftartindakan_id
 * @property int $jenispemeriksaanrad_id
 * @property string $pemeriksaanrad_kode
 * @property string $pemeriksaanrad_nama
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
 * @property PemeriksaanalatradmapMp[] $pemeriksaanalatradmapMps
 * @property PemeriksaanalatradiologiM[] $pemeriksaanalatradiologis
 * @property DaftartindakanM $daftartindakan
 * @property JenispemeriksaanradM $jenispemeriksaanrad
 * @property ReferensihasilradM[] $referensihasilradMs
 */
class PemeriksaanRad extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pemeriksaanrad_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['daftartindakan_id', 'jenispemeriksaanrad_id', 'kelompokpemeriksaanrad_id'], 'required'],
            [['daftartindakan_id', 'jenispemeriksaanrad_id', 'kelompokpemeriksaanrad_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['daftartindakan_id', 'jenispemeriksaanrad_id', 'kelompokpemeriksaanrad_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['pemeriksaanradiologi_id', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['pemeriksaanrad_kode'], 'string', 'max' => 10],
            [['pemeriksaanrad_nama'], 'string', 'max' => 500],
            // [['pemeriksaanrad_kode'], 'unique'],
            [['daftartindakan_id'], 'exist', 'skipOnError' => true, 'targetClass' => DaftarTindakanM::className(), 'targetAttribute' => ['daftartindakan_id' => 'daftartindakan_id']],
            [['jenispemeriksaanrad_id'], 'exist', 'skipOnError' => true, 'targetClass' => JenisPemeriksaanRad::className(), 'targetAttribute' => ['jenispemeriksaanrad_id' => 'jenispemeriksaanrad_id']],
            [['pemeriksaanrad_kode'], 'chkKodeKelompok'],
            [['jenispemeriksaanrad_id'], 'checkJenis'],
            [['daftartindakan_id'], 'chkNamaPemeriksaan'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pemeriksaanradiologi_id' => 'Pemeriksaanradiologi ID',
            'daftartindakan_id' => 'Daftartindakan ID',
            'jenispemeriksaanrad_id' => 'Jenispemeriksaanrad ID',
            'pemeriksaanrad_kode' => 'Pemeriksaanrad Kode',
            'pemeriksaanrad_nama' => 'Pemeriksaanrad Nama',
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
    public function getDaftarTindakan()
    {
        return $this->hasOne(DaftartindakanM::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKelompokPemeriksaanRad()
    {
        return $this->hasOne(KelompokPemeriksaanRad::className(), ['kelompokpemeriksaanrad_id' => 'kelompokpemeriksaanrad_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJenisPemeriksaanRad()
    {
        return $this->hasOne(JenisPemeriksaanRad::className(), ['jenispemeriksaanrad_id' => 'jenispemeriksaanrad_id']);
    }

    // Extra fields
    public function extraFields()
    {
        // Return
        return [
            'jenispemeriksaanrad_m' => function($item) {
                // Return
                return $item->jenisPemeriksaanRad;
            },
            'kelompokpemeriksaanrad_m' => function($item) {
                // Return
                return $item->kelompokPemeriksaanRad;
            },
            'daftartindakan_m' => function($item) {
                // Return
                return $item->daftarTindakan;
            }
        ];
    }

    public function chkKodeKelompok()
    {
        $pemeriksaanrad_kode = $this->pemeriksaanrad_kode;
        $model = self::find()->where(['LOWER (pemeriksaanrad_kode)'=>strtolower($this->pemeriksaanrad_kode),'is_deleted'=>false])->one();
        if (!empty($model) && ($model->pemeriksaanradiologi_id != $this->pemeriksaanradiologi_id)){
            $this->addError("pemeriksaanrad_kode","Kode Sudah Dipakai");
            return false;
        }
    
        return true;
    }

    public function chkNamaPemeriksaan()
    {
        $model = self::find()->where(['daftartindakan_id' => $this->daftartindakan_id, 'is_deleted' => false])->one();
        if (!empty($model) && ($model->pemeriksaanradiologi_id != $this->pemeriksaanradiologi_id)) {
            $this->addError("daftartindakan_id", "Nama Pemeriksaan Sudah Dipakai");
            return false;
        }
        return true;
    }

    public function checkJenis()
    {
        $model = self::find()->where(['jenispemeriksaanrad_id' => $this->jenispemeriksaanrad_id, 'is_deleted' => false])->one();
        if (!empty($model) && ($model->pemeriksaanradiologi_id != $this->pemeriksaanradiologi_id)) {
            $this->addError("jenispemeriksaanrad_id", "Jenis Pemeriksaan Sudah Dipakai");
            return false;
        }
        return true;
    }
}
