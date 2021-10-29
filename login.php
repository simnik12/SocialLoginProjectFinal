<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
  header("location: index.php");
  exit;
}

// Include config file
require_once "config.php";
// Generate a random hash and store in the session for security
$state = hash('sha256', microtime(TRUE) . rand() . $_SERVER['REMOTE_ADDR']);
$_SESSION['state'] = $state;
$git_login_url =  'https://github.com/login/oauth/authorize?response_type=code&client_id=' . GIT_CLIENT_ID . '&scope=' . GIT_SCOPE . '&state=' . $state . '&redirect_uri=' . GIT_REDIRECT_URL;

$Lin_login_url =  'https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=' . LIN_CLIENT_ID . '&scope=' . LIN_SCOPE . '&state=' . $state . '&redirect_uri=' . LIN_REDIRECT_URL;
if (isset($_GET['code']) &&  isset($_GET['state'])  && isset($_GET['git'])) {
  if (!$_GET['state'] || $_SESSION['state'] != $_GET['state']) {
    echo "<pre>";
    print_r($_GET);
  }

  $access_token_url = 'https://github.com/login/oauth/access_token?' . http_build_query([
    'client_id' => GIT_CLIENT_ID,
    'client_secret' => GIT_CLIENT_SECRET,
    'state' => $state,
    'code' => $_GET['code']
  ]);
  $apiURL = filter_var($access_token_url, FILTER_VALIDATE_URL) ? $access_token_url : 'https://api.github.com/user?access_token=' . $access_token_url;
  $context  = stream_context_create([
    'http' => [
      'user_agent' => 'CodexWorld GitHub OAuth Login',
      'header' => 'Accept: application/json'
    ]
  ]);
  $response = @file_get_contents($apiURL, false, $context);
  $response =  $response ? json_decode($response) : $response;
  //echo "<pre>";
  //print_r($response);die;
  $access_token = @$response->access_token;
  header("location: ajax/ajax.php?git_login&token=" . $access_token);
  exit;
}


if (isset($_GET['code'])) { // get linkedin access token

  $code = $_GET['code'];
  $urlCurl = 'https://www.linkedin.com/oauth/v2/accessToken?grant_type=authorization_code&redirect_uri=' . LIN_REDIRECT_URL . '&client_id=' . LIN_CLIENT_ID . '&client_secret=' . LIN_CLIENT_SECRET . '&code=' . $code;
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => $urlCurl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET'
  ));
  $response = json_decode(curl_exec($curl), true);
  curl_close($curl);
  //echo "<pre>";
  //print_r($response);die;
  $access_token = @$response['access_token'];
  header("location: ajax/ajax.php?linkedin_login&token=" . $access_token);
  exit;
}

?>

<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="css/main.css">
</head>

<body>

  <h2>eHealth Application</h2>

  <div class="container">
    <form>
      <div class="row">
        <h2 style="text-align:center">Login with Social Media</h2>

        <div class="">

          <a class="github btn" href="<?php echo $git_login_url; ?>">
          <i class="fa fa-github fa-fw"></i>Login with  GitHub
        </a>
          <a class="linkedin btn" href="<?php echo $Lin_login_url; ?>">
            <i class="fa fa-linkedin fa-fw"></i> Login with Linkedin
          </a>
          <!-- google signin button -->
          <div id="buttonDiv"></div>
        </div>
      </div>
    </form>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

  <script src="https://accounts.google.com/gsi/client" async defer></script>
  <script>
    function decodeJwtResponse(token) {
      var base64Url = token.split('.')[1];
      var base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
      var jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
        return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
      }).join(''));

      return JSON.parse(jsonPayload);
    };

    function handleCredentialResponse(response) {
      console.log("Encoded JWT ID token: " + response.credential);
      // to decode the credential response.
      const responsePayload = decodeJwtResponse(response.credential);
      console.log(responsePayload);

      var formData = new FormData();
      formData.append('id', responsePayload.sub);
      formData.append('name', responsePayload.name);
      formData.append('email', responsePayload.email);
      formData.append('image_url', responsePayload.picture);
      formData.append('request', "login");
      formData.append('socialType', "google");
      $.ajax({
        type: "POST",
        url: "./ajax/ajax.php",
        data: formData,
        dataType: "JSON",
        processData: false,
        contentType: false,
        error: function(jqXHR, textStatus, errorMessage) {
          console.log(errorMessage); // Optional
        },
        success: function(data) {
          window.location.reload('index.php');
        }
      });
    }

    window.onload = function() {
      google.accounts.id.initialize({
        client_id: "<?= GOOGLE_CLIENT_ID ?>",
        callback: handleCredentialResponse
      });
      google.accounts.id.renderButton(
        document.getElementById("buttonDiv"), {
          theme: "filled_blue",
          size: "large",
          width: "400px",
          locale: "en_EN"
        } // customization attributes
      );
      google.accounts.id.prompt(); // also display the One Tap dialog
    }
  </script>


</body>

</html>