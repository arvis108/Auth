<?php 
require_once 'databasecontroll.php';
require_once 'functions.ini.php';



if(isset($_POST['subforgot'])){ 
    $login=$_REQUEST['login_var'];
    $stmt = $conn->prepare("SELECT * FROM  users WHERE username = ? OR email = ?");
    $stmt->execute([$login,$login]);
    if ($stmt->rowCount() > 0){
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $oldftemail = $res['email'];  
        $token = bin2hex(random_bytes(50));
        $stmt = $conn->prepare("INSERT INTO password_reset(email,user_id, token) VALUES (?,?,?)");
    if ($stmt->execute([$oldftemail,$res['userID'],$token])) { 
        $FromName="Auth Chat";
        $FromEmail= GUSER;
        $subject="Paroles atjaunošana"; 
        $output='<p>Cienījamais lietotāj!</p>';
        $output.='<p>Izmantojiet zemāk norādīto hipersaiti, lai atjaunotu savu paroli</p>';
        $output.='<p>-------------------------------------------------------------</p>';
        $output.="<p>http://localhost/Auth/password_reset.php?token=$token</p>"; 
        $output.='<p>-------------------------------------------------------------</p>';
        $output.='<p>Ievadiet hipersaiti pārlūkprogrammā vai arī uzklikšķiniet uz tās.
        Hipersaite paliks nederīga pēc vienas stundas</p>';
        $output.='<p>Ja jūs nepieprasījāt paroles atjaunošanu, ignorējiet šo vēstuli. Taču drošības nolūkam varat atjaunot savu paroli.</p>';   
        $output.='<p>Ar cieņu,</p>';
        $output.='<p>Auth Chat</p>';
        $body = $output; 
        if(!smtpmailer($oldftemail,$FromEmail,$FromName,$subject,$body)) {
            header("location:../forgot_password.php?servererr=1"); 
        } else {
            header("location:../forgot_password.php?sent=1"); 
        }
        } else {
            header("location:../forgot_password.php?something_wrong=1"); 
        }
    }
    else{
      header("location:../forgot_password.php?err=".$login); 
    }
}
?>