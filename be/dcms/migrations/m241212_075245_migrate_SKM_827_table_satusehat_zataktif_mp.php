<?php

use yii\db\Migration;

/**
 * Class m241212_075245_migrate_SKM_827_table_satusehat_zataktif_mp
 */
class m241212_075245_migrate_SKM_827_table_satusehat_zataktif_mp extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $satusehat_zataktif_mp = file_get_contents(__DIR__ . '/definitions/satusehat_zataktif_mp.sql');
        $this->execute($satusehat_zataktif_mp);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241212_075245_migrate_SKM_827_table_satusehat_zataktif_mp cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241212_075245_migrate_SKM_827_table_satusehat_zataktif_mp cannot be reverted.\n";

        return false;
    }
    */
}
