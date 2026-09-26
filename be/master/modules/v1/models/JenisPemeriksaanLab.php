<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-24 10:19:33
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-04-24 14:46:45
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "jenispemeriksaanlab_m".
 *
 * @property int $jenispemeriksaanlab_id
 * @property string $jenispemeriksaanlab_kode
 * @property string $jenispemeriksaanlab_nama
 * @property string $jenispemeriksaanlab_namalainnya
 * @property int $kelompokpemeriksaanlab_id
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
 * @property PemeriksaanlabM[] $pemeriksaanlabMs
 */
class JenisPemeriksaanLab extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jenispemeriksaanlab_m';
    }

    /**
     * {@inheritdoc}
     */

    // Validasi XSS form
    protected $xssProtected = [
        'jenispemeriksaanlab_kode',
        'jenispemeriksaanlab_nama',
        'jenispemeriksaanlab_namalainnya',
        'additional_data'
    ];

    public function rules()
    {
        return [
            [['jenispemeriksaanlab_kode', 'jenispemeriksaanlab_nama', 'kelompokpemeriksaanlab_id'], 'required'],
            [['kelompokpemeriksaanlab_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['kelompokpemeriksaanlab_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['jenispemeriksaanlab_kode'], 'string', 'max' => 10],
            [['jenispemeriksaanlab_nama', 'jenispemeriksaanlab_namalainnya'], 'string', 'max' => 30],
            [['jenispemeriksaanlab_kode'], 'chkKodeKelompok'],
            [['jenispemeriksaanlab_nama'], 'chkNamaPemeriksaan'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jenispemeriksaanlab_id' => 'Jenispemeriksaanlab ID',
            'jenispemeriksaanlab_kode' => 'Jenispemeriksaanlab Kode',
            'jenispemeriksaanlab_nama' => 'Jenispemeriksaanlab Nama',
            'jenispemeriksaanlab_namalainnya' => 'Jenispemeriksaanlab Namalainnya',
            'kelompokpemeriksaanlab_id' => 'Kelompokpemeriksaanlab ID',
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
    public function getPemeriksaanLab()
    {
        return $this->hasMany(PemeriksaanLab::className(), ['jenispemeriksaanlab_id' => 'jenispemeriksaanlab_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKelompokPemeriksaanLab()
    {
        return $this->hasOne(KelompokPemeriksaanLab::className(), ['kelompokpemeriksaanlab_id' => 'kelompokpemeriksaanlab_id']);
    }

    // Extra fields
    public function extraFields()
    {
        // Return
        return [
            'kelompokpemeriksaanlab_m' => function($item) {
                // Return
                return $item->kelompokPemeriksaanLab;
            }
        ];
    }

    public function chkKodeKelompok()
    {
        $jenispemeriksaanlab_kode = $this->jenispemeriksaanlab_kode;
        $model = self::find()->where(['LOWER (jenispemeriksaanlab_kode)'=>strtolower($this->jenispemeriksaanlab_kode),'is_deleted'=>false])->one();
        if (!empty($model) && ($model->jenispemeriksaanlab_id != $this->jenispemeriksaanlab_id)){
            $this->addError("jenispemeriksaanlab_kode","Kode Sudah Dipakai");
            return false;
        }
    
        return true;
    }

    public function chkNamaPemeriksaan()
    {
        $model = self::find()->where(['LOWER (jenispemeriksaanlab_nama)' => strtolower($this->jenispemeriksaanlab_nama), 'is_deleted' => false])->one();
        if (!empty($model) && ($model->jenispemeriksaanlab_id != $this->jenispemeriksaanlab_id)) {
            $this->addError("jenispemeriksaanlab_nama", "Nama Sudah Dipakai");
            return false;
        }
        return true;
    }
}
?>