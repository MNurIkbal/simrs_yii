<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "penjualanresep_t".
 *
 * @property int $penjualanresep_id
 * @property int $pasienadmisi_id
 * @property int $pegawai_id
 * @property int $pendaftaran_id
 * @property int $returresep_id
 * @property int $kelaspelayanan_id
 * @property int $penjamin_id
 * @property int $pasien_id
 * @property int $carabayar_id
 * @property int $ruangan_id
 * @property int $reseptur_id
 * @property int $shift_id
 * @property string $tglpenjualan
 * @property string $jenispenjualan
 * @property string $tglresep
 * @property string $noresep
 * @property double $totharganetto
 * @property double $totalhargajual
 * @property double $totaltarifservice
 * @property double $biayaadministrasi
 * @property double $biayakonseling
 * @property double $pembulatanharga
 * @property double $jasadokterresep
 * @property double $discount
 * @property double $subsidiasuransi
 * @property double $subsidipemerintah
 * @property double $subsidirs
 * @property double $iurbiaya
 * @property int $lamapelayanan
 * @property int $penjpasienpegawai_id
 * @property int $penjpasienruangan_id
 * @property int $antrianfarmasi_id
 * @property int $permohonanoa_id
 * @property double $takaranresep
 * @property bool $isresepperawatan
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
class PelayananJasaDokter extends \Doco\components\DocoActiveRecord
{
    protected $xssProtected = [
        'total_jasa',
        'deskripsi',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pelayananjasadokter_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tgl_transaksi', 'jasadokter_id', 'pegawai_id', 'total_jasa', 'deskripsi'], 'required'],
            [['jasadokter_id', 'pegawai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_transaksi', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['no_transaksi', 'additional_data'], 'string'],
            [['total_jasa'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_transaksi'], 'unique'],
            [
                ['tgl_transaksi'],
                'date', 'format' =>  'yyyy-MM-dd'
            ],
            [
                ['tgl_transaksi'], 
                'validateTglTransaksi'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pelayananjasadokter_id' => Yii::t('app', 'Pelayanan Jasa Dokter'),
            'jasadokter_id' => Yii::t('app', 'Transaksi'),
            'pegawai_id' => Yii::t('app', 'Dokter'),
            'tgl_transaksi' => Yii::t('app', 'Tanggal Transaksi'),
            'no_transaksi' => Yii::t('app', 'No Transaksi'),
            'total_jasa' => Yii::t('app', 'Total Jasa'),
            'deskripsi' => Yii::t('app', 'Deskripsi'),
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

    public function validateTglTransaksi($attribute, $params) 
    {
        $date = new \DateTime();
        $maxDate = date_format($date, 'Y-m-d');
        if ($this->$attribute > $maxDate) {
            $this->addError($attribute, 'Tanggal Transaksi tidak boleh lebih besar dari tanggal hari ini.');
        }
    }
}
