<?php

use yii\db\Migration;

/**
 * Class m201212_043734_migrate_mhbg_20201212_tabel_pasienadmisi_t
 */
class m201212_043734_migrate_mhbg_20201212_tabel_pasienadmisi_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pasienadmisi_t" 
  ADD COLUMN IF NOT EXISTS "limit_tagihan" float8 DEFAULT 0;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201212_043734_migrate_mhbg_20201212_tabel_pasienadmisi_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201212_043734_migrate_mhbg_20201212_tabel_pasienadmisi_t cannot be reverted.\n";

        return false;
    }
    */
}
