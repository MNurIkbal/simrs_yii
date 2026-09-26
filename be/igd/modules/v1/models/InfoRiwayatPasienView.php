<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "inforiwayatpasien_v".
 *
 * @property int $pendaftaran_id
 * @property string $no_rekam_medik
 * @property string $no_pendaftaran
 * @property string $tgl_pendaftaran
 * @property int $r_anamesa
 * @property int $r_pemeriksaanfisik
 * @property int $r_diagnosa
 * @property int $r_konsulpoli
 * @property int $r_tindakan
 * @property int $r_bmhp
 * @property int $r_reseptur
 * @property int $r_resumemedis_rj_rd
 * @property int $p_laboratorium
 * @property int $p_radiologi
 * @property int $p_operasi
 * @property int $r_asesmenawal
 * @property int $r_rekonsiliasiobat
 * @property int $r_asesmenmedis
 * @property int $r_dischargeplan
 * @property int $r_cppt
 * @property int $r_instruktitindakan
 * @property int $r_instruktitindakanbmhp
 * @property int $r_pemberianobat
 * @property int $r_permintaankonsul
 * @property int $r_pindahkamar
 * @property int $r_resumemedis_ri
 * @property int $r_visitedokter
 * @property string $tglpasienpulang
 * @property int $carakeluar_id
 * @property string $carakeluar_nama
 * @property string $status_rj
 * @property string $status_rd_ri
 */
class InfoRiwayatPasienView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inforiwayatpasien_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'r_anamesa', 'r_pemeriksaanfisik', 'r_diagnosa', 'r_konsulpoli', 'r_tindakan', 'r_bmhp', 'r_reseptur', 'r_resumemedis_rj_rd', 'p_laboratorium', 'p_radiologi', 'p_operasi', 'r_asesmenawal', 'r_rekonsiliasiobat', 'r_asesmenmedis', 'r_dischargeplan', 'r_cppt', 'r_instruktitindakan', 'r_instruktitindakanbmhp', 'r_pemberianobat', 'r_permintaankonsul', 'r_pindahkamar', 'r_resumemedis_ri', 'r_visitedokter', 'carakeluar_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'r_anamesa', 'r_pemeriksaanfisik', 'r_diagnosa', 'r_konsulpoli', 'r_tindakan', 'r_bmhp', 'r_reseptur', 'r_resumemedis_rj_rd', 'p_laboratorium', 'p_radiologi', 'p_operasi', 'r_asesmenawal', 'r_rekonsiliasiobat', 'r_asesmenmedis', 'r_dischargeplan', 'r_cppt', 'r_instruktitindakan', 'r_instruktitindakanbmhp', 'r_pemberianobat', 'r_permintaankonsul', 'r_pindahkamar', 'r_resumemedis_ri', 'r_visitedokter', 'carakeluar_id'], 'integer'],
            [['tgl_pendaftaran', 'tglpasienpulang'], 'safe'],
            [['status_rj', 'status_rd_ri'], 'string'],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['carakeluar_nama'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'no_rekam_medik' => 'No Rekam Medik',
            'no_pendaftaran' => 'No Pendaftaran',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'r_anamesa' => 'R Anamesa',
            'r_pemeriksaanfisik' => 'R Pemeriksaanfisik',
            'r_diagnosa' => 'R Diagnosa',
            'r_konsulpoli' => 'R Konsulpoli',
            'r_tindakan' => 'R Tindakan',
            'r_bmhp' => 'R Bmhp',
            'r_reseptur' => 'R Reseptur',
            'r_resumemedis_rj_rd' => 'R Resumemedis Rj Rd',
            'p_laboratorium' => 'P Laboratorium',
            'p_radiologi' => 'P Radiologi',
            'p_operasi' => 'P Operasi',
            'r_asesmenawal' => 'R Asesmenawal',
            'r_rekonsiliasiobat' => 'R Rekonsiliasiobat',
            'r_asesmenmedis' => 'R Asesmenmedis',
            'r_dischargeplan' => 'R Dischargeplan',
            'r_cppt' => 'R Cppt',
            'r_instruktitindakan' => 'R Instruktitindakan',
            'r_instruktitindakanbmhp' => 'R Instruktitindakanbmhp',
            'r_pemberianobat' => 'R Pemberianobat',
            'r_permintaankonsul' => 'R Permintaankonsul',
            'r_pindahkamar' => 'R Pindahkamar',
            'r_resumemedis_ri' => 'R Resumemedis Ri',
            'r_visitedokter' => 'R Visitedokter',
            'tglpasienpulang' => 'Tglpasienpulang',
            'carakeluar_id' => 'Carakeluar ID',
            'carakeluar_nama' => 'Carakeluar Nama',
            'status_rj' => 'Status Rj',
            'status_rd_ri' => 'Status Rd Ri',
            'persalinan_id' => 'Persalinan ID',
            'kelahiranbayi_id' => 'Kelahiranbayi ID',
            'anamesa_id' => 'Anamesa ID',
            'pemeriksaanfisik_id' => 'Pemeriksaanfisik ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'ruangan_penunjang' => 'Ruangan Penunjang',
            'hasilpemeriksaanlab_id' => 'Hasilpemeriksaanlab ID',
            'hasilpemeriksaanrad_id' => 'Hasilpemeriksaanrad ID',
            'hasilpemeriksaanrehabmedik_id' => 'Hasilpemeriksaanrehabmedik ID',
            'hasilpemeriksaanpa_id' => 'Hasilpemeriksaanpa ID',
            'rencanaoperasi_id' => 'Rencanaoperasi ID',
            'konsulpoli_id' => 'Konsulpoli ID',
            'tindakanpelayanan_id' => 'Tindakanpelayanan ID',
            'obatalkespasien_id' => 'Obatalkespasien ID',
            'pasienmorbiditas_id' => 'Pasienmorbiditas ID',
            'diagnosa_id' => 'Diagnosa ID',
            'diagnosa_nama' => 'Diagnosa Nama',
            'operasi_nama' => 'Operasi Nama',
            'dokter_pemeriksa' => 'Dokter Pemeriksa',
            'pasiendirujukkeluar_id' => 'Pasiendirujukkeluar ID',
            'rumahsakit_rujukan' => 'Rumahsakit Rujukan',
            'tglpasienpulang' => 'Rumahsakit Rujukan',
            'carakeluar_id' => 'Cara Keluar ID',
            'status_rj' => 'Status Rawat Jalan',
            'status_rd_ri' => 'Status RD dan RI',
            'no_rekam_medik' => 'No Rekam Medik',
        ];
    }
}
