<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
  
  $event_data_arr = json_decode($_POST['event_data_arr'], true);

  var_dump($event_data_arr);
  $response = create_event($event_data_arr);
  
  
  // DISPLAY THE RESPONSE
  // If there was an error, the response will have a 'success' key with a value of false.
  // If the request fails:
  if (array_key_exists('success', $response)) {
    echo 'There was an problem adding the event to the calendar. The following errors were returned: <br>';
    foreach ($response['errors'] as $error) {
      echo $error . '<br>';
    }

  }
  // If the request is successful:
  else {

    if ($event_data_arr['setup_time'] !== 'none' && $event_data_arr['teardown_time'] !== 'none') {
      $setup_response = add_setup_teardown_event($event_data_arr);
    };

    // Add the event to complete requests.
    append_to_processed_forms($event_data_arr);
    final_instructions($event_data_arr);
    
    ?>
    <!-- BUTTON TO GO TO CONFIRMATION EMAIL VIEW -->
    <form action="confirmation_email.php" method="post">
      <input type="hidden" name='event_data_arr' value='<?php echo json_encode($event_data_arr, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>'>
      <input type="hidden" name="instance_id" value="<?php echo $response['id']; ?>">
      <input type="submit" value="Send Confirmation Email">
    </form>

    <!-- Undo the event: delete the event just created -->
    <form action="form_responses_view.php" method="post">
      <input type="hidden" name="instance_id" value="<?php echo $response['id']; ?>">
      <input type="submit" value="Undo">
    </form>
 
  <?php
  }

}
else {
  echo 'No request to undo was made. <br>';

}

// Function to append an into the the "processed_forms" array in the file named history.json.
function append_to_processed_forms($entry_data) {
  // Get the history array from the history.json file.
  $history = json_decode(file_get_contents('history.json'), true);
  // Build json object to append to the process_forms array.
  $form_entry = array(
      'event_name' => $entry_data['event_name'],
      'entry_id' => $entry_data['entry_id'],
      'date' => $entry_data['date']
  );
  // Append the entry_id to the history array.
  array_push($history['processed_forms'], $form_entry);
  // Write the history array to the history.json file.
  file_put_contents('history.json', json_encode($history, JSON_PRETTY_PRINT));
};


// function to create a new event in Breeze
function create_event($event_data_arr) {
  require('secrets.php');
  require_once('breeze.php');
  
  echo $api_key;


  require_once('data.php');
  $breeze = new Breeze($api_key);
  // Temporary, to get the calender list
  $calendars = $breeze->url('https://'. $subdomain . '.breezechms.com/api/events/calendars/list');
  // TEST echo $calendars;

  // convert the start and end times to epoch time. Times are given in Pacific time.
  // set default time zone to Pacific time
  date_default_timezone_set('America/Los_Angeles');
  $start_time_epoch = strtotime($event_data_arr['date'] . ' ' . $event_data_arr['start_time']);
  $end_time_epoch = strtotime($event_data_arr['date'] . ' ' . $event_data_arr['end_time']);

  echo $start_time_epoch;
  echo $end_time_epoch;

  // BUILD THE REQUEST URL
  // base url
  // Set the variables
  $request_url = 'https://' . $subdomain . '.breezechms.com/api/events/add?';
  $request_url .= 'name=' . urlencode($event_data_arr['event_name']);
  $request_url .= '&starts_on=' . $start_time_epoch;
  $request_url .= '&ends_on=' . $end_time_epoch;
  $request_url .= '&description=' . urlencode($event_data_arr['description']);
  $request_url .= '&category_id=' . $event_data_arr['category_id'];

  echo $request_url;
  // SEND THE REQUEST
  $response = $breeze->url($request_url);
  $response = json_decode($response, true);
  
  return $response;
}

function add_setup_teardown_event($event_data_arr){
  $event_data_arr['start_time'] = $event_data_arr['setup_time'];
  $event_data_arr['end_time'] = $event_data_arr['teardown_time'];
  $event_data_arr['event_name'] = 'Setup/Teardown for ' . $event_data_arr['event_name'];
  $event_data_arr['description'] = 'Setup/Teardown';
  $event_data_arr['category_id'] = '91087';
  $response = create_event($event_data_arr);
  return $response;

}


function final_instructions($event_data_arr) {
  echo '<p><strong>Next Steps:</strong></p>';
  echo '<p>Keep this page open while you complete these steps</p>';
  echo '<ol>
    <li>Add the locations manually: ';
  echo $event_data_arr['locations_string'];
  echo '</li>
    <li>If the event is recurring, set up the recurrence manually.</li>
    <li>If the event looks okay on the calender, click the button to send the confirmation email.</li>
  </ol>';

}
  