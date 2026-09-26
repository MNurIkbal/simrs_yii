<?php

use yii\db\Migration;

/**
 * Class m211103_092152_migrate_obatalkes_m
 */
class m211103_092152_migrate_obatalkes_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
       $this->execute('ALTER TABLE "public"."obatalkes_m" 
  ALTER COLUMN "is_consigment" SET DEFAULT false;');

       $this->execute('ALTER TABLE "public"."obatalkes_m" ADD COLUMN if not exists "tipeobat_id" int4;');

       $this->execute('COMMENT ON COLUMN "public"."obatalkes_m"."tipeobat_id" IS \'lookup_type=tipe_obat\';');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211103_092152_migrate_obatalkes_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211103_092152_migrate_obatalkes_m cannot be reverted.\n";

        return false;
    }
    */
}
