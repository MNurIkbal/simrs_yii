<?php

use yii\db\Migration;

/**
 * Class m210120_051346_migrate_20210120_datapasienperdokter_v
 */
class m210120_051346_migrate_20210120_datapasienperdokter_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.datapasienperdokter_v;');
        $this->execute("CREATE VIEW \"public\".\"datapasienperdokter_v\" AS
             SELECT date(pendaftaran_t.tgl_pendaftaran) AS tgl_pendaftaran,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    pendaftaran_t.ruangan_id,
    ruangan_m.ruangan_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.pegawai_id
            ELSE pasienadmisi_t.pegawai_id
        END AS pegawai_id,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pegawai_m.nama_pegawai
            ELSE peg_admisi.nama_pegawai
        END AS nama_dokter,
    fgetnamalookup((pendaftaran_t.status_pasien)::integer) AS status_pasien,
    pasien_m.pasien_id,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    pasien_m.jeniskelamin,
    pendaftaran_t.carabayar_id,
    pendaftaran_t.penjamin_id,
    COALESCE(pasien_m.no_mobile_pasien, pasien_m.no_telepon_pasien) AS no_telepon_pasien,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    pasien_m.alamat_pasien,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar_nama,
    pendaftaran_t.status_periksa,
    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa_nama
   FROM ((((((pendaftaran_t
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN pegawai_m peg_admisi ON ((pasienadmisi_t.pegawai_id = peg_admisi.pegawai_id)))
     JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
  WHERE ((pendaftaran_t.is_active = true) AND (pendaftaran_t.is_deleted = false))
  GROUP BY (date(pendaftaran_t.tgl_pendaftaran)), pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, instalasi_m.instalasi_id, instalasi_m.instalasi_nama, pendaftaran_t.ruangan_id, ruangan_m.ruangan_nama, pegawai_m.pegawai_id, pegawai_m.nama_pegawai, (fgetnamalookup((pendaftaran_t.status_pasien)::integer)), pasien_m.pasien_id, pasien_m.nama_pasien, pasien_m.no_rekam_medik, pasien_m.jeniskelamin, pendaftaran_t.carabayar_id, pendaftaran_t.penjamin_id, COALESCE(pasien_m.no_mobile_pasien, pasien_m.no_telepon_pasien), pasien_m.tanggal_lahir, pendaftaran_t.umur, pasien_m.alamat_pasien, pasienadmisi_t.pegawai_id, peg_admisi.nama_pegawai
  ORDER BY (date(pendaftaran_t.tgl_pendaftaran)) DESC
            ;");
            $this->execute('ALTER TABLE public.datapasienperdokter_v
    OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210120_051346_migrate_20210120_datapasienperdokter_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210120_051346_migrate_20210120_datapasienperdokter_v cannot be reverted.\n";

        return false;
    }
    */
}
