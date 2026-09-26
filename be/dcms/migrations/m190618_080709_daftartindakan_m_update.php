<?php

use yii\db\Migration;

/**
 * Class m190618_080709_daftartindakan_m_update
 */
class m190618_080709_daftartindakan_m_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->execute('
        ALTER TABLE "public"."daftartindakan_m" 
  ADD COLUMN "is_akomodasi" bool DEFAULT false;
        ');  
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190618_080709_daftartindakan_m_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190618_080709_daftartindakan_m_update cannot be reverted.\n";

        return false;
    }
    */
}
