<?php

use yii\db\Migration;

/**
 * Class m210923_100107_migrate_obatdonasi
 */
class m210923_100107_migrate_obatdonasi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigfarmasi_k" ADD COLUMN if not exists "harga_donasi" float8 DEFAULT 0;');

        $this->execute('ALTER TABLE "public"."penerimaansupp_t" ADD COLUMN if not exists "is_donasi" bool DEFAULT false;');
        
        $this->execute('ALTER TABLE "public"."penerimaansuppdetail_t" ADD COLUMN if not exists "is_donasi" bool DEFAULT false;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210923_100107_migrate_obatdonasi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210923_100107_migrate_obatdonasi cannot be reverted.\n";

        return false;
    }
    */
}
