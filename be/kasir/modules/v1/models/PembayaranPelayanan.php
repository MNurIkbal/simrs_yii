<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pembayaranpelayanan_t".
 *
 * @property int $pembayaranpelayanan_id
 * @property int $carabayar_id
 * @property int $ruangan_id
 * @property int $penjamin_id
 * @property int $pembebasantarif_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $suratketjaminan_id
 * @property int $tandabuktibayar_id
 * @property int $pasien_id
 * @property int $pembklaimdetail_id
 * @property int $ruangan_pelakhir_id
 * @property string $no_pembayaran
 * @property string $tgl_pembayaran
 * @property string $no_resep
 * @property string $no_sjp
 * @property double $total_biayaoa
 * @property double $total_biayatindakan
 * @property double $total_biayapelayanan
 * @property double $total_subsidiasuransi
 * @property double $total_subsidipemerintah
 * @property double $total_subsidirs
 * @property double $total_iurbiaya
 * @property double $total_bayartindakan
 * @property double $total_discount
 * @property double $total_pembebasan
 * @property double $total_sisatagihan
 * @property string $statusbayar
 * @property string $tgljatuhtempo
 * @property double $nilaippnoa
 * @property double $administrasioa
 * @property double $embalance
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

 * @property BayarangsuranpelayananT[] $bayarangsuranpelayananTs
 * @property PasienadmisiT[] $pasienadmisiTs
 * @property PemakaianuangmukaT[] $pemakaianuangmukaTs
 * @property CarabayarM $carabayar
 * @property PasienM $pasien
 * @property PasienadmisiT $pasienadmisi
 * @property PembebasantarifT $pembebasantarif
 * @property PembklaimdetailT $pembklaimdetail
 * @property PendaftaranT $pendaftaran
 * @property PenjaminM $penjamin
 * @property RuanganM $ruangan
 * @property RuanganM $ruanganPelakhir
 * @property SuratketjaminanT $suratketjaminan
 * @property TandabuktibayarT $tandabuktibayar
 * @property PembklaimdetailT[] $pembklaimdetailTs
 * @property PendaftaranT[] $pendaftaranTs
 * @property SuratketjaminanT[] $suratketjaminanTs
 * @property TandabuktibayarT[] $tandabuktibayarTs
 * @property TindakansudahbayarT[] $tindakansudahbayarTs
 */
class PembayaranPelayanan extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pembayaranpelayanan_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['carabayar_id', 'ruangan_id', 'penjamin_id', 'ruangan_pelakhir_id','tgl_pembayaran', 'total_biayatindakan', 'total_biayapelayanan', 'total_subsidiasuransi', 'total_subsidipemerintah', 'total_subsidirs', 'total_iurbiaya', 'total_bayartindakan', 'total_sisatagihan', 'statusbayar'], 'required'],
            [['carabayar_id', 'ruangan_id', 'penjamin_id', 'pembebasantarif_id', 'pendaftaran_id', 'pasienadmisi_id', 'suratketjaminan_id', 'tandabuktibayar_id', 'pasien_id', 'pembklaimdetail_id', 'ruangan_pelakhir_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'penjualanresep_id'], 'default', 'value' => null],
            [['carabayar_id', 'ruangan_id', 'penjamin_id', 'pembebasantarif_id', 'pendaftaran_id', 'pasienadmisi_id', 'suratketjaminan_id', 'tandabuktibayar_id', 'pasien_id', 'pembklaimdetail_id', 'ruangan_pelakhir_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [[
                'tgl_pembayaran',
                'tgljatuhtempo',
                'created_date',
                'last_modified_date',
                'deleted_date',
                'biaya_administrasi',
                'e_collection',
                'no_rekening',
                'nama_pemrekening',
                'penggunaan_uangmuka',
                'total_terbayar',
                'pembulatan',
                'penjualanresep_id',
            ], 'safe'],
            [['total_biayaoa', 'total_biayatindakan', 'total_biayapelayanan', 'total_subsidiasuransi', 'total_subsidipemerintah', 'total_subsidirs', 'total_iurbiaya', 'total_bayartindakan', 'total_discount', 'total_pembebasan', 'total_sisatagihan'], 'number'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            // [['no_pembayaran', 'no_resep', 'no_sjp'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pembayaranpelayanan_id' => Yii::t('app', 'Pembayaran pelayanan'),
            'carabayar_id' => Yii::t('app', 'Cara bayar'),
            'ruangan_id' => Yii::t('app', 'Ruangan'),
            'penjamin_id' => Yii::t('app', 'Penjamin'),
            'pembebasantarif_id' => Yii::t('app', 'Pembebasan tarif'),
            'pendaftaran_id' => Yii::t('app', 'Pendaftaran'),
            'pasienadmisi_id' => Yii::t('app', 'Pasienadmisi'),
            'suratketjaminan_id' => Yii::t('app', 'Surat ketjaminan'),
            'tandabuktibayar_id' => Yii::t('app', 'Tanda bukti bayar'),
            'pasien_id' => Yii::t('app', 'Pasien'),
            'pembklaimdetail_id' => Yii::t('app', 'Pemb klaim detail'),
            'ruangan_pelakhir_id' => Yii::t('app', 'Ruangan pel akhir'),
            'no_pembayaran' => Yii::t('app', 'No pembayaran'),
            'tgl_pembayaran' => Yii::t('app', 'Tanggal pembayaran'),
            'no_resep' => Yii::t('app', 'No resep'),
            'no_sjp' => Yii::t('app', 'No sjp'),
            'total_biayaoa' => Yii::t('app', 'Total biaya oa'),
            'total_biayatindakan' => Yii::t('app', 'Total biaya tindakan'),
            'total_biayapelayanan' => Yii::t('app', 'Total biaya pelayanan'),
            'total_subsidiasuransi' => Yii::t('app', 'Total subsidi asuransi'),
            'total_subsidipemerintah' => Yii::t('app', 'Total subsidi pemerintah'),
            'total_subsidirs' => Yii::t('app', 'Total subsidi rs'),
            'total_iurbiaya' => Yii::t('app', 'Total iur biaya'),
            'total_bayartindakan' => Yii::t('app', 'Total bayar tindakan'),
            'total_discount' => Yii::t('app', 'Total discount'),
            'total_pembebasan' => Yii::t('app', 'Total pembebasan'),
            'total_sisatagihan' => Yii::t('app', 'Total sisa tagihan'),
            'statusbayar' => Yii::t('app', 'Status bayar'),
            'tgljatuhtempo' => Yii::t('app', 'Tanggal jatuh tempo'),
            'nilaippnoa' => Yii::t('app', 'Nilai ppn oa'),
            'administrasioa' => Yii::t('app', 'Administrasi oa'),
            'embalance' => Yii::t('app', 'Embalance'),
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasien()
    {
        return $this->hasOne(Pasien::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftaran()
    {
        return $this->hasOne(Pendaftaran::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    public function getTandaBuktiBayar()
    {
        return $this->hasOne(TandaBuktiBayar::className(), ['tandabuktibayar_id' => 'tandabuktibayar_id']);
    }

    public function extraFields()
    {
        return [
            'pasien_m' => function($item){
                return $item->pasien;
            },
            'pendaftaran_t' => function($item){
                return $item->pendaftaran;
            },
            'tandabuktibayar_t' => function($item){
                return $item->tandaBuktiBayar;
            }
        ];
    }
}
