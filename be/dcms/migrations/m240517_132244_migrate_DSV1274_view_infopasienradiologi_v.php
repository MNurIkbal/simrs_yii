<?php

use yii\db\Migration;

/**
 * Class m240517_132244_migrate_DSV1274_view_infopasienradiologi_v
 */
class m240517_132244_migrate_DSV1274_view_infopasienradiologi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopasienradiologi_v");
        $infopasienradiologi_v = file_get_contents(__DIR__ . '/definitions/infopasienradiologi_v.sql');
        $this->execute($infopasienradiologi_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240517_132244_migrate_DSV1274_view_infopasienradiologi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240517_132244_migrate_DSV1274_view_infopasienradiologi_v cannot be reverted.\n";

        return false;
    }
    */
}
