<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-03-06 10:54:47
 * @Last Modified by:   afil
 * @Last Modified time: 2018-03-06 11:00:40
 * @Description: 
 */

namespace app\modules\rajal\models;

use Yii;

class TarifTindakanRuanganForm extends \yii\base\Model
{
    public $kategoritindakan_id;
    public $kategoritindakan_nama;
    public $kelompoktindakan_id;
    public $kelompoktindakan_nama;
    public $daftartindakan_kode;
    public $daftartindakan_nama;
    public $daftartindakan_namalainnya;
    public $daftartindakan_katakunci;
    public $perdatarif_id;
    public $perdanama_sk;
    public $perda_no;
    public $perda_tgl;
    public $perda_tentang;
    public $ditetapkan_oleh;
    public $tempat_ditetapkan;
    public $jenistarif_id;
    public $jenistarif_nama;
    public $tariftindakan_id;
    public $komponentarif_id;
    public $komponentarif_nama;
    public $harga_tariftindakan;
    public $persendiskon_tindakan;
    public $hargadiskon_tindakan;
    public $persencyto_tindakan;
    public $jeniskelas_id;
    public $jeniskelas_nama;
    public $kelaspelayanan_id;
    public $kelaspelayanan_nama;
    public $kelaspelayanan_namalainnya;
    public $daftartindakan_id;
    public $ruangan_id;
    public $ruangan_nama;
    public $instalasi_id;
    public $instalasi_nama;
    public $daftartindakan_karcis;
    public $daftartindakan_visite;
    public $daftartindakan_konsul;
    public $daftartindakan_akomodasi;
    public $carabayar_id;
    public $carabayar_nama;
    public $penjamin_id;
    public $penjamin_nama;
    public $komponenunit_id;
    public $komponenunit_nama;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tariftindakanruangan_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kategoritindakan_id', 'kelompoktindakan_id', 'perdatarif_id', 'jenistarif_id', 'tariftindakan_id', 'komponentarif_id', 'persendiskon_tindakan', 'persencyto_tindakan', 'jeniskelas_id', 'kelaspelayanan_id', 'daftartindakan_id', 'ruangan_id', 'instalasi_id', 'carabayar_id', 'penjamin_id', 'komponenunit_id'], 'default', 'value' => null],
            [['kategoritindakan_id', 'kelompoktindakan_id', 'perdatarif_id', 'jenistarif_id', 'tariftindakan_id', 'komponentarif_id', 'persendiskon_tindakan', 'persencyto_tindakan', 'jeniskelas_id', 'kelaspelayanan_id', 'daftartindakan_id', 'ruangan_id', 'instalasi_id', 'carabayar_id', 'penjamin_id', 'komponenunit_id'], 'integer'],
            [['perda_tgl'], 'safe'],
            [['perda_tentang'], 'string'],
            [['harga_tariftindakan', 'hargadiskon_tindakan'], 'number'],
            [['daftartindakan_karcis', 'daftartindakan_visite', 'daftartindakan_konsul', 'daftartindakan_akomodasi'], 'boolean'],
            [['kategoritindakan_nama'], 'string', 'max' => 150],
            [['kelompoktindakan_nama', 'kelaspelayanan_nama', 'kelaspelayanan_namalainnya', 'ruangan_nama', 'instalasi_nama', 'carabayar_nama', 'penjamin_nama'], 'string', 'max' => 50],
            [['daftartindakan_kode', 'perda_no'], 'string', 'max' => 20],
            [['daftartindakan_nama', 'daftartindakan_namalainnya', 'perdanama_sk'], 'string', 'max' => 200],
            [['daftartindakan_katakunci', 'ditetapkan_oleh', 'tempat_ditetapkan', 'komponenunit_nama'], 'string', 'max' => 30],
            [['jenistarif_nama', 'komponentarif_nama', 'jeniskelas_nama'], 'string', 'max' => 25],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kategoritindakan_id' => 'Kategoritindakan ID',
            'kategoritindakan_nama' => 'Kategoritindakan Nama',
            'kelompoktindakan_id' => 'Kelompoktindakan ID',
            'kelompoktindakan_nama' => 'Kelompoktindakan Nama',
            'daftartindakan_kode' => 'Daftartindakan Kode',
            'daftartindakan_nama' => 'Daftartindakan Nama',
            'daftartindakan_namalainnya' => 'Daftartindakan Namalainnya',
            'daftartindakan_katakunci' => 'Daftartindakan Katakunci',
            'perdatarif_id' => 'Perdatarif ID',
            'perdanama_sk' => 'Perdanama Sk',
            'perda_no' => 'Perda No',
            'perda_tgl' => 'Perda Tgl',
            'perda_tentang' => 'Perda Tentang',
            'ditetapkan_oleh' => 'Ditetapkan Oleh',
            'tempat_ditetapkan' => 'Tempat Ditetapkan',
            'jenistarif_id' => 'Jenistarif ID',
            'jenistarif_nama' => 'Jenistarif Nama',
            'tariftindakan_id' => 'Tariftindakan ID',
            'komponentarif_id' => 'Komponentarif ID',
            'komponentarif_nama' => 'Komponentarif Nama',
            'harga_tariftindakan' => 'Harga Tariftindakan',
            'persendiskon_tindakan' => 'Persendiskon Tindakan',
            'hargadiskon_tindakan' => 'Hargadiskon Tindakan',
            'persencyto_tindakan' => 'Persencyto Tindakan',
            'jeniskelas_id' => 'Jeniskelas ID',
            'jeniskelas_nama' => 'Jeniskelas Nama',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'kelaspelayanan_namalainnya' => 'Kelaspelayanan Namalainnya',
            'daftartindakan_id' => 'Daftartindakan ID',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_nama' => 'Instalasi Nama',
            'daftartindakan_karcis' => 'Daftartindakan Karcis',
            'daftartindakan_visite' => 'Daftartindakan Visite',
            'daftartindakan_konsul' => 'Daftartindakan Konsul',
            'daftartindakan_akomodasi' => 'Daftartindakan Akomodasi',
            'carabayar_id' => 'Carabayar ID',
            'carabayar_nama' => 'Carabayar Nama',
            'penjamin_id' => 'Penjamin ID',
            'penjamin_nama' => 'Penjamin Nama',
            'komponenunit_id' => 'Komponenunit ID',
            'komponenunit_nama' => 'Komponenunit Nama',
        ];
    }
}
