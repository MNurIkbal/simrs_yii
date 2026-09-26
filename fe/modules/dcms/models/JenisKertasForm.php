<?php

/**
** @author yaya
**/
namespace Doco\dcms\models;

use Yii;
use app\components\DocoBaseModel;

class JenisKertasForm extends DocoBaseModel
{
    public $kertas_id;
    public $kertas_kode;
    public $kertas_nama;
    public $panjang;
    public $lebar;
    public $batas_kiri;
    public $batas_kanan;
    public $batas_atas;
    public $batas_bawah;
    public $ppi;

    protected $xssProtected = [
        'kertas_kode',
        'kertas_nama'
    ];
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kertas_kode','kertas_nama','panjang','lebar','batas_kiri','batas_kanan','batas_atas','batas_bawah'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kertas_kode' => Yii::t('fe','Kode kertas'),
            'kertas_nama' => Yii::t('fe','Nama kertas'),
            'panjang' => Yii::t('fe','Panjang'),
            'lebar' => Yii::t('fe','Lebar'),
            'batas_kiri' => Yii::t('fe','Batas kiri'),
            'batas_kanan' => Yii::t('fe','Batas kanan'),
            'batas_atas' => Yii::t('fe','Batas atas'),
            'batas_bawah' => Yii::t('fe','Batas bawah'),
            'ppi' => Yii::t('fe','Ppi'),
        ];
    }
}