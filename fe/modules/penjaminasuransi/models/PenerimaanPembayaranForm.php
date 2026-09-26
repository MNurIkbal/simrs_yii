<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-09-25 17:39:34
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-26 10:44:31
 */
namespace Doco\penjaminasuransi\models;

use app\components\DocoConstants;

class PenerimaanPembayaranForm extends \yii\base\Model
{
    public $carabayar_id;
    public $penjamin_id;
    public $tgl_terimabayarklaim;
    public $no_terimabayarklaim;
    public $total_terimabayar;
    public $pegawaipenerima_id;
    public $catatan;
    public $is_nontunai;
    public $pemilik_rekening;
    public $bank;
    public $no_rekening;
    public $data_pengajuan;

    public function rules()
    {
        return [
            [['no_terimabayarklaim', 'tgl_terimabayarklaim', 'total_terimabayar','carabayar_id','penjamin_id','pegawaipenerima_id','data_pengajuan'], 'required','message'=>'{attribute} '.\Yii::t('fe', 'Tidak boleh kosong!')],
            [['is_nontunai'], 'cekNontunai'],
            [['total_terimabayar'], 'validTotal'],
            [[
                'pemilik_rekening', 
                'bank', 'no_rekening', 
                'catatan',
                'total_terimabayar',
                'data_pengajuan',
            ], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'carabayar_id'=>\Yii::t('fe', 'Cara Bayar'),
            'penjamin_id'=>\Yii::t('fe', 'Penjamin'),
            'tgl_terimabayarklaim'=>\Yii::t('fe', 'Tanggal Penerimaan'),
            'no_terimabayarklaim'=>\Yii::t('fe', 'No Pembayaran'),
            'total_terimabayar'=>\Yii::t('fe', 'Jumlah Penerimaan Pembayaran'),
            'pegawaipenerima_id'=>\Yii::t('fe', 'Penerima Dana'),
            'catatan'=>\Yii::t('fe', 'Catatan'),
            'is_nontunai'=>\Yii::t('fe', 'Pembayaran Non Tunai'),
            'pemilik_rekening'=>\Yii::t('fe', 'Pemilik Rekening'),
            'bank'=>\Yii::t('fe', 'Nama Bank'),
            'no_rekening'=>\Yii::t('fe', 'No Rekening'),
        ];
    }

    public function cekNontunai()
    {
        if($this->is_nontunai){
            if(empty($this->pemilik_rekening)){
                $this->addError('pemilik_rekening', 'Pemilik Rekening Tidak Boleh Kosong');
            }
            if(empty($this->bank)){
                $this->addError('bank', 'Bank Tidak Boleh Kosong');
            }
            if(empty($this->no_rekening)){
                $this->addError('no_rekening', 'No Rekening Tidak Boleh Kosong');
            }
        }
    }

    public function validTotal()
    {
        if (empty($this->total_terimabayar)) {
            $this->addError('total_terimabayar', 'total penerimaan tidak boleh 0');
        }
        if (!empty($this->data_pengajuan) && is_array($this->data_pengajuan)) {
            $total = 0;
            if($this->carabayar_id != DocoConstants::CARA_BAYAR_BPJS) {
                foreach ($this->data_pengajuan as $value) {
                    $total += isset($value['pembayaran']) ? $value['pembayaran'] : 0;
                }

                if ($total != $this->total_terimabayar) {
                    $this->addError('total_terimabayar', 'total penerimaan harus sama dengan alokasi pengajuanssss');
                }
            }
        }
    }
}