<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-24 16:49:39
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-04-27 17:53:23
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pemeriksaanlab_m".
 *
 * @property int $pemeriksaanlab_id
 * @property int $jenispemeriksaanlab_id
 * @property int $daftartindakan_id
 * @property string $pemeriksaanlab_kode
 * @property string $pemeriksaanlab_nama
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
 * @property bool $is_exception
 *
 * @property DetailhasilpemeriksaanlabT[] $detailhasilpemeriksaanlabTs
 * @property KlasifikasiatpM[] $klasifikasiatpMs
 * @property DaftartindakanM $daftartindakan
 * @property JenispemeriksaanlabM $jenispemeriksaanlab
 */
class PemeriksaanLab extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pemeriksaanlab_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenispemeriksaanlab_id', 'daftartindakan_id', 'kelompokpemeriksaanlab_id'], 'required'],
            [['jenispemeriksaanlab_id', 'daftartindakan_id', 'kelompokpemeriksaanlab_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['jenispemeriksaanlab_id', 'daftartindakan_id', 'kelompokpemeriksaanlab_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active', 'is_exception'], 'boolean'],
            [['pemeriksaanlab_kode'], 'string'],
            [['pemeriksaanlab_nama'], 'string', 'max' => 500],
            [['daftartindakan_id'], 'exist', 'skipOnError' => true, 'targetClass' => DaftarTindakan::className(), 'targetAttribute' => ['daftartindakan_id' => 'daftartindakan_id']],
            [['jenispemeriksaanlab_id'], 'exist', 'skipOnError' => true, 'targetClass' => JenisPemeriksaanLab::className(), 'targetAttribute' => ['jenispemeriksaanlab_id' => 'jenispemeriksaanlab_id']],
            [['pemeriksaanlab_kode'], 'chkKodeKelompok'],
            [['daftartindakan_id'], 'chkNamaPemeriksaan'],
            // [['jenispemeriksaanlab_id'], 'chkJenisPemeriksaan'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pemeriksaanlab_id' => 'Pemeriksaanlab ID',
            'jenispemeriksaanlab_id' => 'Jenispemeriksaanlab ID',
            'daftartindakan_id' => 'Daftartindakan ID',
            'pemeriksaanlab_kode' => 'Pemeriksaanlab Kode',
            'pemeriksaanlab_nama' => 'Pemeriksaanlab Nama',
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
            'is_exception' => 'Exception Test'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDaftarTindakan()
    {
        return $this->hasOne(DaftarTindakan::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKelompokPemeriksaanLab()
    {
        return $this->hasOne(KelompokPemeriksaanLab::className(), ['kelompokpemeriksaanlab_id' => 'kelompokpemeriksaanlab_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJenisPemeriksaanLab()
    {
        return $this->hasOne(JenisPemeriksaanLab::className(), ['jenispemeriksaanlab_id' => 'jenispemeriksaanlab_id']);
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields['nama_kelompok'] = function ($model) {
            return $model->kelompokPemeriksaanLab
                ? $model->kelompokPemeriksaanLab->nama_kelompok
                : null;
        };

        return $fields;
    }

    // Extra fields
    public function extraFields()
    {
        // Return
        return [
            'jenispemeriksaanlab_m' => function($item) {
                // Return
                return $item->jenisPemeriksaanLab;
            },
            'kelompokpemeriksaanlab_m' => function($item) {
                // Return
                return $item->kelompokPemeriksaanLab;
            },
            'daftartindakan_m' => function($item) {
                // Return
                return $item->daftarTindakan;
            },
        ];
    }

    public function chkKodeKelompok()
    {
        $pemeriksaanlab_kode = $this->pemeriksaanlab_kode;
        $model = self::find()->where(['LOWER (pemeriksaanlab_kode)'=>strtolower($this->pemeriksaanlab_kode),'is_deleted'=>false])->one();
        if (!empty($model) && ($model->pemeriksaanlab_id != $this->pemeriksaanlab_id)){
            $this->addError("pemeriksaanlab_kode","Kode Sudah Dipakai");
            return false;
        }
    
        return true;
    }

    public function chkNamaPemeriksaan()
    {
        $model = self::find()->where(['daftartindakan_id' => $this->daftartindakan_id, 'is_deleted' => false])->one();
        if (!empty($model) && ($model->pemeriksaanlab_id != $this->pemeriksaanlab_id)) {
            $this->addError("daftartindakan_id", "Nama Pemeriksaan Sudah Dipakai");
            return false;
        }
        return true;
    }

    // public function chkJenisPemeriksaan()
    // {
    //     $model = self::find()->where(['jenispemeriksaanlab_id' => $this->jenispemeriksaanlab_id, 'is_deleted' => false])->one();
    //     if (!empty($model) && ($model->pemeriksaanlab_id != $this->pemeriksaanlab_id)) {
    //         $this->addError("jenispemeriksaanlab_id", "Jenis Pemeriksaan Sudah Dipakai");
    //         return false;
    //     }
    //     return true;
    // }
}
?>