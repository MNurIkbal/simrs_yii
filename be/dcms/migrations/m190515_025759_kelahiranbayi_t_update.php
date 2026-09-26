<?php

use yii\db\Migration;

/**
 * Class m190515_025759_kelahiranbayi_t_update
 */
class m190515_025759_kelahiranbayi_t_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."kelahiranbayi_t" 
  ADD PRIMARY KEY ("kelahiranbayi_id");');

         $this->execute('ALTER TABLE "public"."kelahiranbayi_t" 
  ADD COLUMN "keterangan_cacat" text COLLATE "pg_catalog"."default";');

           $this->execute('ALTER TABLE "public"."kelahiranbayi_t" 
  ADD COLUMN "keterangan_hipotermi" text COLLATE "pg_catalog"."default";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190515_025759_kelahiranbayi_t_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190515_025759_kelahiranbayi_t_update cannot be reverted.\n";

        return false;
    }
    */
}
