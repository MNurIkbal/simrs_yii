<?php

/**
 * @Author: Sigit
 * @Date:   2019-01-14 18:13:21
 */

namespace app\modules\antrian\models;

use Yii;

class KonfigAntrianForm extends \yii\base\Model
{
    /**
     * {@inheritdoc}
     */
    public $kuota_antrian;
    public $header;
    public $header_detail;
    public $footer;
    public $path_logoheader;
    public $is_slider;
    public $logo;
    public $slides;
    public $url_slider;
    public $is_banyakloket;
    public $is_pilihketeranganpasien;
    public $is_nourut;
    public $is_pisah_cabar;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kuota_antrian', 'header', 'header_detail', 'footer', 'path_logoheader'], 'required'],
            [['kuota_antrian'], 'integer'],
            [['is_slider', 'is_banyakloket', 'is_pilihketeranganpasien', 'is_nourut','is_pisah_cabar'], 'boolean'],
            [['header', 'header_detail', 'footer', 'path_logoheader', 'url_slider'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kuota_antrian' => Yii::t('fe', 'Set Kuota Antrian'),
            'header' => Yii::t('fe', 'Header'),
            'header_detail' => Yii::t('fe', 'Detail Header'),
            'footer' => Yii::t('fe', 'Footer'),
            'path_logoheader' => Yii::t('fe', 'Logo Header'),
            'is_slider' => Yii::t('fe', 'Jenis Slideshow'),
            'logo' => Yii::t('fe', 'Logo Header'),
            'slides' => Yii::t('fe', 'File Slideshow'),
            'is_banyakloket' => Yii::t('fe', 'Jumlah Loket Ditampilkan'),
            'is_pilihketeranganpasien' => Yii::t('fe', 'Keterangan Pasien'),
            'is_nourut' => Yii::t('fe', 'Nomor Urut'),
            'is_pisah_cabar' => Yii::t('fe', 'Pisah Cara Bayar'),
        ];
    }

    public function attributeHints()
    {
        return [
            'header' => Yii::t('fe', 'Teks header maksimal 30 karakter'),
            'header_detail' => Yii::t('fe', 'Teks header detail maksimal 55 karakter'),
        ];
    }
}