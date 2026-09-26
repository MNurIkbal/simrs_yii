<?php

use yii\db\Migration;

/**
 * Class m221229_063717_migrate_GA72_reseptur_t
 */
class m221229_063717_migrate_GA72_reseptur_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE reseptur_t DROP CONSTRAINT IF EXISTS no_reseptur;");
        $this->execute("ALTER TABLE reseptur_t add CONSTRAINT no_reseptur UNIQUE (noresep);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221229_063717_migrate_GA72_reseptur_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221229_063717_migrate_GA72_reseptur_t cannot be reverted.\n";

        return false;
    }
    */
}
