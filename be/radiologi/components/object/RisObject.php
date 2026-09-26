<?php
namespace app\components\object;

use Doco\components\DocoConstants;

class RisObject extends DocoBaseObject
{

    const CYTO = "A21";

    const UMUM = "A23";

    private $user_id;

    private $user_name;

    private $patient_id;

    private $case_no;

    private $patient_name;

    private $date_of_birth;

    private $gender;

    private $address;

    private $postal_code;

    private $country = "ID";

    private $phone_no;

    private $patient_class;

    private $admission_type;

    private $charge_price;

    private $location_poc;

    private $location_room;

    private $is_vip;

    private $financial_class;

    private $no_masukpenunjang;

    private $quantity;

    private $is_mcu;

    private $payer_code;

    private $packet_name;

    private $admit_time;

    private $admit_reason;

    private $clinical_info;

    private $patient_risk;

    private $bill_no;

    private $procedure_code;

    private $procedure_name;

    private $order_no;

    private $filler_order;

    private $ref_doctor_id;

    private $ref_doctor_name;

    private $priority;

    private $group_no;

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     *
     * @return self
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->user_name;
    }

    /**
     * @param mixed $user_name
     *
     * @return self
     */
    public function setUserName($user_name)
    {
        $this->user_name = $user_name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPatientId()
    {
        return $this->patient_id;
    }

    /**
     * @param mixed $patient_id
     *
     * @return self
     */
    public function setPatientId($patient_id)
    {
        $this->patient_id = (string) $patient_id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCaseNo()
    {
        return $this->case_no;
    }

    /**
     * @param mixed $case_no
     *
     * @return self
     */
    public function setCaseNo($case_no)
    {
        $this->case_no = (string) $case_no;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPatientName()
    {
        return $this->patient_name;
    }

    /**
     * @param mixed $patient_name
     *
     * @return self
     */
    public function setPatientName($patient_name)
    {
        $this->patient_name = (string) $patient_name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateOfBirth()
    {
        return $this->date_of_birth;
    }

    /**
     * @param mixed $date_of_birth
     *
     * @return self
     */
    public function setDateOfBirth($date_of_birth)
    {
        $this->date_of_birth = !empty($date_of_birth) ? date('Y-m-d', strtotime($date_of_birth)) : '';

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param mixed $gender
     *
     * @return self
     */
    public function setGender($gender)
    {
        $this->gender = ($gender == DocoConstants::VAR_LK) ? 'M' : 'F';

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     *
     * @return self
     */
    public function setAddress($address)
    {
        $this->address = (string) $address;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostalCode()
    {
        return $this->postal_code;
    }

    /**
     * @param mixed $postal_code
     *
     * @return self
     */
    public function setPostalCode($postal_code)
    {
        $this->postal_code = $postal_code;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     *
     * @return self
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhoneNo()
    {
        return $this->phone_no;
    }

    /**
     * @param mixed $phone_no
     *
     * @return self
     */
    public function setPhoneNo($phone_no)
    {
        $this->phone_no = $phone_no;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPatientClass()
    {
        return $this->patient_class;
    }

    /**
     * @param mixed $patient_class
     *
     * @return self
     */
    public function setPatientClass($patient_class)
    {
        $this->patient_class = $patient_class;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAdmissionType()
    {
        return $this->admission_type;
    }

    /**
     * @param mixed $admission_type
     *
     * @return self
     */
    public function setAdmissionType($admission_type)
    {
        $this->admission_type = $admission_type;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getChargePrice()
    {
        return $this->charge_price;
    }

    /**
     * @param mixed $charge_price
     *
     * @return self
     */
    public function setChargePrice($charge_price)
    {
        $this->charge_price = (string) $charge_price;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocationPoc()
    {
        return $this->location_poc;
    }

    /**
     * @param mixed $location_poc
     *
     * @return self
     */
    public function setLocationPoc($location_poc)
    {
        $this->location_poc = (string) $location_poc;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocationRoom()
    {
        return $this->location_room;
    }

    /**
     * @param mixed $location_room
     *
     * @return self
     */
    public function setLocationRoom($location_room)
    {
        $this->location_room = (string) $location_room;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsVip()
    {
        return $this->is_vip;
    }

    /**
     * @param mixed $is_vip
     *
     * @return self
     */
    public function setIsVip($is_vip)
    {
        $this->is_vip = $is_vip;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFinancialClass()
    {
        return $this->financial_class;
    }

    /**
     * @param mixed $financial_class
     *
     * @return self
     */
    public function setFinancialClass($financial_class)
    {
        $this->financial_class = (string) $financial_class;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNoMasukpenunjang()
    {
        return $this->no_masukpenunjang;
    }

    /**
     * @param mixed $no_masukpenunjang
     *
     * @return self
     */
    public function setNoMasukpenunjang($no_masukpenunjang)
    {
        $this->no_masukpenunjang = (string) $no_masukpenunjang;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param mixed $quantity
     *
     * @return self
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsMcu()
    {
        return $this->is_mcu;
    }

    /**
     * @param mixed $is_mcu
     *
     * @return self
     */
    public function setIsMcu($is_mcu)
    {
        $this->is_mcu = $is_mcu;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPayerCode()
    {
        return $this->payer_code;
    }

    /**
     * @param mixed $payer_code
     *
     * @return self
     */
    public function setPayerCode($payer_code)
    {
        $this->payer_code = (string) $payer_code;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPacketName()
    {
        return $this->packet_name;
    }

    /**
     * @param mixed $packet_name
     *
     * @return self
     */
    public function setPacketName($packet_name)
    {
        $this->packet_name = $packet_name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAdmitTime()
    {
        return $this->admit_time;
    }

    /**
     * @param mixed $admit_time
     *
     * @return self
     */
    public function setAdmitTime($admit_time)
    {
        $this->admit_time = !empty($admit_time) ? date('Y-m-d H:i:s', strtotime($admit_time)) : '';

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAdmitReason()
    {
        return $this->admit_reason;
    }

    /**
     * @param mixed $admit_reason
     *
     * @return self
     */
    public function setAdmitReason($admit_reason)
    {
        $this->admit_reason = $admit_reason;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getClinicalInfo()
    {
        return $this->clinical_info;
    }

    /**
     * @param mixed $clinical_info
     *
     * @return self
     */
    public function setClinicalInfo($clinical_info)
    {
        $this->clinical_info = $clinical_info;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPatientRisk()
    {
        return $this->patient_risk;
    }

    /**
     * @param mixed $patient_risk
     *
     * @return self
     */
    public function setPatientRisk($patient_risk)
    {
        $this->patient_risk = $patient_risk;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBillNo()
    {
        return $this->bill_no;
    }

    /**
     * @param mixed $bill_no
     *
     * @return self
     */
    public function setBillNo($bill_no)
    {
        $this->bill_no = $bill_no;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProcedureCode()
    {
        return $this->procedure_code;
    }

    /**
     * @param mixed $procedure_code
     *
     * @return self
     */
    public function setProcedureCode($procedure_code)
    {
        $this->procedure_code = $procedure_code;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProcedureName()
    {
        return $this->procedure_name;
    }

    /**
     * @param mixed $procedure_name
     *
     * @return self
     */
    public function setProcedureName($procedure_name)
    {
        $this->procedure_name = $procedure_name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrderNo()
    {
        return $this->order_no;
    }

    /**
     * @param mixed $order_no
     *
     * @return self
     */
    public function setOrderNo($order_no)
    {
        $this->order_no = $order_no;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFillerOrder()
    {
        return $this->filler_order;
    }

    /**
     * @param mixed $filler_order
     *
     * @return self
     */
    public function setFillerOrder($filler_order)
    {
        $this->filler_order = $filler_order;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRefDoctorId()
    {
        return $this->ref_doctor_id;
    }

    /**
     * @param mixed $ref_doctor_id
     *
     * @return self
     */
    public function setRefDoctorId($ref_doctor_id)
    {
        $this->ref_doctor_id = $ref_doctor_id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRefDoctorName()
    {
        return $this->ref_doctor_name;
    }

    /**
     * @param mixed $ref_doctor_name
     *
     * @return self
     */
    public function setRefDoctorName($ref_doctor_name)
    {
        $this->ref_doctor_name = $ref_doctor_name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param mixed $priority
     *
     * @return self
     */
    public function setPriority($priority)
    {
        $this->priority = $priority ? self::CYTO : self::UMUM;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGroupNo()
    {
        return $this->group_no;
    }

    /**
     * @param mixed $group_no
     *
     * @return self
     */
    public function setGroupNo($group_no)
    {
        $this->group_no = (string) $group_no;

        return $this;
    }

    private function setNullAttr($value)
    {
        return isset($value) ? $value : '';
    }

    public function buildArray()
    {
        return [
            'user_id' => $this->setNullAttr($this->user_id),
            'user_name' => $this->setNullAttr($this->user_name),
            'patient_id' => $this->setNullAttr($this->patient_id),
            'case_no' => $this->setNullAttr($this->case_no),
            'patient_name' => $this->setNullAttr($this->patient_name),
            'date_of_birth' => $this->setNullAttr($this->date_of_birth),
            'gender' => $this->setNullAttr($this->gender),
            'address' => $this->setNullAttr($this->address),
            'postal_code' => $this->setNullAttr($this->postal_code),
            'country' => $this->setNullAttr($this->country),
            'phone_no' => $this->setNullAttr($this->phone_no),
            'patient_class' => $this->setNullAttr($this->patient_class),
            'admission_type' => $this->setNullAttr($this->admission_type),
            'charge_price' => $this->setNullAttr($this->charge_price),
            'location_poc' => $this->setNullAttr($this->location_poc),
            'location_room' => $this->setNullAttr($this->location_room),
            'is_vip' => $this->setNullAttr($this->is_vip),
            'financial_class' => $this->setNullAttr($this->financial_class),
            'no_masukpenunjang' => $this->setNullAttr($this->no_masukpenunjang),
            'quantity' => $this->setNullAttr($this->quantity),
            'is_mcu' => $this->setNullAttr($this->is_mcu),
            'payer_code' => $this->setNullAttr($this->payer_code),
            'packet_name' => $this->setNullAttr($this->packet_name),
            'admit_time' => $this->setNullAttr($this->admit_time),
            'admit_reason' => $this->setNullAttr($this->admit_reason),
            'clinical_info' => $this->setNullAttr($this->clinical_info),
            'patient_risk' => $this->setNullAttr($this->patient_risk),
            'bill_no' => $this->setNullAttr($this->bill_no),
            'procedure_code' => $this->setNullAttr($this->procedure_code),
            'procedure_name' => $this->setNullAttr($this->procedure_name),
            'order_no' => $this->setNullAttr($this->order_no),
            'filler_order' => $this->setNullAttr($this->filler_order),
            'ref_doctor_id' => $this->setNullAttr($this->ref_doctor_id),
            'ref_doctor_name' => $this->setNullAttr($this->ref_doctor_name),
            'priority' => $this->setNullAttr($this->priority),
            'group_no' => $this->setNullAttr($this->group_no),
        ];
    }

}