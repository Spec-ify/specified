<?php
// Doing it this way WILL break when we do eventually change the JSON Structure. 
// This supports down to v1.0, haven't checked if it works with v0.1 or v0.2 
// but honestly who would use them -K9
$base = array( 'Version' => NULL, 
                'Meta' => array ( 
                    'ElapsedTime' => NULL, 
                    'GenerationDate' => NULL
                    ), 
                'BasicInfo' => array ( 
                    'Edition' => NULL, 
                    'Version' => NULL, 
                    'FriendlyVersion' => NULL 
                ), 
                'System' => array ( 
                    'UserVariables' => NULL, 
                    'SystemVariables' => NULL, 
                    'RunningProcesses' => NULL, 
                    'Services' => NULL, 
                    'InstalledApps' => NULL, 
                    'InstalledHotfixes' => NULL 
                ), 
                'Hardware' => array ( 
                    'Ram' => NULL, 
                    'Cpu' => NULL, 
                    'Gpu' => NULL
                ), 
                'Security' => array ( 
                    'AvList' => NULL, 
                    'FwList' => NULL, 
                    'Tpm' => NULL
                ), 

                'Network' => array ( 
                    'Adapters' => NULL, 
                    'HostsFile' => NULL, 
                    'HostsFileHash' => NULL
                ), 
                'Issues' => NULL
            );
?>
