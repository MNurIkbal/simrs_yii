<?php

use yii\db\Migration;

/**
 * Class m190705_034108_stuktur_table_update
 */
class m190705_034108_stuktur_table_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
         ALTER TABLE "public"."syncakuntansi_r" 
         ADD COLUMN "penerimaanbarang_id" int4;
        ');

        $this->execute('
          ALTER TABLE "public"."penanggungjawab_m" 
          ALTER COLUMN "pengantar" DROP NOT NULL,
          ALTER COLUMN "penanggungjawab_nama" DROP NOT NULL;
        ');

        $this->execute('
           ALTER TABLE "public"."penerimaansupp_t" 
            ADD COLUMN "pajak_id" int4,
          ADD COLUMN "payterm_id" int4;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190705_034108_stuktur_table_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190705_034108_stuktur_table_update cannot be reverted.\n";

        return false;
    }
    */
}
