<?php

use yii\db\Migration;

/**
 * Class m220620_113205_migrate_MHG2156_table_asesmenmedis_t
 */
class m220620_113205_migrate_MHG2156_table_asesmenmedis_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."asesmenmedis_t" ADD IF NOT EXISTS "paritas" int4,
                ADD IF NOT EXISTS "paritas_lainnya" text ,
                ADD IF NOT EXISTS "abortus" int4,
                ADD IF NOT EXISTS "abortus_lainnya" text ,
                ADD IF NOT EXISTS "meninggal" int4,
                ADD IF NOT EXISTS "meninggal_lainnya" text ,
                ADD IF NOT EXISTS "partus_dokter" int4,
                ADD IF NOT EXISTS "partus_bidan" int4,
                ADD IF NOT EXISTS "partus" int4,
                ADD IF NOT EXISTS "partus_lainnya" text ,
                ADD IF NOT EXISTS "lama_kehamilan" varchar(30) ,
                ADD IF NOT EXISTS "komplikasi" int4,
                ADD IF NOT EXISTS "komplikasi_lainnya" text ,
                ADD IF NOT EXISTS "neotanus" int4,
                ADD IF NOT EXISTS "neotanus_lainnya" text ,
                ADD IF NOT EXISTS "maternal" int4,
                ADD IF NOT EXISTS "maternal_lainnya" text ,
                ADD IF NOT EXISTS "r_tumbuh_kembang" text;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220620_113205_migrate_MHG2156_table_asesmenmedis_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220620_113205_migrate_MHG2156_table_asesmenmedis_t cannot be reverted.\n";

        return false;
    }
    */
}
