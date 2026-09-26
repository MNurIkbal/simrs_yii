<?php

use yii\db\Migration;

/**
 * Class m210428_073753_migrate_20210428_infoobatalkespasien_v
 */
class m210428_073753_migrate_20210428_infoobatalkespasien_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infoobatalkespasien_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infoobatalkespasien_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    obatalkespasien_t.obatalkespasien_id,
    racikan_m.racikan_nama,
    obatalkespasien_t.rke,
    obatalkes_m.obatalkes_nama,
    satuanunit_m.satuanunit_nama AS satuankecil_nama,
    obatalkespasien_t.signa_oa,
    obatalkespasien_t.qty_oa,
    obatalkespasien_t.tglpelayanan,
    obatalkespasien_t.obatsudahbayar_id,
    pegawai_1.nama_pegawai,
    pegawai_2.nama_pegawai AS nama_pegawai_dua,
    obatalkespasien_t.pasienmasukpenunjang_id,
    obatalkespasien_t.harganetto_oa,
        CASE
            WHEN obatalkespasien_t.daftartindakan_id IS NULL THEN obatalkespasien_t.tipepaket_id
            ELSE obatalkespasien_t.daftartindakan_id
        END AS daftartindakan_id,
        CASE
            WHEN obatalkespasien_t.daftartindakan_id IS NULL THEN tipepaket_m.tipepaket_nama::character varying(200)
            ELSE daftartindakan_m.daftartindakan_nama
        END AS daftartindakan_nama,
    pendaftaran_t.pasien_id,
    obatalkespasien_t.hargajual_oa
   FROM obatalkespasien_t
     JOIN pendaftaran_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN racikan_m ON obatalkespasien_t.racikan_id = racikan_m.racikan_id
     LEFT JOIN satuanunit_m ON obatalkes_m.satuankecil_id = satuanunit_m.satuanunit_id
     LEFT JOIN pegawai_m pegawai_1 ON pegawai_1.pegawai_id = obatalkespasien_t.perawat1_id
     LEFT JOIN pegawai_m pegawai_2 ON pegawai_2.pegawai_id = obatalkespasien_t.perawat2_id
     LEFT JOIN daftartindakan_m ON obatalkespasien_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN tipepaket_m ON obatalkespasien_t.tipepaket_id = tipepaket_m.tipepaket_id
  WHERE obatalkespasien_t.is_active = true AND obatalkespasien_t.is_deleted = false;");
        
        $this->execute('ALTER TABLE "public"."infoobatalkespasien_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210428_073753_migrate_20210428_infoobatalkespasien_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210428_073753_migrate_20210428_infoobatalkespasien_v cannot be reverted.\n";

        return false;
    }
    */
}
