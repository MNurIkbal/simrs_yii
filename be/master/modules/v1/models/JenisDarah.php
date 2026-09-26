<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "jenisdarah_m".
 *
 * @property int $jenisdarah_id
 * @property string $jenisdarah_nama
 * @property int $lama_penyimpanan
 * @property int $suhu_penyimpanan
 * @property double $harga
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
 */
class JenisDarah extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    
    public static function tableName()
    {
        return 'jenisdarah_m';
    }

    protected $xssProtected = [
        'jenisdarah_nama',
        'lama_penyimpanan',
        'suhu_penyimpanan',
        'harga'
    ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenisdarah_nama', 'lama_penyimpanan', 'suhu_penyimpanan', 'harga'], 'required'],
            [['jenisdarah_nama'], 'trim'],
            [['modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['harga'], 'number'],
            [['additional_data'], 'string'],
            [['jenisdarah_nama', 'lama_penyimpanan', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['jenisdarah_nama'], 'string', 'max' => 255],
            [['jenisdarah_nama'], 'chkNama'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jenisdarah_id' => 'Jenisdarah ID',
            'jenisdarah_nama' => 'Jenisdarah Nama',
            'lama_penyimpanan' => 'Lama Penyimpanan',
            'suhu_penyimpanan' => 'Suhu Penyimpanan',
            'harga' => 'Harga',
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

    public function chkNama($params, $attributes)
    {
        $jenisdarah_nama = $this->jenisdarah_nama;
        $model = self::find()->where([
            'TRIM(LOWER (jenisdarah_nama))' => strtolower($jenisdarah_nama), 
            'is_deleted' => false
        ])->one();
        if(!empty($model) && $model->jenisdarah_id != $this->jenisdarah_id ){
            $this->addError("jenisdarah_nama","Nama Jenis Darah Sudah Dipakai");
            return false;
        }
    
        return true;
    }
}
