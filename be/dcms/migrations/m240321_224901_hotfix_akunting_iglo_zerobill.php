<?php

use yii\db\Migration;

/**
 * Class m240321_224901_hotfix_akunting_iglo_zerobill
 */
class m240321_224901_hotfix_akunting_iglo_zerobill extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS newodoo_saleorderline_v");
        $newodoo_saleorderline_v = file_get_contents(__DIR__ . '/definitions/newodoo_saleorderline_v.sql');
        $this->execute($newodoo_saleorderline_v);
        
        $this->execute("DROP VIEW IF EXISTS newodoo_saleorderlineobat_v");
        $newodoo_saleorderlineobat_v = file_get_contents(__DIR__ . '/definitions/newodoo_saleorderlineobat_v.sql');
        $this->execute($newodoo_saleorderlineobat_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240321_224901_hotfix_akunting_iglo_zerobill cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240321_224901_hotfix_akunting_iglo_zerobill cannot be reverted.\n";

        return false;
    }
    */
}
