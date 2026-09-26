<?php

use yii\db\Migration;

/**
 * Class m200114_231328_trigger_pesanmutasiobat
 */
class m200114_231328_trigger_pesanmutasiobat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP TRIGGER if exists trigger_delete_pemesanan ON public.pesanobatdetail_t;');

        $this->execute('DROP TRIGGER if exists trigger_update_pemesanan ON public.pesanobatdetail_t;');

        $this->execute('DROP TRIGGER if exists trigger_delete_mutasi ON public.mutasiobatdetail_t;');

        $this->execute('DROP FUNCTION if exists public.del_pemesanan_stokobat_r();');

        $this->execute("
            CREATE OR REPLACE FUNCTION public.del_pemesanan_stokobat_r()
  RETURNS trigger AS
\$BODY\$

DECLARE
var_ruangan_id INTEGER;
var_obatalkes_id INTEGER;
var_jml_mutasi INTEGER;
var_pesan_obat_id INTEGER;
var_qty_tersedia INTEGER;
var_qty_pesan INTEGER;
var_stokobatr_id INTEGER;
var_qty_tersedia_count INTEGER;
var_qty_pesan_count INTEGER;
vmutasiobatruangan_id INTEGER;
vis_deleted BOOLEAN;

BEGIN
    var_jml_mutasi := NEW.jumlah_mutasi;
    vmutasiobatruangan_id := NEW.mutasiobatruangan_id;
    var_obatalkes_id := NEW.obatalkes_id;
    vis_deleted := NEW.is_deleted;

    IF (vis_deleted IS TRUE)
    THEN
    
        SELECT ruanganasal_id INTO var_ruangan_id
        FROM mutasiobatruangan_t
        WHERE mutasiobatruangan_id = vmutasiobatruangan_id;
        
        SELECT stokobatr_id, qty_tersedia, qty_dipesan 
        INTO var_stokobatr_id, var_qty_tersedia, var_qty_pesan 
        FROM stokobatalkes_r WHERE obatalkes_id = var_obatalkes_id 
        AND ruangan_id = var_ruangan_id;
        
        var_qty_tersedia_count := var_qty_tersedia + var_jml_mutasi;
        var_qty_pesan_count := var_qty_pesan - var_jml_mutasi;
        
        UPDATE stokobatalkes_r 
        SET qty_tersedia = var_qty_tersedia_count, 
                qty_dipesan = var_qty_pesan_count 
        WHERE stokobatr_id = var_stokobatr_id;
    
END IF;

RETURN NEW;
END;

        \$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION public.del_pemesanan_stokobat_r()
  OWNER TO postgres;');

        $this->execute('CREATE TRIGGER trigger_delete_mutasi
                      AFTER UPDATE OF is_deleted
                      ON public.mutasiobatdetail_t
                      FOR EACH ROW
                      EXECUTE PROCEDURE public.del_pemesanan_stokobat_r();
                    ');

        $this->execute('DROP TRIGGER if exists trigger_insert_mutasi ON public.mutasiobatdetail_t;');

        $this->execute('DROP FUNCTION if exists public.update_stokobatalkes_r_transaksi_pemesanan();');

        $this->execute("
            CREATE OR REPLACE FUNCTION public.update_stokobatalkes_r_transaksi_pemesanan()
  RETURNS trigger AS
\$BODY\$

DECLARE
var_ruangan_id INTEGER;
var_obatalkes_id INTEGER;
var_jml_mutasi INTEGER;
var_pesan_obat_id INTEGER;
var_qty_tersedia INTEGER;
var_qty_pesan INTEGER;
var_stokobatr_id INTEGER;
var_qty_tersedia_count INTEGER;
var_qty_pesan_count INTEGER;
vmutasiobatruangan_id INTEGER;

BEGIN
var_jml_mutasi := NEW.jumlah_mutasi;
vmutasiobatruangan_id := NEW.mutasiobatruangan_id;
var_obatalkes_id := NEW.obatalkes_id;
vmutasiobatruangan_id := NEW.mutasiobatruangan_id;

IF (vmutasiobatruangan_id IS NOT NULL)
    THEN
        SELECT ruanganasal_id INTO var_ruangan_id
        FROM mutasiobatruangan_t
        WHERE mutasiobatruangan_id = vmutasiobatruangan_id;
        
        SELECT stokobatr_id, qty_tersedia, qty_dipesan 
        INTO var_stokobatr_id, var_qty_tersedia, var_qty_pesan 
        FROM stokobatalkes_r 
        WHERE obatalkes_id = var_obatalkes_id 
        AND ruangan_id = var_ruangan_id;
        
        var_qty_tersedia_count := var_qty_tersedia - var_jml_mutasi;
        var_qty_pesan_count := var_qty_pesan + var_jml_mutasi;
        
        UPDATE stokobatalkes_r 
        SET qty_tersedia = var_qty_tersedia_count, 
                qty_dipesan = var_qty_pesan_count 
        WHERE stokobatr_id = var_stokobatr_id;
END IF;

RETURN NEW;
END;

        \$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION public.update_stokobatalkes_r_transaksi_pemesanan()
  OWNER TO postgres;');

        $this->execute('CREATE TRIGGER trigger_insert_mutasi
                      BEFORE INSERT
                      ON public.mutasiobatdetail_t
                      FOR EACH ROW
                      EXECUTE PROCEDURE public.update_stokobatalkes_r_transaksi_pemesanan();
                    ');

        $this->execute('DROP TRIGGER if exists trigger_update_mutasi ON public.mutasiobatdetail_t;');

        $this->execute('DROP FUNCTION if exists public.update_mutasi_stokobat_r();');

        $this->execute("
            CREATE OR REPLACE FUNCTION public.update_mutasi_stokobat_r()
  RETURNS trigger AS
\$BODY\$

DECLARE
var_ruangan_id INTEGER;
var_obatalkes_id INTEGER;
var_jml_mutasi_new INTEGER;
var_jml_mutasi_old INTEGER;
var_jml_mutasi INTEGER;
var_pesan_obat_id INTEGER;
var_qty_tersedia INTEGER;
var_qty_pesan INTEGER;
var_stokobatr_id INTEGER;
var_qty_tersedia_count INTEGER;
var_qty_pesan_count INTEGER;
vmutasiobatruangan_id INTEGER;
vis_deleted BOOLEAN;

BEGIN
    var_jml_mutasi_new := NEW.jumlah_mutasi;
    var_jml_mutasi_old := OLD.jumlah_mutasi;
    vmutasiobatruangan_id := NEW.mutasiobatruangan_id;
    var_obatalkes_id := NEW.obatalkes_id;
    vis_deleted := NEW.is_deleted;
    
    IF (var_jml_mutasi_new <> var_jml_mutasi_old)
    THEN
        
        var_jml_mutasi = var_jml_mutasi_old - var_jml_mutasi_new;
        SELECT ruanganasal_id INTO var_ruangan_id
        FROM mutasiobatruangan_t
        WHERE mutasiobatruangan_id = vmutasiobatruangan_id;
        
        SELECT stokobatr_id, qty_tersedia, qty_dipesan 
        INTO var_stokobatr_id, var_qty_tersedia, var_qty_pesan 
        FROM stokobatalkes_r WHERE obatalkes_id = var_obatalkes_id 
        AND ruangan_id = var_ruangan_id;
        
        var_qty_tersedia_count := var_qty_tersedia + var_jml_mutasi;
        var_qty_pesan_count := var_qty_pesan - var_jml_mutasi;
        
        UPDATE stokobatalkes_r 
        SET qty_tersedia = var_qty_tersedia_count, 
                qty_dipesan = var_qty_pesan_count 
        WHERE stokobatr_id = var_stokobatr_id;
    
END IF;

RETURN NEW;
END;

        \$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION public.update_mutasi_stokobat_r()
  OWNER TO postgres;');

        $this->execute('CREATE TRIGGER trigger_update_mutasi
                      AFTER UPDATE OF jumlah_mutasi
                      ON public.mutasiobatdetail_t
                      FOR EACH ROW
                      EXECUTE PROCEDURE public.update_mutasi_stokobat_r();
                    ');

       
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200114_231328_trigger_pesanmutasiobat cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200114_231328_trigger_pesanmutasiobat cannot be reverted.\n";

        return false;
    }
    */
}
