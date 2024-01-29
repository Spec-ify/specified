<?php
// functions that are common to multiple viewer modes

//XDDDDD
function bytesToGigabytes($bytes)
{
    // 1073741824 = 1024 * 1024 * 1024
    return $bytes / 1073741824;
}

function bytesToMegabytes($bytes)
{
    // 1073741824 = 1024 * 1024
    return $bytes / 1048576;
}

function getDriveUsed($driveinput)
{
    $driveused = 0;
    foreach ($driveinput['Partitions'] as $partition) {
        $driveused += $partition['PartitionCapacity'] - $partition['PartitionFree'];
    }
    return $driveused;
}

function getDriveFree($driveinput)
{
    $drivefree = $driveinput['DiskCapacity'] - getDriveUsed($driveinput);
    return $drivefree;
}

function getDriveCapacity($driveinput)
{
    $partitioncap = 0;
    foreach ($driveinput['Partitions'] as $partition) {
        $partitioncap += $partition['PartitionCapacity'];
    }
    return $partitioncap;
}

/**
 * This is to prevent TypeErrors on malformed jsons where an array is null
 */
function safe_count($arr): int {
    if (is_countable($arr)) {
        return count($arr);
    } else {
        $bt = debug_backtrace();
        trigger_error("safe_count called on null in {$bt['file']} on line {$bt['line']}", E_USER_NOTICE);
        return 0;
    }
}

function safe_implode(string $separator, $arr): string {
    if (is_array($arr)) {
        return implode($separator, $arr);
    } else {
        $bt = debug_backtrace()[0];
        trigger_error("safe_implode called on null in {$bt['file']} on line {$bt['line']}", E_USER_NOTICE);
        return '';
    }
}

function timeConvert($time)
{

    $timeString = "";

    $days = floor($time / (60 * 60 * 24));
    $hours = floor(($time % (60 * 60 * 24)) / (60 * 60));
    $minutes = floor(($time % (60 * 60)) / 60);
    $seconds = $time % 60;

    // Initialize the string with the number of days

    if ($days) {
        $timeString = '<span';
        if ($days > 3) $timeString .= ' style="color:#BF616A;"';
        $timeString .= '>' . $days . ' day';
        if ($days != 1) {
            $timeString .= 's';
        }
        $timeString .= ', ';
    }


    // Add the number of hours to the string
    if ($hours) {
        $timeString .= $hours . ' hour';
        if ($hours != 1) {
            $timeString .= 's';
        }
        $timeString .= ', ';
    }

    // Add the number of minutes to the string
    if ($minutes) {
        $timeString .= $minutes . ' minute';
        if ($minutes != 1) {
            $timeString .= 's';
        }
        $timeString .= ', ';
    }
    // Add the number of seconds to the string A3BE8C
    if ($seconds) {
        if ($days || $hours || $minutes) $timeString .= 'and ';
        $timeString .= $seconds . ' second';
        if ($seconds != 1) {
            $timeString .= 's</span>';
        }
    }

    return $timeString;
}
