<?php

use yii\db\Migration;

/**
 * Class m220602_101508_migrate_VCS206_infopermintaanbmhpdetail_v
 */
class m220602_101508_migrate_VCS206_infopermintaanbmhpdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."infopermintaanbmhpdetail_v";');
        $this->execute("CREATE VIEW \"public\".\"infopermintaanbmhpdetail_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
        pendaftaran_t.no_pendaftaran,
        obatalkespasien_t.obatalkespasien_id,
        to_char(obatalkespasien_t.tglpelayanan, 'YYYY-MM-DD'::text)::date AS tgl_permintaan,
        obatalkespasien_t.obatalkes_id,
        obatalkes_m.obatalkes_nama,
        obatalkespasien_t.qty_oa AS qty_obat,
        obatalkespasien_t.qty_konversi,
        satuanunit_m.satuanunit_nama AS satuan_input,
        satuanunit_m.satuanunit_nama AS satuan_konversi,
        obatalkespasien_t.status_bmhp,
        obatalkespasien_t.satuankecil_id,
        ruangan_tujuan.ruangan_id AS ruangan_tujuan
       FROM pendaftaran_t
         LEFT JOIN ( SELECT a.pasienadmisi_id
               FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
         JOIN ( SELECT a.obatalkespasien_id,
                a.tglpelayanan,
                a.obatalkes_id,
                a.qty_oa,
                a.qty_konversi,
                a.status_bmhp,
                a.satuankecil_id,
                a.pendaftaran_id,
                a.ruangan_id,
                a.penjualanresep_id,
                a.is_deleted
               FROM obatalkespasien_t a) obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
         JOIN ( SELECT a.obatalkes_nama,
                a.obatalkes_id
               FROM obatalkes_m a) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
         JOIN ( SELECT a.ruangan_id,
                a.instalasi_id
               FROM ruangan_m a) ruangan_tujuan ON obatalkespasien_t.ruangan_id = ruangan_tujuan.ruangan_id
         LEFT JOIN ( SELECT a.satuanunit_id,
                a.satuanunit_nama
               FROM satuanunit_m a) satuanunit_m ON obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id
      WHERE obatalkespasien_t.penjualanresep_id IS NULL AND obatalkespasien_t.is_deleted = false AND ruangan_tujuan.instalasi_id = 6;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220602_101508_migrate_VCS206_infopermintaanbmhpdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220602_101508_migrate_VCS206_infopermintaanbmhpdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
