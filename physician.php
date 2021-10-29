<?php
if (isset($_GET['userID'])) {
    // single patient data
    // patients list
    $sql = "SELECT therapy.*, therapy_list.name as th_name, therapy_list.Dosage as dosage, medicine.name as medicineName FROM `therapy` join `therapy_list` on therapy.TherapyList_IDtherapylist = therapy_list.therapy_listID join medicine on therapy.User_IDmed = medicine.medicineID where User_IDpatient = " . $_GET['userID'];
    $patient_result = mysqli_query($con, $sql);
    // details from user table
    $sql = "SELECT * FROM `user` WHERE userID=" . $_GET['userID'] . ";";
    $result = mysqli_query($con, $sql);
    // Mysql_num_row is counting table row
    $row_p = $result->fetch_assoc();
    //print_r($row = $patient_result->fetch_assoc());die;
?>
    <div class="row">
        <div class="container-head">
            <h2>Patient Details</h2>
            <p>Name: <?= $row_p['username']; ?></p>
            <p>Email: <?= $row_p['email']; ?>
                <a href="index.php" class="btn">Back to Listing</a>
            </p>
            <div class="therapy-content">
                <h3>Therapy Details</h3>
                <?php if ($patient_result->num_rows > 0) {
                    // output data of each row
                    while ($row_p = $patient_result->fetch_assoc()) {
                        $sql = "SELECT * from test join test_session on test.testID = test_session.Test_IDtest where Therapy_IDtherapy = " . $row_p['therapyID'];
                        $test_session = mysqli_query($con, $sql); ?>
                        <p><b>Therapy: </b><?= $row_p['th_name']; ?></p>
                        <p><b>Medicine: </b><?= $row_p['medicineName']; ?></p>
                        <p><b>Dosage: </b><?= $row_p['dosage']; ?></p>
                        <h4>Test Sessions</h4>
                        <ul>
                            <?php if ($test_session->num_rows > 0) {
                                // output data of each row 
                                $i = 0;
                                while ($row = $test_session->fetch_assoc()) {
                                    $i++;
                                    $sql = "SELECT * from note where Test_Session_IDtest_session = " . $row['test_SessionID'];
                                    $notes = mysqli_query($con, $sql); ?>
                                    <li class="test-session">
                                        <b>Date Time:</b> <?= $row["dateTime"]; ?>
                                        <br />
                                        <b>Data URL:</b> <a href="test/<?= $row["DataURL"]; ?>.csv"><?= $row["DataURL"]; ?></a></td>

                                        <?php if ($notes->num_rows > 0) { ?>
                                            <br /> <b>Notes:</b>
                                            <ul>
                                                <?php // output data of each row 
                                                $i = 0;
                                                while ($row_notes = $notes->fetch_assoc()) { ?>
                                                    <li><?= $row_notes['note'] ?></li>
                                                <?php } ?>
                                            </ul>
                                        <?php  } ?>
                                    </li>
                                <?php }
                            } else { ?>
                                <li>
                                    No Record!
                                </li>
                            <?php  } ?>
                        </ul>

                    <?php }
                } else { ?>
                    <p>No record found!</p>
                <?php  } ?>
            </div>
        </div>
    </div>
<?php } else {
    // patients list
    $sql = "SELECT * FROM `user` WHERE Role_IDrole='1' ORDER BY RAND();";
    $patients_result = mysqli_query($con, $sql);
?>
    <caption>Patients list</caption>
    <table id="patients" class="center">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php if ($patients_result->num_rows > 0) {
                // output data of each row
                while ($row = $patients_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $row["username"]; ?></td>
                        <td><?= $row["email"]; ?></td>
                        <td><a href="?userID=<?= $row["userID"]; ?>">Check data</a></td>
                    </tr>
                <?php }
            } else { ?>
                <tr>
                    <td colspan="3"> 0 results</td>
                </tr>
            <?php  } ?>
        </tbody>
    </table>
<?php } ?>