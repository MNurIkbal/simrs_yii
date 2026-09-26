<?php

use yii\db\Migration;

/**
 * Class m230919_074151_migrate_RPP660_infopembayarantransaksi_v
 */
class m230919_074151_migrate_RPP660_infopembayarantransaksi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopembayarantransaksi_v");
        $infopembayarantransaksi_v = file_get_contents(__DIR__ . '/definitions/infopembayarantransaksi_v.sql');
        $this->execute($infopembayarantransaksi_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230919_074151_migrate_RPP660_infopembayarantransaksi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230919_074151_migrate_RPP660_infopembayarantransaksi_v cannot be reverted.\n";

        return false;
    }
    */
}
