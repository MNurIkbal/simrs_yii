<?php

namespace Integrasi\Service\Sirs\Models;

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
class PenjualanResep extends \Integrasi\Components\ActiveRepositories
{

    public $_repositori = 'app\components\repositories\PenjualanResepRepositories';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'penjualanresep_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pasienadmisi_id', 'pegawai_id', 'pendaftaran_id', 'returresep_id', 'kelaspelayanan_id', 'penjamin_id', 'pasien_id', 'carabayar_id', 'ruangan_id', 'reseptur_id', 'shift_id', 'lamapelayanan', 'penjpasienpegawai_id', 'penjpasienruangan_id', 'antrianfarmasi_id', 'permohonanoa_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pasienadmisi_id', 'pegawai_id', 'pendaftaran_id', 'returresep_id', 'kelaspelayanan_id', 'penjamin_id', 'pasien_id', 'carabayar_id', 'ruangan_id', 'reseptur_id', 'shift_id', 'lamapelayanan', 'penjpasienpegawai_id', 'penjpasienruangan_id', 'antrianfarmasi_id', 'permohonanoa_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tglpenjualan', 'tglresep', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['jenispenjualan', 'noresep', 'additional_data'], 'string'],
            [['totharganetto', 'totalhargajual', 'totaltarifservice', 'biayaadministrasi', 'biayakonseling', 'pembulatanharga', 'jasadokterresep', 'discount', 'subsidiasuransi', 'subsidipemerintah', 'subsidirs', 'iurbiaya', 'takaranresep'], 'number'],
            [['isresepperawatan', 'is_deleted', 'is_active'], 'boolean'],
            [['noresep'], 'unique'],
            // [['antrianfarmasi_id'], 'exist', 'skipOnError' => true, 'targetClass' => AntrianT::className(), 'targetAttribute' => ['antrianfarmasi_id' => 'antrian_id']],
            [['carabayar_id'], 'exist', 'skipOnError' => true, 'targetClass' => CaraBayar::className(), 'targetAttribute' => ['carabayar_id' => 'carabayar_id']],
            // [['kelaspelayanan_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelaspelayananM::className(), 'targetAttribute' => ['kelaspelayanan_id' => 'kelaspelayanan_id']],
            [['pasien_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pasien::className(), 'targetAttribute' => ['pasien_id' => 'pasien_id']],
            // [['pasienadmisi_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienadmisiT::className(), 'targetAttribute' => ['pasienadmisi_id' => 'pasienadmisi_id']],
            // [['pegawai_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['pegawai_id' => 'pegawai_id']],
            // [['penjpasienpegawai_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['penjpasienpegawai_id' => 'pegawai_id']],
            [['pendaftaran_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pendaftaran::className(), 'targetAttribute' => ['pendaftaran_id' => 'pendaftaran_id']],
            [['penjamin_id'], 'exist', 'skipOnError' => true, 'targetClass' => Penjamin::className(), 'targetAttribute' => ['penjamin_id' => 'penjamin_id']],
            // [['permohonanoa_id'], 'exist', 'skipOnError' => true, 'targetClass' => PermohonanoaT::className(), 'targetAttribute' => ['permohonanoa_id' => 'permohonanoa_id']],
            // [['reseptur_id'], 'exist', 'skipOnError' => true, 'targetClass' => ResepturT::className(), 'targetAttribute' => ['reseptur_id' => 'reseptur_id']],
            // [['returresep_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReturresepT::className(), 'targetAttribute' => ['returresep_id' => 'returresep_id']],
            // [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => RuanganM::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
            // [['penjpasienruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => RuanganM::className(), 'targetAttribute' => ['penjpasienruangan_id' => 'ruangan_id']],
            // [['shift_id'], 'exist', 'skipOnError' => true, 'targetClass' => ShiftM::className(), 'targetAttribute' => ['shift_id' => 'shift_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'penjualanresep_id' => Yii::t('app', 'Penjualan resep'),
            'pasienadmisi_id' => Yii::t('app', 'Pasien admisi'),
            'pegawai_id' => Yii::t('app', 'Pegawai'),
            'pendaftaran_id' => Yii::t('app', 'Pendaftaran'),
            'returresep_id' => Yii::t('app', 'Retur resep'),
            'kelaspelayanan_id' => Yii::t('app', 'Kelas pelayanan'),
            'penjamin_id' => Yii::t('app', 'Penjamin'),
            'pasien_id' => Yii::t('app', 'Pasien'),
            'carabayar_id' => Yii::t('app', 'Cara bayar'),
            'ruangan_id' => Yii::t('app', 'Ruangan'),
            'reseptur_id' => Yii::t('app', 'Reseptur'),
            'shift_id' => Yii::t('app', 'Shift'),
            'tglpenjualan' => Yii::t('app', 'Tanggal penjualan'),
            'jenispenjualan' => Yii::t('app', 'Jenis penjualan'),
            'tglresep' => Yii::t('app', 'Tanggal resep'),
            'noresep' => Yii::t('app', 'No resep'),
            'totharganetto' => Yii::t('app', 'Total harga netto'),
            'totalhargajual' => Yii::t('app', 'Total harga jual'),
            'totaltarifservice' => Yii::t('app', 'Total tarif service'),
            'biayaadministrasi' => Yii::t('app', 'Biaya administrasi'),
            'biayakonseling' => Yii::t('app', 'Biaya konseling'),
            'pembulatanharga' => Yii::t('app', 'Pembulatan harga'),
            'jasadokterresep' => Yii::t('app', 'Jasa dokter resep'),
            'discount' => Yii::t('app', 'Discount'),
            'subsidiasuransi' => Yii::t('app', 'Subsidi asuransi'),
            'subsidipemerintah' => Yii::t('app', 'Subsidi pemerintah'),
            'subsidirs' => Yii::t('app', 'Subsidi rs'),
            'iurbiaya' => Yii::t('app', 'Iur biaya'),
            'lamapelayanan' => Yii::t('app', 'Lama pelayanan'),
            'penjpasienpegawai_id' => Yii::t('app', 'Penjpasienpegawai'),
            'penjpasienruangan_id' => Yii::t('app', 'Penjpasienruangan'),
            'antrianfarmasi_id' => Yii::t('app', 'Antrian farmasi'),
            'permohonanoa_id' => Yii::t('app', 'Permohonanoa'),
            'takaranresep' => Yii::t('app', 'Takaran resep'),
            'isresepperawatan' => Yii::t('app', 'Is resep perawatan'),
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
    
    public function getCaraBayar()
    {
        return $this->hasOne(CaraBayar::className(), ['carabayar_id' => 'carabayar_id']);
    }
    
    public function getPenjamin()
    {
        return $this->hasOne(Penjamin::className(), ['penjamin_id' => 'penjamin_id']);
    }
    
    public function getPasien()
    {
        return $this->hasOne(Pasien::className(), ['pasien_id' => 'pasien_id']);
    }
    
    public function getPendaftaran()
    {
        return $this->hasOne(Pendaftaran::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }
    
    public function extraFields()
    {
        return [
            'carabayar_m' => function($item){
                return $item->caraBayar;
            },
            'penjamin_m' => function($item){
                return $item->penjamin;
            },
            'pasien_m' => function($item){
                return $item->pasien;
            },
            'pendaftaran_t' => function($item){
                return $item->pendaftaran;
            }
        ];
    }
}
