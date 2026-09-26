<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\payload;

use Yii;

class ApiPayload extends \Doco\components\DocoBaseModel
{
    public $pendaftaran_id;
    public $no_pendaftaran;
    public $ruangan_id;
    public $penjamin_id;
    public $kelaspelayanan_id;
    public $instalasi_id;
    public $tgl_transaksi;
    public $tgl_pendaftaran;
    public $detail_tindakan = [];

    public $daftartindakan_id;
    public $tipepaket_id;
    public $dokter_id;
    public $perawat_id;
    public $perawat2_id;
    public $is_cyto;
    public $is_penyulit;
    public $is_penatajasa;
    public $qty;
    public $implementasi_id;
    public $instruksitindakan_id;
    public $carabayar_id;
    public $is_akomodasi;
    public $is_half_day;
    public $kamarruangan_id;
    public $kamartempattidur_id;

    public function rules()
    {
         return [
            [[
                // 'pendaftaran_id', 
                'ruangan_id',
                // 'penjamin_id',
                // 'kelaspelayanan_id',
                'no_pendaftaran',
                'instalasi_id',
                'tgl_transaksi',
                'detail_tindakan',
            ], 'required','message'=>'{attribute} Tidak Boleh Kosong'],
            [[
                'pendaftaran_id',
                'no_pendaftaran',
                'ruangan_id',
                'penjamin_id',
                'kelaspelayanan_id',
                'instalasi_id',
                'tgl_transaksi',
                'detail_tindakan',
                'carabayar_id',
                'tgl_pendaftaran',
                'is_akomodasi',
                'is_half_day',
                'kamarruangan_id',
                'kamartempattidur_id',
            ], 'safe'],
            [[
                'pendaftaran_id',
                'ruangan_id',
                'penjamin_id',
                'kelaspelayanan_id',
                'instalasi_id',
                'kamarruangan_id',
                'kamartempattidur_id',
            ], 'integer'],
            ['tgl_transaksi', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            ['detail_tindakan', 'rulesDetailTindakan'],
        ];
    }

    public function rulesDetailTindakan($attribute)
    {
        if(is_array($this->$attribute)) {
            foreach ($this->$attribute as $key => $value) {
                if(!empty($value['daftartindakan_id']) && empty($value['tipepaket_id'])) {
                    $jenis = 'tindakan';
                    if(!is_numeric($value['daftartindakan_id'])) {
                        $this->addError($attribute, 'Tindakan ID harus berupa Integer.');
                    }
                }
                elseif(empty($value['daftartindakan_id']) && !empty($value['tipepaket_id'])) {
                    $jenis = 'paket';
                    if(!is_numeric($value['tipepaket_id'])) {
                        $this->addError($attribute, 'Paket ID harus berupa Integer.');
                    }
                }
                else {
                    $jenis = 'all';
                    if(empty($value['daftartindakan_id']) && empty($value['tipepaket_id'])) {
                        $this->addError($attribute, 'Tindakan atau Paket tidak boleh kosong.');
                    }
                }

                // check qty
                if(isset($value['qty']) && !empty($value['qty'])) {
                    $qty = $value['qty'];
                    if(!is_numeric($qty)) {
                        $this->addError($attribute, 'Qty harus berupa Integer.');
                    }
                }
                else {
                    $this->addError($attribute, 'Qty tidak boleh kosong.');
                }

                // check is_cyto
                if(isset($value['is_cyto']) && !empty($value['is_cyto'])) {
                    $is_cyto = $value['is_cyto'];
                    if(!is_bool($is_cyto)) {
                        $this->addError($attribute, 'Cyto harus berupa Boolean.');
                    }
                }

                // check is_penyulit
                if(isset($value['is_penyulit']) && !empty($value['is_penyulit'])) {
                    $is_penyulit = $value['is_penyulit'];
                    if(!is_bool($is_penyulit)) {
                        $this->addError($attribute, 'Penyulit harus berupa Boolean.');
                    }
                }
                
                // check implementasi_id
                if(isset($value['implementasi_id']) && !empty($value['implementasi_id'])) {
                    $implementasi_id = $value['implementasi_id'];
                    if(!is_numeric($implementasi_id)) {
                        $this->addError($attribute, 'Implementasi ID harus berupa Integer.');
                    }
                }

                // check instruksitindakan_id
                if(isset($value['instruksitindakan_id']) && !empty($value['instruksitindakan_id'])) {
                    $instruksitindakan_id = $value['instruksitindakan_id'];
                    if(!is_numeric($instruksitindakan_id)) {
                        $this->addError($attribute, 'Instruksi Tindakan ID harus berupa Integer.');
                    }
                }

                // check dokter_id
                if(isset($value['dokter_id']) && !empty($value['dokter_id'])) {
                    $dokter_id = $value['dokter_id'];
                    if(!is_numeric($dokter_id)) {
                        $this->addError($attribute, 'Dokter ID harus berupa Integer.');
                    }
                }

                // check perawat_id
                if(isset($value['perawat_id']) && !empty($value['perawat_id'])) {
                    $perawat_id = $value['perawat_id'];
                    if(!is_numeric($perawat_id)) {
                        $this->addError($attribute, 'Perawat ID harus berupa Integer.');
                    }
                }

                // check perawat2_id
                if(isset($value['perawat2_id']) && !empty($value['perawat2_id'])) {
                    $perawat2_id = $value['perawat2_id'];
                    if(!is_numeric($perawat2_id)) {
                        $this->addError($attribute, 'Perawat2 ID harus berupa Integer.');
                    }
                }

                if (isset($value['kamarruangan_id']) && !empty($value['kamarruangan_id'])) {
                    $kamarRuangan = $value['kamarruangan_id'];
                    if(!is_numeric($kamarRuangan)) {
                        $this->addError($attribute, 'Kamar ruangan ID harus berupa Integer.');
                    }
                }

                if (isset($value['kamartempattidur_id']) && !empty($value['kamartempattidur_id'])) {
                    $tempatTidur = $value['kamartempattidur_id'];
                    if(!is_numeric($tempatTidur)) {
                        $this->addError($attribute, 'Tempat tidur ID harus berupa Integer.');
                    }
                }

                if (isset($value['tgl_akomodasi']) && !empty($value['tgl_akomodasi'])) {
                    if(strtotime($this->tgl_pendaftaran) > strtotime($value['tgl_akomodasi'])) {
                        $this->addError($attribute, 'Tanggal akomodasi tidak valid.');
                    }
                }

            }
        }
    }
}
