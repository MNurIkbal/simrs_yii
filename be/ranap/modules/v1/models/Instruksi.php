<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-13 09:17:42
 * @Last Modified by:   rizal
 * @Last Modified time: 2018-08-14 11:23:54
 */

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\models\PasienKirimUnitlain;

/**
 * This is the model class for table "instruksi_t".
 *
 * @property int $instruksi_id
 * @property int $cppt_id
 * @property string $tgl_instruksi
 * @property string $jenis_instruksi lookup_type='jenis_instruksi'
 * @property string $catatan_instruksi
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
class Instruksi extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'instruksi_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cppt_id', 'tgl_instruksi'], 'required'],
            [['cppt_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['instruksi_id', 'cppt_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_instruksi', 'created_date', 'last_modified_date', 'deleted_date', 'is_puasa'], 'safe'],
            [['catatan_instruksi', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['jenis_instruksi'], 'string', 'max' => 255],
            [['instruksi_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'instruksi_id' => 'Instruksi ID',
            'cppt_id' => 'Cppt ID',
            'tgl_instruksi' => 'Tgl Instruksi',
            'jenis_instruksi' => 'Jenis Instruksi',
            'catatan_instruksi' => 'Catatan Instruksi',
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


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienKirimKeUnitLain()
    {
        return $this->hasOne(PasienKirimUnitLain::className(), ['instruksi_id' => 'instruksi_id']);
    }
}
?>