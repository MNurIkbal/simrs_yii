<?php

use yii\db\Migration;

/**
 * Class m220530_074646_migrate_VCS173_konfigfarmasi_k
 */
class m220530_074646_migrate_VCS173_konfigfarmasi_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public".konfigfarmasi_k ADD COLUMN IF NOT EXISTS "is_check_stokobatalkesasal" bool DEFAULT FALSE;');
        $this->execute('ALTER TABLE "public".konfigfarmasi_k ADD COLUMN IF NOT EXISTS "basecalc_config" json;');
        $this->execute('ALTER TABLE "public".konfigfarmasi_k ADD COLUMN IF NOT EXISTS "base_price_so" VARCHAR DEFAULT 0;');
        $this->execute("COMMENT ON COLUMN konfigfarmasi_k.base_price_so IS '0 = weighted avg, 1 = base price'");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220530_074646_migrate_VCS173_konfigfarmasi_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220530_074646_migrate_VCS173_konfigfarmasi_k cannot be reverted.\n";

        return false;
    }
    */
}
