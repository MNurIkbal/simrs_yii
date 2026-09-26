<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-10 10:54:57
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-08-29 10:15:20
 */

namespace Doco\igd\models;

use Yii;
use app\components\DocoHelpers;

class IntraPegawaiOperasiForm extends \yii\base\Model
{
    public $pasienmasukpenunjang_id;
    public $inpostoperasi_id;
    public $posisi_tim;
    public $pegawai_id;
    public $pegawai_nama;
    public $posisi_tim_nama;

    public function rules()
    {
        return [
            [['pegawai_id', 'posisi_tim'], 'required', 'message'=>'{attribute} Tidak Boleh Kosong'],
            [['pasienmasukpenunjang_id', 'inpostoperasi_id','pegawai_nama','posisi_tim_nama'], 'safe'],
            [['pegawai_id'], 'checkAvailable']
        ];
    }

    public function attributeLabels()
    {
        return [
            'posisi_tim'=>\Yii::t('fe', 'Posisi tim operasi'),
            'pegawai_id'=>\Yii::t('fe', 'Nama pegawai'),
        ];
    }

    public function checkAvailable()
    {
        $pegawai = $this->pegawai_id;
        $posisi  = $this->posisi_tim;
        $getCache = Yii::$app->cache;
        $getCache = $getCache->get('pegawaioperasi-'.DocoHelpers::encrypt($this->inpostoperasi_id).'-'.DocoHelpers::encrypt($this->pasienmasukpenunjang_id));
        if($getCache){
            foreach ($getCache as $key => $value) {
                if($value['pegawai_id'] == $pegawai && $value['posisi_tim'] == $posisi){
                    $this->addError("pegawai_id","Pegawai dengan posisi yang sama sudah diinputkan");
                    return false;
                }
            }
        }
        return true;
    }
}