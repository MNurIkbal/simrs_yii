<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-10 10:54:57
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-08-29 10:15:20
 */

namespace Doco\bedah\models;

use Yii;
use app\components\DocoHelpers;

class IntraPegawaiOperasiForm extends \yii\base\Model
{
    public $pasienmasukpenunjang_id;
    public $inpostoperasi_id;
    public $posisi_tim;
    public $prosentase;
    public $slug_posisi;
    public $pegawai_id;
    public $pegawai_nama;
    public $posisi_tim_nama;
    public $daftartindakan_id;
    public $kegiatanoperasi_id;
    public $golonganoperasi_id;
    public function rules()
    {
        return [
            [['pegawai_id', 'posisi_tim'], 'required', 'message' => '{attribute} Tidak Boleh Kosong'],
            [['pasienmasukpenunjang_id', 'inpostoperasi_id', 'pegawai_nama', 'posisi_tim_nama', 'daftartindakan_id', 'kegiatanoperasi_id', 'golonganoperasi_id'], 'safe'],
            [['pegawai_id'], 'checkAvailable']
        ];
    }

    public function attributeLabels()
    {
        return [
            'posisi_tim' => \Yii::t('fe', 'Posisi Tim Operasi'),
            'pegawai_id' => \Yii::t('fe', 'Nama Pegawai'),
            'prosentase' => \Yii::t('fe', 'Prosentase'),
            'slug_posisi' => \Yii::t('fe', 'Slug'),
        ];
    }

    public function checkAvailable()
    {
        $pegawai = $this->pegawai_id;
        $posisi  = $this->posisi_tim;
        $daftartindakan_id = $this->daftartindakan_id;
        $getCache = Yii::$app->cache;
        $getCache = $getCache->get('pegawaioperasi-' . DocoHelpers::encrypt($this->inpostoperasi_id) . '-' . DocoHelpers::encrypt($this->pasienmasukpenunjang_id));
        if (!empty($getCache[$daftartindakan_id])) {
            $getCache = $getCache[$daftartindakan_id];
            foreach ($getCache as $key => $value) {
                if($value['pegawai_id'] == $pegawai) {
                    $this->addError("pegawai_id", "Pegawai yang sama sudah diinputkan");
                    return false;
                }
                if ($value['posisi_tim'] == $posisi) {
                    $this->addError("posisi_tim", "Posisi yang sama pada tindakan ini sudah diinputkan");
                    return false;
                }
            }
        }
        return true;
    }
}
