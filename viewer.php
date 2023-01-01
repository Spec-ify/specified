<?php
if(!file_exists($_GET['file'])){
    http_response_code(404);
    header("Location: 404.html");
    die();
}

$json_file = $_GET['file'];
$json = file_get_contents($json_file);
$test=0;
$json_data = json_decode($json,true);
$profile_name = explode(".", explode("/", $json_file)[1])[0];

$working_set = 0;
$cpu_percent = 0;
$process_count = count($json_data['System']['RunningProcesses']);
$i=0;
$eollist = '21H1 20H2 2004 1909 1903 1809 1803 1709 1703 1607 1511 1507';
$eol= false;
if(strpos($eollist, $json_data['BasicInfo']['FriendlyVersion'])==true){
    $eol = true;
}
$eoltext= '';
if($eol==true){
    $eoltext = "EOL";
}
else $eoltext = "Not EOL";

for($i==0;$i<$process_count;$i++){
    $working_set = $working_set + $json_data['System']['RunningProcesses'][$i]['WorkingSet'];
}
$i=0;
for($i==0;$i<$process_count;$i++){
    $cpu_percent = $cpu_percent + $json_data['System']['RunningProcesses'][$i]['WorkingSet'];
}
$ram_used = number_format($working_set/1073741824, 2, '.', '');
$green = '#A3BE8C';
$yellow = 'rgb(235, 203, 139)';
$red = 'rgb(191, 97, 106)';
$amd = 'rgb(215,27,27)';
$intel = 'rgb(8,110,224)';

$total_ram = 0;
$ram_sticks = count($json_data['Hardware']['Ram']);
$ram_stick = 0;
for($ram_stick == 0; $ram_stick<$ram_sticks;$ram_stick++){
    if($json_data['Hardware']['Ram'][$ram_stick]['Capacity']!= 0){
        $ram_size = floor($json_data['Hardware']['Ram'][$ram_stick]['Capacity']/1000);
        $total_ram = $total_ram + $ram_size;
    }}
$ram_used_percent = round((float)$ram_used / (float)$total_ram*100);
$motherboard = strtok($json_data['Hardware']['Motherboard']['Manufacturer'], " ");
if (str_contains($json_data['Hardware']['Cpu']['Name'], 'AMD')) {
    $cpu_color = $amd;
}
else{
    $cpu_color = $intel;
}
$ds=strtotime($json_data['Meta']['GenerationDate']);
function timeConvert($time) {
    // Split the time string into its parts
    $parts = preg_split('/[:.]/', $time);

    // Extract the hours, minutes, and seconds
    $hours = (int) $parts[0];
    $minutes = (int) $parts[1];
    $seconds = (int) $parts[2];

    // Initialize the string with the number of hours
    $timeString = "{$hours} day";
    if ($hours != 1) {
        $timeString .= 's';
    }

    // Add the number of minutes to the string
    if ($minutes > 0 || $seconds > 0) {
        $timeString .= ", {$minutes} hour";
        if ($minutes != 1) {
            $timeString .= 's';
        }
    }

    // Add the number of seconds to the string
    if ($seconds > 0) {
        $timeString .= ", and {$seconds} minute";
        if ($seconds != 1) {
            $timeString .= 's';
        }
    }

    return $timeString;
}
$test_time = timeConvert($json_data['BasicInfo']['Uptime']);

//Uservar paths split function
$paths = $json_data['System']['UserVariables']['Path'];

// Split the paths into an array using the semicolon as the delimiter
$path_array = explode(';', $paths);


//PUP check
$badSoftware = array('Driver Booster', 'iTop', 'Driver Easy', 'Roblox', 'ccleaner', 'Malwarebytes', 'Wallpaper Engine', 'Voxal Voice Changer', 'Clownfish Voice Changer', 'Voicemod', 'Voicemeeter', 'Microsoft Office Enterprise 2007', 'System Mechanic', 'MyCleanPC', 'DriverFix', 'Reimage Repair', 'cFosSpeed', 'Browser Assistant', 'KMS', 'Advanced SystemCare', 'AVG', 'Avast', 'salad', 'McAfee', 'Citrix', 'Norton', 'Cleaner', 'Kaspersky', 'Speedify', 'UltraUXThemePatcher', 'TronScript', 'MyWebSearch.J', 'F0C7', '485E', '8ECAA', '64CB', 'GamePlayLabs', '1DE1', 'I Want This', 'Trojan.Win32.Generic!BT', 'Ocl', '100bao', 'aim', 'expand aim-audio', 'allpeers', 'ants-p2p', 'applejuice', 'ares', 'asf-streaming', 'asproxy', 'axifile', 'azureus', 'bittorrent', 'brightcove', 'bypass', 'bypassthat', 'camfrog', 'cgiproxy', 'circumventor', 'coinhave', 'coinhive', 'coinimp', 'coinnebula', 'coralcdn-user', 'deepminer', 'direct-connect', 'dl-free', 'droidvpn', 'ebuddy', 'emule', 'fasttrack', 'feidian', 'fileguri', 'filemail', 'fileserve', 'filesonic', 'fileswire', 'firephoenix', 'flashget', 'foldera', 'foxy', 'freenet', 'ftp', 'fufox', 'funshion', 'gbridge', 'generic-p2p', 'gizmo', 'glype-proxy', 'gnu-httptunnel', 'gnunet', 'gnutella', 'goboogy', 'google-drive-web', 'google-talk', 'expand gtalk-file-transfer', 'gtunnel', 'guardster', 'hamachi', 'hopster', 'horde', 'hotfile', 'hotline', 'http-proxy', 'http-tunnel', 'httport', 'imesh', 'ip-in-ip', 'irc', 'expand irc-base', 'expand irc-dcc-file-transfer', 'jabber', 'jap', 'jsecoin', 'kazaa', 'kino', 'kproxy', 'kugoo', 'libero-video', 'lotus-notes', 'expand lotus-notes-base', 'manolito', 'meebo', 'expand meebo-file-transfer', 'megashare', 'megashares', 'mineralt', 'msn', 'expand msn-file-transfer', 'mute', 'mydownloader', 'neonet', 'nerohut', 'netlog', 'netmeeting', 'nntp', 'openft', 'packetix-vpn', 'pando', 'peerenabler', 'perfect-dark', 'phproxy', 'pingfu', 'poco', 'privax', 'proxeasy', 'proxono', 'psiphon', 'qdown', 'qq', 'expand qq-file-transfer', 'r-exec', 'r-services', 'rediffbol', 'expand rediffbol-file-transfer', 'sharebase.to', 'smtp', 'expand smtp-base', 'socks', 'socks2http', 'sopcast', 'soribada', 'soulseek', 'ssh-tunnel', 'stickam', 'storage.to', 'stumbleupon', 'suresome', 'swapper', 'tesla', 'thecircle', 'totodisk', 'transferbigfiles', 'trinoo', 'tudou', 'turboupload', 'vnc', 'expand vnc-base', 'vnc-http', 'warez-p2p', 'webdav', 'webmine', 'wickr', 'winmx', 'winny', 'woofiles', 'x11', 'xunlei', 'youku', 'expand youku-base', 'yunpan360', 'expand yunpan360-base', 'zelune', '4shared', '51.com', 'expand 51.com-webdisk', '8x8', 'accellion', 'activesync', 'adobe-connect', 'expand adobe-meeting-file-transfer', 'adobe-connectnow', 'expand adobe-connectnow-file-transfer', 'adrive', 'afreeca', 'aim', 'expand aim-base', 'expand aim-file-transfer', 'aim-express', 'expand aim-express-base', 'aim-mail', 'all-slots-casino', 'amazon-cloud-drive', 'expand amazon-cloud-drive-base', 'expand amazon-cloud-drive-uploading', 'amazon-unbox', 'ameba-now', 'expand ameba-now-base', 'expand ameba-now-posting', 'aol-proxy', 'appcelerator', 'apple-appstore', 'atom', 'att-connect', 'avaya-webalive', 'expand avaya-webalive-file-transfer', 'avoidr', 'aws-workdocs', 'babelgum', 'badongo', 'baidu-hi', 'expand baidu-hi-file-transfer', 'baofeng', 'base-crm', 'bebo', 'expand bebo-posting', 'bigupload', 'bitcoin', 'bittorrent-sync', 'blackberry', 'blog-posting', 'boldchat-logmein', 'bomgar', 'bonpoo', 'boxnet', 'expand boxnet-uploading', 'caihong', 'camo-proxy', 'canvas', 'carbonite', 'expand carbonite-uploading', 'expand carbonite-downloading', 'cgi-irc', 'clip2net', 'cloudshare', 'concur', 'expand concur-base', 'crossloop', 'crypto-loot', 'cyworld', 'daap', 'dailymotion', 'datto-backupify', 'daum', 'expand daum-mail', 'expand daum-cloud', 'daum-touch', 'dialpad', 'diino', 'divshare', 'docstoc', 'expand docstoc-base', 'expand docstoc-uploading', 'dontcensorme', 'dostupest', 'drivehq', 'drop.io', 'dropboks', 'dropbox', 'expand dropbox-base', 'e-signlive', 'easy-share', 'eatlime', 'editgrid', 'egnyte', 'expand egnyte-base', 'elluminate', 'eroom-net', 'eventbrite', 'facebook', 'expand facebook-file-sharing', 'expand facebook-video', 'expand facebook-base', 'expand facebook-apps', 'expand facebook-posting', 'fastmail', 'fetch.io', 'file-host', 'filecatalyst-direct', 'filedropper', 'filer.cx', 'filestack', 'expand filestack-downloading', 'flash', 'flock', 'fluxiom', 'fly-proxy', 'freedome', 'freegate', 'friendvox', 'frozenway', 'fs2you', 'fuze-meeting', 'expand fuze-meeting-file-sharing', 'g.ho.st', 'gadu-gadu', 'genesys', 'expand genesys-base', 'ghostsurf', 'glide', 'gmail', 'expand gmail-base', 'expand gmail-enterprise', 'gmx-mail', 'goagent', 'gogobox', 'google-base', 'google-buzz', 'google-chat', 'google-desktop', 'google-hangouts', 'expand google-hangouts-chat', 'google-talk', 'expand google-talk-base', 'expand gtalk-voice', 'gpass', 'gpass-proxy', 'graboid-video', 'gyao', 'h.323', 'hadoop', 'expand hdfs', 'hangame', 'hola-unblocker', 'homepipe', 'hotmail', 'http-audio', 'http-nsri', 'http-video', 'i2p', 'icmp', 'icmpsh', 'icq', 'ifile.it', 'ifolder', 'ilohamail', 'im-plus', 'imap', 'imgur', 'expand imgur-base', 'imhaha', 'imo', 'instan-t', 'expand instan-t-file-transfer', 'ipfs', 'issuu', 'expand issuu-uploading', 'join-me', 'expand join-me-file-transfer', 'jubii', 'justin.tv', 'jxta', 'kerio-vpn', 'korea-webmail', 'labnol-proxy', 'laconica', 'leapfile', 'limelight', 'live-meeting', 'live-mesh', 'expand live-mesh-base', 'expand live-mesh-sync', 'logmein', 'lotus-sametime', 'lotuslive', 'expand lotuslive-meeting-file-sharing', 'expand lotuslive-file-sharing', 'mail.com', 'mail.ru', 'expand mail.ru-base', 'mail.ru-agent', 'expand mail.ru-agent-file-transfer', 'mailchimp', 'me2day', 'mediafire', 'megaupload', 'mendeley', 'expand mendeley-base', 'expand mendeley-uploading', 'mercurial', 'expand mercurial-base', 'metacafe', 'mgoon', 'mibbit', 'mixi', 'expand mixi-posting', 'mobile-me', 'mogulus', 'ms-exchange', 'ms-groove', 'ms-onedrive', 'expand ms-onedrive-base', 'expand ms-onedrive-downloading', 'ms-rdp', 'ms-update', 'msn', 'expand msn-base', 'expand msn-voice', 'msn2go', 'myspace', 'expand myspace-mail', 'expand myspace-base', 'expand myspace-posting', 'nakido-flag', 'nate-video', 'nateon-im', 'expand nateon-im-base', 'expand nateon-file-transfer', 'naver-ndrive', 'ndmp', 'netease-mail', 'netload', 'niconico-douga', 'nintendo-wfc', 'noteworthy', 'expand noteworthy-base', 'octopz', 'odnoklassniki', 'expand odnoklassniki-base', 'office-live', 'okurin', 'omnidrive', 'opendoor', 'openmeetings', 'openomy', 'opera-mini', 'oridus-nettouch', 'orkut', 'orsiso', 'outblaze-mail', 'outlook-web', 'p10', 'pandora-tv', 'party-poker', 'pastebin', 'expand pastebin-base', 'pichat', 'ping-tunnel', 'pinterest', 'expand pinterest-posting', 'plaxo', 'poker-stars', 'pop3', 'pownce', 'pplive', 'ppstream', 'pptp', 'puffin', 'putlocker', 'qq', 'expand qq-base', 'qq-download', 'qq-mail', 'qqlive', 'qvod', 'radmin', 'rapidshare', 'rdp2tcp', 'reddit', 'expand reddit-posting', 'reduh', 'remobo', 'remoteview', 'ringcentral', 'rlogin', 'roundcube', 'rsh', 'rtmp', 'rtmpe', 'rtmpt', 'ruckus', 'saba-centra-meeting', 'scotty', 'screencast', 'expand screencast-base', 'screenconnect', 'scribd', 'expand scribd-uploading', 'seamless-phenom', 'second-life', 'expand second-life-base', 'secret', 'secure-access', 'secureserver-mail', 'security-kiss', 'sendspace', 'share-p2p', 'sharebox', 'sharefile', 'expand sharefile-base', 'expand sharefile-uploading', 'expand sharefile-downloading', 'showmypc', 'silent-circle', 'simplehelp', 'simplite-msn', 'sina-uc', 'expand sina-uc-web-disk', 'sina-weibo', 'expand sina-weibo-base', 'sip', 'sipviaheader-nat', 'sky-player', 'skydrive', 'expand skydrive-base', 'expand skydrive-uploading', 'sliderocket', 'smtp', 'expand smtp-starttls', 'socialtv', 'sosbackup', 'source-engine', 'spark', 'spotnet', 'sproutsocial', 'squirrelmail', 'ssh', 'ssl', 'stealthnet', 'steganos-vpn', 'sugarsync', 'surrogafier', 'synology-cloudstation', 'synology-dsm', 'tagoo', 'taku-file-bin', 'tcp-over-dns', 'teamup-calendar', 'expand teamup-calendar-base', 'expand teamup-calendar-uploading', 'techinline', 'telenet-webmail', 'tftp', 'thinkfree', 'thwapr', 'expand thwapr-uploading', 'timbuktu', 'tor2web', 'totoexpress', 'tubes', 'tudou-speedup', 'tunnelbear', 'turboshare', 'tvants', 'twig', 'twitch', 'twitter', 'expand twitter-posting', 'expand twitter-uploading', 'expand twitter-messaging', 'txp', 'ultrasurf', 'uploading', 'usejump', 'usermin', 'uusee', 'vagaa', 'veetle', 'vimeo', 'expand vimeo-base', 'vkontakte', 'expand vkontakte-base', 'expand vkontakte-mail', 'vnn', 'vsee', 'vtun', 'vtunnel', 'vyew', 'web-browsing', 'web-crawler', 'web-de-mail', 'webaim', 'webhard', 'webot', 'webqq', 'xdrive', 'yahoo-douga', 'yahoo-im', 'expand yahoo-im-base', 'expand yahoo-voice', 'expand yahoo-file-transfer', 'yandex-mail', 'yoics', 'yoomba', 'your-freedom', 'yourfilehost', 'yousendit', 'expand yousendit-base', 'expand yousendit-uploading', 'youtube', 'expand youtube-base', 'expand youtube-safety-mode', 'expand youtube-uploading', 'expand youtube-streaming', 'zamzar', 'zango', 'zbigz', 'zenbe', 'zoho-im');
$normalizedArray = array_map('strtolower',$badSoftware);
// Set up the reference list
$referenceListInstalled = $json_data['System']['InstalledApps'];
$referenceListRunning = $json_data['System']['RunningProcesses'];
// Set up the list to check
$checkList = $badSoftware;
$pupsfoundInstalled = array(
        ''
);
$internalpupsinstalled = array_map('strtolower',$pupsfoundInstalled);

$pupsfoundRunning = array(
    ''
);
$internalpupsrunning = array_map('strtolower',$pupsfoundRunning);

foreach ($normalizedArray as $searchitem) {
    foreach ($referenceListInstalled as $object){
    $checkobject = strtolower($object['Name']);
    // Check if the object's name is in the array of names
    if (str_contains( $checkobject,$searchitem)) {
        // If the name is in the array, see if the name has already been passed into the PUPs found array
        foreach($internalpupsinstalled as $internalpup){
            //If not, add it to the pupsfound array
        if(!str_contains($internalpup,$searchitem)){
            $pupsfoundInstalled[] = $object['Name'];
        }
        }
    }
    }
}
foreach ($normalizedArray as $searchitem) {
    foreach ($referenceListRunning as $object){
        $checkobject = strtolower($object['ProcessName']);
        // Check if the object's name is in the array of names
        if (str_contains( $checkobject,$searchitem)) {
            foreach($internalpupsrunning as $internalpup){
            if(!str_contains($internalpup,$searchitem)){
                $pupsfoundrunning[] = $object['ProcessName'];
            }}
        }
    }
}
function bytesToGigabytes($bytes) {
    return $bytes / 1073741824;
}
?>
<!doctype html><html lang="en">
<meta content="text/html;charset=UTF-8" http-equiv="content-type"/>
<head>
    <meta charset="utf-8"/>
    <title>Specified</title>
    <meta content="width=device-width,initial-scale=1" name="viewport"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css"/>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"/>

    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/5.0.0/mdb.dark.min.css" rel="stylesheet">
    <link href="static/css/main.css" rel="stylesheet">

    <meta name="og:title" content="<?= $json_data["BasicInfo"]["Hostname"] ?>" />
    <meta name="og:site_name" content="Specify" />
    <meta name="og:description" content="Generated on <?= $json_data["Meta"]["GenerationDate"] ?>" />
    <meta name="og:type" content="data.specify_result" />
</head>
<body>
<noscript>You need to enable JavaScript to run this app.</noscript>
<div id="main">
    <!--$-->
    <button
            type="button"
            class="btn btn-info btn-floating btn-lg"
            id="btn-back-to-top"
    >
        <i class="fas fa-arrow-up"></i>
    </button>
    <header class="header_header">
        <a class="logo" href="index.html">
            <img src="assets/logo.png" height="25em">
        </a>
        <button type="button" class="btn btn-info" id="CollapseToggle">Collapse All</button>
        <select title="mappings" id="ModeToggle">

            <optgroup
                    label="View">
                <option value="auto">Dark Mode</option>
                <option value="none">(WIP)Light Mode</option>
            </optgroup>
        </select>

        </span></header><main>
        <div class="specify">
            <div class="controls">
                <div class="textbox title">
                    <span>Profile  <span id="filename"><?=$profile_name?></span> created
                <?=date("F j, Y, g:i a",$ds)?>, runtime
					<?=$json_data['Meta']['ElapsedTime']." ms,"?>
                Under Specify Version <?=$json_data['Version']?>
				</span>
                </div>
                <input class="searchbar" type="text" placeholder="Search..." id="searchBarDiv"  onkeyup="searchFunction()">
            </div>
            <div  id="main">
            <div class="metadata_metadata expanded">
                <div class="widgets_widgets widgets" id="hardware_widgets" data-hide="false">
                    <div class="widget widget-cpu hover">
                        <h1>CPU</h1>
                        <div class="widget-values">
                            <div class="widget-value">
                                <div style="color: <?=$cpu_color?>;">
                                    <?=$json_data['Hardware']['Cpu']['Name']?>
                                </div>
                                <div>Callsign</div>
                            </div>
                        </div>
                    </div>
                    <div class="widget widget-ram hover" type="button" data-mdb-toggle="modal" data-mdb-target="#ramModal">
                        <h1>RAM</h1>
                        <div class="widget-values">
                            <?php
                            $ram_sticks = count($json_data['Hardware']['Ram']);
                            $ram_stick = 0;
                            for($ram_stick; $ram_stick<$ram_sticks;$ram_stick++){
                                $current_ram_stick = $ram_stick + 1;
                                if($json_data['Hardware']['Ram'][$ram_stick]['Capacity']!= 0){
                                    $ram_size = floor($json_data['Hardware']['Ram'][$ram_stick]['Capacity']/1000);
                                    echo '
							<div class="widget-value">
								<div style="color:'.$green. '">'.$ram_size.'GB</div>
								<div>DIMM'.$current_ram_stick.'</div>
							</div>';
                                }
                                else
                                    echo '
							<div class="widget-value">
								<div style="color: rgb(215,27,27);">--</div>
								<div>DIMM' .$current_ram_stick.'</div>
							</div>';
                            }

                            ?>
                        </div>
                    </div>

                    <div class="modal fade " id="ramModal" tabindex="-1" aria-labelledby="ramModal" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="ModalLabel">RAM info</h5>
                                    <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th scope="col">DIMM</th>
                                            <th scope="col">Manufacturer</th>
                                            <th scope="col">Model</th>
                                            <th scope="col">Speed</th>
                                            <th scope="col">Capacity</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $ram_sticks = count($json_data['Hardware']['Ram']);
                                        $ram_stick = 0;
                                        for($ram_stick; $ram_stick<$ram_sticks;$ram_stick++){
                                            $ram_size = floor($json_data['Hardware']['Ram'][$ram_stick]['Capacity']);
                                            $ram_speed = $json_data['Hardware']['Ram'][$ram_stick]['ConfiguredSpeed'];
                                            echo
                                                '<tr>
                                                <td>'.$json_data['Hardware']['Ram'][$ram_stick]['DeviceLocation'].'</td>
                                                <td>'.$json_data['Hardware']['Ram'][$ram_stick]['Manufacturer'].'</td>
                                                <td>'.$json_data['Hardware']['Ram'][$ram_stick]['PartNumber'].'</td>
                                                <td>'.$ram_speed.'MHz</td>
                                                <td>'.$ram_size.'MB</td>
                                            </tr>';}
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="widget widget-board hover" type="button" data-mdb-toggle="modal" data-mdb-target="#boardModal">
                        <h1>Motherboard</h1>
                        <div class="widget-values">
                            <div class="widget-value">
                                <div style="color: <?=$green?>;">
                                    <?=$motherboard?>
                                </div>
                                <div>OEM</div>
                            </div>
                            <div class="widget-value">
                                <div style="color: <?=$green?>;">
                                    <?=$json_data['Hardware']['Motherboard']['Product']?>
                                </div>
                                <div>Chipset</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="boardModal" tabindex="-1" aria-labelledby="boardModal" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="ModalLabel">Board Information</h5>
                                    <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <table class="table">
                                        <thead>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>Manufacturer</td>
                                            <td><?=$json_data['Hardware']['BiosInfo'][0]['Manufacturer']?></td>
                                        </tr>
                                        <tr>
                                            <td>Version</td>
                                            <td><?=$json_data['Hardware']['BiosInfo'][0]['SMBIOSBIOSVersion']?></td>
                                        </tr>
                                        <tr>
                                            <td>Release Date</td>
                                            <?php
                                            $biosdate = $json_data['Hardware']['BiosInfo'][0]['ReleaseDate'];

                                            // Use the DateTime class to parse the date string
                                            $datetime = DateTime::createFromFormat('YmdHis.uO', $biosdate);

                                            // Format the date using the desired MMDDYYYY format
                                            $formattedbiosdate = $datetime->format('m/d/Y');
                                            ?>
                                            <td><?=$formattedbiosdate?></td>
                                        </tr>
                                        <tr>
                                            <td>Base</td>
                                            <td><?=$json_data['Hardware']['BiosInfo'][0]['BIOSVersion'][2]?></td>
                                        </tr>
                                        <tr>
                                            <td>Serial Number</td>
                                            <td><?=$json_data['Hardware']['BiosInfo'][0]['SerialNumber']?></td>
                                        </tr>
                                        <?php
                                        if($json_data['Security']['Tpm']['IsEnabled_InitialValue']){
                                            $tpm_status = 'Enabled';}
                                        else{
                                            $tpm_status ='Disabled';
                                        }

                                        echo

                                            '<tr>
                                                <td>TPM Status</td>
                                                <td>'.$tpm_status.'</td>
                                            </tr>';
                                        echo
                                            '<tr>
                                                <td>Manufacturer Version</td>
                                                <td>'.$json_data['Security']['Tpm']['ManufacturerVersionInfo'].' '.$json_data['Security']['Tpm']['ManufacturerVersion'].'</td>
                                            </tr>';
                                        echo
                                            '<tr>
                                                <td>Version</td>
                                                <td>'.$json_data['Security']['Tpm']['SpecVersion'].'</td>
                                            </tr>';
                                        ?>
                                        </tbody>
                                    </table>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="widget widget-gpu hover" type="button" data-mdb-toggle="modal" data-mdb-target="#gpuModal">
                        <h1>GPU</h1>
                        <div class="widget-values">
                            <div class="widget-value">
                                <div style="color: <?=$green?>;">
                                    <?=$json_data['Hardware']['Monitors'][0]['Name']?>
                                </div>
                                <div>Model</div>
                            </div>
                        </div>

                    </div>
                    <div class="modal fade" id="gpuModal" tabindex="-1" aria-labelledby="gpuModal" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="ModalLabel">GPU and Monitor Info</h5>
                                    <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th scope="col">Name</th>
                                            <th scope="col">VRAM</th>
                                            <th scope="col">Mode</th>
                                            <th scope="col">Monitor</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $monitor_count = count($json_data['Hardware']['Monitors']);
                                        $monitor = 0;

                                        for($monitor;$monitor<$monitor_count;$monitor++){

                                            echo
                                                '<tr>
                                                <td>'.$json_data['Hardware']['Monitors'][$monitor]['Name'].'</td>
                                                <td>'.$json_data['Hardware']['Monitors'][$monitor]['DedicatedMemory'].'</td>
                                                <td>'.$json_data['Hardware']['Monitors'][$monitor]['CurrentMode'].'</td>
                                                <td>'.$json_data['Hardware']['Monitors'][$monitor]['MonitorModel'].'</td>
                                            </tr>';}
                                        ?>
                                        </tbody>
                                    </table>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="widget widget-os hover" type="button" data-mdb-toggle="modal" data-mdb-target="#osModal">
                        <h1>Operating System</h1>
                        <div class="widget-values">
                            <div class="widget-value">
                                <div class="widget-value">
									<span
                                            style="color: <?=$green?>;">
										<?=$json_data['BasicInfo']['Edition']?>
									</span>
                                </div>
                                <div>
                                    <?=$json_data['BasicInfo']['FriendlyVersion']?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade " id="osModal" tabindex="-1" aria-labelledby="osModal" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="ModalLabel">System Information</h5>
                                    <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <table class="table">
                                        <thead>

                                        </thead>
                                        <tbody>
                                        <?php
                                            echo
                                                '<tr>
                                                <td>Edition</td>
                                                <td>'.$json_data['BasicInfo']['Edition'].'</td>
                                            </tr>';
                                                                                    echo
                                                                                        '<tr>
                                                <td>Version</td>
                                                <td>'.$json_data['BasicInfo']['Version'].'</td>
                                            </tr>';
                                        echo
                                            '<tr>
                                                <td>Friendly Version</td>
                                                <td>'.$json_data['BasicInfo']['FriendlyVersion'].'</td>
                                            </tr>';
                                        echo
                                            '<tr>
                                                <td>Install Date</td>
                                                <td>'.$json_data['BasicInfo']['InstallDate'].'</td>
                                            </tr>';
                                        echo
                                            '<tr>
                                                <td>Uptime</td>
                                                <td>'.$json_data['BasicInfo']['Uptime'].'</td>
                                            </tr>';
                                        echo
                                            '<tr>
                                                <td>Hostname</td>
                                                <td>'.$json_data['BasicInfo']['Hostname'].'</td>
                                            </tr>';
                                        echo
                                            '<tr>
                                                <td>Username</td>
                                                <td>'.$json_data['BasicInfo']['Username'].'</td>
                                            </tr>';
                                        echo
                                            '<tr>
                                                <td>Domain</td>
                                                <td>'.$json_data['BasicInfo']['Domain'].'</td>
                                            </tr>';
                                        if($json_data['Security']['UacEnabled']){
                                            $uac_status = 'Enabled';}
                                        else{
                                            $uac_status ='Disabled';
                                        }

                                        echo

                                            '<tr>
                                                <td>UAC Status</td>
                                                <td>'.$uac_status.'</td>
                                            </tr>';
                                        echo
                                            '<tr>
                                                <td>UAC Level</td>
                                                <td>'.$json_data['Security']['UacLevel'].'</td>
                                            </tr>';
                                        echo
                                            '<tr>
                                                <td>Boot Mode</td>
                                                <td>'.$json_data['BasicInfo']['BootMode'].'</td>
                                            </tr>';
                                        echo
                                            '<tr>
                                                <td>Boot State</td>
                                                <td>'.$json_data['BasicInfo']['BootState'].'</td>
                                            </tr>';
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="widget widget-board hover"  type="button" data-mdb-toggle="modal" data-mdb-target="#nicModal">
                        <h1>NIC</h1>
                        <div class="widget-values">
                            <div class="widget-value">
                                <div style="color: <?=$green?>;">
                                    <?php
                                    $current_adapter = 0;
                                    $exit=false;
                                    $adapter_count = count($json_data['Network']['Adapters2']);
                                    for($current_adapter;$current_adapter < $adapter_count; $current_adapter++){
                                        if($json_data['Network']['Adapters2'][$current_adapter]['ConnectorPresent'] && $exit==false){
                                            echo $json_data['Network']['Adapters2'][$current_adapter]['InterfaceDescription'];
                                            $exit = true;
                                        };
                                    };

                                    ?>

                                </div>
                                <div>OEM</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade " id="nicModal" tabindex="-1" aria-labelledby="nicModal" aria-hidden="true">
                        <div class="modal-dialog modal-fullscreen">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="ModalLabel">System Information</h5>
                                    <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <table id="nicTable" class="table">
                                        <thead>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>MAC</th>
                                        <th>Gateway(s)</th>
                                        <th>DHCP State</th>
                                        <th>DHCP Server</th>
                                        <th>DNS Domain</th>
                                        <th>DNS Host name</th>
                                        <th>DNS IPs</th>
                                        <th>IP(s)</th>
                                        <th>Subnet</th>
                                        </thead>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="widgets_widgets widgets" id="storage_widgets" data-hide="false">
                    <div class="widget widget-temps hover"  type="button" data-mdb-toggle="modal" data-mdb-target="#partitionsModal">
                        <h1>Partitions</h1>
                        <div class="widget-values">
                            <div class="widget-value">
                                <div >
                                All partitions
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade " id="partitionsModal" tabindex="-1" aria-labelledby="partitionsModal" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="ModalLabel">Partitions and Drive information</h5>
                                    <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="metadata-detail" id="accordionTablesPartitions">
                                        <?php
                                            foreach($json_data['Hardware']['Storage'] as $current_drive){
                                                $driveKey = array_search($current_drive, $json_data['Hardware']['Storage']);
                                        echo '
                                        <div class="accordion">
                                            <h1 class="accordion-header" id="partitionsTableButton'.$driveKey.'">
                                                <button
                                                        class="accordion-button"
                                                        type="button"
                                                        data-mdb-toggle="collapse"
                                                        data-mdb-target="#partitionModal'.$driveKey.'"
                                                        aria-expanded="true"
                                                        aria-controls="partitionModal'.$driveKey.'"
                                                >
                                                   '. $current_drive['DeviceName'].'
                                                </button></h1>
                                            <div class="metadata-detail tablebox jsondata accordion-item accordion-collapse collapse storagemodal" id="partitionModal'.$driveKey.'">
                                                <table id="partitionsTable'.$driveKey.'Info" class="table">
                                                    <thead>
                                                    <th>Name</th>
                                                    <th>SN</th>
                                                    <th>#</th>
                                                    <th>Capacity</th>
                                                    <th>Free</th>
                                                    </thead>
                                                    <tbody>
                                                    '.'<td>'.$current_drive['DeviceName'].'</td>'.'
                                                    '.'<td>'.$current_drive['SerialNumber'].'</td>'.'
                                                    '.'<td>'.$current_drive['DiskNumber'].'</td>'.'
                                                    '.'<td>'.floor(bytesToGigabytes($current_drive['DiskCapacity'])).'GB</td>'.'
                                                    '.'<td>'.floor(bytesToGigabytes($current_drive['DiskFree'])).'GB</td>'.'
                                                    </tbody>
                                                </table>
                                                <h5>Partitions</h5>
                                                <table id="partitionsTable'.$driveKey.'" class="table">
                                                    <thead>
                                                    <th>Label</th>
                                                    <th>Capacity</th>
                                                    <th>Free</th>
                                                    <th>FS Type</th>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>';}
                                        ?>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    $drives_amount = count($json_data['Hardware']['Storage']);
                    $drive = 0;
                    $smart = 0;

                    for($drive=0; $drive < $drives_amount; $drive++)
                    {
                        if(is_countable($json_data['Hardware']['Storage'][$drive]['SmartData'])){
                            $smart_amount = count($json_data['Hardware']['Storage'][$drive]['SmartData']);
                        }
                        $current_drive = $drive+1;
                        $drive_size_raw = $json_data['Hardware']['Storage'][$drive]['DiskCapacity'];
                        $drive_free_raw = $json_data['Hardware']['Storage'][$drive]['DiskFree'];
                        $drive_taken_raw = $drive_size_raw - $drive_free_raw;
                        $drive_size = floor($drive_size_raw)/1073741824;
                        $drive_taken = floor($drive_taken_raw)/1073741824;
                        if($drive_taken!=0){
                        $drive_percentage = round((float)$drive_taken / (float)$drive_size*100);
                        }
                        else $drive_percentage = 0;
                        $flavor_color = '';

                        if($drive_percentage>=80){
                            $flavor_color = $red;
                        }
                        elseif($drive_percentage>=50 && $drive_percentage <=79){
                            $flavor_color = $yellow;
                        }
                        elseif($drive_percentage>=0 && $drive_percentage <=49){
                            $flavor_color = $green;
                        }

                        echo '
					<div class="widget widget-disk hover" type="button" data-mdb-toggle="modal" data-mdb-target="#driveModal' . $drive . '">
						<h1>' . $json_data['Hardware']['Storage'][$drive]['DeviceName'] . '</h1>
						<div class="widget-values">
							<div class="widget-value">
								<div class="widget-single-value">
									<span
                                                                   style="color:' . $flavor_color . ';">' . (int)$drive_taken . ' GB</span>
									<span>/</span>
									<span>' . (int)$drive_size . ' GB</span>
								</div>
								<div>' . $drive_percentage . '%</div>
							</div>
						</div>
					</div>
					<div class="modal fade" id="driveModal' . $drive . '" tabindex="-1" aria-labelledby="driveModal" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="ModalLabel">' . $json_data['Hardware']['Storage'][$drive]['DeviceName'] . '</h5>
									<button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
								</div>
								<div class="modal-body">
									<table class="table">
										<thead>
											<tr>
												<th scope="col">Index</th>
												<th scope="col">Name</th>
												<th scope="col">Value</th>
											</tr>
										</thead>
										<tbody>';
                        if (isset($json_data['Hardware']['Storage'][$drive]['SmartData'])) {
                            for ($smart = 0; $smart < $smart_amount; $smart++) {
                                echo
                                    '
											<tr>
												<th scope="row">' . $json_data['Hardware']['Storage'][$drive]['SmartData'][$smart]['Id'] . '</th>
												<td>' . $json_data['Hardware']['Storage'][$drive]['SmartData'][$smart]['Name'] . '</td>
												<td>' . $json_data['Hardware']['Storage'][$drive]['SmartData'][$smart]['RawValue'] . '</td>
											</tr>';
                            }
                        }
                        echo
                        '
										</tbody>
									</table>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
								</div>
							</div>
						</div>
					</div>';
                    }
                    ?>
                </div>
                <div class="widgets_widgets widgets" id="realtime_widgets" data-hide="false">
                    <div class="widget widget-cpu hover">
                        <h1>CPU
                            <span>(Used)</span>
                        </h1>
                        <div class="widget-values">
                            <div class="widget-value">
                                <div class="widget-value">
									<span
                                            style="color: <?=$green?>;">
										<?=$ram_used?>%
									</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="widget widget-memory hover">
                        <h1>Memory
                            <span>(Used)</span>
                        </h1>
                        <div class="widget-values">
                            <div class="widget-value">
                                <div class="widget-single-value">
									<span
                                            style="color: <?=$green?>;">
										<?=$ram_used?>GB
									</span>
                                    <span>/</span>
                                    <span>
										<?=$total_ram?>GB
									</span>
                                </div>
                                <div>
                                    <?=$ram_used_percent?>%
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="widget widget-temps hover"  type="button" data-mdb-toggle="modal" data-mdb-target="#tempsModal">
                        <h1>Temps</h1>
                        <div class="widget-values">
                            <div class="widget-value">
                                <div style="color: <?=$cpu_color?>;">
                                    🔥
                                </div>
                                <div>C</div>
                            </div>
                        </div>
                    </div>
                        <div class="modal fade " id="tempsModal" tabindex="-1" aria-labelledby="tempsModal" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="ModalLabel">Temps</h5>
                                        <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <table id="tempTable" class="table">
                                            <thead>
                                            <th>Hardware</th>
                                            <th>Sensor</th>
                                            <th>Temperature</th>
                                            </thead>
                                        </table>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <div class="widget widget-audio hover"  type="button" data-mdb-toggle="modal" data-mdb-target="#audioModal">
                        <h1>Audio Devices</h1>
                        <div class="widget-values">
                                <?php
                                $internalaudio=0;
                                $externalaudio=0;
                                foreach($json_data['Hardware']['AudioDevices'] as $audioDevice){
                                    if(str_contains(strtolower($audioDevice['DeviceID']),'hdaudio')){
                                        $internalaudio = $internalaudio + 1;
                                    }
                                    else {
                                        $externalaudio = $externalaudio+1;
                                    }
                                    ;
                                }
                                echo '<div class="widget-value" style="color:'.$green.';">Internal : '.$internalaudio.'</div>';
                                echo '<div class="widget-value" style="color:'.$yellow.';">External : '.$externalaudio.'</div>';
                                ?>
                        </div>
                    </div>
                    <div class="modal fade " id="audioModal" tabindex="-1" aria-labelledby="audioModal" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="ModalLabel">Audio Devices</h5>
                                    <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <table id="audioTable" class="table">
                                        <thead>
                                        <th>Device ID</th>
                                        <th>Manufacturer</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                        </thead>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="widget widget-power hover"type="button" data-mdb-toggle="modal" data-mdb-target="#powerModal">
                        <h1>Power Profile/Battery
                        </h1>
                        <div class="widget-values">
                            <div class="widget-value">
                                <div class="widget-value">

										<?php
                                        if($json_data['System']['PowerProfiles']){
                                        $profile_count = count($json_data['System']['PowerProfiles']);
                                        $profile_color = '';
                                        $current_profile = '';
                                        if($profile_count != 0){
                                        for($profile=0;$profile<$profile_count;$profile++){
                                            if($json_data['System']['PowerProfiles'][$profile]['IsActive']){
                                                $current_profile =  $json_data['System']['PowerProfiles'][$profile]['ElementName'];
                                            }
                                        }
                                        if(str_contains(strtolower($current_profile),'balanced')){
                                            $profile_color = '#EBCB8B';
                                        }
                                        elseif(str_contains(strtolower($current_profile),'high')){
                                            $profile_color = '#D08770';
                                        }
                                        elseif(str_contains(strtolower($current_profile),'saver')){
                                            $profile_color = '#A3BE8C';
                                        }

                                        echo '<span style="color:'.$profile_color.';">'.$current_profile.'</span>';
                                        }
                                        }
                                        ?>
                                    <div style="font-size: 10pt;">Current Profile</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade " id="powerModal" tabindex="-1" aria-labelledby="powerModal" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="ModalLabel">Power/Battery</h5>
                                    <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <table id="powerTable" class="table">
                                        <thead>
                                        <th>Description</th>
                                        <th>Element</th>
                                        <th>Instance Path</th>
                                        <th>Status</th>
                                        </thead>
                                    </table>
                                    <h4>Battery</h4>
                                    <table id="batteryTable" class="table">
                                        <thead>
                                        <th>Name</th>
                                        <th>Manufacturer</th>
                                        <th>Chemistry</th>
                                        <th>Design Capacity</th>
                                        <th>Current Full Charge Capacity</th>
                                        </thead>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="textbox metadata-detail" id="notes">
                    <ul class="metadata-detail-controls">
                        <li class="selected">Notes</li>
                        <li class="pups_button">PUPs</li>
                        <li class="variables_button">Variables</li>
                        <li class="browsers_button">Browsers</li>
                    </ul>
                    <div class="metadata-detail-content">
                        <p>The OS is<?php
                            $oscheck = '';
                            if($json_data['BasicInfo']['FriendlyVersion'] == "22H2"){
                                $oscheck = 'Up-to-date';
                            }
                            else $oscheck = 'Not Up-to-Date';

                            ?>
                            <span><?=$oscheck?></span> running version "<span><?=$json_data['BasicInfo']['FriendlyVersion']?></span>".
                        </p>
                        <p>The OS is currently <span><?=$eoltext?></span>.

                        </p>
                        <p>The detected AV is "<span><?=$json_data['Security']['AvList'][0]?></span>".

                        </p>

                        <p>The process was created by Specify
                            <span>
								<?=$json_data['Version']?>
							</span>
                        </p>

                        <p>The current computer uptime is
                            <span>
								<?=$test_time?>.
							</span>
                        </p>
                        <p>Specify was running for
                            <span>
								<?=$json_data['Meta']['ElapsedTime']."ms"?>
							</span>.
                        </p>
                        <?php
                        if($json_data['System']['UsernameSpecialCharacters']==true){
                        echo '
                        <p>
                            Username found with <span>Special Characters</span>
                        </p>
                        ';}
                        if($json_data['System']['OneDriveCommercialPathLength']!=null){
                            echo '
                        <p>
                            OneDrive Path Length : <span>'.$json_data['System']['OneDriveCommercialPathLength'].'</span>
                            OneDrive Name Length : <span>'.$json_data['System']['OneDriveCommercialNameLength'].'</span>
                        </p>
                        ';
                        }
                        ?>
                        <br>
                        <h4>Rudimentary Registry Checks</h4>
                        <br>
                        <?php
                        if($json_data['System']['StaticCoreCount']!=false){
                            echo '
                            <p>
                                <span>Static Core Count</span> found set.
                            </p>
                            ';
                        }
                        if($json_data['System']['RecentMinidumps']!=0){
                            echo '
                            <p>
                                There have been <span>'.$json_data['System']['RecentMinidumps'].' Minidumps found</span>
                            </p>>
                            
                            ';
                        }
                        foreach($json_data['System']['ChoiceRegistryValues'] as $regkey){
                            if($regkey['Value']!= null && $regkey['Name']!= "NetworkThrottlingIndex"){
                                echo '
                                <p>Registry Value <span>'.$regkey['Name'].'</span> found set, value of <span>'.$regkey['Value'].'</span></p>
                                ';
                            }
                        }
                        if($json_data['System']['ChoiceRegistryValues'][2]['Value']!=10){
                            echo '
                            <p>Network Throttling Index found set at <span>'.$json_data['System']['ChoiceRegistryValues'][2]['Value'].'</span></p>
                            ';
                        }
                        ?>
                    </div>
                </div>
                <div class="textbox metadata-detail" id="pups">
                    <ul class="metadata-detail-controls">
                        <li class="notes_button">Notes</li>
                        <li class="selected">PUPs</li>
                        <li class="variables_button">Variables</li>
                        <li class="browsers_button">Browsers</li>
                    </ul>
                    <div class="metadata-detail-content jsondata">
                        <table id="pupsTableInstalled" class="table">
                        <?php
                        foreach($pupsfoundInstalled as $pup){
                            echo '<tr><td>'.$pup.' Found installed</td></tr>';
                        }
                        echo '</table><table id="pupsTableRunning" class="table">';
                        foreach($pupsfoundrunning as $pup){
                            echo '<tr><td>'.$pup.' Found Running</td></tr>';
                        }
                        ?>
                        </table>
                    </div>
                </div>
                <div class="textbox metadata-detail" id="variables">
                    <ul class="metadata-detail-controls">
                        <li class="notes_button">Notes</li>
                        <li class="pups_button">PUPs</li>
                        <li class="selected">Variables</li>
                        <li class="browsers_button">Browsers</li>
                    </ul>
                    <div class="metadata-detail-content jsondata">
                        <table id="variables_table" class="table">
                            <thead>
                                <th>Field</th>
                                <th>Value</th>
                            </thead>
                            <tbody>
                            <?php
                            echo '<h1>User Variables</h1>';
                            $uservar_keys = array_keys($json_data['System']['UserVariables']);
                            foreach($uservar_keys as $uservar){
                                if($uservar != "Path"){
                                echo '<tr><td>'.$uservar.'</td><td>'.$json_data['System']['UserVariables'][$uservar].'</td></tr>';
                                };
                            };
                            echo '<table class="table"><thead><th>Path Variables</th></thead>';
                            foreach ($path_array as $path) {
                                // Only print the path if it starts with a Windows-accepted drive letter
                                if (preg_match('/^[C-Z]:/', $path)) {
                                    echo '<tr><td>'.$path .'</td></tr>';
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                        <table id="variables_table" class="table">
                            <thead>
                            <th>Field</th>
                            <th>Value</th>
                            </thead>
                            <tbody>
                            <?php
                            echo '<h1>System Variables</h1>';
                            $uservar_keys = array_keys($json_data['System']['SystemVariables']);
                            foreach($uservar_keys as $uservar){
                                if($uservar != "Path"){
                                    echo '<tr><td>'.$uservar.'</td><td>'.$json_data['System']['SystemVariables'][$uservar].'</td></tr>';
                                };
                            }?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="textbox metadata-detail" id="browsers">
                    <ul class="metadata-detail-controls">
                        <li class="notes_button">Notes</li>
                        <li class="pups_button">PUPs</li>
                        <li class="variables_button">Variables</li>
                        <li class="selected">Browsers</li>
                    </ul>
                    <div class="metadata-detail-content jsondata">
                        <div class="widgets_widgets widgets">

                                <?php
                                $browser_icon= '';
                                $default_browser= '';
                                foreach($json_data['System']['BrowserExtensions'] as $browser){
                                    if(str_contains(strtolower($json_data['System']['DefaultBrowser']),strtolower($browser['Name']))){
                                        $default_browser = "(Default)";
                                    }
                                    else $default_browser = '';
                                    $browsercheckformat = strtolower($browser['Name']);

                                    if(str_contains($browsercheckformat,'chrome')){
                                        $browser_icon= 'assets/chrome.png';
                                    }
                                    elseif(str_contains($browsercheckformat,'firefox')){
                                        $browser_icon='assets/firefox.png';
                                    }
                                    elseif(str_contains($browsercheckformat,'edge')){
                                        $browser_icon='assets/edge.png';
                                    }
                                    elseif(str_contains($browsercheckformat,'opera')){
                                        $browser_icon='assets/gx.png';
                                    }
                                    elseif(str_contains($browsercheckformat,'brave')){
                                        $browser_icon='assets/brave.png';
                                    }
                                    elseif(str_contains($browsercheckformat,'vivaldi')){
                                        $browser_icon='assets/vivaldi.png';
                                    }
                                    else{
                                        $browser_icon='#';
                                    }
                                    echo '<div class="widget widget-browser hover"  type="button" data-mdb-toggle="modal" data-mdb-target="#'.$browser['Name'].'Modal">
<div class="widget-values">
                                            <div class="widget-value">
                                            <h1>'.$browser['Name'].$default_browser.'</h1>
                                            <img class="center" height="48px" width="48px" src="'.$browser_icon.'">
                                            </div>
                                          </div>
                                          </div>
                                    ';
                                echo '<div class="modal fade " id="'.$browser['Name'].'Modal" tabindex="-1" aria-labelledby="'.$browser['Name'].'Modal" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="ModalLabel">'.$browser['Name'].' Extensions</h5>
                                        <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body" id="browserContainer'.$browser['Name'].'">';
                                foreach($browser['Profiles']as $browserprofile){
                                    $profileKey = array_search($browserprofile,$browser['Profiles']);
                                    echo '
                                        <h2>'.$browser['Name'].' Profile "'.$browser['Profiles'][$profileKey]['name'].'"</h2>
                                        <table id="'.$browser['Name'].'Profile'.$profileKey.'Table" class="table">
                                        </table>';
                                };
                                echo '
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>';
                                }
                                ?>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="textbox metadata-detail" id="accordionTablesDevices">
                        <div class="accordion">
                            <h1 class="accordion-header" id="devicesTableButton">
                                <button
                                        class="accordion-button"
                                        type="button"
                                        data-mdb-toggle="collapse"
                                        data-mdb-target="#devices"
                                        aria-expanded="true"
                                        aria-controls="devices"
                                >
                                    Devices
                                </button></h1>
                            <div class="textbox metadata-detail tablebox widget jsondata accordion-item accordion-collapse collapse" id="devices">
                                <table id="devicesTable" class="table">
                                    <thead>
                                    <th>Description</th>
                                    <th>Name</th>
                                    <th>DID</th>
                                    <th>Status</th>
                                    </thead>
                                </table>
                            </div>
                            <h1 class="accordion-header" id="driversTableButton">
                                <button
                                        class="accordion-button"
                                        type="button"
                                        data-mdb-toggle="collapse"
                                        data-mdb-target="#drivers"
                                        aria-expanded="true"
                                        aria-controls="drivers"
                                >
                                    Drivers
                                </button></h1>
                            <div class="textbox metadata-detail tablebox widget jsondata accordion-item accordion-collapse collapse" id="drivers">
                                <table id="driversTable" class="table">
                                    <thead>
                                    <th>Name</th>
                                    <th>Friendly Name</th>
                                    <th>Manufacturer</th>
                                    <th>DID</th>
                                    <th>Version</th>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <div>
                <div class="textbox metadata-detail" id="accordionTablesApps">
                <div class="accordion">
                    <h1 class="accordion-header" id="runningProcessesButton">
                        <button
                                class="accordion-button"
                                type="button"
                                data-mdb-toggle="collapse"
                                data-mdb-target="#runningProcesses"
                                aria-expanded="true"
                                aria-controls="runningProcesses"
                        >
                            Running Processes
                        </button></h1>
                <div class="textbox metadata-detail tablebox widget jsondata accordion-item accordion-collapse collapse" id="runningProcesses">
                    <table id="runningProcessesTable" class="table">
                        <thead>
                        <th>Name</th>
                        <th>Path</th>
                        <th>PID</th>
                        <th>RAM</th>
                        <th>CPU</th>
                        </thead>
                    </table>
                </div>
                    <h1 class="accordion-header" id="installedAppButton">
                        <button
                                class="accordion-button"
                                type="button"
                                data-mdb-toggle="collapse"
                                data-mdb-target="#installedApp"
                                aria-expanded="true"
                                aria-controls="installedApp"
                        >
                            Installed Apps
                        </button></h1>
                <div class="textbox metadata-detail tablebox widget jsondata accordion-item accordion-collapse collapse" id="installedApp">
                    <table id="installedAppTable" class="table">
                        <thead>
                        <th>Name</th>
                        <th>Version</th>
                        <th>Install Date</th>
                        </thead>
                    </table>
                </div>

                </div>
                </div>
            </div>
                <div>
                    <div class="textbox metadata-detail" id="accordionTablesServices">
                        <div class="accordion">
                            <h1 class="accordion-header" id="servicesTableButton">
                                <button
                                        class="accordion-button"
                                        type="button"
                                        data-mdb-toggle="collapse"
                                        data-mdb-target="#services"
                                        aria-expanded="true"
                                        aria-controls="services"
                                >
                                    Services
                                </button></h1>
                            <div class="textbox metadata-detail tablebox widget jsondata accordion-item accordion-collapse collapse" id="services">
                                <table id="servicesTable" class="table">
                                    <thead>
                                    <th>Caption</th>
                                    <th>Name</th>
                                    <th>Path</th>
                                    <th>Start Mode</th>
                                    <th>State</th>
                                    </thead>
                                </table>
                            </div>
                            <h1 class="accordion-header" id="tasksTableButton">
                                <button
                                        class="accordion-button"
                                        type="button"
                                        data-mdb-toggle="collapse"
                                        data-mdb-target="#tasks"
                                        aria-expanded="true"
                                        aria-controls="tasks"
                                >
                                    Tasks
                                </button></h1>
                            <div class="textbox metadata-detail tablebox widget jsondata accordion-item accordion-collapse collapse" id="tasks">
                                <table id="tasksTable" class="table">
                                    <thead>
                                    <th>Name</th>
                                    <th>Path</th>
                                    <th>State</th>
                                    <th>Active</th>
                                    <th>Author</th>
                                    <th>Triggers</th>
                                    </thead>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
                <div>
                    <div class="textbox metadata-detail" id="accordionTablesNetwork">
                        <div class="accordion">
                            <h1 class="accordion-header" id="netconTableButton">
                                <button
                                        class="accordion-button"
                                        type="button"
                                        data-mdb-toggle="collapse"
                                        data-mdb-target="#netcon"
                                        aria-expanded="true"
                                        aria-controls="netcon"
                                >
                                    Network Connections
                                </button></h1>
                            <div class="textbox metadata-detail tablebox widget jsondata accordion-item accordion-collapse collapse" id="netcon">
                                <table id="netconTable" class="table">
                                    <thead>
                                    <th>Local IP</th>
                                    <th>Local Port</th>
                                    <th>Remote IP</th>
                                    <th>Remote Port</th>
                                    <th>PID</th>
                                    </thead>
                                </table>
                            </div>
                            <h1 class="accordion-header" id="routesTableButton">
                                <button
                                        class="accordion-button"
                                        type="button"
                                        data-mdb-toggle="collapse"
                                        data-mdb-target="#routes"
                                        aria-expanded="true"
                                        aria-controls="routes"
                                >
                                    Routes Table
                                </button></h1>
                            <div class="textbox metadata-detail tablebox widget jsondata accordion-item accordion-collapse collapse" id="routes">
                                <table id="routesTable" class="table">
                                    <thead>
                                    <th>Route</th>
                                    <th>Destination</th>
                                    <th>Interface</th>
                                    <th>Mask</th>
                                    <th>Metric</th>
                                    <th>Next Hop</th>
                                    </thead>
                                </table>
                            </div>
                            <h1 class="accordion-header" id="hostsTableButton">
                                <button
                                        class="accordion-button"
                                        type="button"
                                        data-mdb-toggle="collapse"
                                        data-mdb-target="#hosts"
                                        aria-expanded="true"
                                        aria-controls="hosts"
                                >
                                    Hosts File
                                </button></h1>
                            <div class="textbox metadata-detail tablebox widget jsondata accordion-item accordion-collapse collapse" id="hosts">
                                <?php
                                $hoststext = nl2br($json_data['Network']['HostsFile']);
                                ?>
                                    <p style="font-size: 10pt;"><?=$hoststext?> </p>
                            </div>
                        </div>
                    </div>
                </div>

        </div>
        </div>
        <span>Massive Shoutout to <a href="https://spark.lucko.me/" target="_blank">Spark</a></span>
    </main>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.2/jquery.min.js" integrity="sha512-tWHlutFnuG0C6nQRlpvrEhE4QpkG1nn2MOUMWmUeRePl4e3Aki0VB6W1v3oLjFtd0hVOtRQ9PHpSfN6u6/QXkQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script
        src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/5.0.0/mdb.min.js"
        type="text/javascript"
></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.13.1/sc-2.0.7/datatables.min.js"></script>
<script defer="defer" src="static/js/main.js"></script>
</html>
