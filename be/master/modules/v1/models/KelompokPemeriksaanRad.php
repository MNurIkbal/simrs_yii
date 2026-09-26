<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kelompokpemeriksaanrad_m".
 *
 * @property int $kelompokpemeriksaanrad_id
 * @property string $kode_kelompok
 * @property string $nama_kelompok
 * @property string $keterangan_kelompok
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
class KelompokPemeriksaanRad extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kelompokpemeriksaanrad_m';
    }

    // validasi XSS form
    protected $xssProtected = [
        'kode_kelompok',
        'nama_kelompok',
        'keterangan_kelompok'
    ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kode_kelompok', 'nama_kelompok'], 'required'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kode_kelompok'], 'string', 'max' => 25],
            [['nama_kelompok', 'keterangan_kelompok'], 'string', 'max' => 255],
            [['kelompokpemeriksaanrad_id'],'safe'],
            [['kode_kelompok'], 'chkKodeKelompok'],
            [['nama_kelompok'], 'chkNamaKelompok'],
            [['nama_kelompok'], 'chkNamaKelompok'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kelompokpemeriksaanrad_id' => 'Kelompokpemeriksaanrad ID',
            'kode_kelompok' => 'Kode Kelompok',
            'nama_kelompok' => 'Nama Kelompok',
            'keterangan_kelompok' => 'Keterangan Kelompok',
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

    public function chkKodeKelompok()
    {
        $kode_kelompok = $this->kode_kelompok;
        $model = self::find()->where(['LOWER (kode_kelompok)'=>strtolower($this->kode_kelompok),'is_deleted'=>false])->one();
        if(!empty($model) && $model->kelompokpemeriksaanrad_id != $this->kelompokpemeriksaanrad_id ){
            $this->addError("kode_kelompok","Kode Sudah Dipakai");
            return false;
        }
    
        return true;
    }

    public function chkNamaKelompok()
    {
        $rest = substr($this->nama_kelompok, 0, 1);    // returns "f"
        if ($rest == " ") {
            $this->addError("nama_kelompok", "kelompok Pemeriksaan mengandung spasi di awal kata");
            return false;
        } else {
            $model = self::find()->where(['LOWER (nama_kelompok)' => strtolower($this->nama_kelompok), 'is_deleted' => false])->one();
            if (!empty($model) && $model->kelompokpemeriksaanrad_id != $this->kelompokpemeriksaanrad_id) {
                $this->addError("nama_kelompok", "Nama Sudah Dipakai");
                return false;
            }
        }
        
        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJenisPemeriksaanRad()
    {
        return $this->hasMany(JenisPemeriksaanRad::className(), ['kelompokpemeriksaanrad_id' => 'kelompokpemeriksaanrad_id']);
    }
}
