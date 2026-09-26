<?php

/**
 * @Author: Sunarko
 * @Date:   2018-07-25 10:28:24
 * @Last Modified by:
 * @Last Modified time: 
 **/

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "kondisikeluar_v".
 *
 * @property int $kondisikeluar_id
 * @property int $carakeluar_id
 * @property string $carakeluar_kode
 * @property string $carakeluar_nama
 * @property string $kondisikeluar_kode
 * @property string $kondisikeluar_nama
 * @property string $kondisikeluar_namalain
 * @property int $carakeluar_urutan
 * @property string $catatan
 * @property bool $is_active
 */

class KondisiKeluarForm extends \yii\base\Model
{
    // Public variable
    public $kondisikeluar_id;
    public $carakeluar_id;
    public $carakeluar_kode;
    public $carakeluar_nama;
    public $kondisikeluar_kode;
    public $kondisikeluar_nama;
    public $kondisikeluar_namalain;
    public $carakeluar_urutan;
    public $catatan;
    public $is_active;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['carakeluar_id','kondisikeluar_kode','kondisikeluar_nama'], 'required'],
            [['kondisikeluar_kode','kondisikeluar_nama'], 'trimWhitespace'],
            [['kondisikeluar_id', 'carakeluar_id', 'carakeluar_urutan'], 'default', 'value' => null],
            [['kondisikeluar_id', 'carakeluar_id', 'carakeluar_urutan'], 'integer'],
            [['catatan'], 'string'],
            [['is_active'], 'boolean'],
            [['carakeluar_kode', 'carakeluar_nama', 'kondisikeluar_kode', 'kondisikeluar_nama', 'kondisikeluar_namalain'], 'string', 'max' => 100],
        ];
    }

    public function trimWhitespace(){
        $kondisikeluar_kode = $this->kondisikeluar_kode;
        $kondisikeluar_nama = $this->kondisikeluar_nama;
        $return = true;
        if (strpos(substr($kondisikeluar_kode, 0, 1), ' ') !== FALSE) {
            $this->addError('kondisikeluar_kode', 'Kode mengandung spasi di awal kata');
            $return = false;
        }
        if (strpos(substr($kondisikeluar_nama, 0, 1), ' ') !== FALSE) {
            $this->addError('kondisikeluar_nama', 'Kondisi Keluar mengandung spasi di awal kata');
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
            'kondisikeluar_id' => 'Kondisikeluar ID',
            'carakeluar_id' => 'Cara Keluar',
            'carakeluar_kode' => 'Carakeluar Kode',
            'kondisikeluar_kode' => 'Kode',
            'carakeluar_nama' => 'Cara Keluar',
            'kondisikeluar_nama' => 'Kondisi Keluar',
            'kondisikeluar_namalain' => 'Nama Lainnya',
            'carakeluar_urutan' => 'Carakeluar Urutan',
            'catatan' => 'Catatan',
            'is_active' => 'Aktif',
        ];
    }
}
?>