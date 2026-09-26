<?php

use yii\db\Migration;

/**
 * Class m230904_094412_migrate_RPP339_infopasienri_v
 */
class m230904_094412_migrate_RPP339_infopasienri_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopasienri_v");
        $infopasienri_v = file_get_contents(__DIR__ . '/definitions/infopasienri_v.sql');
        $this->execute($infopasienri_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230904_094412_migrate_RPP339_infopasienri_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230904_094412_migrate_RPP339_infopasienri_v cannot be reverted.\n";

        return false;
    }
    */
}
