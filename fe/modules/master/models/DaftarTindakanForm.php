<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-25 09:06:40
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-08-08 12:12:17
 */

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "daftartindakan_m".
 *
 * @property int $daftartindakan_id
 * @property int $kategoritindakan_id
 * @property int $kelompoktindakan_id
 * @property int $komponenunit_id
 * @property int $jeniskegiatantindakan_id
 * @property string $daftartindakan_kode
 * @property string $daftartindakan_nama
 * @property string $tindakanmedis_nama
 * @property string $daftartindakan_namalainnya
 * @property string $daftartindakan_katakunci
 * @property bool $daftartindakan_karcis
 * @property bool $daftartindakan_visite
 * @property bool $daftartindakan_konsul
 * @property bool $daftartindakan_akomodasi
 * @property bool $daftartindakan_tindakan
 * @property bool $daftartindakan_observasi
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
 *
 * @property JeniskegiatantindakanM $jeniskegiatantindakan
 * @property KategoritindakanM $kategoritindakan
 * @property KelompoktindakanM $kelompoktindakan
 * @property KomponenunitM $komponenunit
 * @property KarcisM[] $karcisMs
 * @property MenudietM[] $menudietMs
 * @property OperasiM[] $operasiMs
 * @property PaketbmhpM[] $paketbmhpMs
 * @property PaketpelayananM[] $paketpelayananMs
 * @property PelayananrekeningM[] $pelayananrekeningMs
 * @property PemeriksaanlabM[] $pemeriksaanlabMs
 * @property PemeriksaanradiologiM[] $pemeriksaanradiologiMs
 * @property PemeriksaanrehabmedikM[] $pemeriksaanrehabmedikMs
 * @property PermintaanmcuT[] $permintaanmcuTs
 * @property RencanatindakanT[] $rencanatindakanTs
 * @property SimbolodontogramM[] $simbolodontogramMs
 * @property TarifambulansM[] $tarifambulansMs
 * @property TarifdietMp[] $tarifdietMps
 * @property JenisdietM[] $jenisdiets
 * @property TariftindakanM[] $tariftindakanMs
 * @property TindakanpelayananT[] $tindakanpelayananTs
 * @property TindakanruanganMp[] $tindakanruanganMps
 * @property RuanganM[] $ruangans
 * @property TindakansudahbayarT[] $tindakansudahbayarTs
 */
class DaftarTindakanForm extends \yii\base\Model
{
    public $isGroupInaCbg;
    public $daftartindakan_id;
    public $kategoritindakan_id;
    public $kelompoktindakan_id;
    public $komponenunit_id;
    public $jeniskegiatantindakan_id;
    public $daftartindakan_kode;
    public $daftartindakan_nama;
    public $tindakanmedis_nama;
    public $daftartindakan_namalainnya;
    public $daftartindakan_katakunci;
    public $daftartindakan_karcis;
    public $daftartindakan_visite;
    public $daftartindakan_konsul;
    public $daftartindakan_akomodasi;
    public $daftartindakan_tindakan;
    public $daftartindakan_observasi;
    public $groupinacbg_id;
    public $catatan;
    public $additional_data;
    public $is_akomodasi;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;
    public $is_konsultasi;
    public $servicecategory_id;
    public $servicegroup_id;

    //form tindakan non paket fisioterapi
    public $is_fisio;
    public $jenis_pemeriksaan_fisio_id;
    public $kelompok_pemeriksaan_fisio_id;
    public $list_ruangan;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['daftartindakan_kode','daftartindakan_namalainnya','daftartindakan_nama',
              'kelompoktindakan_id'/*, 'groupinacbg_id'*/], 'required'],
            [['kategoritindakan_id', 'kelompoktindakan_id', 'jeniskegiatantindakan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['kategoritindakan_id', 'kelompoktindakan_id', 'jeniskegiatantindakan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['daftartindakan_karcis', 'daftartindakan_visite', 'daftartindakan_konsul', 'daftartindakan_akomodasi', 'daftartindakan_tindakan', 'daftartindakan_observasi', 'is_deleted', 'is_active', 'is_konsultasi'], 'boolean'],
            [['additional_data'], 'string'],
            [['list_ruangan', 'created_date', 'last_modified_date', 'deleted_date', 'is_akomodasi', 'servicecategory_id', 'servicegroup_id'], 'safe'],
            [['daftartindakan_kode'], 'string', 'max' => 20],
            [['daftartindakan_nama', 'tindakanmedis_nama', 'daftartindakan_namalainnya'], 'string', 'max' => 200],
            [['daftartindakan_katakunci'], 'string', 'max' => 30],
            ['groupinacbg_id', 'required', 'when' => function ($model) {
                return $model->isGroupInaCbg == 1;
            }],
            [['jenis_pemeriksaan_fisio_id', 'kelompok_pemeriksaan_fisio_id'], 'required', 'when' => function ($model) {
                    return $model->is_fisio == 1;
            }],
            [['daftartindakan_kode'], 'string', 'max' => 10, 'when' => function ($model) {
                return $model->is_fisio == 1;
            }],
            // [['jeniskegiatantindakan_id'], 'exist', 'skipOnError' => true, 'targetClass' => JeniskegiatantindakanM::className(), 'targetAttribute' => ['jeniskegiatantindakan_id' => 'jeniskegiatantindakan_id']],
            // [['kategoritindakan_id'], 'exist', 'skipOnError' => true, 'targetClass' => KategoritindakanM::className(), 'targetAttribute' => ['kategoritindakan_id' => 'kategoritindakan_id']],
            // [['kelompoktindakan_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelompoktindakanM::className(), 'targetAttribute' => ['kelompoktindakan_id' => 'kelompoktindakan_id']],
            // [['komponenunit_id'], 'exist', 'skipOnError' => true, 'targetClass' => KomponenunitM::className(), 'targetAttribute' => ['komponenunit_id' => 'komponenunit_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jenis_pemeriksaan_fisio_id' => 'Jenis Pemeriksaan',
            'kelompok_pemeriksaan_fisio_id' => 'Kelompok Pemeriksaan',
            'daftartindakan_id' => 'Daftartindakan ID',
            'kategoritindakan_id' => 'Nama Kategori',
            'kelompoktindakan_id' => 'Nama Kelompok',
            'groupinacbg_id' => 'Group INA CBGS',
            'jeniskegiatantindakan_id' => 'Nama Kegiatan',
            'daftartindakan_kode' => 'Kode Tindakan',
            'daftartindakan_nama' => 'Nama Tindakan',
            'tindakanmedis_nama' => 'Tindakanmedis Nama',
            'daftartindakan_namalainnya' => 'Nama Lainnya',
            'daftartindakan_katakunci' => 'Daftartindakan Katakunci',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
            'catatan' => 'Catatan',
            'is_akomodasi' => "Tindakan Harian Ranap",
            'is_konsultasi' => "Konsultasi"
        ];
    }


    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getJeniskegiatantindakan()
    // {
    //     return $this->hasOne(Jeniskegiatantindakan::className(), ['jeniskegiatantindakan_id' => 'jeniskegiatantindakan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getKategoritindakan()
    // {
    //     return $this->hasOne(Kategoritindakan::className(), ['kategoritindakan_id' => 'kategoritindakan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getKelompoktindakan()
    // {
    //     return $this->hasOne(Kelompoktindakan::className(), ['kelompoktindakan_id' => 'kelompoktindakan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getKomponenunit()
    // {
    //     return $this->hasOne(Komponenunit::className(), ['komponenunit_id' => 'komponenunit_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getKarcisMs()
    // {
    //     return $this->hasMany(Karcis::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getMenudietMs()
    // {
    //     return $this->hasMany(Menudiet::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getOperasiMs()
    // {
    //     return $this->hasMany(Operasi::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPaketbmhpMs()
    // {
    //     return $this->hasMany(Paketbmhp::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPaketpelayananMs()
    // {
    //     return $this->hasMany(Paketpelayanan::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPelayananrekeningMs()
    // {
    //     return $this->hasMany(Pelayananrekening::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPemeriksaanlabMs()
    // {
    //     return $this->hasMany(Pemeriksaanlab::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPemeriksaanradiologiMs()
    // {
    //     return $this->hasMany(Pemeriksaanradiologi::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPemeriksaanrehabmedikMs()
    // {
    //     return $this->hasMany(Pemeriksaanrehabmedik::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPermintaanmcuTs()
    // {
    //     return $this->hasMany(Permintaanmcu::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getRencanatindakanTs()
    // {
    //     return $this->hasMany(Rencanatindakan::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getSimbolodontogramMs()
    // {
    //     return $this->hasMany(Simbolodontogram::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getTarifambulansMs()
    // {
    //     return $this->hasMany(Tarifambulans::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getTarifdietMps()
    // {
    //     return $this->hasMany(Tarifdiet::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getJenisdiets()
    // {
    //     return $this->hasMany(Jenisdiet::className(), ['jenisdiet_id' => 'jenisdiet_id'])->viaTable('tarifdiet_mp', ['daftartindakan_id' => 'daftartindakan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getTariftindakanMs()
    // {
    //     return $this->hasMany(Tariftindakan::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getTindakanpelayananTs()
    // {
    //     return $this->hasMany(Tindakanpelayanan::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getTindakanruanganMps()
    // {
    //     return $this->hasMany(Tindakanruangan::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getRuangans()
    // {
    //     return $this->hasMany(Ruangan::className(), ['ruangan_id' => 'ruangan_id'])->viaTable('tindakanruangan_mp', ['daftartindakan_id' => 'daftartindakan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getTindakansudahbayarTs()
    // {
    //     return $this->hasMany(Tindakansudahbayar::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    // }
}
?>