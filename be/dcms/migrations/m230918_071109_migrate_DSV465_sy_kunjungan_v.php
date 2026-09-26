<?php

use yii\db\Migration;

/**
 * Class m230918_071109_migrate_DSV465_sy_kunjungan_v
 */
class m230918_071109_migrate_DSV465_sy_kunjungan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS sy_kunjungan_v");
        $sy_kunjungan_v = file_get_contents(__DIR__ . '/definitions/sy_kunjungan_v.sql');
        $this->execute($sy_kunjungan_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230918_071109_migrate_DSV465_sy_kunjungan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230918_071109_migrate_DSV465_sy_kunjungan_v cannot be reverted.\n";

        return false;
    }
    */
}
