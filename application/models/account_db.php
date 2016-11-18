<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account_Db extends CI_Model{


    public function getMoneyBalance($sessionName){ //retreive variable from controller and select by usersession
         
        $query=$this->db->query("SELECT macc_balance FROM apdb_moneyacc WHERE macc_username=" . "'" . $sessionName . "'");
		return $query->result();
        if ($query->num_rows() == 1) {
            return true;
        } else {
            return false;
        }
      
    }
    
    public function getAllGold($sessionName){
        
        $query=$this->db->query("SELECT SUM(gacc_weight),COUNT(gacc_id) FROM apdb_goldacc WHERE gacc_username=" . "'" . $sessionName . "'");
        return $query->result();
        if ($query->num_rows() >= 1) {
            return true;
        } else {
            return false;
        }
        
    }
        
 
    public function getAllSilver($sessionName){
        

        $query=$this->db->query("SELECT SUM(sacc_weight),COUNT(sacc_id) FROM apdb_silveracc WHERE sacc_username=" . "'" . $sessionName . "'");
        return $query->result();
        if ($query->num_rows() >= 1) {
            return true;
        } else {
            return false;
        }
        
    }

    public function getGoldList($sessionName) {


        $query=$this->db->query("SELECT * FROM apdb_goldacc WHERE gacc_username=" . "'" . $sessionName . "'");
        return $query->result();
        if ($query->num_rows() >= 1) {
            return true;
        } else {
            return false;
        }

    }

    public function getSilverList($sessionName) {

        $query=$this->db->query("SELECT * FROM apdb_silveracc WHERE sacc_username=" . "'" . $sessionName . "'");
        return $query->result();
        if ($query->num_rows() >= 1) {
            return true;
        } else {
            return false;
        }

    }

    public function getPinCode($sessionName) {

        $query=$this->db->query("SELECT members_pin_no FROM apdb_members WHERE members_username=" . "'" . $sessionName . "'");
        return $query->result();
        if ($query->num_rows() >= 1) {
            return true;
        } else {
            return false;
        }

    }





    }
    


