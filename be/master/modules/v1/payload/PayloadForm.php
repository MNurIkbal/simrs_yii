<?php 
namespace app\modules\v1\payload;

use Yii;

class PayloadForm extends \Doco\components\DocoBaseModel
{
    public $jenisantrian_id;
    public $jadwalbukapoli_id;
    public $instalasi_id;
    public $ruangan_id;
    public $tipepaket_kode;
    public $tipepaket_nama;
    public $tipepaket_namalainnya;
    public $is_active;
    public $keterangan_tipepaket;
    public $is_mcu;

    protected $xssProtected = [
        'tipepaket_kode',
        'tipepaket_nama',
        'tipepaket_namalainnya',
        'keterangan_tipepaket'
    ];
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tipepaket_kode', 'tipepaket_nama', 'tipepaket_namalainnya'], 'required', 'on' => 'paket'],
            [['tipepaket_kode'], 'string', 'max' => 20],
            [['tipepaket_nama', 'tipepaket_namalainnya'], 'string', 'max' => 50],
            [['keterangan_tipepaket'], 'string'],
            [['is_active', 'is_mcu'], 'boolean'],
            [[
                'jenisantrian_id',
                'jadwalbukapoli_id',
                'instalasi_id',
                'ruangan_id',
                'tipepaket_kode',
                'tipepaket_nama',
                'tipepaket_namalainnya',
                'is_active',
                'keterangan_tipepaket',
                'is_mcu',
            ],'safe'],
            [[
                'jenisantrian_id',
                'jadwalbukapoli_id',
                'instalasi_id',
                'ruangan_id',
            ], 'integer']
        ];
    }

    public function attributeLabels()
    {
        return [
            'tipepaket_nama' => Yii::t('app', 'Nama Paket'),
            'tipepaket_kode' => Yii::t('app', 'Kode Paket'),
            'tipepaket_namalainnya' => Yii::t('app', 'Nama Lainnya'),
            'keterangan_tipepaket' => Yii::t('app', 'Keterangan'),
        ];
    }
}