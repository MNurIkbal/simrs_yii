<?php

use yii\db\Migration;

/**
 * Class m240104_071605_migrate_glbj297_view_laporancatatankeperawatan_v
 */
class m240104_071605_migrate_glbj297_view_laporancatatankeperawatan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS laporancatatankeperawatan_v");
        $laporancatatankeperawatan_v = file_get_contents(__DIR__ . '/definitions/laporancatatankeperawatan_v.sql');
        $this->execute($laporancatatankeperawatan_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240104_071605_migrate_glbj297_view_laporancatatankeperawatan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240104_071605_migrate_glbj297_view_laporancatatankeperawatan_v cannot be reverted.\n";

        return false;
    }
    */
}
