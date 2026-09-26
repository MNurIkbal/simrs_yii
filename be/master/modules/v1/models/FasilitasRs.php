<?php

/**
 * @Author: Sigit
 * @Date:   2018-09-17 11:23:14
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "fasilitasrs_m".
 *
 * @property int $fasilitasrs_id
 * @property string $jenis_fasilitas instalasi_m, free text
 * @property string $nama_fasilitas ruangan_m berdasarkan instalasi_id, free text
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
class FasilitasRs extends \Doco\components\DocoActiveRecord
{
	/**
     * {@inheritdoc}
     */
    private $_detail;
    
    /**
     * {@inheritdoc}
     */
    public function setDetail($detail)
    {
        $this->_detail = $detail;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getDetail()
    {        
        return $this->_detail;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fasilitasrs_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenis_fasilitas', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['nama_fasilitas', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['nama_fasilitas'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'fasilitasrs_id' => 'Fasilitasrs ID',
            'jenis_fasilitas' => 'Jenis Fasilitas',
            'nama_fasilitas' => 'Nama Fasilitas',
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
