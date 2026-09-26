<?php

use yii\db\Migration;

/**
 * Class m240328_085210_migrate_pcp38_konfigfarmasi_k
 */
class m240328_085210_migrate_pcp38_konfigfarmasi_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigfarmasi_k" 
                            ADD COLUMN if not exists "is_transaksiobat_0" boolean;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240328_085210_migrate_pcp38_konfigfarmasi_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240328_085210_migrate_pcp38_konfigfarmasi_k cannot be reverted.\n";

        return false;
    }
    */
}
