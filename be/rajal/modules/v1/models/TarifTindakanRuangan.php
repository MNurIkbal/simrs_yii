<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-03-06 10:59:05
 * @Last Modified by:   afil
 * @Last Modified time: 2018-03-06 11:01:13
 * @Description: 
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "tariftindakanruangan_v".
 *
 * @property int $kategoritindakan_id
 * @property string $kategoritindakan_nama
 * @property int $kelompoktindakan_id
 * @property string $kelompoktindakan_nama
 * @property string $daftartindakan_kode
 * @property string $daftartindakan_nama
 * @property string $daftartindakan_namalainnya
 * @property string $daftartindakan_katakunci
 * @property int $perdatarif_id
 * @property string $perdanama_sk
 * @property string $perda_no
 * @property string $perda_tgl
 * @property string $perda_tentang
 * @property string $ditetapkan_oleh
 * @property string $tempat_ditetapkan
 * @property int $jenistarif_id
 * @property string $jenistarif_nama
 * @property int $tariftindakan_id
 * @property int $komponentarif_id
 * @property string $komponentarif_nama
 * @property double $harga_tariftindakan
 * @property int $persendiskon_tindakan
 * @property double $hargadiskon_tindakan
 * @property int $persencyto_tindakan
 * @property int $jeniskelas_id
 * @property string $jeniskelas_nama
 * @property int $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property string $kelaspelayanan_namalainnya
 * @property int $daftartindakan_id
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $instalasi_id
 * @property string $instalasi_nama
 * @property bool $daftartindakan_karcis
 * @property bool $daftartindakan_visite
 * @property bool $daftartindakan_konsul
 * @property bool $daftartindakan_akomodasi
 * @property int $carabayar_id
 * @property string $carabayar_nama
 * @property int $penjamin_id
 * @property string $penjamin_nama
 * @property int $komponenunit_id
 * @property string $komponenunit_nama
 */
class TarifTindakanRuangan extends \Doco\components\DocoActiveRecord
{
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
