<?php

# Template for check_mk-zvolsize graphs
#
# Author: Jens Giessmann jg@handcode.de

$i=0;

$RRD = array();
foreach ($NAME as $i => $n) {
    $RRD[$n] = "$RRDFILE[$i]:$DS[$i]:MAX";
    $WARN[$n] = $WARN[$i];
    $CRIT[$n] = $CRIT[$i];
    $MIN[$n]  = $MIN[$i];
    $MAX[$n]  = $MAX[$i];
    $ACT[$n]  = $ACT[$i];
}

$zvol_name = explode(' ', $this->MACRO['DISP_SERVICEDESC'])[1];


# get longest name for indenting
$name_length = 0;
foreach ($this->DS as $KEY=>$VAL) {
    if ($name_length < strlen($VAL['NAME'])) {
        $name_length = strlen($VAL['NAME']);
    }
}

#
# graph for sizes
#
$ds_name[$i] = "vol-sizes";
$def[$i]  = "";
#$opt[$i]  = " --vertical-label 'Vol-Sizes in MB' --title '" . $this->MACRO['DISP_HOSTNAME'] . " / " . $this->MACRO['DISP_SERVICEDESC'] . "' -l 0";
$opt[$i]  = " --vertical-label 'ZVol-Sizes in MB' --title '" . $this->MACRO['DISP_HOSTNAME'] . " " . $zvol_name . "' -l 0";

# possible vars are:
# size|used|free|used_data|used_snap|comp_ratio

foreach ($this->DS as $KEY=>$VAL) {
    if (preg_match('/^(size|used|used_data|used_snap)$/', $VAL['NAME'])) {
        $name = str_pad($VAL['NAME'], $name_length);
        $def[$i] .= "DEF:var${KEY}=${VAL['RRDFILE']}:${DS[$VAL['DS']]}:AVERAGE ";
        #$def[$i] .= "AREA:var${KEY}".rrd::color($KEY).":\"". $name ."\":STACK ";
        if ($VAL['NAME'] == 'used' && !empty($VAL['CRIT'])) {
            #$def[$i] .= "LINE1:" . $VAL['CRIT'] . "#f00:\"critical\\n\" ";
            $def[$i] .= "LINE1:" . $VAL['CRIT'] . "#f00:\"\" ";
        }
        if ($VAL['NAME'] == 'size') {
            #$def[$i] .= "AREA:var${KEY}#d4d4ff:\"". $name ."\" ";
            #$def[$i] .= "HRULE:" .  . "#900:\"\" ";
            $def[$i] .= "AREA:var${KEY}#ccccc9:\"". $name ."\" ";
            $def[$i] .= "LINE1:var${KEY}#333333:\"\" ";
        } else {
            $def[$i] .= "LINE1:var${KEY}".rrd::color($KEY).":\"". $name ."\" ";
        }
        $def[$i] .= "GPRINT:var${KEY}:LAST:\"Last\: %8.1lf MB\" ";
        $def[$i] .= "GPRINT:var${KEY}:MAX:\"Max\: %8.1lf MB\" ";
        $def[$i] .= "GPRINT:var${KEY}:AVERAGE:\"Average\: %8.1lf MB\" ";
        $def[$i] .= "COMMENT:\"\\n\" ";
    }
}

# graph for compression-ratio
++$i;
$ds_name[$i] = "vol-sizes";
$def[$i]  = "";
$opt[$i]  = " --vertical-label 'compression ratio %' --title '" . $this->MACRO['DISP_HOSTNAME'] . " " . $zvol_name . "' -l 0";
foreach ($this->DS as $KEY=>$VAL) {
    if ($VAL['NAME'] == 'comp_ratio') {
        $def[$i] .= "DEF:var${KEY}=${VAL['RRDFILE']}:${DS[$VAL['DS']]}:AVERAGE ";
        $def[$i] .= "LINE1:var${KEY}".rrd::color($KEY).":\"".$VAL['NAME']."\" ";
        $def[$i] .= "GPRINT:var${KEY}:LAST:\"Last\: %5.2lf %% \" ";
        $def[$i] .= "GPRINT:var${KEY}:MAX:\"Max\: %5.2lf %% \" ";
        $def[$i] .= "GPRINT:var${KEY}:AVERAGE:\"Average\: %5.2lf %% \" ";
        $def[$i] .= "COMMENT:\"\\n\" ";
    }
}
