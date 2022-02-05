<?php
require_once('secclass.php');
$sessionData = $secpayroll->getSessionData();
$secpayroll->verifyUserAccess($sessionData['access'], $sessionData['fullname']);
$id = $_GET['logid'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
<style>
.contact {
    position:absolute;
    right: 550px;
    width: auto;
    padding: auto;
}    
.header{
    text-align:center;
    padding-bottom: 50px;

}
table {
  border: 2px solid black;
}
.information{
    position: absolute;
    left: 400px;
    width: auto;
    padding: auto;
}
.grossbreakdown{
    position:absolute;
    left: 400px;
    top: 75px;"
}
.deductionbreakdown{
    position:absolute;
    right: 450px;
    top: 75px;"
}
.breakdown{
    position: absolute;
    width: 1500px;
    height: 250px;
    border: 1px solid BLACK;
    padding-bottom:100px
}
.netpay{
    position:absolute;
    top:300px;
    right:750px;
}
</style>
</head>
<body>
    <?php
    $sql = "SELECT *
    FROM generated_salary
    INNER JOIN employee_info ON generated_salary.emp_id = employee_info.emp_id
    WHERE generated_salary.log = ?;";
    $stmt = $secpayroll->con()->prepare($sql);
    $stmt->execute([$id]);
    $rows = $stmt->fetch();
    $absentrate = $rows->day_absent * $rows->hours_duty;
    $absentprice = $absentrate * $rows->rate_hour;
    $minlaterate = 0.992;  //Security Officer Rate
    $lateprice = $minlaterate * $rows->min_late;
    $totalhourofregularholiday = $rows->hours_duty * $rows->regular_holiday;
    $totalhourofspecialholiday = $rows->hours_duty * $rows->special_holiday;
    ?>
    <a href="manualpayroll.php">BACK</a>
<div class="header">JDTV SECURITY AGENCY</br>400 Gem Bldg.,Gen T De Leon Ave.</br>Barangay Gen T. De Leon, Valenzuela City</div>
<div class="breakdown">
<div class="information">
 <label for="empid">Employee ID: </label><?php echo $rows->emp_id; ?></br>
 <label for="empname">Employee Name: </label><?php echo $rows->firstname ." ". $rows->lastname; ?></br>
 <label for="location">Location: </label><?php echo $rows->location; ?>
</div>
<div class="contact">
<label for="email">Email: </label><?php echo $rows->email;?></br>
<label for="contact">Contact: </label><?php echo "0".$rows->contact;   ?></br>
<label for="date">Date: </label><?php echo $rows->date.' - '.$rows->dateandtime_created;?>
</div>

<div class="grossbreakdown">
    <table>
    <thead>
            <tr>
                <th>Earnings</th>
                <th>Hours</th>
                <th>Rate/hour&nbsp;</th>
            </tr>
            <?php echo "<tr>
            <td>Standard Pay</td>
            <td>$rows->total_hours</td>
            <td>$rows->rate_hour</td>
            <td>",number_format($rows->regular_pay),"</td>
            </tr>
            <tr>
            <td>Regular Holiday </td>
            <td>$totalhourofregularholiday</td>
            <td></td>
            <td>",number_format($rows->regular_holiday_pay),"</td>
            </tr>
            </tr>
            <tr>
            <td>Special Holiday</td>
            <td>$totalhourofspecialholiday </td>
            <td></td>
            <td>",number_format($rows->special_holiday_pay)," </td>
            </tr>
            <tr>
            <td>13Month</td>
            <td></td>
            <td> </td>
            <td>",number_format($rows->thirteenmonth)," </td>
            </tr>
            <tr>
            <td>&emsp;</td>
            <td> </td>
            <td> </td>
            <td> </td>
            </tr>
            <tr>
            <td>&emsp;</td>
            <td> </td>
            <td> </td>
            <td></td>
            </tr>
            <tr>
            <td>&emsp;</td>
            <td> </td>
            <td> </td>
            <td></td>
            </tr>
            <tr>
            <td>Total Gross</td>
            <td> </td>
            <td> </td>
            <td>",number_format($rows->total_gross),"</td>
            </tr>
            ";?>
        </thead>
    </table>
</div>
<div class="deductionbreakdown">
    <table>
    <thead>
            <tr>
                <th>Deductions</th>
                <th>No. of</th>
                <th>Rate</th>
            </tr>
            <?php echo "<tr>
            <td>Absent </td>
            <td>$rows->day_absent</td>
            <td></td>
            <td>$rows->absent_pay</td> </tr>
            <tr>
            <td>Late </td>
            <td>$rows->min_late</td>
            <td>$minlaterate per min </td>
            <td>$lateprice</td>
            </tr>
            <tr>
            <td>SSS </td>
            <td> </td>
            <td> </td>
            <td>$rows->sss </td>
            </tr>
            <tr>
            <td>Pag-ibig Fund </td>
            <td> </td>
            <td> </td>
            <td> </td>
            </tr>
            <tr>
            <td>Philhealth </td>
            <td> </td>
            <td> </td>
            <td> </td>
            </tr>
            <tr>
            <td>Cash Bond </td>
            <td> </td>
            <td> </td>
            <td>$rows->cashbond </td>
            </tr>
            <tr>
            <td>Vale </td>
            <td> </td>
            <td> </td>
            <td>$rows->vale </td>
            </tr>
            <tr>
            <td>Total Deduction</td>
            <td></td>
            <td></td>
            <td>",number_format($rows->total_deduction),"</td>
            </tr>
            ";?>
        </thead>
    </table>
</div>
<div class="netpay">Total Netpay: <?php echo number_format($rows->total_netpay);
?>

</div>
</div>
</body>
</html>