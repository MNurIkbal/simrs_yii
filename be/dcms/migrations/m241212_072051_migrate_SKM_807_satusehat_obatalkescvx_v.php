<?php

use yii\db\Migration;

/**
 * Class m241212_072051_migrate_SKM_807_satusehat_obatalkescvx_v
 */
class m241212_072051_migrate_SKM_807_satusehat_obatalkescvx_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS satusehat_obatalkescvx_v");
        $satusehat_obatalkescvx_v = file_get_contents(__DIR__ . '/definitions/satusehat_obatalkescvx_v.sql');
        $this->execute($satusehat_obatalkescvx_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241212_072051_migrate_SKM_807_satusehat_obatalkescvx_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241212_072051_migrate_SKM_807_satusehat_obatalkescvx_v cannot be reverted.\n";

        return false;
    }
    */
}
