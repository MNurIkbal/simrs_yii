<?php
/**
 * @Author: Naufal Ziyad L
 * @Date:   2018-01-12 10:47
 * @Last Modified by:   Naufal
 */
namespace Doco\models;

use Yii;

class RujukanKeluar extends \Doco\components\DocoActiveRecord
{
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
            [['asalrujukan_id'], 'exist', 'skipOnError' => true, 'targetClass' => AsalRujukan::className(), 'targetAttribute' => ['asalrujukan_id' => 'asalrujukan_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rujukankeluar_id' => 'Rujukankeluar ID',
            'asalrujukan_id' => 'Asalrujukan ID',
            'rumahsakit_rujukan' => 'Rumahsakit Rujukan',
            'alamat_rsrujukan' => 'Alamat Rsrujukan',
            'telp_fax' => 'Telp Fax',
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
