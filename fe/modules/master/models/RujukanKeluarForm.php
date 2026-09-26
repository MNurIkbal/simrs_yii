<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "rujukankeluar_m".
 *
 * @property integer $rujukankeluar_id
 * @property integer $asalrujukan_id
 * @property string $rumahsakit_rujukan
 * @property string $alamat_rsrujukan
 * @property string $telp_fax
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
 *
 * @property InvoicekeluarT[] $invoicekeluarTs
 * @property InvoicemasukT[] $invoicemasukTs
 * @property AsalrujukanM $asalrujukan
 */

class RujukanKeluarForm extends \yii\base\Model
{
    
    public $rujukankeluar_id;
    public $asalrujukan_id;
    public $rumahsakit_rujukan;
    public $alamat_rsrujukan;
    public $telp_fax;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rujukankeluar_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['asalrujukan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['rumahsakit_rujukan'], 'required'],
            [['alamat_rsrujukan', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['rumahsakit_rujukan', 'telp_fax'], 'string', 'max' => 50],
           /* [['asalrujukan_id'], 'exist', 'skipOnError' => true, 'targetClass' => AsalRujukan::className(), 'targetAttribute' => ['asalrujukan_id' => 'asalrujukan_id']],*/
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rujukankeluar_id' => Yii::t('fe', 'Rujukan Keluar ID'),
            'asalrujukan_id' => Yii::t('fe', 'Asal Rujukan'),
            'rumahsakit_rujukan' => Yii::t('fe', 'Rumah Sakit Rujukan'),
            'alamat_rsrujukan' => Yii::t('fe', 'Alamat RS Rujukan'),
            'telp_fax' => Yii::t('fe', 'No Telp'),
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
