<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "stokopname_t".
 *
 * @property integer $stokopname_id
 * @property integer $ruangan_id
 * @property integer $formuliropname_id
 * @property string $tglstokopname
 * @property string $nostokopname
 * @property boolean $isstokawal
 * @property string $jenisstokopname
 * @property string $keterangan_opname
 * @property double $totalharga
 * @property double $totalnetto
 * @property integer $mengetahui_id
 * @property integer $petugas1_id
 * @property string $additional_data
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 */
class StokOpname extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stokopname_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['stokopname_id'], 'required'],
            [['stokopname_id', 'ruangan_id', 'formuliropname_id', 'mengetahui_id', 'petugas1_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tglstokopname', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['nostokopname', 'jenisstokopname', 'keterangan_opname', 'additional_data'], 'string'],
            [['isstokawal', 'is_deleted', 'is_active'], 'boolean'],
            [['totalharga', 'totalnetto'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'stokopname_id' => Yii::t('app', 'Stok opname'),
            'ruangan_id' => Yii::t('app', 'Ruangan'),
            'formuliropname_id' => Yii::t('app', 'Formulir opname'),
            'tglstokopname' => Yii::t('app', 'Tanggal stok opname'),
            'nostokopname' => Yii::t('app', 'No stok opname'),
            'isstokawal' => Yii::t('app', 'Is stok awal'),
            'jenisstokopname' => Yii::t('app', 'Jenis stok opname'),
            'keterangan_opname' => Yii::t('app', 'Keterangan opname'),
            'totalharga' => Yii::t('app', 'Total harga'),
            'totalnetto' => Yii::t('app', 'Total netto'),
            'mengetahui_id' => Yii::t('app', 'Mengetahui'),
            'petugas1_id' => Yii::t('app', 'Petugas 1'),
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
        ];
    }
}
