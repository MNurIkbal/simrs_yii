<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "jeniskasuspenyakit_m".
 *
 * @property integer $jeniskasuspenyakit_id
 * @property string $jeniskasuspenyakit_nama
 * @property string $jeniskasuspenyakit_namalainnya
 * @property integer $jeniskasuspenyakit_urutan
 * @property string $additional_data
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 *
 * @property KasuspenyakitdiagnosaMp[] $kasuspenyakitdiagnosaMps
 * @property DiagnosaM[] $diagnosas
 * @property KasuspenyakitobatMp[] $kasuspenyakitobatMps
 * @property KasuspenyakitruanganMp[] $kasuspenyakitruanganMps
 * @property RuanganM[] $ruangans
 * @property PasienmasukpenunjangT[] $pasienmasukpenunjangTs
 * @property PendaftaranT[] $pendaftaranTs
 * @property TindakanpelayananT[] $tindakanpelayananTs
 */
class JenisKasusPenyakit extends \app\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jeniskasuspenyakit_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jeniskasuspenyakit_nama'], 'required'],
            [['jeniskasuspenyakit_urutan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['jeniskasuspenyakit_nama', 'jeniskasuspenyakit_namalainnya'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'jeniskasuspenyakit_nama' => 'Jeniskasuspenyakit Nama',
            'jeniskasuspenyakit_namalainnya' => 'Jeniskasuspenyakit Namalainnya',
            'jeniskasuspenyakit_urutan' => 'Jeniskasuspenyakit Urutan',
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
    public function getKasuspenyakitdiagnosaMps()
    {
        return $this->hasMany(KasusPenyakitDiagnosa::className(), ['jeniskasuspenyakit_id' => 'jeniskasuspenyakit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDiagnosas()
    {
        return $this->hasMany(Diagnosa::className(), ['diagnosa_id' => 'diagnosa_id'])->viaTable('kasuspenyakitdiagnosa_mp', ['jeniskasuspenyakit_id' => 'jeniskasuspenyakit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKasuspenyakitobatMps()
    {
        return $this->hasMany(KasusPenyakitObat::className(), ['jeniskasuspenyakit_id' => 'jeniskasuspenyakit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKasuspenyakitruanganMps()
    {
        return $this->hasMany(KasusPenyakitRuangan::className(), ['jeniskasuspenyakit_id' => 'jeniskasuspenyakit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuangans()
    {
        return $this->hasMany(Ruangan::className(), ['ruangan_id' => 'ruangan_id'])->viaTable('kasuspenyakitruangan_mp', ['jeniskasuspenyakit_id' => 'jeniskasuspenyakit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienmasukpenunjangTs()
    {
        return $this->hasMany(PasienMasukPenunjang::className(), ['jeniskasuspenyakit_id' => 'jeniskasuspenyakit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftaranTs()
    {
        return $this->hasMany(Pendaftaran::className(), ['jeniskasuspenyakit_id' => 'jeniskasuspenyakit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTindakanpelayananTs()
    {
        return $this->hasMany(TindakanPelayanan::className(), ['jeniskasuspenyakit_id' => 'jeniskasuspenyakit_id']);
    }
}
