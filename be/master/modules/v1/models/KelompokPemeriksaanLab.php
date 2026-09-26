<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-23 11:25:27
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-04-24 10:31:03
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kelompokpemeriksaanlab_m".
 *
 * @property int $kelompokpemeriksaanlab_id
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
class KelompokPemeriksaanLab extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kelompokpemeriksaanlab_m';
    }

    /**
     * {@inheritdoc}
     */

    // Validasi XSS di Form
    protected $xssProtected = [
        'kode_kelompok',
        'keterangan_kelompok',
        'nama_kelompok'
    ];

    public function rules()
    {
        return [
            [['kode_kelompok','nama_kelompok'], 'required', 'message'=>'{attribute} Tidak boleh kosong'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kode_kelompok'], 'string', 'max' => 25],
            [['nama_kelompok', 'keterangan_kelompok'], 'string', 'max' => 255],
            [['kode_kelompok'], 'chkKodeKelompok'],
            [['nama_kelompok'], 'chkNamaKelompok'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kelompokpemeriksaanlab_id' => 'Kelompokpemeriksaanlab ID',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJenisPemeriksaanLab()
    {
        return $this->hasMany(JenisPemeriksaanLab::className(), ['kelompokpemeriksaanlab_id' => 'kelompokpemeriksaanlab_id']);
    }

    public function chkKodeKelompok()
    {
        $kode_kelompok = $this->kode_kelompok;
        $model = self::find()->where(['LOWER (kode_kelompok)'=>strtolower($this->kode_kelompok),'is_deleted'=>false])->one();
        if(!empty($model) && ($model->kelompokpemeriksaanlab_id != $this->kelompokpemeriksaanlab_id)){
            $this->addError("kode_kelompok","Kode Sudah Dipakai");
            return false;
        }
    
        return true;
    }

    public function chkNamaKelompok()
    {
        $model = self::find()->where(['LOWER (nama_kelompok)' => strtolower($this->nama_kelompok), 'is_deleted' => false])->one();
        if (!empty($model) && ($model->kelompokpemeriksaanlab_id != $this->kelompokpemeriksaanlab_id)) {
            $this->addError("nama_kelompok", "Nama Sudah Dipakai");
            return false;
        }
        return true;
    }
}
?>