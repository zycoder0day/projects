
<style>
	body {
		background: #000000 repeat-x;
		font-size:17px;
		font: Arial, Helvetica, sans-serif;
		padding: 0px;
		margin: 0px;
		color: #FFF;
	}
	A:link { Color:#F00;
	}
	A {
		COLOR: #0000FF;
	}
	A:visited {
		COLOR: #0066cc; TEXT-DECORATION: none
	}
	A:active {
		COLOR: Cyan; TEXT-DECORATION: none
	}
	A:hover {
		COLOR: Cyan; TEXT-DECORATION: blink
	}	
	input[type=submit] {
    	border:bold;
    	-moz-border-radius:5px;
    	-webkit-border-radius:5px;
	}
	select {
		width:23%;
		background-color:#333;
		color:#FFF;
	}
	input[type=text]{
		background-color:#333;
		color:#FFF;
	}
	textarea[name=domain] {
		background-color:#333;
		color:#FFF;
		margin-center: 0px;
		-moz-border-radius:5px;
    	-webkit-border-radius:5px;
	}
</style>
<title>Zone-H Auto Mass Submit</title>
<link rel="shortcut icon" href="https://2.bp.blogspot.com/-epCx_Mgiw64/WDldPibaF-I/AAAAAAAAAfo/xdlwwH9PIH8-I_0D8AxJ3wNzsXdN8rxUACEw/s1600/zone-.jpg" />
<center>
<center>
<img src="https://4.bp.blogspot.com/-jwVe0Z0JEZ0/WDldRNfzkFI/AAAAAAAAAfs/eaURzoHYIB8qM8Dv2ZeAZ6_NcwAqmyxHACEw/s1600/zone-hlogo.jpg" hight="50" widht="50"><br>
<br>
<h1>Zone-H Auto Mass Submit</h1>

	<form method=POST>
	<form action="" method="post">
<input type="text" name="defacer" size="56" placeholder="Nickname" /><br>
<select name="hackmode">
<option>--------SELECT--------</option>
<option value="1">known vulnerability (i.e. unpatched system)</option>
<option value="2" >undisclosed (new) vulnerability</option>
<option value="3" >configuration / admin. mistake</option>
<option value="4" >brute force attack</option>
<option value="5" >social engineering</option>
<option value="6" >Web Server intrusion</option>
<option value="7" >Web Server external module intrusion</option>
<option value="8" >Mail Server intrusion</option>
<option value="9" >FTP Server intrusion</option>
<option value="10" >SSH Server intrusion</option>
<option value="11" >Telnet Server intrusion</option>
<option value="12" >RPC Server intrusion</option>
<option value="13" >Shares misconfiguration</option>
<option value="14" >Other Server intrusion</option>
<option value="15" >SQL Injection</option>
<option value="16" >URL Poisoning</option>
<option value="17" >File Inclusion</option>
<option value="18" >Other Web Application bug</option>
<option value="19" >Remote administrative panel access bruteforcing</option>
<option value="20" >Remote administrative panel access password guessing</option>
<option value="21" >Remote administrative panel access social engineering</option>
<option value="22" >Attack against administrator(password stealing/sniffing)</option>
<option value="23" >Access credentials through Man In the Middle attack</option>
<option value="24" >Remote service password guessing</option>
<option value="25" >Remote service password bruteforce</option>
<option value="26" >Rerouting after attacking the Firewall</option>
<option value="27" >Rerouting after attacking the Router</option>
<option value="28" >DNS attack through social engineering</option>
<option value="29" >DNS attack through cache poisoning</option>
<option value="30" >Not available</option>
</select>
<br>
<select name="reason">
<option style='display:block;width:100%;'>--------SELECT--------</option>
<option value="1" >Heh...just for fun!</option>
<option value="2" >Revenge against that website</option>
<option value="3" >Political reasons</option>
<option value="4" >As a challenge</option>
<option value="5" >I just want to be the best defacer</option>
<option value="6" >Patriotism</option>
<option value="7" >Not available</option>
</select>
<br>
<textarea name="domain" placeholder="http://sites.com/" style="width: 600px; height: 250px; margin: 5px auto; resize: none;"></textarea>
<p>(1 Domain Per Lines)</p>
<input type="submit" value="CrotZ" name="SendNowToZoneH" />
</form></form><span style="color:red">


    
    <?php
function ZoneH($url, $hacker, $hackmode,$reson, $site )
{
	$k = curl_init();
	curl_setopt($k, CURLOPT_URL, $url);
	curl_setopt($k,CURLOPT_POST,true);
	curl_setopt($k, CURLOPT_POSTFIELDS,"defacer=".$hacker."&domain1=". $site."&hackmode=".$hackmode."&reason=".$reson);
	curl_setopt($k,CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($k, CURLOPT_RETURNTRANSFER, true);
	$kubra = curl_exec($k);
	curl_close($k);
	return $kubra;
}

				if($_POST['SendNowToZoneH'])
			{
				ob_start();
				$sub = @get_loaded_extensions();
				if(!in_array("curl", $sub))
				{
					die('Curl Tidak Didukung !! ');
				}
			
				$hacker = $_POST['defacer'];
				$method = $_POST['hackmode'];
				$neden = $_POST['reason'];
				$site = $_POST['domain'];
				
				if ($hacker == "Your Zone-h Name")
				{
					die ("Masukkan Nick mu, bro !"); 
				}
				elseif($method == "--------SELECT--------") 
				{
					die("Masukkan metodenya, bro !");
				}
				elseif($neden == "--------SELECT--------") 
				{
					die("Anda Harus Pilih Alasan, bro !");
				}
				elseif(empty($site)) 
				{
					die("Masukkan site yg lu depes, bro ! ");
				}
				$i = 0;
				$sites = explode("\n", $site);
				while($i < count($sites)) 
				{
					if(substr($sites[$i], 0, 4) != "http") 
					{
						$sites[$i] = "http://".$sites[$i];
					}
					ZoneH("http://zone-h.org/notify/single", $hacker, $method, $neden, $sites[$i]);
					echo "Site : ".$sites[$i]."ERROR<br>";
					++$i;
				}
				echo "OK";
			}
			?>
			</span>
            </center>
