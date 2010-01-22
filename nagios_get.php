<?php 
$file = fopen("/var/spool/nagios/status.dat", "r") or exit("Unable to open file!"); //path to nagios file
$refreshvalue = 10; //value in seconds to refresh page

$collastcheck = true; //true/false to show last checked date column in table
$colhost = true; //true/false to show host column in table
$colstatusinfo = true; //true/false to show status info/plugin info column in table
$colservice = true; //true/false to show service type column column in table

$pagetitle = "NSN ASA NOC Dashboard";

#$thedate = date('Y-m-d H:i:s');
$thedate = date('d-m-Y H:i:s');
$showthedate = false;
?>
<?php 
//i may have missed some column displays here so add them in if you need them
if (($collastcheck == true) and ($colhost == true) and ($colstatusinfo == true) and ($colservice == false)) {
    echo("<table width=90% border=0 class=boldtable align=center>");
}
if (($collastcheck == false) and ($colhost == true) and ($colstatusinfo == true) and ($colservice == false)) {
    echo("<table width=90% border=0 class=boldtable align=center>");
}
if (($collastcheck == false) and ($colhost == false) and ($colstatusinfo == true) and ($colservice == false)) {
    echo("<table width=90% border=0 class=boldtable align=center>");
}
if (($collastcheck == false) and ($colhost == true) and ($colstatusinfo == false) and ($colservice == false)) {
    echo("<table width=90% border=0 class=boldtable align=center>");
}
if (($collastcheck == true) and ($colhost == true) and ($colstatusinfo == true) and ($colservice == true)) {
    echo("<table width=90% border=0 class=boldtable align=center>");
}
?>
<tr class="head">
    <?php 
    if ($collastcheck == true) {
        echo("<th width=210>Last Checked</th>");
    }
    if ($colhost == true) {
        echo("<th width=140>Host</th>");
    }
    if ($colstatusinfo == true) {
        echo("<th width=290>Status Info</th>");
    }
    if ($colservice == true) {
        echo("<th width=150>Service</td>");
    }
    ?>
</tr>
<?php 
function dashdisplay($dashline, $collastcheck, $colhost, $colstatusinfo, $colservice) { //function to display array data into table with colors/font etc

    $dashstatus = substr($dashline, 0, strpos($dashline, ','));
    $dashline = substr($dashline, strpos($dashline, ',') + 1, strlen($dashline));
    switch ($dashstatus) {
        case 0:
            $dashstatus = "UP";
            $trclass = "up";
            break;
        case 1:
            $dashstatus = "WARNING";
            $trclass = "warning";
            break;
        case 2:
            $dashstatus = "CRITICAL";
            $trclass = "critical";
            break;
        case 3:
            $dashstatus = "DISABLED";
            $trclass = "disabled";
            break;
    }
    $dashack = substr($dashline, 0, strpos($dashline, ','));
    $dashline = substr($dashline, strpos($dashline, ',') + 1, strlen($dashline));
    
    if ($dashack == "") { //if somehow the acknowledgement state is blank, set it to 0 (acknowledged)
        $dashack = 0;
    }
    
    $dashdate = substr($dashline, 0, strpos($dashline, ',') - 1);
    $dashline = substr($dashline, strpos($dashline, ',') + 1, strlen($dashline));
    
    $dashhost = substr($dashline, 0, strpos($dashline, ',') - 1);
    $dashline = substr($dashline, strpos($dashline, ',') + 1, strlen($dashline));
    
    $dashplugin = substr($dashline, 0, strrpos($dashline, ',') - 1);
    $dashline = substr($dashline, strrpos($dashline, ',') + 1, strlen($dashline));
    
    $dashservice = $dashline;
    
?>
<?php if($dashack == 0): ?>
<tr class="<?php print $trclass ?>">
<?php 
    if ($collastcheck == true) {
        echo("<td>".date("Y-m-d H:i:s", $dashdate)."</td>");
    }
    if ($colhost == true) {
        echo("<td>".$dashhost."</td>");
    }
    if ($colstatusinfo == true) {
        echo("<td class=\"statusinfo\">".$dashplugin."</td>");
    }
    if ($colservice == true) {
        if ($dashservice == "") {
            echo("<td>HOST PING</td>");
        } else {
            echo("<td>".$dashservice."</td>");
        }
    }
endif; ?>
</tr>
<?php 
} //end display function

//arrays for different fields in nagios status.dat
$hostarray = array();
$servicearray = array();
$statearray = array();
$pluginarray = array();
$checkarray = array();
$ackarray = array();
$disarray = array();

//arrays for status of hosts/services
$finaluparray = array();
$finalwarnarray = array();
$finalcritarray = array();
$finaldisarray = array();

//field to check in nagios status.dat
$hostname = 'host_name=';
$servicedes = 'service_description=';
$currstate = 'current_state=';
$pluginout = 'plugin_output=';
$lastcheck = 'last_check=';
$ackcheck = 'been_acknowledged=';
$discheck = 'active_checks_enabled=';

//counters for loops
$hostcount = 0;
$servicecount = 0;
$currcount = 0;
$plugcount = 0;
$lastcount = 0;
$discount = 0;
$disttlcount = 0;
$ackcount = 0;
$ttlcount = 0;
$check = 0;
$okcount = 0;
$warncount = 0;
$critcount = 0;

while (!feof($file)) { //begin while through nagios status.dat
    $line = fgets($file);
    
    //strpos to check for field line by line
    $hostpos = strpos($line, $hostname);
    $servicepos = strpos($line, $servicedes);
    $currpos = strpos($line, $currstate);
    $plugpos = strpos($line, $pluginout);
    $lastpos = strpos($line, $lastcheck);
    $dispos = strpos($line, $discheck);
    $ackpos = strpos($line, $ackcheck);
    
    if ($hostpos !== false) {
        $hostcount++;
        $hostarray[$hostcount] = substr($line, strpos($line, '=') + 1, strlen($line));
        $check = 1;
    }
    
    $check = 0;
    
    if ($servicepos !== false) {
        $servicecount++;
        $servicearray[$servicecount] = substr($line, strpos($line, '=') + 1, strlen($line));
        $check = 1;
    }
    
    $check = 0;
    
    if ($currpos !== false) {
        $currcount++;
        $statearray[$currcount] = substr($line, strpos($line, '=') + 1, strlen($line));
        $check = 1;
    }
    
    $check = 0;
    
    if ($plugpos !== false) {
        if (strpos($line, "long_plugin_output=") === false) {
            $plugcount++;
            $pluginarray[$plugcount] = substr($line, strpos($line, '=') + 1, strlen($line));
            $check = 1;
        }
    }
    
    $check = 0;
    
    if ($lastpos !== false) {
        $lastcount++;
        $checkarray[$lastcount] = substr($line, strpos($line, '=') + 1, strlen($line));
        $check = 1;
    }
    
    $check = 0;
    
    #if (isset($servicearray[$servicecount]) && $servicearray[$servicecount] != "") { //if the host has a service being checked
    if ($servicearray[$servicecount] != "") { //if the host has a service being checked
        if ($dispos !== false) {
            $discount++;
            $disarray[$discount] = substr($line, strpos($line, '=') + 1, strlen($line));
            $check = 1;
        }
        
        $check = 0;
        
        if ($ackpos !== false) {
            $ackcount++;
            $ackarray[$ackcount] = substr($line, strpos($line, '=') + 1, strlen($line));
            $check = 1;
        }
    }
    
    #if (isset($servicearray[$servicecount]) && $servicearray[$servicecount] == "") { //if the host has no service being checked
    if ($servicearray[$servicecount] == "") { //if the host has no service being checked
        if ($ackpos !== false) {
            $ackcount++;
            $ackarray[$ackcount] = substr($line, strpos($line, '=') + 1, strlen($line));
            $check = 1;
        }
        
        $check = 0;
        
        if ($dispos !== false) {
            $discount++;
            $disarray[$discount] = substr($line, strpos($line, '=') + 1, strlen($line));
            $check = 1;
        }
    }
    
    if ($check == 1) { //if for final array building
        $ttlcount++;
        
        if ($disarray[$ttlcount] == 1) { //if for active checks being enabled (1)
        
            if ($statearray[$ttlcount] == 0) { //if for state being up/ok (0 for acknowledgements, you dont acknowledge an up service/host)
                $okcount++;
                $finaluparray[$okcount] = $statearray[$ttlcount].",0,".$checkarray[$ttlcount].",".$hostarray[$ttlcount].",".$pluginarray[$ttlcount].",".$servicearray[$servicecount];
            }
            
            if ($statearray[$ttlcount] == 1) { //if for state being warning
                $warncount++;
                if ($ackarray[$ttlcount] == "") {
                    $ackarray[$ttlcount] = 0;
                }
                
                $finalwarnarray[$warncount] = $statearray[$ttlcount].",".$ackarray[$ttlcount].",".$checkarray[$ttlcount].",".$hostarray[$ttlcount].",".$pluginarray[$ttlcount].",".$servicearray[$servicecount];
            }
            
            if ($statearray[$ttlcount] == 2) { //if for state being critical
                $critcount++;
                
                if ($ackarray[$ttlcount] == "") {
                    $ackarray[$ttlcount] = "0";
                }
                
                $finalcritarray[$critcount] = $statearray[$ttlcount].",".$ackarray[$ttlcount].",".$checkarray[$ttlcount].",".$hostarray[$ttlcount].",".$pluginarray[$ttlcount].",".$servicearray[$servicecount];
            }
        } //end if for active checks being enabled
        
        //if active checks are 0 then checking is disabled (0), the 3 represents the disabled state
        if ($disarray[$ttlcount] == 0) {
            $disttlcount++;
            $finaldisarray[$disttlcount] = "3,0,".$checkarray[$ttlcount].",".$hostarray[$ttlcount].",".$pluginarray[$ttlcount].",".$servicearray[$servicecount];
        }
        
    } //end if for final array building
}//end while loop through nagios status.dat

fclose($file);

//loops sending arrays and column details to display function
//loops run in order > critical, warnings, up, disabled

for ($l = 1; $l <= $critcount; $l++) {
    dashdisplay($finalcritarray[$l], $collastcheck, $colhost, $colstatusinfo, $colservice);
}

for ($m = 1; $m <= $warncount; $m++) {
    dashdisplay($finalwarnarray[$m], $collastcheck, $colhost, $colstatusinfo, $colservice);
}

#for ($n = 1; $n <= $okcount; $n++) {
#   dashdisplay($finaluparray[$n],$collastcheck,$colhost,$colstatusinfo,$colservice);
#}

#for ($o = 1; $o <= $disttlcount; $o++) {
#   dashdisplay($finaldisarray[$o],$collastcheck,$colhost,$colstatusinfo,$colservice);
#}
?>
</table>
