<?php

use yii\db\Migration;

/**
 * Class m221214_152852_migrate_mhg3448_public_infoklaiminacbgdetail_v
 */
class m221214_152852_migrate_mhg3448_public_infoklaiminacbgdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."infoklaiminacbgdetail_v";');
        $this->execute("CREATE OR REPLACE VIEW public.infoklaiminacbgdetail_v
            AS SELECT klaiminacbgdetail_t.klaiminacbgdetail_id,
                klaiminacbg_t.klaiminacbg_id,
                klaiminacbgdetail_t.kode_diagnosa,
                klaiminacbgdetail_t.nama_diagnosa,
                klaiminacbgdetail_t.icd_versi,
                NULL::boolean AS is_icdprimer,
                NULL::boolean AS is_inacbg,
                klaiminacbg_t.status_klaim
            FROM klaiminacbg_t
                JOIN klaiminacbgdetail_t ON klaiminacbg_t.klaiminacbg_id = klaiminacbgdetail_t.klaiminacbg_id AND klaiminacbgdetail_t.is_deleted = false
            WHERE klaiminacbg_t.is_deleted = false
            UNION ALL
            SELECT NULL::integer AS klaiminacbgdetail_id,
                klaiminacbg_t.klaiminacbg_id,
                diagnosa_m.diagnosa_kode AS kode_diagnosa,
                diagnosa_m.diagnosa_nama AS nama_diagnosa,
                tabularlist_m.tabularlist_versi AS icd_versi,
                koreksidiagnosa_t.is_icdprimer,
                koreksidiagnosa_t.is_inacbg,
                klaiminacbg_t.status_klaim
            FROM klaiminacbg_t
                JOIN koreksidiagnosa_t ON klaiminacbg_t.pendaftaran_id = koreksidiagnosa_t.pendaftaran_id AND koreksidiagnosa_t.is_deleted = false AND koreksidiagnosa_t.is_inacbg = true
                JOIN diagnosa_m ON koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id AND diagnosa_m.is_deleted = false
                JOIN klasifikasidiagnosa_m ON diagnosa_m.klasifikasidiagnosa_id = klasifikasidiagnosa_m.klasifikasidiagnosa_id
                JOIN dtd_m ON klasifikasidiagnosa_m.dtd_id = dtd_m.dtd_id
                JOIN tabularlist_m ON dtd_m.tabularlist_id = tabularlist_m.tabularlist_id
            WHERE klaiminacbg_t.is_deleted = false;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221214_152852_migrate_mhg3448_public_infoklaiminacbgdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221214_152852_migrate_mhg3448_public_infoklaiminacbgdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
