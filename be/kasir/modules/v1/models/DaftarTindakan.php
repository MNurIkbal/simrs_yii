<?php

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
 * @property bool $is_akomodasi
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
class DaftarTindakan extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */

    public $isGroupInaCbg;
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
            [['daftartindakan_kode','daftartindakan_namalainnya','daftartindakan_nama',
              'kelompoktindakan_id'/*, 'groupinacbg_id'*/], 'required'],
            [['kategoritindakan_id', 'kelompoktindakan_id', 'jeniskegiatantindakan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['kategoritindakan_id', 'kelompoktindakan_id', 'jeniskegiatantindakan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['catatan', 'created_date', 'last_modified_date', 'deleted_date', 'isGroupInaCbg', 'is_akomodasi', 'is_konsultasi'], 'safe'],
            [['daftartindakan_kode'], 'string', 'max' => 20],
            [['daftartindakan_nama', 'tindakanmedis_nama', 'daftartindakan_namalainnya'], 'string', 'max' => 200],
            [['daftartindakan_katakunci'], 'string', 'max' => 30],
            [['daftartindakan_kode'], 'chkKode'],
            [['daftartindakan_nama'], 'chkNama'],
            [['daftartindakan_namalainnya'], 'chkNamaLainnya'],
            ['groupinacbg_id', 'required', 'when' => function ($model) {
                return $model->isGroupInaCbg == 1;
            }],
            // [['jeniskegiatantindakan_id'], 'exist', 'skipOnError' => true, 'targetClass' => JenisKegiatanTindakan::className(), 'targetAttribute' => ['jeniskegiatantindakan_id' => 'jeniskegiatantindakan_id']],
            // [['kategoritindakan_id'], 'exist', 'skipOnError' => true, 'targetClass' => KategoriTindakan::className(), 'targetAttribute' => ['kategoritindakan_id' => 'kategoritindakan_id']],
            // [['kelompoktindakan_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelompokTindakan::className(), 'targetAttribute' => ['kelompoktindakan_id' => 'kelompoktindakan_id']],
            // [['komponenunit_id'], 'exist', 'skipOnError' => true, 'targetClass' => KomponenunitM::className(), 'targetAttribute' => ['komponenunit_id' => 'komponenunit_id']],
        ];
    }

    public function chkKode($params, $attributes)
    {
        $daftartindakan_kode = $this->daftartindakan_kode;
        $rest = substr($this->daftartindakan_kode, 0, 1);
        if ($rest == " ") {
            $this->addError("daftartindakan_kode", "Kode Tindakan mengandung spasi di awal kata");
            return false;
        }
        else {
            $model = self::find()->where(['LOWER (daftartindakan_kode)' => strtolower($this->daftartindakan_kode), 'is_deleted' => false])->one();
            if(!empty($model) && $model->daftartindakan_id != $this->daftartindakan_id ){
                $this->addError("daftartindakan_kode","Kode Tindakan Sudah Dipakai");
                return false;
            }
        }

        return true;
    }

    public function chkNama($params, $attributes)
    {
        $rest = substr($this->daftartindakan_nama, 0, 1);
        if ($rest == " ") {
            $this->addError("daftartindakan_nama", "Nama Tindakan mengandung spasi di awal kata");
            return false;
        } else {
            $model = self::find()->where(['LOWER (daftartindakan_nama)' => strtolower($this->daftartindakan_nama), 'is_deleted' => false])->one();
              if(!empty($model) && $model->daftartindakan_id != $this->daftartindakan_id ){
                  $this->addError("daftartindakan_nama","Nama Tindakan Sudah Dipakai");
                  return false;
              }
        }

        return true;
    }

    public function chkNamaLainnya($params, $attributes)
    {
        $rest = substr($this->daftartindakan_namalainnya, 0, 1);
        if ($rest == " ") {
            $this->addError("daftartindakan_namalainnya", "Nama Lainnya mengandung spasi di awal kata");
            return false;
        }

        return true;
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
            'groupinacbg_id' => 'Group INA CBGS',
            'jeniskegiatantindakan_id' => 'Jeniskegiatantindakan ID',
            'daftartindakan_kode' => 'Daftartindakan Kode',
            'daftartindakan_nama' => 'Daftartindakan Nama',
            'tindakanmedis_nama' => 'Tindakanmedis Nama',
            'daftartindakan_namalainnya' => 'Daftartindakan Namalainnya',
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
            'is_konsultasi' => 'Konsultasi'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJeniskegiatantindakan()
    {
        return $this->hasOne(JenisKegiatanTindakan::className(), ['jeniskegiatantindakan_id' => 'jeniskegiatantindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKategoritindakan()
    {
        return $this->hasOne(KategoriTindakan::className(), ['kategoritindakan_id' => 'kategoritindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKelompoktindakan()
    {
        return $this->hasOne(KelompokTindakan::className(), ['kelompoktindakan_id' => 'kelompoktindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKomponenunit()
    {
        return $this->hasOne(Komponenunit::className(), ['komponenunit_id' => 'komponenunit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKarcisMs()
    {
        return $this->hasMany(Karcis::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenudietMs()
    {
        return $this->hasMany(Menudiet::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperasiMs()
    {
        return $this->hasMany(Operasi::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaketbmhpMs()
    {
        return $this->hasMany(Paketbmhp::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaketpelayananMs()
    {
        return $this->hasMany(Paketpelayanan::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPelayananrekeningMs()
    {
        return $this->hasMany(Pelayananrekening::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPemeriksaanlabMs()
    {
        return $this->hasMany(Pemeriksaanlab::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPemeriksaanradiologiMs()
    {
        return $this->hasMany(Pemeriksaanradiologi::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPemeriksaanrehabmedikMs()
    {
        return $this->hasMany(Pemeriksaanrehabmedik::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermintaanmcuTs()
    {
        return $this->hasMany(Permintaanmcu::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRencanatindakanTs()
    {
        return $this->hasMany(Rencanatindakan::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSimbolodontogramMs()
    {
        return $this->hasMany(Simbolodontogram::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTarifambulansMs()
    {
        return $this->hasMany(Tarifambulans::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTarifdietMps()
    {
        return $this->hasMany(Tarifdiet::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJenisdiets()
    {
        return $this->hasMany(Jenisdiet::className(), ['jenisdiet_id' => 'jenisdiet_id'])->viaTable('tarifdiet_mp', ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTariftindakanMs()
    {
        return $this->hasMany(Tariftindakan::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTindakanpelayananTs()
    {
        return $this->hasMany(Tindakanpelayanan::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTindakanruanganMps()
    {
        return $this->hasMany(Tindakanruangan::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuangans()
    {
        return $this->hasMany(Ruangan::className(), ['ruangan_id' => 'ruangan_id'])->viaTable('tindakanruangan_mp', ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTindakansudahbayarTs()
    {
        return $this->hasMany(Tindakansudahbayar::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    public function getGroupinacbg()
    {
        return $this->hasOne(GroupInaCbg::className(), ['groupinacbg_id' => 'groupinacbg_id']);
    }
}
