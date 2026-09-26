<?php

use yii\db\Migration;

/**
 * Class m220715_072706_migrate_MHG2859_pasienubahdata_v
 */
class m220715_072706_migrate_MHG2859_pasienubahdata_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."pasienubahdata_v";');
        $this->execute("CREATE OR REPLACE VIEW public.pasienubahdata_v
        AS SELECT pasienubahdata_t.pasienubahdata_id,
            pasienubahdata_t.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            pasienubahdata_t.tgl_ubahdata,
            pasienubahdata_t.keterangan_ubahdata,
            pasienubahdata_t.alasan_ubahdata,
            pasienubahdata_t.additional_data,
            pasienubahdata_t.created_date,
            pasienubahdata_t.created_by,
            pegawai_created.nama_pegawai AS pegawai_created,
            pasienubahdata_t.last_modified_date,
            pasienubahdata_t.last_modified_by,
            pegawai_modified.nama_pegawai AS pegawai_modified
           FROM pasienubahdata_t
             JOIN ( SELECT a.pasien_id,
                    a.nama_pasien,
                    a.no_rekam_medik
                   FROM pasien_m a) pasien_m ON pasienubahdata_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.loginpemakai_id,
                    a.pegawai_id
                   FROM loginpemakai_k a) login_created ON pasienubahdata_t.created_by = login_created.loginpemakai_id
             LEFT JOIN ( SELECT a.loginpemakai_id,
                    a.pegawai_id
                   FROM loginpemakai_k a) login_modified ON pasienubahdata_t.last_modified_by = login_modified.loginpemakai_id
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_created ON login_created.pegawai_id = pegawai_created.pegawai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_modified ON login_modified.pegawai_id = pegawai_modified.pegawai_id;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220715_072706_migrate_MHG2859_pasienubahdata_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220715_072706_migrate_MHG2859_pasienubahdata_v cannot be reverted.\n";

        return false;
    }
    */
}
