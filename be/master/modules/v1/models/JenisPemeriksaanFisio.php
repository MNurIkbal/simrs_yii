<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "jenispemeriksaanfisio_m".
 * 
 * @property int $jenispemeriksaanfisio_id
 * @property string $jenispemeriksaanfisio_kode
 * @property string $jenispemeriksaanfisio_nama
 * @property string $jenispemeriksaanfisio_namalainnya
 * @property int $kelompokpemeriksaanfisio_id
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
 * @property PemeriksaanfisioM[] $pemeriksaanfisioMs
 */
class JenisPemeriksaanFisio extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jenispemeriksaanfisio_m';
    }

    // validasi XSS
    protected $xssProtected = [
        'jenispemeriksaanfisio_kode',
        'jenispemeriksaanfisio_nama',
        'jenispemeriksaanfisio_namalain'
    ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenispemeriksaanfisio_kode', 'jenispemeriksaanfisio_nama', 'kelompokpemeriksaanfisio_id'], 'required'],
            [['kelompokpemeriksaanfisio_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['kelompokpemeriksaanfisio_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['jenispemeriksaanfisio_kode'], 'string', 'max' => 10],
            [['jenispemeriksaanfisio_nama', 'jenispemeriksaanfisio_namalain'], 'string', 'max' => 100],
            [['jenispemeriksaanfisio_kode'], 'chkKodeKelompok'],
            [['jenispemeriksaanfisio_nama'], 'chkNamaPemeriksaan'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jenispemeriksaanfisio_id' => 'Jenispemeriksaanfisio ID',
            'jenispemeriksaanfisio_kode' => 'Jenispemeriksaanfisio Kode',
            'jenispemeriksaanfisio_nama' => 'Jenispemeriksaanfisio Nama',
            'jenispemeriksaanfisio_namalain' => 'Jenispemeriksaanfisio Namalain',
            'kelompokpemeriksaanfisio_id' => 'Kelompokpemeriksaanfisio ID',
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
    public function getPemeriksaanFisio()
    {
        return $this->hasMany(PemeriksaanFisio::className(), ['jenispemeriksaanfisio_id' => 'jenispemeriksaanfisio_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKelompokPemeriksaanFisio()
    {
        return $this->hasOne(KelompokPemeriksaanFisio::className(), ['kelompokpemeriksaanfisio_id' => 'kelompokpemeriksaanfisio_id']);
    }

    // Extra fields
    public function extraFields()
    {
        // Return
        return [
            'kelompokpemeriksaanfisio_m' => function($item) {
                // Return
                return $item->kelompokPemeriksaanFisio;
            }
        ];
    }

    public function chkKodeKelompok()
    {
        $jenispemeriksaanfisio_kode = $this->jenispemeriksaanfisio_kode;
        $model = self::find()->where(['LOWER (jenispemeriksaanfisio_kode)'=>strtolower($this->jenispemeriksaanfisio_kode),'is_deleted'=>false])->one();
        if (!empty($model) && ($model->jenispemeriksaanfisio_id != $this->jenispemeriksaanfisio_id)){
            $this->addError("jenispemeriksaanfisio_kode","Kode Sudah Dipakai");
            return false;
        }
    
        return true;
    }

    public function chkNamaPemeriksaan()
    {
        $model = self::find()->where(['LOWER (jenispemeriksaanfisio_nama)' => strtolower($this->jenispemeriksaanfisio_nama), 'is_deleted' => false])->one();
        if (!empty($model) && ($model->jenispemeriksaanfisio_id != $this->jenispemeriksaanfisio_id)) {
            $this->addError("jenispemeriksaanfisio_nama", "Nama Sudah Dipakai");
            return false;
        }
        return true;
    }
}
