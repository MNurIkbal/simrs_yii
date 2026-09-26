<?php

namespace app\modules\kasir\models;

use Yii;

/**
 * This is the model class for table "returbayarpelayanan_t".
 *
 * @property int $returbayarpelayanan_id
 * @property int $ruangan_id
 * @property int $tandabuktikeluar_id
 * @property int $tandabuktibayar_id
 * @property int $returresep_id
 * @property string $tgl_returpelayanan
 * @property string $no_returbayar
 * @property double $total_obatretur
 * @property double $total_tindakanretur
 * @property double $total_biayaretur
 * @property double $biaya_administrasi
 * @property string $keterangan_retur
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
class ReturBayarPelayananForm extends \yii\base\Model
{
    
    public $returbayarpelayanan_id;
    public $ruangan_id;
    public $tandabuktikeluar_id;
    public $tandabuktibayar_id;
    public $returresep_id;
    public $tgl_returpelayanan;
    public $no_returbayar;
    public $total_obatretur;
    public $total_tindakanretur;
    public $total_biayaretur;
    public $biaya_administrasi;
    public $keterangan_retur;
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
        return 'returbayarpelayanan_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ruangan_id', 'tgl_returpelayanan', 'no_returbayar', 'total_tindakanretur', 'total_biayaretur'], 'required'],
            [['ruangan_id', 'tandabuktikeluar_id', 'tandabuktibayar_id', 'returresep_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruangan_id', 'tandabuktikeluar_id', 'tandabuktibayar_id', 'returresep_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_returpelayanan', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['total_obatretur', 'total_tindakanretur', 'total_biayaretur', 'biaya_administrasi'], 'number'],
            [['keterangan_retur', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_returbayar'], 'string', 'max' => 50],
            // [['returresep_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReturresepT::className(), 'targetAttribute' => ['returresep_id' => 'returresep_id']],
            // [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => RuanganM::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
            // [['tandabuktibayar_id'], 'exist', 'skipOnError' => true, 'targetClass' => TandaBuktiBayar::className(), 'targetAttribute' => ['tandabuktibayar_id' => 'tandabuktibayar_id']],
            // [['tandabuktikeluar_id'], 'exist', 'skipOnError' => true, 'targetClass' => TandaBuktiKeluar::className(), 'targetAttribute' => ['tandabuktikeluar_id' => 'tandabuktikeluar_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'returbayarpelayanan_id' => Yii::t('fe', 'Retur bayar pelayanan'),
            'ruangan_id' => Yii::t('fe', 'Ruangan'),
            'tandabuktikeluar_id' => Yii::t('fe', 'Tanda bukti keluar'),
            'tandabuktibayar_id' => Yii::t('fe', 'Tanda bukti bayar'),
            'returresep_id' => Yii::t('fe', 'Retur resep'),
            'tgl_returpelayanan' => Yii::t('fe', 'Tanggal retur pelayanan'),
            'no_returbayar' => Yii::t('fe', 'No retur bayar'),
            'total_obatretur' => Yii::t('fe', 'Total obat retur'),
            'total_tindakanretur' => Yii::t('fe', 'Total tindakan retur'),
            'total_biayaretur' => Yii::t('fe', 'Total biaya retur'),
            'biaya_administrasi' => Yii::t('fe', 'Biaya administrasi'),
            'keterangan_retur' => Yii::t('fe', 'Keterangan retur'),
            'additional_data' => Yii::t('fe', 'Additional Data'),
            'created_date' => Yii::t('fe', 'Created Date'),
            'created_by' => Yii::t('fe', 'Created By'),
            'modified_count' => Yii::t('fe', 'Modified Count'),
            'last_modified_date' => Yii::t('fe', 'Last Modified Date'),
            'last_modified_by' => Yii::t('fe', 'Last Modified By'),
            'is_deleted' => Yii::t('fe', 'Is Deleted'),
            'is_active' => Yii::t('fe', 'Is Active'),
            'deleted_date' => Yii::t('fe', 'Deleted Date'),
            'deleted_by' => Yii::t('fe', 'Deleted By'),
        ];
    }
}
