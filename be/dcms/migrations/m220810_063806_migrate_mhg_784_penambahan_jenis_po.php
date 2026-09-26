<?php

use yii\db\Migration;

/**
 * Class m220810_063806_migrate_mhg_784_penambahan_jenis_po
 */
class m220810_063806_migrate_mhg_784_penambahan_jenis_po extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."validasipoobat_t" ADD IF NOT EXISTS "is_consigment" bool DEFAULT false;');
		$this->execute('ALTER TABLE "public"."validasipoobat_t" ADD IF NOT EXISTS "is_admin" bool DEFAULT false;');
		$this->execute('ALTER TABLE "public"."validasipoobat_t" ADD IF NOT EXISTS "is_cito" bool DEFAULT false;');
		
		$this->execute('ALTER TABLE "public"."validasipobarang_t" ADD IF NOT EXISTS "is_admin" bool DEFAULT false;');
		$this->execute('ALTER TABLE "public"."validasipobarang_t" ADD IF NOT EXISTS "is_cito" bool DEFAULT false;');
		
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220810_063806_migrate_mhg_784_penambahan_jenis_po cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220810_063806_migrate_mhg_784_penambahan_jenis_po cannot be reverted.\n";

        return false;
    }
    */
}
