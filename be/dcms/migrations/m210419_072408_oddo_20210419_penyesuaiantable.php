<?php

use yii\db\Migration;

/**
 * Class m210419_072408_oddo_20210419_penyesuaiantable
 */
class m210419_072408_oddo_20210419_penyesuaiantable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

  $this->execute('ALTER TABLE "public"."int_obatalkespasien_r" ALTER COLUMN  "tarif_dijamin" SET DEFAULT 0;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210419_072408_oddo_20210419_penyesuaiantable cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210419_072408_oddo_20210419_penyesuaiantable cannot be reverted.\n";

        return false;
    }
    */
}
