<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "marginkhusus_k".
 *
 * @property int $marginkhusus_id
 * @property string $nama
 * @property string $perda
 * @property string $mulai_berlaku
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
class MarginKhusus extends \Doco\components\DocoActiveRecord
{
    public $detail;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'marginkhusus_k';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nama', 'perda'], 'required'],
            [['perda','nama', 'detail', 'mulai_berlaku', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['perda','nama'], 'string', 'max' => 255],
            [['nama'], 'chkNama'],
            [['mulai_berlaku'], 'chkTglBerlaku']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'marginkhusus_id' => 'Margin Khusus ID',
            'perda' => 'Perda',
            'nama' => 'Nama',
            'mulai_berlaku' => 'Tgl Berlaku',
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
        $nama = $this->nama;
        $model = self::find()->where([
            'TRIM(LOWER (nama))' => strtolower($nama),
            'is_deleted' => false
        ])->one();
        if(!empty($model) && $model->marginkhusus_id != $this->marginkhusus_id ){
            $this->addError("nama","Nama Sudah Dipakai");
            return false;
        }

        return true;
    }

    public function chkTglBerlaku($params, $attributes)
    {
        $mulai_berlaku = $this->mulai_berlaku;
        $model = self::find()->where([
            'mulai_berlaku' => $mulai_berlaku,
            'is_deleted' => false
        ])->one();
        if(!empty($model) && $model->marginkhusus_id != $this->marginkhusus_id ){
            $this->addError("mulai_berlaku","Mulai Berlaku Sudah Dipakai");
            return false;
        }

        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMarginKhususDetail()
    {
        return $this->hasMany(MarginKhususDetail::className(), ['marginkhusus_id' => 'marginkhusus_id']);
    }

    public function getActiveMargin() {
        $mulai_berlaku = $this->mulai_berlaku;
        $model = Yii::$app->db->createCommand('
                    SELECT * FROM marginkhusus_k
                    WHERE mulai_berlaku <= now()
                    ORDER BY mulai_berlaku DESC
                    LIMIT 1')->queryOne();

        if($model['mulai_berlaku'] === $mulai_berlaku) {
            return true;
        } else {
            return false;
        }
    }

    public function extraFields()
    {
        return [
            'active_margin' => function($item){
                return $item->activeMargin;
            }
        ];
    }
}
