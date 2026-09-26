<?php

use yii\db\Migration;

/**
 * Class m231101_103220_migrate_DSV774_dokumeneklaimparams_v
 */
class m231101_103220_migrate_DSV774_dokumeneklaimparams_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS dokumeneklaimparams_v");
        $dokumeneklaimparams_v = file_get_contents(__DIR__ . '/definitions/dokumeneklaimparams_v.sql');
        $this->execute($dokumeneklaimparams_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231101_103220_migrate_DSV774_dokumeneklaimparams_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231101_103220_migrate_DSV774_dokumeneklaimparams_v cannot be reverted.\n";

        return false;
    }
    */
}
