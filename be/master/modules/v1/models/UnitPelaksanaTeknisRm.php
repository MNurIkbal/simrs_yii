<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "unitpelaksanateknis_rm".
 *
 * @property int $upt_id
 * @property int $upt_sync_id
 * @property string $upt_nama
 * @property string|null $created_date
 * @property int|null $created_by
 * @property int|null $modified_count
 * @property string|null $last_modified_date
 * @property int|null $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string|null $deleted_date
 * @property int|null $deleted_by
 */
class UnitPelaksanaTeknisRm extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'unitpelaksanateknis_rm';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['upt_sync_id', 'upt_nama'], 'required'],
            [['upt_id', 'upt_sync_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['upt_nama'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'upt_id' => Yii::t('app', 'ID'),
            'upt_sync_id' => Yii::t('app', 'Sync ID'),
            'upt_nama' => Yii::t('app', 'Nama UPT'),
            'created_date' => Yii::t('app', 'Created Date'),
            'created_by' => Yii::t('app', 'Created By'),
            'modified_count' => Yii::t('app', 'Modified Count'),
            'last_modified_date' => Yii::t('app', 'Last Modified Date'),
            'last_modified_by' => Yii::t('app', 'Last Modified By'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'is_active' => Yii::t('app', 'Is Active'),
            'deleted_date' => Yii::t('app', 'Deleted Date'),
            'deleted_by' => Yii::t('app', 'Deleted By'),
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert && empty($this->upt_id)) {
            $this->upt_id = $this->generateUptId();
        }

        return parent::beforeSave($insert);
    }

    private function generateUptId()
    {
        $db = static::getDb();
        $table = static::tableName();

        $sequenceName = $db->createCommand(
            "SELECT pg_get_serial_sequence(:table, :column)",
            [':table' => $table, ':column' => 'upt_id']
        )->queryScalar();

        if (!empty($sequenceName)) {
            return (int) $db->createCommand(
                "SELECT nextval(:sequence)",
                [':sequence' => $sequenceName]
            )->queryScalar();
        }

        $quotedTable = $db->quoteTableName($table);
        return (int) $db->createCommand("SELECT COALESCE(MAX(upt_id), 0) + 1 FROM {$quotedTable}")->queryScalar();
    }
}
