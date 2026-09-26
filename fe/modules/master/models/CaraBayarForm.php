<?php

namespace app\modules\master\models;

use Yii;

/**
 *
 * @property integer $carabayar_id
 * @property string $carabayar_nama
 * @property string $carabayar_namalainnya
 * @property string $metode_pembayaran
 * @property string $carabayar_loket
 * @property string $carabayar_singkatan
 * @property integer $carabayar_urutan
 * @property boolean $is_subsidiasuransi
 * @property boolean $is_subsidipemerintah
 * @property boolean $is_subsidirs
 * @property boolean $is_active
 */
class CaraBayarForm extends \yii\base\Model
{
    public $carabayar_nama;
    public $carabayar_namalainnya;
    public $metode_pembayaran;
    public $carabayar_loket;
    public $carabayar_singkatan;
    public $is_subsidiasuransi;
    public $is_subsidipemerintah;
    public $is_subsidirs;
    public $is_deleted;
    public $is_active;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['carabayar_nama', 'metode_pembayaran'], 'required'],
            [['metode_pembayaran'], 'integer'],
            [['is_subsidirs', 'is_subsidipemerintah', 'is_subsidiasuransi', 'is_active'], 'default', 'value' => '0'],
            [['is_subsidiasuransi', 'is_subsidipemerintah', 'is_subsidirs', 'is_deleted', 'is_active'], 'boolean'],
            [['carabayar_nama', 'carabayar_namalainnya', 'metode_pembayaran', 'carabayar_loket'], 'string', 'max' => 100],
            [['carabayar_singkatan'], 'string', 'max' => 10],
            [['carabayar_nama'], 'trimWhitespacenama'],
            [['carabayar_nama'], 'trimKutipnama'],
            [['carabayar_namalainnya'], 'trimWhitespacelainnya'],
            [['carabayar_namalainnya'], 'trimKutiplainnya'],
            [['is_subsidipemerintah'], 'safe'],
        ];
    }

    public function trimWhitespacenama(){
        $carabayar_nama = $this->carabayar_nama;
        $return = true;
        if (strpos(substr($carabayar_nama, 0, 1), ' ') !== FALSE) {
            $this->addError('carabayar_nama', 'Nama mengandung spasi di awal kata');
            $return = false;
        }
        
        return $return;
    }

    public function trimKutipnama(){
        $carabayar_nama = $this->carabayar_nama;
        $return = true;
        if (preg_match("/'/",$carabayar_nama) == 1) {
            $this->addError('carabayar_nama', 'Nama mengandung kutip');
            $return = false;
        }
        
        return $return;
    }

    public function trimWhitespacelainnya(){
        $carabayar_namalainnya = $this->carabayar_namalainnya;
        $return = true;
        if (strpos(substr($carabayar_namalainnya, 0, 1), ' ') !== FALSE) {
            $this->addError('carabayar_namalainnya', 'Nama mengandung spasi di awal kata');
            $return = false;
        }
        
        return $return;
    }

    public function trimKutiplainnya(){
        $carabayar_namalainnya = $this->carabayar_namalainnya;
        $return = true;
        if (preg_match("/'/",$carabayar_namalainnya) == 1) {
            $this->addError('carabayar_namalainnya', 'Nama Lainnya mengandung kutip');
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
            'carabayar_nama' => Yii::t('fe', 'Nama'),
            'carabayar_namalainnya' => Yii::t('fe', 'Nama Lainnya'),
            'metode_pembayaran' => Yii::t('fe', 'Metode Pembayaran'),
            'carabayar_loket' => Yii::t('fe', 'Cara Bayar Loket'),
            'carabayar_singkatan' => Yii::t('fe', 'Singkatan'),
            'is_subsidiasuransi' => Yii::t('fe', 'Subsidi Asuransi'),
            'is_subsidipemerintah' => Yii::t('fe', 'Subsidi Pemerintah'),
            'is_subsidirs' => Yii::t('fe', 'Subsidi Rumah Sakit'),
            'is_active' => \Yii::t('fe','Status'),
        ];
    }

    
}
