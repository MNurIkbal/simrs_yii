<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Kamar Form
 * @copyright 21 Mei 2018 aweutist
 */

namespace Doco\master\models;
use app\components\DocoBaseModel;

use Yii;
use yii\db\Query;
class KamarForm extends DocoBaseModel
{
    public $kamarruangan_nokamar;
    public $ruangan_id;
    public $kelaspelayanan_id;
    public $kamarruangan_jenis;
    public $kelas_pelayanan_id;
    public $jeniskasuspenyakit_id;
    public $kamarruangan_deskripsi;
    public $is_active;
    public $kamarruangan_kode;

    protected $xssProtected = [
        'kamarruangan_nokamar',
        'kamarruangan_deskripsi'
    ];

    /**
     * @todo Rule for Form Obat Alkes Jenis Kasus Penyakit
     * @return void
     */
    public function rules()
    {
        return [
            [['kamarruangan_nokamar'], 'trimWhitespace'],
            [['kamarruangan_nokamar'], 'string', 'max' => 25],
            [['kamarruangan_kode'], 'string', 'max' => 20],
            [['kamarruangan_deskripsi'], 'string'],
            [['kamarruangan_deskripsi', 'kamarruangan_kode'], 'safe'],
            [['ruangan_id', 'kelaspelayanan_id', 'kamarruangan_jenis', 'kamarruangan_nokamar','jeniskasuspenyakit_id'], 'required'],
        ];
    }

    public function trimWhitespace(){
        $kamarruangan_nokamar = $this->kamarruangan_nokamar;
        if (strpos(substr($kamarruangan_nokamar, 0, 1), ' ') !== FALSE) {
            $this->addError('kamarruangan_nokamar', 'Nama Kamar mengandung spasi di awal kata');
            return false;
        }
        return true;
    }


    /**
     * @todo for attribute label form
     */
    public function attributeLabels()
    {
        return [
            'ruangan_id' => \Yii::t('fe', 'Ruangan'),
            'kelaspelayanan_id' => \Yii::t('fe', 'Kelas pelayanan'),
            'kamarruangan_jenis' => \Yii::t('fe', 'Jenis kamar'),
            'ruangan_id' => \Yii::t('fe', 'Ruangan'),
            'kamarruangan_nokamar' => \Yii::t('fe', 'Nama Kamar'),
            'jeniskasuspenyakit_id' => \Yii::t('fe', 'Jenis Kasus Penyakit'),
            'kamarruangan_deskripsi' => \Yii::t('fe', 'Keterangan'),
            'kamarruangan_kode' => \Yii::t('fe', 'Kamar Ruangan Kode'),
            'is_active' => \Yii::t('fe', 'Status Kamar'),
        ];
    }

}
