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

$wgAjaxExportList [] = 'wfQueryCounter';
function wfQueryCounter($callback){
        global $wgMemc, $wgRequest;
        $dbr =& wfGetDB( DB_MASTER );
        
        $key = wfMemcKey( 'wikiasearch' , 'metric' , 'querycounter', 'queryrate' );
        $obj = $wgMemc->get($key);
        
	if(!$obj || ($obj && time() - $obj["at"] > 3600) ){
                // get the total number of queries
                $sql = "SELECT SUM(num_queries) AS the_sum FROM metrics_hourly_queries;";
                $res = $dbr->query($sql);
                $row = $dbr->fetchObject($res);                
                $totalQueryCount = $row->the_sum;

                // get today's queries
                $sql = "SELECT SUM(num_queries) AS the_sum, COUNT(*) AS the_count
                        FROM metrics_hourly_queries
                        WHERE DATE(created_at)=(
                                SELECT DATE(created_at) FROM metrics_hourly_queries ORDER BY created_at DESC LIMIT 1
                        );";
                $res = $dbr->query($sql);
                $row = $dbr->fetchObject($res);
                
                // query rate in seconds
                $rateQuery = $row->the_sum / ($row->the_count * 60 * 60);

                // total contribtuions
                $sql = "SELECT SUM(`count`) AS the_sum FROM metrics_ktops;";
                $res = $dbr->query($sql);
                $row = $dbr->fetchObject($res);                
                $totalContributionCount = $row->the_sum;
                
                // get today's contributions
                $sql = "SELECT SUM(`count`) AS the_sum, COUNT(*) AS the_count
                        FROM metrics_ktops
                        WHERE DATE(created_at)=(
                                SELECT DATE(created_at) FROM metrics_ktops ORDER BY created_at DESC LIMIT 1
                        );";
                $res = $dbr->query($sql);
                $row = $dbr->fetchObject($res);
                
                // query rate in seconds
                $rateContribution = $row->the_sum / ($row->the_count * 60 * 60);

                // figure out the last hour we have
                $sql = "SELECT UNIX_TIMESTAMP(created_at) AS the_time FROM metrics_hourly_queries ORDER BY created_at DESC LIMIT 1";
                $res = $dbr->query($sql);
                $row = $dbr->fetchObject($res);
                $lastTime = $row->the_time;
                
                $obj["contributionRate"] = round($rateContribution, 4);
                $obj["queryRate"] = round($rateQuery, 4);
                $obj["at"] = time();
                
        }else{
                $totalContributionCount = $obj["contributions"];
                $totalQueryCount = $obj["queries"];
                
                $rateContribution = $obj["contributionRate"];
                $rateQuery = $obj["queryRate"];
                
                $lastTime = $obj["at"];
        }
        
        // diff between the last data point in seconds
        $timeDiff = time() - $lastTime;
        $totalQueryCount = $totalQueryCount + ($timeDiff * $rateQuery);
        $totalContributionCount = $totalContributionCount + ($timeDiff * $rateContribution);
        
        $obj["at"] = time();
        $obj["queries"] = round($totalQueryCount);
        $obj["contributions"] = round($totalContributionCount);
        
        $wgMemc->set( $key, $obj );
        
        $res = 'var data=' . jsonify($obj) . ";\n\n" . $callback .'(data);';
        
        return $res;
}

$wgAjaxExportList [] = 'wfSiteMetricsJSON';
function wfSiteMetricsJSON($metric, $callback){
	global $wgMemc, $wgRequest;
	
	// figure out which metric we want
	// these indexes match with the phpval properties from var METRICS = new Array(); in the javascript
		
	switch($metric){
		
	// get the MW/site metrics
	case 0: return fetchSiteMetrics($metric, $callback); break;
	case 1: return fetchSiteMetrics($metric, $callback); break;
	case 2: return fetchSiteMetrics($metric, $callback); break;
	case 3: return fetchSiteMetrics($metric, $callback); break;
	case 4: return fetchSiteMetrics($metric, $callback); break;
	case 5: return fetchSiteMetrics($metric, $callback); break;
  	case 6: return fetchSiteMetrics($metric, $callback); break;
	case 7: return fetchSiteMetrics($metric, $callback); break;

	// get the query data
	case 8: return fetchQueryData($metric, $callback); break;
	case 9: return fetchQueryData($metric, $callback); break;
	case 10: return fetchQueryData($metric, $callback); break;
	case 11: return fetchQueryData($metric, $callback); break;
	
	// get KT stats
	case 12: return fetchKTStats($metric, $callback); break;
	case 13: return fetchKTTrend($metric, $callback); break;
	
	// do trends on queries
	case 14: return fetchQueryTrend($metric, $callback); break;
	
	// get the needed pages
	case 15: return fetchQueryData($metric, $callback); break;
	case 16: return fetchGlobalKTTrend($metric, $callback); break;
        
	default: return "";
	break;
	}
}

function createJSON($sql, $boundSQL){
        global $wgRequest;
        
        $dbr =& wfGetDB( DB_MASTER );
        $IsByMonth = $wgRequest->getVal("month", false);
        $outputCSV = $wgRequest->getVal("csv", false);

	// get the i18n messages
	$msg = efWikiaSiteMetrics();
	$keys = array_keys($msg["en"]);
	$messages = array();
	foreach($keys as $key){ $messages[$key] = wfMsg($key); }

	$res = $dbr->query($sql);
        $data = array();
        
	// get the data into an array
	$res = $dbr->query($sql);
	while ($row = $dbr->fetchObject( $res ) ) {
		$data[] = array(
                                "timestamp"=>$row->the_date,
                                "date"=>date("n/j/Y", $row->the_date),
				"count"=>round($row->the_count)
                                );
	}
        
        $data = array_reverse($data);
        
	$monthBuckets = array();
	$monthTimes = array();
	
	if($IsByMonth){
		foreach($data as $d){
			$key = date("m/y", $d["timestamp"]);
                        
			if(!array_key_exists($key, $monthBuckets)){
				$monthBuckets[$key] = 0;
				$monthTimes[$key] = substr($d["timestamp"], 0, 6) . "0000";
			}
			
			$monthBuckets[$key] += $d["count"];
		}
		
		$data = array();
		foreach($monthBuckets as $key => $count){
			$data[] = array("timestamp"=>$monthTimes[$key],
				"date"=>$key,
				"count"=>$count);
		}
	}
	
	$avgSum = 0;
	$avgData = array();

	$lastVal = NULL;
	
	$finalData = array();
	
	$finalAvgDelta = 0;
	$finalMaxDelta = 0;
	$finalMinDelta = 0;
	
	$finalAvg = 0;
	$finalMin = $data[0]["count"];
	$finalMax = 0;
	$movingAverageMax = 0;
	
	$dateParts = getdate();
	
	$month = $dateParts["mon"];
	$day = $dateParts["mday"];
	$year = $dateParts["year"];
	$hours = $dateParts["hours"];
	
	for($i=0; $i < count($data); $i++){
		$avgSum += $data[$i]["count"];
				
		$thisDate = getdate($data[$i]["timestamp"]);

		if($thisDate["mon"] == $month && $thisDate["mday"] == $day && $thisDate["year"] == $year){
			$data[$i]["count"] = round( $data[$i]["count"] + ($data[$i]["count"]/$hours) * (24-$hours) );
			$data[$i]["predicted"] = true;
		}
			
		if($IsByMonth && $thisDate["mon"] == $month && $thisDate["year"] == $year){
			$data[$i]["count"] = round( $data[$i]["count"] + ( ($data[$i]["count"]/$day) * (30-$day) ) );
			$data[$i]["predicted"] = true;
		}
		
		if(!is_null($lastVal)){
			$data[$i]["delta"] = round( ( ($data[$i]["count"] - $lastVal) / $lastVal), 3);
		}else{
			$data[$i]["delta"] = "N/A";
		}
		
		$lastVal = $data[$i]["count"];
                
		$finalData[] = $data[$i];
		$finalAvg += $data[$i]["count"];
		$finalAvgDelta += $data[$i]["delta"];
			
		$finalMin = ( $data[$i]["count"] < $finalMin ) ? $data[$i]["count"] : $finalMin;
		$finalMax = ( $data[$i]["count"] > $finalMax ) ? $data[$i]["count"] : $finalMax;
		$finalMaxDelta = ( $data[$i]["delta"] > $finalMaxDelta ) ? $data[$i]["delta"] : $finalMaxDelta;
		$finalMinDelta = ( $data[$i]["delta"] < $finalMinDelta ) ? $data[$i]["delta"] : $finalMinDelta;
			
	}
	
	// round stuff off
	$finalAvg = round( ($finalAvg / count($finalData)), 3);
	$finalAvgDelta = round ( ($finalAvgDelta / count($finalData)), 3);
	$finalMaxDelta = round($finalMaxDelta * 100, 3);
	$finalMinDelta = abs($finalMinDelta);
	
	// while were messing around lets figure out the standard deviation
	$rms = 0;
	$dataString = array();
	$avgDataString = array();
	$deltaString = array();
	
	foreach($finalData as $d){
		$rms += pow( ( $d["count"] - $finalAvg ), 2);
		
		if(is_numeric($d["count"])) { $dataString[] = $d["count"]; }
		
		if(is_numeric($d["delta"])) {
			$deltaString[] = round(($finalMinDelta+$d["delta"]) * 100);
		}else{
			$deltaString[] = 0;
		}	
	}

	$n = 1 / count($finalData);
	$stdDev = round(sqrt($n * $rms), 3);
	
	$finalMaxDelta = ($finalMinDelta+$finalMaxDelta);
		
	// figure out the max and min available times
	$res = $dbr->query($boundSQL);
	$row = $dbr->fetchObject( $res );
	$maxTime = $row->the_date;
	
	$boundSQL = str_replace("DESC", "ASC", $boundSQL);
	$res = $dbr->query($boundSQL);
	$row = $dbr->fetchObject( $res );
	$minTime = $row->the_date;
        
        // get the i18n messages
	$msg = efWikiaSiteMetrics();
	$keys = array_keys($msg["en"]);
	$messages = array();
	foreach($keys as $key){ $messages[$key] = wfMsg($key); }

	// concat up the arrays
	$stats = array();
	$stats["tableData"] = $finalData;
	$stats["average"] = $finalAvg;
	$stats["averageDelta"] = $finalAvgDelta;
	$stats["min"] = $finalMin;
	$stats["max"] = $finalMax;
	$stats["stdDev"] = $stdDev;
	$stats["maxDelta"] = $finalMaxDelta;
	
	$stats["startDate"] = $startDate;
	$stats["endDate"] = $endDate;
	$stats["minTime"] = $minTime;
	$stats["maxTime"] = $maxTime;
	
	$stats["deltaString"] = $deltaString;
	$stats["dataString"] = $dataString;
	$stats["averageString"] = $avgDataString;
	$stats["messages"] = $messages;
	
        return $stats;
}

function fetchGlobalKTTrend($metric, $callback){
	global $wgRequest, $wgMemc;
	
	// go back a month by default
	$DEFAULT_TIME = 86400 * 30;
        
        $dbr =& wfGetDB( DB_MASTER );
        
	// grab some query params
	$startDate = $wgRequest->getVal("startDate");
	$endDate = $wgRequest->getVal("endDate");
	$IsByMonth = $wgRequest->getVal("month", false);
        $outputCSV = $wgRequest->getVal("csv", false);
        
	// if there isnt a start date figure one out
	if(!is_null($startDate)){
		$endDate = !is_null($endDate) ? $endDate : time();		
	}else{
		$startDate = time() - $DEFAULT_TIME;
		$endDate = time();
	}
        
	$endDateSql = date("Y-m-d 23:59:59", $endDate);
        $startDateSql = date("Y-m-d 00:00:00", $startDate);
	
	$sql = 'SELECT SUM(`count`) AS the_count, UNIX_TIMESTAMP(created_at) AS the_date
                        FROM metrics_ktops
                        WHERE created_at > "' . $startDateSql . '" AND created_at < "' . $endDateSql . '"
                        GROUP BY DATE(created_at)
                        ORDER BY created_at DESC LIMIT 365;';
                        
        $boundSQL = 'SELECT UNIX_TIMESTAMP(created_at) AS the_date FROM metrics_ktops ORDER BY created_at DESC LIMIT 1';
        $key = wfMemcKey( 'wikiasearch' , 'metrics' , $metric, $callback, $IsByMonth, $startDateSql, $endDateSql );
	
	$result = $wgMemc->get($key);
	if(!$result){
                $result = createJSON($sql, $boundSQL);
                $wgMemc->set( $key, $result, 300 );
        }
        
        if($outputCSV){ return arrayToCSV($result); }
        
        $result = jsonify($result);
	$res = 'var metricData =' . $result . ";\n\n" . $callback .'(metricData, ' . $metric . ');';
	
	return $res;
}

function fetchSiteMetrics($metric, $callback){
	global $wgRequest, $wgMemc;
	
	// the time in seconds to provide running averages for
	// its set at 2 days to test right now
	// assume each point is a day...
	// this isnt used currently
	
	// go back a month by default
	$DEFAULT_TIME = 86400 * 30;
	
	$dbr =& wfGetDB( DB_MASTER );
	
	if(is_null($callback)){ $callback = "displayMetric"; }
	
	// grab some query params
	$startDate = $wgRequest->getVal("startDate");
	$endDate = $wgRequest->getVal("endDate");
	$outputCSV = $wgRequest->getVal("csv", false);
	$IsByMonth = $wgRequest->getVal("month", false);
	
	// if there isnt a start date figure one out
	if(!is_null($startDate)){
	
		$endDate = !is_null($endDate) ? $endDate : time();
		$endDateSql = date("Y-m-d 23:59:59", $endDate);
		
		// go back the AVERAGE_TIME so we'll have enough data to provide running averages	
		$fixedStartDate = date("Y-m-d 00:00:00", $startDate);
	
		// if we have start and end dates then dont do the default query	
		$hasTimeBound = ( !is_null($startDate) && !is_null($endDateSql) );
	}else{
		$startDate = time() - $DEFAULT_TIME;
		$endDate = time();
	}
	
	// by default go back a while - but actually go back far enough to get running averages
	$lastWeek = date("Y-m-d 00:00:00", time() - $DEFAULT_TIME);
	$fixedLastWeek = date("Y-m-d 00:00:00", time() - $DEFAULT_TIME );
	
	if($hasTimeBound){
		$key = wfMemcKey( 'wikiasearch' , 'metrics' , $metric, $callback, $IsByMonth, $fixedStartDate,  $endDateSql);
	}else{
		$key = wfMemcKey( 'wikiasearch' , 'metrics' , $metric, $callback, $IsByMonth, $fixedLastWeek);
	}
	
	$result = $wgMemc->get($key);
	
	if($result){ return $result; }

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
		
		$boundSQL = "SELECT UNIX_TIMESTAMP(r_date) as the_date
				FROM `user_relationship` WHERE r_type=1
				GROUP BY DATE(r_date) ORDER BY DATE(r_date) DESC LIMIT 1";
		
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
			
		$boundSQL = "SELECT UNIX_TIMESTAMP(ub_date) AS the_date
				FROM user_board
				GROUP BY DATE(ub_date)
				ORDER BY DATE(ub_date) DESC LIMIT 1;";
	    break;
    
	case 2:
		$sql = "SELECT count(*) AS the_count, UNIX_TIMESTAMP(log_timestamp) AS the_date
			FROM logging WHERE log_type='profile' ";

		if($hasTimeBound){
			$sql .= "AND log_timestamp > '" . $fixedStartDate . "' AND log_timestamp < '" . $endDateSql . "'";
		}else{
			$sql .= "AND log_timestamp > '" . $fixedLastWeek . "'";
		}

		$sql .=	" GROUP BY DATE(log_timestamp)
			ORDER BY DATE(log_timestamp) DESC";
		
		$boundSQL = "SELECT UNIX_TIMESTAMP(log_timestamp) AS the_date
				FROM logging WHERE log_type='profile'
				GROUP BY DATE(log_timestamp)
				ORDER BY DATE(log_timestamp) DESC LIMIT 1";
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
		
		$boundSQL = "SELECT UNIX_TIMESTAMP(poke_date) AS the_date
				FROM poke
				GROUP BY DATE(poke_date)
				ORDER BY DATE(poke_date) DESC LIMIT 1";
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
			
		$boundSQL = "SELECT UNIX_TIMESTAMP(img_timestamp) AS the_date
				FROM image
				GROUP BY DATE(img_timestamp)
				ORDER BY DATE(img_timestamp) DESC LIMIT 1";
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
			  
		$boundSQL = "SELECT UNIX_TIMESTAMP(us_date) AS the_date
				FROM user_profile_status
				GROUP BY DATE(us_date)
				ORDER BY DATE(us_date) DESC LIMIT 1";
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
		
		$boundSQL = "SELECT UNIX_TIMESTAMP(ur_date) AS the_date
				FROM user_register_track
				GROUP BY DATE(ur_date)
				ORDER BY DATE(ur_date) DESC LIMIT 1";
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
			
		$boundSQL = "SELECT UNIX_TIMESTAMP(created_at) as the_date
				FROM metrics_hourly_queries
				GROUP BY DATE(created_at)
				ORDER BY DATE(created_at) DESC LIMIT 1";
	break;

	default: die("NO"); break;
	}
	
	$result = $wgMemc->get($key);
	if(!$result){
                $result = createJSON($sql, $boundSQL);
                $wgMemc->set( $key, $result, 300 );
        }
        
        if($outputCSV){ return arrayToCSV($result); }
        
        $result = jsonify($result);
	
	$res = 'var metricData =' . $result . ";\n\n" . $callback .'(metricData, ' . $metric . ');';
	return $res;
}

function arrayToCSV($data){
        $str = "date, count, delta \n";
        
        foreach($data["tableData"] as $d){
                $str .= $d["date"] . ", " . $d["count"] . ", " . $d["delta"] . "\n";
        }
        
        return $str;
}

// does the trends for queries
function fetchQueryTrend($metric, $callback){
	
	global $wgRequest, $wgMemc;
	
	// go back a month by default
	$DEFAULT_TIME = 86400 * 30;
        
        $dbr =& wfGetDB( DB_MASTER );
        
	// grab some query params
	$startDate = $wgRequest->getVal("startDate");
	$endDate = $wgRequest->getVal("endDate");
	$IsByMonth = $wgRequest->getVal("month", false);
        $op = mysql_real_escape_string( $wgRequest->getVal("op", "add") );
	$query = mysql_real_escape_string($wgRequest->getVal("query", false));
	$lang = mysql_real_escape_string($wgRequest->getVal("lang", false));
        $outputCSV = $wgRequest->getVal("csv", false);
        
	// if there isnt a start date figure one out
	if(!is_null($startDate)){
		$endDate = !is_null($endDate) ? $endDate : time();		
	}else{
		$startDate = time() - $DEFAULT_TIME;
		$endDate = time();
	}
        
	$endDateSql = date("Y-m-d 23:59:59", $endDate);
        $startDateSql = date("Y-m-d 00:00:00", $startDate);
	
	// figure out if we want a KT or a search trend
	switch($metric){
		case 14:
			if($lang){
				$sql = 'SELECT SUM(`count`) AS the_count, `query` AS the_query, UNIX_TIMESTAMP(created_at) AS the_date
					FROM metrics_current_top_queries
					WHERE `query`="' . $query.  '" AND `language`="' . $lang . '"
                                        AND created_at > "' . $startDateSql . '" AND created_at < "' . $endDateSql . '"
					GROUP BY DATE(created_at)
					ORDER BY created_at DESC
					LIMIT 365;';
                                $boundSQL = 'SELECT UNIX_TIMESTAMP(created_at) AS the_date
                                                FROM metrics_current_top_queries
                                                WHERE `query`="' . $query.  '" AND `language`="' . $lang . '"
                                                ORDER BY created_at DESC LIMIT 1';
				$key = wfMemcKey( 'wikiasearch' , 'metrics' , $metric, $callback, $query, $lang, $IsByMonth, $startDateSql, $endDateSql );
			}else{
				$sql = 'SELECT SUM(`count`) AS the_count, `keyword` AS the_query, UNIX_TIMESTAMP(created_at) AS the_date
					FROM metrics_current_ktkeywords
					WHERE `keyword`="' . $query.  '"
                                        AND created_at > "' . $startDateSql . '" AND created_at < "' . $endDateSql . '"
					GROUP BY DATE(created_at)
					ORDER BY created_at DESC
					LIMIT 365;';
                                $boundSQL = 'SELECT UNIX_TIMESTAMP(created_at) AS the_date
                                                FROM metrics_current_ktkeywords
                                                WHERE `keyword`="' . $query.  '" ORDER BY created_at DESC LIMIT 1';
				$key = wfMemcKey( 'wikiasearch' , 'metrics' , $metric, $callback, $query, $IsByMonth, $startDateSql, $endDateSql );
			}
		break;
	}
	
	$result = $wgMemc->get($key);
	if(!$result){
                $result = createJSON($sql, $boundSQL);
                $wgMemc->set( $key, $result, 300 );
        }
        
        if($outputCSV){ return arrayToCSV($result); }
        
        $result = jsonify($result);
	
	$res = 'var metricData =' . $result . ";\n\n" . $callback .'(metricData, ' . $metric . ');';
	return $res;
}

function fetchKTTrend($metric, $callback){
	global $wgRequest, $wgMemc;
	
	// go back a month by default
	$DEFAULT_TIME = 86400 * 30;
        
        $dbr =& wfGetDB( DB_MASTER );
        
	// grab some query params
	$startDate = $wgRequest->getVal("startDate");
	$endDate = $wgRequest->getVal("endDate");
	$IsByMonth = $wgRequest->getVal("month", false);
        $op = mysql_real_escape_string( $wgRequest->getVal("op", "add") );
        $outputCSV = $wgRequest->getVal("csv", false);
        
	// if there isnt a start date figure one out
	if(!is_null($startDate)){
		$endDate = !is_null($endDate) ? $endDate : time();		
	}else{
		$startDate = time() - $DEFAULT_TIME;
		$endDate = time();
	}
        
	$endDateSql = date("Y-m-d 23:59:59", $endDate);
        $startDateSql = date("Y-m-d 00:00:00", $startDate);
        
        $key = wfMemcKey( 'wikiasearch' , 'metricss' , $metric, $callback, $op, $IsByMonth, $startDateSql, $endDateSql);
	$result = $wgMemc->get($key);
	
	if($result){ return $result; }
        
	$sql = "SELECT SUM(`count`) AS the_count, op, UNIX_TIMESTAMP(created_at) AS the_date
                        FROM metrics_ktops
			WHERE op='" . $op . "' AND created_at > '" . $startDateSql . "' AND created_at < '" . $endDateSql ."'
			GROUP BY DATE(created_at)
			ORDER BY created_at DESC LIMIT 365;";

        $boundSQL = "SELECT UNIX_TIMESTAMP(created_at) AS the_date FROM metrics_ktops WHERE op=\"{$op}\" ORDER BY created_at DESC LIMIT 1";

	$result = $wgMemc->get($key);
	if(!$result){
                $result = createJSON($sql, $boundSQL);
                $wgMemc->set( $key, $result, 300 );
        }
        
        if($outputCSV){ return arrayToCSV($result); }
        
        $result = jsonify($result);
	
	$res = 'var metricData =' . $result . ";\n\n" . $callback .'(metricData, ' . $metric . ');';
	return $res;

}

// grabs the KT stats
function fetchKTStats($metric, $callback){
	global $wgRequest, $wgMemc;
	
	$dbr =& wfGetDB( DB_MASTER );
	
        $sql = "SELECT SUM(`count`) AS the_count, op
			FROM metrics_ktops
			GROUP BY op
			ORDER BY the_count DESC LIMIT 12;";
	$key = wfMemcKey( 'wikiasearch' , 'metrics' , $metric, $callback );
	
	$result = $wgMemc->get($key);	
	if($result){ return $result; }
	
	// get the i18n messages
	$msg = efWikiaSiteMetrics();
	$keys = array_keys($msg["en"]);
	$messages = array();
	foreach($keys as $key){ $messages[$key] = wfMsg($key); }

	$res = $dbr->query($sql);
	$data = array();
	
	$chartData = array();
	$maxValue = 0;
	
	while ($row = $dbr->fetchObject( $res ) ) {
		$i = array("count" => round($row->the_count), "op" => $row->op);
			
		$data[] = $i;
		$chartData[] = $row->the_count;
		$maxValue = ($row->the_count > $maxValue) ? $row->the_count : $maxValue;
	}
	
	$ret["messages"] = $messages;
	$ret["tableData"] = $data;
	
	$ret["chartData"] = $chartData;
	$ret["maxValue"] = $maxValue;
	
	$result = jsonify($ret);
	$result = 'var metricData =' . $result . ";\n\n" . $callback .'(metricData, ' . $metric . ');';
	
	$wgMemc->set( $key, $result, 180 );
	return $result;

}

// grabs data for search and KT queries
function fetchQueryData($metric, $callback){
	global $wgRequest, $wgMemc;
	
	$dbr =& wfGetDB( DB_MASTER );
	$lang = mysql_real_escape_string($wgRequest->getVal("lang", false));
	
	$hasLang = false;
	$noCount = false;
	
	if($lang){
		$key = wfMemcKey( 'wikiasearch' , 'metricsM' , $metric, $callback, $lang );
	}else{
		$key = wfMemcKey( 'wikiasearch' , 'metricsM' , $metric, $callback );
	}
	
	$result = $wgMemc->get($key);
	if($result){ return $result; }
	
	switch($metric){
		case 8: // all time top queries
		$sql = "SELECT SUM(`count`) as the_count, `query` as the_query, `language` AS lang FROM metrics_current_top_queries"
			. ($lang ? " WHERE `language`=\"{$lang}\" " : "" ) .
			" GROUP BY `query`, `language`
			ORDER BY the_count DESC LIMIT 0,500";
			$hasLang = true;
			break;
		case 9: // all time top kt keywords
		$sql = "SELECT SUM(`count`) as the_count, `keyword` AS the_query FROM metrics_current_ktkeywords
			GROUP BY `keyword`
			ORDER BY the_count DESC
			LIMIT 0,500";
			break;
		case 10: // current top queries
		$sql = 'SELECT SUM(`count`) as the_count, `query` AS the_query, `language` AS lang
			FROM metrics_current_top_queries '
			. ($lang ? " WHERE `language`=\"{$lang}\" " : "" ) .
			' GROUP BY `query`, `language`, DATE(created_at)
			ORDER BY DATE(created_at) DESC, the_count DESC LIMIT 0,500';
			$hasLang = true;
			break;
		case 11: // current top KT keywords
		$sql = 'SELECT SUM(`count`) as the_count, `keyword` AS the_query
			FROM metrics_current_ktkeywords
			GROUP BY `keyword`, DATE(created_at)
			ORDER BY DATE(created_at) DESC, the_count DESC LIMIT 0,500';
			break;
		case 15: // most wanted
			$sql = 'SELECT `keyword` AS the_query, lang
				FROM metrics_lowkt '
				. ($lang ? " WHERE `lang`=\"{$lang}\" " : "" ) .
				' ORDER BY search_count DESC, kt_count ASC LIMIT 500;';
			$hasLang = true;
			$noCount = true;
			break;
		default: break;
	}
	
	$msg = efWikiaSiteMetrics();
	$keys = array_keys($msg["en"]);
	$messages = array();
	
	// get the i18n messages
	foreach($keys as $key){ $messages[$key] = wfMsg($key); }

	$res = $dbr->query($sql);
	$data = array();
        
	while ($row = $dbr->fetchObject( $res ) ) {
		$i = array("query" => trim($row->the_query));
		
		if(!$noCount){ $i["count"] = round($row->the_count); }
		
                if($hasLang){ $i["lang"] = $row->lang; }
		
		$data[] = $i;
	}
	
        // get languages if we need em
        $languages = array();
        
        if($hasLang){
                $sql = "SELECT `language` FROM metrics_current_top_queries
                        GROUP BY `language`
                        ORDER BY `language` ASC LIMIT 500;";
                $res = $dbr->query($sql);
                while ($row = $dbr->fetchObject( $res ) ) {
                       $languages[] = $row->language; 
                }
        }
        
	$ret["messages"] = $messages;
	$ret["tableData"] = $data;
	$ret["languages"] = $languages;
        
	$result = jsonify($ret);
	$result = 'var metricData =' . $result . ";\n\n" . $callback .'(metricData, ' . $metric . ');';
	$wgMemc->set( $key, $result, 180 );
	
	return $result;
}

?>
