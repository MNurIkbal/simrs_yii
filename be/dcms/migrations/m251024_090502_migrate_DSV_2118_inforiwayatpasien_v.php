<?php

use yii\db\Migration;

/**
 * Class m251024_090502_migrate_DSV_2118_inforiwayatpasien_v
 */
class m251024_090502_migrate_DSV_2118_inforiwayatpasien_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS inforiwayatpasien_v;');
        $inforiwayatpasien_v = file_get_contents(__DIR__ . '/definitions/inforiwayatpasien_v.view.sql');
        $this->execute($inforiwayatpasien_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251024_090502_migrate_DSV_2118_inforiwayatpasien_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251024_090502_migrate_DSV_2118_inforiwayatpasien_v cannot be reverted.\n";

        return false;
    }
    */
}
