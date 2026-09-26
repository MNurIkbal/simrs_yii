<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "hasilpemeriksaanlab_t".
 *
 * @property int $hasilpemeriksaanlab_id
 * @property int $pasien_id
 * @property int $pasienmasukpenunjang_id
 * @property int $pasienadmisi_id
 * @property int $pendaftaran_id
 * @property string $nohasilperiksalab
 * @property string $tgl_hasilpemeriksaanlab
 * @property string $tgl_pengambilanhasil
 * @property string $catatan
 * @property bool $printhasillab
 * @property bool $is_kritis
 * @property string $tgl_kritis
 * @property bool $is_expertise
 * @property string $expertise
 * @property int $pegawailab_id
 * @property string $upload_file
 * @property string $tgl_expertise
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
 * @property PasienM $pasien
 * @property PasienadmisiT $pasienadmisi
 * @property PasienmasukpenunjangT $pasienmasukpenunjang
 * @property PendaftaranT $pendaftaran
 * @property HasilpemeriksaanlabdetailT[] $hasilpemeriksaanlabdetailTs
 */
class HasilPemeriksaanLab extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hasilpemeriksaanlab_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasien_id', 'pasienmasukpenunjang_id', 'pasienadmisi_id', 'pendaftaran_id', 'pegawailab_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pasien_id', 'pasienmasukpenunjang_id', 'pasienadmisi_id', 'pendaftaran_id', 'pegawailab_id', 'samplelab_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_hasilpemeriksaanlab', 'tgl_pengambilanhasil', 'tgl_kritis', 'tgl_expertise', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['catatan', 'expertise', 'upload_file', 'additional_data'], 'string'],
            [['printhasillab', 'is_kritis', 'is_expertise', 'is_deleted', 'is_active'], 'boolean'],
            [['nohasilperiksalab'], 'string', 'max' => 20],
            // [['pasien_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienM::className(), 'targetAttribute' => ['pasien_id' => 'pasien_id']],
            // [['pasienadmisi_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienadmisiT::className(), 'targetAttribute' => ['pasienadmisi_id' => 'pasienadmisi_id']],
            // [['pasienmasukpenunjang_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienmasukpenunjangT::className(), 'targetAttribute' => ['pasienmasukpenunjang_id' => 'pasienmasukpenunjang_id']],
            // [['pendaftaran_id'], 'exist', 'skipOnError' => true, 'targetClass' => PendaftaranT::className(), 'targetAttribute' => ['pendaftaran_id' => 'pendaftaran_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'hasilpemeriksaanlab_id' => 'Hasilpemeriksaanlab ID',
            'pasien_id' => 'Pasien ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'nohasilperiksalab' => 'Nohasilperiksalab',
            'tgl_hasilpemeriksaanlab' => 'Tgl Hasilpemeriksaanlab',
            'tgl_pengambilanhasil' => 'Tgl Pengambilanhasil',
            'catatan' => 'Catatan',
            'printhasillab' => 'Printhasillab',
            'is_kritis' => 'Is Kritis',
            'tgl_kritis' => 'Tgl Kritis',
            'is_expertise' => 'Is Expertise',
            'expertise' => 'Expertise',
            'pegawailab_id' => 'Pegawailab ID',
            'upload_file' => 'Upload File',
            'tgl_expertise' => 'Tgl Expertise',
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
    // public function getPasien()
    // {
    //     return $this->hasOne(PasienM::className(), ['pasien_id' => 'pasien_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPasienadmisi()
    // {
    //     return $this->hasOne(PasienadmisiT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPasienmasukpenunjang()
    // {
    //     return $this->hasOne(PasienmasukpenunjangT::className(), ['pasienmasukpenunjang_id' => 'pasienmasukpenunjang_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPendaftaran()
    // {
    //     return $this->hasOne(PendaftaranT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getHasilpemeriksaanlabdetailTs()
    // {
    //     return $this->hasMany(HasilpemeriksaanlabdetailT::className(), ['hasilpemeriksaanlab_id' => 'hasilpemeriksaanlab_id']);
    // }
}
