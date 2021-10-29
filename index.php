<?php
// Initialize the session
session_start();
require_once "config.php";

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
$loggedinAs = ($_SESSION['userData']['Role_IDrole'] == '1')
    ? 'Patient'
    : (($_SESSION['userData']['Role_IDrole'] == '2')
        ? 'Physician'
        : 'Researcher')
?>
<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/main.css">
</head>

<body>
    <div class="container-head">
        <h2>eHealth Application</h2>
        <p>Welcome <?php echo $_SESSION['social_name']; ?> (LoggedIn via <?= $_SESSION['socialType']; ?>)</p>
        <p>You are logged in as a <b><?= $loggedinAs; ?></b> to account <b><?= @$_SESSION['userData']['username']; ?>(<?= @$_SESSION['userData']['email']; ?>)</b></p>

        <ul class="horizontal">
            <?php if ($_SESSION['userData']['Role_IDrole'] != 1) { ?>
                <li data-attr="patients_Data"><a class="active" href="javascript:void(0)">Patients List</a></li>
            <?php } ?>
            <?php if ($_SESSION['userData']['Role_IDrole'] == 3) { ?>
                <li data-attr="rss_Feed"><a href="javascript:void(0)">RSS Feed</a></li>
            <?php } ?>
            <li class="rightli" style="float:right"><a href="logout.php">Logout me!</a></li>
        </ul>
    </div>
    <div class="container">
        <?php if ($_SESSION['userData']['Role_IDrole'] == 1) {
            include('patient.php');
        } else if ($_SESSION['userData']['Role_IDrole'] == 2) {
            include('physician.php');
        } else { ?>
        <div class="content-hideShow">
            <div class="patients_Data">
                <?php
                include('physician.php'); ?>
            </div>
            <div class="rss_Feed hide">
                <?php
                include('rss_feed.php'); ?>
            </div>
        </div>
        <?php } ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <script>
        $(document).ready(function() {
            $('ul.horizontal li').click(function(e) {
                $('ul.horizontal li a').removeClass('active');
                $(this).find('a').addClass('active');
                var attr = $(this).attr('data-attr');
                if(attr == 'patients_Data')
                {
                  $('.patients_Data').removeClass('hide');
                  $('.rss_Feed').addClass('hide');
                }else{
                   $('.rss_Feed').removeClass('hide');
                   $('.patients_Data').addClass('hide');
                }
            });
        });
    </script>
</body>

</html>