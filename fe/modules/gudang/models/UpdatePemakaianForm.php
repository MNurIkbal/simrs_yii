<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-04-03 15:03:20
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-04-06 09:59:48
 */
namespace Doco\gudang\models;

use Yii;

class UpdatePemakaianForm extends \yii\base\Model
{
	public $tgl_pemakaian;
	public $barang;
	public $no_pemakaian;
	public $satuan;
	public $qty;
	public $stok;
	public $pemakaianbarang_id;
	public $keterangan;	

	public function rules()
	{
		return [
            [[
            	'no_pemakaian',
                'tgl_pemakaian',
                'barang',                
                'satuan',
                'qty',
                'pemakaianbarang_id',
            ], 'required'],
            ['qty','validQty'],            
            [['stok','keterangan'],'safe']
        ];
	}
	public function validQty($attribute){
		if($this->qty > $this->stok){
			$this->addError($attribute, Yii::t('fe','Stok tidak mencukupi'));
		}
	}

	public function attributeLabels()
    {
        return [
            'tgl_pemakaian' => Yii::t('fe','tanggal pemakaian'),
            'no_pemakaian' => Yii::t('fe','Nomor pemakaian'),
            'barang' => Yii::t('fe','Nama barang'),            
            'satuan' => Yii::t('fe','satuan'),
            'qty' => Yii::t('fe','Qty'),
            'stok'=> Yii::t('fe','Stok'),
            'keterangan'=> Yii::t('fe','Keterangan'),
        ];
    }	

}