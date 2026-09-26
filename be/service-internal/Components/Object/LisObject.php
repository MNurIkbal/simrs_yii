<?php

namespace Integrasi\Components\Object;

use Integrasi\Components\DocoConstants;

class LisObject extends DocoBaseObject
{

    const CYTO = "A21";

    const UMUM = "A23";

    private $username;
    private $key;
    private $pmrn;
    private $pname;
    private $sex;
    private $birthdt;
    private $address;
    private $notlp;
    private $ordercontrol;
    private $ptype;
    private $regno;
    private $orderlab;
    private $providerid;
    private $providername;
    private $orderdate;
    private $clinicalid;
    private $clinicalname;
    private $bangsalid;
    private $bangsalname;
    private $badid;
    private $badname;
    private $classid;
    private $classname;
    private $cito;
    private $medlegal;
    private $userid;
    private $ordertest;

    /**
     * @return mixed
     */
    public function getusername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     *
     * @return self
     */
    public function setusername($username)
    {
        $this->username = $username;

        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getkey()
    {
        return $this->key;
    }

    /**
     * @param mixed $key
     *
     * @return self
     */
    public function setkey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getpmrn()
    {
        return $this->pmrn;
    }

    /**
     * @param mixed $pmrn
     *
     * @return self
     */
    public function setpmrn($pmrn)
    {
        $this->pmrn = $pmrn;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getPName()
    {
        return $this->pname;
    }

    /**
    * @param mixed $pname
    *
    * @return self
    */
    public function setPName($pname)
    {
        $this->pname = $pname;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getSex()
    {
        return $this->sex;
    }

    /**
    * @param mixed $sex
    *
    * @return self
    */
    public function setSex($sex)
    {
        $this->sex = $sex;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getBirthDt()
    {
        return $this->birthdt;
    }

    /**
    * @param mixed $birthdt
    *
    * @return self
    */
    public function setBirthDt($birthdt)
    {
        $this->birthdt = $birthdt;

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
        $this->address = $address;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getNoTlp()
    {
        return $this->notlp;
    }

    /**
    * @param mixed $notlp
    *
    * @return self
    */
    public function setNoTlp($notlp)
    {
        $this->notlp = $notlp;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getOrderControl()
    {
        return $this->ordercontrol;
    }

    /**
    * @param mixed $ordercontrol
    *
    * @return self
    */
    public function setOrderControl($ordercontrol)
    {
        $this->ordercontrol = $ordercontrol;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getPType()
    {
        return $this->ptype;
    }

    /**
    * @param mixed $ptype
    *
    * @return self
    */
    public function setPType($ptype)
    {
        $this->ptype = $ptype;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getRegNo()
    {
        return $this->regno;
    }

    /**
    * @param mixed $regno
    *
    * @return self
    */
    public function setRegNo($regno)
    {
        $this->regno = $regno;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getOrderLab()
    {
        return $this->orderlab;
    }

    /**
    * @param mixed $orderlab
    *
    * @return self
    */
    public function setOrderLab($orderlab)
    {
        $this->orderlab = $orderlab;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getProviderId()
    {
        return $this->providerid;
    }

    /**
    * @param mixed $providerid
    *
    * @return self
    */
    public function setProviderId($providerid)
    {
        $this->providerid = $providerid;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getProviderName()
    {
        return $this->providername;
    }

    /**
    * @param mixed $providername
    *
    * @return self
    */
    public function setProviderName($providername)
    {
        $this->providername = $providername;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getOrderDate()
    {
        return $this->orderdate;
    }

    /**
    * @param mixed $orderdate
    *
    * @return self
    */
    public function setOrderDate($orderdate)
    {
        $this->orderdate = $orderdate;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getClinicalId()
    {
        return $this->clinicalid;
    }

    /**
    * @param mixed $clinicalid
    *
    * @return self
    */
    public function setClinicalId($clinicalid)
    {
        $this->clinicalid = $clinicalid;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getClinicalName()
    {
        return $this->clinicalname;
    }

    /**
    * @param mixed $clinicalname
    *
    * @return self
    */
    public function setClinicalName($clinicalname)
    {
        $this->clinicalname = $clinicalname;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getBangsalId()
    {
        return $this->bangsalid;
    }

    /**
    * @param mixed $bangsalid
    *
    * @return self
    */
    public function setBangsalId($bangsalid)
    {
        $this->bangsalid = $bangsalid;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getBangsalName()
    {
        return $this->bangsalname;
    }

    /**
    * @param mixed $bangsalname
    *
    * @return self
    */
    public function setBangsalName($bangsalname)
    {
        $this->bangsalname = $bangsalname;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getBadId()
    {
        return $this->badid;
    }

    /**
    * @param mixed $badid
    *
    * @return self
    */
    public function setBadId($badid)
    {
        $this->badid = $badid;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getBadName()
    {
        return $this->badname;
    }

    /**
    * @param mixed $badname
    *
    * @return self
    */
    public function setBadName($badname)
    {
        $this->badname = $badname;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getClassId()
    {
        return $this->classid;
    }

    /**
    * @param mixed $classid
    *
    * @return self
    */
    public function setClassId($classid)
    {
        $this->classid = $classid;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getClassName()
    {
        return $this->classname;
    }

    /**
    * @param mixed $classname
    *
    * @return self
    */
    public function setClassName($classname)
    {
        $this->classname = $classname;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getCito()
    {
        return $this->cito;
    }

    /**
    * @param mixed $cito
    *
    * @return self
    */
    public function setCito($cito)
    {
        $this->cito = $cito;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getMedLegal()
    {
        return $this->medlegal;
    }

    /**
    * @param mixed $medlegal
    *
    * @return self
    */
    public function setMedLegal($medlegal)
    {
        $this->medlegal = $medlegal;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getUserId()
    {
        return $this->userid;
    }

    /**
    * @param mixed $userid
    *
    * @return self
    */
    public function setUserId($userid)
    {
        $this->userid = $userid;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getOrderTest()
    {
        return $this->ordertest;
    }

    /**
    * @param mixed $ordertest
    *
    * @return self
    */
    public function setOrderTest($ordertest)
    {
        $this->ordertest = $ordertest;

        return $this;
    }

    private function setNullAttr($value)
    {
        return isset($value) && $value != '' ? $value : '-';
    }

    private function setZeroAttr($value)
    {
        return isset($value) ? $value : 0;
    }

    public function buildArray()
    {
        return [
            'order' => [
              'msh' => [
                'product' => 'SOFTMEDIX LIS',
                'version' => 'ws.001',
                "user_id" =>  $this->setNullAttr($this->username),
                "key" => $this->setNullAttr($this->key)
              ],
              'pid' => [
                'pmrn' => $this->setNullAttr($this->pmrn),
                'pname' => $this->setNullAttr($this->pname),
                'sex' => $this->setNullAttr($this->sex),
                'birthdt' => $this->setNullAttr($this->birthdt),
                'address' => $this->setNullAttr($this->address),
                'notlp' => $this->setNullAttr($this->notlp),
              ],
              'obr' => [
                'order_control' => $this->setNullAttr($this->ordercontrol),
                'ptype' => $this->setNullAttr($this->ptype),
                'regno' => $this->setNullAttr($this->regno),
                'orderlab' => $this->setNullAttr($this->orderlab),
                'providerid' => $this->setNullAttr($this->providerid),
                'providername' => $this->setNullAttr($this->providername),
                'orderdate' => $this->setNullAttr($this->orderdate),
                'clinicalid' => $this->setNullAttr($this->clinicalid),
                'clinicalname' => $this->setNullAttr($this->clinicalname),
                'bangsalid' => $this->setNullAttr($this->bangsalid),
                'bangsalname' => $this->setNullAttr($this->bangsalname),
                'badid' => $this->setNullAttr($this->badid),
                'badname' => $this->setNullAttr($this->badname),
                'classid' => $this->setNullAttr($this->classid),
                'classname' => $this->setNullAttr($this->classname),
                'cito' => $this->setNullAttr($this->cito),
                'medlegal' => $this->setNullAttr($this->medlegal),
                'userid' => $this->setNullAttr($this->userid),
                'ordertest' => $this->setNullAttr($this->ordertest),
              ]
            ]
        ];
    }

}