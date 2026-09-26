<?php

use yii\db\Migration;

/**
 * Class m210705_101318_migrate_improve_konfigpendigd_3734
 */
class m210705_101318_migrate_improve_konfigpendigd_3734 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pendaftaran_t" 
          ADD COLUMN IF NOT EXISTS "prev_pendaftaran_id" int4;');
        $this->execute('COMMENT ON COLUMN "public"."pendaftaran_t"."prev_pendaftaran_id" IS \'menyimpan id pendaftaran sebelumnya\';');
        $this->execute('ALTER TABLE "public"."konfigsystem_k" 
          ADD COLUMN IF NOT EXISTS "is_set_igdkeri" bool DEFAULT false;');
        $this->execute('COMMENT ON COLUMN "public"."konfigsystem_k"."is_set_igdkeri" IS \'Konfig MCU ke RI\';');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210705_101318_migrate_improve_konfigpendigd_3734 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210705_101318_migrate_improve_konfigpendigd_3734 cannot be reverted.\n";

        return false;
    }
    */
}
