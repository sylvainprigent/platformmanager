<?php

require_once 'Framework/Model.php';

/**
 * Model to calculate GRR statistics
 *
 * @author Sylvain Prigent
 */
class BkReport extends Model {

	/**
	 * Calculate statistics
	 * @param unknown $datebegin
	 * @param unknown $dateend
	 * @param unknown $champ
	 * @param unknown $type_recherche
	 * @param unknown $text
	 * @param unknown $contition_et_ou
	 * @return multitype:
	 */
	public function reportstats($datebegin, $dateend, $champ, $type_recherche, $text, $contition_et_ou){
		
		
		$sql = "SELECT distinct e.id, e.start_time, e.end_time, e.short_description, e.full_description, "
				. "e.color_type_id, e.recipient_id, "
				. "a.name as area_name, r.name as resource, r.description, a.id as area, u.login, c.name color"
				. " FROM bk_calendar_entry e, re_area a, re_info r, core_users u, bk_color_codes c "
				. " WHERE e.resource_id = r.id  AND u.id = e.recipient_id AND r.id_area = a.id AND c.id = e.color_type_id"		
				. " AND e.start_time >= " . $datebegin . " AND e.end_time <= " .$dateend . " ";
		
		//echo "contition_et_ou = " . $contition_et_ou . "<br/>";
		
                //print_r($champ);
                //echo "<br/>";
                $emptyFields = 0;
                $countF = 0;
                for($i = 0 ; $i < count($champ) ; $i++){
                    $countF++;
                    //echo "ch = " . $text[$i] . "<br/>";
                    if ($text[$i] == ""){
                        $emptyFields++;
                    }
                }
                
                //echo "empty fields = " . $emptyFields . ", count fields = " . $countF . "<br/>"; 
                if ($countF != $emptyFields){
                    $sql .= " AND (";
                    $first = true;
                    for($i = 0 ; $i < count($champ) ; $i++){

                            if( $text[$i] != "" ){

                                    if(!$first){
                                            $sql .= " " . $contition_et_ou . " ";
                                    }
                                    if ($first){
                                            $first = false;
                                    }
                                    $sql .= $this->extractQueryFrom( $champ[$i], $text[$i], $type_recherche[$i] );
                            }
                    }
                    $sql .= ")";
                }
		//echo "sql = " . $sql . "<br/>";
		//return;
		
		$data = $this->runRequest($sql);
		return $data->fetchAll();
	}
	
	/**
	 * Internal method used by reportstats
	 * @param unknown $champ
	 * @param unknown $text
	 * @param unknown $type_recherche
	 * @return string
	 */
	private function extractQueryFrom($champ, $text, $type_recherche){
		
		$like = " LIKE ";
		if( $type_recherche == 0 ){
			$like = " NOT LIKE ";
		}
		
		if ( $champ == "area" ){
			return " a.name = " . $text;
		}
		if ($champ == "resource"){
			return " r.name ". $like ." '%" . $text . "%'";
		}
		if ($champ == "color_code"){
			return " c.name ". $like ." '%" . $text . "%'";
		}
		if ($champ == "short_description"){
			return " e.short_description ". $like ." '%" . $text . "%'";
		}
		if ($champ == "short_description"){
			return " e.full_description ". $like ." '%" . $text . "%'";
		}
		if ($champ == "recipient" ){
			return " u.login ". $like ." '%" . $text . "%'";
		}
	}
	
	/**
	 * summaryse report stat into a table 
	 * @param unknown $table
	 * @param unknown $entrySummary
	 * @return multitype:Ambigous <multitype:, number> multitype:unknown
	 */
	public function summaryseReportStats($table, $entrySummary){
		
		if ($entrySummary == "recipient"){
			$entrySummary = "login";
		}
		if ($entrySummary == "color_code"){
			$entrySummary = "color";
		}
		
		//echo '<br /> entrySummary = ' . $entrySummary . "<br/>";
		
		// get unique resource
		$tResources = array();
		foreach($table as $t){
			
			$found = false;
			foreach($tResources as $res){ 
				if( $t['resource'] == $res ){
					$found = true;
					break;
				}
			}
			
			if (!$found){
				$tResources[] = $t['resource'];
			}
		}
		
		// get unique entry summary
		$tSummary = array();
		foreach($table as $t){
				
			//print_r($t);
			//return;
			
			$found = false;
			foreach($tSummary as $res){
				if( $t[$entrySummary] == $res ){
					$found = true;
					break;
				}
			}
				
			if (!$found){
				$tSummary[] = $t[$entrySummary];
			}
		}
		
		// count the numbers of reservation and the time
		$countTable = array();
		$timeTable = array();
		
		foreach ($tSummary as $sum){
			foreach ($tResources as $res){
			
				$count = 0;
				$time = 0;
				foreach($table as $t){
					if ( $t["resource"] == $res && $t[$entrySummary] == $sum ){
						$count += 1;
						$time += $t["end_time"] - $t["start_time"]; 
					}
				}
				$countTable[$sum][$res] = $count;
				$timeTable[$sum][$res] = $time;
			}
		} 
		
		$summary = array('countTable' => $countTable, 'timeTable' => $timeTable, 'resources' => $tResources, 'entrySummary' => $tSummary);
		return $summary;
	}
}