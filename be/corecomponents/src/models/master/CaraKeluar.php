<?php

namespace Doco\models\master;


use Yii;
use Doco\models\KonfigSystem;
use Doco\components\DocoConstants;

/**
 * This is the model class for table "carakeluar_m".
 *
 * @property int $carakeluar_id
 * @property string $carakeluar_nama
 * @property string $carakeluar_namalain
 * @property string $carakeluar_kode
 * @property int $carakeluar_urutan
 * @property string $catatan
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
 */
class CaraKeluar extends \Doco\components\DocoActiveRecord
{
    protected $xssProtected = [
        'carakeluar_nama',
        'carakeluar_kode',
        'carakeluar_namalain',
        'catatan'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'carakeluar_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['carakeluar_nama', 'carakeluar_kode'], 'required'],
            [['carakeluar_nama', 'carakeluar_kode'], 'checkUniqueCase'],
            [['carakeluar_urutan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['carakeluar_urutan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['catatan', 'additional_data'], 'string'],
            [['carakeluarinacbg_id', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['carakeluar_nama', 'carakeluar_namalain', 'carakeluar_kode'], 'string', 'max' => 100],
        ];
    }

    public function checkUniqueCase($attribute, $params)
    {
        $carakeluar_nama = strtoupper($this->carakeluar_nama);
        $carakeluar_kode = strtoupper($this->carakeluar_kode);
        
        $sql_nama = "select carakeluar_id,carakeluar_nama from carakeluar_m where UPPER( carakeluar_nama ) = '{$carakeluar_nama}' and is_deleted = false "; 
        $dataNama = Yii::$app->db->createCommand($sql_nama)->queryOne();
        
        $sql_kode = "select carakeluar_id,carakeluar_kode from carakeluar_m where UPPER( carakeluar_kode ) = '{$carakeluar_kode}' and is_deleted = false "; 
        $dataKode = Yii::$app->db->createCommand($sql_kode)->queryOne();
        $return = 1;
        if (!empty($dataNama)) {
            if ($this->carakeluar_id != $dataNama['carakeluar_id']) {
                $this->addError('carakeluar_nama','"'.$this->carakeluar_nama.'" telah dipergunakan.');
                $return ++;
            }
        }

        if (!empty($dataKode)) {
            if ($this->carakeluar_id != $dataKode['carakeluar_id']) {
                $this->addError('carakeluar_kode','"'.$this->carakeluar_kode.'" telah dipergunakan.');
                $return ++;
            }
        }
        
        if ($return == 1) {
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'carakeluar_id' => 'Carakeluar ID',
            'carakeluar_nama' => 'Carakeluar Nama',
            'carakeluar_namalain' => 'Carakeluar Namalain',
            'carakeluar_kode' => 'Carakeluar Kode',
            'carakeluar_urutan' => 'Carakeluar Urutan',
            'catatan' => 'Catatan',
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
    
    /*
    * CACHE NAME = master__cara-keluar:urutan#asc
    */
    public static function getAllAscending() 
    {
        return Yii::$app->cache->getOrSet('master__cara-keluar:urutan-asc', function () {
                  return self::find()
                      ->select(['carakeluar_id', 'carakeluar_namalain'])
                      ->where(['is_active' => true])->orderBy(['carakeluar_urutan' => SORT_ASC])
                      ->asArray()
                      ->all();
            }, 3600);
    }
    
    /*
    * CACHE NAME = master__cara-keluar:urutan#desc
    */
    public static function getAllDescending()
    {
        return Yii::$app->cache->getOrSet('master__cara-keluar:urutan-desc', function () {
                  return self::find()
                      ->select(['carakeluar_id', 'carakeluar_namalain'])
                      ->where(['is_active'=>true])->orderBy(['carakeluar_urutan' => SORT_DESC])
                      ->asArray()
                      ->all();
            }, 3600);
    }

}
