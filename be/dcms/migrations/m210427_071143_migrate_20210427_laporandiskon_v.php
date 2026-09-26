<?php

use yii\db\Migration;

/**
 * Class m210427_071143_migrate_20210427_laporandiskon_v
 */
class m210427_071143_migrate_20210427_laporandiskon_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."laporandiskon_v";');

        $this->execute("
            CREATE VIEW \"public\".\"laporandiskon_v\" AS  SELECT pembayaranpelayanan_t.tgl_pembayaran,
    pembayaranpelayanan_t.tgl_pembayaran AS tgl_diskon,
    pembayaranpelayanan_t.no_pembayaran,
    pendaftaran_t.tgl_pendaftaran AS tgl_masuk,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pulang_rj.tglpasienpulang
            ELSE pulang_ri.tglpasienpulang
        END AS tgl_keluar,
    pendaftaran_t.no_pendaftaran,
    pasien_m.nama_pasien,
    pembayaran.catatan AS remarks,
    NULL::text AS diskon_type,
    pegawai_kasir.nama_pegawai AS authorized_by,
    loginpemakai_k.nama_pemakai AS username,
    pembayaran.total_tagihan AS billing_total,
    COALESCE(pembayaran.diskon, 0::double precision) AS discount_total
   FROM pendaftaran_t
     JOIN pembayaranpelayanan_t ON pendaftaran_t.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id AND pembayaranpelayanan_t.is_deleted = false
     JOIN ( SELECT pembayaran_t.pembayaran_id,
            pembayaran_t.total_tagihan,
            pembayaran_t.total_discount + pembayaran_t.total_discountpembayaran AS diskon,
            pembayaran_t.catatan,
            pembayaran_t.created_by
           FROM pembayaran_t) pembayaran ON pembayaranpelayanan_t.pembayaran_id = pembayaran.pembayaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN pasienpulang_t pulang_rj ON pendaftaran_t.pasienpulang_id = pulang_rj.pasienpulang_id
     LEFT JOIN pasienpulang_t pulang_ri ON pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id
     LEFT JOIN loginpemakai_k ON pembayaran.created_by = loginpemakai_k.loginpemakai_id
     LEFT JOIN pegawai_m pegawai_kasir ON loginpemakai_k.pegawai_id = pegawai_kasir.pegawai_id;");
        
        $this->execute('ALTER TABLE "public"."laporandiskon_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210427_071143_migrate_20210427_laporandiskon_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210427_071143_migrate_20210427_laporandiskon_v cannot be reverted.\n";

        return false;
    }
    */
}
