<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "monitorbpjs_v".
 *
 * @property int $monitorbpjs_id
 * @property int $groupinacbg_id
 * @property string $kelompoktindakan_nama
 * @property string $groupinacbg_nama
 * @property bool $is_active
 * 
 */

class KelompokTindakanBpjsForm extends \yii\base\Model
{
    // Public variable
    public $monitorbpjs_id;
    public $groupinacbg_id;
    public $kelompoktindakan_nama;
    public $groupinacbg_nama;
    public $is_active;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kelompoktindakan_nama', 'groupinacbg_id'], 'required'],
            [['kelompoktindakan_nama'], 'trimWhitespace'],
            [['kelompoktindakan_nama'], 'checkCharacter'],
            // ['kelompoktindakan_nama', 'match', 'pattern' => '/[A-Za-z0-9\/-]$/', 'message' => 'Penulisan nama atau karakter salah.'],
            [['monitorbpjs_id', 'kelompoktindakan_nama'], 'default', 'value' => null],
            [['monitorbpjs_id'], 'integer'],
            [['groupinacbg_id'], 'safe'],
            [['kelompoktindakan_nama'], 'string'],
            [['is_active'], 'boolean'],
        ];
    }

    public function trimWhitespace(){
        $kelompoktindakan_nama = $this->kelompoktindakan_nama;
        $return = true;
        if (strpos(substr($kelompoktindakan_nama, 0, 1), ' ') !== FALSE) {
            $this->addError('kelompoktindakan_nama', 'Kelompok Tindakan mengandung spasi di awal kata');
            $return = false;
        }
        return $return;
    }

    public function checkCharacter($attribute, $params)
    {
        $return = 1;
        if (preg_match('/^[A-Za-z0-9\/-]+[A-Za-z0-9\/ - ]+[A-Za-z0-9\/-]$/', $this->kelompoktindakan_nama)) {
            $return = 0;
        }else{
            $this->addError('kelompoktindakan_nama', $this->kelompoktindakan_nama. ' terdapat karakter yang tidak sesuai');
            $return ++;
        }

        if ($return >= 1) {
            return true;
        }else{
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'monitorbpjs_id' => 'Monitoring BPJS ID',
            'groupinacbg_id' => 'Inacbgs ID',
            'kelompoktindakan_nama' => 'Nama Kelompok Tindakan',
            'is_active' => 'Aktif',
        ];
    }
}
?>