<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "bayaruangmuka_t".
 *
 * @property integer $bayaruangmuka_id
 * @property integer $pembatalanuangmuka_id
 * @property integer $pasienadmisi_id
 * @property integer $pemakaianuangmuka_id
 * @property integer $tandabuktibayar_id
 * @property integer $ruangan_id
 * @property integer $pasien_id
 * @property integer $pendaftaran_id
 * @property string $tgl_uangmuka
 * @property double $jumlah_uangmuka
 * @property string $keterangan_uangmuka
 * @property string $tgl_perjanjian
 * @property string $keterangan_perjanjian
 * @property integer $pembayarankapitasidetail_id
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
 */
class BayarUangMuka extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bayaruangmuka_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pembatalanuangmuka_id', 'pasienadmisi_id', 'pemakaianuangmuka_id', 'tandabuktibayar_id', 'ruangan_id', 'pasien_id', 'pendaftaran_id', 'pembayarankapitasidetail_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['ruangan_id', 'pasien_id', 'pendaftaran_id', 'tgl_uangmuka', 'jumlah_uangmuka'], 'required'],
            [['tgl_uangmuka', 'tgl_perjanjian', 'created_date', 'last_modified_date', 'deleted_date','bayaruangmuka_id'], 'safe'],
            [['jumlah_uangmuka'], 'number'],
            [['keterangan_uangmuka', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['keterangan_perjanjian'], 'string', 'max' => 200],
            // [['bayaruangmuka_id'], 'exist', 'skipOnError' => true, 'targetClass' => BayarUangMuka::className(), 'targetAttribute' => ['bayaruangmuka_id' => 'bayaruangmuka_id']],
            [['pasien_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pasien::className(), 'targetAttribute' => ['pasien_id' => 'pasien_id']],
            // [['pasienadmisi_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienadmisiT::className(), 'targetAttribute' => ['pasienadmisi_id' => 'pasienadmisi_id']],
            // [['pemakaianuangmuka_id'], 'exist', 'skipOnError' => true, 'targetClass' => PemakaianuangmukaT::className(), 'targetAttribute' => ['pemakaianuangmuka_id' => 'pemakaianuangmuka_id']],
            // [['pembatalanuangmuka_id'], 'exist', 'skipOnError' => true, 'targetClass' => PembatalanuangmukaT::className(), 'targetAttribute' => ['pembatalanuangmuka_id' => 'pembatalanuangmuka_id']],
            [['pendaftaran_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pendaftaran::className(), 'targetAttribute' => ['pendaftaran_id' => 'pendaftaran_id']],
            // [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => RuanganM::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
            // [['tandabuktibayar_id'], 'exist', 'skipOnError' => true, 'targetClass' => TandabuktibayarT::className(), 'targetAttribute' => ['tandabuktibayar_id' => 'tandabuktibayar_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'bayaruangmuka_id' => Yii::t('app', 'Bayar uang muka'),
            'pembatalanuangmuka_id' => Yii::t('app', 'Pembatalan uang muka'),
            'pasienadmisi_id' => Yii::t('app', 'Pasien admisi'),
            'pemakaianuangmuka_id' => Yii::t('app', 'Pemakaian uang muka'),
            'tandabuktibayar_id' => Yii::t('app', 'Tanda bukti bayar'),
            'ruangan_id' => Yii::t('app', 'Ruangan'),
            'pasien_id' => Yii::t('app', 'Pasien'),
            'pendaftaran_id' => Yii::t('app', 'Pendaftaran'),
            'tgl_uangmuka' => Yii::t('app', 'Tanggal uang muka'),
            'jumlah_uangmuka' => Yii::t('app', 'Jumlah uang muka'),
            'keterangan_uangmuka' => Yii::t('app', 'Keterangan uang muka'),
            'tgl_perjanjian' => Yii::t('app', 'Tanggal Perjanjian'),
            'keterangan_perjanjian' => Yii::t('app', 'Keterangan perjanjian'),
            'pembayarankapitasidetail_id' => Yii::t('app', 'Pembayaran kapitasi detail'),
            'additional_data' => Yii::t('app', 'Additional Data'),
            'created_date' => Yii::t('app', 'Created Date'),
            'created_by' => Yii::t('app', 'Created By'),
            'modified_count' => Yii::t('app', 'Modified Count'),
            'last_modified_date' => Yii::t('app', 'Last Modified Date'),
            'last_modified_by' => Yii::t('app', 'Last Modified By'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'is_active' => Yii::t('app', 'Is Active'),
            'deleted_date' => Yii::t('app', 'Deleted Date'),
            'deleted_by' => Yii::t('app', 'Deleted By'),
        ];
    }
    
    public function getPasien()
    {
        return $this->hasOne(Pasien::className(), ['pasien_id' => 'pasien_id']);
    }
    
    public function getPasienClosing()
    {
        return $this->hasOne(Pasien::className(), ['pasien_id' => 'pasien_id'])
                            ->from(['p' => Pasien::tableName()]);
    }

    public function getPendaftaran()
    {
        return $this->hasOne(Pendaftaran::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }
    
    public function getPemakaianUangMuka()
    {
        return $this->hasOne(PemakaianUangMuka::className(), ['bayaruangmuka_id' => 'bayaruangmuka_id']);
    }
    // public function getPembayaranPelayanan()
    // {
    //     return $this->hasOne(PembayaranPelayanan::className(), ['pendaftaran_id'=>'pendaftaran_id']);
    // }
    // public function getTandaBuktiBayar()
    // {
    //     return $this->hasOne(TandaBuktiBayar::className(), ['bayaruangmuka_id'=>'bayaruangmuka_id']);
    // }
    
    public function extraFields()
    {
        return [
            'pemakaianuangmuka_t' => function($item){
                return $item->pemakaianUangMuka;
            },
            'pendaftaran_t' => function($item){
                return $item->pendaftaran;
            },
            'pasien_m' => function($item){
                return $item->pasien;
            }
        ];
    }
}
