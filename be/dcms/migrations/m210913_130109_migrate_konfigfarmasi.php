<?php

use yii\db\Migration;

/**
 * Class m210913_130109_migrate_konfigfarmasi
 */
class m210913_130109_migrate_konfigfarmasi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigfarmasi_k" ADD COLUMN if not exists "is_fulfilledso" bool DEFAULT false;');
        $this->execute('ALTER TABLE "public"."konfigfarmasi_k" ADD COLUMN if not exists "is_returnstock" bool DEFAULT false;');
        $this->execute('COMMENT ON COLUMN "public"."konfigfarmasi_k"."is_fulfilledso" IS \'config untuk transaksi so, boleh input null (false) atau harus diisi (true)\';');
        $this->execute('COMMENT ON COLUMN "public"."konfigfarmasi_k"."is_returnstock" IS \'konfig untuk return stok / tidak obat racikan\';');
     

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210913_130109_migrate_konfigfarmasi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210913_130109_migrate_konfigfarmasi cannot be reverted.\n";

        return false;
    }
    */
}
