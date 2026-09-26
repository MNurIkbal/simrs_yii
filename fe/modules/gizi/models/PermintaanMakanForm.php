<?php

namespace app\modules\gizi\models;

use Yii;

class PermintaanMakanForm extends \yii\base\Model
{
    public $jenisdiet_id;
    public $menu_diet;
    public $waktu_diet;
    public $daftartindakan_id;
    public $keterangan;
    public $makanandiet_id;
    public $perubahan_diet;
    public $kesimpulan;
    public $kondisi_puasa;
    public $puasa_tgl_awal;
    public $puasa_tgl_akhir;
    public $puasa_operasi_awal;
    public $puasa_operasi_akhir;
    public $buka_puasa;
    public $jenisdiet_lainnya;
    public $form_ubah;
    public $is_ditagihkan;

	/**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['makanandiet_id'], 'required', 'message'=>'{attribute} '.Yii::t('fe','Tidak Boleh Kosong'),
            'when' => function($model) {
                return $model->form_ubah == 1;
            }],
            [[
                'jenisdiet_id',
                'keterangan',
                'jenisdiet_lainnya',
                'makanandiet_id',
                'perubahan_diet',
                'kondisi_puasa',
                'kesimpulan',
                'puasa_tgl_awal',
                'puasa_tgl_akhir',
                'puasa_operasi_awal',
                'puasa_operasi_akhir',
                'buka_puasa',
                'form_ubah',
                'is_ditagihkan',
            ],
            'safe'],
            [['puasa_tgl_awal','puasa_tgl_akhir','puasa_operasi_awal','puasa_operasi_akhir','buka_puasa'], 'validateDate'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
        	'jenisdiet_id' => 'Jenis Diet',
            'jenisdiet_lainnya' => 'Jenis Diet Lainnya',
        	'makanandiet_id' => 'Makanan',
        	'waktu_diet' => 'Waktu Diet',
            'keterangan' => 'Catatan Diet',
            'bentuk_makanan' => 'Makanan',
            'perubahan_diet' => 'Perubahan Diet',
            'kondisi_puasa' => '1. Kondisi',
            'kesimpulan' => 'Kesimpulan',
            'puasa_tgl_awal' => '2. Pemeriksaan',
            'puasa_tgl_akhir' => 's/d',
            'puasa_operasi_awal' => '3. Operasi',
            'puasa_operasi_akhir' => 's/d',
            'buka_puasa' => '1. Mulai Jam',
            'is_ditagihkan' => 'Ditagihkan',
        ];
    }

    public function validateDate()
    {
        if(!empty($this->puasa_tgl_awal)){
            if (strtotime($this->puasa_tgl_awal) < strtotime("now -1hours")) {
                $this->addError('puasa_tgl_awal','Tanggal Pemeriksaaan tidak boleh kecil dari hari ini');
            }
        }

        if (strtotime($this->puasa_tgl_awal) > strtotime($this->puasa_tgl_akhir) ) {
            $this->addError('puasa_tgl_akhir','Tanggal Selesei Pemeriksaaan tidak boleh kecil dari Tanggal Pemeriksaan Awal');
        }

        if(!empty($this->puasa_operasi_awal)){
            if (strtotime($this->puasa_operasi_awal) < strtotime("now -1hours")) {
                $this->addError('puasa_operasi_awal','Tanggal Operasi tidak boleh kecil dari hari ini');
            }
        }

        if (strtotime($this->puasa_operasi_awal) > strtotime($this->puasa_operasi_akhir)) {
            $this->addError('puasa_operasi_akhir','Tanggal Selesei Operasi idak boleh kecil dari Tanggal Operasi Awal');
        }

        if(!empty($this->buka_puasa)){
            if (strtotime($this->buka_puasa) < strtotime("now -1hours")) {
                $this->addError('buka_puasa','Tanggal Buka Puasa Operasi tidak boleh kecil dari hari ini');
            }
        }


    }

}
