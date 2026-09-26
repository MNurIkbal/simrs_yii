<?php

use yii\db\Migration;

/**
 * Class m250904_030154_rpp_2225_create_indexing_order_no_hasilpemeriksaanlab_roche_t
 */
class m250904_030154_rpp_2225_create_indexing_order_no_hasilpemeriksaanlab_roche_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE INDEX IF NOT EXISTS "hasilpemeriksaanlab_roche_t_order_no_idx" ON "public"."hasilpemeriksaanlab_roche_t" USING btree ("order_no");
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250904_030154_rpp_2225_create_indexing_order_no_hasilpemeriksaanlab_roche_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250904_030154_rpp_2225_create_indexing_order_no_hasilpemeriksaanlab_roche_t cannot be reverted.\n";

        return false;
    }
    */
}
