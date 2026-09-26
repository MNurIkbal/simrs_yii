<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "komponentarif_m".
 *
 * @property int $komponentarif_id
 * @property string $komponentarif_nama
 * @property string $komponentarif_namalainnya
 * @property string $komponentarif_kode
 * @property double $persen_delegasi
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
 * @property bool $is_total
 * @property string $catatan
 *
 * @property KomponenjasaM[] $komponenjasaMs
 * @property KomponentarifinstalasiMp[] $komponentarifinstalasiMps
 * @property InstalasiM[] $instalasis
 * @property ObatalkeskomponenT[] $obatalkeskomponenTs
 * @property PelayananrekeningM[] $pelayananrekeningMs
 * @property TariftindakanM[] $tariftindakanMs
 */
class KomponenTarif extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'komponentarif_m';
    }

    /**
     * {@inheritdoc}
     */
    
    // Validasi XSS form
    protected $xssProtected = [
        'komponentarif_nama',
        'komponentarif_namalainnya',
        'komponentarif_kode',
        'persen_delegasi',
        'catatan',
        'additional_data'
    ];

    public function rules()
    {
        return [
            [['komponentarif_nama', 'komponentarif_namalainnya','komponentarif_kode'], 'required','message'=>'{attribute} '.Yii::t('app','Tidak boleh kosong')],
            // [['persen_delegasi'], 'number'],
            [['additional_data', 'catatan'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active', 'is_total', 'is_sync'], 'boolean'],
            [['komponentarif_nama', 'komponentarif_namalainnya'], 'string', 'max' => 25],
            [['komponentarif_kode'], 'string', 'max' => 53],
            [['komponentarif_kode'], 'chkKomponenKode'],
            [['komponentarif_nama'], 'chkNamaKomponen'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'komponentarif_id' => 'Komponentarif ID',
            'komponentarif_nama' => 'Nama Komponen Tarif',
            'komponentarif_namalainnya' => 'Nama Komponen Tarif Nama Lainnya',
            'komponentarif_kode' => 'Kode Komponen Tarif',
            'persen_delegasi' => 'Persen Delegasi',
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
            'is_total' => 'Is Total',
            'catatan' => 'Catatan',
        ];
    }

    public function chkKomponenKode(){
        $komponentarif_kode = $this->komponentarif_kode;
        $model = self::find()->where(['LOWER (komponentarif_kode)' => strtolower($this->komponentarif_kode), 'is_deleted' => false])->one();
        if (!empty($model) && $model->komponentarif_id != $this->komponentarif_id) {
            $this->addError("komponentarif_kode", "Kode Komponen Sudah Dipakai");
            return false;
        }

        return true;
    }

    public function chkNamaKomponen(){
        $komponentarif_nama = $this->komponentarif_nama;
        $model = self::find()->where(['LOWER (komponentarif_nama)' => strtolower($this->komponentarif_nama), 'is_deleted' => false])->one();
        if (!empty($model) && $model->komponentarif_id != $this->komponentarif_id) {
            $this->addError("komponentarif_nama", "Nama Komponen Sudah Dipakai");
            return false;
        }

        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKomponenjasaMs()
    {
        return $this->hasMany(KomponenjasaM::className(), ['komponentarif_id' => 'komponentarif_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKomponentarifinstalasiMps()
    {
        return $this->hasMany(KomponentarifinstalasiMp::className(), ['komponentarif_id' => 'komponentarif_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstalasis()
    {
        return $this->hasMany(InstalasiM::className(), ['instalasi_id' => 'instalasi_id'])->viaTable('komponentarifinstalasi_mp', ['komponentarif_id' => 'komponentarif_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getObatalkeskomponenTs()
    {
        return $this->hasMany(ObatalkeskomponenT::className(), ['komponentarif_id' => 'komponentarif_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPelayananrekeningMs()
    {
        return $this->hasMany(PelayananrekeningM::className(), ['komponentarif_id' => 'komponentarif_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTariftindakanMs()
    {
        return $this->hasMany(TariftindakanM::className(), ['komponentarif_id' => 'komponentarif_id']);
    }
}
