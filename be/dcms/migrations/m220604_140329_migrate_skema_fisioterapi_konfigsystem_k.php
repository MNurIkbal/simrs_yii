<?php

use yii\db\Migration;

/**
 * Class m220604_140329_migrate_skema_fisioterapi_konfigsystem_k
 */
class m220604_140329_migrate_skema_fisioterapi_konfigsystem_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."konfigsystem_k" 
            ADD COLUMN IF NOT EXISTS "expired_time_program_fisio" int4,
            ADD COLUMN IF NOT EXISTS "is_expired_time_program_fisio" bool DEFAULT false;
            ');

        $this->execute('
            COMMENT ON COLUMN "public"."konfigsystem_k"."expired_time_program_fisio" IS \'Satuan Hari\';
            ');

        $this->execute('
            COMMENT ON COLUMN "public"."konfigsystem_k"."is_expired_time_program_fisio" IS \'untuk menentukan penggunaan expired beda rs\';
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220604_140329_migrate_skema_fisioterapi_konfigsystem_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220604_140329_migrate_skema_fisioterapi_konfigsystem_k cannot be reverted.\n";

        return false;
    }
    */
}
