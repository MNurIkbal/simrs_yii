<?php

namespace app\modules\v1\models;

use Yii;
use \yii\helpers\ArrayHelper;
use SirsCore\models\LogActivityR;
use Doco\components\DocoConstants;

/**
 * This is the model class for table "pasienadmisi_t".
 *
 * @property int $pasienadmisi_id
 * @property int $shift_id
 * @property int $carabayar_id
 * @property int $penjamin_id
 * @property int $pasien_id
 * @property int $caramasuk_id
 * @property int $ruangan_id
 * @property int $pasienpulang_id
 * @property int $bookingkamar_id
 * @property int $pembayaranpelayanan_id
 * @property int $pendaftaran_id
 * @property int $kamarruangan_id
 * @property int $kelaspelayanan_id
 * @property int $pegawai_id
 * @property string $tgl_admisi
 * @property string $tgl_pendaftaran
 * @property string $tgl_pulang
 * @property string $kunjungan
 * @property bool $status_keluar
 * @property bool $rawat_gabung
 * @property string $rencana_pulang
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
 * @property int $kamartempattidur_id
 *
 * @property AnamnesaT[] $anamnesaTs
 * @property AnamnesadietT[] $anamnesadietTs
 * @property AsuhankeperawatanT[] $asuhankeperawatanTs
 * @property BayaruangmukaT[] $bayaruangmukaTs
 * @property BookingkamarT[] $bookingkamarTs
 * @property DietpasienT[] $dietpasienTs
 * @property HasilpemeriksaanlabT[] $hasilpemeriksaanlabTs
 * @property HasilpemeriksaanradT[] $hasilpemeriksaanradTs
 * @property HasilpemeriksaanrmT[] $hasilpemeriksaanrmTs
 * @property MasukkamarT[] $masukkamarTs
 * @property BookingkamarT $bookingkamar
 * @property CarabayarM $carabayar
 * @property CaramasukM $caramasuk
 * @property KamarruanganM $kamarruangan
 * @property KelaspelayananM $kelaspelayanan
 * @property PasienM $pasien
 * @property PasienpulangT $pasienpulang
 * @property PegawaiM $pegawai
 * @property PembayaranpelayananT $pembayaranpelayanan
 * @property PendaftaranT $pendaftaran
 * @property PenjaminM $penjamin
 * @property RuanganM $ruangan
 * @property PasienmasukpenunjangT[] $pasienmasukpenunjangTs
 * @property PasienpulangT[] $pasienpulangTs
 * @property PembayaranpelayananT[] $pembayaranpelayananTs
 * @property PendaftaranT[] $pendaftaranTs
 * @property PenjualanresepT[] $penjualanresepTs
 * @property PindahkamarT[] $pindahkamarTs
 * @property RencanaoperasiT[] $rencanaoperasiTs
 * @property ReturresepT[] $returresepTs
 * @property TindakanpelayananT[] $tindakanpelayananTs
 */
class PasienAdmisi extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */

    public $jeniskasuspenyakit_id;
    public $keterangan;
    public static function tableName()
    {
        return 'pasienadmisi_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shift_id', 'carabayar_id', 'penjamin_id', 'pasien_id', 'ruangan_id', 'pasienpulang_id', 'bookingkamar_id', 'pembayaranpelayanan_id', 'pendaftaran_id', 'kamarruangan_id', 'kelaspelayanan_id', 'pegawai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'kamartempattidur_id'], 'default', 'value' => null],
            [['shift_id', 'carabayar_id', 'penjamin_id', 'pasien_id', 'caramasuk_id', 'ruangan_id', 'pasienpulang_id', 'bookingkamar_id', 'pembayaranpelayanan_id', 'pendaftaran_id', 'kamarruangan_id', 'kelaspelayanan_id', 'pegawai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'kamartempattidur_id'], 'integer'],
            // [['carabayar_id', 'penjamin_id', 'pasien_id', 'ruangan_id', 'pendaftaran_id'], 'required'],
            [[
                'caramasuk_id', 
                'tgl_admisi', 
                'tgl_pendaftaran', 
                'tgl_pulang', 
                'rencana_pulang', 
                'created_date', 
                'last_modified_date', 
                'deleted_date',
                'bpjs_id', 
                'keterangan', 
                'is_pasientitipan', 
                'is_aps',
                'limit_tagihan'
            ], 'safe'],
            [['status_keluar', 'rawat_gabung', 'is_deleted', 'is_active', 'is_pasientitipan', 'is_stoptitipan'], 'boolean'],
            [['additional_data'], 'string'],
            [['kunjungan'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'shift_id' => 'Shift ID',
            'carabayar_id' => 'Carabayar ID',
            'penjamin_id' => 'Penjamin ID',
            'pasien_id' => 'Pasien ID',
            'caramasuk_id' => 'Caramasuk ID',
            'ruangan_id' => 'Ruangan ID',
            'pasienpulang_id' => 'Pasienpulang ID',
            'bookingkamar_id' => 'Bookingkamar ID',
            'pembayaranpelayanan_id' => 'Pembayaranpelayanan ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'kamarruangan_id' => 'Kamarruangan ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'pegawai_id' => 'Pegawai ID',
            'tgl_admisi' => 'Tgl Admisi',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'tgl_pulang' => 'Tgl Pulang',
            'kunjungan' => 'Kunjungan',
            'status_keluar' => 'Status Keluar',
            'rawat_gabung' => 'Rawat Gabung',
            'rencana_pulang' => 'Rencana Pulang',
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
            'kamartempattidur_id' => 'Kamartempattidur ID',
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        if(!$insert) {
            $this->attachBehavior('typecast',\yii\behaviors\AttributeTypecastBehavior::class);
            $this->typecastAttributes();

            $before = [];
            $after = [];

            $fieldsToTrack = [
                'carabayar_id',
                'pegawai_id',
                'penjamin_id',
                'kelaspelayanan_id',
                'kamarruangan_id',
                'kamartempattidur_id',
                'ruangan_id',
                'tgl_pendaftaran',
            ];

            foreach ($changedAttributes as $fieldName => $valueBefore) {
                if(in_array($fieldName, $fieldsToTrack)) {
                    $valueAfter = ArrayHelper::getValue($this->attributes,$fieldName);
                    if($valueBefore !== $valueAfter) {
                        if ($fieldName == 'carabayar_id') {
                            $before['admisi_carabayar_nama'] = CaraBayar::findOne($valueBefore)->carabayar_nama;
                            $after['admisi_carabayar_nama'] = CaraBayar::findOne($valueAfter)->carabayar_nama;
                        }elseif ($fieldName == 'pegawai_id') {
                            $before['admisi_nama_pegawai'] = Pegawai::findOne($valueBefore)->nama_pegawai;
                            $after['admisi_nama_pegawai'] = Pegawai::findOne($valueAfter)->nama_pegawai;
                        }elseif ($fieldName == 'penjamin_id') {
                            $before['admisi_penjamin_nama'] = Penjamin::findOne($valueBefore)->penjamin_nama;
                            $after['admisi_penjamin_nama'] = Penjamin::findOne($valueAfter)->penjamin_nama;
                        }elseif ($fieldName == 'kelaspelayanan_id') {
                            $before['admisi_kelaspelayanan_nama'] = KelasPelayanan::findOne($valueBefore)->kelaspelayanan_nama;
                            $after['admisi_kelaspelayanan_nama'] = KelasPelayanan::findOne($valueAfter)->kelaspelayanan_nama;
                        }elseif ($fieldName == 'kamarruangan_id') {
                            $before['admisi_kamarruangan_nokamar'] = KamarRuangan::findOne($valueBefore)->kamarruangan_nokamar;
                            $after['admisi_kamarruangan_nokamar'] = KamarRuangan::findOne($valueAfter)->kamarruangan_nokamar;
                        }elseif ($fieldName == 'kamartempattidur_id') {
                            $before['admisi_no_tempattidur'] = KamarTempatTidur::findOne($valueBefore)->no_tempattidur;
                            $after['admisi_no_tempattidur'] = KamarTempatTidur::findOne($valueAfter)->no_tempattidur;
                        }elseif ($fieldName == 'ruangan_id') {
                            $before['admisi_ruangan_nama'] = Ruangan::findOne($valueBefore)->ruangan_nama;
                            $after['admisi_ruangan_nama'] = Ruangan::findOne($valueAfter)->ruangan_nama;
                        }else{
                            $before[$fieldName] = $valueBefore;
                            $after[$fieldName] = $valueAfter;
                        }
                    }
                }
            }
            
            if(count($before) > 0 || count($after) >0) {
                $changedSummary = ['before' => $before, 'after' => $after];

                $model = new LogActivityR();
                $model->attributes = [
                    'tgl' => date('Y-m-d H:i:s'),
                    'aksi' => DocoConstants::LA_AKSI_EDIT,
                    'tipe' => 'PENDAFTARAN_RANAP',
                    'transaksi_id' => $this->pendaftaran_id,
                    // 'alasan' => $this->keterangan_pendaftaran,
                    'additional_detail' => $changedSummary,
                ];
                $model->save(false);
            }

        }
        return parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnamnesaTs()
    {
        return $this->hasMany(AnamnesaT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnamnesadietTs()
    {
        return $this->hasMany(AnamnesadietT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsuhankeperawatanTs()
    {
        return $this->hasMany(AsuhankeperawatanT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBayaruangmukaTs()
    {
        return $this->hasMany(BayaruangmukaT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBookingkamarTs()
    {
        return $this->hasMany(BookingkamarT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDietpasienTs()
    {
        return $this->hasMany(DietpasienT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHasilpemeriksaanlabTs()
    {
        return $this->hasMany(HasilpemeriksaanlabT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHasilpemeriksaanradTs()
    {
        return $this->hasMany(HasilpemeriksaanradT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHasilpemeriksaanrmTs()
    {
        return $this->hasMany(HasilpemeriksaanrmT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMasukkamarTs()
    {
        return $this->hasMany(MasukkamarT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBookingkamar()
    {
        return $this->hasOne(BookingkamarT::className(), ['bookingkamar_id' => 'bookingkamar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCarabayar()
    {
        return $this->hasOne(CarabayarM::className(), ['carabayar_id' => 'carabayar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaramasuk()
    {
        return $this->hasOne(CaramasukM::className(), ['caramasuk_id' => 'caramasuk_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKamarruangan()
    {
        return $this->hasOne(KamarruanganM::className(), ['kamarruangan_id' => 'kamarruangan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKelaspelayanan()
    {
        return $this->hasOne(KelaspelayananM::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasien()
    {
        return $this->hasOne(PasienM::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienpulang()
    {
        return $this->hasOne(PasienpulangT::className(), ['pasienpulang_id' => 'pasienpulang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPegawai()
    {
        return $this->hasOne(PegawaiM::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPembayaranpelayanan()
    {
        return $this->hasOne(PembayaranpelayananT::className(), ['pembayaranpelayanan_id' => 'pembayaranpelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftaran()
    {
        return $this->hasOne(PendaftaranT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenjamin()
    {
        return $this->hasOne(PenjaminM::className(), ['penjamin_id' => 'penjamin_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuangan()
    {
        return $this->hasOne(RuanganM::className(), ['ruangan_id' => 'ruangan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienmasukpenunjangTs()
    {
        return $this->hasMany(PasienmasukpenunjangT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienpulangTs()
    {
        return $this->hasMany(PasienpulangT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPembayaranpelayananTs()
    {
        return $this->hasMany(PembayaranpelayananT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftaranTs()
    {
        return $this->hasMany(PendaftaranT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenjualanresepTs()
    {
        return $this->hasMany(PenjualanresepT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPindahkamarTs()
    {
        return $this->hasMany(PindahkamarT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRencanaoperasiTs()
    {
        return $this->hasMany(RencanaoperasiT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReturresepTs()
    {
        return $this->hasMany(ReturresepT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTindakanpelayananTs()
    {
        return $this->hasMany(TindakanpelayananT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }
}
