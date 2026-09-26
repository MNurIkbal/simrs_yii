<?php

use yii\db\Migration;

/**
 * Class m190114_042130_alter_table_add_code
 */
class m190114_042130_alter_table_add_code extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE pendidikan_m ADD COLUMN pendidikan_kode VARCHAR
            ");
        $this->execute("
            ALTER TABLE pekerjaan_m ADD COLUMN pekerjaan_kode VARCHAR
            ");
        $this->execute("
            ALTER TABLE asalrujukan_m ADD COLUMN asalrujukan_kode VARCHAR
            ");
        $this->execute("
            ALTER TABLE carabayar_m ADD COLUMN carabayar_kode VARCHAR
            ");
        $this->execute("
            ALTER TABLE penjamin_m ADD COLUMN s_kode VARCHAR
            ");
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m190114_042130_alter_table_add_code cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190114_042130_alter_table_add_code cannot be reverted.\n";

        return false;
    }
    */
}
