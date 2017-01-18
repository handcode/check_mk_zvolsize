<?php

# Template fir check_mk-zvolsize graphs

# Based on:
# Default Template used if no other template is found.
# Copyright (c) 2006-2010 Joerg Linge (http://www.pnp4nagios.org)

# Define some colors ..
#
$_WARNRULE = '#FFFF00';
$_CRITRULE = '#FF0000';
$_AREA     = '#256aef';
$_LINE     = '#000000';
#
# Initial Logic ...
#

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

#
# graph with all data
#
$ds_name[$i] = "vol-sizes";
$def[$i]  = "";
$opt[$i]  = " --vertical-label 'Vol-Sizes in MB' --title '" . $this->MACRO['DISP_HOSTNAME'] . " / " . $this->MACRO['DISP_SERVICEDESC'] . "' -l 0";

# possible vars are:
# size|used|free|used_data|used_snap|comp_ratio

foreach ($this->DS as $KEY=>$VAL) {
    if (preg_match('/^(size|used|used_data|used_snap|comp_ratio)$/', $VAL['NAME'])) {
        $def[$i] .= "DEF:var${KEY}=${VAL['RRDFILE']}:${DS[$VAL['DS']]}:AVERAGE ";
        #$def[$i] .= "AREA:var${KEY}".rrd::color($KEY).":\"".$VAL['NAME']."\":STACK ";
        $def[$i] .= "LINE:var${KEY}".rrd::color($KEY).":\"".$VAL['NAME']."\" ";
        $def[$i] .= "GPRINT:var${KEY}:LAST:\"Last %5.0lf%S\" ";
        $def[$i] .= "GPRINT:var${KEY}:MAX:\"Max %5.0lf%S\" ";
        $def[$i] .= "GPRINT:var${KEY}:AVERAGE:\"Average %5.1lf%S\" ";
        $def[$i] .= "COMMENT:\"\\n\" ";
    }
}


return;

foreach ($this->DS as $KEY=>$VAL) {


    $maximum  = "";
    $minimum  = "";
    $critical = "";
    $crit_min = "";
    $crit_max = "";
    $warning  = "";
    $warn_max = "";
    $warn_min = "";
    $vlabel   = " ";
    $lower    = "";
    $upper    = "";

    if ($VAL['WARN'] != "" && is_numeric($VAL['WARN']) ){
        $warning = $VAL['WARN'];
    }
    if ($VAL['WARN_MAX'] != "" && is_numeric($VAL['WARN_MAX']) ) {
        $warn_max = $VAL['WARN_MAX'];
    }
    if ( $VAL['WARN_MIN'] != "" && is_numeric($VAL['WARN_MIN']) ) {
        $warn_min = $VAL['WARN_MIN'];
    }
    if ( $VAL['CRIT'] != "" && is_numeric($VAL['CRIT']) ) {
        $critical = $VAL['CRIT'];
    }
    if ( $VAL['CRIT_MAX'] != "" && is_numeric($VAL['CRIT_MAX']) ) {
        $crit_max = $VAL['CRIT_MAX'];
    }
    if ( $VAL['CRIT_MIN'] != "" && is_numeric($VAL['CRIT_MIN']) ) {
        $crit_min = $VAL['CRIT_MIN'];
    }
    if ( $VAL['MIN'] != "" && is_numeric($VAL['MIN']) ) {
        $lower = " --lower=" . $VAL['MIN'];
        $minimum = $VAL['MIN'];
    }
    if ( $VAL['MAX'] != "" && is_numeric($VAL['MAX']) ) {
        $maximum = $VAL['MAX'];
    }
    if ($VAL['UNIT'] == "%%") {
        $vlabel = "%";
        $upper = " --upper=101 ";
        $lower = " --lower=0 ";
    }
    else {
        $vlabel = $VAL['UNIT'];
    }

    $opt[$KEY] = '--vertical-label "VL' . $vlabel . '" --title "' . $this->MACRO['DISP_HOSTNAME'] . ' / ' . $this->MACRO['DISP_SERVICEDESC'] . '"' . $upper . $lower;
    $ds_name[$KEY] = $VAL['LABEL'] . '-X';
    $def[$KEY]  = rrd::def     ("var1", $VAL['RRDFILE'], $VAL['DS'], "AVERAGE");
    $def[$KEY] .= rrd::gradient("var1", "ccccff", "cccc44", rrd::cut($VAL['NAME'],16), 20);
    $def[$KEY] .= rrd::line1   ("var1", $_LINE );
    #$def[$KEY] .= rrd::gprint  ("var1", array("LAST","MAX","AVERAGE"), "%3.4lf %S".$VAL['UNIT']);
    $def[$KEY] .= rrd::gprint  ("var1", array("LAST","MAX","AVERAGE"), "%3.0lf MB");
    if ($warning != "") {
        $def[$KEY] .= rrd::hrule($warning, $_WARNRULE, "Warning  $warning \\n");
    }
    if ($warn_min != "") {
        $def[$KEY] .= rrd::hrule($warn_min, $_WARNRULE, "Warning  (min)  $warn_min \\n");
    }
    if ($warn_max != "") {
        $def[$KEY] .= rrd::hrule($warn_max, $_WARNRULE, "Warning  (max)  $warn_max \\n");
    }
    if ($critical != "") {
        $def[$KEY] .= rrd::hrule($critical, $_CRITRULE, "Critical $critical \\n");
    }
    if ($crit_min != "") {
        $def[$KEY] .= rrd::hrule($crit_min, $_CRITRULE, "Critical (min)  $crit_min \\n");
    }
    if ($crit_max != "") {
        $def[$KEY] .= rrd::hrule($crit_max, $_CRITRULE, "Critical (max)  $crit_max \\n");
    }
    $def[$KEY] .= rrd::comment("Template " . $VAL['TEMPLATE'] . "\\r");
}
?>


