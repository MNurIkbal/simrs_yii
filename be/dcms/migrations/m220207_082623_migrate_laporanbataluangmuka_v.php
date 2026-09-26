<?php

use yii\db\Migration;

/**
 * Class m220207_082623_migrate_laporanbataluangmuka_v
 */
class m220207_082623_migrate_laporanbataluangmuka_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporanbataluangmuka_v;');

        $this->execute("
            CREATE VIEW \"public\".\"laporanbataluangmuka_v\" AS  SELECT bayaruangmuka_t.bayaruangmuka_id,
    bayaruangmuka_t.pendaftaran_id,
    bayaruangmuka_t.deleted_date AS tgl_batal,
    pembayaran_t.no_pembayaran,
    pembayaran_t.created_date AS tgl_pembayaran,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    bayaruangmuka_t.no_uangmuka,
    bayaruangmuka_t.alasan_batal,
    bayaruangmuka_t.jumlah_uangmuka,
    bayaruangmuka_t.created_date AS tgl_uangmuka
   FROM bayaruangmuka_t
     JOIN pendaftaran_t ON bayaruangmuka_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pembayaran_t ON pendaftaran_t.pendaftaran_id = pembayaran_t.pendaftaran_id AND pembayaran_t.is_deleted = false
  WHERE bayaruangmuka_t.is_deleted = true;
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220207_082623_migrate_laporanbataluangmuka_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220207_082623_migrate_laporanbataluangmuka_v cannot be reverted.\n";

        return false;
    }
    */
}
