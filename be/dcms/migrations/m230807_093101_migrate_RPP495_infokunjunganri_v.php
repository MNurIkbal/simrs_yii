<?php

use yii\db\Migration;

/**
 * Class m230807_093101_migrate_RPP495_infokunjunganri_v
 */
class m230807_093101_migrate_RPP495_infokunjunganri_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infokunjunganri_v");
        
        $infokunjunganri_v = file_get_contents(__DIR__ . '/definitions/infokunjunganri_v.sql');
        $this->execute($infokunjunganri_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230807_093101_migrate_RPP495_infokunjunganri_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230807_093101_migrate_RPP495_infokunjunganri_v cannot be reverted.\n";

        return false;
    }
    */
}
