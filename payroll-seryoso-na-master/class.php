<?php
// required to para makapag send ng email
use PHPMailer\PHPMailer\PHPMailer;
require_once "PHPMailer/PHPMailer.php";
require_once "PHPMailer/SMTP.php";
require_once "PHPMailer/Exception.php";

Class Payroll
{
    private $username = "root";
    private $password = "";

    private $dns = "mysql:host=localhost;dbname=payroll";
    protected $pdo;


    public function con()
    {
        $this->pdo = new PDO($this->dns, $this->username, $this->password);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        return $this->pdo;
    }


    // used to set timezone and get date and time
    public function getDateTime()
    {
        date_default_timezone_set('Asia/Manila'); // set default timezone to manila
        $curr_date = date("Y/m/d"); // date
        $curr_time = date("h:i:sa"); // time

        // return date and time in array
        $_SESSION['datetime'] = array('time' => $curr_time, 'date' => $curr_date);
        return $_SESSION['datetime'];
    }



    public function login()
    {
        // set 5 attempts
        session_start();
        if(!isset($_SESSION['attempts'])){
            $_SESSION['attempts'] = 5;
        }

        // create email and password using session
        if(!isset($_SESSION['reservedEmail']) && !isset($_SESSION['reservedPassword'])){
            $_SESSION['reservedEmail'] = "";
            $_SESSION['reservedPassword'] = "";
        }


        // if attempts hits 2
        if($_SESSION['attempts'] == 2){
            echo 'Your credentials has been sent to your email<br/>';
            
            echo 'Reserved Email: '.$_SESSION['reservedEmail'].'<br/>
                  Reserved Password: '.$_SESSION['reservedPassword'];
            
            // send user credentials
            $this->sendEmail($_SESSION['reservedEmail'], $_SESSION['reservedPassword']);
            echo 'No of attempts: '.$_SESSION['attempts'];
            $_SESSION['attempts'] -= 1; // decrease 1 attempt to current attempts

        } else if($_SESSION['attempts'] == 0){ // if attempts bring down to 0
            
            // select username na gumamit ng 5 attempts
            $reservedEmail = $_SESSION['reservedEmail'];
            $setTimerSql = "SELECT * FROM super_admin WHERE username = ?";
            $stmtTimer = $this->con()->prepare($setTimerSql);
            $stmtTimer->execute([$reservedEmail]);
            $usersTimer = $stmtTimer->fetch();
            $countRowTimer = $stmtTimer->rowCount();

            // kapag may nadetect na ganong username
            if($countRowTimer > 0){
                // get id of that username
                $userId = $usersTimer->id;
                $userAccess = $usersTimer->access;
                $accessSuspended = "suspended";
                

                // update column timer set value to DATENOW - 6HRS
                
                    $updateTimerSql = "UPDATE `super_admin` 
                                    SET `timer` = NOW() + INTERVAL 6 HOUR, 
                                        `access` = '$accessSuspended'
                                    WHERE `id` = $userId;
                    
                                    SET GLOBAL event_scheduler='ON';
                                    CREATE EVENT one_time_event
                                    ON SCHEDULE AT CURRENT_TIMESTAMP + INTERVAL 6 HOUR
                                    ON COMPLETION NOT PRESERVE
                                    DO
                                        UPDATE `super_admin` 
                                        SET `timer` = NULL, 
                                            `access` = '$userAccess' 
                                        WHERE `id` = $userId;
                                    ";
                $updateTimerStmt = $this->con()->prepare($updateTimerSql);
                $updateTimerStmt->execute();
                $updateCountRow = $updateTimerStmt->rowCount();

                // checking if the column was updated already
                if($updateCountRow > 0){
                    echo 'System has been locked for 6 hrs';
                    session_destroy(); // destroy all the sessions
                } else {
                    echo 'There was something wrong in the codes';
                    session_destroy();
                }
            } else {
                echo 'Ussername is not exists';
                session_destroy();
            }

        } else {
            // if user hit login button
            if(isset($_POST['login'])){

                // get input data
                $username = $_POST['username'];
                $password = md5($_POST['password']);
    
                // if username and password are empty
                if(empty($username) && empty($password)){
                    echo 'All input fields are required to login.';
                } else {
                    // check if email is exist using a function
                    $checkEmailArray = $this->checkEmailExist($username); // returns an array(true, cho@gmail.com)
                    $passwordArray = $checkEmailArray[1]; // password ni cho

                    // kapag ang unang array ay nag true
                    if($checkEmailArray[0]){

                        $suspendedAccess = 'suspended';
                        

                        // find account that matches the username and password
                        $sql = "SELECT * FROM super_admin WHERE username = ? AND password = ?";
                        $stmt = $this->con()->prepare($sql);
                        $stmt->execute([$username, $password]);
                        $users = $stmt->fetch();
                        $countRow = $stmt->rowCount();
        
                        // if account exists
                        if($countRow > 0){

                            if($users->access != $suspendedAccess){
                                $fullname = $users->firstname." ".$users->lastname; // create fullname
                                $action = "login"; 
                                    
                                // set timezone and get date and time
                                $datetime = $this->getDateTime(); 
                                $time = $datetime['time'];
                                $date = $datetime['date'];
                
                                // insert mo sa activity log ni admin
                                $actLogSql = "INSERT INTO admin_log(`name`, 
                                                                    `action`,
                                                                    `time`,
                                                                    `date`
                                                                    )
                                            VALUES(?, ?, ?, ?)";
                                $actLogStmt = $this->con()->prepare($actLogSql);
                                $actLogStmt->execute([$fullname, $action, $time, $date]);
                
                                // create user details using session
                                session_start();
                                $_SESSION['adminDetails'] = array('fullname' => $fullname,
                                                'access' => $users->access,
                                                'id' => $users->id
                                                );
                                header('Location: dashboard.php'); // redirect to dashboard.php
                                return $_SESSION['adminDetails']; // after calling the function, return session
                            } else {
                                $dateExpiredArray = $this->formatDateLocked($users->timer);
                                $dateExpired = implode(" ", $dateExpiredArray);
                                
                                echo 'Your account has been locked until</br>'.
                                     'Date: '.$dateExpired;
                            } 
                        } else {

                            $sqlCheckAccess = "SELECT * FROM super_admin WHERE username = ?";
                            $stmtCheckAccess = $this->con()->prepare($sqlCheckAccess);
                            $stmtCheckAccess->execute([$username]);
                            $usersCheckAccess = $stmtCheckAccess->fetch();
                            $countCheckAccess = $stmtCheckAccess->rowCount();

                            if($countCheckAccess > 0){
                                if($usersCheckAccess->access == $suspendedAccess){
                                    
                                    $dateExpiredArray2 = $this->formatDateLocked($usersCheckAccess->timer);
                                    $dateExpired2 = implode(" ", $dateExpiredArray2);
                                    
                                    echo 'Your account has been locked until</br>'.
                                        'Date: '.$dateExpired2;
                                } else {
                                    echo "Username and password are not matched <br/>";
                                    echo 'No of attempts: '.$_SESSION['attempts'];
                                    $_SESSION['attempts'] -= 1; // decrease 1 attempt to current attempts
                                    $_SESSION['reservedEmail'] = $username; // blank to kanina, nagkaron na ng laman
                                    $_SESSION['reservedPassword'] = $passwordArray; // blank to kanina, nagkaron na ng laman
                                }
                            }
                        }
                    } else {
                        echo 'Your email is not exist in our system';
                    }
                }
            }
        }


        
    }
    public function formatDateLocked($date)
    {
        $dateArray = explode(" ", $date);

        $dateExpired = date("F j Y", strtotime($dateArray[0])); // date
        $timeExpired = date("h:i:s A", strtotime($dateArray[1])); // time
        return array($dateExpired, $timeExpired);
    }
    

    public function checkAccountTimer($id)
    {
        $sql = "SELECT * FROM super_admin WHERE id = ?";
        $stmt = $this->con()->prepare($sql);
        $stmt->execute([$id]);
        $users = $stmt->fetch();
        $countRow = $stmt->rowCount();

        if($countRow > 0){
            if($users->timer != NULL){
                return true;
            } else {
                return false;
            }
        }

    }


    public function checkEmailExist($email)
    {
        // find email exist in the database
        $sql = "SELECT * FROM super_admin WHERE username = ?";
        $stmt = $this->con()->prepare($sql);
        $stmt->execute([$email]);
        $users = $stmt->fetch();
        $countRow = $stmt->rowCount();

        // kapag may nadetect
        if($countRow > 0){
            return array(true, $users->password); // yung kaakibat na password, return mo
        } else {
            return array(false, ''); // pag walang nakita, return false and null
        }
    }

    public function sendEmail($email, $password)
    {
        
        $name = 'JTDV Incorporation';
        $subject = 'subject kunwari';
        $body = "Credentials
                 Your username: $email <br/>
                 Your password: $password
                ";

        if(!empty($email)){

            $mail = new PHPMailer();

            // smtp settings
            $mail->isSMTP();
            $mail->Host = "smtp.gmail.com";
            $mail->SMTPAuth = true;
            $mail->Username = "DammiDoe123@gmail.com";  // gmail address
            $mail->Password = "DammiDoe123123";         // gmail password
            $mail->Port = 465;
            $mail->SMTPSecure = "ssl";

            // email settings
            $mail->isHTML(true);
            $mail->setFrom($email, $name);              // Katabi ng user image
            $mail->addAddress($email);                  // gmail address ng pagsesendan
            $mail->Subject = ("$email ($subject)");     // headline
            $mail->Body = $body;                        // textarea

            if($mail->send()){
                $status = "success";
                $response = "Email is sent!";
                echo '<br/>'.$status."<br/>".$response;
            } else {
                $status = "failed";
                $response = "Something is wrong: <br/>". $mail->ErrorInfo;
                echo '<br/>'.$status."<br/>".$response;
            }
        } 
    }

    public function logout()
    {
        $this->pdo = null;
        session_start();
        unset($_SESSION['adminDetails']);
        session_destroy();
        header('Location: login.php');
    }

    // get login session
    public function getSessionData()
    {
        session_start();
        if($_SESSION['adminDetails']){
            return $_SESSION['adminDetails'];
        }
    }

    public function verifyUserAccess($access, $fullname)
    {
        $message = 'You are not allowed to enter the system';
        if($access == 'user'){
            header("Location: login.php?message=$message");
        } else if($access == 'super administrator'){
            echo 'Welcome '.$fullname;
        } else {
            header("Location: login.php?message=$message");
        }
    }
















    // for secretary functionality in admin
    public function addSecretary($id, $fullnameAdmin)
    {
        if(isset($_POST['addsecretary'])){
            $fullname = $_POST['fullname'];
            $cpnumber = $_POST['cpnumber'];
            $email = $_POST['email'];
            $gender = $_POST['gender'];
            $address = $_POST['address'];
            $access = "secretary";
            // generated password
            $password = $this->generatedPassword($fullname);
            $isDeleted = FALSE;

            $timer = NULL;

            if(empty($fullname) &&
               empty($email) &&
               empty($gender) &&
               empty($address) &&
               empty($password) &&
               empty($isDeleted)
            ){
                echo 'All input fields are required!';
            } else {

                // check email if existing
                
                if($this->checkSecEmailExist($email)){
                    echo 'Email is already exist!';
                } else {

                    // set timezone and get date and time
                    $datetime = $this->getDateTime(); 
                    $time = $datetime['time'];
                    $date = $datetime['date'];

                    $sql = "INSERT INTO secretary(fullname, 
                                                  gender, 
                                                  cpnumber, 
                                                  address, 
                                                  email, 
                                                  password,
                                                  timer, 
                                                  admin_id,
                                                  access,
                                                  isDeleted
                                                  )
                            VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $this->con()->prepare($sql);
                    $stmt->execute([$fullname, $gender, $cpnumber, $address, $email, $password[0], $timer, $id, $access, $isDeleted]);
                    $users = $stmt->fetch();
                    $countRow = $stmt->rowCount();

                    if($countRow > 0){
                        echo 'A new date was added';


                        $this->sendEmail($email, $password[1]);

                        $action = "Add Secretary";

                        $sqlAdminLog = "INSERT INTO admin_log(name, action, time, date)
                                        VALUES(?, ?, ?, ?)";
                        $stmtAdminLog = $this->con()->prepare($sqlAdminLog);
                        $stmtAdminLog->execute([$fullnameAdmin, $action, $time, $date]);
                        $countRowAdminLog = $stmtAdminLog->rowCount();

                        if($countRowAdminLog > 0){
                            echo 'pumasok na sa act log';
                        } else {
                            echo 'di pumasok sa act log';
                        }

                    } else {
                        echo 'Error in adding secretary!';
                    }
                }

            }
        }
    }

    public function generatedPassword($fullname)
    {
        $keyword = "%15@!#Fa4%#@kE";
        $generatedPassword = md5($fullname.$keyword);
        return array($generatedPassword, $fullname.$keyword);
    }

    // for secretary table only
    public function checkSecEmailExist($email)
    {
        // find email exist in the database
        $sql = "SELECT * FROM secretary WHERE email = ?";
        $stmt = $this->con()->prepare($sql);
        $stmt->execute([$email]);
        $users = $stmt->fetch();
        $countRow = $stmt->rowCount();

        // kapag may nadetect
        if($countRow > 0){
            return true; 
        } else {
            return false; 
        }
    }


    // show only 2 record of secretary
    public function show2Secretary()
    {
        $sql = "SELECT fullname, access FROM secretary LIMIT 2";
        $stmt = $this->con()->query($sql);
        while($row = $stmt->fetch()){
            echo "<h1>$row->fullname</h1><br/>
                  <h4>$row->access</h4><br/>";
        }
    }

    public function showAllSecretary()
    {
        $sql = "SELECT * FROM secretary";
        $stmt = $this->con()->query($sql);

        while($row = $stmt->fetch()){
            echo "<tr>
                    <td>$row->fullname</td>
                    <td>$row->gender</td>
                    <td>$row->email</td>
                    <td>
                        <a href='showAll.php?secId=$row->id'>view</a>
                    </td>
                  </tr>";
        }
    }

    public function showSpecificSec()
    {
        if(isset($_GET['secId'])){
            $id = $_GET['secId'];

            $sql = "SELECT * FROM secretary WHERE id = ?";
            $stmt = $this->con()->prepare($sql);
            $stmt->execute([$id]);
            $user = $stmt->fetch();
            $countRow = $stmt->rowCount();

            if($countRow > 0){
                $fullname = $user->fullname;
                $gender = $user->gender;
                $email = $user->email;
                $cpnumber = $user->cpnumber;
                $address = $user->address;

                echo "<script>
                         let viewModal = document.querySelector('.view-modal');
                         viewModal.setAttribute('id', 'show-modal');

                         let fullname = document.querySelector('#fullname').value = '$fullname';
                         let gender = document.querySelector('#gender').value = '$gender';
                         let email = document.querySelector('#email').value = '$email';
                         let cpnumber = document.querySelector('#cpnumber').value = '$cpnumber';
                         let address = document.querySelector('#address').value = '$address';
                      </script>";
            }
        }
    }
    
}

$payroll = new Payroll;

?>