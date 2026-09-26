<?php
/*
* @Author: metafiliana 
* @Date: 2018-01-23 13:27:47 
 * @Last Modified by:   afil
 * @Last Modified time: 2018-03-28 13:40:11
* @Description: 
*/
namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "paketbmhp_m".
 *
 * @property int $paketbmhp_id
 * @property int $obatalkes_id
 * @property int $satuankecil_id
 * @property int $tipepaket_id
 * @property int $daftartindakan_id
 * @property int $kelompokumur_id
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
 * @property KelompokumurM $kelompokumur
 * @property ObatalkesM $obatalkes
 * @property SatuankecilM $satuankecil
 * @property TipepaketM $tipepaket
 */
class PaketBmhp extends \Doco\components\DocoActiveRecord
{
    public $daftartindakan_nama;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'paketbmhp_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['obatalkes_id', 'satuankecil_id', 'tipepaket_id', 'daftartindakan_id', 'kelompokumur_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['obatalkes_id', 'satuankecil_id', 'tipepaket_id', 'daftartindakan_id', 'kelompokumur_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['qty_pemakaian', 'qty_stokout', 'hargapemakaian'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['daftartindakan_id'], 'exist', 'skipOnError' => true, 'targetClass' => DaftarTindakan::className(), 'targetAttribute' => ['daftartindakan_id' => 'daftartindakan_id']],
            // [['kelompokumur_id'], 'exist', 'skipOnError' => true, 'targetClass' => Kelompokumur::className(), 'targetAttribute' => ['kelompokumur_id' => 'kelompokumur_id']],
            [['obatalkes_id'], 'exist', 'skipOnError' => true, 'targetClass' => ObatAlkes::className(), 'targetAttribute' => ['obatalkes_id' => 'obatalkes_id']],
            // [['satuankecil_id'], 'exist', 'skipOnError' => true, 'targetClass' => SatuanKecil::className(), 'targetAttribute' => ['satuankecil_id' => 'satuankecil_id']],
            // [['tipepaket_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipePaket::className(), 'targetAttribute' => ['tipepaket_id' => 'tipepaket_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'paketbmhp_id' => 'Paketbmhp ID',
            'obatalkes_id' => 'Obatalkes ID',
            'satuankecil_id' => 'Satuankecil ID',
            'tipepaket_id' => 'Tipepaket ID',
            'daftartindakan_id' => 'Daftartindakan ID',
            'kelompokumur_id' => 'Kelompokumur ID',
            'qty_pemakaian' => 'Qty Pemakaian',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDaftartindakan()
    {
        return $this->hasOne(DaftarTindakan::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKelompokumur()
    {
        return $this->hasOne(KelompokUmur::className(), ['kelompokumur_id' => 'kelompokumur_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getObatalkes()
    {
        return $this->hasOne(ObatAlkes::className(), ['obatalkes_id' => 'obatalkes_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSatuankecil()
    {
        return $this->hasOne(SatuanKecil::className(), ['satuankecil_id' => 'satuankecil_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipepaket()
    {
        return $this->hasOne(TipePaket::className(), ['tipepaket_id' => 'tipepaket_id']);
    }

    public function extraFields()
    {
        return [
            'daftartindakan_m' => function($item){
                return $item->daftartindakan;
            },
            'obatalkes_m' => function($item){
                return $item->obatalkes;
            },
        ];
    }
}
