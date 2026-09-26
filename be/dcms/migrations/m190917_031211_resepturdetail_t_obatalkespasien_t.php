<?php

use yii\db\Migration;

/**
 * Class m190917_031211_resepturdetail_t_obatalkespasien_t
 */
class m190917_031211_resepturdetail_t_obatalkespasien_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."resepturdetail_t" 
                    ADD COLUMN "qty_konversi" float8;');

        $this->execute('ALTER TABLE "public"."obatalkespasien_t" 
  ADD COLUMN "qty_konversi" float8;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190917_031211_resepturdetail_t_obatalkespasien_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190917_031211_resepturdetail_t_obatalkespasien_t cannot be reverted.\n";

        return false;
    }
    */
}
