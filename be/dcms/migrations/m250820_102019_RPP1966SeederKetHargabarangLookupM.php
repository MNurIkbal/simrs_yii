<?php

use yii\db\Migration;

/**
 * Class m250820_102019_RPP1966SeederKetHargabarangLookupM
 */
class m250820_102019_RPP1966SeederKetHargabarangLookupM extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookup_m WHERE lookup_type = 'ket_hargabarang'");

        $this->execute("
            INSERT INTO lookup_m (lookup_type,lookup_name,lookup_value,lookup_urutan,lookup_kode,additional_data,created_date,created_by,modified_count,last_modified_date,last_modified_by,is_deleted,is_active,deleted_date,deleted_by) VALUES
                ('ket_hargabarang','Penerimaan Barang Manual','Penerimaan Barang',NULL,NULL,NULL,'2020-03-02 00:00:00.000',NULL,NULL,NULL,NULL,false,true,NULL,NULL),
                ('ket_hargabarang','Adjusment Barang','Adjusment Barang',NULL,NULL,NULL,'2020-03-02 00:00:00.000',NULL,NULL,NULL,NULL,false,true,NULL,NULL),
                ('ket_hargabarang','Perubahan Manual','Perubahan Manual',NULL,NULL,NULL,'2020-03-02 00:00:00.000',NULL,NULL,NULL,NULL,false,true,NULL,NULL),
                ('ket_hargabarang','Penerimaan Barang Supplier','Penerimaan Barang Supplier',NULL,NULL,NULL,'2020-03-02 00:00:00.000',NULL,NULL,NULL,NULL,false,true,NULL,NULL);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250820_102019_RPP1966SeederKetHargabarangLookupM cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250820_102019_RPP1966SeederKetHargabarangLookupM cannot be reverted.\n";

        return false;
    }
    */
}
