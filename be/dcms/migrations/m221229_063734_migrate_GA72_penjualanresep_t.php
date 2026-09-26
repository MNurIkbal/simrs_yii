<?php

use yii\db\Migration;

/**
 * Class m221229_063734_migrate_GA72_penjualanresep_t
 */
class m221229_063734_migrate_GA72_penjualanresep_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE penjualanresep_t DROP CONSTRAINT IF EXISTS no_resep;");
        $this->execute("ALTER TABLE penjualanresep_t add CONSTRAINT no_resep UNIQUE (noresep);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221229_063734_migrate_GA72_penjualanresep_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221229_063734_migrate_GA72_penjualanresep_t cannot be reverted.\n";

        return false;
    }
    */
}
