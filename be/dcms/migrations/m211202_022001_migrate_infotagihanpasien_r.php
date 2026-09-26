<?php

use yii\db\Migration;

/**
 * Class m211202_022001_migrate_infotagihanpasien_r
 */
class m211202_022001_migrate_infotagihanpasien_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."infotagihanpasien_r" ADD COLUMN if not exists "is_ditagihkan" bool DEFAULT true;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211202_022001_migrate_infotagihanpasien_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211202_022001_migrate_infotagihanpasien_r cannot be reverted.\n";

        return false;
    }
    */
}
