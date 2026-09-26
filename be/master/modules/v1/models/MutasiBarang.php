<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "mutasibarang_t".
 *
 * @property int $mutasibarang_id
 * @property int $pesanbarang_id
 * @property int $pegawaipengirim_id
 * @property int $pegawaimengetahui_id
 * @property int $ruangantujuan_id
 * @property string $tgl_mutasibarang
 * @property string $nomutasi_barang
 * @property string $keterangan_mutasi
 * @property double $totalhargamutasi
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
 */
class MutasiBarang extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mutasibarang_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pesanbarang_id', 'pegawaipengirim_id', 'pegawaimengetahui_id', 'ruangantujuan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pesanbarang_id', 'pegawaipengirim_id', 'pegawaimengetahui_id', 'ruangantujuan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['pegawaipengirim_id', 'ruangantujuan_id', 'tgl_mutasibarang', 'nomutasi_barang', 'totalhargamutasi'], 'required'],
            [['tgl_mutasibarang', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['keterangan_mutasi', 'additional_data'], 'string'],
            [['totalhargamutasi'], 'number'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['nomutasi_barang'], 'string', 'max' => 50],
            [['ruangantujuan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ruangan::className(), 'targetAttribute' => ['ruangan_id' => 'ruangantujuan_id']],
            // [['pegawaimengetahui_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['pegawaimengetahui_id' => 'pegawai_id']],
            // [['pesanbarang_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pesanbarang::className(), 'targetAttribute' => ['pesanbarang_id' => 'pesanbarang_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mutasibarang_id' => Yii::t('app', 'Mutasi barang'),
            'pesanbarang_id' => Yii::t('app', 'Pesan barang'),
            'pegawaipengirim_id' => Yii::t('app', 'Pegawai pengirim'),
            'pegawaimengetahui_id' => Yii::t('app', 'Pegawai mengetahui'),
            'ruangantujuan_id' => Yii::t('app', 'Ruangan tujuan'),
            'tgl_mutasibarang' => Yii::t('app', 'Tanggal mutasi barang'),
            'nomutasi_barang' => Yii::t('app', 'No mutasi barang'),
            'keterangan_mutasi' => Yii::t('app', 'Keterangan mMutasi'),
            'totalhargamutasi' => Yii::t('app', 'Total harga mutasi'),
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
