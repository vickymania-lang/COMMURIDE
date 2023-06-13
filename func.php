<?php 
include "db.php";

 $func = $_POST['ins'];
 switch ($func) {
 	case 'register':
 		register();
 		break;
    case 'login':
        login();
        break;
    case 'forget':
        forget();
        break;
 	case 'allow':
 		allow();
        break;
    case 'copyBanner':
        copyBanner();
        break;
 	case 'adminverify':
        Adminverify();
        break;
    case 'userverifyads':
        userverifyads();
        break;
    case 'approve':
        approve();
        break;
    case 'withdraw':
        withdraw();
        break;
 	default:
 		echo "<div class='error'>Function does not exist</div>";
 		break;
 }

 function mailerFunc($to, $subject,$from){
    $to = $to;
$subject = $subject;

$message = "
<html>
<head>
<title>HTML email</title>
</head>
<body>
<p>This email contains HTML Tags!</p>
<table>
<tr>
<th>Firstname</th>
<th>Lastname</th>
</tr>
<tr>
<td>John</td>
<td>Doe</td>
</tr>
</table>
</body>
</html>
";

// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= 'From: <'.$from.'>' . "\r\n";
$headers .= 'Cc: myboss@example.com' . "\r\n";

mail($to,$subject,$message,$headers);
 }


function register(){
    $fullname = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password=$_POST['password'];
    
    $main_image = $_FILES['image']['name'];

	$main_image_tmp = $_FILES['image']['tmp_name'];

    $allowed_ext = ['png','jpg','jpeg'];
    
    $main_image_ext= pathinfo($main_image, PATHINFO_EXTENSION);
    $main_image_ext = strtolower($main_image_ext);
    if (in_array($main_image_ext, $allowed_ext)) {
      $rand = uniqid(rand(6,10)); 
      $mainImage_name = $rand.'.'.$main_image_ext;
      move_uploaded_file($main_image_tmp, '../images/'.$mainImage_name);
    }

    if (!empty($fullname) && !empty($email)&& !empty($phone)&&!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $check = Database::getInstance()->select_count_where('member', 'email', $email);
            if ($check>0) {
                echo "Email address already registered";
            }else{
                $insert= Database::getInstance()->insert4('member','full_name','email','phone','password', $fullname,$email,$phone,$password);
                echo $insert;
                $subject = "Ads Earner";
                $from = "adsmoney@gmail.com";
                mailerFunc($email, $subject,$from);
            }
        }
    }
}
function login(){
    $email = $_POST['email'];
    $password=$_POST['password'];
    if (!empty($email)&& !empty($password)) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $check = Database::getInstance()->select_count_where('member', 'email', $email);
            if ($check<0) {
                echo "Email does not exist";
            }else{
                $insert= Database::getInstance()->member_login('member',$email,$password);
                echo json_encode($insert);
            }
        }
    }
}

function forget(){
    $email = $_POST['email'];
    if (!empty($email)) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $check = Database::getInstance()->select_count_where('member', 'email', $email);
            if ($check<0) {
                echo "Email does not exist";
            }else{
                $token = uniqid(rand(6,10));
                //send message to email

                $insert= Database::getInstance()->insert_double_col('reset_password','email','token',$email,$token);
                echo json_encode($insert);
            }
        }
    }
}


 function copyBanner(){
    $banner = $_POST['banner'];
    $user =$_POST['user'];
    $medium = $_POST['medium'];

    //check if user have copied once
    $check = Database::getInstance()->select_count_where_3('ads_copy_track','medium','ads_id','user_email',$medium,$banner,$user);
    if ($check == 0) {
        Database::getInstance()->insert_tripple_col('ads_copy_track','medium','ads_id','user_email',$medium,$banner,$user); 
    }else{
        echo 'You have copied this banner once';
    }
 }

 function Adminverify(){
    $banner = $_POST['banner'];
    $user =$_POST['user'];
    $medium = $_POST['medium'];

    //check if user actually copied the ads
    $check = Database::getInstance()->select_count_where_3('ads_copy_track','medium','ads_id','user_email',$medium,$banner,$user);
    if ($check == 1) {

        // check is user have verified once
        $checkVerify = Database::getInstance()->select_count_where_4('finance','medium','ads_id','user_email','status',$medium,$banner,$user, 'verified');
        if ($checkVerify <1) {
            // get the price per view and total view per user
            $selectImp = Database::getInstance()->select_from_while('advertisement', 'advert_id', $banner);   

            foreach ($variable as $value) {
                        $price_per_view = $value['price_per_view'];
                        $total_view =$value['total_view'];
                        $end_date = $value['end_date'];
                        $expected_country =$value['expected_country'];
                        $expected_state = $value['expected_state'];
                        $expected_city= $value['expected_city'];
                        $expected_gender = $value['expected_gender'];
                        $start_date = $value['start_date'];
                     } 
            $amount_earned = $price_per_view * $total_view;

            // add to finance
            $insert = Database::getInstance()->insert7('finance','user_email','ads_id','medium','price_per_view','total_view','amount_earned','status', $_SESSION['user'],$banner,$medium,$price_per_view,$total_view,$amount_earned, 'verified');
            echo $insert;
        }

        Database::getInstance()->insert_tripple_col('ads_copy_track','medium','ads_id','user_email',$medium,$banner,$user); 
    }else{
        echo 'You have copied this banner once';
    }
 }

 function userverifyads(){
    $response = '';
    $ads_id = $_POST['bannerId'];
    $ads_file = $_FILES['ads_file']['name'];
    $medium = $_POST['medium'];
    $ads_file_tmp = $_FILES['ads_file']['tmp_name'];
    $link = $_POST['verify_link'];
    
    $allowed_ext = ['mpeg','mp4','png','jpeg','jpg'];
    $ads_file_ext= pathinfo($ads_file, PATHINFO_EXTENSION);
    $ads_file_ext = strtolower($ads_file_ext);
    if (in_array($ads_file_ext, $allowed_ext)) {
      $rand = uniqid(rand(6,10)); 
      $ads_file_name = $rand.'.'.$ads_file_ext;
      move_uploaded_file($ads_file_tmp, '../verify/'.$ads_file_name);
    }
    // Check if ads id exist indeed and if user has copied that ads
    $check_copy = Database::getInstance()->select_count_where_3('ads_copy_track','ads_id','user_email','medium', $ads_id, $_SESSION['user'],$medium);
    if ($check_copy===1) {

    // check if the verification form has not been submitted
        $check_verify = Database::getInstance()->select_count_where_3('ads_copy_track','ads_id','user_email','medium', $ads_id, $_SESSION['user'],$medium);
    // if check =0, then the form has not been previously submitted
      if ($check_verify===0) {
           $insert = Database::getInstance()->insert5('verify','verify_link','ads_file','ads_id','email','medium',$link,$ads_file_name,$ads_id,$_SESSION['user'],$medium);
            $response = $insert; 
       }if($check_verify>0){
        $response = '<div class"error">This request has been submitted earlier.</div>';
       }
    }else{
        $response = '<div class"error">Error! Something is missing in your request. Kindly provide the correct information of the ads you copied</div>';
    }
    
    echo $response;
    
}
function approve(){
    $email =$_POST['email'];
    $adsId = $_POST['adsId'];
    $medium = $_POST['medium'];
    // add verified to verify table
    $update = Database::getInstance()->update1_where2('verify', 'status','verified','email','ads_id',$email,$adsId);

    // select the ads detail for the user to be added to finance
    $select= Database::getInstance()->select_from_while('advertisement','advert_id',$adsId);
    foreach($select as $value){
        $total_view =$value['total_view'];
        $price_per_view = $value['price_per_view'];
    }
    $amount_earned = $total_view * $price_per_view;
    Database::getInstance()->insert7('finance', 'user_email','ads_id','medium','price_per_view','total_view','amount_earned','status',$email,$adsId,$medium,$price_per_view,$total_view,$amount_earned,'paid');
 }
  function withdraw(){
    $email =$_POST['email'];
    $adsId = $_POST['adsId'];
    $medium = $_POST['medium'];
    // add verified to verify table
    $update = Database::getInstance()->update1_where2('verify', 'status','pending','email','ads_id',$email,$adsId);

    // update the finance table
    Database::getInstance()->update1_where3('finance', 'status','retracted','user_email','ads_id','medium',$email,$adsId,$medium);
}
?>









