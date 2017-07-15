function redirectUser(redirectAddress, displayError) {
  /* Redirects to the string value in redirectAddress
   * If displayError is true, deisplays an error meessage
   */
  if(displayError){
    errorMessage = "You do not have permission to view this page";
    alert(errorMessage);
  }
  window.location = redirectAddress;
}

function checkUser(user, acceptAddress, denyAddress, onsuccess) {
  /* Checks if the user is on the whitelist
   * If they are, redirects to acceptAddress
   * Else, redirects to denyAddress
   */
  if(user){
    name = user.displayName;
    email = user.email;
    uid = user.uid;

    $.get('admin_query.php', {user: user.uid}, function(data) {
        var response = JSON.parse(data);
//        console.log(response);
        if (response.admin) { //user is on whitelist
            onsuccess();
            $('.restricted').show();
        } else { //user account is not on whitelist
            redirectUser(denyAddress, true);
        }
    });
  }
  else{ //user is not signed in
    redirectUser(denyAddress, true);
  }
}

function enableAuthenticationRequirement(currentPage, onsuccess) {
    $('.restricted').hide();
    firebase.auth().onAuthStateChanged(function(user){
        if (currentPage != undefined) {
            var successAdd = "?p=" + currentPage, denyAdd = "?p=registration_signin&from=" + currentPage;
            checkUser(user, successAdd, denyAdd, onsuccess);
        }
    });  
}

function signIn(){
  var email = document.getElementById("email-box").value;
  var password = document.getElementById("password-box").value;

  firebase.auth().signInWithEmailAndPassword(email, password);
}

function signOut(){
  firebase.auth().signOut().then(function() {
    console.log('Signed Out');
  }, function(error) {
    console.error('Sign Out Error', error);
  });
}
