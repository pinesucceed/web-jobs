<?php 
get_header();
wp_enqueue_style( 'web-jobs', plugins_url( '/css/form.css' , __FILE__ ));
global $wpdb;
	$surnameError = '';
    $givenNameError = '';
    $emailError = '';
    $messageError = '';
    $fileError = '';
    $acceptError = '';
    $captchaError = '';
	$success = '';
	$error = '';
	$isPost=false;
	$jobTitle = get_the_title();
    if ( isset( $_POST['saveform'] ) ) {  
		$isPost = true;
        $surname  = sanitize_text_field( $_POST["surname"] );
        $given_name  = sanitize_text_field( $_POST["given_name"] );
        $telephone  = sanitize_text_field( $_POST["telephone"] );
//        $mobilephone  = sanitize_text_field( $_POST["mobilephone"] );
        $email  = sanitize_text_field( $_POST["email"] );
//        $street = sanitize_text_field( $_POST["street"] );
//        $postal_code = sanitize_text_field( $_POST["postal_code"] );
//        $town = sanitize_text_field( $_POST["town"] );
//
//        $msg = sanitize_text_field( $_POST["message"] );
        $send_copy_to_myself =  sanitize_text_field( $_POST["send_copy_to_myself"] );
        $read_data_privacy_statement = sanitize_text_field( $_POST["read_data_privacy_statement"]);
        $captcha = sanitize_text_field( $_POST["captcha"]);
        $attachement0 = $_FILES['attachement0'];
        $attachement1 = $_FILES['attachement1'];
        $attachement2 = $_FILES['attachement2'];
        $valid = true;
        if(empty($surname)){
            $surnameError = "<span class='error'>Pflichtfeld</span>"; 
            $valid = false;
        }
        if(empty($given_name)){
            $givenNameError = "<span class='error'>Pflichtfeld</span>";
            $valid = false;
        }
        if(empty($email)){
            $emailError = "<span class='error'>Pflichtfeld</span>";
            $valid = false;
        }else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $emailError = "<span class='error'>Gültige E-Mail</span>";
            $valid = false;
        }
//        if(empty($msg)){
//            $messageError = "<span class='error'>Pflichtfeld</span>";
//            $valid = false;
//        }
        if(empty($read_data_privacy_statement)){
            $acceptError = "<span class='error'>Sie müssen die Datenschutzerklärung akzeptieren.</span>";
            $valid = false;
        }
        if((!empty($attachement0) && !empty($attachement0['name'])) || (!empty($attachement1) && !empty($attachement1['name'])) || (!empty($attachement2) && !empty($attachement2['name']))){
            
        }else{
            $fileError = "<span class='error'>Sie müssen mindestens einen Anhang auswählen.</span>";
            $valid = false;
        }
        
        if(empty($captcha)){
            $captchaError = "<span class='error'>Sie müssen das Captcha eingeben.</span>";
            $valid = false;
        }else if ($captcha != $_SESSION["captcha"] OR $_SESSION["captcha"]=='')  { 
            $captchaError = "<span class='error'>Sie müssen das Captcha eingeben.</span>";
            $valid = false;
        }
        if($valid){
			ini_set("upload_max_filesize","300M");
            $upload = wp_upload_dir(); 
            $upload_dir = $upload['basedir'];
            $upload_dir = $upload_dir . '/web-jobs';
            if (! is_dir($upload_dir)) {
               mkdir( $upload_dir, 0700 );
            }
            $mail_attachment = array();
            if((!empty($attachement0) && !empty($attachement0['name']))){
                $target_file = $upload_dir .'/' . basename($attachement0["name"]);
                if (move_uploaded_file($attachement0["tmp_name"], $target_file)) {
                    $mail_attachment[] = $target_file; 
                }
            }
            if((!empty($attachement1) && !empty($attachement1['name']))){
                $target_file1 = $upload_dir .'/' . basename($attachement1["name"]);
                if (move_uploaded_file($attachement1["tmp_name"], $target_file1)) {
                    $mail_attachment[] = $target_file1; 
                }
            }
            if((!empty($attachement2) && !empty($attachement2['name']))){
                $target_file2 = $upload_dir .'/' . basename($attachement2["name"]);
                if (move_uploaded_file($attachement2["tmp_name"], $target_file2)) {
                    $mail_attachment[] = $target_file2; 
                }
            }
            $subject = $jobTitle;
            $message = get_option('form_user_email');
            $message = str_replace("[given_name]",$given_name,$message);
            $message = str_replace("[surname]",$surname,$message);
            $message = str_replace("[telephone]",$telephone,$message);
//            $message = str_replace("[mobilephone]",$mobilephone,$message);
            $message = str_replace("[email]",$email,$message);
//            $message = str_replace("[street]",$street,$message);
//            $message = str_replace("[postal_code]",$postal_code,$message);
//            $message = str_replace("[town]",$town,$message);
//            $message = str_replace("[message]",$msg,$message);
            $message = str_replace("[send_copy_to_myself]",$send_copy_to_myself,$message);

            $to = $email;

            // If email has been process for sending, display a success message
            if (wp_mail($to, $subject, $message)) {
                $message = get_option('form_admin_email');
				$message = str_replace("[given_name]",$given_name,$message);
				$message = str_replace("[surname]",$surname,$message);
				$message = str_replace("[telephone]",$telephone,$message);
//				$message = str_replace("[mobilephone]",$mobilephone,$message);
				$message = str_replace("[email]",$email,$message);
//				$message = str_replace("[street]",$street,$message);
//				$message = str_replace("[postal_code]",$postal_code,$message);
//				$message = str_replace("[town]",$town,$message);
//				$message = str_replace("[message]",$msg,$message);
				$message = str_replace("[send_copy_to_myself]",$send_copy_to_myself,$message);

				
				$to = !empty(get_option('admin_form_email')) ? get_option('admin_form_email') : get_option('admin_email');

				//$headers = "From: ".$given_name." <".$email.">" . "\r\n";

				// If email has been process for sending, display a success message
				if (wp_mail($to, $subject, $message, '', $mail_attachment)) {
					$success = '<span class="success">Vielen Dank für Ihre Anfrage Vorlage. Unser Team wird Ihre Anfrage überprüfen und wird in Kürze Kontakt mit Ihnen.</span>';
				}else{
					$error = '<span class="error">Technischer Fehler, bitte versuchen Sie es später noch einmal</span>';
				} 
            } else {
                $error = '<span class="error">Technischer Fehler, bitte versuchen Sie es später noch einmal</span>';
            }
        }
    }
?>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery(".toggleclick").click(function(){
            jQuery(".showForm").slideToggle();
        })
    })
</script>
<div class="container">
    <div class="row">
      <?php  if(have_posts()) : ?>
    <div class="row">
        <?php while ( have_posts() ) : the_post(); ?>
                <div class="col-sm-12">
				<div class='job_box'>
                        <h1><?php the_title(); ?></h1>
                        <?php the_content(); ?>
                        Job Anfang : <?php $job_date = ( get_post_meta( get_the_ID() , 'job_date', true ) ); echo $job_date; ?>
                        <button class="btn btn-primary toggleclick">BEWERBEN</button>
                        <div class='showForm' style="display: <?php echo $isPost ? "block" : "none"; ?>;">
                        <?php 
                        
    $url = esc_url( $_SERVER['REQUEST_URI'] );
    $captchFile = plugins_url().'/'.plugin_basename(dirname(__FILE__)).'/captcha.php';
    
	if(!empty($success)){
		echo '
    <div class="application_form">
	'.$success.'
	</div>';
	}else{
    echo '
    <div class="application_form">
	'.$error.'	
        <form method="post" action="'.$url.'" enctype="multipart/form-data">
            <div class="form_box">
					<div class="form-heading">
                            <h2>Bewerbungsformular</h2>
                            <span>Sehr geehrte Bewerberin, sehr geehrter Bewerber, wir freuen uns, dass Sie sich bei uns bewerben möchten.</span>
                    </div>
                    <div class="form-heading">
                            <h2>1. Schritt: Ihre Kontaktdaten</h2>
                            <span>Bitte nennen Sie uns Ihre Anschrift und Ihre Kontaktdaten, damit wir Sie ggf. unkompliziert erreichen können.</span>
                    </div>
                    <div class="form-content">
                            <div class="form-group">
                                    '.$surnameError.'
                                    <label>Name*</label>

	<input type="text" class="form-control" name="surname" value="'.(isset($_POST['surname']) ? $_POST['surname'] : "").'"/>
							</div>
                            <div class="form-group">
                                    '.$givenNameError.'
                                    <label>Vorname*</label>
	<input type="text" class="form-control" name="given_name" value="'.(isset($_POST['given_name']) ? $_POST['given_name'] : "").'"/>
                            </div>
                            <div class="form-group">
                                    <label>Telefonnummer</label>
	<input type="text" class="form-control" name="telephone" value="'.(isset($_POST['telephone']) ? $_POST['telephone'] : "").'"/>
                            </div>
                            <div class="form-group">
                                    '.$emailError.'
                                    <label>E-Mail*</label>
	<input type="email" class="form-control" name="email" value="'.(isset($_POST['email']) ? $_POST['email'] : "").'"/>
                            </div>
                    </div>
            </div>


          
            <div class="form_box">
                    <div class="form-heading">
                            <h2>2. Schritt: Anhänge</h2>
                            <span>Um uns einen möglichst umfassenden Eindruck von Ihnen zu vermitteln, haben Sie hier die Möglichkeit, zusätzliche Unterlagen mitzusenden. Neben Ihrem ausführlichen Anschreiben interessieren uns v.a. Ihr Lebenslauf sowie wichtige Zeugnisse und ggf. Arbeitsproben. Wenn möglich, nutzen Sie für Ihre Anhänge bitte das PDF- oder Word-Format. Bitte beachten Sie: Die maximale Größe Ihrer Anhänge darf 15 MB nicht übersteigen.</span>
                    </div>
                    <div class="form-content">
                                    '.$fileError.'
                            <div class="form-group">
                                    <label>Anhang 1*</label>
                                    <input type="file" name="attachement0">
                            </div>
                            <div class="form-group">
                                    <label>Anhang 2 (optional)</label>
                                    <input type="file" name="attachement1">
                            </div>
                            <div class="form-group">
                                    <label>Anhang 3 (optional)</label>
                                    <input type="file" name="attachement2">
                            </div>
                    </div>
            </div>


            <div class="form_box">
                    <div class="form-heading">
                            <h2>3. Schritt: Kontrolle Ihrer Angaben</h2>
                            <span>Bevor Sie Ihre Bewerbung nun absenden, bitten wir Sie, nochmals Ihre Eingaben zu kontrollieren. Es wäre schade, wenn wir Ihre Bewerbung aufgrund von Fehlern nicht in den Auswahlprozess mit einbeziehen könnten.</span>
                    </div>
                    <div class="form-content">
                            <div class="form-group">
                            <div class="checkbox">
                                     <input type="checkbox" name="send_copy_to_myself" value="Yes"> Ich will eine Kopie meiner Bewerbung an meine oben angegebene E-Mailadresse erhalten.<br>				
                            </div>	
                            </div>			
                    </div>
            </div>


            <div class="form_box">
                    <div class="form-heading">
                            <h2>4. Schritt: Datenschutzerklärung</h2>
                    </div>
                    <div class="form-content">
                                    '.$acceptError.'
                            <div class="form-group">
                            <div class="checkbox">
			<input type="checkbox"  name="read_data_privacy_statement" value="Yes" '.(isset($_POST['read_data_privacy_statement']) && !empty($_POST['read_data_privacy_statement']) ? "checked" : "").'>Ja, ich bin damit einverstanden, dass meine Daten zum Zwecke der Übermittlung meiner Bewerbung verarbeitet werden. Jede andere Verwendung ist unzulässig.*<br>				
                            </div>		
    </div>			
                    </div>
            </div>

            <div class="form_box">
                    <div class="form-heading">
                            <h2>5. Schritt: Captcha</h2>
                            <span>Bitte geben Sie abschließend den Captcha-Code ein. Der Captcha-Code ist eine wichtige Maßnahme gegen im Internet kursierende Schadprogramme und dient damit der vollständigen und fehlerfreien Übermittlung Ihrer Bewerbung.*</span>
                    </div>
                    <div class="form-content">
                                    '.$captchaError.'
                            <div class="captcha-image">
                                    <img src="'.$captchFile.'">
						<input type="text" name="captcha" class="form-control"/> 
                            </div>
                    </div>
                    <div class="footer_btn">
                        <input type="submit" value="Bewerbung absenden" name="saveform" class="form_submit_btn">
                    </div>
                    <div class="footer_text">
                            <span>Bewerbung absenden</span>
                            <span>Ihre Bewerbung wird versendet. Dies kann je nach Anhang etwas dauern. Vielen Dank.</span>
                    </div>
            </div>
            </form>
    </div>';
	}
                        ?>
                        </div>
						</div>
                </div>
        <?php endwhile; ?>
    </div>
    <?php endif; wp_reset_query(); ?>
    </div>
</div>
<?php 
get_footer();
?>