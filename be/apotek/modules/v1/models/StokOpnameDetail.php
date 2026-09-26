<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "stokopnamedetail_t".
 *
 * @property integer $stokopnamedetail_id
 * @property integer $formstokopname_id
 * @property integer $satuankecil_id
 * @property integer $sumberdana_id
 * @property integer $stokopname_id
 * @property integer $obatalkes_id
 * @property double $volume_fisik
 * @property double $volume_sistem
 * @property double $revisi_stok
 * @property double $hargasatuan
 * @property double $jumlahharga
 * @property double $harganetto
 * @property double $jumlahnetto
 * @property string $tglkadaluarsa
 * @property string $kondisibarang
 * @property string $tglperiksafisik
 * @property double $jmlselisihstok
 * @property string $additional_data
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property boolean $is_newso
 * @property string $deleted_date
 * @property integer $deleted_by
 */
class StokOpnameDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stokopnamedetail_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['stokopnamedetail_id'], 'required'],
            [['stokopnamedetail_id', 'formstokopname_id', 'satuankecil_id', 'sumberdana_id', 'stokopname_id', 'obatalkes_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['volume_fisik', 'volume_sistem', 'hargasatuan', 'jumlahharga', 'harganetto', 'jumlahnetto', 'jmlselisihstok','stok_akhir','selisih_akhir','revisi_stok'], 'number'],
            [['tglkadaluarsa', 'tglperiksafisik','revisi_stok', 'created_date', 'last_modified_date', 'deleted_date', 'stokobatalkes_id','stok_akhir','selisih_akhir'], 'safe'],
            [['kondisibarang', 'additional_data'], 'string'],
            [['is_deleted', 'is_active','is_newso'], 'boolean'],
            [['stokopname_id'], 'exist', 'skipOnError' => true, 'targetClass' => StokOpname::className(), 'targetAttribute' => ['stokopname_id' => 'stokopname_id']],
            [['obatalkes_id'], 'exist', 'skipOnError' => true, 'targetClass' => StokOpname::className(), 'targetAttribute' => ['obatalkes_id' => 'obatalkes_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'stokopnamedetail_id' => Yii::t('app', 'Stok opname detail'),
            'formstokopname_id' => Yii::t('app', 'Form stok opname'),
            'satuankecil_id' => Yii::t('app', 'Satuan kecil'),
            'sumberdana_id' => Yii::t('app', 'Sumber dana'),
            'stokopname_id' => Yii::t('app', 'Stok opname'),
            'obatalkes_id' => Yii::t('app', 'Obat alkes'),
            'volume_fisik' => Yii::t('app', 'Volume fisik'),
            'revisi_stok' => Yii::t('app', 'Revisi Stok'),
            'volume_sistem' => Yii::t('app', 'Volume sistem'),
            'hargasatuan' => Yii::t('app', 'Harga satuan'),
            'jumlahharga' => Yii::t('app', 'Jumlah harga'),
            'harganetto' => Yii::t('app', 'Harga netto'),
            'jumlahnetto' => Yii::t('app', 'Jumlah netto'),
            'tglkadaluarsa' => Yii::t('app', 'Tanggal kadaluarsa'),
            'kondisibarang' => Yii::t('app', 'Kondisi barang'),
            'tglperiksafisik' => Yii::t('app', 'Tanggal periksa fisik'),
            'jmlselisihstok' => Yii::t('app', 'Jumlah selisih stok'),
            'additional_data' => Yii::t('app', 'Additional Data'),
            'created_date' => Yii::t('app', 'Created Date'),
            'created_by' => Yii::t('app', 'Created By'),
            'modified_count' => Yii::t('app', 'Modified Count'),
            'last_modified_date' => Yii::t('app', 'Last Modified Date'),
            'last_modified_by' => Yii::t('app', 'Last Modified By'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'is_active' => Yii::t('app', 'Is Active'),
            'deleted_date' => Yii::t('app', 'Deleted Date'),
            'deleted_by' => Yii::t('app', 'Deleted By'),
            'is_newso' => Yii::t('app', 'New SO'),
        ];
    }
    
    public function getStokOpname()
    {
        return $this->hasOne(StokOpname::className(), ['stokopname_id' => 'stokopname_id']);
    }
    
    public function getObatAlkes()
    {
        return $this->hasOne(ObatAlkes::className(), ['obatalkes_id' => 'obatalkes_id']);
    }
    
    public function extraFields()
    {
        return [
            'stokopname_t' => function($item){
                return $item->stokOpname;
            },
            'obatalkes_m' => function($item){
                return $item->obatAlkes;
            }
        ];
    }
}
