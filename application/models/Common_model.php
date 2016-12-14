<?php
class Common_model extends CI_Model {
	//Common Insert Function
	public function commonInsert($tableName,$arrayData){
		$this->db->insert($tableName,$arrayData);
		$insert_id = $this->db->insert_id();
		return  $insert_id;
	}
	//Common Update Function
	public function commonUpdate($tableName,$updateArray,$whereCondition){
		$this->db->where($whereCondition);
		return $this->db->update($tableName,$updateArray);
	}
        
}
?>
