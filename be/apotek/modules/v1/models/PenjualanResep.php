<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "penjualanresep_t".
 *
 * @property integer $penjualanresep_id
 * @property integer $pasienadmisi_id
 * @property integer $pegawai_id
 * @property integer $pendaftaran_id
 * @property integer $returresep_id
 * @property integer $kelaspelayanan_id
 * @property integer $penjamin_id
 * @property integer $pasien_id
 * @property integer $carabayar_id
 * @property integer $ruangan_id
 * @property integer $reseptur_id
 * @property integer $shift_id
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
 * @property integer $lamapelayanan
 * @property integer $penjpasienpegawai_id
 * @property integer $penjpasienruangan_id
 * @property integer $antrianfarmasi_id
 * @property integer $permohonanoa_id
 * @property double $takaranresep
 * @property boolean $isresepperawatan
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
 *
 * @property AntrianT $antrianfarmasi
 * @property CarabayarM $carabayar
 * @property KelaspelayananM $kelaspelayanan
 * @property PasienM $pasien
 * @property PasienadmisiT $pasienadmisi
 * @property PegawaiM $pegawai
 * @property PegawaiM $penjpasienpegawai
 * @property PendaftaranT $pendaftaran
 * @property PenjaminM $penjamin
 * @property PermohonanoaT $permohonanoa
 * @property ResepturT $reseptur
 * @property ReturresepT $returresep
 * @property RuanganM $ruangan
 * @property RuanganM $penjpasienruangan
 * @property ShiftM $shift
 * @property ReturresepT[] $returresepTs
 */
class PenjualanResep extends \Doco\components\DocoActiveRecord
{
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
            // [['penjualanresep_id'], 'required'],
            [['karyawan_id', 'pasienadmisi_id', 'pegawai_id', 'pendaftaran_id', 'returresep_id', 'kelaspelayanan_id', 'penjamin_id', 'pasien_id', 'carabayar_id', 'ruangan_id', 'reseptur_id', 'shift_id', 'lamapelayanan', 'penjpasienpegawai_id', 'penjpasienruangan_id', 'antrianfarmasi_id', 'permohonanoa_id', 'pegawai_menyerahkan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by',
                'resep_kronis_asal_id',
                'reseptur_kronis_asal_id',
                'hasil_resep_kronis_id',
            ], 'integer'],
            [['status_reseptur', 'tgl_approve', 'pegawai_approve_id', 'nama_pembeli', 'jenispenjualan', 'tglpenjualan', 'tglresep', 'iter','created_date','catatan','pembatalanresep_id', 'kelaspelayanan_id', 'tgl_menyerahkan', 
                'reseptur_kronis_asal_id',
                'SEP',
            ], 'safe'],
            [['nama_pembeli', 'jenispenjualan', 'noresep', 'additional_data'], 'string'],
            [['totharganetto', 'totalhargajual', 'totaltarifservice', 'biayaadministrasi', 'biayakonseling', 'pembulatanharga', 'jasadokterresep', 'discount', 'subsidiasuransi', 'subsidipemerintah', 'subsidirs', 'iurbiaya', 'takaranresep'], 'number'],
            // [['isresepperawatan', 'is_deleted', 'is_active'], 'boolean'],
            // [['antrianfarmasi_id'], 'exist', 'skipOnError' => true, 'targetClass' => AntrianT::className(), 'targetAttribute' => ['antrianfarmasi_id' => 'antrian_id']],
            // [['carabayar_id'], 'exist', 'skipOnError' => true, 'targetClass' => CarabayarM::className(), 'targetAttribute' => ['carabayar_id' => 'carabayar_id']],
            // [['kelaspelayanan_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelaspelayananM::className(), 'targetAttribute' => ['kelaspelayanan_id' => 'kelaspelayanan_id']],
            // [['pasien_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienM::className(), 'targetAttribute' => ['pasien_id' => 'pasien_id']],
            // [['pasienadmisi_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienadmisiT::className(), 'targetAttribute' => ['pasienadmisi_id' => 'pasienadmisi_id']],
            // [['pegawai_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['pegawai_id' => 'pegawai_id']],
            // [['penjpasienpegawai_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['penjpasienpegawai_id' => 'pegawai_id']],
            // [['pendaftaran_id'], 'exist', 'skipOnError' => true, 'targetClass' => PendaftaranT::className(), 'targetAttribute' => ['pendaftaran_id' => 'pendaftaran_id']],
            // [['penjamin_id'], 'exist', 'skipOnError' => true, 'targetClass' => PenjaminM::className(), 'targetAttribute' => ['penjamin_id' => 'penjamin_id']],
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
            'penjualanresep_id' => 'Penjualanresep ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pegawai_id' => 'Pegawai ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'returresep_id' => 'Returresep ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'penjamin_id' => 'Penjamin ID',
            'pasien_id' => 'Pasien ID',
            'carabayar_id' => 'Carabayar ID',
            'ruangan_id' => 'Ruangan ID',
            'reseptur_id' => 'Reseptur ID',
            'shift_id' => 'Shift ID',
            'tglpenjualan' => 'Tglpenjualan',
            'jenispenjualan' => 'Jenispenjualan',
            'tglresep' => 'Tglresep',
            'noresep' => 'Noresep',
            'totharganetto' => 'Totharganetto',
            'totalhargajual' => 'Totalhargajual',
            'totaltarifservice' => 'Totaltarifservice',
            'biayaadministrasi' => 'Biayaadministrasi',
            'biayakonseling' => 'Biayakonseling',
            'pembulatanharga' => 'Pembulatanharga',
            'jasadokterresep' => 'Jasadokterresep',
            'discount' => 'Discount',
            'subsidiasuransi' => 'Subsidiasuransi',
            'subsidipemerintah' => 'Subsidipemerintah',
            'subsidirs' => 'Subsidirs',
            'iurbiaya' => 'Iurbiaya',
            'lamapelayanan' => 'Lamapelayanan',
            'penjpasienpegawai_id' => 'Penjpasienpegawai ID',
            'penjpasienruangan_id' => 'Penjpasienruangan ID',
            'antrianfarmasi_id' => 'Antrianfarmasi ID',
            'permohonanoa_id' => 'Permohonanoa ID',
            'takaranresep' => 'Takaranresep',
            'isresepperawatan' => 'Isresepperawatan',
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
            'resep_kronis_asal_id' => 'Resep Kronis Asal',
            'reseptur_kronis_asal_id' => 'Reseptur Kronis Asal',
            'hasil_resep_kronis_id' => 'Hasil Generate Resep Kronis',
            'SEP' => 'SEP',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAntrianfarmasi()
    {
        return $this->hasOne(Antrian::className(), ['antrian_id' => 'antrianfarmasi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCarabayar()
    {
        return $this->hasOne(Carabayar::className(), ['carabayar_id' => 'carabayar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKelaspelayanan()
    {
        return $this->hasOne(Kelaspelayanan::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
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
    public function getPasienadmisi()
    {
        return $this->hasOne(Pasienadmisi::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPegawai()
    {
        return $this->hasOne(Pegawai::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenjpasienpegawai()
    {
        return $this->hasOne(Pegawai::className(), ['pegawai_id' => 'penjpasienpegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftaran()
    {
        return $this->hasOne(Pendaftaran::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenjamin()
    {
        return $this->hasOne(Penjamin::className(), ['penjamin_id' => 'penjamin_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermohonanoa()
    {
        return $this->hasOne(Permohonanoa::className(), ['permohonanoa_id' => 'permohonanoa_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReseptur()
    {
        return $this->hasOne(Reseptur::className(), ['reseptur_id' => 'reseptur_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReturresep()
    {
        return $this->hasOne(Returresep::className(), ['returresep_id' => 'returresep_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuangan()
    {
        return $this->hasOne(Ruangan::className(), ['ruangan_id' => 'ruangan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenjpasienruangan()
    {
        return $this->hasOne(Ruangan::className(), ['ruangan_id' => 'penjpasienruangan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShift()
    {
        return $this->hasOne(Shift::className(), ['shift_id' => 'shift_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReturresepTs()
    {
        return $this->hasMany(Returresep::className(), ['penjualanresep_id' => 'penjualanresep_id']);
    }

    public function extraFields()
    {
        return [
            'instalasi' => function($item) {
                return $item->ruangan->instalasi;
            }
        ];
    }

    public function getDataPenjualanById($id, $type)
    {
        $connection = Yii::$app->db;
        $sql = " SELECT
                penjualanresep_id,
                noresep,
                tglresep,
                nama_pasien,
                pasien_id,
                nama_pembeli,
                nama_pegawai,
                nama_karyawan,
                carabayar_nama,
                penjamin_nama,
                jenispenjualan,
                catatan,
                biayaadministrasi,
                totaltarifservice,
                pembulatanharga,
                totalhargajual,
                iter,
                instalasi_nama,
                ruangan_nama
            FROM
                infopenjualanresep_v
            WHERE
                penjualanresep_id = :id
            AND jenispenjualan = :jenis
        ";
        $command = $connection->createCommand($sql);
        $command->bindValue(':id', $id);
        $command->bindValue(':jenis', $type);
        $data = $command->queryOne();

        return ($data) ? $data : [];
    }

    public function getDetailPenjualanById($id, $type)
    {
        $connection = Yii::$app->db;
        $sql = " SELECT
                penjualanresep_id,
                resepturdetail_id,
                reseptur_id,
                racikan_id,
                rke,
                obatalkes_nama,
                signa_oa,
                qty_oa,
                hargajual_oa,
                hargasatuan_oa,
                additional_data::json ->> 'posisi' as posisi,
                tglpelayanan,
                satuan_input,
                hargasatuan_reseptur,
                hargajual_reseptur,
                qty_reseptur,
                etiket_reseptur
            FROM
                infopenjualanresepdetail_v
            WHERE
                penjualanresep_id = :id
            AND jenispenjualan = :jenis
            ORDER BY posisi ASC, tglpelayanan ASC
        ";
        $command = $connection->createCommand($sql);
        $command->bindValue(':id', $id);
        $command->bindValue(':jenis', $type);
        $data = $command->queryAll();

        return ($data) ? $data : [];
    }

    public function getResepData($column, $value)
    {
        $column = $column == 'reseptur_id' ? 'rt.reseptur_id' : 'pt.penjualanresep_id';

        $sql =  'select 
                \'resep\'::text AS jenis, pt.*, pt.status_reseptur as status_reseptur_id,
                pt.noresep as no_resep, pt.noresep as no_reseptur, rm.ruangan_nama as ruangan_resep,
                im.instalasi_nama as instalasi_resep, pm.nama_pegawai,
                cm.carabayar_nama, km.kelaspelayanan_nama, pm1.penjamin_nama
            from penjualanresep_t pt
            left join pendaftaran_t pt2 on pt2.pendaftaran_id = pt.pendaftaran_id
            left join ruangan_m rm on rm.ruangan_id = pt.ruangan_id
            left join instalasi_m im on im.instalasi_id = rm.instalasi_id
            left join pegawai_m pm on pm.pegawai_id = pt.pegawai_id
            left join carabayar_m cm on cm.carabayar_id = pt.carabayar_id
            left join kelaspelayanan_m km on km.kelaspelayanan_id = pt.kelaspelayanan_id
            left join penjamin_m pm1 on pm1.penjamin_id = pt.penjamin_id';

        $where = " where $column = $value";

        $query = Yii::$app->db->createCommand($sql.$where);

        return $query->queryOne();
    }
}
