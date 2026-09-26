<?php

use yii\db\Migration;

/**
 * Class m250703_071156_RPP2164_pemberianpiutang_fn
 */
class m250703_071156_RPP2164_pemberianpiutang_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP FUNCTION IF EXISTS public.pemberianpiutang_fn(varchar, int4, int4);");
        $pemberianpiutang_fn = file_get_contents(__DIR__ . '/definitions/pemberianpiutang.fn.sql');
        $this->execute($pemberianpiutang_fn);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250703_071156_RPP2164_pemberianpiutang_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250703_071156_RPP2164_pemberianpiutang_fn cannot be reverted.\n";

        return false;
    }
    */
}
