<?php

// Include config file
require_once "../config.php";

// login
if (
    (@$_POST['request'] == 'login' && isset($_POST)) ||
    (isset($_GET['linkedin_login']) && isset($_GET['token'])) ||
    (isset($_GET['git_login']) && isset($_GET['token']))

) {

    if (isset($_GET['git_login']) || isset($_GET['linkedin_login'])) {
        $dataLogin = [];
        $urlCurl =  (isset($_GET['git_login'])) ? 'https://api.github.com/user' : 'https://api.linkedin.com/v2/me';
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $urlCurl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: bearer ' . $_GET['token'],
                'User-Agent: Login'
            ),
        ));

        $response = json_decode(curl_exec($curl),true);

        curl_close($curl);
        if (isset($_GET['git_login'])) {
            $dataLogin['name'] = @$response['login'];
            $dataLogin['socialType'] = 'github';
        } else {
            $dataLogin['name'] = @$response['localizedFirstName'] . ' ' . @$response['localizedLastName'];
            $dataLogin['socialType'] = 'linkedin';
        }
    } else {
        $dataLogin = $_POST;
    }

    // Password is correct, so start a new session
    session_start();

    // for google get patients(1) , linkedin get physician (2) and for else get researcher(3)
    $id = ($dataLogin['socialType'] == 'google')
        ? 1
        : (($dataLogin['socialType'] == 'linkedin')
            ? 2
            : 3);
    // get random one record based on role ID
    $sql = "SELECT * FROM `user` WHERE Role_IDrole='$id' ORDER BY RAND() LIMIT 1;";
    $result = mysqli_query($con, $sql);
    // Mysql_num_row is counting table row
    $count = mysqli_num_rows($result);
    if ($count == 1) {
        $row   = $result->fetch_assoc();
        $_SESSION["userData"] = $row;
        $_SESSION["username"] = $row['username'];
        $_SESSION["userID"] = $row['userID'];
        $_SESSION["email"] = $row['email'];
    }

    // Store data in session variables
    $_SESSION["loggedin"] = true;
    $_SESSION["social_name"] = $dataLogin['name']; // social media acc name
    $_SESSION['socialType'] = $dataLogin['socialType'];

    // Redirect user to welcome page
    if ($dataLogin['socialType'] == 'google') {
        echo json_encode(array("success" => 200, "message" => "Login successfully!"));
    } else {
        header("location: " . BASE_URL);
    }
    exit;
}
