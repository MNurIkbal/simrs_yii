<?php

namespace app\modules\pendaftaran\models;
use app\components\DocoConstants;

use Yii;

class TipePasienForm extends \yii\base\Model
{
    const SCENARIO_IGD = 'scenario-igd';

    public $carabayar_id;
    public $penjamin_id;
    public $asalrujukan_id;
    public $no_rekam_medik;
    public $groupcarabayar_id;
    public $tipe_pasien;
    public $no_asuransi;
    public $no_bpjs;
    public $is_rujuk;
    public $antrian_id;
    public $is_aps;
    public $is_bbl;
    public $pendaftaranol_id;
    public $chk_status_pasien;
    public $jenis_pendaftaran;
    public $pendaftaran_id;
    public $pasien;
    public $dokter_perujuk;
    public $is_kolektif;
    public $upload_file;
    public $is_multi_payer;
    public $instalasi_workspace;

    public function init() {
        parent::init();
        $this->is_kolektif = false;
    }

    public function rules()
    {
        return [
            [[
                'carabayar_id',
                'penjamin_id',
                'asalrujukan_id',
                'no_rekam_medik',
                'groupcarabayar_id',
                'tipe_pasien',
                'no_asuransi',
                'no_bpjs',
                'is_rujuk',
                'antrian_id',
                'is_bbl',
                'is_aps',
                'pendaftaranol_id',
                'jenis_pendaftaran',
                'pendaftaran_id',
                'dokter_perujuk',
                'is_kolektif',
                'upload_file',
                'is_multi_payer',
                'instalasi_workspace',
            ],'safe'],
            [[
                'carabayar_id',
                'penjamin_id',
                'asalrujukan_id',
            ],'required'],
            [['upload_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'xls, xlsx', 'message' => \Yii::t('fe', '')],
            [['no_rekam_medik'], 'noRekamMedikRules'],
            [['carabayar_id'],'checkPasien']
        ];
    }

    /**
     * No rekam medik rules function
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function noRekamMedikRules()
    {
        if (isset($this->is_ranap) && $this->is_ranap && empty($this->no_rekam_medik)) {
            $this->addError('no_rekam_medik', 'No Rekam Medik tidak boleh kosong');
        }
    }

    public function checkPasien($params, $attributes)
    {
        if(!$this->is_kolektif){
            if ($this->groupcarabayar_id == DocoConstants::GROUP_UMUM) {
                if ((!empty($this->tipe_pasien) && $this->jenis_pendaftaran != 'pasien-rs') && empty($this->no_rekam_medik) && $this->is_multi_payer == 'false') {
                    $this->addError('no_rekam_medik', 'No Rekam Medik tidak boleh kosong');
                }

                if(empty($this->jenis_pendaftaran) && empty($this->asalrujukan_id)) {
                    $this->addError('asalrujukan_id','Asal Rujukan tidak boleh kosong.');
                }
            }

            if ($this->groupcarabayar_id == DocoConstants::GROUP_JAMINAN) {
                if($this->chk_status_pasien == 1) {
                    if(empty($this->no_rekam_medik) && $this->is_multi_payer == 'false') {
                        $this->addError('no_rekam_medik', 'No Rekam Medik tidak boleh kosong');
                    }
                }

                if ($this->instalasi_workspace != DocoConstants::WS_IGD && empty($this->no_asuransi) && empty($this->jenis_pendaftaran)) {
                    $this->addError('no_asuransi', 'No Asuransi tidak boleh kosong');
                }

                if ($this->instalasi_workspace != DocoConstants::WS_IGD && strlen($this->no_asuransi) <= 3 && empty($this->jenis_pendaftaran)) {
                    $this->addError('no_asuransi', 'No Asuransi minimal 4 digit');
                }

                if (empty($this->jenis_pendaftaran) && empty($this->asalrujukan_id)) {
                    $this->addError('asalrujukan_id','Asal Rujukan tidak boleh kosong.');
                }
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'carabayar_id' => 'Cara bayar',
            'penjamin_id' => 'Penjamin',
            'asalrujukan_id' => 'Asal Rujukan',
            'no_rekam_medik' => 'No Rekam Medik',
            'no_asuransi_label' => 'No Asuransi',
            'is_bbl' => 'Pendaftaran Bayi',
            'pendaftaran_id' => 'Pencarian',
            'is_multi_payer' => 'Multi Payer'
        ];
    }

    public function upload()
    {
        $path = \Yii::getAlias('@webroot');
        if ($this->validate()) {
            return true;
        } else {
            return false;
        }
    }
}
