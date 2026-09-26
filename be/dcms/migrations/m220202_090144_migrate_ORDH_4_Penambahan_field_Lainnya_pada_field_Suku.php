<?php

use yii\db\Migration;

/**
 * Class m220202_090144_migrate_ORDH4_PenambahanfieldLainnyapadafieldSuku
 */
class m220202_090144_migrate_ORDH_4_Penambahan_field_Lainnya_pada_field_Suku extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DELETE FROM suku_m WHERE suku_id = 9999;');
        $this->execute("INSERT INTO suku_m (suku_id, suku_nama, suku_namalainnya) VALUES (9999, 'Lainnya', 'Lainnya');");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220202_090144_migrate_ORDH4_PenambahanfieldLainnyapadafieldSuku cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220202_090144_migrate_ORDH4_PenambahanfieldLainnyapadafieldSuku cannot be reverted.\n";

        return false;
    }
    */
}
