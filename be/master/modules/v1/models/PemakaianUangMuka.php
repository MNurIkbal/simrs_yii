<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pemakaianuangmuka_t".
 *
 * @property integer $pemakaianuangmuka_id
 * @property integer $pembayaranpelayanan_id
 * @property integer $tandabuktikeluar_id
 * @property integer $bayaruangmuka_id
 * @property integer $pendaftaran_id
 * @property string $tgl_pemakaian
 * @property double $total_uangmuka
 * @property double $pemakaian_uangmuka
 * @property double $sisa_uangmuka
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
class PemakaianUangMuka extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pemakaianuangmuka_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pembayaranpelayanan_id', 'tandabuktikeluar_id', 'bayaruangmuka_id', 'pendaftaran_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_pemakaian', 'total_uangmuka'], 'required'],
            [['tgl_pemakaian', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['total_uangmuka', 'pemakaian_uangmuka', 'sisa_uangmuka'], 'number'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['bayaruangmuka_id'], 'exist', 'skipOnError' => true, 'targetClass' => BayarUangMuka::className(), 'targetAttribute' => ['bayaruangmuka_id' => 'bayaruangmuka_id']],
            // [['pembayaranpelayanan_id'], 'exist', 'skipOnError' => true, 'targetClass' => PembayaranPelayanan::className(), 'targetAttribute' => ['pembayaranpelayanan_id' => 'pembayaranpelayanan_id']],
            [['pendaftaran_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pendaftaran::className(), 'targetAttribute' => ['pendaftaran_id' => 'pendaftaran_id']],
            // [['tandabuktikeluar_id'], 'exist', 'skipOnError' => true, 'targetClass' => TandabuktikeluarT::className(), 'targetAttribute' => ['tandabuktikeluar_id' => 'tandabuktikeluar_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pemakaianuangmuka_id' => Yii::t('app', 'Pemakaian uang muka'),
            'pembayaranpelayanan_id' => Yii::t('app', 'Pembayaran pelayanan'),
            'tandabuktikeluar_id' => Yii::t('app', 'Tanda bukti keluar'),
            'bayaruangmuka_id' => Yii::t('app', 'Bayar uang muka'),
            'pendaftaran_id' => Yii::t('app', 'Pendaftaran'),
            'tgl_pemakaian' => Yii::t('app', 'Tanggal pemakaian'),
            'total_uangmuka' => Yii::t('app', 'Total uang muka'),
            'pemakaian_uangmuka' => Yii::t('app', 'Pemakaian uang muka'),
            'sisa_uangmuka' => Yii::t('app', 'Sisa uang muka'),
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
    
    public function getBayarUangMuka()
    {
        return $this->hasOne(BayarUangMuka::className(), ['bayaruangmuka_id' => 'bayaruangmuka_id']);
    }
    
    public function getPendaftaran()
    {
        return $this->hasOne(Pendaftaran::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }
    
    public function extraFields()
    {
        return [
            'bayaruangmuka_t' => function($item){
                return $item->bayarUangMuka;
            },
            'pendaftaran_t' => function($item){
                return $item->pendaftaran;
            }
        ];
    }
}
