<?php

use yii\db\Migration;

/**
 * Class m210324_133210_oddo_20210324_penyesuaiantable
 */
class m210324_133210_oddo_20210324_penyesuaiantable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."int_obatalkespasien_r" ADD COLUMN IF NOT exists "tarif_dijamin" float8 DEFAULT 0;');
        $this->execute('ALTER TABLE "public"."int_obatalkespasien_r" ADD COLUMN IF NOT exists "tarif_dibayarkan" float8 DEFAULT 0;');
        $this->execute('ALTER TABLE "public"."int_obatalkespasien_r" ADD COLUMN IF NOT exists "tarif_diskon" float8 DEFAULT 0;');

        $this->execute('ALTER TABLE "public"."obatalkespasien_r" ADD COLUMN IF NOT exists "tarif_diskon" float8 DEFAULT 0;');

        $this->execute('ALTER TABLE "public"."tindakanpelayanan_r" ADD COLUMN IF NOT exists "tarif_diskon" float8 DEFAULT 0;');
      

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210324_133210_oddo_20210324_penyesuaiantable cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210324_133210_oddo_20210324_penyesuaiantable cannot be reverted.\n";

        return false;
    }
    */
}
