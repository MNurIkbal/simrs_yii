<?php

/**
 * @Author: Sunarko
 * @Date:   2018-07-25 11:45:24
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-09-26 15:00:41
 **/

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "carakeluar_v".
 *
 * @property int $carakeluar_id
 * @property string $carakeluar_nama
 * @property string $carakeluar_kode
 * @property string $carakeluar_namalain
 * @property int $carakeluar_urutan
 * @property string $catatan
 * @property bool $is_active
 * 
 */

class CaraKeluarForm extends \yii\base\Model
{
    // Public variable
    public $carakeluar_id;
    public $carakeluar_nama;
    public $carakeluar_kode;
    public $carakeluar_namalain;
    public $carakeluar_urutan;
    public $catatan;
    public $is_active;
    public $carakeluarinacbg_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['carakeluar_nama','carakeluar_kode'], 'required'],
            [['carakeluar_nama','carakeluar_kode'], 'trimWhitespace'],
            [['carakeluar_id', 'carakeluar_urutan'], 'default', 'value' => null],
            [['carakeluar_id', 'carakeluar_urutan'], 'integer'],
            [['catatan'], 'string'],
            [['is_active'], 'boolean'],
            [['carakeluarinacbg_id'], 'safe'],
            [['carakeluar_nama', 'carakeluar_namalain', 'carakeluar_kode'], 'string', 'max' => 100],
        ];
    }

    public function trimWhitespace(){
        $carakeluar_kode = $this->carakeluar_kode;
        $carakeluar_nama = $this->carakeluar_nama;
        $return = true;
        if (strpos(substr($carakeluar_kode, 0, 1), ' ') !== FALSE) {
            $this->addError('carakeluar_kode', 'Kode mengandung spasi di awal kata');
            $return = false;
        }
        if (strpos(substr($carakeluar_nama, 0, 1), ' ') !== FALSE) {
            $this->addError('carakeluar_nama', 'Cara Keluar mengandung spasi di awal kata');
            $return = false;
        }
        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'carakeluar_id' => 'Carakeluar ID',
            'carakeluar_nama' => 'Cara Keluar',
            'carakeluar_namalain' => 'Nama lainnya',
            'carakeluar_kode' => 'Kode',
            'carakeluar_urutan' => 'Urutan',
            'catatan' => 'Catatan',
            'is_active' => 'Aktif',
        ];
    }
}
?>