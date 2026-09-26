<?php
namespace app\components\object;

/**
 * 
 */
class FreezeBillingObject extends DocoBaseObject
{
    private $invoice_id;
    private $invoice_date; 
    private $invoice_due_date; 
    private $invoice_number;
    private $invoice_total;
    private $payer_id;
    private $payer_code; 
    private $payer_name; 
    private $payer_sync_id_api; 
    private $create_uid; 
    private $create_by; 
    private $create_date; 
    private $write_uid;
    private $write_by; 
    private $write_date; 
    private $details;
    private $pendaftaran_id;
    private $invoice_status;


    /**
     * Get the value of invoice_id
     */ 
    public function getInvoice_id()
    {
        return $this->invoice_id;
    }

    /**
     * Set the value of invoice_id
     *
     * @return  self
     */ 
    public function setInvoice_id($invoice_id)
    {
        $this->invoice_id = $invoice_id;

        return $this;
    }

    /**
     * Get the value of invoice_date
     */ 
    public function getInvoice_date()
    {
        return $this->invoice_date;
    }

    /**
     * Set the value of invoice_date
     *
     * @return  self
     */ 
    public function setInvoice_date($invoice_date)
    {
        $this->invoice_date = $invoice_date;

        return $this;
    }

    /**
     * Get the value of invoice_due_date
     */ 
    public function getInvoice_due_date()
    {
        return $this->invoice_due_date;
    }

    /**
     * Set the value of invoice_due_date
     *
     * @return  self
     */ 
    public function setInvoice_due_date($invoice_due_date)
    {
        $this->invoice_due_date = $invoice_due_date;

        return $this;
    }

    /**
     * Get the value of invoice_number
     */ 
    public function getInvoice_number()
    {
        return $this->invoice_number;
    }

    /**
     * Set the value of invoice_number
     *
     * @return  self
     */ 
    public function setInvoice_number($invoice_number)
    {
        $this->invoice_number = $invoice_number;

        return $this;
    }

    /**
     * Get the value of invoice_total
     */ 
    public function getInvoice_total()
    {
        return $this->invoice_total;
    }

    /**
     * Set the value of invoice_total
     *
     * @return  self
     */ 
    public function setInvoice_total($invoice_total)
    {
        $this->invoice_total = $invoice_total;

        return $this;
    }

    /**
     * Get the value of payer_id
     */ 
    public function getPayer_id()
    {
        return $this->payer_id;
    }

    /**
     * Set the value of payer_id
     *
     * @return  self
     */ 
    public function setPayer_id($payer_id)
    {
        $this->payer_id = $payer_id;

        return $this;
    }

    /**
     * Get the value of payer_code
     */ 
    public function getPayer_code()
    {
        return $this->payer_code;
    }

    /**
     * Set the value of payer_code
     *
     * @return  self
     */ 
    public function setPayer_code($payer_code)
    {
        $this->payer_code = $payer_code;

        return $this;
    }

    /**
     * Get the value of payer_name
     */ 
    public function getPayer_name()
    {
        return $this->payer_name;
    }

    /**
     * Set the value of payer_name
     *
     * @return  self
     */ 
    public function setPayer_name($payer_name)
    {
        $this->payer_name = $payer_name;

        return $this;
    }

    /**
     * Get the value of payer_sync_id_api
     */ 
    public function getPayer_sync_id_api()
    {
        return $this->payer_sync_id_api;
    }

    /**
     * Set the value of payer_sync_id_api
     *
     * @return  self
     */ 
    public function setPayer_sync_id_api($payer_sync_id_api)
    {
        $this->payer_sync_id_api = $payer_sync_id_api;

        return $this;
    }

    /**
     * Get the value of create_uid
     */ 
    public function getCreate_uid()
    {
        return $this->create_uid;
    }

    /**
     * Set the value of create_uid
     *
     * @return  self
     */ 
    public function setCreate_uid($create_uid)
    {
        $this->create_uid = $create_uid;

        return $this;
    }

    /**
     * Get the value of create_by
     */ 
    public function getCreate_by()
    {
        return $this->create_by;
    }

    /**
     * Set the value of create_by
     *
     * @return  self
     */ 
    public function setCreate_by($create_by)
    {
        $this->create_by = $create_by;

        return $this;
    }

    /**
     * Get the value of create_date
     */ 
    public function getCreate_date()
    {
        return $this->create_date;
    }

    /**
     * Set the value of create_date
     *
     * @return  self
     */ 
    public function setCreate_date($create_date)
    {
        $this->create_date = $create_date;

        return $this;
    }

    /**
     * Get the value of write_uid
     */ 
    public function getWrite_uid()
    {
        return $this->write_uid;
    }

    /**
     * Set the value of write_uid
     *
     * @return  self
     */ 
    public function setWrite_uid($write_uid)
    {
        $this->write_uid = $write_uid;

        return $this;
    }

    /**
     * Get the value of write_by
     */ 
    public function getWrite_by()
    {
        return $this->write_by;
    }

    /**
     * Set the value of write_by
     *
     * @return  self
     */ 
    public function setWrite_by($write_by)
    {
        $this->write_by = $write_by;

        return $this;
    }

    /**
     * Get the value of write_date
     */ 
    public function getWrite_date()
    {
        return $this->write_date;
    }

    /**
     * Set the value of write_date
     *
     * @return  self
     */ 
    public function setWrite_date($write_date)
    {
        $this->write_date = $write_date;

        return $this;
    }


    /**
     * Get the value of details
     */ 
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Set the value of details
     *
     * @return  self
     */ 
    public function setDetails($details)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get the value of pendaftaran_id
     */ 
    public function getPendaftaran_id()
    {
        return $this->pendaftaran_id;
    }

    /**
     * Set the value of pendaftaran_id
     *
     * @return  self
     */ 
    public function setPendaftaran_id($pendaftaran_id)
    {
        $this->pendaftaran_id = $pendaftaran_id;

        return $this;
    }

    /**
     * Get the value of invoice_status
     */ 
    public function getInvoice_status()
    {
        return $this->invoice_status;
    }

    /**
     * Set the value of invoice_status
     *
     * @return  self
     */ 
    public function setInvoice_status($invoice_status)
    {
        $this->invoice_status = $invoice_status;

        return $this;
    }



    public function buildArray(){
        return [
            'invoice_id' => $this->invoice_id,
            'invoice_date' => $this->invoice_date,
            'invoice_due_date' => $this->invoice_due_date,
            'invoice_number' => $this->invoice_number,
            'invoice_total' => $this->invoice_total,
            'payer_id' => $this->payer_id,
            'payer_code' =>(string) $this->payer_code,
            'payer_name' => $this->payer_name,
            'payer_sync_id_api' => $this->payer_sync_id_api,
            'create_uid' => $this->create_uid,
            'create_by' => $this->create_by,
            'create_date' => $this->create_date,
            'write_uid' => $this->write_uid,
            'write_by' => $this->write_by,
            'write_date' => $this->write_date,
            'pendaftaran_id' => $this->pendaftaran_id,
            'additional_detail' => $this->details,
            'status' => $this->invoice_status,
        ];
    }

}