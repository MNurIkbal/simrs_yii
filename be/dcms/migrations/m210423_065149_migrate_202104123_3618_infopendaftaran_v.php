<?php

use yii\db\Migration;

/**
 * Class m210423_065149_migrate_202104123_3618_infopendaftaran_v
 */
class m210423_065149_migrate_202104123_3618_infopendaftaran_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infopendaftaran_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infopendaftaran_v\" AS
            SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.tgl_pendaftaran AS tgl_masuk,
            pasienpulang_t.tglpasienpulang AS tgl_keluar,
            pasienpulang_t.carakeluar_id,
            carakeluar_m.carakeluar_nama,
            bpjs_t.bpjs_id,
            bpjs_t.nosep,
            bpjs_t.nokartuasuransi,
            pasien_m.jeniskelamin AS jeniskelamin_id,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin_nama,
            pasien_m.tanggal_lahir,
            pendaftaran_t.pasien_id,
            pasien_m.nama_pasien,
            pendaftaran_t.umur,
            pasien_m.no_rekam_medik,
            pendaftaran_t.pegawai_id AS dokter_id,
            pegawai_m.nama_pegawai AS dokter_nama,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            bpjs_t.jnspelayanan,
            pasien_m.jenisidentitas AS jenisidentitas_id,
            fgetnamalookup((pasien_m.jenisidentitas)::integer) AS jenisidentitas_nama,
            pasien_m.no_identitas_pasien,
            pasien_m.additional_pasien
            FROM (((((((pendaftaran_t
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
            JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
            LEFT JOIN bpjs_t ON ((pendaftaran_t.pendaftaran_id = bpjs_t.pendaftaran_id)))
        ;");
        $this->execute('
            ALTER TABLE public.infopendaftaran_v OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210423_065149_migrate_202104123_3618_infopendaftaran_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210423_065149_migrate_202104123_3618_infopendaftaran_v cannot be reverted.\n";

        return false;
    }
    */
}
