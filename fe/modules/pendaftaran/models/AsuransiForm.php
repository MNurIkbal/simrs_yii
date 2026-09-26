<?php
namespace app\modules\pendaftaran\models;

use Yii;
use app\components\DocoBaseModel;

class AsuransiForm extends DocoBaseModel
{
    const SCENARIO_IGD = 'scemario-igd';

    public $nokartuasuransi;
    public $namapemilikasuransi;
    public $nomorpokokperusahaan;
    public $kelastanggungan_id;
    public $namaperusahaan;
    public $tgl_konfirmasi;
    public $status_konfirmasi;
    public $asuransipasien_id;
    public $masaberlakukartu;
    public $nama_asuransi;
    public $penjamingrade_id;
    public $benefit_id;
    public $isIntegrasi;

    protected $xssProtected = [
        'nokartuasuransi',
        'namapemilikasuransi',
        'nomorpokokperusahaan',
        'namaperusahaan'
    ];

    public function rules()
    {
         return [
            [['nokartuasuransi', 'namapemilikasuransi'], 'required', 'on' => 'edit-pendaftaran'],
            [[
                'namapemilikasuransi',
            ], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong'), 'on' => 'default'],
            ['benefit_id', 'required', 'when' => function($model) {return $model->isIntegrasi == 1;}, 'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')],
            [[
                'nomorpokokperusahaan',
                'kelastanggungan_id',
                'namaperusahaan',
                'tgl_konfirmasi',
                'status_konfirmasi',
                'nokartuasuransi',
                'asuransipasien_id',
                'masaberlakukartu',
                'nama_asuransi',
                'penjamingrade_id',
                'benefit_id',
                'isItegrasi'
            ], 'safe'],
            [['namapemilikasuransi', 'benefit_id'], 'safe', 'on' => self::SCENARIO_IGD]
        ];
    }

    public function attributeLabels()
    {
        return [
            'nokartuasuransi' => \Yii::t('fe', 'Nomor asuransi'),
            'namapemilikasuransi' => \Yii::t('fe', 'Nama pemilik'),
            'nomorpokokperusahaan' => \Yii::t('fe', 'Nomor pokok perusahaan'),
            'kelastanggungan_id' => \Yii::t('fe', 'Kelas tanggungan'),
            'namaperusahaan' => \Yii::t('fe', 'Nama perusahaan'),
            'tgl_konfirmasi' => \Yii::t('fe', 'Tanggal Konfirmasi'),
            'status_konfirmasi' => \Yii::t('fe', 'Telah konfirmasi'),
            'pasien_id'=>Yii::t('fe', 'Pasien'),
            'masaberlakukartu' => \Yii::t('fe', 'Masa Berlaku Kartu'),
            'nama_asuransi' => \Yii::t('fe', 'Nama Asuransi'),
            'penjamingrade_id' => \Yii::t('fe', 'Penjamin Grade'),
            'benefit_id' => \Yii::t('fe', 'Benefit'),
        ];
    }
}
