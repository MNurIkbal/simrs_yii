<?php

namespace app\modules\kasir\models;

use Yii;

class ClosingKasir extends \yii\base\Model
{
    
    public $total_closing;
    public $total_uang_pecahan;
    public $pecahan;
    public $qty;
    public $pegawai;
    public $cache_pembayaran;
    public $cache_closing;
    public $total_uang;
    public $start_date;
    public $end_date;
    public $saldo_awal;
    public $shift_id;
    public $total_tagihan;
    public $tunai;
    public $nontunai;
    public $dijamin;
    public $catatan;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'total_closing', 
                    'total_uang_pecahan', 
                    'pecahan', 
                    'qty', 
                    'pegawai',
                    'cache_pembayaran',
                    'cache_closing',
                    'total_uang',
                    'saldo_awal',
                    'shift_id',
                    'total_tagihan',
                    'tunai',
                    'nontunai',
                    'dijamin',
                    'catatan'
                ], 'safe'
            ],
            [['qty'],'checkQty','on' => 'set-closing'],
            [['saldo_awal', 'shift_id', 'catatan'], 'required'],
            [['pecahan','qty', 'pegawai'], 'required','on' => 'set-closing'],
            [['total_closing','total_uang_pecahan','pegawai'], 'required','on' => 'save-closing'],
            [['total_closing'],'checkValidasion','on' => 'save-closing']
        ];
    }

    public function checkQty($attribute, $params)
    {
        if ($this->qty <= 0) {
            $this->addError('qty',Yii::t('fe','Qty tidak boleh 0'));
        }
    }

    public function checkValidasion($attribute, $params)
    {
        $request = Yii::$app->request;
        $total = $this->total_closing;
        $uang = $this->total_uang_pecahan;
        if ($total != $uang) {
            $this->addError('total_uang_pecahan',Yii::t('fe','Total uang pecahan harus balance'));
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'saldo_awal' => Yii::t('fe', 'Saldo Awal'),
            'total_tagihan' => Yii::t('fe', 'Total Tagihan'),
            'tunai' => Yii::t('fe', 'Total Tunai'),
            'nontunai' => Yii::t('fe', 'Total Non-Tunai'),
            'dijamin' => Yii::t('fe', 'Total Dijamin'),
            'catatan' => Yii::t('fe', 'Catatan'),
            'total_uang_pecahan' => Yii::t('fe', 'Total uang pecahan'),
            'pecahan' => Yii::t('fe', 'Pecahan'),
            'qty' => Yii::t('fe', 'Qty'),
            'pegawai' => Yii::t('fe', 'Pegawai mengetahui'),
        ];
    }
}