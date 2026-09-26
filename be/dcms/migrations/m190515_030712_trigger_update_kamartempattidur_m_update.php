<?php

use yii\db\Migration;

/**
 * Class m190515_030712_trigger_update_kamartempattidur_m_update
 */
class m190515_030712_trigger_update_kamartempattidur_m_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
   {
        $this->execute(' DROP TRIGGER trigger_update_kamartempattidur_m ON kamartempattidur_m;');

         $this->execute('CREATE TRIGGER trigger_update_kamartempattidur_m
  AFTER UPDATE OF is_active, is_deleted, kamarruangan_id
  ON kamartempattidur_m
  FOR EACH ROW
  EXECUTE PROCEDURE kamartempattidur_m_update();');

         

    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190515_030712_trigger_update_kamartempattidur_m_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190515_030712_trigger_update_kamartempattidur_m_update cannot be reverted.\n";

        return false;
    }
    */
}
