<?php

use yii\db\Migration;

/**
 * Class m221217_050751_migrate_gb_330_konfigfarmasi_k_is_weighted_avg_rs
 */
class m221217_050751_migrate_gb_330_konfigfarmasi_k_is_weighted_avg_rs extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE konfigfarmasi_k ADD IF NOT EXISTS is_weighted_avg_rs bool NOT NULL DEFAULT true;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221217_050751_migrate_gb_330_konfigfarmasi_k_is_weighted_avg_rs cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221217_050751_migrate_gb_330_konfigfarmasi_k_is_weighted_avg_rs cannot be reverted.\n";

        return false;
    }
    */
}
