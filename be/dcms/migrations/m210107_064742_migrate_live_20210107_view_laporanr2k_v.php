<?php

use yii\db\Migration;

/**
 * Class m210107_064742_migrate_live_20210107_view_laporanr2k_v
 */
class m210107_064742_migrate_live_20210107_view_laporanr2k_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporanr2k_v;');
        $this->execute("CREATE VIEW \"public\".\"laporanr2k_v\" AS
             SELECT pendaftaran_t.pendaftaran_id,
    pasien_m.pasien_id,
    pendaftaran_t.no_pendaftaran AS no_registrasi,
    pasien_m.no_rekam_medik,
    pendaftaran_t.tgl_pendaftaran AS tgl_registrasi,
    pegawai_m.nama_pegawai AS nama_dokter,
    pasien_m.nama_pasien,
    pasien_m.alamat_pasien,
    pasien_m.tanggal_lahir,
    pasien_m.tempat_lahir,
    pendaftaran_t.umur,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin,
    fgetnamalookup((pasien_m.warga_negara)::integer) AS kebangsaan,
    suku_m.suku_nama,
        CASE
            WHEN (pasien_m.no_identitas_pasien IS NULL) THEN (pasien_m.additional_pasien)::character varying
            ELSE pasien_m.no_identitas_pasien
        END AS no_identitas_pasien,
    fgetnamalookup((pasien_m.statusperkawinan)::integer) AS statusperkawinan,
    fgetnamalookup((pasien_m.agama)::integer) AS agama,
    asuransipasien_m.namaperusahaan,
    asuransipasien_m.nokartuasuransi AS no_asuransi_pasien,
    pasien_m.no_telepon_pasien,
    pendidikan_m.pendidikan_nama,
    pekerjaan_m.pekerjaan_nama,
    NULL::text AS pemberitahuan,
    penanggungjawab_m.penanggungjawab_nama,
        CASE
            WHEN ((penanggungjawab_m.jenisidentitas)::text = '94'::text) THEN penanggungjawab_m.no_identitas
            ELSE penanggungjawab_m.no_identitas
        END AS no_identitas_penanggung,
    penanggungjawab_m.penanggungjawab_alamat,
    penanggungjawab_m.hubungankeluarga,
    NULL::text AS alamat_kantor,
    pegawai_m.nama_pegawai AS dokter_pengirim,
    fgetnamalookup((pasien_m.golongandarah)::integer) AS golongandarah,
    NULL::text AS diet,
    asesmenperawatrd_t.alergi_obat,
    asesmenperawatrd_t.alergi_lainnya
   FROM ((((((((pendaftaran_t
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
     LEFT JOIN suku_m ON ((pasien_m.suku_id = suku_m.suku_id)))
     LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
     LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
     LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
     LEFT JOIN asesmenperawatrd_t ON ((pendaftaran_t.pendaftaran_id = asesmenperawatrd_t.pendaftaran_id)))
            ;");
            $this->execute('ALTER TABLE public.laporanr2k_v
    OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210107_064742_migrate_live_20210107_view_laporanr2k_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210107_064742_migrate_live_20210107_view_laporanr2k_v cannot be reverted.\n";

        return false;
    }
    */
}
