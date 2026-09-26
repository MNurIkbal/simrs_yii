<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "obatalkes_m".
 *
 * @property integer $obatalkes_id
 * @property integer $jenisobatalkes_id
 * @property integer $sumberdana_id
 * @property integer $lokasigudang_id
 * @property integer $satuankecil_id
 * @property integer $satuanbesar_id
 * @property integer $subjenis_id
 * @property integer $generik_id
 * @property string $obatalkes_barcode
 * @property string $obatalkes_kode
 * @property string $obatalkes_namalain
 * @property string $obatalkes_golongan
 * @property string $obatalkes_kategori
 * @property string $obatalkes_kadarobat
 * @property integer $kemasanbesar
 * @property integer $kekuatan
 * @property string $satuankekuatan
 * @property double $ppn_persen
 * @property double $harganetto
 * @property double $hargajual
 * @property double $hargamaksimum
 * @property double $hargaminimum
 * @property double $hargaratarata
 * @property double $margin
 * @property double $discount
 * @property string $tglkadaluarsa
 * @property integer $minimalstok
 * @property string $formularium
 * @property boolean $discountinue
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
class ObatAlkes extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'obatalkes_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jenisobatalkes_id', 'sumberdana_id', 'lokasigudang_id', 'satuankecil_id', 'satuanbesar_id', 'subjenis_id', 'generik_id', 'kemasanbesar', 'kekuatan', 'minimalstok', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['obatalkes_barcode', 'obatalkes_kode', 'obatalkes_namalain', 'obatalkes_golongan', 'obatalkes_kategori', 'obatalkes_kadarobat', 'satuankekuatan', 'formularium', 'additional_data'], 'string'],
            [['ppn_persen', 'harganetto', 'hargajual', 'hargamaksimum', 'hargaminimum', 'hargaratarata', 'margin', 'discount'], 'number'],
            [['tglkadaluarsa', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['discountinue', 'is_deleted', 'is_active'], 'boolean'],
            // [['generik_id'], 'exist', 'skipOnError' => true, 'targetClass' => GenerikM::className(), 'targetAttribute' => ['generik_id' => 'generik_id']],
            // [['jenisobatalkes_id'], 'exist', 'skipOnError' => true, 'targetClass' => JenisobatalkesM::className(), 'targetAttribute' => ['jenisobatalkes_id' => 'jenisobatalkes_id']],
            // [['lokasigudang_id'], 'exist', 'skipOnError' => true, 'targetClass' => LokasigudangM::className(), 'targetAttribute' => ['lokasigudang_id' => 'lokasigudang_id']],
            // [['satuanbesar_id'], 'exist', 'skipOnError' => true, 'targetClass' => SatuanbesarM::className(), 'targetAttribute' => ['satuanbesar_id' => 'satuanbesar_id']],
            // [['satuankecil_id'], 'exist', 'skipOnError' => true, 'targetClass' => SatuankecilM::className(), 'targetAttribute' => ['satuankecil_id' => 'satuankecil_id']],
            // [['subjenis_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjenisM::className(), 'targetAttribute' => ['subjenis_id' => 'subjenis_id']],
            // [['sumberdana_id'], 'exist', 'skipOnError' => true, 'targetClass' => SumberdanaM::className(), 'targetAttribute' => ['sumberdana_id' => 'sumberdana_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'obatalkes_id' => Yii::t('app', 'Obat alkes'),
            'jenisobatalkes_id' => Yii::t('app', 'Jenis obat alkes'),
            'sumberdana_id' => Yii::t('app', 'Sumber dana'),
            'lokasigudang_id' => Yii::t('app', 'Lokasi gudang'),
            'satuankecil_id' => Yii::t('app', 'Satuan kecil'),
            'satuanbesar_id' => Yii::t('app', 'Satuan besar'),
            'subjenis_id' => Yii::t('app', 'Sub jenis'),
            'generik_id' => Yii::t('app', 'Generik'),
            'obatalkes_barcode' => Yii::t('app', 'Obat alkes barcode'),
            'obatalkes_kode' => Yii::t('app', 'Obat alkes kode'),
            'obatalkes_namalain' => Yii::t('app', 'Nama lainnya'),
            'obatalkes_golongan' => Yii::t('app', 'Golongan obat alkes'),
            'obatalkes_kategori' => Yii::t('app', 'Obat alkes kategori'),
            'obatalkes_kadarobat' => Yii::t('app', 'Obat alkes kadarobat'),
            'kemasanbesar' => Yii::t('app', 'Kemasan besar'),
            'kekuatan' => Yii::t('app', 'Kekuatan'),
            'satuankekuatan' => Yii::t('app', 'Satuan kekuatan'),
            'ppn_persen' => Yii::t('app', 'Ppn persen'),
            'harganetto' => Yii::t('app', 'Harga netto'),
            'hargajual' => Yii::t('app', 'Harga jual'),
            'hargamaksimum' => Yii::t('app', 'Harga maksimum'),
            'hargaminimum' => Yii::t('app', 'Harga minimum'),
            'hargaratarata' => Yii::t('app', 'Harga rata rata'),
            'margin' => Yii::t('app', 'Margin'),
            'discount' => Yii::t('app', 'Discount'),
            'tglkadaluarsa' => Yii::t('app', 'Tanggal kadaluarsa'),
            'minimalstok' => Yii::t('app', 'Minimal stok'),
            'formularium' => Yii::t('app', 'Formularium'),
            'discountinue' => Yii::t('app', 'Discountinue'),
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
