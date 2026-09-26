<?php

use yii\db\Migration;

/**
 * Class m231115_071307_migrate_mhg_5422_mhg_5433_ruangan_v
 */
class m231115_071307_migrate_mhg_5422_mhg_5433_ruangan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."ruangan_v";');
        $this->execute('
            CREATE OR REPLACE VIEW public.ruangan_v
                AS
                SELECT
                    ruangan_m.instalasi_id,
                    instalasi_m.instalasi_nama,
                    ruangan_m.ruangan_id,
                    ruangan_m.ruangan_nama,
                    ruangan_m.ruangan_singkatan,
                    ruangan_m.is_active,
                    ruangan_m.is_sync,
                    ruangan_m.is_online,
                    ruangan_m.ruangan_image AS ruangan_gambar,
                    ruangan_m.ruangan_image_blob,
                    ruangan_m.ruangan_filesuara_blob,
                    ruangan_m.kode_ruangan_bpjs,
                    satusehat_ruangan.satusehat_ruangan_id 
                FROM
                    ruangan_m
                JOIN ( 
                    SELECT 
                        instalasi_m_1.instalasi_id, 
                        instalasi_m_1.instalasi_nama 
                    FROM instalasi_m instalasi_m_1 
                ) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                LEFT JOIN (
                    SELECT
                        r_satusehat.ruangan_id,
                        r_satusehat.satusehat_ruangan_id,
                        r_satusehat.satusehat_integration_id 
                    FROM
                        ruangan_satusehat_m r_satusehat 
                    WHERE
                        r_satusehat.satusehat_ruangan_id IS NOT NULL 
                        AND r_satusehat.is_deleted = FALSE 
                        AND r_satusehat.is_active = TRUE 
                ) satusehat_ruangan ON satusehat_ruangan.ruangan_id = ruangan_m.ruangan_id 
                WHERE
                    ruangan_m.is_deleted = FALSE;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231115_071307_migrate_mhg_5422_mhg_5433_ruangan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231115_071307_migrate_mhg_5422_mhg_5433_ruangan_v cannot be reverted.\n";

        return false;
    }
    */
}
