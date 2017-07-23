<?php
include 'firebase_include_js.php';
?>

<link rel="stylesheet" type="text/css" href="css/toggleSwitch.css">
<style>
  .option-text{
    margin-left: 15px;
  }
</style>

<script src="admin_restriction.js"></script>
<script>
  enableAuthenticationRequirement("settings", function() {});
</script>

<div class="content-wrapper clearfix restricted">
  <div class="container">

    <div class="sixteen columns">
      <div class="page-title clearfix">
        <h1>Settings</h1>
      </div>
    </div>

    <h2>Registration</h2>
    <div class="one column">
      <label class="switch allignleft">
        <input type="checkbox" id="<?php echo SETTING_ALLOW_USER_UPDATES ?>" name="<?php echo SETTING_ALLOW_USER_UPDATES ?>" onclick="setFlag(this);">
        <div class="slider round"></div>
      </label>
    </div>
    <div class="fifteen columns">
      <h3 class="option-text">Allow users to edit their profiles</h3>
    </div>

    <div class="divider"><span class="divider-line"></span><!--span class="divider-color"></span--></div>

    <h2>Voting</h2>
    <div class="one column">
      <label class="switch allignleft">
        <input type="checkbox" id="<?php echo SETTING_ALLOW_VOTING?>" name="<?php echo SETTING_ALLOW_VOTING ?>" onclick="setFlag(this);">
        <div class="slider round"></div>
      </label>
    </div>
    <div class="fifteen columns">
      <h3 class="option-text">Enable voting</h3>
    </div>

  </div>
</div>

<script>
function updateTogglesFromFirebase(){
  var checkboxes = document.getElementsByTagName('input');

  <?php
    $values = json_decode($firebase->get(PATH_TO_FLAGS));
    foreach ($values as $key => $setting) {
  ?>

      checkboxes["<?php echo $key ?>"].checked = <?php if($setting) { echo 1; } else { echo 0; } ?>;

  <?php
    }
  ?>
}
updateTogglesFromFirebase(); //Run on startup

function setFlag(box){
  var updateInfo = {};
  updateInfo[box.id] = box.checked;

  firebase.database().ref("<?php echo PATH_TO_FLAGS?>").update(updateInfo);
}
</script>
