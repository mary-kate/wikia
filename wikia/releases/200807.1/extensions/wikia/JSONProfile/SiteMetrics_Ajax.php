<?php

/**
* Ouputs JSON data for site metrics.
**/

$wgExtensionFunctions[] = 'wfSiteMetricsReadLang';

//read in localisation messages
function wfSiteMetricsReadLang(){
	global $wgMessageCache, $IP;
	require_once ( "extensions/wikia/MetricsNY/SiteMetrics.i18n.php" );
	
	foreach( efWikiaSiteMetrics() as $lang => $messages ){
		$wgMessageCache->addMessages( $messages, $lang );
	}
}

$wgAjaxExportList [] = 'wfSiteMetricsJSON';
function wfSiteMetricsJSON($metric, $callback){
	global $wgMemc, $wgRequest;
	
	// the time in seconds to provide running averages for
	// its set at 2 days to test right now
	// assume each point is a day...
	$AVERAGE_TIME = 604800;
	$AVERAGE_NUM = $AVERAGE_TIME / 86400;

	// go back a month by default
	$DEFAULT_TIME = 2629744;
	
	$dbr =& wfGetDB( DB_MASTER );
	
	if(is_null($callback)){ $callback = "displayMetric"; }
	
	$startDate = $wgRequest->getVal("startDate");
	$endDate = $wgRequest->getVal("endDate");
	
	if(!is_null($startDate)){
	
		$endDate = is_numeric($endDateSql) ? $endDateSql : time();
		$endDateSql = date("Y-m-d 23:59:59", $endDate);
		
		// go back the AVERAGE_TIME so we'll have enough data to provide running averages	
		$fixedStartDate = date("Y-m-d 00:00:00", $startDate-$AVERAGE_TIME);
	
		// if we have start and end dates then dont do the default query	
		$hasTimeBound = ( !is_null($startDate) && !is_null($endDateSql) );
	}else{
		$startDate = time() - $DEFAULT_TIME;
		$endDate = time();
	}
	
	// by default go back a while - but actually go back far enough to get running averages
	$lastWeek = date("Y-m-d 00:00:00", time() - $DEFAULT_TIME);
	$fixedLastWeek = date("Y-m-d 00:00:00", time() - ($DEFAULT_TIME + $AVERAGE_TIME) );
	
	// figure out which metric we want
	switch($metric){
	case 0:
		$sql = "SELECT count( * ) / 2 AS the_count, UNIX_TIMESTAMP(r_date) as the_date
				FROM `user_relationship` ";
		
		if($hasTimeBound){
			$sql .= "WHERE r_type=1 AND r_date > '" . $fixedStartDate . "' AND r_date < '" . $endDateSql . "'";
		}else{
			$sql .= "WHERE r_type=1 AND r_date > '" . $fixedLastWeek . "'";
		}
		
		$sql .= " GROUP BY DATE(r_date) ORDER BY DATE(r_date) DESC";
		break;
	
	case 1:
		$sql = "SELECT count(*) AS the_count, UNIX_TIMESTAMP(ub_date) AS the_date
			FROM user_board ";
			
		if($hasTimeBound){
			$sql .= "WHERE ub_date > '" . $fixedStartDate . "' AND ub_date < '" . $endDateSql . "'";
		}else{
			$sql .= "WHERE ub_date > '" . $fixedLastWeek . "'";
		}
			
		$sql .= " GROUP BY DATE(ub_date)
			ORDER BY DATE(ub_date) DESC";
	    break;
    
	case 2:
		$sql = "SELECT count(*) AS the_count, UNIX_TIMESTAMP(log_timestamp), log_timestamp AS the_date
			FROM logging WHERE log_type='profile' ";

		if($hasTimeBound){
			$sql .= "AND log_timestamp > '" . $fixedStartDate . "' AND log_timestamp < '" . $endDateSql . "'";
		}else{
			$sql .= "AND log_timestamp > '" . $fixedLastWeek . "'";
		}

		$sql .=	" GROUP BY DATE(log_timestamp)
			ORDER BY DATE(log_timestamp) DESC";
	break;
	
	case 3:
		$sql = "SELECT count(*) AS the_count, UNIX_TIMESTAMP(poke_date) AS the_date
			FROM poke WHERE ";
		
		if($hasTimeBound){
			$sql .= " poke_date > '" . $fixedStartDate . "' AND poke_date < '" . $endDateSql . "'";
		}else{
			$sql .= " poke_date > '" . $fixedLastWeek . "'";
		}
		
		$sql .= " GROUP BY DATE(poke_date)
			ORDER BY DATE(poke_date) DESC";
	break;

	case 4:
		$sql = "SELECT count(*) AS the_count, UNIX_TIMESTAMP(img_timestamp) AS the_date
			FROM image WHERE ";

		if($hasTimeBound){
			$sql .= "img_timestamp > '" . $fixedStartDate . "' AND img_timestamp < '" . $endDateSql . "'";
		}else{
			$sql .= "img_timestamp > '" . $fixedLastWeek . "'";
		}

		$sql .= " GROUP BY DATE(img_timestamp)
			ORDER BY DATE(img_timestamp) DESC";
	break;
	
	case 5:
		$sql = "SELECT count(*) AS the_count, UNIX_TIMESTAMP(us_date) AS the_date
			FROM user_profile_status WHERE ";

		if($hasTimeBound){
			$sql .= "us_date > '" . $fixedStartDate . "' AND us_date < '" . $endDateSql . "'";
		}else{
			$sql .= "us_date > '" . $fixedLastWeek . "'";
		}

		$sql .= " GROUP BY DATE(us_date)
			  ORDER BY DATE(us_date) DESC";
	break;
	
  	case 6:
		$sql = "SELECT count(*) AS the_count, UNIX_TIMESTAMP(ur_date) AS the_date
			FROM user_register_track WHERE ";
			
		if($hasTimeBound){
			$sql .= " ur_date > '" . $fixedStartDate . "' AND ur_date < '" . $endDateSql . "'";
		}else{
			$sql .= " ur_date > '" . $fixedLastWeek . "'";
		}
			
		$sql .= " GROUP BY DATE(ur_date)
			  ORDER BY DATE(ur_date) DESC";
	break;

	case 7:
		$sql = "SELECT SUM(num_queries) as the_count, UNIX_TIMESTAMP(created_at) as the_date
			FROM metrics_hourly_queries WHERE ";
		
		if($hasTimeBound){
			$sql .= " created_at > '" . $fixedStartDate . "' AND created_at < '" . $endDateSql . "'";
		}else{
			$sql .= " created_at > '" . $fixedLastWeek . "'";
		}
	
		$sql .= " GROUP BY DATE(created_at)
			ORDER BY DATE(created_at) DESC";
	break;
	
	// everything past here is broken
	case 9:
		$keyMonth = wfMemcKey( 'wikiasearch', 'sitemetrics', 'metric', 'numqueries', 'month', $monthPostfix );
		$keyDay = wfMemcKey( 'wikiasearch', 'sitemetrics', 'metric', 'numqueries', 'day', $dayPostfix );

		$sqlDay = "SELECT SUM(num_queries) as the_count, DATE_FORMAT(DATE(created_at), '%y %m %d') as the_date
				FROM metrics_hourly_queries
				GROUP BY DATE(created_at)
				ORDER BY DATE(created_at) DESC
				LIMIT 0, 120;";
		$sqlMonth = 'SELECT SUM(num_queries) as the_count, DATE_FORMAT(DATE(created_at), "%y %m") as the_date
				FROM metrics_hourly_queries
				GROUP BY DATE_FORMAT(DATE(created_at), "%y %m")
				ORDER BY DATE_FORMAT(DATE(created_at), "%y %m") DESC
				LIMIT 0, 12;';
		
		$sqlDayBack = "SELECT SUM(num_queries) as the_count, DATE_FORMAT(DATE(created_at), '%y %m %d') as the_date
				FROM metrics_hourly_queries
				WHERE created_at < '%lastDay'
				GROUP BY DATE(created_at)
				ORDER BY DATE(created_at) ASC
				LIMIT 0, 29;";
		$sqlMonthBack = 'SELECT SUM(num_queries) as the_count, DATE_FORMAT(DATE(created_at), "%y %m") as the_date
				FROM metrics_hourly_queries
				WHERE created_at < "%lastDay"
				GROUP BY DATE_FORMAT(DATE(created_at), "%y %m")
				ORDER BY DATE_FORMAT(DATE(created_at), "%y %m") ASC
				LIMIT 0,3;';

	break;
	case 10:
		$key = wfMemcKey( 'wikiasearch', 'sitemetrics', 'metric', 'topqueries' );
		
		$sql = "SELECT AVG(`count`) as the_count, `query`, `language` FROM metrics_current_top_queries
				GROUP BY `query`, DATE(created_at)
				ORDER BY the_count DESC
				LIMIT 0,500";
	break;
	case 11:
		$key = wfMemcKey( 'wikiasearch', 'sitemetrics', 'metric', 'topkeywords' );
		
		$sql = "SELECT AVG(`count`) as the_count, `keyword`
			FROM metrics_current_ktkeywords
			GROUP BY `keyword`, DATE(created_at)
			ORDER BY the_count DESC
			LIMIT 0,500";
	break;
	case 12:
		$key = wfMemcKey( 'wikiasearch', 'sitemetrics', 'metric', 'currentqueries' );
		
		$sql = 'SELECT AVG(`count`) as the_count, `query`, `language`
			FROM metrics_current_top_queries
			WHERE created_at <= CONCAT(DATE(NOW()), " 23:59:59")
				AND created_at >= CONCAT(DATE(NOW()), " 00:00:00")
			GROUP BY `query`, DATE(created_at)
			ORDER BY the_count DESC
			LIMIT 0,500';
	break;
	case 13:
		$key = wfMemcKey( 'wikiasearch', 'sitemetrics', 'metric', 'currentkeywords' );
		
		$sql = 'SELECT AVG(`count`) as the_count, `keyword`
			FROM metrics_current_ktkeywords
			WHERE created_at <= CONCAT(DATE(NOW()), " 23:59:59")
				AND created_at >= CONCAT(DATE(NOW()), " 00:00:00")
			GROUP BY `keyword`, DATE(created_at)
			ORDER BY the_count DESC
			LIMIT 0,500';
	break;
	case 14:
		$sqlDay = "SELECT AVG(`count`) as the_count, op, Date_FORMAT(DATE(created_at), '%m/%d/%y') as the_date
				FROM metrics_ktops
				GROUP BY op, DATE(created_at)
				ORDER BY op, DATE(created_at) DESC
				LIMIT 0, 120;";
		$sqlMonth = "SELECT AVG(`count`) as the_count, op, Date_FORMAT(DATE(created_at), '%m/%y') as the_date
				FROM metrics_ktops
				GROUP BY op, Date_FORMAT(DATE(created_at), '%m %y')
				ORDER BY op, DATE(created_at) DESC
				LIMIT 0, 120;";
				
		$sqlDayBack = "SELECT AVG(`count`) as the_count, op, Date_FORMAT(DATE(created_at), '%m/%d/%y') as the_date
				FROM metrics_ktops
				WHERE created_at < '%lastDay'
				GROUP BY op, DATE(created_at)
				ORDER BY op, DATE(created_at) ASC
				LIMIT 0, 29;";
		$sqlMonthBack = "SELECT AVG(`count`) as the_count, op, Date_FORMAT(DATE(created_at), '%m/%y') as the_date
				FROM metrics_ktops
				WHERE created_at < '%lastDay'
				GROUP BY op, Date_FORMAT(DATE(created_at), '%m %y')
				ORDER BY op, DATE(created_at) ASC
				LIMIT 0, 3;";
	break;
	default: return "";
	break;
	}
	
	$msg = efWikiaSiteMetrics();
	$keys = array_keys($msg["en"]);
	$messages = array();
	
	// get the i18n messages
	foreach($keys as $key){ $messages[$key] = wfMsg($key); }
	
	// figure out what to do about these
	/* if($metric == 10 || $metric == 11 || $metric == 12 || $metric == 13){
		$data = $wgMemc->get($key);
		
		if(!$data){
			$res = $dbr->query($sql);
			while ($row = $dbr->fetchObject( $res ) ) {
				if($metric == 10 || $metric == 12)
					$data[] = array(   "count"=>round($row->the_count), "query"=>$row->query );
				else if($metric == 11 || $metric == 13)
					$data[] = array(   "count"=>round($row->the_count), "query"=>$row->keyword );
			}
			
			$wgMemc->set( $key, $data, (60*60) );
		}
		
		$stats["messages"] = $messages;
		$stats["tableData"] = $data;
	
		// convert to JSON and return
		$result = jsonify($stats);
	
		return 'var metricData =' . $result . ";\n\n" . $callback .'(metricData, ' . $metricId . ');';
	} */
	
	$data = array();
	
	// get the data into an array
	$res = $dbr->query($sql);
	while ($row = $dbr->fetchObject( $res ) ) {
		$data[] = array("timestamp"=>$row->the_date,
				"date"=>date("m/d/Y", $row->the_date),
				"count"=>round($row->the_count),
				"movingAverage"=> "N/A");
	}
	
	// start at the oldest data and move forward to work out the averages
	$avgSum = 0;
	$avgData = array();
	
	
	$lastVal = NULL;
	
	$finalData = array();
	
	$finalAvgDelta = 0;
	$finalAvg = 0;
	$finalMin = 0;
	$finalMax = 0;
	$movingAverageMax = 0;
	
	$dateParts = getdate();
	
	$month = $dateParts["mon"];
	$day = $dateParts["mday"];
	$year = $dateParts["year"];
	$hours = $dateParts["hours"];
	
	for($i=count($data)-1; $i >= 0; $i--){
		$avgSum += $data[$i]["count"];
		$avgData[] = $data[$i]["count"];
		
		if(!is_null($lastVal)){
			$data[$i]["delta"] = ($data[$i]["count"] - $lastVal) / $lastVal;
		}else{
			$data[$i]["delta"] = "N/A";
		}
		
		$lastVal = $data[$i]["count"];
		
		// we can start averaging
		if( (count($data)-1) - $i >= $AVERAGE_NUM){
			$data[$i]["movingAverage"] = round($avgSum / $AVERAGE_NUM, 3);
			$movingAverageMax = ( $data[$i]["movingAverage"] > $movingAverageMax ) ? $data[$i]["movingAverage"] : $movingAverageMax;
			$avgSum -= array_shift($avgData);
		}
		
		if($data[$i]["timestamp"] > $startDate){
			
			$thisDate = getdate($data[$i]["timestamp"]);

			if($thisDate["mon"] == $month && $thisDate["mday"] == $day && $thisDate["year"] == $year){
				$data[$i]["count"] = round( $data[$i]["count"] + ($data[$i]["count"]/$hours) * (24-$hours) );
				$data[$i]["predicted"] = true;
			}
			
			$finalData[] = $data[$i];
			$finalAvg += $data[$i]["count"];
			$finalAvgDelta += $data[$i]["delta"];
			$finalMin = ( $data[$i]["count"] < $finalMin ) ? $data[$i]["count"] : $finalMin;
			$finalMax = ( $data[$i]["count"] > $finalMax ) ? $data[$i]["count"] : $finalMax;
		}
	}
	
	$finalAvg = round( ($finalAvg / count($finalData)), 3);
	$finalAvgDelta = round ( ($finalAvgDelta / count($finalData)), 3);
	
	// while were messing around lets figure out the standard deviation
	$rms = 0;
	
	$dataString = array();
	$avgDataString = array();
	
	foreach($finalData as $d){
		$rms += pow( ( $d["count"] - $finalAvg ), 2);
		
		if(is_numeric($d["count"])) { $dataString[] = $d["count"]; }
		if(is_numeric($d["movingAverage"])){ $avgDataString[] = $d["movingAverage"]; }
	}
	$n = 1 / count($finalData);
	$stdDev = round(sqrt($n * $rms), 3);
	
	// concat up the arrays
	$stats = array();
	$stats["tableData"] = $finalData;
	$stats["average"] = $finalAvg;
	$stats["averageDelta"] = $finalAvgDelta;
	$stats["min"] = $finalMin;
	$stats["max"] = $finalMax;
	$stats["stdDev"] = $stdDev;
	$stats["movingAvgMax"] = $movingAverageMax;
	$stats["startDate"] = $startDate;
	$stats["endDate"] = $endDate;
	$stats["dataString"] = $dataString;
	$stats["averageString"] = $avgDataString;
	$stats["messages"] = $messages;
	
	// convert to JSON and return
	$result = jsonify($stats);
	
	return 'var metricData =' . $result . ";\n\n" . $callback .'(metricData, ' . $metric . ');';
}

// flips the MySQL date into something more palatable
function formatDate($date){
	$date_array = split(" ",$date);
	
	$year = $date_array[0];
	$month = $date_array[1];
		
	$time = mktime(0,0,0,$month,1,"20".$year);
	return date("m",$time) . "/" . date("y",$time);
}

// flips the MySQL date into something more palatable
function formatDateDay($date){
	$date_array = split(" ",$date);
		
	$year = $date_array[0];
	$month = $date_array[1];
	$day = $date_array[2];
		
	$time = mktime(0,0,0,$month,$day,"20".$year);
	return date("m",$time) . "/" . date("d",$time) . "/" . date("y",$time);
}

?>
