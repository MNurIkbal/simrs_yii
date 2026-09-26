<?php

use yii\db\Migration;

/**
 * Class m230815_095938_migrate_RPP513_skrining_pasien_rj_t
 */
class m230815_095938_migrate_RPP513_skrining_pasien_rj_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS public.skrining_pasien_rj_t (
            pendaftaran_id int4 NULL,
            kesadaran int4 NULL,
            pernapasan int4 NULL,
            resiko_jatuh int4 NULL,
            nyeri_dada int4 NULL,
            nyeri varchar(100) NULL,
            batuk int4 NULL,
            keputusan int4 NULL,
            bahasa int4 NULL,
            bahasa_daerah varchar(100) NULL,
            bahasa_asing varchar(100) NULL,
            poli_tujuan varchar(100) NULL,
            asal_rujukan varchar(100) NULL,
            petugas_loket_pendaftaran varchar(100) NULL,
            pasien_keluarga_pasien varchar(100) NULL,
            created_date date NULL,
            is_active bool NULL DEFAULT true,
            is_deleted bool NULL DEFAULT false,
            skrining_pasien_rj_id serial4 NOT NULL,
            CONSTRAINT skrining_pasien_rj_t_pk PRIMARY KEY (skrining_pasien_rj_id)
        );");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230815_095938_migrate_RPP513_skrining_pasien_rj_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230815_095938_migrate_RPP513_skrining_pasien_rj_t cannot be reverted.\n";

        return false;
    }
    */
}
