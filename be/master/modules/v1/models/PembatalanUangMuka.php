<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pembatalanuangmuka_t".
 *
 * @property int $pembatalanuangmuka_id
 * @property int $bayaruangmuka_id
 * @property int $tandabuktikeluar_id
 * @property int $tandabuktibayar_id
 * @property int $ruangan_id
 * @property string $tglpembatalan
 * @property string $keterangan_batal
 * @property double $jmlkaskeluarbatal
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
 * @property BayaruangmukaT[] $bayaruangmukaTs
 * @property BayaruangmukaT $bayaruangmuka
 * @property RuanganM $ruangan
 * @property TandabuktibayarT $tandabuktibayar
 * @property TandabuktikeluarT $tandabuktikeluar
 * @property TandabuktibayarT[] $tandabuktibayarTs
 * @property TandabuktikeluarT[] $tandabuktikeluarTs
 */
class PembatalanUangMuka extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pembatalanuangmuka_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['bayaruangmuka_id', 'tandabuktikeluar_id', 'tandabuktibayar_id', 'ruangan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['bayaruangmuka_id', 'tandabuktikeluar_id', 'tandabuktibayar_id', 'ruangan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['ruangan_id', 'tglpembatalan', 'keterangan_batal'], 'required'],
            [['tglpembatalan', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['keterangan_batal', 'additional_data'], 'string'],
            [['jmlkaskeluarbatal'], 'number'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['bayaruangmuka_id'], 'exist', 'skipOnError' => true, 'targetClass' => BayarUangMuka::className(), 'targetAttribute' => ['bayaruangmuka_id' => 'bayaruangmuka_id']],
            [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ruangan::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
            [['tandabuktibayar_id'], 'exist', 'skipOnError' => true, 'targetClass' => TandaBuktiBayar::className(), 'targetAttribute' => ['tandabuktibayar_id' => 'tandabuktibayar_id']],
            [['tandabuktikeluar_id'], 'exist', 'skipOnError' => true, 'targetClass' => TandaBuktiKeluar::className(), 'targetAttribute' => ['tandabuktikeluar_id' => 'tandabuktikeluar_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pembatalanuangmuka_id' => 'Pembatalanuangmuka ID',
            'bayaruangmuka_id' => 'Bayaruangmuka ID',
            'tandabuktikeluar_id' => 'Tandabuktikeluar ID',
            'tandabuktibayar_id' => 'Tandabuktibayar ID',
            'ruangan_id' => 'Ruangan ID',
            'tglpembatalan' => 'Tglpembatalan',
            'keterangan_batal' => 'Keterangan Batal',
            'jmlkaskeluarbatal' => 'Jmlkaskeluarbatal',
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
    public function getBayaruangmukaTs()
    {
        return $this->hasMany(BayarUangMuka::className(), ['pembatalanuangmuka_id' => 'pembatalanuangmuka_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBayaruangmuka()
    {
        return $this->hasOne(BayarUangMuka::className(), ['bayaruangmuka_id' => 'bayaruangmuka_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuangan()
    {
        return $this->hasOne(Ruangan::className(), ['ruangan_id' => 'ruangan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTandabuktibayar()
    {
        return $this->hasOne(TandaBuktiBayar::className(), ['tandabuktibayar_id' => 'tandabuktibayar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTandabuktikeluar()
    {
        return $this->hasOne(TandaBuktiKeluar::className(), ['tandabuktikeluar_id' => 'tandabuktikeluar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTandabuktibayarTs()
    {
        return $this->hasMany(TandaBuktiBayar::className(), ['pembatalanuangmuka_id' => 'pembatalanuangmuka_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTandabuktikeluarTs()
    {
        return $this->hasMany(TandaBuktiKeluar::className(), ['pembatalanuangmuka_id' => 'pembatalanuangmuka_id']);
    }
}
