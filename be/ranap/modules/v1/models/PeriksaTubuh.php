<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-03-26 14:22:54
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-07-02 14:55:43
 * @Description: 
 */

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\models\BagianTubuh;
use app\modules\v1\models\BagianTubuhDetail;
/**
 * This is the model class for table "periksatubuh_t".
 *
 * @property int $periksatubuh_id
 * @property int $pemeriksaanfisik_id
 * @property int $bagiantubuh_id
 * @property string $catatan_tubuh
 * @property double $koordinat_x
 * @property double $koordinat_y
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
 * @property int $jenis_gambar_id
 * @property string $jenis_gambar_nama
 * @property string $berat_luka_bakar
 */
class PeriksaTubuh extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'periksatubuh_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pemeriksaanfisik_id'], 'required'],
            [['periksatubuh_id', 'pemeriksaanfisik_id', 'bagiantubuh_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['periksatubuh_id', 'pemeriksaanfisik_id', 'bagiantubuh_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'jenis_gambar_id'], 'integer'],
            [['catatan_tubuh', 'additional_data', 'jenis_gambar_nama', 'berat_luka_bakar'], 'string'],
            [['koordinat_x', 'koordinat_y'], 'number'],
            [['created_date', 'last_modified_date', 'deleted_date','counters','bagiantubuhdetail_id'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['periksatubuh_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'periksatubuh_id' => 'Periksatubuh ID',
            'pemeriksaanfisik_id' => 'Pemeriksaanfisik ID',
            'bagiantubuh_id' => 'Bagiantubuh ID',
            'catatan_tubuh' => 'Catatan Tubuh',
            'koordinat_x' => 'Koordinat X',
            'koordinat_y' => 'Koordinat Y',
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

    public function getBagianTubuh()
    {
        return $this->hasOne(BagianTubuh::className(),['bagiantubuh_id' => 'bagiantubuh_id']);
    }
    public function getBagianTubuhDetail()
    {
        return $this->hasOne(BagianTubuhDetail::className(),['bagiantubuhdetail_id' => 'bagiantubuhdetail_id']);
    }
}
