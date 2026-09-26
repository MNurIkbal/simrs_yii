<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "closingkasir_t".
 *
 * @property int $closingkasir_id
 * @property int $shift_id
 * @property int $pegawai_id
 * @property int $setorbank_id
 * @property string $tgl_closingkasir
 * @property string $closing_dari
 * @property string $sampai_dengan
 * @property double $closing_saldoawal
 * @property double $terima_uangmuka
 * @property double $terima_uangpelayanan
 * @property double $total_pengeluaran
 * @property double $nilai_closingtransaksi
 * @property double $total_setoran
 * @property double $jumlah_uanglogam
 * @property double $jumlah_uangkertas
 * @property int $jumlah_transaksi
 * @property double $piutang
 * @property string $keterangan_closing
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
 * @property int $ruangan_id
 */
class ClosingKasir extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'closingkasir_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shift_id', 'setorbank_id', 'jumlah_transaksi', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'ruangan_id'], 'default', 'value' => null],
            [['shift_id', 'pegawai_id', 'setorbank_id', 'jumlah_transaksi', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'ruangan_id'], 'integer'],
            [['pegawai_id', 'tgl_closingkasir', 'closing_dari', 'sampai_dengan', 'jumlah_transaksi'], 'required'],
            [[
                'no_closingkasir', 
                'tgl_closingkasir', 
                'closing_dari', 
                'sampai_dengan', 
                'created_date', 
                'last_modified_date', 
                'deleted_date',
                'pegawaimengetahui_id', 
                'pembayaran_nontunai'
            ], 'safe'],
            [['closing_saldoawal', 'terima_uangmuka', 'terima_uangpelayanan', 'total_pengeluaran', 'nilai_closingtransaksi', 'total_setoran', 'jumlah_uanglogam', 'jumlah_uangkertas', 'piutang'], 'number'],
            [['keterangan_closing', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            // [['pegawai_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pegawai::className(), 'targetAttribute' => ['pegawai_id' => 'pegawai_id']],
            // [['setorbank_id'], 'exist', 'skipOnError' => true, 'targetClass' => SetorBank::className(), 'targetAttribute' => ['setorbank_id' => 'setorbank_id']],
            // [['shift_id'], 'exist', 'skipOnError' => true, 'targetClass' => Shift::className(), 'targetAttribute' => ['shift_id' => 'shift_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'closingkasir_id' => Yii::t('app', 'Closingkasir ID'),
            'shift_id' => Yii::t('app', 'Shift ID'),
            'pegawai_id' => Yii::t('app', 'Pegawai ID'),
            'setorbank_id' => Yii::t('app', 'Setorbank ID'),
            'tgl_closingkasir' => Yii::t('app', 'Tgl Closingkasir'),
            'closing_dari' => Yii::t('app', 'Closing Dari'),
            'sampai_dengan' => Yii::t('app', 'Sampai Dengan'),
            'closing_saldoawal' => Yii::t('app', 'Closing Saldoawal'),
            'terima_uangmuka' => Yii::t('app', 'Terima Uangmuka'),
            'terima_uangpelayanan' => Yii::t('app', 'Terima Uangpelayanan'),
            'total_pengeluaran' => Yii::t('app', 'Total Pengeluaran'),
            'nilai_closingtransaksi' => Yii::t('app', 'Nilai Closingtransaksi'),
            'total_setoran' => Yii::t('app', 'Total Setoran'),
            'jumlah_uanglogam' => Yii::t('app', 'Jumlah Uanglogam'),
            'jumlah_uangkertas' => Yii::t('app', 'Jumlah Uangkertas'),
            'jumlah_transaksi' => Yii::t('app', 'Jumlah Transaksi'),
            'piutang' => Yii::t('app', 'Piutang'),
            'keterangan_closing' => Yii::t('app', 'Keterangan Closing'),
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
            'ruangan_id' => Yii::t('app', 'Ruangan ID'),
        ];
    }
    

    public function getView()
    {
        return $this->hasMany(ClosingKasirView::className(),['closingkasir_id' => 'closingkasir_id']);
    }

    public function getRuangan()
    {
        return $this->hasOne(Ruangan::className(),['ruangan_id' => 'ruangan_id']);
    }

    public function getPegawai()
    {
        return $this->hasOne(Pegawai::className(), ['pegawai_id' => 'pegawai_id']);
    }
    
    public function getShift()
    {
        return $this->hasOne(Shift::className(), ['shift_id' => 'shift_id']);
    }

    public function getRincianClosing()
    {
        return $this->hasMany(RincianClosing::className(), ['closingkasir_id' => 'closingkasir_id']);
    }

    public function getTandaBuktiBayar()
    {
        return $this->hasMany(TandaBuktiBayar::className(), ['closingkasir_id' => 'closingkasir_id']);
    }
    
    public function extraFields()
    {
        return [
            'pegawai' => function($item){
                return $item->pegawai;
            },
            'shift' => function($item){
                return $item->shift;
            }
        ];
    }
}
