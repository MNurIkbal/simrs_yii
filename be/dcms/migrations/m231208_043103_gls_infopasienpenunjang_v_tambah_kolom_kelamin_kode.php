<?php

use yii\db\Migration;

/**
 * Class m231208_043103_gls_infopasienpenunjang_v_tambah_kolom_kelamin_kode
 */
class m231208_043103_gls_infopasienpenunjang_v_tambah_kolom_kelamin_kode extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopasienpenunjang_v");
        $infopasienpenunjang_v = file_get_contents(__DIR__ . '/definitions/infopasienpenunjang_v.sql');
        $this->execute($infopasienpenunjang_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231208_043103_gls_infopasienpenunjang_v_tambah_kolom_kelamin_kode cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231208_043103_gls_infopasienpenunjang_v_tambah_kolom_kelamin_kode cannot be reverted.\n";

        return false;
    }
    */
}
