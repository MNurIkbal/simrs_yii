<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-15 11:25:46
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-07 10:44:40
 */

namespace Doco\bedah\models;

class IntraPenggunaanBmhpForm extends \yii\base\Model
{
    public $pasienmasukpenunjang_id;
    public $inpostoperasi_id;
    public $obatalkes_id;
    public $obatalkes_nama;
    public $persediaan;
    public $tambahan;
    public $terpakai;
    public $sisa;
    public $is_ditagihkan;
    public $ditagihkan;
    public $is_available;
    public $msg;
    public $daftartindakan_id;
    public $operasi_key;

    public function rules()
    {
        return [
            [['obatalkes_id'], 'required', 'message'=>'{attribute} Tidak Boleh Kosong'],
            [['tambahan', 'terpakai'], 'default', 'value'=>0],
            [['tambahan', 'terpakai'], 'number', 'message'=>'{attribute} Harus berupa angka'],
            [['is_available'],'default', 'value'=>1],
            ['sisa', 'cekStok'],
            [['pasienmasukpenunjang_id', 'inpostoperasi_id','obatalkes_nama','persediaan','tambahan', 'terpakai', 'sisa', 'is_ditagihkan', 'ditagihkan','is_available', 'msg', 'daftartindakan_id', 'operasi_key'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'obatalkes_id'=>\Yii::t('fe', 'BMHP'),
            'persediaan'=>\Yii::t('fe', 'Persediaan'),
            'tambahan'=>\Yii::t('fe', 'Tambahan'),
            'terpakai'=>\Yii::t('fe', 'Terpakai'),
            'sisa'=>\Yii::t('fe', 'Sisa'),
            'is_ditagihkan'=>\Yii::t('fe', 'Ditagihkan'),
        ];
    }
    public function cekStok()
    {
    	$persediaan = $this->persediaan;
    	$tambahan = $this->tambahan;
    	$terpakai = $this->terpakai;
    	$total = $persediaan + $tambahan;
    	if($terpakai > $total){
    		$this->addError('sisa', 'Stok tidak mencukupi');
    	}
    }
}