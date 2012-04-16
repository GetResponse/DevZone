<?php 
class ModelModuleGetresponse extends Model {
	public function getContacts() {
		$query = $this->db->query("SELECT
								cu.firstname ,
								cu.lastname, 
								cu.email,
								cu.telephone,
								ad.address_1,
								ad.postcode,	
								ad.city,
								ca.name AS country
								FROM " . DB_PREFIX . "customer cu
								JOIN " . DB_PREFIX . "address ad ON ad.customer_id = cu.customer_id
								JOIN " . DB_PREFIX . "country ca ON ad.country_id = ca.country_id								
								WHERE cu.newsletter = 1");

		return $query->rows;
	}
}
?>	