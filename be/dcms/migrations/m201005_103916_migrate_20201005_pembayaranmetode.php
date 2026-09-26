<?php

use yii\db\Migration;

/**
 * Class m201005_103916_migrate_20201005_pembayaranmetode
 */
class m201005_103916_migrate_20201005_pembayaranmetode extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
                $this->execute('ALTER TABLE "public"."pembayaranmetode_t" ADD IF NOT EXISTS "edclist_id" int4;');
                $this->execute('ALTER TABLE "public"."pembayaranmetode_t" ADD IF NOT EXISTS "nama_edc" varchar(255) COLLATE "pg_catalog"."default";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201005_103916_migrate_20201005_pembayaranmetode cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201005_103916_migrate_20201005_pembayaranmetode cannot be reverted.\n";

        return false;
    }
    */
}
