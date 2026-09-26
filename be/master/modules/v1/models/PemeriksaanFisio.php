<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pemeriksaanfisio_m".
 *
 * @property int $pemeriksaanfisio_id
 * @property int $daftartindakan_id
 * @property int $jenispemeriksaanfisio_id
 * @property string $pemeriksaanfisio_kode
 * @property string $pemeriksaanfisio_nama
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
 * @property DaftartindakanM $daftartindakan
 * @property JenispemeriksaanfisioM $jenispemeriksaanfisio
 */
class PemeriksaanFisio extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pemeriksaanfisio_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['daftartindakan_id', 'jenispemeriksaanfisio_id', 'kelompokpemeriksaanfisio_id'], 'required'],
            [['daftartindakan_id', 'jenispemeriksaanfisio_id', 'kelompokpemeriksaanfisio_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['daftartindakan_id', 'jenispemeriksaanfisio_id', 'kelompokpemeriksaanfisio_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['daftartindakan_id', 'jenispemeriksaanfisio_id', 'kelompokpemeriksaanfisio_id', 'pemeriksaanfisio_kode', 'pemeriksaanfisio_nama', 'tipepaket_id', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['pemeriksaanfisio_kode'], 'string', 'max' => 10],
            [['pemeriksaanfisio_nama'], 'string', 'max' => 500],
            // [['pemeriksaanfisio_kode'], 'unique'],
            [['daftartindakan_id'], 'exist', 'skipOnError' => true, 'targetClass' => DaftarTindakan::className(), 'targetAttribute' => ['daftartindakan_id' => 'daftartindakan_id']],
            [['jenispemeriksaanfisio_id'], 'exist', 'skipOnError' => true, 'targetClass' => JenisPemeriksaanFisio::className(), 'targetAttribute' => ['jenispemeriksaanfisio_id' => 'jenispemeriksaanfisio_id']],
            [['pemeriksaanfisio_kode'], 'chkKodeKelompok'],
            [['daftartindakan_id'], 'chkNamaPemeriksaan'],
            [['jenispemeriksaanfisio_id'], 'chkJenisPemeriksaan'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pemeriksaanfisio_id' => 'Pemeriksaanfisioterapi ID',
            'daftartindakan_id' => 'Daftartindakan ID',
            'jenispemeriksaanfisio_id' => 'Jenispemeriksaanfisio ID',
            'pemeriksaanfisio_kode' => 'Pemeriksaanfisio Kode',
            'pemeriksaanfisio_nama' => 'Pemeriksaanfisio Nama',
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
    public function getDaftarTindakan()
    {
        return $this->hasOne(DaftarTindakan::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKelompokPemeriksaanFisio()
    {
        return $this->hasOne(KelompokPemeriksaanFisio::className(), ['kelompokpemeriksaanfisio_id' => 'kelompokpemeriksaanfisio_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJenisPemeriksaanFisio()
    {
        return $this->hasOne(JenisPemeriksaanFisio::className(), ['jenispemeriksaanfisio_id' => 'jenispemeriksaanfisio_id']);
    }

    // Extra fields
    public function extraFields()
    {
        // Return
        return [
            'jenispemeriksaanfisio_m' => function ($item) {
                // Return
                return $item->jenisPemeriksaanFisio;
            },
            'kelompokpemeriksaanfisio_m' => function ($item) {
                // Return
                return $item->kelompokPemeriksaanFisio;
            },
            'daftartindakan_m' => function ($item) {
                // Return
                return $item->daftarTindakan;
            }
        ];
    }

    public function chkKodeKelompok()
    {
        $pemeriksaanfisio_kode = $this->pemeriksaanfisio_kode;
        $model = self::find()->where(['LOWER (pemeriksaanfisio_kode)' => strtolower($this->pemeriksaanfisio_kode), 'is_deleted' => false])->one();
        if (!empty($model) && ($model->pemeriksaanfisio_id != $this->pemeriksaanfisio_id)) {
            $this->addError("pemeriksaanfisio_kode", "Kode Sudah Dipakai");
            return false;
        }

        return true;
    }

    public function chkNamaPemeriksaan()
    {
        $model = self::find()->where(['daftartindakan_id' => $this->daftartindakan_id, 'is_deleted' => false])->one();
        if (!empty($model) && ($model->pemeriksaanfisio_id != $this->pemeriksaanfisio_id)) {
            $this->addError("daftartindakan_id", "Nama Pemeriksaan Sudah Dipakai");
            return false;
        }
        return true;
    }

    public function chkJenisPemeriksaan()
    {
        $model = self::find()->where(['jenispemeriksaanfisio_id' => $this->jenispemeriksaanfisio_id, 'is_deleted' => false])->one();
        if (!empty($model) && ($model->pemeriksaanfisio_id != $this->pemeriksaanfisio_id)) {
            $this->addError("jenispemeriksaanfisio_id", "Jenis Pemeriksaan Sudah Dipakai");
            return false;
        }
        return true;
    }
}
