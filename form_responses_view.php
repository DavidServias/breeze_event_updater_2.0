<?php

require_once 'data.php';
require('breeze.php');
require('secrets.php');
$breeze = new Breeze($api_key);

// If the user clicked "undo" on the finished screen, delete the event we just created.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && array_key_exists('undo', $_POST)) {
  if ($_POST['undo'] == 'true') {
    // delete the event from the calendar
    $instance_id = $_POST['instance_id'];
    $request_url = 'https://' . $subdomain . '.breezechms.com/api/events/delete?instance_id=' . $instance_id;
    $response = $breeze->url($request_url);
    $response = json_decode($response, true);
    echo 'The event you just created has been deleted. <br>';
  }

}
else {
  // Delete after testing
  echo 'No request to undo was made. <br>';
}

// If the user clicked "Mark as Resolved" on the form_responses_view screen, mark the form entry as resolved.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && array_key_exists('mark_as_resolved', $_POST)) {
  if ($_POST['mark_as_resolved'] == 'true') {
    // Add the entry to the processed_forms in  file history.json.
    $entry_data = array(
      'entry_id' => $_POST['entry_id'],
      'event_name' => $_POST['event_name'],
      'date' => $_POST['date']
    );
    append_to_processed_forms($entry_data);
  }
}
else {
  // Delete after testing
  echo 'No request to mark as resolved was made. <br>';
}

// GET FORM ENTRIES
// Construct request url.
$form_id = 607160; // Room Reservation Form
$request_url = 'https://';
$request_url .= $subdomain;
$request_url .= '.breezechms.com/api/forms/list_form_entries?form_id=';
$request_url .= $form_id;
$request_url .= '&details=1';

// get form entries
$forms_entries = $breeze->url($request_url);

// Decode the JSON response.
$form_entries = json_decode($forms_entries, true);

// DISPLAY FORM ENTRIES
// Exclude entries that have already been processed.
$last_manually_processed_entry_id = 106195106;

$is_processed = function($entry) {
//TODO:  Account for bad values.
 // search the history.json file for the entry id
  $history = json_decode(file_get_contents('history.json'), true);
  $entry_id = $entry['id'];
  $processed_forms = $history['processed_forms'];
  foreach($processed_forms as $form) {
    if ($form['entry_id'] == $entry_id) {
      return true;
    }
  }
  return false;
 
};

foreach ($form_entries as $entry) {
  // Uncomment to skip entries that have already been processed
  if ($entry['id'] <= $last_manually_processed_entry_id) {
      // continue;
  }
  else if ($is_processed($entry)) {
    continue;
  }
  else {
    // MAKE THE EVENT_DATA_ARRAY
    // Use the response data to create an array of event data that is useful. The array will be used to display the event, and send to the next view in a single package.

    // MAKE LOCATIONS STRING
    // Get locations from the form entry.
    $locations = $entry['response']['2063157391'];
    // Add name to locations ids. 
    // NOTE: The ids are for the form fields (not the actual location ids).
    // Build locations string.
    $locations_string = '';
    foreach($locations as &$location) {
      $location_name = $locations_dictionary[$location['value']];
      $locations_string .= $location_name;
      // add comma unless it's the last location in the list.
      if ($location != end($locations)) {
        $locations_string .= ', ';
      }
    };
    // php documentation says to unset the reference to avoid problems
    unset($location);


    // list each item in $entry.
    foreach ($entry['response'] as $key => $value) {
      echo $key . ': ';
      var_dump($value);
      echo '<br>';
    };


    // Make the event data array.
    $event_data_arr = array(
      'entry_id' => $entry['id'], 
      'event_name' => $entry['response']['2063157390'],
      'date' => $entry['response']['2063157392'],
      'date_requested' => $entry['created_on'],
      'start_time' => $entry['response']['2063157393'],
      'end_time' => $entry['response']['2063157661'],
      'description' => $entry['response']['2063157596'],
      'setup_time' => $entry['response']['2063157660'],
      'teardown_time' => $entry['response']['2063157662'],
      'first_name' => $entry['response']['2063157388']['first_name'],
      'last_name' => $entry['response']['2063157388']['last_name'],
      'email' => $entry['response']['2063157389'],
      'recurring' => $entry['response']['2063157396'],
      'locations_string' => $locations_string

    );
    // Add needs_promotion to the event_data_arr.
    $yes_promotion_code = '446';
    echo 'Needs Promotion: ' . $entry['response']['2063158164']['value'] . '<br>';
    if($entry['response']['2063158164']['value'] == '446') {
      $event_data_arr['needs_promotion'] = true;
      echo 'The event needs promotion. <br>';
    }
    else {
      $event_data_arr['needs_promotion'] = false;
    };
   

    ?>
    <!-- DISPLAY ENTRY -->
    <p>****************************************</p>
    <p>Event Name: <?php echo $event_data_arr[
      'event_name'];?></p>
    <p>Date: <?php echo $event_data_arr[
      'date'];?></p>
    <p>Date Requested: <?php echo $event_data_arr[
      'date_requested'];?></p>
    <p>Id: <?php echo $entry['id'];?></p>
    <p>Locations: <?php echo $event_data_arr[
      'locations_string'];?></p>
      <p>Recurring: <?php echo $event_data_arr[
      'recurring'];?></p>
    
    <!-- DISPLAY BUTTONS -->
    <!-- Edit and Publish Button -->
    <form action="edit_event_view.php" method="post">
      <input type="hidden" name='event_data_arr' value='<?php echo json_encode($event_data_arr, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>'>
      <input type="submit" value="edit and publish">
    </form>

    <!-- Mark as Resolved Button -->
    <form action="form_responses_view.php" method="post">
      <input type="hidden" name="entry_id" value="<?php echo $event_data_arr['entry_id']; ?>">
      
      <input type="hidden" name="event_name" value="<?php echo $event_data_arr['event_name'];?>" >

      <input type="hidden" name="date" value="<?php echo $event_data_arr['date'];?>" >

      <input type="hidden" name="mark_as_resolved" value="true">
      <input type="submit" value="Mark as Resolved">
    </form>


    <?php
  }; // end of else

  
};


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



