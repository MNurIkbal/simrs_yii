<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "mutasiobatruangan_t".
 *
 * @property int $mutasiobatruangan_id
 * @property int $pesanobatalkes_id
 * @property int $terimamutasi_id
 * @property string $tglmutasioa
 * @property string $nomutasioa
 * @property int $ruanganasal_id
 * @property int $ruangantujuan_id
 * @property string $keteranganmutasi
 * @property double $totalharganettomutasi
 * @property double $totalhargajual
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
 * @property int $status_mutasi
 */
class MutasiObatRuangan extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mutasiobatruangan_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mutasiobatruangan_id'], 'required'],
            [['mutasiobatruangan_id', 'pesanobatalkes_id', 'terimamutasi_id', 'ruanganasal_id', 'ruangantujuan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'status_mutasi'], 'default', 'value' => null],
            [['mutasiobatruangan_id', 'pesanobatalkes_id', 'terimamutasi_id', 'ruanganasal_id', 'ruangantujuan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'status_mutasi'], 'integer'],
            [['tglmutasioa', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['nomutasioa', 'keteranganmutasi', 'additional_data'], 'string'],
            [['totalharganettomutasi', 'totalhargajual'], 'number'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['mutasiobatruangan_id'], 'unique'],
            [['ruangantujuan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ruangan::className(), 'targetAttribute' => ['ruangan_id' => 'ruangantujuan_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mutasiobatruangan_id' => Yii::t('app', 'Mutasi obat ruangan'),
            'pesanobatalkes_id' => Yii::t('app', 'Pesan obat alkes'),
            'terimamutasi_id' => Yii::t('app', 'Terima mutasi'),
            'tglmutasioa' => Yii::t('app', 'Tanggal mutasi oa'),
            'nomutasioa' => Yii::t('app', 'No mutasi oa'),
            'ruanganasal_id' => Yii::t('app', 'Ruangan asal'),
            'ruangantujuan_id' => Yii::t('app', 'Ruangan tujuan'),
            'keteranganmutasi' => Yii::t('app', 'Keterangan mutasi'),
            'totalharganettomutasi' => Yii::t('app', 'Total harga netto mutasi'),
            'totalhargajual' => Yii::t('app', 'Total harga jual'),
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
            'status_mutasi' => Yii::t('app', 'Status Mutasi'),
        ];
    }
    
    public function getRuangan()
    {
        return $this->hasOne(Ruangan::className(), ['ruangan_id' => 'ruangantujuan_id']);
    }
    
    public function extraFields()
    {
        return [
            'ruangan_m' => function($item){
                return $item->ruangan;
            },
            'instalasi_m' => function($item){
                return @$item->ruangan->instalasi;
            }
        ];
    }
}
