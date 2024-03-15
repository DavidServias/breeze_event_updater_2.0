<?php

require_once('calendars.json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
  var_dump($_POST);
  $event_data_arr = json_decode($_POST['event_data_arr'], true);
  var_dump($event_data_arr);
  
?>

  <!-- FORM WITH PREPOPULATED DATA -->
  <h1>Confirm Event Details</h1>
    <form action="confirm_details_view.php" method="post">
      <!-- Event Name -->
      <label for="event_name">Event Name:</label>
      <input type="text" name="event_name" value="<?php echo $event_data_arr['event_name']; ?>" size='50'>
      <br>
      <!-- Date -->
      <label for="date">Date:</label>
      <input type="text" name="date" value="<?php echo $event_data_arr['date'] ?>">
      <br>
      <!-- Start Time -->
      <label for="start_time">Start Time:</label>
      <input type="text" name="start_time" value="<?php echo $event_data_arr['start_time']; ?>">
      <br>
      <!-- End Time -->
      <label for="end_time">End Time:</label>
      <input type="text" name="end_time" value="<?php echo $event_data_arr['end_time']; ?>">
      <br>
      <!-- Setup Time -->
      <label for="setup_time">Setup Time:</label>
      <input type="text" name="setup_time" value="<?php echo $event_data_arr['setup_time']; ?>">
      <br>
      <!-- Teardown Time -->
      <label for="teardown_time">Teardown Time:</label>
      <input type="text" name="teardown_time" value="<?php echo $event_data_arr['teardown_time']; ?>">
      <br>
      <!-- Description -->
      <label for="description">Description:</label><br>
      <textarea type="text" name="description" value="" rows="8" cols="100" ><?php echo $event_data_arr['description']; ?></textarea>
      <br><br>
      <!-- Locations -->
      <span>Locations: <?php echo $event_data_arr['locations_string'];?></span><br>
      <!-- Requested by -->
      <p>Requested by: <?php echo $event_data_arr['first_name'] . ' ' . $event_data_arr['last_name']; ?></p>
      <!-- Email -->
      <p>Email: <?php echo $event_data_arr['email']; ?></p>
      
      <!-- Radio Buttons to choose category_id -->
      <?php
      //create radio button for each category in file calendars.json.
      $calendars = json_decode(file_get_contents('calendars.json'), true);
      foreach ($calendars as $calendar) {
        echo '<label for="category_id">' . $calendar['name'] . '</label>';
        echo '<input type="radio" name="category_id" value="' . $calendar['id'] . '"><br>';
      }
      ?>
      <br>
       

      <!-- Hidden Inputs -->
      <input type="hidden" name='event_data_arr' value='<?php echo json_encode($event_data_arr, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>'>
    
      <!-- Submit -->
      <input type="submit" value="confirm details">
    </form>

<?php
}
else {
  echo 'No request to undo was made. <br>';
}
