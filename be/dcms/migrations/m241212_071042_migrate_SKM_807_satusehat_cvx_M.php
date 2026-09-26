<?php

use yii\db\Migration;

/**
 * Class m241212_071042_migrate_SKM_807_satusehat_cvx_M
 */
class m241212_071042_migrate_SKM_807_satusehat_cvx_M extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $satusehat_cvx_m = file_get_contents(__DIR__ . '/definitions/satusehat_cvx_m.sql');
        $this->execute($satusehat_cvx_m);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241212_071042_migrate_SKM_807_satusehat_cvx_M cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241212_071042_migrate_SKM_807_satusehat_cvx_M cannot be reverted.\n";

        return false;
    }
    */
}
