<?php

use yii\db\Migration;

/**
 * Class m240122_041321_migrate_RPP1060_function_pendaftaran_t
 */
class m240122_041321_migrate_RPP1060_function_pendaftaran_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $pendaftaran_t = file_get_contents(__DIR__ . '/definitions/pendaftaran_t.fn.sql');
        $this->execute($pendaftaran_t);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240122_041321_migrate_RPP1060_function_pendaftaran_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240122_041321_migrate_RPP1060_function_pendaftaran_t cannot be reverted.\n";

        return false;
    }
    */
}
