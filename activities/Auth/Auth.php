<?php

namespace Auth;

use Exception;
use database\database;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;

class Auth
{

    protected function redirect($url)
    {
        header('Location: ' . trim(CURRENT_DOMAIN, '/ ') . '/' . trim($url, '/ '));
        exit;
    }


    protected function redirectBack()
    {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }


    private function hash($password)
    {
        $hashPassword = password_hash($password, PASSWORD_DEFAULT);
        return $hashPassword;
    }
    private function random()
    {
        return bin2hex(openssl_random_pseudo_bytes(32));
    }
    public function activationMessage($username, $verifyToken)
    {
        $message = '
        <h1>َAcoount Activation Email</h1>
        <p> dear ' . $username . 'please click on the below link to activate your account </p>
        <di><a href="' . url('activation/' . $verifyToken) . '">Account Activation  </a></di>
        ';
        return $message;
    }

    public function sendMail($emailAddress, $subject, $body)
    {
        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->CharSet = "UTF-8"; //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = MAIL_HOST;                     //Set the SMTP server to send through
            $mail->SMTPAuth   = SMTP_AUTH;                                   //Enable SMTP authentication
            $mail->Username   = MAIL_USERNAME;                     //SMTP username
            $mail->Password   = MAIL_PASSWORD;                               //SMTP password
            $mail->SMTPSecure = 'tls';            //Enable implicit TLS encryption
            $mail->Port       = MAIL_PORT;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom(SENDER_MAIL, SENDER_NAME);
            $mail->addAddress($emailAddress);     //Add a recipient
            // $mail->addAddress('ellen@example.com');               //Name is optional
            // $mail->addReplyTo('info@example.com', 'Information');
            // $mail->addCC('cc@example.com');
            // $mail->addBCC('bcc@example.com');

            //Attachments
            // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $body;
            // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $result = $mail->send();
            echo 'Message has been sent';
            return $result;
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            return false;
        }
    }
    public function register()
    {
        require_once(BASE_PATH . '/template/auth/register.php');
    }
    public function registerStore($request)
    {
        if (empty($request['email']) || empty($request['username']) || empty($request['password'])) {
            flash('register_error', 'All the fields are required');
            $this->redirectBack();
        } else if (strlen($request['password']) < 8) {
            flash('register_error', 'The password requires to be at least 8 characters');
            $this->redirectBack();
        } else if (!filter_var($request['email'], FILTER_VALIDATE_EMAIL)) {
            flash('register_error', 'the email is not valid');

            $this->redirectBack();
        } else {
            $db = new Database();
            $user = $db->select('SELECT * from users WHERE email = ?', [$request['email']])->fetch();
            if ($user != null) {
                flash('register_error', 'the user is already registered');
                $this->redirectBack();
            } else {
                $randomToken = $this->random();
                $activationMessage = $this->activationMessage($request['username'], $randomToken);
                $result = $this->sendMail($request['email'], 'activation', $activationMessage);
                if ($result) {
                    $request['verify_token'] = $randomToken;
                    $request['password'] = $this->hash($request['password']);
                    $db->insert('users', array_keys($request), $request);
                    $this->redirect('login');
                } else {
                    flash('register_error', 'the user is already registered');
                    $this->redirectBack();
                }
            }
        }
    }
    public function activation($verifyToken)
    {
        $db = new database;
        $user = $db->select("SELECT * FROM users WHERE verify_token = ? AND is_active = 0;", [$verifyToken])->fetch();
        if ($user == null) {
            $this->redirect('login');
        } else {
            $result = $db->update('users', $user['id'], ['is_active'], [1]);
            $this->redirect('login');
        }
    }
    public function login()
    {
        require_once(BASE_PATH . '/template/auth/login.php');
    }
    public function checkLogin($request)
    {
        if (empty($request['email']) || empty($request['password'])) {
            flash('login_error', 'All the fields are required');

            $this->redirectBack();
        } else {
            $db = new DataBase();
            $user = $db->select("SELECT * FROm users WHERE email = ?", [$request['email']])->fetch();

            if ($user != null) {
                if (password_verify($request['password'], $user['password']) && $user['is_active'] == 1) {
                    $_SESSION['user'] = $user['id'];
                    $this->redirect('admin');
                } else {
                    flash('login_error', 'The process failed');
                    $this->redirectBack();
                }
            } else {
                flash('login_error', 'This user was not found');
                $this->redirectBack();
            }
        }
    }
    public function checkAdmin()
    {
        if (isset($_SESSION['user'])) {
            $db = new DataBase();
            $user = $db->select('SELECT * FROM users WHERE id = ?', [$_SESSION['user']])->fetch();
            if ($user != null) {
                if ($user['permission'] != 'admin') {
                    $this->redirect('home');
                }
            } else {
                $this->redirect('home');
            }
        } else {
            $this->redirect('home');
        }
    }


    public function logout()
    {
        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
            session_destroy();
        }
        $this->redirect('home');
    }
    public function forgotMessage($username, $forgotToken)
    {

        $message = '
         <h1>َAcoount Activation Email</h1>
        <p> dear ' . $username . 'please click on the below link to activate your account </p>
        <di><a href="' . url('reset-password-form/' . $forgotToken) . '">Account Activation</a></di>
        ';
        return $message;
    }
    public function forgot()
    {
        require_once(BASE_PATH . '/template/auth/forgot.php');
    }
    public function forgotRequest($request)
    {
        if (empty($request['email'])) {
            flash('forgot_error', 'The email address is required');
            $this->redirectBack();
        } else if (!filter_var($request['email'], FILTER_VALIDATE_EMAIL)) {
            flash('forgot_error', 'It\'s not a valid Email address');
            $this->redirectBack();
        } else {
            $db = new DataBase();
            $user = $db->select('SELECT * FROM users WHERE email = ?', [$request['email']])->fetch();
            if ($user == null) {
                flash('forgot_error', 'The user was not found');
                $this->redirectBack();
            } else {
                $randomToken = $this->random();
                $forgotMessage = $this->forgotMessage($user['username'], $randomToken);
                $result = $this->sendMail($request['email'], 'Reset your password', $forgotMessage);
                date_default_timezone_set('ASIA/TEHRAN');
                if ($result) {
                    $db->update('users', $user['id'], ['forgot_token', 'forgot_token_expire'], [$randomToken, date('Y-m-d H:i:s', strtotime('+15 minutes'))]);
                    $this->redirect('login');
                } else {
                    flash('forgot_error', 'Email has not been sent');
                    $this->redirectBack();
                }
            }
        }
    }


    public function resetPasswordView($forgot_token)
    {
        require_once(BASE_PATH . '/template/auth/reset-password.php');
    }

    public function resetPassword($request, $forgot_token)
    {
        if (!isset($request['password']) || strlen($request['password']) < 8) {
            flash('reset_error', 'The password requires to be at least 8 characters');
            $this->redirectBack();
        } else {
            $db = new DataBase();
            $user = $db->select('SELECT * FROM users WHERE forgot_token = ?', [$forgot_token])->fetch();
            if ($user == null) {
                flash('reset_error', 'The user was not found');
                $this->redirectBack();
            } else {
                date_default_timezone_set('Asia/Tehran');
                if ($user['forgot_token_expire'] < date('Y-m-d H:i:s')) {
                    flash('reset_error', 'The token was not found or it\'s been expired');
                    $this->redirectBack();
                }
                if ($user) {
                    $db->update('users', $user['id'], ['password'], [$this->hash($request['password'])]);
                    $this->redirect('login');
                } else {
                    flash('reset_error', 'The user was not found');
                    $this->redirectBack();
                }
            }
        }
    }
}
