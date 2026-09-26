<?php

use yii\db\Migration;

/**
 * Class m241212_075317_migrate_SKM_827_satusehat_zataktif_v
 */
class m241212_075317_migrate_SKM_827_satusehat_zataktif_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS satusehat_zataktif_v;");
        $satusehat_zataktif_v = file_get_contents(__DIR__ . '/definitions/satusehat_zataktif_v.sql');
        $this->execute($satusehat_zataktif_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241212_075317_migrate_SKM_827_satusehat_zataktif_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241212_075317_migrate_SKM_827_satusehat_zataktif_v cannot be reverted.\n";

        return false;
    }
    */
}
