<?php

use yii\db\Migration;

/**
 * Class m210705_040715_migrate_stokopname_t
 */
class m210705_040715_migrate_stokopname_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
$this->execute('ALTER TABLE "public"."stokopname_t" ADD COLUMN if not exists "ruanganinput_id" int4;');
$this->execute('ALTER TABLE "public"."stokopnamedetail_t" ADD COLUMN if not exists "stok_akhir" float8;');
$this->execute('ALTER TABLE "public"."stokopnamedetail_t" ADD COLUMN if not exists "selisih_akhir" float8;');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210705_040715_migrate_stokopname_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210705_040715_migrate_stokopname_t cannot be reverted.\n";

        return false;
    }
    */
}
