<?php

use yii\db\Migration;

/**
 * Class m211130_025535_migrate_infobatalpembayaran_v
 */
class m211130_025535_migrate_infobatalpembayaran_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pembayaran_t" 
  ADD COLUMN if not exists "no_pembayaran" varchar(255) COLLATE "pg_catalog"."default";');

        $this->execute('DROP VIEW if exists public.infobatalpembayaran_v;');

        $this->execute("
            CREATE VIEW \"public\".\"infobatalpembayaran_v\" AS  SELECT pembayaran_t.deleted_date AS tanggal_batal,
    pegawai_delete.nama_pegawai AS dibatalkan_oleh,
    COALESCE(pembayaran_t.no_pembayaran,pembayaranpelayanan_t.no_pembayaran) as no_pembayaran,
    pembayaran_t.created_date AS tgl_pembayaran,
    COALESCE(pasien_m.nama_pasien, penjualanresep_t.nama_pembeli) AS nama_pasien,
    pasien_m.no_rekam_medik AS no_rm,
    COALESCE(pendaftaran_t.no_pendaftaran, penjualanresep_t.noresep) AS no_pendaftaran,
    pembayaran_t.total_tagihan AS jumlah_tagihan,
    pembayaran_t.alasan_batal,
    pendaftaran_t.pendaftaran_id
   FROM pembayaran_t
     LEFT JOIN ( SELECT a.pembayaran_id,
            a.penjualanresep_id,
            a.no_pembayaran
           FROM pembayaranpelayanan_t a
          GROUP BY a.pembayaran_id, a.penjualanresep_id, a.no_pembayaran
          ) pembayaranpelayanan_t ON pembayaran_t.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
     LEFT JOIN loginpemakai_k login_delete ON pembayaran_t.deleted_by = login_delete.loginpemakai_id
     LEFT JOIN pegawai_m pegawai_delete ON login_delete.pegawai_id = pegawai_delete.pegawai_id
     LEFT JOIN pendaftaran_t ON pembayaran_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN penjualanresep_t ON pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
     LEFT JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
  WHERE pembayaran_t.is_deleted = true;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211130_025535_migrate_infobatalpembayaran_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211130_025535_migrate_infobatalpembayaran_v cannot be reverted.\n";

        return false;
    }
    */
}
