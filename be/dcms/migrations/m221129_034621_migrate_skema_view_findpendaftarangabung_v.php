<?php

use yii\db\Migration;

/**
 * Class m221129_034621_migrate_skema_view_findpendaftarangabung_v
 */
class m221129_034621_migrate_skema_view_findpendaftarangabung_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."findpendaftarangabung_v";
        ');

        $this->execute('
            CREATE VIEW "public"."findpendaftarangabung_v" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik, 
    pasien_m.nama_pasien,
    pendaftaran_t.penjamin_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.instalasi_id,
    COALESCE(gabung.is_gabung, false) AS is_gabung,
    instalasi_m.is_penunjang
   FROM pendaftaran_t
     JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.no_rekam_medik
           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama,
            a.is_penunjang
           FROM instalasi_m a) instalasi_m ON instalasi_m.instalasi_id = pendaftaran_t.instalasi_id
     LEFT JOIN ( SELECT x.pendaftaran_id,
            true AS is_gabung
           FROM ( SELECT gabungpelayanandetail_t.pendaftaran_id
                   FROM gabungpelayanandetail_t
                  WHERE gabungpelayanandetail_t.is_deleted = false
                UNION ALL
                 SELECT gabungpelayanandetail_t.ref_pendaftaran_id
                   FROM gabungpelayanandetail_t
                  WHERE gabungpelayanandetail_t.is_deleted = false) x
          GROUP BY x.pendaftaran_id) gabung ON pendaftaran_t.pendaftaran_id = gabung.pendaftaran_id
  WHERE pendaftaran_t.status_bayar = 349
  ORDER BY pendaftaran_t.tgl_pendaftaran DESC;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221129_034621_migrate_skema_view_findpendaftarangabung_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221129_034621_migrate_skema_view_findpendaftarangabung_v cannot be reverted.\n";

        return false;
    }
    */
}
