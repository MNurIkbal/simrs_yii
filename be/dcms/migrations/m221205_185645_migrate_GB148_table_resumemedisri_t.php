<?php

use yii\db\Migration;

/**
 * Class m221205_185645_migrate_GB148_table_resumemedisri_t
 */
class m221205_185645_migrate_GB148_table_resumemedisri_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."resumemedisri_t" ADD COLUMN IF NOT EXISTS "obat_dibawa_pulang_text" text;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220419_140900_migrate_GB148_table_resumemedisri_t cannot be reverted.\n";

        return false;
    }
}
