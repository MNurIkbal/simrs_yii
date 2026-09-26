<?php

use yii\db\Migration;

/**
 * Class m230123_063239_migrate_gb728_pasienkirimkeunitlain_t_add_ref_pasienmasukpenunjang_id
 */
class m230123_063239_migrate_gb728_pasienkirimkeunitlain_t_add_ref_pasienmasukpenunjang_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->execute('ALTER TABLE pasienkirimkeunitlain_t ADD IF NOT EXISTS "ref_pasienmasukpenunjang_id" int4;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230123_063239_migrate_gb728_pasienkirimkeunitlain_t_add_ref_pasienmasukpenunjang_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230123_063239_migrate_gb728_pasienkirimkeunitlain_t_add_ref_pasienmasukpenunjang_id cannot be reverted.\n";

        return false;
    }
    */
}
