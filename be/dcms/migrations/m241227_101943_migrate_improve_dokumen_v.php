<?php

use yii\db\Migration;

/**
 * Class m241227_101943_migrate_improve_dokumen_v
 */
class m241227_101943_migrate_improve_dokumen_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS dokumen_v");
        $dokumen_v = file_get_contents(__DIR__ . '/definitions/dokumen_v.sql');
        $this->execute($dokumen_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241227_101943_migrate_improve_dokumen_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241227_101943_migrate_improve_dokumen_v cannot be reverted.\n";

        return false;
    }
    */
}
