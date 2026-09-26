<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "hasilpemeriksaanlabdetail_t".
 *
 * @property int $hasilpemeriksaanlabdetail_id
 * @property int $tindakanpelayanan_id
 * @property int $pemeriksaanlab_id
 * @property int $hasilpemeriksaanlab_id
 * @property int $nilairujukan_id
 * @property string $hasil
 * @property string $nilai_rujukan
 * @property string $satuan_hasil
 * @property string $keterangan
 * @property int $petugaslab_id
 * @property int $tindakanpaket_id
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
 * @property HasilpemeriksaanlabT $hasilpemeriksaanlab
 * @property PemeriksaanlabM $pemeriksaanlab
 * @property TindakanpelayananT $tindakanpelayanan
 */
class HasilPemeriksaanLabDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    protected $xssProtected = [
        'hasil'
    ];
    
    public static function tableName()
    {
        return 'hasilpemeriksaanlabdetail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tindakanpelayanan_id', 'pemeriksaanlab_id', 'hasilpemeriksaanlab_id', 'nilairujukan_id', 'petugaslab_id', 'tindakanpaket_id', 'samplelab_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by','no_urut'], 'default', 'value' => null],
            [['tindakanpelayanan_id', 'pemeriksaanlab_id', 'hasilpemeriksaanlab_id', 'nilairujukan_id', 'petugaslab_id', 'tindakanpaket_id', 'samplelab_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['pemeriksaanlab_id', 'hasilpemeriksaanlab_id'], 'required'],
            [['hasil', 'nilai_rujukan', 'keterangan', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', 'tanggal_verifikasi', 'petugas_verifikasi'], 'safe'],
            [['is_deleted', 'is_active', 'is_verifikasi'], 'boolean'],
            [['satuan_hasil'], 'string', 'max' => 100],
            // [['hasilpemeriksaanlab_id'], 'exist', 'skipOnError' => true, 'targetClass' => HasilpemeriksaanlabT::className(), 'targetAttribute' => ['hasilpemeriksaanlab_id' => 'hasilpemeriksaanlab_id']],
            // [['pemeriksaanlab_id'], 'exist', 'skipOnError' => true, 'targetClass' => PemeriksaanlabM::className(), 'targetAttribute' => ['pemeriksaanlab_id' => 'pemeriksaanlab_id']],
            // [['tindakanpelayanan_id'], 'exist', 'skipOnError' => true, 'targetClass' => TindakanpelayananT::className(), 'targetAttribute' => ['tindakanpelayanan_id' => 'tindakanpelayanan_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'hasilpemeriksaanlabdetail_id' => 'Hasilpemeriksaanlabdetail ID',
            'tindakanpelayanan_id' => 'Tindakanpelayanan ID',
            'pemeriksaanlab_id' => 'Pemeriksaanlab ID',
            'hasilpemeriksaanlab_id' => 'Hasilpemeriksaanlab ID',
            'nilairujukan_id' => 'Nilairujukan ID',
            'hasil' => 'Hasil',
            'nilai_rujukan' => 'Nilai Rujukan',
            'satuan_hasil' => 'Satuan Hasil',
            'keterangan' => 'Keterangan',
            'petugaslab_id' => 'Petugaslab ID',
            'tindakanpaket_id' => 'Tindakanpaket ID',
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
             // No Urut
            'no_urut' => 'Nomor Urut',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getHasilpemeriksaanlab()
    // {
    //     return $this->hasOne(HasilpemeriksaanlabT::className(), ['hasilpemeriksaanlab_id' => 'hasilpemeriksaanlab_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPemeriksaanlab()
    // {
    //     return $this->hasOne(PemeriksaanlabM::className(), ['pemeriksaanlab_id' => 'pemeriksaanlab_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getTindakanpelayanan()
    // {
    //     return $this->hasOne(TindakanpelayananT::className(), ['tindakanpelayanan_id' => 'tindakanpelayanan_id']);
    // }
}
