<?php

use yii\db\Migration;

/**
 * Class m220604_140911_migrate_skema_fisioterapi_MHG2386_jadwalterapifisio_t
 */
class m220604_140911_migrate_skema_fisioterapi_MHG2386_jadwalterapifisio_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."jadwalterapifisio_t"
            ADD COLUMN IF NOT EXISTS "tgl_penjadwalan_awal" timestamp(6),
            ADD COLUMN IF NOT EXISTS "tgl_penjadwalan_akhir" timestamp(6),
            ADD COLUMN IF NOT EXISTS "pegawai_id" int4,
            ADD COLUMN IF NOT EXISTS "tgl_penjadwalan" timestamp(6);
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220604_140911_migrate_skema_fisioterapi_MHG2386_jadwalterapifisio_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220604_140911_migrate_skema_fisioterapi_MHG2386_jadwalterapifisio_t cannot be reverted.\n";

        return false;
    }
    */
}
