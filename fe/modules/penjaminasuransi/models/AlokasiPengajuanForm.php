<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-09-26 10:35:41
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-28 13:29:17
 */

namespace Doco\penjaminasuransi\models;
use app\components\DocoConstants;

class AlokasiPengajuanForm extends \yii\base\Model
{
    public $pengajuanklaim_id;
    public $no_pengajuanklaim;
    public $tgl_pengajuanklaim;
    public $total_pengajuan;
    public $total_terbayar;
    public $pembayaran;
    public $total_sisapiutang;
    public $status_pengajuan;

    public function rules()
    {
        return [
             [[
                'pengajuanklaim_id', 
                'total_pengajuan', 
                'total_terbayar',
                'pembayaran',
                'total_sisapiutang',
                'status_pengajuan',
                'tgl_pengajuanklaim',
            ], 'required', 'message'=>'{attribute} '.\Yii::t('fe', 'Tidak boleh kosong!')],
             [['no_pengajuanklaim','status_pengajuan'],'safe'],
             [['pengajuanklaim_id'], 'cekDuplicate'],
             [['pembayaran'], 'checkPembayaran'],
             [['status_pengajuan'], 'checkStatus'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'no_pengajuanklaim'=> \Yii::t('fe', 'No Ajuan'),
            'total_pengajuan'=> \Yii::t('fe', 'Total Pengajuan'),
            'total_terbayar'=> \Yii::t('fe', 'Telah Bayar'),
            'pembayaran'=> \Yii::t('fe', 'Pembayaran'),
            'total_sisapiutang'=> \Yii::t('fe', 'Sisa Piutang'),
            'pengajuanklaim_id'=> \Yii::t('fe', 'No Ajuan'),
        ];
    }

    public function checkPembayaran()
    {
        if ($this->pembayaran > $this->total_pengajuan) {
            $this->addError('pembayaran', 'Pembayaran tidak boleh lebih besar dari total pengajuan');
        }
    }

    public function checkStatus()
    {
        $session = \Yii::$app->session;
        $statusAble = [
            DocoConstants::BELUM_PENGAJUAN,
            DocoConstants::SUDAH_PEMBAYARAN
        ];
        if (!in_array($this->status_pengajuan, $statusAble)) {
            $this->addError('status_pengajuan', 'No Pengajuan sedang dalam proses');
        }
    }

    public function cekDuplicate()
    {
        $session = \Yii::$app->session;
        $sessionData = $session['data-penerimaan'];
        if(count($sessionData) > 0){
            foreach ($sessionData as $key => $value) {
                if ($value['pengajuanklaim_id'] == $this->pengajuanklaim_id){
                    $this->addError('no_pengajuanklaim', 'No Pengajuan Sudah Pernah Diinputkan!');
                }
            }
        }
    }
}