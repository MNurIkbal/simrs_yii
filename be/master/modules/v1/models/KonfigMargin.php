<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "konfigmargin_k".
 *
 * @property int $konfigmargin_id
 * @property string $perda_margin
 * @property string $tgl_berlaku
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
 * @property int $diskon
 */
class KonfigMargin extends \Doco\components\DocoActiveRecord
{
    public $detail;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'konfigmargin_k';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'nama_margin', 
                'groupmargin_id', 
                'perda_margin',
                'kelaspelayanan_id',
                'jenisobatalkes_id'
            ], 'required'],
            [[
                'perda_margin',
                'nama_margin',
                'groupmargin_id', 
                'detail', 
                'tgl_berlaku', 
                'created_date', 
                'last_modified_date', 
                'deleted_date',
                'kelaspelayanan_id',
                'jenisobatalkes_id',
                'diskon'
            ], 'safe'],
            [['additional_data'], 'string'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['perda_margin','nama_margin'], 'string', 'max' => 255],
            [['nama_margin'], 'chkNama'],
            [['tgl_berlaku'], 'chkTglBerlakuGroup'],
            [['groupmargin_id'], 'chkTglBerlakuGroup'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'konfigmargin_id' => 'Konfigmargin ID',
            'perda_margin' => 'Perda Margin',
            'nama_margin' => 'Nama',
            'tgl_berlaku' => 'Tgl Berlaku',
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
            'kelaspelayanan_id' => 'Kelas Pelayanan',
            'jenisobatalkes_id' => 'Jenis Obat'
        ];
    }

    public function chkNama($params, $attributes)
    {
        $nama_margin = $this->nama_margin;
        $model = self::find()->where([
            'TRIM(LOWER (nama_margin))' => strtolower($nama_margin),
            'is_deleted' => false
        ])->one();
        if(!empty($model) && $model->konfigmargin_id != $this->konfigmargin_id ){
            $this->addError("nama_margin","Nama Perda Sudah Dipakai");
            return false;
        }

        return true;
    }

    public function chkTglBerlakuGroup($params, $attributes)
    {
        $tgl_berlaku = $this->tgl_berlaku;
        $model = self::find()->where([
            'tgl_berlaku' => $tgl_berlaku,
            'groupmargin_id' => $this->groupmargin_id,
            'kelaspelayanan_id' => $this->kelaspelayanan_id,
            'jenisobatalkes_id' => $this->jenisobatalkes_id,
            'is_deleted' => false
        ])->one();
        if(!empty($model) && $model->konfigmargin_id != $this->konfigmargin_id ){
            $this->addError("tgl_berlaku","Group Margin & Mulai Berlaku Sudah Dipakai");
            $this->addError("groupmargin_id","Group Margin & Mulai Berlaku Sudah Dipakai");
            return false;
        }

        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKonfigMarginDetail()
    {
        return $this->hasMany(KonfigMarginDetail::className(), ['konfigmargin_id' => 'konfigmargin_id']);
    }

    public function getGroupmargin()
    {
        return $this->hasOne(GroupMargin::className(), ['groupmargin_id' => 'groupmargin_id']);
    }

    public function getActiveMargin() 
    {
        $tgl_berlaku = $this->tgl_berlaku;
        $groupmargin_id = $this->groupmargin_id;
        $model = Yii::$app->db->createCommand('
                    SELECT DISTINCT ON (km.groupmargin_id)
                        km.konfigmargin_id, km.groupmargin_id, km.nama_margin, km.tgl_berlaku
                    FROM (
                        SELECT * FROM konfigmargin_k
                        WHERE tgl_berlaku <= now()
                        ORDER BY groupmargin_id ASC, tgl_berlaku DESC
                    ) km')->queryAll();

        foreach($model as $value) {
            if($groupmargin_id == $value['groupmargin_id'] && $tgl_berlaku == $value['tgl_berlaku']) {
                return true;
            }
        }
        return false;
    }

    public function extraFields()
    {
        return [
            'groupmargin_m' => function($item){
                return $item->groupmargin;
            },
            'active_margin' => function($item){
                return $item->activeMargin;
            }
        ];
    }
}
