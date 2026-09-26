<?php

use yii\db\Migration;

/**
 * Class m210616_045757_migrate_infotagihan_r
 */
class m210616_045757_migrate_infotagihan_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."infotagihanpasien_r" ADD COLUMN IF NOT exists "penyulit" float8;');

        $this->execute('ALTER TABLE "public"."infotagihanpasien_r" ADD COLUMN IF NOT exists "pelayanan_id" int4;');
        
        $this->execute('ALTER TABLE "public"."infotagihanpasien_r" ADD COLUMN IF NOT exists "harga_origin" float8;');

        $this->execute('ALTER TABLE "public"."infotagihanpasien_r" ADD COLUMN IF NOT exists "cyto_origin" float8;');

        $this->execute('ALTER TABLE "public"."infotagihanpasien_r" ADD COLUMN IF NOT exists "penyulit_origin" float8;');

        $this->execute('ALTER TABLE "public"."infotagihanpasien_r" ADD COLUMN IF NOT exists "id" int4;');
     
     
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210616_045757_migrate_infotagihan_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210616_045757_migrate_infotagihan_r cannot be reverted.\n";

        return false;
    }
    */
}
