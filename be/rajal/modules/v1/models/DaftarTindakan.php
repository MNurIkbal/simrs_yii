<?php
/*
 * @Author: metafiliana 
 * @Date: 2018-01-23 15:43:03 
 * @Last Modified by:   afil
 * @Last Modified time: 2018-04-02 16:29:58
 * @Description: 
 */

namespace app\modules\v1\models;

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
 * @property TarifambulansM[] $tarifambulansMs
 * @property TarifdietMp[] $tarifdietMps
 * @property JenisdietM[] $jenisdiets
 * @property TariftindakanM[] $tariftindakanMs
 * @property TindakanpelayananT[] $tindakanpelayananTs
 * @property TindakanruanganMp[] $tindakanruanganMps
 * @property RuanganM[] $ruangans
 * @property TindakansudahbayarT[] $tindakansudahbayarTs
 */
class DaftarTindakan extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'daftartindakan_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kategoritindakan_id', 'kelompoktindakan_id', 'komponenunit_id', 'daftartindakan_nama'], 'required'],
            [['kategoritindakan_id', 'kelompoktindakan_id', 'komponenunit_id', 'jeniskegiatantindakan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['kategoritindakan_id', 'kelompoktindakan_id', 'komponenunit_id', 'jeniskegiatantindakan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['daftartindakan_karcis', 'daftartindakan_visite', 'daftartindakan_konsul', 'daftartindakan_akomodasi', 'daftartindakan_tindakan', 'daftartindakan_observasi', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['daftartindakan_kode'], 'string', 'max' => 20],
            [['daftartindakan_nama', 'tindakanmedis_nama', 'daftartindakan_namalainnya'], 'string', 'max' => 200],
            [['daftartindakan_katakunci'], 'string', 'max' => 30],
            [['jeniskegiatantindakan_id'], 'exist', 'skipOnError' => true, 'targetClass' => JeniskegiatantindakanM::className(), 'targetAttribute' => ['jeniskegiatantindakan_id' => 'jeniskegiatantindakan_id']],
            [['kategoritindakan_id'], 'exist', 'skipOnError' => true, 'targetClass' => KategoritindakanM::className(), 'targetAttribute' => ['kategoritindakan_id' => 'kategoritindakan_id']],
            [['kelompoktindakan_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelompoktindakanM::className(), 'targetAttribute' => ['kelompoktindakan_id' => 'kelompoktindakan_id']],
            [['komponenunit_id'], 'exist', 'skipOnError' => true, 'targetClass' => KomponenunitM::className(), 'targetAttribute' => ['komponenunit_id' => 'komponenunit_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'daftartindakan_id' => 'Daftartindakan ID',
            'kategoritindakan_id' => 'Kategoritindakan ID',
            'kelompoktindakan_id' => 'Kelompoktindakan ID',
            'komponenunit_id' => 'Komponenunit ID',
            'jeniskegiatantindakan_id' => 'Jeniskegiatantindakan ID',
            'daftartindakan_kode' => 'Daftartindakan Kode',
            'daftartindakan_nama' => 'Daftartindakan Nama',
            'tindakanmedis_nama' => 'Tindakanmedis Nama',
            'daftartindakan_namalainnya' => 'Daftartindakan Namalainnya',
            'daftartindakan_katakunci' => 'Daftartindakan Katakunci',
            'daftartindakan_karcis' => 'Daftartindakan Karcis',
            'daftartindakan_visite' => 'Daftartindakan Visite',
            'daftartindakan_konsul' => 'Daftartindakan Konsul',
            'daftartindakan_akomodasi' => 'Daftartindakan Akomodasi',
            'daftartindakan_tindakan' => 'Daftartindakan Tindakan',
            'daftartindakan_observasi' => 'Daftartindakan Observasi',
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJeniskegiatantindakan()
    {
        return $this->hasOne(JeniskegiatantindakanM::className(), ['jeniskegiatantindakan_id' => 'jeniskegiatantindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKategoritindakan()
    {
        return $this->hasOne(KategoritindakanM::className(), ['kategoritindakan_id' => 'kategoritindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKelompoktindakan()
    {
        return $this->hasOne(KelompoktindakanM::className(), ['kelompoktindakan_id' => 'kelompoktindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKomponenunit()
    {
        return $this->hasOne(KomponenunitM::className(), ['komponenunit_id' => 'komponenunit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKarcisMs()
    {
        return $this->hasMany(KarcisM::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenudietMs()
    {
        return $this->hasMany(MenudietM::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperasiMs()
    {
        return $this->hasMany(OperasiM::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaketBmhp()
    {
        return $this->hasMany(PaketBmhp::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaketpelayananMs()
    {
        return $this->hasMany(PaketpelayananM::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPelayananrekeningMs()
    {
        return $this->hasMany(PelayananrekeningM::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPemeriksaanlabMs()
    {
        return $this->hasMany(PemeriksaanlabM::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPemeriksaanradiologiMs()
    {
        return $this->hasMany(PemeriksaanradiologiM::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPemeriksaanrehabmedikMs()
    {
        return $this->hasMany(PemeriksaanrehabmedikM::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermintaanmcuTs()
    {
        return $this->hasMany(PermintaanmcuT::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRencanatindakanTs()
    {
        return $this->hasMany(RencanatindakanT::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTarifambulansMs()
    {
        return $this->hasMany(TarifambulansM::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTarifdietMps()
    {
        return $this->hasMany(TarifdietMp::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJenisdiets()
    {
        return $this->hasMany(JenisdietM::className(), ['jenisdiet_id' => 'jenisdiet_id'])->viaTable('tarifdiet_mp', ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTariftindakanMs()
    {
        return $this->hasMany(TariftindakanM::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTindakanpelayananTs()
    {
        return $this->hasMany(TindakanpelayananT::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTindakanruanganMps()
    {
        return $this->hasMany(TindakanruanganMp::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuangans()
    {
        return $this->hasMany(RuanganM::className(), ['ruangan_id' => 'ruangan_id'])->viaTable('tindakanruangan_mp', ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTindakansudahbayarTs()
    {
        return $this->hasMany(TindakansudahbayarT::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    public function extraFields()
    {
        return [
            'paketbmhp_m' => function($item){
                return $item->paketBmhp;
            },
        ];
    }
}
