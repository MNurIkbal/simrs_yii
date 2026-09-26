<?php

namespace app\modules\master\models;

use Yii;

/**
 *
 * @property string $jeniskasuspenyakit_nama
 * @property string $jeniskasuspenyakit_namalainnya
 * @property bool $is_active

 */
class JenisKasusPenyakitForm extends\yii\base\Model
{
    public $jeniskasuspenyakit_nama;
    public $jeniskasuspenyakit_namalainnya;
    public $is_active;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jeniskasuspenyakit_nama'], 'required'],
            [['is_active'], 'boolean'],
            [['jeniskasuspenyakit_nama', 'jeniskasuspenyakit_namalainnya'], 'string', 'max' => 100],
            [['jeniskasuspenyakit_nama'], 'trimWhitespace'],
        ];
    }

    public function trimWhitespace(){
        $jeniskasuspenyakit_nama = $this->jeniskasuspenyakit_nama;
        $return = true;
        if (strpos(substr($jeniskasuspenyakit_nama, 0, 1), ' ') !== FALSE) {
            $this->addError('jeniskasuspenyakit_nama', 'Nama mengandung spasi di awal kata');
            $return = false;
        }
        
        return $return;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jeniskasuspenyakit_nama' => Yii::t('fe','Nama'),
            'jeniskasuspenyakit_namalainnya' => Yii::t('fe','Nama Lainnya'),
            'jeniskasuspenyakit_urutan' => Yii::t('fe','Urutan'),
            'is_active' => Yii::t('fe','Status'),
        ];
    }
}
