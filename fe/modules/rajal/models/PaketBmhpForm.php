<?php
/*
 * @Author: metafiliana 
 * @Date: 2018-01-23 13:29:03 
 * @Last Modified by:   afil
 * @Last Modified time: 2018-03-28 14:16:58
 * @Description: 
 */
namespace app\modules\rajal\models;


use Yii;

class PaketBmhpForm extends \yii\base\Model
{

    public $paketbmhp_id;
    public $obatalkes_id;
    public $satuankecil_id;
    public $tipepaket_id;
    public $daftartindakan_id;
    public $kelompokumur_id;
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
            [['tipepaket_id', 'qty_pemakaian', 'qty_stokout'], 'required'],
            [['qty_pemakaian', 'qty_stokout', 'hargapemakaian'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'paketbmhp_id' => Yii::t('fe', 'paketbmhp_id'),
            'obatalkes_id' => Yii::t('fe', 'obatalkes_id'),
            'satuankecil_id' => Yii::t('fe', 'satuankecil_id'),
            'tipepaket_id' => Yii::t('fe', 'tipepaket_id'),
            'daftartindakan_id' => Yii::t('fe', 'daftartindakan_id'),
            'kelompokumur_id' => Yii::t('fe', 'kelompokumur_id'),
            'qty_pemakaian' => Yii::t('fe', 'qty_pemakaian'),
            'qty_stokout' => Yii::t('fe', 'qty_stokout'),
            'hargapemakaian' => Yii::t('fe', 'hargapemakaian'),
            'additional_data' => Yii::t('fe', 'additional_data'),
            'created_date' => Yii::t('fe', 'created_date'),
            'created_by' => Yii::t('fe', 'created_by'),
            'modified_count' => Yii::t('fe', 'modified_count'),
            'last_modified_date' => Yii::t('fe', 'last_modified_date'),
            'last_modified_by' => Yii::t('fe', 'last_modified_by'),
            'is_deleted' => Yii::t('fe', 'is_deleted'),
            'is_active' => Yii::t('fe', 'is_active'),
            'deleted_date' => Yii::t('fe', 'deleted_date'),
            'deleted_by' => Yii::t('fe', 'deleted_by'),
        ];
    }
}