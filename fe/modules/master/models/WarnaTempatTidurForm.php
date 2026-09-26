<?php
/**
 * @Author: Iqbal@docotel.om
 * @Date:   2018-09-03 16:36:56
 * @Last Modified by:   
 * @Last Modified time: 
 * @Description: 
 */

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "kettempattidur_m".
 *
 * @property integer $kettempattidur_id
 * @property string $kettempattidur_nama
 * @property string $kettempattidur_warna
 * @property string $kode_warna
 * @property string $rgb
 * @property string $kamarruangan_jenis
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property boolean $is_kosong
 * @property string $deleted_date
 * @property integer $deleted_by
 */
class WarnaTempatTidurForm extends \app\components\DocoBaseModel
{
    public $kettempattidur_id;
    public $kettempattidur_nama;
    public $kettempattidur_warna;
    public $kode_warna;
    public $rgb;
    public $kamarruangan_jenis;
    public $is_kosong;
    public $is_active;

    protected $xssProtected = [
        'kettempattidur_nama',
        'kettempattidur_warna',
    ];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kettempattidur_nama', 'kettempattidur_warna' ,'kode_warna' ,'rgb', 'kamarruangan_jenis', 'is_kosong'], 'default', 'value' => null],
            [['kettempattidur_nama','is_kosong', 'kode_warna'], 'required'],
            [['is_kosong', 'is_active'], 'boolean'],
            [['kettempattidur_nama', 'kettempattidur_warna' ,'rgb'], 'string'],
            [['kettempattidur_warna', 'rgb'], 'string', 'max' => 50],
            [['kettempattidur_nama'], 'string', 'max' => 255],
            [['kettempattidur_nama'], 'trimWhitespace'],
            [['kettempattidur_id'], 'safe'],
        ];
    }

    public function trimWhitespace(){
        $kettempattidur_nama = $this->kettempattidur_nama;
        $return = true;
        if (strpos(substr($kettempattidur_nama, 0, 1), ' ') !== FALSE) {
            $this->addError('kettempattidur_nama', 'Kode mengandung spasi di awal kata');
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
            'kettempattidur_nama' => Yii::t('app', 'Keterangan Tempat Tidur'),
            'kettempattidur_warna' => Yii::t('app', 'Nama Warna Tempat Tidur'),
            'kode_warna' => Yii::t('app', 'Warna Tempat Tidur'),
            'rgb' => Yii::t('app', 'RGB Warna'),
            'kamarruangan_jenis' => Yii::t('app', 'Jenis Kamar Ruangan'),
            'is_kosong' => Yii::t('app', 'Status Kamar'),
            'is_active' => Yii::t('app', 'Status'),
            
        ];
    }
}
