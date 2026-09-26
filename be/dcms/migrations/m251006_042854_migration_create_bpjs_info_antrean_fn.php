<?php

use yii\db\Migration;

/**
 * Class m251006_042854_migration_create_bpjs_info_antrean_fn
 */
class m251006_042854_migration_create_bpjs_info_antrean_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // bpjs_info_antrean_fn

        $this->execute("DROP FUNCTION IF EXISTS bpjs_info_antrean_fn");
        $bpjs_info_antrean_fn = file_get_contents(__DIR__ . '/definitions/bpjs_info_antrean_fn.sql');
        $this->execute($bpjs_info_antrean_fn);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251006_042854_migration_create_bpjs_info_antrean_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251006_042854_migration_create_bpjs_info_antrean_fn cannot be reverted.\n";

        return false;
    }
    */
}
