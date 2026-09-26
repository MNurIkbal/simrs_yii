<?php

/**
*  @author yaya
*/
namespace app\modules\v1\models;

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
class StokObatAlkes extends \Doco\components\DocoActiveRecord
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
            [['ruangan_id', 'penerimaanobatdetail_id', 'terimamutasidetail_id', 'returresepdetail_id', 'returpenerimaanobatdetail_id', 'mutasiobatdetail_id', 'obatalkespasien_id', 'pemusnahanobatdetail_id', 'stokopnamedetail_id', 'obatalkes_id', 'stokobatalkesasal_id', 'satuankecil_id', 'lokasiobat_id', 'rakobat_id', 'pemakaianobatdetail_id', 'produksiobatdetail_id', 'storexpiredobatdetail_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruangan_id', 'penerimaanobatdetail_id', 'terimamutasidetail_id', 'returresepdetail_id', 'returpenerimaanobatdetail_id', 'mutasiobatdetail_id', 'obatalkespasien_id', 'pemusnahanobatdetail_id', 'stokopnamedetail_id', 'obatalkes_id', 'stokobatalkesasal_id', 'satuankecil_id', 'lokasiobat_id', 'rakobat_id', 'pemakaianobatdetail_id', 'produksiobatdetail_id', 'storexpiredobatdetail_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tglkadaluarsa', 'tglstok_in', 'tglstok_out', 'tglterima', 'created_date', 'last_modified_date', 'deleted_date', 'jmlppn', 'harganetto'], 'safe'],
            [['qtystok_in', 'qtystok_out', 'harganetto', 'persendiscount', 'jmldiscount', 'persenppn', 'persenpph', 'persenmargin', 'jmlmargin'], 'number'],
            [['stokoa_aktif', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'stokobatalkes_id' => 'Stokobatalkes ID',
            'ruangan_id' => 'Ruangan ID',
            'penerimaanobatdetail_id' => 'Penerimaanobatdetail ID',
            'terimamutasidetail_id' => 'Terimamutasidetail ID',
            'returresepdetail_id' => 'Returresepdetail ID',
            'returpenerimaanobatdetail_id' => 'Returpenerimaanobatdetail ID',
            'mutasiobatdetail_id' => 'Mutasiobatdetail ID',
            'obatalkespasien_id' => 'Obatalkespasien ID',
            'pemusnahanobatdetail_id' => 'Pemusnahanobatdetail ID',
            'stokopnamedetail_id' => 'Stokopnamedetail ID',
            'obatalkes_id' => 'Obatalkes ID',
            'tglkadaluarsa' => 'Tglkadaluarsa',
            'nobatch' => 'Nobatch',
            'tglstok_in' => 'Tglstok In',
            'tglstok_out' => 'Tglstok Out',
            'qtystok_in' => 'Qtystok In',
            'qtystok_out' => 'Qtystok Out',
            'harganetto' => 'Harganetto',
            'persendiscount' => 'Persendiscount',
            'jmldiscount' => 'Jmldiscount',
            'persenppn' => 'Persenppn',
            'persenpph' => 'Persenpph',
            'persenmargin' => 'Persenmargin',
            'jmlmargin' => 'Jmlmargin',
            'stokoa_aktif' => 'Stokoa Aktif',
            'stokobatalkesasal_id' => 'Stokobatalkesasal ID',
            'satuankecil_id' => 'Satuankecil ID',
            'tglterima' => 'Tglterima',
            'lokasiobat_id' => 'Lokasiobat ID',
            'rakobat_id' => 'Rakobat ID',
            'pemakaianobatdetail_id' => 'Pemakaianobatdetail ID',
            'produksiobatdetail_id' => 'Produksiobatdetail ID',
            'storexpiredobatdetail_id' => 'Storexpiredobatdetail ID',
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
    public function getPenerimaanobatdetailTs()
    {
        return $this->hasMany(PenerimaanobatdetailT::className(), ['stokobatalkes_id' => 'stokobatalkes_id']);
    }
}

