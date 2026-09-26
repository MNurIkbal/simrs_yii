<?php

namespace app\modules\v1\models;

use Yii;

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
class JasaDokter extends \Doco\components\DocoActiveRecord
{
    protected $xssProtected = [
        'jasadokter_nama',
        'jasadokter_kode',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jasadokter_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jasadokter_nama', 'jasadokter_kode'], 'required'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['jasadokter_nama'], 'string', 'max' => 100],
            [['jasadokter_kode'], 'string', 'max' => 10],
            [['jasadokter_kode'], 'chkKode'],
            [['jasadokter_nama'], 'chkNama'],
        ];
    }
    
    public function chkKode($params, $attributes)
    {
        $jasadokter_kode = $this->jasadokter_kode;
        $rest = substr($this->jasadokter_kode, 0, 1);
        if ($rest == " ") {
            $this->addError('jasadokter_kode', 'Kode '.$this->jasadokter_kode.' mengandung spasi di awal kata');
            return false;
        }
        else {
            $model = self::find()->where(['TRIM(LOWER (jasadokter_kode))' => strtolower($this->jasadokter_kode), 'is_deleted' => false])->one();
            if(!empty($model) && $model->jasadokter_id != $this->jasadokter_id ){
                $this->addError('jasadokter_kode','Kode Transaksi '.$this->jasadokter_kode.' telah dipergunakan');
                return false;
            }
        }
        
        return true;
    }

    public function chkNama($params, $attributes)
    {
        $jasadokter_nama = $this->jasadokter_nama;
        $rest = substr($this->jasadokter_nama, 0, 1);
        if ($rest == " ") {
            $this->addError('jasadokter_nama', 'Kode '.$this->jasadokter_nama.' mengandung spasi di awal kata');
            return false;
        }
        else {
            $model = self::find()->where(['TRIM(LOWER (jasadokter_nama))' => strtolower($this->jasadokter_nama), 'is_deleted' => false])->one();
            if(!empty($model) && $model->jasadokter_id != $this->jasadokter_id ){
                $this->addError('jasadokter_nama','Transaksi '.$this->jasadokter_nama.' telah dipergunakan');
                return false;
            }
        }
        
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jasadokter_id' => 'Jasa Dokter ID',
            'jasadokter_nama' => 'Jasa Dokter Nama',
            'jasadokter_kode' => 'Jasa Dokter Kode',
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

}
