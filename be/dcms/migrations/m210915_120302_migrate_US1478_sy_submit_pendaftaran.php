<?php

use yii\db\Migration;

/**
 * Class m210915_120302_migrate_US1478_sy_submit_pendaftaran
 */
class m210915_120302_migrate_US1478_sy_submit_pendaftaran extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigtarif_k"
            ADD COLUMN IF NOT EXISTS "is_spesialis" bool DEFAULT false;');

        $this->execute('ALTER TABLE "public"."tariftindakan_m"
            ADD COLUMN IF NOT EXISTS "dokter_id" int4;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210915_120302_migrate_US1478_sy_submit_pendaftaran cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210915_120302_migrate_US1478_sy_submit_pendaftaran cannot be reverted.\n";

        return false;
    }
    */
}
