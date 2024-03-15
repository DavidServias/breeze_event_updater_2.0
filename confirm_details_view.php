<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
  var_dump($_POST);

  $event_data_arr = json_decode($_POST['event_data_arr'], true);
  
  
  // Update the event data array with the user's changes.
  $event_data_arr['event_name'] = $_POST['event_name'];
  $event_data_arr['date'] = $_POST['date'];
  $event_data_arr['start_time'] = $_POST['start_time'];
  $event_data_arr['end_time'] = $_POST['end_time'];
  $event_data_arr['setup_time'] = $_POST['setup_time'];
  $event_data_arr['teardown_time'] = $_POST['teardown_time'];
  $event_data_arr['description'] = $_POST['description'];
  $event_data_arr['category_id'] = $_POST['category_id'];
  
  // Look up category id from calendars.json by name.
  // $calendars = json_decode(file_get_contents('calendars.json'), true);
  // foreach ($calendars as $calendar) {
  //   if ($calendar['name'] === $_POST['category_id']) {
  //     $event_data_arr['category_id'] = $calendar['id'];
  //     break;
  //   }
  // }

  
  // $event_data_arr['category_id'] = $_POST['category_id'];

  var_dump($event_data_arr);

  // Display the updated event data array.
  ?>
  <!-- DISPLAY UPDATED ENTRY -->
  <h1>Event Details</h1>
  <p>****************************************</p>
  <p>Event Name: <?php echo $event_data_arr['event_name'];?></p>
  <p>Date: <?php echo $event_data_arr['date'];?></p>
  <p>Start Time: <?php echo $event_data_arr['start_time'];?></p>
  <p>End Time: <?php echo $event_data_arr['end_time'];?></p>
  <p>Setup Time: <?php echo $event_data_arr['setup_time'];?></p>
  <p>Teardown Time: <?php echo $event_data_arr['teardown_time'];?></p>
  <p>Description: <?php echo $event_data_arr['description'];?></p>
  <p>Locations: <?php echo $event_data_arr['locations_string'];?></p>
  <p>Requested by: <?php echo $event_data_arr['first_name'] . ' ' . $event_data_arr['last_name']; ?></p>
  <p>Email: <?php echo $event_data_arr['email']; ?></p>
  <p>Recurring: <?php echo $event_data_arr['recurring']; ?></p>
  <p>Needs Promotion: <?php var_dump($event_data_arr['needs_promotion']); ?></p>
  <p>Category ID: <?php echo $event_data_arr['category_id']; ?></p>
   <?php 
   // Lookup category name from category id.
    $calendars = json_decode(file_get_contents('calendars.json'), true);
    foreach ($calendars as $calendar) {
      if ($calendar['id'] === $event_data_arr['category_id']) {
        echo '<p>Category_Id Name: ' . $calendar['name'] . '</p>';
        break;
      }
    }
  ?>


  <!-- CREATE EVENT BUTTON -->
  <form action="finished_view.php" method="post">
    <input type="hidden" name='event_data_arr' value='<?php echo json_encode($event_data_arr, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>'>
    <input type="submit" value="Create Event">
  </form>
  <!-- EDIT MORE BUTTON -->
  <form action="edit_event_view.php" method="post">
    <input type="hidden" name='event_data_arr' value='<?php echo json_encode($event_data_arr, true); ?>'>
    <input type="submit" value="Edit More">
  </form>


<?php
}
else {
  echo 'No request to undo was made. <br>';
}