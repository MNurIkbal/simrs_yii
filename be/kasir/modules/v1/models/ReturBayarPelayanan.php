<?php

namespace app\modules\v1\models;

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
 * @property double $total_nontunai
 * @property double $jmlpembayaran
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
class ReturBayarPelayanan extends \Doco\components\DocoActiveRecord
{
    protected $xssProtected = [
        'keterangan_retur'
    ];

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
            [['ruangan_id', 'tandabuktibayar_id', 'tgl_returpelayanan', 'total_biayaretur', 'total_nontunai'], 'required'],
            [['returresep_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruangan_id', 'tandabuktibayar_id', 'returresep_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['ruangan_id', 'tandabuktibayar_id', 'tgl_returpelayanan', 'total_biayaretur', 'biaya_administrasi', 'created_date', 'last_modified_date', 'deleted_date', 'jmlpembayaran', 'total_nontunai'], 'safe'],
            [['total_obatretur', 'total_tindakanretur', 'total_biayaretur', 'biaya_administrasi', 'total_nontunai'], 'number'],
            [['keterangan_retur', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_returbayar'], 'string', 'max' => 50],
            [['tandabuktibayar_id'], 'exist', 'skipOnError' => true, 'targetClass' => TandaBuktiBayar::className(), 'targetAttribute' => ['tandabuktibayar_id' => 'tandabuktibayar_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'returbayarpelayanan_id' => Yii::t('app', 'Retur bayar pelayanan'),
            'ruangan_id' => Yii::t('app', 'Ruangan'),
            'tandabuktibayar_id' => Yii::t('app', 'Tanda bukti bayar'),
            'returresep_id' => Yii::t('app', 'Retur resep'),
            'tgl_returpelayanan' => Yii::t('app', 'Tanggal retur pelayanan'),
            'no_returbayar' => Yii::t('app', 'No retur bayar'),
            'total_obatretur' => Yii::t('app', 'Total obat retur'),
            'total_tindakanretur' => Yii::t('app', 'Total tindakan retur'),
            'total_biayaretur' => Yii::t('app', 'Total biaya retur'),
            'total_nontunai' => Yii::t('app', 'Total Non Tunai'),
            'biaya_administrasi' => Yii::t('app', 'Biaya administrasi'),
            'keterangan_retur' => Yii::t('app', 'Keterangan retur'),
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

    public function getTandaBuktiBayar()
    {
        return $this->hasOne(TandaBuktiBayar::className(), ['tandabuktibayar_id' => 'tandabuktibayar_id']);
    }

    public function extraFields()
    {
        return [
            'tandabuktibayar_t' => function($item){
                return $item->tandaBuktiBayar;
            },
            'tandabuktikeluar_t' => function($item){
                return $item->tandaBuktiKeluar;
            }
        ];
    }



    public function checkJmlPembayaran($attribute, $param)
    {
        if ($this->total_biayaretur > $this->jmlpembayaran) {
            $this->addError('total_biayaretur',Yii::t('fe','Total Retur Tidak Boleh Lebih dari Jumlah Pembayaran'));
        }
    }

    public function checkTotalRetur($attribute, $param)
    {
        if ($this->total_biayaretur < 1) {
            $this->addError('total_biayaretur',Yii::t('fe','Total Retur Tidak Boleh Kurang Dari 0 (nol)'));
        }
    }
}
