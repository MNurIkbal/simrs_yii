<?php

use yii\db\Migration;

/**
 * Class m241212_071622_migrate_SKM_807_table_satusehat_cvx_obatalkes_mp
 */
class m241212_071622_migrate_SKM_807_table_satusehat_cvx_obatalkes_mp extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $satusehat_cvx_obatalkes_mp = file_get_contents(__DIR__ . '/definitions/satusehat_cvx_obatalkes_mp.sql');
        $this->execute($satusehat_cvx_obatalkes_mp);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241212_071622_migrate_SKM_807_table_satusehat_cvx_obatalkes_mp cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241212_071622_migrate_SKM_807_table_satusehat_cvx_obatalkes_mp cannot be reverted.\n";

        return false;
    }
    */
}
