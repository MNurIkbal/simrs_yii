<?php

use yii\db\Migration;

/**
 * Class m240626_030643_migrate_GA_1692_improve_scroll_cashier_trx_pengeluaran_penerimaan
 */
class m240626_030643_migrate_GA_1692_improve_scroll_cashier_trx_pengeluaran_penerimaan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS public.newodoo_scroll_v;");

        $newodoo_scroll_v = file_get_contents(__DIR__ . '/definitions/newodoo_scroll_v.sql');
        $this->execute($newodoo_scroll_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240626_030643_migrate_GA_1692_improve_scroll_cashier_trx_pengeluaran_penerimaan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240626_030643_migrate_GA_1692_improve_scroll_cashier_trx_pengeluaran_penerimaan cannot be reverted.\n";

        return false;
    }
    */
}
