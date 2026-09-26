<?php

use yii\db\Migration;

/**
 * Class m231108_145652_migrate_GA_336_instalasi_v
 */
class m231108_145652_migrate_GA_336_instalasi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."instalasi_v";');
        
        $this->execute('
            CREATE OR REPLACE VIEW public.instalasi_v
            AS
            SELECT
                instalasi_m.instalasi_id,
                instalasi_m.instalasi_nama,
                instalasi_m.instalasi_singkatan,
                instalasi_m.profilers_id,
                profilrumahsakit_m.nama_rumahsakit,
                instalasi_m.is_pelayanan,
                instalasi_m.is_sync,
                instalasi_m.is_active,
                instalasi_m.is_deleted,
                satusehat_instalasi.satusehat_instalasi_id 
            FROM
                instalasi_m
                LEFT JOIN profilrumahsakit_m ON instalasi_m.profilers_id = profilrumahsakit_m.profilrs_id
                LEFT JOIN (
                        SELECT
                            instalasi_satusehat_m.id,
                            instalasi_satusehat_m.instalasi_id,
                            instalasi_satusehat_m.satusehat_instalasi_id,
                            instalasi_satusehat_m.is_active,
                            instalasi_satusehat_m.is_deleted 
                        FROM
                            instalasi_satusehat_m 
                        WHERE
                            instalasi_satusehat_m.satusehat_instalasi_id IS NOT NULL 
                            AND instalasi_satusehat_m.is_active = TRUE 
                            AND instalasi_satusehat_m.is_deleted = FALSE 
                ) satusehat_instalasi ON satusehat_instalasi.instalasi_id = instalasi_m.instalasi_id 
            WHERE
                instalasi_m.is_active = TRUE 
                AND instalasi_m.is_deleted = FALSE;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231108_145652_migrate_GA_336_instalasi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230612_092252_migrate_GA_336_instalasi_v cannot be reverted.\n";

        return false;
    }
    */
}
