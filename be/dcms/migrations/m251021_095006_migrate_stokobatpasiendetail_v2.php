<?php

use yii\db\Migration;

/**
 * Class m251021_095006_migrate_stokobatpasiendetail_v2
 */
class m251021_095006_migrate_stokobatpasiendetail_v2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS stokobatpasiendetail_v;");
        $this->execute('
            CREATE VIEW public.stokobatpasiendetail_v
            AS SELECT rekonsiliasiobat_t.pendaftaran_id,
                \'REKONSILIASI_OBAT\'::text AS tipe_pemberian,
                stokobatpasien_r.stokobatpasien_id,
                \'REKONSILIASI OBAT\'::character varying AS nomor,
                obatalkes_m.jenisobatalkes_id,
                jenisobatalkes_m.jenisobatalkes_nama,
                stokobatpasien_r.obatalkes_id,
                stokobatpasien_r.nama_obat,
                rekonsiliasiobat_t.signa,
                stokobatpasien_r.stok_sisa,
                stokobatpasien_r.stok_dipakai,
                rekonsiliasiobat_t.dokter_id,
                dok_rekon.nama_pegawai AS dokter,
                NULL::timestamp without time zone AS tglpenjualan
            FROM stokobatpasien_r
                JOIN rekonsiliasiobat_t ON stokobatpasien_r.rekonsiliasiobat_id = rekonsiliasiobat_t.rekonsiliasiobat_id
                JOIN pendaftaran_t ON rekonsiliasiobat_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                LEFT JOIN obatalkes_m ON rekonsiliasiobat_t.obatalkes_id = obatalkes_m.obatalkes_id
                LEFT JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                LEFT JOIN pegawai_m dok_rekon ON rekonsiliasiobat_t.dokter_id = dok_rekon.pegawai_id
            UNION ALL
            SELECT penjualanresep_t.pendaftaran_id,
                \'RESEP\'::text AS tipe_pemberian,
                stokobatpasien_r.stokobatpasien_id,
                penjualanresep_t.noresep AS nomor,
                obatalkes_m.jenisobatalkes_id,
                jenisobatalkes_m.jenisobatalkes_nama,
                stokobatpasien_r.obatalkes_id,
                stokobatpasien_r.nama_obat,
                    CASE
                        WHEN obatalkespasien_t.signa_oa::integer = 0 OR obatalkespasien_t.signa_oa::integer IS NULL AND obatalkespasien_t.signa IS NOT NULL THEN (obatalkespasien_t.signa ->> \'text\'::text)::character varying
                        ELSE signaobat_m.signa_nama
                    END AS signa,
                stokobatpasien_r.stok_sisa,
                stokobatpasien_r.stok_dipakai,
                obatalkespasien_t.pegawai_id AS dokter_id,
                dok_resep.nama_pegawai AS dokter,
                penjualanresep_t.tglpenjualan
            FROM stokobatpasien_r
                JOIN obatalkespasien_t ON stokobatpasien_r.obatalkespasien_id = obatalkespasien_t.obatalkespasien_id
                JOIN penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
                LEFT JOIN signaobat_m ON obatalkespasien_t.signa_oa::integer = signaobat_m.signa_id
                JOIN obatalkes_m ON stokobatpasien_r.obatalkes_id = obatalkes_m.obatalkes_id
                JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                JOIN pegawai_m dok_resep ON obatalkespasien_t.pegawai_id = dok_resep.pegawai_id
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251021_095006_migrate_stokobatpasiendetail_v2 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251021_095006_migrate_stokobatpasiendetail_v2 cannot be reverted.\n";

        return false;
    }
    */
}
