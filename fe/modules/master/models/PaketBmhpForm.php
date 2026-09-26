<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "paketbmhp_m".
 *
 * @property int $paketbmhp_id
 * @property int $obatalkes_id
 * @property int $satuankecil_id
 * @property int $tipepaket_id
 * @property int $daftartindakan_id
 * @property int $golonganumur_id
 * @property double $qty_pemakaian
 * @property double $qty_stokout
 * @property double $hargapemakaian
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
 * @property DaftartindakanM $daftartindakan
 * @property GolonganumurM $golonganumur
 * @property ObatalkesM $obatalkes
 * @property SatuanunitM $satuankecil
 * @property TipepaketM $tipepaket
 */
class PaketBmhpForm extends \yii\base\Model
{
    /**
     * {@inheritdoc}
     */
    
    public $satuankecil_nama;
    public $operasi_nama;
    public $obatalkes_nama;

    public $paketbmhp_id;
    public $obatalkes_id;
    public $satuankecil_id;
    public $tipepaket_id;
    public $daftartindakan_id;
    public $golonganumur_id;
    public $qty_pemakaian;
    public $qty_stokout;
    public $hargapemakaian;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;

    public static function tableName()
    {
        return 'paketbmhp_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['obatalkes_id', 'daftartindakan_id', 'qty_pemakaian'], 'required'],
            [['obatalkes_id', 'satuankecil_id', 'tipepaket_id', 'daftartindakan_id', 'golonganumur_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['obatalkes_id', 'satuankecil_id', 'tipepaket_id', 'daftartindakan_id', 'golonganumur_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['qty_pemakaian', 'qty_stokout', 'hargapemakaian'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['qty_pemakaian'], 'number', 'min' => 1],
            // [['daftartindakan_id'], 'exist', 'skipOnError' => true, 'targetClass' => DaftartindakanM::className(), 'targetAttribute' => ['daftartindakan_id' => 'daftartindakan_id']],
            // [['golonganumur_id'], 'exist', 'skipOnError' => true, 'targetClass' => GolonganumurM::className(), 'targetAttribute' => ['golonganumur_id' => 'golonganumur_id']],
            // [['obatalkes_id'], 'exist', 'skipOnError' => true, 'targetClass' => ObatalkesM::className(), 'targetAttribute' => ['obatalkes_id' => 'obatalkes_id']],
            // [['satuankecil_id'], 'exist', 'skipOnError' => true, 'targetClass' => SatuanunitM::className(), 'targetAttribute' => ['satuankecil_id' => 'satuanunit_id']],
            // [['tipepaket_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipepaketM::className(), 'targetAttribute' => ['tipepaket_id' => 'tipepaket_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'paketbmhp_id' => 'Paketbmhp ID',
            'obatalkes_id' => 'Obat Alkes',
            'satuankecil_id' => 'Satuankecil ID',
            'tipepaket_id' => 'Tipepaket ID',
            'daftartindakan_id' => 'Nama Operasi',
            'golonganumur_id' => 'Golonganumur ID',
            'qty_pemakaian' => 'Jumlah Pemakaian',
            'qty_stokout' => 'Qty Stokout',
            'hargapemakaian' => 'Hargapemakaian',
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

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getDaftartindakan()
    // {
    //     return $this->hasOne(DaftartindakanM::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getGolonganumur()
    // {
    //     return $this->hasOne(GolonganumurM::className(), ['golonganumur_id' => 'golonganumur_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getObatalkes()
    // {
    //     return $this->hasOne(ObatalkesM::className(), ['obatalkes_id' => 'obatalkes_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getSatuankecil()
    // {
    //     return $this->hasOne(SatuanunitM::className(), ['satuanunit_id' => 'satuankecil_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getTipepaket()
    // {
    //     return $this->hasOne(TipepaketM::className(), ['tipepaket_id' => 'tipepaket_id']);
    // }
}
