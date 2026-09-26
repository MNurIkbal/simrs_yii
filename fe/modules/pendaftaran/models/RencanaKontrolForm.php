<?php

namespace app\modules\pendaftaran\models;

use Yii;

/**
 * Validation model for rencan kontrol form.
 *
 * @property int $rencanakontrol_id
 * @property int $pendaftaran_id
 * @property int $bpjs_id
 * @property string $no_spri
 * @property string $jenis_rencana
 * @property string $no_sep
 * @property string $no_kartu
 * @property string $nama
 * @property string $nosuratkontrol
 * @property string $nama_spesialis
 * @property string $dokterdpjp_kode
 * @property string $dokterdpjp_nama
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 * @property string $tgl_rencanakontrol
 * @property string $jenis_pelayanan
 */

class RencanaKontrolForm extends \yii\base\Model
{
    public $pendaftaran_id;
    public $bpjs_id;
    public $no_spri;
    public $jenis_pelayanan;
    public $jenis_pelayanan_nama;
    public $jenis_rencana;
    public $no_sep;
    public $no_kartu;
    public $nama;
    public $nosuratkontrol;
    public $nama_spesialis;
    public $dokterdpjp_kode;
    public $dokterdpjp_nama;
    public $additional_data;
    public $tgl_rencanakontrol;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;
    public $tgl_renacan_inap;
    public $user;
    public $kode_poli;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'nama_spesialis',
                    'jenis_rencana',
                    'jenis_pelayanan'
                ], 'required','on' => 'update'
            ],
            [
                [
                    // 'no_sep', 'jenis_rencana', 'tgl_rencanakontrol', 
                    // 'kamartempattidur_no_tempattidur'
                    'nama_spesialis'
                ], 
                'required'
            ],
            [
                [
                    'pendaftaran_id', 'bpjs_id', 'jenis_rencana', 'kode_poli',
                    'no_sep', 'no_kartu', 'nama', 'nama_spesialis',
                    'nosuratkontrol', 'no_spri', 'dokterdpjp_kode', 'dokterdpjp_nama', 'additional_data', 'tgl_rencanakontrol', 'jenis_pelayanan','tgl_renacan_inap',
                ], 'safe'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'bpjs_id' => Yii::t('fe', 'BPJS'),
            'pendaftaran_id' => Yii::t('fe', 'Pendaftaran'),
            'no_sep' => Yii::t('fe', 'No SEP'),
            'no_kartu' => Yii::t('fe', 'No Kartu'),
            'jenis_rencana' => Yii::t('fe', 'Pilih'),
            'tgl_rencanakontrol' => Yii::t('fe', 'Tanggal Rencana Kontrol/Inap'),
            'jenis_pelayanan' => Yii::t('fe', 'Pelayanan'),
            'nosuratkontrol' => Yii::t('fe', 'No Surat Kontrol'),
            'no_spri' => Yii::t('fe', 'No Surat Kontrol'),
            'nama_spesialis' => Yii::t('fe', 'Spesialis/Sub Spesialis'),
            'dokterdpjp_kode' => Yii::t('fe', 'DPJP Tujuan Kontrol/Inap'),
            'dokterdpjp_nama' => Yii::t('fe', 'DPJP Tujuan Kontrol/Inap'),
            'tgl_renacan_inap' => Yii::t('fe', 'Tanggal Renacana Inap'),
            'jenis_pelayanan_nama' => Yii::t('fe', 'Pelayanan'),
            'kode_poli' => Yii::t('fe', 'Spesialis/Sub Spesialis'),
        ];
    }
}