<?php

use yii\db\Migration;

/**
 * Class m220105_130404_migrate_US2599_additional_style_docmapping_k_master
 */
class m220105_130404_migrate_US2599_additional_style_docmapping_k_master extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->execute('ALTER TABLE "public"."docmapping_k" ADD COLUMN IF NOT EXISTS "additional_style" text;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220105_130404_migrate_US2599_additional_style_docmapping_k_master cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220105_130404_migrate_US2599_additional_style_docmapping_k_master cannot be reverted.\n";

        return false;
    }
    */
}
