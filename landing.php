<?php
use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

session_start();
require_once __DIR__ . '/Facebook/autoload.php';
$fb = new Facebook([
  'app_id' => '1848754958779168',
  'app_secret' => 'cec35733cae49458e8eba7ab4b83eadf',
  'default_graph_version' => 'v2.9',
  ]);
$helper = $fb->getRedirectLoginHelper();
//$permissions = ['email']; // optional

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
    
    
    // Getting user facebook profile info
    try {
 
        $profileRequest = $fb->get('/me?fields=name,first_name,last_name,birthday,email,link,gender,locale,picture',$_SESSION['facebook_access_token']);
        $profileRequest1 = $fb->get('/me?fields=name');
        $requestPicture = $fb->get('/me/picture?redirect=false&height=310&width=300'); //getting user picture
        $profileRequest3 = $fb->get('/me?fields=gender');
        $requestFriends = $fb->get('/me/taggable_friends?fields=name&limit=20');
		$fbUserProfile = $profileRequest->getGraphNode()->asArray();
		$friends = $requestFriends->getGraphEdge();
		$birthday= $fb->get('/me?fields=age_range,timezone');
		$a = $fb->get('/me/friends?fields=name,gender');
		$b = $a ->getGraphEdge();
        $fbUserProfile1 = $profileRequest1->getGraphNode();
        $picture = $requestPicture->getGraphNode();
 		$bday = $birthday->getGraphNode();
        $fbUserProfile3 = $profileRequest3->getGraphNode();
        
		
		// If button is clicked a photo with a caption will be uploaded to facebook
		if(isset($_POST['insert'])){
     	$data = ['source' => $fb->fileToUpload(__DIR__.'/photo.jpeg'), 'message' => 'Check out this app! It is awesome http://localhost/fbapp/i.pnp '];
		$request = $fb->post('/me/photos', $data);
		$response = $request->getGraphNode()->asArray();
		header("Location: http://facebook.com");
     
    }
        
        
        
    } catch(FacebookResponseException $e) {
    
    	
        echo 'Graph returned an errrrrrror: ' . $e->getMessage();
        session_destroy();
        header("Location: ./");
        exit;
    } catch(FacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }
   // assigning a country according to the timezone
  $randomInteger = rand(0,19);
  $name= $friends[$randomInteger]['name'];
  $output = $fbUserProfile1;
  
 
  
  // getting gender
  if ($fbUserProfile['gender']=='male'){
  	$gender = 'female';
  }
  else{
  	$gender = 'male';
  }
  
    
}else{

}
?>
<html>
<head>
<title>Facebook app</title>
 <script src="html2canvas.js"></script> 
 <style>
 body {
    background-image: url("best.jpg");
    background-size:100%;
  	background-repeat: no-repeat;
  
}
    @-webkit-keyframes spin {
    0% { -webkit-transform: rotate(0deg); }
    100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
    }
    
    .button{
    background-image: url("share.png");
    margin: 20px;
    background-size: 100% 100%;
    width: 400px;
    height:100px;
    }

    .headnameline{
      padding-top: 20px;
      text-shadow: 2px 2px 4px #000000;
      color:blue;
    }

    .proimagediv{
        padding-top: 50px;
        padding-left:50px;
    }

     .probutdiv{
        padding-bottom: 10px;
    }
 </style>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script>
    var hidden = false;


setTimeout(function(){


document.getElementById("you").style.visibility='hidden';
document.getElementById("content").style.visibility='hidden';
},1);


setTimeout(function(){


document.getElementById("you").style.visibility='visible';
document.getElementById("content").style.visibility='visible';
},3000);



</script>

 
</head>
<body>

         <div class="headnameline">
            <center><h1 class="warning"><b><?php echo $name." is your Best Friend!"; ?></b></h1></center>
            </div>
            <center><section class="proimagediv">
               <div class="images">
                  <?php echo "<img src='".$picture['url']."' class='you' id='you' />"?>
               </div>
            </section></center>

            <div class="probutdiv">
            <form method="post">
               <center><input type="submit" name="insert" class="button" value=""/></center>
            </form>
            </div>
    </body>
</html>
