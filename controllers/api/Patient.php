<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';
use Restserver\Libraries\REST_Controller;

class Patient extends REST_Controller {

    function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->database();
        $this->load->model('M_patient','pat');
    }
    
    function listpatient_post() 
    {   
        $userid=$this->post('userid');
        $secretkey=$this->post('secretkey');

        $postdata = $this->pat->list_patient($userid,$secretkey);
        if($postdata['ResponseCode'] == '00')
        {
            $this->response($postdata, 200);
        }else{
            $this->response($postdata);
        }
    }

    function currentpatient_post() 
    {   
        $userid=$this->post('userid');
        $secretkey=$this->post('secretkey');

        $postdata = $this->pat->current_patient($userid,$secretkey);
        if($postdata['ResponseCode'] == '00')
        {
            $this->response($postdata, 200);
        }else{
            $this->response($postdata);
        }
    }

    function addprescription_post(){
        $item_prescription=$this->post('item_prescription');
        $patient_id=$this->post('patient_id');
        $userid=$this->post('userid');
        $secretkey=$this->post('secretkey');

        $postdata = $this->pat->add_prescription($item_prescription,$patient_id,$userid,$secretkey);
        if($postdata['ResponseCode'] == '00')
        {
            $this->response($postdata, 200);
        }else{
            $this->response($postdata);
        }
    }

    function listprescription_post(){
        $patient_id=$this->post('patient_id');
        $userid=$this->post('userid');
        $secretkey=$this->post('secretkey');
        
        $postdata = $this->pat->list_prescription($patient_id,$userid,$secretkey);
        if($postdata['ResponseCode'] == '00')
        {
            $this->response($postdata, 200);
        }else{
            $this->response($postdata);
        }
    }

    function addlaboratory_post(){
        $item_laboratory=$this->post('item_laboratory');
        $patient_id=$this->post('patient_id');
        $userid=$this->post('userid');
        $secretkey=$this->post('secretkey');

        $postdata = $this->pat->add_laboratory($item_laboratory,$patient_id,$userid,$secretkey);
        if($postdata['ResponseCode'] == '00')
        {
            $this->response($postdata, 200);
        }else{
            $this->response($postdata);
        }
    }

    function listlaboratory_post(){
        $patient_id=$this->post('patient_id');
        $userid=$this->post('userid');
        $secretkey=$this->post('secretkey');
        
        $postdata = $this->pat->list_laboratory($patient_id,$userid,$secretkey);
        if($postdata['ResponseCode'] == '00')
        {
            $this->response($postdata, 200);
        }else{
            $this->response($postdata);
        }
    }

    function addradiology_post(){
        $item_radiology=$this->post('item_radiology');
        $patient_id=$this->post('patient_id');
        $userid=$this->post('userid');
        $secretkey=$this->post('secretkey');

        $postdata = $this->pat->add_radiology($item_radiology,$patient_id,$userid,$secretkey);
        if($postdata['ResponseCode'] == '00')
        {
            $this->response($postdata, 200);
        }else{
            $this->response($postdata);
        }
    }

    function listradiology_post(){
        $patient_id=$this->post('patient_id');
        $userid=$this->post('userid');
        $secretkey=$this->post('secretkey');
        
        $postdata = $this->pat->list_radiology($patient_id,$userid,$secretkey);
        if($postdata['ResponseCode'] == '00')
        {
            $this->response($postdata, 200);
        }else{
            $this->response($postdata);
        }
    }

    function testradiology_post() 
    {   
        $userid=$this->post('userid');
        $patientid=$this->post('patientid');
        $notes=$this->post('notes');
        $secretkey=$this->post('secretkey');

        $postdata = $this->pat->test_radiology($userid,$patientid,$notes,$secretkey);
        if($postdata['ResponseCode'] == '00')
        {
            $this->response($postdata, 200);
        }else{
            $this->response($postdata);
        }
    }

    function testlaboratorium_post() 
    {   
        $userid=$this->post('userid');
        $patientid=$this->post('patientid');
        $notes=$this->post('notes');
        $secretkey=$this->post('secretkey');

        $postdata = $this->pat->test_laboratorium($userid,$patientid,$notes,$secretkey);
        if($postdata['ResponseCode'] == '00')
        {
            $this->response($postdata, 200);
        }else{
            $this->response($postdata);
        }
    }

    
    
}
?>