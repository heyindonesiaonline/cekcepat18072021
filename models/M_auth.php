<?php

class M_auth extends CI_Model{


    public function __construct() {
        parent::__construct();
        $this->secretkey_server = $this->config->item('secretkey_server');
        $this->url_redirect = $this->config->item('url_redirect');
        $this->load->library('enc_lib');
        $this->load->model('M_base','base');
    }


    function login($email,$password,$secretkey){
        
        //cek signature

        if($secretkey != $this->secretkey_server){
            return array(
                'Status' => 'Failed',
                'Message' => 'Invalid Token',
                'ResponseCode' => '01' 
            );
        }

        //parameter
        if ($email == NULL || $email == '' || $password == NULL || $password == '') {
            return array(
                'Status' => 'Failed',
                'Message' => 'Invalid Data',
                'ResponseCode' => '02' 
            );
        }

        //check type user

        $login_staff = $this->db->query("SELECT email FROM staff WHERE email = '$email' ")->row();
        $login_patient = $this->db->query("SELECT email from patients WHERE email = '$email' ")->row();


        if (isset($login_staff)) {
            $sql_login = "SELECT id,email,password,local_address,name,specialization FROM staff WHERE email = '$email' AND is_active = '1' ";
            $exec_login = $this->db->query($sql_login)->row();

            if (isset($exec_login)) {

                $get_password = $exec_login->password;

                if (password_verify($password, $get_password)) {


                    $userid = $exec_login->id;

                    //check rate
                    $check_rate = $this->db->query("SELECT id FROM rates WHERE userid = '$userid' AND status = 1  ")->row();
                    if (isset($check_rate)) {
                        $rate = 1;
                    }else{
                        $rate = 0;
                    }
                    //end rate
                    
                    if ($rate == 0) {
                        $data_rate = array(
                            'price_consultant' => 0,
                            'doctor_consultant' => 0, 
                            'bank_name' => NULL,
                            'account_number' => NULL,
                            'account_name' => NULL,
                            'userid' => $userid,
                            'create_date' => date('Y-m-d h:i:s')
                        );
                        $data_execute = $this->db->insert('rates',$data_rate);
                    }

                    return array('Status'=>'Success',
                            'Message'=>'Success Login',
                            'Data' => array(
                                'userid' => $exec_login->id,
                                'name' => $exec_login->name,
                                'email' => $exec_login->email, 
                                'local_address' => $exec_login->local_address,
                                'specialization' => $exec_login->specialization
                            ),
                            'ResponseCode'=>'00'
                    );


                } else {

                    return array('Status'=>'Failed',
                            'Message'=>'Wrong Password',
                            'ResponseCode'=>'03'
                    );

                }

                

                
            }else{
                return array('Status'=>'Failed',
                        'Message'=>'User not found',
                        'ResponseCode'=>'03'
                );
            }
        }else{
            $sql_login = "SELECT usr.id,usr.user_id,usr.username,pt.patient_unique_id,pt.email,pt.patient_name FROM users usr INNER JOIN patients pt ON pt.id = usr.user_id WHERE pt.email = '$email' AND usr.password = '$password' AND usr.is_active = 'yes' ";
            $exec_login = $this->db->query($sql_login)->row();

            if (isset($exec_login)) {

                return array('Status'=>'Success',
                            'Message'=>'Success Login',
                            'Data' => array(
                                'userid' => $exec_login->id,
                                'patient_unique_id' => $exec_login->patient_unique_id,
                                'username' => $exec_login->username,
                                'email' => $exec_login->email, 
                                'patient_name' => $exec_login->patient_name
                            ),
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

    function register($username,$email,$password,$type_account,$secretkey){

        //cek signature

        if($secretkey != $this->secretkey_server){
            return array(
                'Status' => 'Failed',
                'Message' => 'Invalid Token',
                'ResponseCode' => '01' 
            );
        }

        //parameter
        if ($username == NULL || $username == '' || $password == NULL || $password == '' || $email == NULL || $email == '' || $type_account == NULL || $type_account == '') {
            return array(
                'Status' => 'Failed',
                'Message' => 'Invalid Data',
                'ResponseCode' => '02' 
            );
        }

        if ($type_account == 1) {
            $role = 'doctor';
        }elseif ($type_account == 2) {
            $role = 'patient';
            $sql_login = "SELECT id,username,role FROM users WHERE username = '$username'";
            $exec_login = $this->db->query($sql_login)->row();

            if (isset($exec_login)) {
                return array('Status'=>'Failed',
                        'Message'=>'Username or Email was registered, Please change your Username or Email',
                        'ResponseCode'=>'03'
                );
            }else{

                $data_insert = array(
                    'username' => $username,
                    'password' => $password,
                    'childs' => '',
                    'role' =>  $role,
                    'verification_code' => '',
                    'is_active' => 'yes'
                );

                $exec_insert = $this->db->insert('users',$data_insert);
                $insert_id = $this->db->insert_id();
                if ($exec_insert == TRUE) {
                    $this->db->query("UPDATE users SET user_id = '$insert_id' WHERE id = '$insert_id' ");

                    $get_last_patient = $this->db->query("SELECT patient_unique_id FROM `patients` ORDER BY id DESC LIMIT 1")->row();
                    $new_unique_id = $get_last_patient->patient_unique_id + 1;

                    $data_patient = array(
                        'id' => $insert_id,
                        'patient_unique_id' => $new_unique_id,
                        'email' => $email, 
                        'lang_id' => 0,
                        'age' => '',
                        'month' => '',
                        'marital_status' => '',
                        'blood_group' => '',
                        'address' => '',
                        'guardian_email' => '',
                        'is_active' => 'yes',
                        'discharged' => '',
                        'patient_type' => '',
                        'organisation' => '',
                        'known_allergies' => '',
                        'old_patient' => 'No',
                        'disable_at' => '',
                        'note' => '',
                        'is_ipd' => '',
                        'app_key' => ''
                    );
                    $exec_insert = $this->db->insert('patients',$data_patient);

                    return array('Status'=>'Success',
                            'Message'=>'Success Register',
                            'ResponseCode'=>'00'
                    );
                }else{
                    return array('Status'=>'Failed',
                            'Message'=>'User not found',
                            'ResponseCode'=>'03'
                    );
                }

            }
        }else{
            return array(
                'Status' => 'Failed',
                'Message' => 'Invalid Data',
                'ResponseCode' => '02' 
            );
        }



        //

        
        

    }


    function forget_password($email,$secretkey){
        
        //cek signature

        if($secretkey != $this->secretkey_server){
            return array(
                'Status' => 'Failed',
                'Message' => 'Invalid Token',
                'ResponseCode' => '01' 
            );
        }

        //parameter
        if ($email == NULL || $email == '') {
            return array(
                'Status' => 'Failed',
                'Message' => 'Invalid Data',
                'ResponseCode' => '02' 
            );
        }

        
        $check_email = "SELECT id,email FROM staff WHERE email = '$email' AND is_active = '1' ";
        $exec_email = $this->db->query($check_email)->row();
        
        if (isset($exec_email)) {

            $generate_link = $this->url_redirect.'api/auth/'.md5($exec_email->id);
            $subject = "Reset Password";
            $message = "Reset password ".$generate_link;
            $email = $exec_email->email;

            $send_email = $this->base->send_email($subject,$email,$message);

            if ($send_email == TRUE) {
                return array('Status'=>'Success',
                        'Message'=>'Success Forget Password',
                        'ResponseCode'=>'00'
                );
            }else{
                return array('Status'=>'Failed',
                        'Message'=>'Failed',
                        'ResponseCode'=>'03'
                );
            }

            
        }else{
            return array('Status'=>'Failed',
                    'Message'=>'User not found',
                    'ResponseCode'=>'03'
            );
        }
        

    }


    function reset_password($id,$password,$conf_password,$secretkey){
        
        //cek signature

        if($secretkey != $this->secretkey_server){
            return array(
                'Status' => 'Failed',
                'Message' => 'Invalid Token',
                'ResponseCode' => '01' 
            );
        }

        //parameter


        if($password != $conf_password){
            return array(
                'Status' => 'Failed',
                'Message' => 'Wrong Password',
                'ResponseCode' => '01' 
            );
        }

        
        $check_user = "SELECT id FROM staff WHERE md5(id) = '$id' AND is_active = '1' ";
        $exec_user = $this->db->query($check_user)->row();
        
        if (isset($exec_user)) {

            $hash_pass = $this->enc_lib->passHashEnc($password);
            $update_pass = $this->db->query("UPDATE staff SET password = '$hash_pass' WHERE md5(id) = '$id' ");

            if ($update_pass == TRUE ) {
               return array('Status'=>'Success',
                        'Message'=>'Success Reset Password',
                        'ResponseCode'=>'00'
                );
            }else{
                return array('Status'=>'Failed',
                        'Message'=>'Failed',
                        'ResponseCode'=>'03'
                );
            }

            
        }else{
            return array('Status'=>'Failed',
                    'Message'=>'User not found',
                    'ResponseCode'=>'03'
            );
        }
        

    }


    function change_password($userid,$old_password,$password,$conf_password,$secretkey){
        
        //cek signature

        if($secretkey != $this->secretkey_server){
            return array(
                'Status' => 'Failed',
                'Message' => 'Invalid Token',
                'ResponseCode' => '01' 
            );
        }

        //parameter


        if($password != $conf_password){
            return array(
                'Status' => 'Failed',
                'Message' => 'Wrong Password',
                'ResponseCode' => '01' 
            );
        }

        
        $check_user = "SELECT id,password FROM staff WHERE id = '$userid' AND is_active = '1' ";
        $exec_user = $this->db->query($check_user)->row();
        
        if (isset($exec_user)) {


            //checking old password - password
            $new_hash_old_password = $this->enc_lib->passHashEnc($old_password);


            $get_password = $exec_user->password;

            if (password_verify($old_password, $get_password)) {


                $hash_pass = $this->enc_lib->passHashEnc($password);
                $update_pass = $this->db->query("UPDATE staff SET password = '$hash_pass' WHERE id = '$userid' ");

                return array('Status'=>'Success',
                        'Message'=>'Success Change Password',
                        'ResponseCode'=>'00'
                );


            } else {

                return array('Status'=>'Failed',
                        'Message'=>'Wrong Password',
                        'ResponseCode'=>'03'
                );

            }
            //

            
        }else{
            return array('Status'=>'Failed',
                    'Message'=>'User not found',
                    'ResponseCode'=>'03'
            );
        }
        

    }
    
   



}

?>