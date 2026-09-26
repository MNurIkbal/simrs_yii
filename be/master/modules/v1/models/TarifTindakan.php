<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "tariftindakan_m".
 *
 * @property int $tariftindakan_id
 * @property int $kelaspelayanan_id
 * @property int $komponentarif_id
 * @property int $daftartindakan_id
 * @property int $jenistarif_id
 * @property int $perdatarif_id
 * @property double $harga_tariftindakan
 * @property int $persendiskon_tindakan
 * @property double $hargadiskon_tindakan
 * @property int $persencyto_tindakan
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property string $deleted_date
 * @property int $deleted_by
 * @property int $tipepaket_id
 *
 * @property DaftarTindakan $daftartindakan
 * @property JenisTarif $jenistarif
 * @property KelasPelayanan $kelaspelayanan
 * @property KomponenTarif $komponentarif
 * @property PerdaTarif $perdatarif
 */
class TarifTindakan extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tariftindakan_m';
    }

    /**
     * {@inheritdoc}
     */
    
    // Validasi XSS form
    protected $xssProtected = [
        'harga_tariftindakan',
        'persendiskon_tindakan',
        'hargadiskon_tindakan',
        'persencyto_tindakan',
        'daftartindakan',
        'jenistarif',
        'kelaspelayanan',
        'komponentarif',
        'perdatarif',
        'additional_data',
        'persen_penyulit',
        'persentase_komponen'
    ];

    public function rules()
    {
        return [
            [['kelaspelayanan_id', 'harga_tariftindakan', 'hargadiskon_tindakan','penjamin_id'], 'required'],
            [['kelaspelayanan_id', 'komponentarif_id', 'daftartindakan_id', 'jenistarif_id', 'perdatarif_id', 'persendiskon_tindakan', 'persencyto_tindakan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'tipepaket_id','penjamin_id', 'persen_penyulit', 'dokter_id','ruangan_id', 'persentase_komponen'], 'default', 'value' => null],
            [['kelaspelayanan_id', 'komponentarif_id', 'daftartindakan_id', 'jenistarif_id', 'perdatarif_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'tipepaket_id','penjamin_id', 'tarifparent_id', 'dokter_id','ruangan_id'], 'integer'],
            [['harga_tariftindakan', 'hargadiskon_tindakan'], 'number'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active', 'created_date', 'last_modified_date', 'deleted_date','is_clone', 'tarifparent_id', 'kamarruangan_id', 'persen_penyulit', 'dokter_id', 'ruangan_id', 'persentase_komponen', 'is_persentase'], 'safe'],
            [['is_deleted', 'is_active', 'is_persentase'], 'boolean'],
            // [['daftartindakan_id'], 'exist', 'skipOnError' => true, 'targetClass' => DaftarTindakan::className(), 'targetAttribute' => ['daftartindakan_id' => 'daftartindakan_id']],
            // [['jenistarif_id'], 'exist', 'skipOnError' => true, 'targetClass' => JenisTarif::className(), 'targetAttribute' => ['jenistarif_id' => 'jenistarif_id']],
            // [['kelaspelayanan_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelasPelayanan::className(), 'targetAttribute' => ['kelaspelayanan_id' => 'kelaspelayanan_id']],
            // [['komponentarif_id'], 'exist', 'skipOnError' => true, 'targetClass' => KomponenTarif::className(), 'targetAttribute' => ['komponentarif_id' => 'komponentarif_id']],
            // [['perdatarif_id'], 'exist', 'skipOnError' => true, 'targetClass' => PerdaTarif::className(), 'targetAttribute' => ['perdatarif_id' => 'perdatarif_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tariftindakan_id' => 'Tariftindakan ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'komponentarif_id' => 'Komponentarif ID',
            'daftartindakan_id' => 'Daftartindakan ID',
            'jenistarif_id' => 'Jenistarif ID',
            'perdatarif_id' => 'Perdatarif ID',
            'penjamin_id' => 'Perjamin ID',
            'harga_tariftindakan' => 'Harga Tariftindakan',
            'persendiskon_tindakan' => 'Persendiskon Tindakan',
            'hargadiskon_tindakan' => 'Hargadiskon Tindakan',
            'persencyto_tindakan' => 'Persencyto Tindakan',
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
            'tipepaket_id' => 'Tipepaket ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    // CATATAN : code ini di comment karena terjadi error ketika penyimpanan tarif tindakan dengan message "Object class app ... could not be converted to string"
    // public function getDaftartindakan()
    // {
    //     return $this->hasOne(DaftarTindakan::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJenistarif()
    {
        return $this->hasOne(JenisTarif::className(), ['jenistarif_id' => 'jenistarif_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    // CATATAN : code ini di comment karena terjadi error ketika penyimpanan tarif tindakan dengan message "Object class app ... could not be converted to string"
    // public function getKelaspelayanan()
    // {
    //     return $this->hasOne(KelasPelayanan::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // CATATAN : code ini di comment karena terjadi error ketika penyimpanan tarif tindakan dengan message "Object class app ... could not be converted to string"
    // public function getKomponentarif()
    // {
    //     return $this->hasOne(KomponenTarif::className(), ['komponentarif_id' => 'komponentarif_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // CATATAN : code ini di comment karena terjadi error ketika penyimpanan tarif tindakan dengan message "Object class app ... could not be converted to string"
    // public function getPerdatarif()
    // {
    //     return $this->hasOne(PerdaTarif::className(), ['perdatarif_id' => 'perdatarif_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenjamin()
    {
        return $this->hasOne(Penjamin::className(), ['penjamin_id' => 'penjamin_id']);
    }

    
    public function extraFields()
    {
        return [
            'perdatarif_m' => function($item){
                return $item->perdatarif;
            },
            'kelaspelayanan_m' => function($item){
                return $item->kelaspelayanan;
            },
            'komponentarif_m' => function($item){
                return $item->komponentarif;
            },
            'daftartindakan_m' => function($item){
                return $item->daftartindakan;
            },
            'jenistarif_m' => function($item){
                return $item->jenistarif;
            }
        ];
    }

    public function countBy($condition)
    {
        return self::find()->andWhere($condition)->count();
    }
}
