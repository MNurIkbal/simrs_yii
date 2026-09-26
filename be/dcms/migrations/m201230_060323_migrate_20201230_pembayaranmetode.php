<?php

use yii\db\Migration;

/**
 * Class m201230_060323_migrate_20201230_pembayaranmetode
 */
class m201230_060323_migrate_20201230_pembayaranmetode extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute('ALTER TABLE "public"."pembayaranmetode_t" ADD COLUMN if not exists "edclist_id" int4;');
    $this->execute('ALTER TABLE "public"."pembayaranmetode_t" ADD COLUMN if not exists "nama_edc" varchar(255) COLLATE "pg_catalog"."default";');
  

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201230_060323_migrate_20201230_pembayaranmetode cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201230_060323_migrate_20201230_pembayaranmetode cannot be reverted.\n";

        return false;
    }
    */
}
