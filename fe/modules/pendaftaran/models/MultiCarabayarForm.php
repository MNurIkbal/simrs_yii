<?php

namespace app\modules\pendaftaran\models;

use app\components\DocoConstants;
use Yii;

class MultiCarabayarForm extends \yii\base\Model
{
    public $add_carabayar_id_1;
    public $add_penjamin_id_1;
    public $add_asalrujukan_id_1;
    public $add_carabayar_id_2;
    public $add_penjamin_id_2;
    public $add_asalrujukan_id_2;
    public $additional;
    public $add_no_asuransi_1;
    public $add_no_asuransi_2;
    public $add_carabayargroup_1;
    public $add_carabayargroup_2;
    public $flagBpjs;
    public $carabayar_utama;
    public $carabayargroup_utama;
    public $add_nokartuasuransi_1;
    public $add_namapemilikasuransi_1;
    public $add_nomorpokokperusahaan_1;
    public $add_kelastanggungan_id_1;
    public $add_namaperusahaan_1;
    public $add_tgl_konfirmasi_1;
    public $add_status_konfirmasi_1;
    public $add_asuransipasien_id_1;
    public $add_nokartuasuransi_2;
    public $add_namapemilikasuransi_2;
    public $add_nomorpokokperusahaan_2;
    public $add_kelastanggungan_id_2;
    public $add_namaperusahaan_2;
    public $add_tgl_konfirmasi_2;
    public $add_status_konfirmasi_2;
    public $add_asuransipasien_id_2;
    public $is_add_payer;
    public $add_penjamingrade_id_1;
    public $add_penjamingrade_id_2;
    public $instalasi_workspace;

    public function init() {
        parent::init();
    }

    protected $xssProtected = [
        'add_nokartuasuransi_1',
        'add_namapemilikasuransi_1',
        'add_nomorpokokperusahaan_1',
        'add_namaperusahaan_1',
        'add_nokartuasuransi_2',
        'add_namapemilikasuransi_2',
        'add_nomorpokokperusahaan_2',
        'add_namaperusahaan_2'
    ];

    public function rules()
    {
        return [
            [[
                'add_carabayar_id_1',
                'add_penjamin_id_1',
                'add_asalrujukan_id_1',
                'add_carabayar_id_2',
                'add_penjamin_id_2',
                'add_asalrujukan_id_2',
                'additional',
                'add_no_asuransi_1',
                'add_no_asuransi_2',
                'add_carabayargroup_1',
                'add_carabayargroup_2',
                'flagBpjs',
                'carabayar_utama',
                'carabayargroup_utama',
                'add_nomorpokokperusahaan_1',
                'add_kelastanggungan_id_1',
                'add_namaperusahaan_1',
                'add_tgl_konfirmasi_1',
                'add_status_konfirmasi_1',
                'add_nokartuasuransi_1',
                'add_asuransipasien_id_1'.
                'add_nomorpokokperusahaan_2',
                'add_kelastanggungan_id_2',
                'add_namaperusahaan_2',
                'add_tgl_konfirmasi_2',
                'add_status_konfirmasi_2',
                'add_nokartuasuransi_2',
                'add_asuransipasien_id_2',
                'is_add_payer',
                'instalasi_workspace',
            ],'safe'],
            [[
                'add_carabayar_id_1',
                'add_penjamin_id_1',
                // 'add_asalrujukan_id_1',
            ],'required', 'on' => 'default'],
            [[
                'add_namapemilikasuransi_1',
            ], 'required' ,'on' => 'first-validate-asuransi', 'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')],
            [[
                'add_namapemilikasuransi_2',
            ], 'required' ,'on' => 'second-validate-asuransi', 'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')],
            [[
                'add_namapemilikasuransi_1',
                'add_namapemilikasuransi_2',
            ], 'safe' ,'on' => 'asuransi-igd'],
            [['add_carabayar_id_1', 'add_carabayar_id_2', 'carabayar_utama'],'checkCarabayar'],
        ];
    }

    public function checkCarabayar($params, $attributes)
    {
    //     if ($this->groupcarabayar_id == 417) {
    //         if ((!empty($this->tipe_pasien) && $this->jenis_pendaftaran != 'pasien-rs') && empty($this->no_rekam_medik)) {
    //             $this->addError('no_rekam_medik', 'No Rekam Medik tidak boleh kosong');
    //         }

    //         if(empty($this->jenis_pendaftaran) && empty($this->asalrujukan_id)) {
    //             $this->addError('asalrujukan_id','Asal Rujukan tidak boleh kosong.');
    //         }
    //     }
        if ($this->carabayargroup_utama == 418 && $this->add_carabayargroup_1 == 418 && $this->add_carabayargroup_2 == 418) {
            $this->addError('add_carabayar_id_1', 'Cara Bayar BPJS tidak boleh lebih dari 1');
            $this->addError('add_carabayar_id_2', 'Cara Bayar BPJS tidak boleh lebih dari 1');
        }else if($this->carabayargroup_utama == 418 && $this->add_carabayargroup_1 == 418) {
            $this->addError('add_carabayar_id_1', 'Cara Bayar BPJS tidak boleh lebih dari 1');
        } else if ($this->carabayargroup_utama == 418 && $this->add_carabayargroup_2 == 418) {
            $this->addError('add_carabayar_id_2', 'Cara Bayar BPJS tidak boleh lebih dari 1');
        } else if($this->add_carabayargroup_1 == 418 && $this->add_carabayargroup_2 == 418) {
            $this->addError('add_carabayar_id_2', 'Cara Bayar BPJS tidak boleh lebih dari 1');
        }

        Yii::error($this->instalasi_workspace);

        if ($this->instalasi_workspace != DocoConstants::WS_IGD && $this->add_carabayargroup_1 == 419) {
            if (empty($this->add_no_asuransi_1)) {
                $this->addError('add_no_asuransi_1', 'No Asuransi tidak boleh kosong');
            }

            if (strlen($this->add_no_asuransi_1) <= 3) {
                $this->addError('add_no_asuransi_1', 'No Asuransi minimal 4 digit');
            }
        }

        if($this->is_add_payer != null) {

            if(!empty($this->add_carabayar_id_2)) {
                if(empty($this->add_penjamin_id_2)) {
                    $this->addError('add_penjamin_id_2', 'Penjamin tidak boleh kosong');
                }

                // if(empty($this->add_asalrujukan_id_2)) {
                //     $this->addError('add_asalrujukan_id_2', 'Asal Rujukan tidak boleh kosong');
                // }

                if($this->instalasi_workspace != DocoConstants::WS_IGD && $this->add_carabayargroup_2 == 419) {
                    if (empty($this->add_no_asuransi_2)) {
                        $this->addError('add_no_asuransi_2', 'No Asuransi tidak boleh kosong');
                    }

                    if (strlen($this->add_no_asuransi_2) <= 3) {
                        $this->addError('add_no_asuransi_2', 'No Asuransi minimal 4 digit');
                    }
                }
            } else {
                $this->addError('add_carabayar_id_2', 'Cara Bayar tidak boleh kosong');
                $this->addError('add_penjamin_id_2', 'Penjamin tidak boleh kosong');
            }
        }

    }

    public function attributeLabels()
    {
        return [
            'add_carabayar_id_1' =>\Yii::t('fe','Cara bayar'),
            'add_penjamin_id_1' =>\Yii::t('fe','Penjamin'),
            'add_asalrujukan_id_1' =>\Yii::t('fe','Asal Rujukan'),
            'add_carabayar_id_2' =>\Yii::t('fe','Cara bayar'),
            'add_penjamin_id_2' =>\Yii::t('fe','Penjamin'),
            'add_asalrujukan_id_2' => \Yii::t('fe','Asal Rujukan'),
            'additional' =>\Yii::t('fe','Additional'),
            'add_no_asuransi_1' =>\Yii::t('fe','No Asuransi'),
            'add_no_asuransi_2' =>\Yii::t('fe','No Asuransi'),
            'add_nokartuasuransi_1' => \Yii::t('fe', 'Nomor asuransi'),
            'add_namapemilikasuransi_1' => \Yii::t('fe', 'Nama pemilik'),
            'add_nomorpokokperusahaan_1' => \Yii::t('fe', 'Nomor pokok perusahaan'),
            'add_kelastanggungan_id_1' => \Yii::t('fe', 'Kelas tanggungan'),
            'add_namaperusahaan_1' => \Yii::t('fe', 'Nama perusahaan'),
            'add_tgl_konfirmasi_1' => \Yii::t('fe', 'Tanggal Konfirmasi'),
            'add_status_konfirmasi_1' => \Yii::t('fe', 'Telah konfirmasi'),
            'add_asuransipasien_id_1'=>\Yii::t('fe', 'Pasien'),
            'add_nokartuasuransi_2' => \Yii::t('fe', 'Nomor asuransi'),
            'add_namapemilikasuransi_2' => \Yii::t('fe', 'Nama pemilik'),
            'add_nomorpokokperusahaan_2' => \Yii::t('fe', 'Nomor pokok perusahaan'),
            'add_kelastanggungan_id_2' => \Yii::t('fe', 'Kelas tanggungan'),
            'add_namaperusahaan_2' => \Yii::t('fe', 'Nama perusahaan'),
            'add_tgl_konfirmasi_2' => \Yii::t('fe', 'Tanggal Konfirmasi'),
            'add_status_konfirmasi_2' => \Yii::t('fe', 'Telah konfirmasi'),
            'add_asuransipasien_id_2'=>\Yii::t('fe', 'Pasien'),
            'add_penjamingrade_id_1'=>\Yii::t('fe', 'Penjamin Grade'),
            'add_penjamingrade_id_2'=>\Yii::t('fe', 'Penjamin Grade'),
        ];
    }
}
