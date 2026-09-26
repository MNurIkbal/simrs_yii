<?php

use yii\db\Migration;

/**
 * Class m241117_151003_migrate_pcp62_bulk_register_infopendaftaranol
 */
class m241117_151003_migrate_pcp62_bulk_register_infopendaftaranol extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopendaftaranol_v");
        $infoPendaftaranOlView = file_get_contents(__DIR__ . '/definitions/infopendaftaranol_v.view.sql');
        $this->execute($infoPendaftaranOlView);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241117_151003_migrate_pcp62_bulk_register_infopendaftaranol cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241117_151003_migrate_pcp62_bulk_register_infopendaftaranol cannot be reverted.\n";

        return false;
    }
    */
}
