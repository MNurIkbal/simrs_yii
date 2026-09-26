<?php

use yii\db\Migration;

/**
 * Class m231201_094026_migrate_optimaze_table_kuotadokter_r
 */
class m231201_094026_migrate_optimaze_table_kuotadokter_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP INDEX IF EXISTS"public"."kuota_jadwaldokter_id_idx";
        ');

        $this->execute('
            CREATE INDEX "kuota_jadwaldokter_id_idx" ON "public"."kuotadokter_r" USING btree (
                "jadwaldokter_id"
            );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231201_094026_migrate_optimaze_table_kuotadokter_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231201_094026_migrate_optimaze_table_kuotadokter_r cannot be reverted.\n";

        return false;
    }
    */
}
