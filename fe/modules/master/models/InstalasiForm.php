<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "instalasi_m".
 *
 * @property integer $instalasi_id
 * @property string $instalasi_nama
 * @property string $instalasi_namalainnya
 * @property string $instalasi_singkatan
 * @property string $instalasi_lokasi
 * @property boolean $instalasirujukaninternal
 * @property boolean $instalasi_adakamar
 * @property string $instalasi_image
 * @property boolean $is_penunjang
 * @property integer $profilers_id
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
class InstalasiForm extends \yii\base\Model
{
    public $instalasi_nama;
    public $instalasi_namalainnya;
    public $instalasi_singkatan;
    public $instalasi_lokasi;
    public $instalasirujukaninternal;
    public $instalasi_adakamar;
    public $instalasi_image;
    public $is_penunjang;
    public $is_pelayanan;
    public $profilers_id;
    public $additional_data;
    public $satusehat_instalasi;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'instalasi_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['profilers_id'], 'integer'],
            [['instalasi_nama', 'instalasi_singkatan', 'profilers_id'], 'required'],
            [['instalasirujukaninternal', 'instalasi_adakamar', 'is_penunjang', 'is_pelayanan'], 'boolean'],
            [['additional_data'], 'string'],
            [['satusehat_instalasi'], 'safe'],
            [['instalasi_nama', 'instalasi_namalainnya', 'instalasi_lokasi'], 'string', 'max' => 50],
            [['instalasi_singkatan'], 'string', 'max' => 5],
            [['instalasi_image'], 'string', 'max' => 200],
            [['instalasi_nama'], 'trimWhitespacenama'],
            [['instalasi_nama'], 'trimKutipnama'],
            [['instalasi_namalainnya'], 'trimWhitespacelainnya'],
            [['instalasi_namalainnya'], 'trimKutiplainnya'],
            [['instalasi_singkatan'], 'trimWhitespacesingkatan'],
            [['instalasi_singkatan'], 'trimKutipsingkatan'],
        ];
    }

    public function trimWhitespacenama(){
        $instalasi_nama = $this->instalasi_nama;
        $return = true;
        if (strpos(substr($instalasi_nama, 0, 1), ' ') !== FALSE) {
            $this->addError('instalasi_nama', 'Nama mengandung spasi di awal kata');
            $return = false;
        }
        return $return;
    }

    public function trimKutipnama(){
        $instalasi_nama = $this->instalasi_nama;
        $return = true;
        if (preg_match("/'/",$instalasi_nama) == 1) {
            $this->addError('instalasi_nama', 'Nama mengandung kutip');
            $return = false;
        }
        return $return;
    }

    public function trimWhitespacelainnya(){
        $instalasi_namalainnya = $this->instalasi_namalainnya;
        $return = true;
        if (strpos(substr($instalasi_namalainnya, 0, 1), ' ') !== FALSE) {
            $this->addError('instalasi_namalainnya', 'Nama mengandung spasi di awal kata');
            $return = false;
        }
        return $return;
    }

    public function trimKutiplainnya(){
        $instalasi_namalainnya = $this->instalasi_namalainnya;
        $return = true;
        if (preg_match("/'/",$instalasi_namalainnya) == 1) {
            $this->addError('instalasi_namalainnya', 'Nama mengandung kutip');
            $return = false;
        }
        return $return;
    }

    public function trimWhitespacesingkatan(){
        $instalasi_singkatan = $this->instalasi_singkatan;
        $return = true;
        if (strpos(substr($instalasi_singkatan, 0, 1), ' ') !== FALSE) {
            $this->addError('instalasi_singkatan', 'Nama mengandung spasi di awal kata');
            $return = false;
        }
        return $return;
    }

    public function trimKutipsingkatan(){
        $instalasi_singkatan = $this->instalasi_singkatan;
        $return = true;
        if (preg_match("/'/",$instalasi_singkatan) == 1) {
            $this->addError('instalasi_singkatan', 'Nama mengandung kutip');
            $return = false;
        }
        return $return;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'instalasi_id'             => Yii::t('fe', 'Instalasi ID'),
            'riwayatruangan_id'        => Yii::t('fe', 'Riwayatruangan ID'),
            'instalasi_nama'           => Yii::t('fe', 'Nama Instalasi'),
            'instalasi_namalainnya'    => Yii::t('fe', 'Nama Lain Instalasi'),
            'instalasi_singkatan'      => Yii::t('fe', 'Nama Singkatan Instalasi'),
            'instalasi_lokasi'         => Yii::t('fe', 'Instalasi Lokasi'),
            'instalasirujukaninternal' => Yii::t('fe', 'Instalasirujukaninternal'),
            'instalasi_adakamar'       => Yii::t('fe', 'Ada Kamar'),
            'instalasi_image'          => Yii::t('fe', 'Instalasi Image'),
            'is_penunjang'             => Yii::t('fe', 'Penunjang'),
            'is_pelayanan'             => Yii::t('fe', 'Pelayanan'),
            'profilers_id'             => Yii::t('fe', 'Profile Rumah Sakit'),
            'additional_data'          => \Yii::t('fe','Additional data'),
            'created_date'             => \Yii::t('fe','Created date'),
            'created_by'               => \Yii::t('fe','Created by'),
            'modified_count'           => \Yii::t('fe','Modified count'),
            'last_modified_date'       => \Yii::t('fe','Last modified date'),
            'last_modified_by'         => \Yii::t('fe','Last modified by'),
            'is_deleted'               => \Yii::t('fe','Is deleted'),
            'is_active'                => \Yii::t('fe','Status'),
            'deleted_date'             => \Yii::t('fe','Deleted date'),
            'deleted_by'               => \Yii::t('fe','Deleted by'),
            'satusehat_instalasi'      => \Yii::t('fe','Satu Sehat Organization ID'),
        ];
    }
}
