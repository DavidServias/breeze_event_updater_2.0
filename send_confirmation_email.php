<?php

//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require 'secrets.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 

  // format of the mail function: 
  // mail(to,subject,message,headers,parameters);

  $from_email = $_POST['from_email'];
  $to_email = $_POST['to_email'];
  $to_name = $_POST['to_name'];
  $subject = $_POST['subject'];
  $message = $_POST['greeting'] . '<br><br>' . $_POST['message_paragraph1'] . '<br><br>' . $_POST['message_paragraph2'] . '<br><br>' . $_POST['message_paragraph3'] . '<br><br>' . $_POST['message_paragraph4'] . '<br><br>' . $_POST['salutation'] . '<br>' . $_POST['signature'] . '<br><br>';
  
  // if locations_string contains "kitchen" include kitchen instructions.
  if (strpos($_POST['locations_string'], 'Kitchen') !== false) {
    $message .= $_POST['kitchen_instructions'] . '<br><br>';
  };
  
  // If event needs promotion, include promotion instructions.
  if ($_POST['needs_promotion'] == true) {
    $message .= $_POST['promotion_instructions'] . '<br><br>';
  };
 
  $message .=  $_POST['building_access_instructions'];


  // Copied and pasted from the PHPMailer documentation
  
  /**
   * This example shows settings to use when sending via Google's Gmail servers.
   * This uses traditional id & password authentication - look at the gmail_xoauth.phps
   * example to see how to use XOAUTH2.
   * The IMAP section shows how to save this message to the 'Sent Mail' folder using IMAP commands.
   */

  // Copilot says use "use" at the very beginning of the file. Copied this to the top of the file. If that works, delete this part. 
   // //Import PHPMailer classes into the global namespace
  // use PHPMailer\PHPMailer\PHPMailer;
  // use PHPMailer\PHPMailer\SMTP;

  require './vendor/autoload.php';

  //Create a new PHPMailer instance
  $mail = new PHPMailer();

  //Tell PHPMailer to use SMTP
  $mail->isSMTP();

  //Enable SMTP debugging
  //SMTP::DEBUG_OFF = off (for production use)
  //SMTP::DEBUG_CLIENT = client messages
  //SMTP::DEBUG_SERVER = client and server messages
  $mail->SMTPDebug = SMTP::DEBUG_SERVER;

  //Set the hostname of the mail server
  $mail->Host = 'smtp.gmail.com';
  //Use `$mail->Host = gethostbyname('smtp.gmail.com');`
  //if your network does not support SMTP over IPv6,
  //though this may cause issues with TLS

  //Set the SMTP port number:
  // - 465 for SMTP with implicit TLS, a.k.a. RFC8314 SMTPS or
  // - 587 for SMTP+STARTTLS
  $mail->Port = 465;

  //Set the encryption mechanism to use:
  // - SMTPS (implicit TLS on port 465) or
  // - STARTTLS (explicit TLS on port 587)
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

  //Whether to use SMTP authentication
  $mail->SMTPAuth = true;

  //Username to use for SMTP authentication - use full email address for gmail
  $mail->Username = $email_username;


  //Password to use for SMTP authentication
  $mail->Password = $app_password;

  //Set who the message is to be sent from
  //Note that with gmail you can only use your account address (same as `Username`)
  //or predefined aliases that you have configured within your account.
  //Do not use user-submitted addresses in here
  $mail->setFrom($from_email, $email_sent_from_name);

  //Set an alternative reply-to address
  //This is a good place to put user-submitted addresses
  //$mail->addReplyTo('replyto@example.com', 'First Last');

  //Set who the message is to be sent to
  //$mail->addAddress($to_email, $to_name);

  // Separate the email addresses into an array.
  $to_email_arr = explode(',', $to_email);
  foreach ($to_email_arr as $email) {
    $mail->addAddress($email);
  }


  //Set the subject line
  $mail->Subject = $subject;

  //Read an HTML message body from an external file, convert referenced images to embedded,
  //convert HTML into a basic plain-text alternative body
  // Original:
  //$mail->msgHTML(file_get_contents('contents.html'), __DIR__);
  $mail->msgHTML($message);

  //Replace the plain text body with one created manually
  // $mail->AltBody = 'This is a plain-text message body';

  //Attach an image file
  // $mail->addAttachment('images/phpmailer_mini.png');



//Section 2: IMAP
  // The messages appear to be sent and saved automatically without this function, so it may not be necessary.

  //IMAP commands requires the PHP IMAP Extension, found at: https://php.net/manual/en/imap.setup.php
  //Function to call which uses the PHP imap_*() functions to save messages: https://php.net/manual/en/book.imap.php
  //You can use imap_getmailboxes($imapStream, '/imap/ssl', '*' ) to get a list of available folders or labels, this can
  //be useful if you are trying to get this working on a non-Gmail IMAP server.
  function save_mail($mail)
  {
    //You can change 'Sent Mail' to any other folder or tag
    $path = '{imap.gmail.com:993/imap/ssl}[Gmail]/Sent Mail';

    //Tell your server to open an IMAP connection using the same username and password as you used for SMTP
    $imapStream = imap_open($path, $mail->Username, $mail->Password);

    $result = imap_append($imapStream, $path, $mail->getSentMIMEMessage());
    imap_close($imapStream);

    return $result;
  }

  
  //send the message, check for errors
  if (!$mail->send()) {
      echo 'Mailer Error: ' . $mail->ErrorInfo;
  } else {
      echo 'Message sent!';
      echo '<button><a href="form_responses_view.php">Back to Form Responses</a></button>';
      //Section 2: IMAP
      //Uncomment these to save your message in the 'Sent Mail' folder.
      // if (save_mail($mail)) {
      //   echo "Message saved!";
      // }
  }
  
} 
else {
  echo 'No request to undo was made. <br>';
}


