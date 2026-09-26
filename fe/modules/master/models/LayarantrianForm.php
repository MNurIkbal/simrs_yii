<?php

namespace app\modules\master\models;

use Yii;

class LayarantrianForm extends \yii\base\Model
{
    public $layarantrian_id;
    public $layarantrian_jenis;
    public $layarantrian_nama;
    public $layarantrian_judul;
    public $layarantrian_fungsi;
    public $layarantrian_latarbelakang;
    public $layarantrian_maxitem;
    public $layarantrian_itemhigh;
    public $layarantrian_itemwidth;
    public $layarantrian_intrefresh;
    public $is_active;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'layarantrian_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // [['layarantrian_jenis', 'layarantrian_nama', 'layarantrian_judul'], 'required'],
            // [['layarantrian_latarbelakang', 'additional_data'], 'string'],
            [['layarantrian_latarbelakang'], 'file', 'extensions'=>'jpg, gif, png'],
            [['layarantrian_latarbelakang'], 'file', 'maxSize'=>'100000'],
            // [['layarantrian_maxitem', 'layarantrian_itemhigh', 'layarantrian_itemwidth', 'layarantrian_intrefresh'], 'integer'],
            // [['layarantrian_jenis', 'layarantrian_nama'], 'string', 'max' => 100],
            // [['layarantrian_judul'], 'string', 'max' => 200],
            // [['layarantrian_fungsi'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'layarantrian_id' => Yii::t('fe', 'layarantrian_id'),
            'layarantrian_jenis' => Yii::t('fe', 'layarantrian_jenis'),
            'layarantrian_nama' => Yii::t('fe', 'layarantrian_nama'),
            'layarantrian_judul' => Yii::t('fe', 'layarantrian_judul'),
            'layarantrian_fungsi' => Yii::t('fe', 'layarantrian_fungsi'),
            'layarantrian_latarbelakang' => Yii::t('fe', 'layarantrian_latarbelakang'),
            'layarantrian_maxitem' => Yii::t('fe', 'layarantrian_maxitem'),
            'layarantrian_itemhigh' => Yii::t('fe', 'layarantrian_itemhigh'),
            'layarantrian_itemwidth' => Yii::t('fe', 'layarantrian_itemwidth'),
            'layarantrian_intrefresh' => Yii::t('fe', 'layarantrian_intrefresh'),
            'additional_data' => Yii::t('fe','Additional Data'),
            'created_date' => Yii::t('fe','Created Date'),
            'created_by' => Yii::t('fe','Created By'),
            'modified_count' => Yii::t('fe','Modified Count'),
            'last_modified_date' => Yii::t('fe','Last Modified Date'),
            'last_modified_by' => Yii::t('fe','Last Modified By'),
            'is_deleted' => Yii::t('fe','Is Deleted'),
            'is_active' => Yii::t('fe','Is Active'),
            'deleted_date' => Yii::t('fe','Deleted Date'),
            'deleted_by' => Yii::t('fe','Deleted By'),
        ];
    }

    public function attributes()
    {
        $list = parent::attributes();
        $list[] = 'layarantrian_jenis';
        return $list;
    }
}
