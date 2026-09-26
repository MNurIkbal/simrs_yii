<?php
namespace app\modules\master\models;

use Yii;

class PendidikanForm extends \yii\base\Model
{

    public $indexing_id;
    public $pendidikan_urutan;
    public $pendidikan_nama;
    public $pendidikan_namalainnya;   
    public $is_active;   

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[ 'pendidikan_urutan', 'pendidikan_nama', 'pendidikan_namalainnya', 'is_active'], 'default', 'value' => null],
            [['is_active'], 'boolean'],
            [['indexing_id'], 'safe'],
            [['indexing_id', 'pendidikan_urutan'], 'integer'],
            [['pendidikan_urutan', 'pendidikan_nama'], 'required'],
            [['pendidikan_nama', 'pendidikan_namalainnya'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'indexing_id' => Yii::t('fe','Indexing'),
            'pendidikan_urutan' => Yii::t('fe','Pendidikan Urutan'),
            'pendidikan_nama' => Yii::t('fe','Nama Pendidikan'),
            'pendidikan_namalainnya' => Yii::t('fe','Nama Lainnya'),           
            'is_active' => Yii::t('fe','Status'),           
        ];
    }
}