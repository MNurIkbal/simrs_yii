<?php

use yii\db\Migration;

/**
 * Class m210819_054229_migrate_reservasimcudetail
 */
class m210819_054229_migrate_reservasimcudetail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.reservasimcudetail_v;');

        $this->execute("
            CREATE VIEW \"public\".\"reservasimcudetail_v\" AS  SELECT reservasimcu_r.reservasimcu_id,
    reservasimcu_r.no_exportexcel AS no_order,
    reservasimcu_r.tgl_reservasi AS tgl_order,
    reservasimcu_r.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pembayaran_t.no_pembayaran,
    reservasimcu_r.no_rekam_medik,
    reservasimcu_r.nama_lengkap AS nama,
    reservasimcu_r.tanggal_lahir,
    reservasimcu_r.jeniskelamin AS jeniskelamin_id,
    fgetnamalookup(reservasimcu_r.jeniskelamin::integer) AS jenis_kelamin,
    reservasimcu_r.penjamin_id,
    penjamin.penjamin_nama AS penjamin,
    pendaftaran_t.status_periksa,
    reservasimcu_r.status_reservasi AS statusreservasi_id,
    fgetnamalookup(reservasimcu_r.status_reservasi::integer) AS status_reservasi,
    pembayaran_t.pembayaran_id
   FROM reservasimcu_r
     JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
           FROM penjamin_m a) penjamin ON reservasimcu_r.penjamin_id = penjamin.penjamin_id
     LEFT JOIN ( SELECT a.pendaftaran_id,
            a.no_pendaftaran,
            a.tgl_pendaftaran,
            fgetnamalookup(a.status_periksa::integer) AS status_periksa
           FROM pendaftaran_t a) pendaftaran_t ON reservasimcu_r.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT a.pendaftaran_id,
            a.no_pembayaran,
            a.pembayaran_id
           FROM pembayaran_t a
          WHERE a.is_deleted = false) pembayaran_t ON pendaftaran_t.pendaftaran_id = pembayaran_t.pendaftaran_id;");
        
        $this->execute('ALTER TABLE "public"."reservasimcudetail_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210819_054229_migrate_reservasimcudetail cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210819_054229_migrate_reservasimcudetail cannot be reverted.\n";

        return false;
    }
    */
}
