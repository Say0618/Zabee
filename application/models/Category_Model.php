<?php
class Category_Model extends CI_Model
{
	function __construct() 
	{
		parent::__construct();
		$this->load->database("default");
	}
	
	public function getParentCategories($productcnts)
	{
		echo 'asd';die();
		$sql = "SELECT cat.category_id, cat.category_name,cat.category_image FROM ".DBPREFIX."_categories AS cat		
			WHERE cat.display_status = '1' AND cat.parent_category_id = '0'
			ORDER BY cat.category_id
		";
		$result = $this->db->query($sql);
		if($result && $result->num_rows()>0){
			$categoryData = $result->result_array();
			$retVal = array();	
			$cnt = 0;
			foreach($categoryData as $category){
				$retVal[$category["category_id"]] = $category;
				$retVal[$category["category_id"]]["prod_cnt"] = isset($productcnts['category'][$category["category_id"]]["cnt"]) ? $productcnts['category'][$category["category_id"]]["cnt"] : 0;
				
				if($retVal[$category["category_id"]]["prod_cnt"] == "0")
				unset($retVal[$category["category_id"]]);
				$cnt++;
			}
			return $retVal;
		} 
	}
	
	public function getChildCategories($allCats,$productcnts)
	{
		$sql = "SELECT subcat.category_id, subcat.parent_category_id, subcat.category_name,subcat.category_image FROM ".DBPREFIX."_categories AS subcat										
				WHERE subcat.display_status = '1' AND subcat.parent_category_id != '0'
				ORDER BY subcat.category_id";
		$result = $this->db->query($sql);
		if($result && $result->num_rows()>0){
			$subcategories = $result->result_array();
			$retVal = array();
			$cnt = 0;
			foreach($allCats as $categories){
				$retVal[$categories["category_id"]] = $categories;
				$retVal[$categories["category_id"]]["sub_categories"] = array();
				$cntsub = 0;
				foreach($subcategories as $subcats){
					$parents = explode(",",$subcats["parent_category_id"]);
					foreach($parents as $parent){
						if($parent == $categories["category_id"]){
							$retVal[$categories["category_id"]]["sub_categories"][$subcats["category_id"]] = $subcats;
							$retVal[$categories["category_id"]]["sub_categories"][$subcats["category_id"]]["prod_cnt"] = isset($productcnts["category"][$parent]["subcategory"][$subcats["category_id"]]) ? $productcnts["category"][$parent]["subcategory"][$subcats["category_id"]] : 0;
							if($retVal[$categories["category_id"]]["sub_categories"][$subcats["category_id"]]["prod_cnt"] == 0){
								unset($retVal[$categories["category_id"]]["sub_categories"][$subcats["category_id"]]);
							}else
								$cntsub++;							
						}
					}
				}
				$cnt++;
			}			
			return $retVal;
		}
	}
}
?>