<?php

use yii\db\Migration;

/**
 * Class m190515_031213_tigger_update_pesanbarangdetail_t_update
 */
class m190515_031213_tigger_update_pesanbarangdetail_t_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
  {
        $this->execute(' DROP TRIGGER tigger_update_pesanbarangdetail_t ON pesanbarangdetail_t;');

         $this->execute('CREATE TRIGGER tigger_update_pesanbarangdetail_t
  AFTER UPDATE OF barang_id, qty_pesan
  ON pesanbarangdetail_t
  FOR EACH ROW
  EXECUTE PROCEDURE pesanbarangdetail_t_update();');

         

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190515_031213_tigger_update_pesanbarangdetail_t_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190515_031213_tigger_update_pesanbarangdetail_t_update cannot be reverted.\n";

        return false;
    }
    */
}
