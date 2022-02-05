<?php
require_once('secclass.php');
$sessionData = $secpayroll->getSessionData();
$secpayroll->verifyUserAccess($sessionData['access'], $sessionData['fullname']);
$fullname = $sessionData['fullname'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
<style>
.sidebar{
    position:absolute;
    left: 0px;
  width: 300px;
  padding: 10px;
}
table,th,tr{
    border: 1px solid BLACK;
}
.attendance_monitoring{
    position: absolute;
  right: 100px;
  top:0px;
  width: auto;
  border: 1px solid BLACK;
  padding: 100px;
}
</style>
</head>
<body>

</body>
<div class="main-container">
        <!--SIDENAV START-->
        <div class="sidebar">
            <div class="sidebar__logo">
                <div class="logo"></div>
                <h3>JDTV</h3>
            </div>
            <nav>
                <ul>
                    <li class="li__records">
                        <a href="#" class="">DASHBOARD</a>
                    </li>
                    <li class="li__records active">
                        <a href="secdashboard.php" class="active">ATTENDANCE</a>
                    </li>

                    <li class="li__report" class ="active">
                        <a href="">Payroll</a>
                        <ul>
                            <li><a href="manualpayroll.php">Manual</a></li>
                            <li><a href="#">Automatic</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
            <div class="sidebar__logout">
                <div class="li li__logout"><a href="../logout.php">LOGOUT</a></div>
            </div>
        </div>

        <div class="page-info-head">
            Secretary
        </div>

        <div class="user-info">
            <p></p>
            <div class="user-profile">
            </div>
        </div>

        <div class="attendance_monitoring">
            <div class="attendance_monitoring__header">
                <h1>Attendance Monitoring</h1>
                <button class="btn_primary">
                    <span class="material-icons"></span>
                    <a href="#">Print</a>
                </button>
                <div class="attendance_monitoring__header__svg">
                    <object data="SVG_modified/search.svg" type=""></object>
                    <form method="post"><input type="search" name="search" placeholder="Search">
                    <button type="submit" name="bsearch">Search</button></form>
                </div>
            </div>

            <div class="card">
                <div class="card__content">
                <table>
        <thead>
            <tr>
                <th>Employee ID</th>
                <th>Firstname</th>
                <th>Lastname</th>
                <th>Company</th>
                <th>Time-in</th>
                <th>Date</th>
                <th>Time-out</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                if(isset($_POST['print'])){
                } else if(isset($_POST['bsearch'])){
                    $secpayroll->search();
                }
                    else {
                    $secpayroll->displayAttendance(); 
                }
            ?>
        </tbody>
    </table>
                </div>
            </div>
</html>
