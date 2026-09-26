<?php

namespace app\modules\master\models;

use Yii;

/**
 *
 * @property int $jeniskasuspenyakit_id
 * @property int $diagnosa_id
 * @property bool $is_active
 */
class KasusPenyakitDiagnosaForm extends \yii\base\Model
{
    public $jeniskasuspenyakit_id;
    public $diagnosa_id;
    public $list_diagnosa_id;
    public $is_active;

    public $diagnosa_kode;
    public $jeniskasuspenyakit_nama;
    public $jeniskasuspenyakit_id_before;
    public $diagnosa_id_before;
    public $type_method;

    /**
     * @inheritdoc
     */
    /*public static function tableName()
    {
        return 'kasuspenyakitdiagnosa_mp';
    }*/

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jeniskasuspenyakit_id', 'diagnosa_id'], 'required'],
            [['jeniskasuspenyakit_id', 'diagnosa_id'], 'integer'],
            [['is_active'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jeniskasuspenyakit_id' => 'Jenis Kasus Penyakit',
            'diagnosa_id' => 'Diagnosa',
            'is_active' => 'Status',
        ];
    }

    function attributes()
    {
        $attributes = parent::attributes();
        $attributes[] = 'list_diagnosa_id';
        return $attributes;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDiagnosa()
    {
        return $this->hasOne(DiagnosaM::className(), ['diagnosa_id' => 'diagnosa_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJeniskasuspenyakit()
    {
        return $this->hasOne(JeniskasuspenyakitM::className(), ['jeniskasuspenyakit_id' => 'jeniskasuspenyakit_id']);
    }
}
