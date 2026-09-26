<?php

/**
** @author yaya
**/
namespace Doco\dcms\models;

use Yii;
use app\components\DocoBaseModel;

class DokumenTercetakForm extends DocoBaseModel
{
    public $docmapping_id;
    public $docheader_id;
    public $docbody_id;
    public $docfooter_id;
    public $controller;
    public $doc_key;
    public $nama_doc;
    public $kode_dokumen;
    public $nama_dokumen;
    public $kertas_id;
    public $modul_id;
    public $menu_id;
    public $sub_menu_id;
    public $kode_doc;

    public $docbody_text;

    protected $xssProtected = [
        'kode_doc',
        'nama_doc'
    ];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['docheader_id','docfooter_id','nama_doc',
                'kode_doc','kertas_id'], 'required'],
            [['modul_id','menu_id','sub_menu_id','docbody_text','kode_doc','doc_key'],'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'docmapping_id' => Yii::t('fe','Kode kertas'),
            'docheader_id' => Yii::t('fe','Header kertas'),
            'docbody_id' => Yii::t('fe','Panjang'),
            'docfooter_id' => Yii::t('fe','Footer Kertas'),
            'controller' => Yii::t('fe','Controller'),
            'kode_dokumen' => Yii::t('fe','Kode Dokumen'),
            'doc_key' => Yii::t('fe','Kode Dokumen'),
            'nama_dokumen' => Yii::t('fe','Nama Dokumen'),
            'nama_doc' => Yii::t('fe','Nama Dokumen'),
            'kertas_id' => Yii::t('fe','Jenis Kertas'),
            'modul_id' => Yii::t('fe','Service'),
            'menu_id' => Yii::t('fe','Menu'),
            'sub_menu_id' => Yii::t('fe','Dokumen'),
        ];
    }
}