<?php

namespace app\modules\apotek\models;

use Yii;

/**
 * This is the model class for table "stokobatalkes_t".
 *
 * @property int $stokobatalkes_id
 * @property int $ruangan_id
 * @property int $penerimaanobatdetail_id
 * @property int $terimamutasidetail_id
 * @property int $returresepdetail_id
 * @property int $returpenerimaanobatdetail_id
 * @property int $mutasiobatdetail_id
 * @property int $obatalkespasien_id
 * @property int $pemusnahanoadet_id
 * @property int $stokopnamedetail_id
 * @property int $obatalkes_id
 * @property string $tglkadaluarsa
 * @property string $nobatch
 * @property string $tglstok_in
 * @property string $tglstok_out
 * @property double $qtystok_in
 * @property double $qtystok_out
 * @property double $harganetto
 * @property double $persendiscount
 * @property double $jmldiscount
 * @property double $persenppn
 * @property double $persenpph
 * @property double $persenmargin
 * @property double $jmlmargin
 * @property bool $stokoa_aktif
 * @property int $stokobatalkesasal_id
 * @property int $satuankecil_id
 * @property string $tglterima
 * @property int $lokasiobat_id
 * @property int $rakobat_id
 * @property int $pemakaianobatdetail_id
 * @property int $produksiobat_id
 * @property int $produksiobatdet_id
 * @property int $stokinhand
 * @property int $storeeddetail_id
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
 * @property PenerimaanobatdetailT[] $penerimaanobatdetailTs
 */
class StokObatAlkes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stokobatalkes_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ruangan_id', 'obatalkes_id'], 'required'],
            [['ruangan_id', 'penerimaanobatdetail_id', 'terimamutasidetail_id', 'returresepdetail_id', 'returpenerimaanobatdetail_id', 'mutasiobatdetail_id', 'obatalkespasien_id', 'pemusnahanoadet_id', 'stokopnamedetail_id', 'obatalkes_id', 'stokobatalkesasal_id', 'satuankecil_id', 'lokasiobat_id', 'rakobat_id', 'pemakaianobatdetail_id', 'produksiobat_id', 'produksiobatdet_id', 'stokinhand', 'storeeddetail_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruangan_id', 'penerimaanobatdetail_id', 'terimamutasidetail_id', 'returresepdetail_id', 'returpenerimaanobatdetail_id', 'mutasiobatdetail_id', 'obatalkespasien_id', 'pemusnahanoadet_id', 'stokopnamedetail_id', 'obatalkes_id', 'stokobatalkesasal_id', 'satuankecil_id', 'lokasiobat_id', 'rakobat_id', 'pemakaianobatdetail_id', 'produksiobat_id', 'produksiobatdet_id', 'stokinhand', 'storeeddetail_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tglkadaluarsa', 'tglstok_in', 'tglstok_out', 'tglterima', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['qtystok_in', 'qtystok_out', 'harganetto', 'persendiscount', 'jmldiscount', 'persenppn', 'persenpph', 'persenmargin', 'jmlmargin'], 'number'],
            [['stokoa_aktif', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['nobatch'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'stokobatalkes_id' => Yii::t('fe','Stokobatalkes ID'),
            'ruangan_id' => Yii::t('fe','Ruangan ID'),
            'penerimaanobatdetail_id' => Yii::t('fe','Penerimaanobatdetail ID'),
            'terimamutasidetail_id' => Yii::t('fe','Terimamutasidetail ID'),
            'returresepdetail_id' => Yii::t('fe','Returresepdetail ID'),
            'returpenerimaanobatdetail_id' => Yii::t('fe','Returpenerimaanobatdetail ID'),
            'mutasiobatdetail_id' => Yii::t('fe','Mutasiobatdetail ID'),
            'obatalkespasien_id' => Yii::t('fe','Obatalkespasien ID'),
            'pemusnahanoadet_id' => Yii::t('fe','Pemusnahanoadet ID'),
            'stokopnamedetail_id' => Yii::t('fe','Stokopnamedetail ID'),
            'obatalkes_id' => Yii::t('fe','Obatalkes ID'),
            'tglkadaluarsa' => Yii::t('fe','Tglkadaluarsa'),
            'nobatch' => Yii::t('fe','Nobatch'),
            'tglstok_in' => Yii::t('fe','Tglstok In'),
            'tglstok_out' => Yii::t('fe','Tglstok Out'),
            'qtystok_in' => Yii::t('fe','Qtystok In'),
            'qtystok_out' => Yii::t('fe','Qtystok Out'),
            'harganetto' => Yii::t('fe','Harganetto'),
            'persendiscount' => Yii::t('fe','Persendiscount'),
            'jmldiscount' => Yii::t('fe','Jmldiscount'),
            'persenppn' => Yii::t('fe','Persenppn'),
            'persenpph' => Yii::t('fe','Persenpph'),
            'persenmargin' => Yii::t('fe','Persenmargin'),
            'jmlmargin' => Yii::t('fe','Jmlmargin'),
            'stokoa_aktif' => Yii::t('fe','Stokoa Aktif'),
            'stokobatalkesasal_id' => Yii::t('fe','Stokobatalkesasal ID'),
            'satuankecil_id' => Yii::t('fe','Satuankecil ID'),
            'tglterima' => Yii::t('fe','Tglterima'),
            'lokasiobat_id' => Yii::t('fe','Lokasiobat ID'),
            'rakobat_id' => Yii::t('fe','Rakobat ID'),
            'pemakaianobatdetail_id' => Yii::t('fe','Pemakaianobatdetail ID'),
            'produksiobat_id' => Yii::t('fe','Produksiobat ID'),
            'produksiobatdet_id' => Yii::t('fe','Produksiobatdet ID'),
            'stokinhand' => Yii::t('fe','Stokinhand'),
            'storeeddetail_id' => Yii::t('fe','Storeeddetail ID'),
            'additional_data' => Yii::t('fe','Additional Data'),
            'created_date' => Yii::t('fe','Created Date'),
            'created_by' => Yii::t('fe','Created By'),
            'modified_count' => Yii::t('fe','Modified Count'),
            'last_modified_date' => Yii::t('fe','Last Modified Date'),
            'last_modified_by' => Yii::t('fe','Last Modified By'),
            'is_deleted' => Yii::t('fe','Is Deleted'),
            'is_active' => Yii::t('fe','Is Active'),
            'deleted_date' => Yii::t('fe','Deleted Date'),
            'deleted_by' => Yii::t('fe','Deleted By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenerimaanobatdetailTs()
    {
        return $this->hasMany(PenerimaanobatdetailT::className(), ['stokobatalkes_id' => 'stokobatalkes_id']);
    }
}
