<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "kasuspenyakitruangan_mp".
 *
 * @property int $ruangan_id
 * @property int $jeniskasuspenyakit_id
 * @property bool $is_active
 */
class KasusPenyakitRuanganForm extends \yii\base\Model
{
    public $ruangan_id;
    public $jeniskasuspenyakit_id;
    public $is_active;

    public $list_jeniskasuspenyakit_id;
    public $list_ruangan_id;
    
    public $ruangan_id_before;
    public $jeniskasuspenyakit_id_before;
    public $type_method;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ruangan_id', 'jeniskasuspenyakit_id'], 'required'],
            [['ruangan_id', 'jeniskasuspenyakit_id'], 'default', 'value' => null],
            [['ruangan_id', 'jeniskasuspenyakit_id'], 'integer'],
            [['is_active'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ruangan_id' => Yii::t('fe','Ruangan'),
            'jeniskasuspenyakit_id' => Yii::t('fe','Jenis Kasus Penyakit'),
            'is_active' => Yii::t('fe','Status'),
        ];
    }

    function attributes()
    {
        $attributes = parent::attributes();
        $attributes[] = 'list_jeniskasuspenyakit_id';
        $attributes[] = 'list_ruangan_id';
        return $attributes;
    }
}
