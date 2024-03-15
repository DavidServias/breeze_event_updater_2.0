<?php

require_once 'secrets.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
  $event_data_arr = json_decode($_POST['event_data_arr'], true);
  // Display an email form with prepopulated data.
  ?>
  <h1>Confirmation Email</h1>
  <!-- Message -->
  <?php 
    // Build the message string.
    $message_greeting = 'Hi ' . $event_data_arr['first_name'] . ',';

    $message_paragraph1 = 'The event named "' . $event_data_arr['event_name'] . '"' . ' has been added to the calendar.';
    
    $message_paragraph2 = 'The reservation is for ' . $event_data_arr['date'] . ' from ' . $event_data_arr['start_time'] . ' to ' . $event_data_arr['end_time'] . ' and includes the following locations: ' . $event_data_arr['locations_string'];

    $room_use_calendar_link = '<a href="https://uucorvallis.org/room-reservations/">Room Use Calendar</a>';

    $message_paragraph3 = 'Please check the ' . $room_use_calendar_link . ' to make sure all the details are correct, and let us know if anything doesn\'t look right.';

    // Optional content
    $message_paragraph4 = 'Thanks!';
    $message_salutation = 'Best,';
    $message_signature = 'David'; 
  
    // Add special instructions for kitchen.
    //$kitchen_coordinator_email = 'mizginnyg@gmail.com';
    $kitchen_coordinator_name = 'Ginny Gibson';
    $kitchen_instructions = '*You have requested to reserve the kitchen for your event, which requires an orientation. Our kitchen coordinator, ';
    $kitchen_instructions .= $kitchen_coordinator_name . ', will be contacting you to schedule a kitchen orientation. Her email address is <a href="mailto:';
    $kitchen_instructions .= $kitchen_coordinator_email . '">' . $kitchen_coordinator_name . '</a> if you\'d like to contact her.';

    
    // Add special instructions for the accessing the building
    require_once 'secrets.php';
    $building_access_instructions = 'Building Access: To access the building, please use the code, currently ' . $lockbox_code . ' to open the lockbox located to the right of the parking lot double doors. Press the black panel down to access the number dial, and line up numbers for the code across the line. The key inside will let you into the building. The door will still be locked, so use the doorstop to prop the door open while you return the key to the lockbox. Move the numbers on the number dial off the code, and then slide the black panel back up. Close the propped door once you enter the building. The doors need to remain locked, so please station someone from your team at the door to let other participants in, or have them text you when they arrive so that you can let them in. <br><br><strong>The lockbox code is updated regularly on the following dates: </strong>Feb. 15, May 15, Aug 15, and Nov 15.<br><br><strong>IMPORTANT:</strong> If the event happens after one of these updates, please sign up for the email updates using this form: <br>';
    $building_access_instructions .= '<a href="https://uufc.breezechms.com/form/120d2a">Lockbox Code Updates</a>';


  $default_from_email = 'music@uucorvallis.org';
    ?>

  <form action="send_confirmation_email.php" method="post">
    <!-- To -->
    <label for="to_email">To:</label>
    <input type="text" name="to_email" value="<?php 
      $to_email = $event_data_arr['email'];
      
      // Add the promotions team email if the event needs promotion.
      if($event_data_arr['needs_promotion']==true) {
        $to_email .= ', ' . $promotions_team_email;
      };
      
      // Add the kitchen coordinator email if the event includes the kitchen.
      if(strpos($event_data_arr['locations_string'], 'Kitchen') !== false) {
        $to_email .= ', ' . $kitchen_coordinator_email;
      };
      echo $to_email; ?>">
    
    <!-- From -->
    <label for="from_email">From:</label>
    <input type="text" name="from_email" value="<?php echo $default_from_email; ?>">
    
    <!-- Subject -->
    <label for="subject">Subject:</label>
    <input type="text" name="subject" value="Confirming <?php echo $event_data_arr['event_name']; ?>">
    <br><br>
    
    <!-- Message -->
    <label for="greeting">Greeting:</label><br>
    <input type="text" name="greeting" value="<?php echo $message_greeting; ?>">
    <input type="hidden" name="to_name" value="<?php echo $event_data_arr['first_name']; ?>">
    <br><br>
    <label for="message_paragraph1">Paragraph 1:</label><br>
    <textarea type="text" name="message_paragraph1" rows="2" cols="100" ><?php echo $message_paragraph1; ?></textarea>
    <br><br>
    <label for="message_paragraph2">Paragraph 2:</label><br>
    <textarea type="text" name="message_paragraph2" rows="5" cols="100" ><?php echo $message_paragraph2; ?></textarea>
    <br><br>
    <label for="message_paragraph3">Paragraph 3:</label><br>
    <textarea type="text" name="message_paragraph3" rows="3" cols="100" ><?php echo $message_paragraph3; ?></textarea>
    <br><br>
    <label for="message_paragraph4">Paragraph 4:</label><br>
    <textarea type="text" name="message_paragraph4" rows="2" cols="100" ><?php echo $message_paragraph4; ?></textarea>
    <br><br>
    <label for="salutation">Salutation:</label><br>
    <input type="text" name="salutation" value="<?php echo $message_salutation; ?>">
    <br><br>
    <label for="signature">Signature:</label><br>
    <input type="text" name="signature" value="<?php echo $message_signature; ?>">
    <br><br>
    <label for="kitchen_instructions">Kitchen Instructions:</label><br>
    <?php 
    
    // If the event includes the kitchen, add the kitchen instructions.
    if (strpos($event_data_arr['locations_string'], 'Kitchen') !== false) {
     echo '(Confirmation email will be copied to the kitchen coordinator.)<br>';
     ?>
      <textarea type="text" name="kitchen_instructions" rows="5" cols="100" ><?php echo $kitchen_instructions; ?></textarea>
      <?php
    }
    else {
      echo 'The event does not include the kitchen. <br>';
      ?>
      <textarea type="text" name="kitchen_instructions" rows="5" cols="100" >Kitchen not needed</textarea>
      <br><br>
      <?php
    };
    
    // If the event needs promotion, add the promotion instructions.
    if ($event_data_arr['needs_promotion']==true) {
      echo 'The event needs promotion. <br>';
      ?>
      <textarea type="text" name="promotion_instructions" rows="5" cols="100" >*The promotions team is included in this email, since you've indicated that you would like help with promotions.</textarea>
      <br><br>
      <?php
    }
    else {
      echo 'The event does not need promotion. <br><br>';
    };
    ?>
    <input type="hidden" name="needs_promotion" value="<?php echo $event_data_arr['needs_promotion']; ?>">

    
    <label for="building_access_instructions">Building Access Instructions:</label><br>
    <textarea type="text" name="building_access_instructions" rows="8" cols="100" ><?php echo $building_access_instructions; ?></textarea>
    <br>
    <!-- Send locations_string -->
    <input type="hidden" name="locations_string" value="<?php echo $event_data_arr['locations_string']; ?>">

    <!-- Submit -->
    <input type="submit" value="send email">
  </form>


  <!-- Undo the event: delete the event just created -->
  <p>Delete event with instance_id: <?php echo $_POST['instance_id']; ?></p>
  <form action="form_responses_view.php" method="post">  
    <input type="hidden" name="instance_id" value="<?php echo $_POST['instance_id']; ?>">
    <input type="hidden" name="undo" value="true">
    <input type="submit" value="Delete this Event"> 
  </form>

<?php
}
else {
  echo 'No request to undo was made. <br>';
}