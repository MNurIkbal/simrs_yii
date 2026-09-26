<?php

/**
 * @Author: Sigit
 * @Date:   2019-01-21 11:58:12
 */

namespace app\modules\pendaftaran\models;

use Yii;

class KonfigPendaftaranForm extends \yii\base\Model
{
    /**
     * {@inheritdoc}
     */
    public $is_pemilihandokter;
    public $reservasi_awal;
    public $reservasi_akhir;
    public $is_validasipendaftaranrj;
    public $is_validasipendaftaranri;
    public $is_validasipendaftaranrd;
    public $konfigsystem_id;
    public $dash_kamarheader;
    public $dash_kamardetail;
    public $dash_kamarfooter;
    public $dash_logo;
    public $is_limit_tagihan;
    public $support_multipayer;
    public $is_set_igdkeri;
    public $is_reservasi;
    public $is_slot_dokter;
    public $is_sep_mandatory_on_edit;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_pemilihandokter', 'is_limit_tagihan', 'is_set_igdkeri', 'support_multipayer', 'is_reservasi', 'is_slot_dokter', 'is_sep_mandatory_on_edit',], 'boolean'],
            [['dash_kamarheader'], 'string', 'max' => 30],
            [['dash_kamardetail'], 'string', 'max' => 55],
            [['dash_kamarfooter', 'dash_logo'], 'string', 'max' => 255]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'is_pemilihandokter' => Yii::t('fe', 'Set Pemilihan Dokter'),
            'reservasi_awal' => Yii::t('fe', 'Set Reservasi Awal'),
            'reservasi_akhir' => Yii::t('fe', 'Set Reservasi Akhir'),
            'is_validasipendaftaranrj' => Yii::t('fe', 'Set Validasi Pendaftaran Rawat Jalan'),
            'is_validasipendaftaranri' => Yii::t('fe', 'Set Validasi Pendaftaran Rawat Inap'),
            'is_validasipendaftaranrd' => Yii::t('fe', 'Set Validasi Pendaftaran Rawat Darurat'),
            'konfigsystem_id' => 'Konfigsystem ID',
            'dash_kamarheader' => 'Header',
            'dash_kamardetail' => 'Detail Header',
            'dash_kamarfooter' => 'Footer',
            'dash_logo' => 'Logo Header',
            'support_multipayer' => 'Support Multipayer',
            'is_set_igdkeri' => 'Konfig IGD rujuk rawat inap',
            'is_reservasi' => 'Pengambilan Kuota Reservasi',
            'is_slot_dokter' => 'Konfig Sloting Dokter',
            'is_sep_mandatory_on_edit' => 'Konfig SEP Mandatory pada edit pendaftaran',
        ];
    }

    public function attributeHints()
    {
        return [
            'dash_kamarheader' => Yii::t('fe', 'Teks header maksimal 30 karakter'),
            'dash_kamardetail' => Yii::t('fe', 'Teks header detail maksimal 55 karakter'),
        ];
    }
}
