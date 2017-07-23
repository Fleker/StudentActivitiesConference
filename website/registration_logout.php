<?php
    include 'firebase_client.php';
 ?>
<script>
firebase.auth().signOut().then(function() {
  // Sign-out successful.
    window.location.href = '?p=home';
}, function(error) {
  // An error happened.
    window.location.href = '?p=home';
});
</script>