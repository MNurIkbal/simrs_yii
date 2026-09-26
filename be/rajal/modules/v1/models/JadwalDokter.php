<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "jadwaldokter_m".
 *
 * @property integer $jadwaldokter_id
 * @property integer $pegawai_id
 * @property integer $instalasi_id
 * @property integer $ruangan_id
 * @property string $jadwaldokter_tgl
 * @property string $jadwaldokter_hari
 * @property string $jadwaldokter_waktupelayanan
 * @property string $jadwaldokter_mulai
 * @property string $jadwaldokter_tutup
 * @property integer $maximumantrian
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
 * @property InstalasiM $instalasi
 * @property PegawaiM $pegawai
 * @property RuanganM $ruangan
 */
class JadwalDokter extends \Doco\components\DocoActiveRecord
{
    public $ruangan_nama;
    public $instalasi_nama;
    public $nama_pegawai;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jadwaldokter_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pegawai_id', 'instalasi_id', 'ruangan_id', 'maximumantrian', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['instalasi_id', 'ruangan_id', 'jadwaldokter_tgl', 'jadwaldokter_hari', 'jadwaldokter_waktupelayanan'], 'required'],
            [['jadwaldokter_tgl', 'jadwaldokter_mulai', 'jadwaldokter_tutup', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['jadwaldokter_hari'], 'string', 'max' => 20],
            [['jadwaldokter_waktupelayanan'], 'string', 'max' => 50],
            [['instalasi_id'], 'exist', 'skipOnError' => true, 'targetClass' => InstalasiM::className(), 'targetAttribute' => ['instalasi_id' => 'instalasi_id']],
            [['pegawai_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['pegawai_id' => 'pegawai_id']],
            [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => RuanganM::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jadwaldokter_id' => 'Jadwaldokter ID',
            'pegawai_id' => 'Pegawai ID',
            'instalasi_id' => 'Instalasi ID',
            'ruangan_id' => 'Ruangan ID',
            'jadwaldokter_tgl' => 'Jadwaldokter Tgl',
            'jadwaldokter_hari' => 'Jadwaldokter Hari',
            'jadwaldokter_waktupelayanan' => 'Jadwaldokter Waktupelayanan',
            'jadwaldokter_mulai' => 'Jadwaldokter Mulai',
            'jadwaldokter_tutup' => 'Jadwaldokter Tutup',
            'maximumantrian' => 'Maximumantrian',
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
    public function getInstalasi()
    {
        return $this->hasOne(Instalasi::className(), ['instalasi_id' => 'instalasi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPegawai()
    {
        return $this->hasOne(Pegawai::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuangan()
    {
        return $this->hasOne(Ruangan::className(), ['ruangan_id' => 'ruangan_id']);
    }

    public function extraFields()
    {
        return [
            'pegawai_m' => function($item){
                return $item->pegawai;
            }
        ];
    }
}
