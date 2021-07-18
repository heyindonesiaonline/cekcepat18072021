<?php

class M_patient extends CI_Model{


    public function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->secretkey_server = $this->config->item('secretkey_server');
        $this->base_url = $this->config->item('base_url');
        $this->load->model('M_base','base');
    }


    function list_patient($userid,$secretkey){
        
        //cek signature

        if($secretkey != $this->secretkey_server){
            return array(
                'Status' => 'Failed',
                'Message' => 'Invalid Token',
                'ResponseCode' => '01' 
            );
        }
        
        $sql_login = "SELECT * FROM staff WHERE id = '$userid' AND is_active = '1' ";
        $exec_login = $this->db->query($sql_login)->row();

        if (isset($exec_login)) {

                $get_patient = $this->db->query("SELECT * FROM patients")->result();
                return array('Status'=>'Success',
                        'Message'=>'Success List Patient',
                        'Data' => $get_patient,
                        'ResponseCode'=>'00'
                );

            
        }else{
            return array('Status'=>'Failed',
                    'Message'=>'User not found',
                    'ResponseCode'=>'03'
            );
        }
        

    }

    function current_patient($userid,$secretkey){
        date_default_timezone_set('Asia/Jakarta');
        //cek signature

        if($secretkey != $this->secretkey_server){
            return array(
                'Status' => 'Failed',
                'Message' => 'Invalid Token',
                'ResponseCode' => '01' 
            );
        }
        
        $sql_login = "SELECT * FROM staff WHERE id = '$userid' AND is_active = '1' ";
        $exec_login = $this->db->query($sql_login)->row();

        if (isset($exec_login)) {

                //cek appointment today
                $date_now = date("Y-m-d ");
                $time_now = date("h:i:s");
                $sql_schedule = "SELECT id,start_time,end_time FROM schedule sch WHERE sch.end_time >= '$time_now' AND sch.start_time <= '$time_now' AND userid = '$userid' AND status = 1 ";
                $get_schedule = $this->db->query($sql_schedule)->row();
                if (isset($get_schedule)) {
                    $start_time = $get_schedule->start_time;
                    $end_time = $get_schedule->end_time;

                    $datetime_start = $date_now.' '.$start_time;
                    $datetime_end = $date_now.' '.$end_time;
                    $sql_appointment = "SELECT id,patient_name,gender,email,mobileno,message,0 AS age,0 AS dob, 0 AS height FROM appointment WHERE date >= '$datetime_start' AND date <= '$datetime_end' AND doctor = '$userid' ORDER BY id ASC LIMIT 1 ";
                    $get_appointment = $this->db->query($sql_appointment)->row();

                    if (isset($get_appointment)) {
                        return array('Status'=>'Success',
                                'Message'=>'Success Data Patient',
                                'Data' => array(
                                    'id' => $get_appointment->id,
                                    'patient_name' => $get_appointment->patient_name,
                                    'gender' => $get_appointment->gender,
                                    'email' => $get_appointment->email,
                                    'mobileno' => $get_appointment->mobileno,
                                    'message' => $get_appointment->message,
                                    'age' => $get_appointment->age,
                                    'dob' => $get_appointment->dob,
                                    'height' => $get_appointment->height 
                                ),
                                'ResponseCode'=>'00'
                        );
                    }else{
                        return array(
                            'Status' => 'Failed',
                            'Message' => 'Not Found Data',
                            'ResponseCode' => '02 '.$sql_appointment 
                        );
                    }
                }else{
                    return array(
                        'Status' => 'Failed',
                        'Message' => 'Not Found Data',
                        'ResponseCode' => '02' 
                    );
                } 
        }else{
            return array('Status'=>'Failed',
                    'Message'=>'User not found',
                    'ResponseCode'=>'03'
            );
        }
        

    }

    //add prescription
    function add_prescription($item_prescription,$patient_id,$userid,$secretkey){
        

        //global declare
        $create_date = date("Y-m-d H:i:s");
        $uniqueid = 'PRS'.rand(10000,99999);
        //cek signature

        if($secretkey != $this->secretkey_server){
            return array(
                'Status' => 'Failed',
                'Message' => 'Invalid Token '.$secretkey,
                'ResponseCode' => '01' 
            );
        }
        
        $sql_login = "SELECT * FROM staff WHERE id = '$userid' AND is_active = '1' ";
        $exec_login = $this->db->query($sql_login)->row();
        if (isset($exec_login)) {
        foreach ($item_prescription as $key => $value) {
            $data = array(
                'opd_id' => 0,
                'visit_id' => 0, 
                'medicine_category_id' => 0,
                'medicine' => $value['medicine'],
                'dosage' => $value['dosage'],
                'instruction' => $value['instruction'],
                'quantity' => $value['quantity'],
                'isrepeat' => $value['isrepeat'],
                'times' => $value['times'],
                'patient_id' => $patient_id,
                'doctor_id' => $userid,
                'uniqueid' => $uniqueid,
                'create_date' => $create_date
            );
            $this->db->insert('prescription',$data);
        }
            return array('Status'=>'Success',
                        'Message'=>'Success Add Prescriptoin',
                        'ResponseCode'=>'00'
                );
        }else{
            return array(
                'Status' => 'Failed',
                'Message' => 'Not Found Data',
                'ResponseCode' => '02' 
            );
        } 
        
        
        

    }


    //list prescription
    function list_prescription($patient_id,$userid,$secretkey){
        
        //cek signature

        if($secretkey != $this->secretkey_server){
            return array(
                'Status' => 'Failed',
                'Message' => 'Invalid Token',
                'ResponseCode' => '01' 
            );
        }
        
        $sql_login = "SELECT * FROM staff WHERE id = '$userid' AND is_active = '1' ";
        $exec_login = $this->db->query($sql_login)->row();

        if (isset($exec_login)) {

                $get_prescription = $this->db->query("SELECT * FROM prescription WHERE patient_id = '$patient_id' AND doctor_id = '$userid' ")->result();
                return array('Status'=>'Success',
                        'Message'=>'Success List Prescriptoin',
                        'Data' => $get_prescription,
                        'ResponseCode'=>'00'
                );

            
        }else{
            return array('Status'=>'Failed',
                    'Message'=>'User not found',
                    'ResponseCode'=>'03'
            );
        }
        

    }


    function add_laboratory($item_lab,$patient_id,$userid,$secretkey){
        

        //global declare
        $create_date = date("Y-m-d H:i:s");
        $uniqueid = 'PRS'.rand(10000,99999);
        //cek signature

        if($secretkey != $this->secretkey_server){
            return array(
                'Status' => 'Failed',
                'Message' => 'Invalid Token '.$secretkey,
                'ResponseCode' => '01' 
            );
        }
        
        $sql_login = "SELECT * FROM staff WHERE id = '$userid' AND is_active = '1' ";
        $exec_login = $this->db->query($sql_login)->row();
        if (isset($exec_login)) {
        foreach ($item_lab as $key => $value) {
            $data = array(
                'test_name' => $value['lab_name'],
                'short_name' => $value['lab_name'], 
                'test_type' => $value['note'],
                'pathology_category_id' => $value['lab_id'],
                'unit' => '',
                'sub_category' => '',
                'report_days' => '',
                'method' => '',
                'charge_id' => '',
                'patient_id' => $patient_id,
                'requested_at' => '',
                'confirmed_at' => '',
                'finished_at' => '',
                'status' => 'requested',
                'canceled_at' => '',
                'pathoable_id' => '',
                'pathoable_type' => '',
                'created_at' => $create_date
            );
            $this->db->insert('pathology',$data);
        }
            return array('Status'=>'Success',
                        'Message'=>'Success Add Laboratory',
                        'ResponseCode'=>'00'
                );
        }else{
            return array(
                'Status' => 'Failed',
                'Message' => 'Not Found Data',
                'ResponseCode' => '02' 
            );
        } 
        
        
        

    }

    //list laboratory
    function list_laboratory($patient_id,$userid,$secretkey){
        
        //cek signature

        if($secretkey != $this->secretkey_server){
            return array(
                'Status' => 'Failed',
                'Message' => 'Invalid Token',
                'ResponseCode' => '01' 
            );
        }
        
        $sql_login = "SELECT * FROM staff WHERE id = '$userid' AND is_active = '1' ";
        $exec_login = $this->db->query($sql_login)->row();

        if (isset($exec_login)) {

                $get_lab = $this->db->query("SELECT test_name AS lab_name,pathology_category_id AS lab_id,test_type AS note FROM pathology WHERE patient_id = '$patient_id'")->result();
                return array('Status'=>'Success',
                        'Message'=>'Success List Laboratory',
                        'Data' => $get_lab,
                        'ResponseCode'=>'00'
                );

            
        }else{
            return array('Status'=>'Failed',
                    'Message'=>'User not found',
                    'ResponseCode'=>'03'
            );
        }
        

    }


    function add_radiology($item_radiology,$patient_id,$userid,$secretkey){
        

        //global declare
        $create_date = date("Y-m-d H:i:s");
        $uniqueid = 'PRS'.rand(10000,99999);
        //cek signature

        if($secretkey != $this->secretkey_server){
            return array(
                'Status' => 'Failed',
                'Message' => 'Invalid Token '.$secretkey,
                'ResponseCode' => '01' 
            );
        }
        
        $sql_login = "SELECT * FROM staff WHERE id = '$userid' AND is_active = '1' ";
        $exec_login = $this->db->query($sql_login)->row();
        if (isset($exec_login)) {
        foreach ($item_radiology as $key => $value) {
            $data = array(
                'test_name' => $value['radiology_name'],
                'short_name' => $value['radiology_name'], 
                'test_type' => $value['note'],
                'radiology_category_id' => $value['radiology_id'],
                'radiology_parameter_id' => '',
                'sub_category' => '',
                'report_days' => '',
                'charge_id' => '',
                'patient_id' => $patient_id,
                'created_at' => $create_date,
                'radioable_id' => '',
                'radioable_type' => '',
                'status' => 'requested',
                'requested_at' => '',
                'confirmed_at' => '',
                'finished_at' => '',
                'discharged' => ''
                
            );
            $this->db->insert('radio',$data);
        }
            return array('Status'=>'Success',
                        'Message'=>'Success Add Radiology',
                        'ResponseCode'=>'00'
                );
        }else{
            return array(
                'Status' => 'Failed',
                'Message' => 'Not Found Data',
                'ResponseCode' => '02' 
            );
        } 
        
    }

    function list_radiology($patient_id,$userid,$secretkey){
        
        //cek signature

        if($secretkey != $this->secretkey_server){
            return array(
                'Status' => 'Failed',
                'Message' => 'Invalid Token',
                'ResponseCode' => '01' 
            );
        }
        
        $sql_login = "SELECT * FROM staff WHERE id = '$userid' AND is_active = '1' ";
        $exec_login = $this->db->query($sql_login)->row();

        if (isset($exec_login)) {

                $get_radiology = $this->db->query("SELECT test_name AS radiology_name,radiology_category_id AS radiology_id,test_type AS note FROM radio WHERE patient_id = '$patient_id'")->result();
                return array('Status'=>'Success',
                        'Message'=>'Success List Radiology',
                        'Data' => $get_radiology,
                        'ResponseCode'=>'00'
                );

            
        }else{
            return array('Status'=>'Failed',
                    'Message'=>'User not found',
                    'ResponseCode'=>'03'
            );
        }
        

    }



    
   



}

?>