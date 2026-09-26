<?php

use yii\db\Migration;

/**
 * Class m210309_110859_migrate_20210309_3140_laporanpasienrujukridet_v
 */
class m210309_110859_migrate_20210309_3140_laporanpasienrujukridet_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporanpasienrujukridet_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laporanpasienrujukridet_v\" AS
            SELECT 'RJ'::text AS jenis,
            pendaftaran_t.tgl_pendaftaran AS tgl_kunjungan,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.alamat_pasien,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama
            FROM ((((pendaftaran_t
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            WHERE ((pasienpulang_t.carakeluar_id = 5) AND (pendaftaran_t.instalasi_id = 1) AND (pendaftaran_t.is_active = true) AND (pendaftaran_t.is_deleted = false))
            UNION ALL
            SELECT 'RD'::text AS jenis,
            pendaftaran_t.tgl_pendaftaran AS tgl_kunjungan,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.alamat_pasien,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama
            FROM ((((pendaftaran_t
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            WHERE ((pasienpulang_t.carakeluar_id = 5) AND (pendaftaran_t.instalasi_id = 2) AND (pendaftaran_t.is_active = true) AND (pendaftaran_t.is_deleted = false))
            ;");
            $this->execute('
                ALTER TABLE public.laporanpasienrujukridet_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210309_110859_migrate_20210309_3140_laporanpasienrujukridet_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210309_110859_migrate_20210309_3140_laporanpasienrujukridet_v cannot be reverted.\n";

        return false;
    }
    */
}
