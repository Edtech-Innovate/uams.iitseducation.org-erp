<?php
ini_set('display_errors', 1);
if (isset($_GET['id'])) {
  require '../../includes/db-config.php';
  session_start();

  $id = mysqli_real_escape_string($conn, $_GET['id']);
  $id = base64_decode($id);
  $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));

  $heads = array();
  $fee_heads = $conn->query("SELECT ID, Name FROM Fee_Structures WHERE University_ID = " . $_SESSION['university_id']);
  while ($fee_head = $fee_heads->fetch_assoc()) {
    $heads[$fee_head['ID']] = $fee_head['Name'];
  }

  $student = $conn->query("SELECT Admission_Sessions.Name as Session, Admission_Types.Name as Admission_Type, Courses.Short_Name as Course, Sub_Courses.Name as Sub_Course, Students.Duration as Duration, Student_Documents.Location, Modes.Name as Mode FROM Students LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Admission_Types ON Students.Admission_Type_ID = Admission_Types.ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Student_Documents ON Students.ID = Student_Documents.Student_ID AND Student_Documents.Type = 'Photo' LEFT JOIN Modes ON Students.Mode_ID = Modes.ID WHERE Students.ID = $id");
  $student = $student->fetch_assoc();
?>
  <div class="row d-flex justify-content-center">
    <div class="col-md-3 col-sm-12">
      <div class="card card-transparent">
        <div class="card-header bg-transparent text-center">
          <img class="profile_img" src="<?= $student['Location'] ?>" alt="">
          <h5><?= $student['Session'] ?> (<?= $student['Admission_Type'] ?>)</h5>
          <h6><?= $student['Course'] ?> (<?= $student['Sub_Course'] ?>)</h6>
        </div>
        <div>
        <?php if (isset($_SESSION['gateway'])) { ?>
          <button type="button" class="btn custom_erp_btn" onclick="add('<?php echo $_SESSION['gateway'] == 1 ? 'easebuzz' : '' ?>', 'md')"> Pay Online</button>
        <?php } ?>
        <?php if (in_array($_SESSION['Role'], ['Administrator', 'Accountant'])) { ?>
          <button class="btn custom_erp_btn" onclick="add('offline-payments', 'lg')">Pay Offline</button>
        <?php } ?>
      </div>
      </div>
    </div>
  <!--</div>-->
  <!--<div class="row" style="margin-bottom:20px">-->
  <!--  <div class="col-md-12 d-flex justify-content-end">-->
  <!--    <div>-->
  <!--      <?php if (isset($_SESSION['gateway'])) { ?>-->
  <!--        <button type="button" class="btn btn-primary" onclick="add('<?php echo $_SESSION['gateway'] == 1 ? 'easebuzz' : '' ?>', 'md')"> Pay Online</button>-->
  <!--      <?php } ?>-->
  <!--      <?php if (in_array($_SESSION['Role'], ['Administrator', 'Accountant'])) { ?>-->
  <!--        <button class="btn btn-primary" onclick="add('offline-payments', 'lg')">Pay Offline</button>-->
  <!--      <?php } ?>-->
  <!--    </div>-->
  <!--  </div>-->
  <!--</div>-->
  <!--<div class="row">-->
    <div class="col-md-9 col-sm-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-borderless">
              <thead>
                <tr>
                  <th><?= $student['Mode'] ?></th>
                  <th>Date</th>
                  <th>Particular</th>
                  <th>Source</th>
                  <th>Transaction ID</th>
                  <th class="text-right">Debit</th>
                  <th class="text-right">Credit</th>
                  <th class="text-right">Balance</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $balance = 0;
                $credit = 0;
                // echo "SELECT Student_Ledgers.*, Payments.Amount as received, Payments.Gateway_ID as art_id FROM Student_Ledgers LEFT JOIN Payments ON Payments.Transaction_ID = Student_Ledgers.Transaction_ID WHERE Student_Ledgers.Student_ID = $id AND Duration <= '" . $student['Duration'] . "' AND Student_Ledgers.Status = 1 ORDER BY Student_Ledgers.Type, Student_Ledgers.Created_At"; 
                $ledgers = $conn->query("SELECT Student_Ledgers.*, Payments.Amount as received, Payments.Gateway_ID as art_id, Payments.Added_By FROM Student_Ledgers LEFT JOIN Payments ON Payments.Transaction_ID = Student_Ledgers.Transaction_ID WHERE Student_Ledgers.Student_ID = $id AND Duration <= '" . $student['Duration'] . "' AND Student_Ledgers.Status = 1 ORDER BY Student_Ledgers.Type, Student_Ledgers.Created_At");

                // $ledgers = $conn->query("SELECT * FROM Student_Ledgers WHERE Student_Ledgers.Student_ID = $id AND Duration <= " . $student['Duration'] . " AND Status = 1 ORDER BY Student_Ledgers.Type, Student_Ledgers.Created_At");

                while ($ledger = $ledgers->fetch_assoc()) {
                  $fees = [];
                  //  echo "<pre>"; print_r($ledger); 
                  // if (!empty($ledger['Fee_Without_Sharing'])) {
                  //   $fees = json_decode($ledger['Fee_Without_Sharing'], true);
                  // } elseif (!empty($ledger['Fee'])) {
                  //   $fees = json_decode($ledger['Fee'], true);
                  // }
                  $credited = $ledger['Type'] == 2 ? $ledger['received'] : 0;
                  if ($credited != 0) {
                    $fees = json_decode($credited, true);
                    if (is_array($fees)) {
                      foreach ($fees as $feee) {
                        $credit = $feee;
                      }
                    }
                  }


                  // if (is_array($fees)) {
                  //   foreach (array_filter($fees) as $key => $value) {
                ?>


                  <tr>
                    <td><?= $ledger['Duration'] ?></td>
                    <td><?= date("d-m-Y", strtotime($ledger['Date'])) ?></td>
                    <td><?php echo $debit = $ledger['Type'] == 1 ? "Due" : "Paid" ?></td>
                    <?php $student_count = '';
                    if ($ledger['Type'] == 2) {
                      $students = $conn->query("SELECT CONCAT(TRIM(CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name)), ' (', IF(Students.Unique_ID='' OR Students.Unique_ID IS NULL, RIGHT(CONCAT('000000', Students.ID), 6), Students.Unique_ID), ')') as Student_Name , Students.ID as std_ID FROM Invoices LEFT JOIN Students ON Invoices.Student_ID = Students.ID WHERE `User_ID` = " . $ledger['Added_By'] . " AND Invoice_No = '" . $ledger['Transaction_ID'] . "'  AND Invoices.University_ID = " . $_SESSION['university_id'] . " ");
                      $student_name = array();
                      while ($student = mysqli_fetch_assoc($students)) {
                        $student_name[] = $student['std_ID'];
                      }
                      $student_count =  count($student_name);
                    } ?>

                    <td><?= $ledger['Source'] ?></td>
                    <td><a href="/print/receipt/index.php?id=<?= $ledger['ID'] ?>" target="_blank"><u><?= $ledger['Transaction_ID'] ?></u></a></td>
                    <td class="text-right">
                      <?php

                       $debit_val = ($ledger['Type'] == 1) ? $ledger['Fee'] : $credited;
                      // $type = gettype($value);

if ((int)$debit_val) {
    // If $debit_val is an integer
    $debit = ($ledger['Type'] == 1) ? $ledger['Fee'] : $credited;

    if ($ledger['Type'] == 2) {
        if ($student_count > 0) {
            $debitamount = $credited / $student_count;
            echo "&#8377; " . number_format($debitamount, 2);
        } else {
            echo "&#8377; " . number_format($debit, 2);
        }
    } else {
        echo "&#8377; " . number_format($debit, 2);
    }
} else {
    // If $debit_val is not an integer
    $debit = ($ledger['Type'] == 1) ? json_decode($ledger['Fee'], true) : $credited;

    if ($ledger['Type'] == 1) {
        $debitvalue = reset($debit);
        echo "&#8377; " . number_format($debitvalue, 2);
    } else if ($ledger['Type'] == 2) {
        if ($student_count > 0) {
            $debitamount = $credited / $student_count;
            echo "&#8377; " . number_format($debitamount, 2);
        } else {
            echo "&#8377; " . number_format($debitvalue, 2);
        }
    } else {
        echo "&#8377; " . number_format($debitvalue, 2);
    }
}

                      ?>
                    </td>
                    <td class="text-right">
                      <?php
                      if ($ledger['Type'] == 2) {
                        if ($student_count > 0) {
                          $credit = $credited / $student_count;
                          echo "&#8377; " . number_format($credit, 2);
                        } else {
                          echo "&#8377; " . number_format($credited, 2);
                        }
                      } else {
                        echo "&#8377; " . number_format($credited, 2);
                      }
                      ?>
                    </td>
                    <td class="text-right">
                      <?php

                      $debit_val1 = ($ledger['Type'] == 1) ?$ledger['Fee'] : $credited;

                      if((int)$debit_val1) {
                        
                        $debit = ($ledger['Type'] == 1) ? number_format($balance, 2) : number_format($credited, 2);

                        if ($ledger['Type'] == 2) {
                          if ($student_count > 0) {
                            $debit = $credited / $student_count;
                            echo "&#8377; " . number_format($debit, 2);
                          } else {
                            echo "&#8377; " . $debit;
                          }
                        } else {
                          echo "&#8377; " . $debit;
                        }
                      }else{
                        $balance = json_decode($ledger['Fee'], true);
                        $balancevalue = reset($balance);
                        echo $debitbalance = ($ledger['Type'] == 1) ? number_format($balancevalue, 2) : number_format($balancevalue, 2);
                      }
                      ?>
                    </td>
                  </tr>
                <?php
                  //}
                  //}
                }
                ?>

                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php }
?>