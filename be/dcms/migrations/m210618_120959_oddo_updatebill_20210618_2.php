<?php

use yii\db\Migration;

/**
 * Class m210618_120959_oddo_updatebill_20210618_2
 */
class m210618_120959_oddo_updatebill_20210618_2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."tindakanpelayanan_t" ADD COLUMN if not exists "is_overwrite" bool;');
        
        $this->execute('ALTER TABLE "public"."tindakanpelayanan_t" ADD COLUMN if not exists "harga_origin" float8;');

        $this->execute('ALTER TABLE "public"."tindakanpelayanan_t" ADD COLUMN if not exists "cyto_origin" float8;');

        $this->execute('ALTER TABLE "public"."tindakanpelayanan_t" ADD COLUMN if not exists "penyulit_origin" float8;');



        $this->execute('ALTER TABLE "public"."obatalkespasien_t" ADD COLUMN if not exists "is_overwrite" bool;');
        
        $this->execute('ALTER TABLE "public"."obatalkespasien_t" ADD COLUMN if not exists "harga_origin" float8;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210618_120959_oddo_updatebill_20210618_2 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210618_120959_oddo_updatebill_20210618_2 cannot be reverted.\n";

        return false;
    }
    */
}
