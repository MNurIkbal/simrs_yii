<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "tabularlist_m".
 *
 * @property int $tabularlist_id
 * @property string $tabularlist_chapter
 * @property string $tabularlist_block
 * @property string $tabularlist_title
 * @property string $tabularlist_revisi
 * @property string $tabularlist_versi
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
 * @property DtdM[] $dtdMs
 */
class TabularChapterForm extends \yii\base\Model
{

  public $tabularlist_id;
  public $tabularlist_chapter;
  public $tabularlist_block;
  public $tabularlist_title;
  public $tabularlist_revisi;
  public $tabularlist_versi;
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
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tabularlist_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tabularlist_chapter', 'tabularlist_block'], 'required'],
            [['tabularlist_title', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['tabularlist_chapter', 'tabularlist_block', 'tabularlist_revisi', 'tabularlist_versi'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tabularlist_id' => 'Tabularlist ID',
            'tabularlist_chapter' => 'Tabularlist Chapter',
            'tabularlist_block' => 'Tabularlist Block',
            'tabularlist_title' => 'Tabularlist Title',
            'tabularlist_revisi' => 'Tabularlist Revisi',
            'tabularlist_versi' => 'Tabularlist Versi',
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
    public function getDtdMs()
    {
        return $this->hasMany(DtdM::className(), ['tabularlist_id' => 'tabularlist_id']);
    }
}
