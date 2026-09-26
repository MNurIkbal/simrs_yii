<?php

namespace Doco\models;

use Yii;

/**
 * This is the model class for table "reseptur_t".
 *
 * @property integer $reseptur_id
 * @property integer $pasienadmisi_id
 * @property integer $ruangan_id
 * @property integer $pasien_id
 * @property integer $pegawai_id
 * @property integer $pendaftaran_id
 * @property integer $penjualanresep_id
 * @property string $tglreseptur
 * @property string $noresep
 * @property integer $ruanganreseptur_id
 * @property string $fileresep
 * @property string $create_time
 * @property string $update_time
 * @property integer $create_loginpemakai_id
 * @property integer $update_loginpemakai_id
 * @property integer $create_ruangan
 * @property integer $unitdosis_id
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
 * @property string $catatan
 *
 * @property PenjualanresepT[] $penjualanresepTs
 */
class Reseptur extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reseptur_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pasienadmisi_id', 'ruangan_id', 'pasien_id', 'pegawai_id', 'pendaftaran_id', 'penjualanresep_id', 'ruanganreseptur_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tglreseptur', 'kategori_resep', 'created_date', 'last_modified_date', 'deleted_date','catatan'], 'safe'],
            [['additional_data'], 'string'],
            [['noresep'], 'string', 'max' => 50],
            [['luas_tubuh'], 'string', 'max' => 100],
            [['noresep'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'reseptur_id' => 'Reseptur ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'ruangan_id' => 'Ruangan ID',
            'pasien_id' => 'Pasien ID',
            'pegawai_id' => 'Pegawai ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'penjualanresep_id' => 'Penjualanresep ID',
            'tglreseptur' => 'Tglreseptur',
            'noresep' => 'Noresep',
            'ruanganreseptur_id' => 'Ruanganreseptur ID',
            'status_reseptur' => 'Status Reseptur',
            'antrian_id' => 'Antrian ID',
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
            'instruksi_id' => 'Instruksi ID',
            'is_hamil' => 'Is Hamil',
            'berat_badan' => 'Berat Badan',
            'tinggi_badan' => 'Tinggi Badan',
            'luas_tubuh' => 'Luas Tubuh',
            'diagnosa_id' => 'Diagnosa ID',
            'catatan' => 'Catatan',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenjualanresepTs()
    {
        return $this->hasMany(PenjualanresepT::className(), ['reseptur_id' => 'reseptur_id']);
    }
}
