<?php


$geojson = new stdclass;
$geojson->type = 'FeatureCollection';
$geojson->features = array();

$count = 0;

$filename = "papua_gazetter.tsv";
//$filename = "y.tst";

$file = @fopen($filename, "r") or die("couldn't open $filename");
	
$file_handle = fopen($filename, "r");
	
$file_handle = fopen($filename, "r");
while (!feof($file_handle)) 
{
	$line = rtrim(fgets($file_handle), "\n");
	$parts = explode("\t", $line);
	
	if ($count == 0)
	{
		echo "island\tlocality\tstateProvince\tverbatimLongitude\tverbatimLatitude\tverbatimAltitude\tgeoreferenceSources\tdecimalLongitude\tdecimalLatitude\n";
	}
	else
	{
	
		//print_r($parts);
		
		$decimalLongitude = '';
		$decimalLatitude = '';
		
		if ($parts[3] != '')
		{
			if (is_numeric($parts[3]))
			{
				$decimalLongitude = $parts[3];
			}
		
			if (preg_match('/^(?<degrees>[-]?\d+),(?<minutes>\d+)$/', $parts[3], $matches))
			{
					$degrees = $matches['degrees'];
					$minutes = $matches['minutes'];
					$long = $degrees . '.' . $minutes;
					$decimalLongitude = $long;
					//exit();
			}
			if (preg_match('/^(?<hemisphere>[-])?(?<degrees>\d+)°(?<minutes>\d+)$/', $parts[3], $matches))
			{
					$hemisphere = '1.0';
					if ($matches['hemisphere'] == '-')
					{
						$hemisphere = -1.0;
					}
					$degrees = $matches['degrees'];
					$minutes = $matches['minutes'];
					$long = $degrees + ($minutes/60);
					$long *= $hemisphere;
					$decimalLongitude = $long;
					//exit();
			}
		}
		
		if ($parts[4] != '')
		{
			if (is_numeric($parts[4]))
			{
				$decimalLatitude = $parts[4];
			}
			
			if (preg_match('/^(?<degrees>[-]?\d+),(?<minutes>\d+)$/', $parts[4], $matches))
			{
					$degrees = $matches['degrees'];
					$minutes = $matches['minutes'];
					$lat = $degrees . '.' . $minutes;
					$decimalLatitude = $lat;
			}
			if (preg_match('/^(?<hemisphere>[-])?(?<degrees>\d+)°(?<minutes>\d+)$/', $parts[4], $matches))
			{
					$hemisphere = '1.0';
					if ($matches['hemisphere'] == '-')
					{
						$hemisphere = -1.0;
					}
					$degrees = $matches['degrees'];
					$minutes = $matches['minutes'];
					$lat = $degrees + ($minutes/60);
					$lat *= $hemisphere;
					$decimalLatitude = $lat;
			}
		}
		
		// Output
		$parts[] = $decimalLongitude;
		$parts[] = $decimalLatitude;
		
		if ($decimalLongitude != '')
		{
			$feature = new stdclass;
			$feature->type = 'Feature';
	
			$feature->properties = new stdclass;
			
			if ($parts[0] != '' && $parts[0] != 'XXX' && $parts[0] != '**')
			{
				$feature->properties->island = $parts[0];
			}
			if ($parts[1] != '')
			{
				$feature->properties->locality = $parts[1];
			}
			if ($parts[2] != '')
			{
				$feature->properties->stateProvince = $parts[2];
			}
			if ($parts[3] != '')
			{
				$feature->properties->verbatimCoordinates = $parts[4] . ' ' . $parts[3];
			}
			if ($parts[5] != '')
			{
				$feature->properties->verbatimAltitude = $parts[5];
			}
			if ($parts[6] != '')
			{
				$feature->properties->georeferenceSources = $parts[6];
			}
			
			$feature->geometry = new stdclass;
			$feature->geometry->type = 'Point';
			$feature->geometry->coordinates = array();
			$feature->geometry->coordinates[] = (Double)$decimalLongitude;
			$feature->geometry->coordinates[] = (Double)$decimalLatitude;
			
			$geojson->features[] = $feature;
		}	
		
		echo join("\t", $parts) . "\n";
		
	}
	$count++;
}

//echo json_encode($geojson);
file_put_contents('gazetter.geojson', json_encode($geojson));


?>