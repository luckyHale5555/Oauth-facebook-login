<html>
<head>
<title>Facebook App</title>
 
<style type="text/css"> 

body {
    background-image: url("best.jpg");
    background-size:100%;
  	background-repeat: no-repeat;
  
}
    .warning{font-family:Arial, Helvetica, sans-serif;color:#FFF; top:0px;position:relative; text-shadow: 4px 4px 8px #000000;font-size:40px;}
    .you { position: relative; top: -200px; left: 300px; } 
    .letter{position:absolute; top:-200px; left:800px;}
    .content{font-family: Papyrus,fantasy;top:-300px;left:820px;position:relative;font-size:20px; }
    
    
    
    
    .link{
    background-image: url("click.png");
    background-size: 100% 100%;
    width: 400px;
    height:100px;
 	display:block;
    background-repeat: no-repeat;
    position:relative;
    }
 
 
    .skull{
    width: 250px;
    height:300px;
    left:700px;
    position:relative;
    
    }
    </style>
    <script>var hidden = false;
var count = 1;
setInterval(function(){ // This function is here for the blink effect of the button
	
    document.getElementById("link").style.visibility= hidden ? "visible" : "hidden"; // setInterval will execute this infinite time
    																				// within interval of 300 seconds
  
   hidden = !hidden;

},300);


</script>

 
</head>
<body>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>

	<center><h1 class="warning" id="warning"><b>Who will become your best friend in your next life?</b></h1></center>

	
 
    </body>
</html>



<?php
// new 
session_start();
require_once __DIR__ . '/Facebook/autoload.php';
$fb = new Facebook\Facebook([
  'app_id' => '597810213915524',
  'app_secret' => '46dc32afae04eefc8c4146ee6391ea52',
  'default_graph_version' => 'v2.9',
  ]);
$helper = $fb->getRedirectLoginHelper();
//$permissions = ['email']; // optional
//$permissions = ['friendlist'];
$permissions =  array("email","user_friends");	
try {
	if (isset($_SESSION['facebook_access_token'])) {
		$accessToken = $_SESSION['facebook_access_token'];
	} else {
  		$accessToken = $helper->getAccessToken();
	}
} catch(Facebook\Exceptions\FacebookResponseException $e) {
 	// When Graph returns an error
 	echo 'Graph returned an error: ' . $e->getMessage();
  	exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
 	// When validation fails or other local issues
	echo 'Facebook SDK returned an error: ' . $e->getMessage();
  	exit;
 }
if (isset($accessToken)) {
	if (isset($_SESSION['facebook_access_token'])) {
		$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
		header('Location: http://localhost/fbapp/landing.php');
	} else {
		// getting short-lived access token
		$_SESSION['facebook_access_token'] = (string) $accessToken;
	  	// OAuth 2.0 client handler
		$oAuth2Client = $fb->getOAuth2Client();
		// Exchanges a short-lived access token for a long-lived one
		$longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
		$_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;
		// setting default access token to be used in script
		$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
	}
	// redirect the user back to the same page if it has "code" GET variable
	if (isset($_GET['code'])) {
		
		header('Location: ./');
	}
	//header('Location: http://localhost/fbapp/landing.php');

} else {
	// replace your website URL same as added in the developers.facebook.com/apps e.g. if you used http instead of https and you used non-www version or www version of your website then you must add the same here
	$loginUrl = $helper->getLoginUrl('http://localhost/fbapp/index.php', $permissions);
	
	echo '<center><a class="link" href="' . $loginUrl . '"></a></center>';

}



?>