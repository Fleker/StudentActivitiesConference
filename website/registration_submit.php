<?php
include 'config.php'; // Load all variables
include 'firebase_include.php'; // Include Firebase API
require 'mail_config.php';

$DEBUG = false;
$DEBUG_DISABLE_STRIPE = false;

function sendRegistrationEmail($email, $name, $value) {
    // Send email to attendee
    $message = "Dear $name,<br>
    <p>Thank you for registering for the 2017 IEEE Region 2 Student Activities Conference (SAC), hosted at Rowan University located in Glassboro, NJ.  This year, conference registration has been redesigned, allowing each conference attendant the opportunity to individually complete registration. Conference registration is mandatory because it lets us know important information such as which competition you'll be competing in and what dinner choice you prefer. Please complete conference registration by clicking <a href='https://sac17.rowanieee.org/?p=registration_attendee&attendee=$value'>here</a>.</p>
    <p>If you have any questions about the registration process, please contact Jacob Culleny at jacob.culleny@ieee.org. We look forward to seeing you compete at the 2017 Region 2 SAC on April 8th, 2017!</p>
    <p>Kind Regards,<br>
    The Rowan University IEEE Student Branch</p>
    <p>Registration Link: https://sac17.rowanieee.org/?p=registration_attendee&attendee=$value</p>";
    sendEmail($email, $name, "Finish Registering for the 2017 IEEE Region 2 Student Activities Conference", $message);
}

if (isset($_POST['stripeToken'])) {
    // Test with this key: $20, 2 people: -KXxnZncqm77S_nj3gQK
    // Get the credit card details submitted by the form
    require_once('stripe/init.php');
    // Set your secret key: remember to change this to your live secret key in production
    // See your keys here: https://dashboard.stripe.com/account/apikeys
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    
    // Get Stripe payment
    $token = $_POST['stripeToken'];
    if ($DEBUG) {
        echo $token."<br>";
        echo print_r($_POST);
    }
    // Should we store this token for later use?
//    $firebase->
    // Create a charge: this will charge the user's card
    try {
        // Update value info
        $b_name = $_POST['buyer_name'];
        $b_email = $_POST['buyer_email'];
        $b_phone = $_POST['buyer_phone'];
        
        if ($DEBUG) {
//            echo print_r($charge)."<br><br>";
            echo "<br>Payment from ".$_POST['firebase_key']." for ".$_POST['price']."<br>"; 
            echo "$b_name, $b_email, $b_phone<br>";
        }
        
        $price = $_POST['price'];
        
        // Create a Customer:
        if (!$DEBUG_DISABLE_STRIPE) {
            $customer = \Stripe\Customer::create(array(
                "email" => $b_email,
                "source" => $token,
            ));

            $charge = \Stripe\Charge::create(array(
                "amount" => $price, // Amount in cents
                "currency" => "usd",
                "customer" => $customer->id,
                "description" => "Payment from user"
                ));
        }
//        echo "Charge was made<br>";
        
        $buyer = $_POST['firebase_key'];
        $value = json_decode($firebase->get(DEFAULT_PATH . "/buyers/$buyer"));
        if ($DEBUG) {
            echo "<br>PHP FB ".var_dump($firebase->get(DEFAULT_PATH . "/buyers/$buyer"))."<br><br>";
            echo "<br>Firebase info ".var_dump($value)."<br><br>";
        }   
        
        $valueAttendees = $value->attendees;
        $valueAttendees2 = $value->attendees;
        
        if (!isset($valueAttendees)) {
            echo $firebase->get(DEFAULT_PATH . "/buyers/".$buyer);
            echo "<br>$buyer<br>".print_r($value)."<br>";
            echo DEFAULT_PATH . "buyers/$buyer<br>";
            die("No people are registered!");   
        }
        // Update with more buyer information: index.php?p=registration_payments&payment=-K_WRXkiE1il0sI3m_k8&counselor=&school=Rowan
        echo DEFAULT_PATH . "/buyers/$buyer/name<br>";
        $firebase->set(DEFAULT_PATH . "/buyers/$buyer/name", $b_name);
        $firebase->set(DEFAULT_PATH . "/buyers/$buyer/email", $b_email);
        $firebase->set(DEFAULT_PATH . "/buyers/$buyer/phone", $b_phone);
        $firebase->set(DEFAULT_PATH . "/buyers/$buyer/stripe_token", $token);
        $firebase->set(DEFAULT_PATH . "/buyers/$buyer/submitted", time());
        
        
        foreach (explode(",", $valueAttendees2) as $value) {
            $attendee = json_decode($firebase->get(DEFAULT_PATH . "/attendees/$value"));
            $firebase->set(DEFAULT_PATH . "/attendees/$value/paid", "true");
            
            if ($DEBUG) {
                echo "<br>Attendee: ".print_r($attendee);
                echo "<br>".$value."<br><br>";
            }
            sendRegistrationEmail($attendee->email, $attendee->name, $value);
        }
        if ($DEBUG) {
            echo var_dump($valueAttendees)."*<br>";
            die ("Redirect to thanks page: registration_thanks&attendees=".$valueAttendees);
        }   
        header("Location: index.php?p=registration_thanks&attendees=".$valueAttendees);
    } catch(\Stripe\Error\Card $e) {
      // The card has been declined
        header("Location: index.php?p=registration_payments&payment=$key&error=card");
    } catch (\Stripe\Error\InvalidRequest $e) {
      // Invalid parameters were supplied to Stripe's API
        die("Error: invalid parameters were supplied to Stripe's API");
    } catch (\Stripe\Error\Authentication $e) {
      // Authentication with Stripe's API failed
      // (maybe you changed API keys recently)
        die("Error: We cannot authenticate on our end");
    } catch (\Stripe\Error\ApiConnection $e) {
      // Network communication with Stripe failed
        die("Error: Network communication with Stripe failed");
    } catch (\Stripe\Error\Base $e) {
      // Display a very generic error to the user, and maybe send
      // yourself an email
        die("Very generic error happened");
    } catch (Exception $e) {
      // Something else happened, completely unrelated to Stripe
    }
} else if (isset($_POST['attendee_key'])) {
    // Update attendee info into form
    if ($DEBUG) {
        echo print_r($_POST);
    }
    $attendee = array(
        'name' => $_POST['attendee_name'],
        'email' => $_POST['attendee_email'],
        'tshirt' => $_POST['attendee_tshirt'],
        'password' => 'has been set',
        'updated' => time()
        );
    if (isset($_POST['attendee_school'])) {
        $attendee['school'] = $_POST['attendee_school'];
    }
    if (isset($_POST['attendee_year'])) {
        $attendee['year'] = $_POST['attendee_year'];
    }
    if (isset($_POST['attendee_ieee_number'])) {
        $attendee['ieee_number'] = $_POST['attendee_ieee_number'];
    }
    if (isset($_POST['attendee_competition'])) {
        $attendee['competition'] = $_POST['attendee_competition'];
    }
    if (isset($_POST['attendee_toc'])) {
        $attendee['toc'] = $_POST['attendee_toc'];
    }
    if (isset($_POST['attendee_paper_competition'])) {
        $attendee['paper_competition'] = $_POST['attendee_paper_competition'];
    } else {
        $attendee['paper_competition'] = false;
    }
    if (isset($_POST['attendee_tshirt_competition'])) {
        $attendee['tshirt_competition'] = $_POST['attendee_tshirt_competition'];
    } else {
        $attendee['tshirt_competition'] = false;
    }
    if (isset($_POST['attendee_project_showcase'])) {
        $attendee['project_showcase'] = $_POST['attendee_project_showcase'];
    } else {
        $attendee['project_showcase'] = false;
    }
    if (isset($_POST['attendee_hotel'])) {
        $attendee['hotel_opt_out'] = $_POST['attendee_hotel'];
    } else {
        $attendee['hotel_opt_out'] = false;
    }
    if (isset($_POST['banquet_entree'])) {
        $attendee['banquet_entree'] = $_POST['banquet_entree'];
    }
    if (isset($_POST['attendee_banquet'])) {
        $attendee['banquet_opt_out'] = $_POST['attendee_banquet'];   
    } else {
        $attendee['banquet_opt_out'] = false;
    }
    if (isset($_POST['attendee_special'])) {
        $attendee['special'] = $_POST['attendee_special'];   
    }
    if (isset($_POST['attendee_phone'])) {
        $attendee['phone'] = $_POST['attendee_phone'];   
    }
    if (isset($_POST['attendee_sex'])) {
        $attendee['sex'] = $_POST['attendee_sex'];   
    }
    if (isset($_POST['guest_banquet_entree'])) {
        $attendee['guest_banquet_entree'] = $_POST['guest_banquet_entree'];   
    }
    if (isset($_POST['guest_name'])) {
        $attendee['guest_name'] = $_POST['guest_name'];
    }
    if ($DEBUG) {
        echo print_r($attendee);
        die();
    }
    if (isset($_POST['attendee_key'])) {
        $firebase->update(DEFAULT_PATH . "/attendees/".$_POST['attendee_key'], $attendee);
        header("Location: index.php?p=registration_attendee&attendee=".$_POST['attendee_key']."&update");
    }
} else {    
    // Submit user data into form
    if ($DEBUG) {
        echo print_r($_POST)."<br>";
        echo "<br><br>^ POST, v GET<br><br>";
        echo print_r($_GET)."<br>";
    }
    // Figure out how many attendees are in the list
    $i = 0;
    $quit = false;
    $firebase_keys = array();
    $isCounselor = false;
    while (!$quit) {
        if (!isset($_POST["attendee_name$i"])) {
            if ($DEBUG) {
//                echo "    " . $_POST["attendee_name$i"]."<br>";
                echo "Cannot find \$_POST['attendee_name$i']<br>";   
            }
            $quit = true;
            break;   
        }
        // Submit attendee info
        $name = $_POST['attendee_name'.$i];
        $email = $_POST['attendee_email'.$i];
        $attendee = array(
            'name' => $name,
            'email' => $email,
            'counselor' => $_POST['attendee_counselor'.$i],
            'registered' => time(),
            'paid' => 'false',
            'ticket_price' => $_POST['attendee_ticket_price'.$i],
            'school' => $_POST['buyer_school']
            );
        $isCounselor = ($attendee['counselor'] === true || $attendee['counselor'] === 'true') || $isCounselor;
        if ($isCounselor) {
            $attendee['paid'] = true;
        }
        
        if ($DEBUG) {
            echo var_dump($attendee)."<br>";
            echo var_dump($isCounselor)."<br>";
            echo print_r($attendee)."<br>";
            if ($isCounselor) {
                echo "$name is a counselor<br>";   
            }
        }
        // Firebase insertion
        if (!empty($_POST['attendee_firebase_key'.$i])) {
            // This should ALWAYS be true.
            $rawkey = $firebase->set(DEFAULT_PATH . '/attendees/' . $_POST['attendee_firebase_key'.$i], $attendee);
        }
        if ($DEBUG) {
            echo "Submitted, got back ".$rawkey."<br>";
            echo print_r(json_decode($rawkey))."<br>";
            $jsondecode = json_decode($rawkey);
            echo $jsondecode->name."<br>";
//            echo print_r(json_decode($rawkey)['name'])."<br>";
        }
        $key = json_decode($rawkey)->name;
        if ($DEBUG) {
            echo "Submitting attendee ".$key."<br>";   
        }
        $firebase_keys[$i] = $_POST['attendee_firebase_key'.$i];
        // Move to the next attendee
        $i++;
    }
    if ($DEBUG) {
        echo ($i)." attendees<br>";   
    }
    // Submit buyer info
    $firebase_keys_string = implode(",", $firebase_keys);
    $buyer = array(
        'attendees' => $firebase_keys_string,
        'school' => $_POST['buyer_school']
        );
    if ($DEBUG) {
        echo "Submitting ".print_r($buyer)."<br>";   
    }
    $rawkey = $firebase->push(DEFAULT_PATH . '/buyers/', $buyer);
    $key = json_decode($rawkey)->name;
    // We have ($i + 1) attendees.
    
    // Redirect users to the payment screen
//    $COST_PER_PERSON = 10;
//    $ticket_price = 10 * ($i);
    // TODO user auth
    if ($DEBUG) {
        echo "COUNSELOR: ".count($firebase_keys)."  '".$isCounselor."' ".count($isCounselor)."<br>";
        if (count($firebase_keys) == 1 && $isCounselor) {
            echo "true";  
        }  else {
            echo "false";   
        }
        echo "<br>";
        echo "Redirect the user to ?payment=".$key."<br>";
        die();
    }
//    echo "200"; // Report that everything was okay. (But we're redirecting the user so it's irrelevant)
    
//    echo file_get_contents("?p=registration_payments&payment=$key");
    if (count($firebase_keys) == 1 && $isCounselor) {
        echo "Thanks!<br>";
        $rawkey = $firebase->set(DEFAULT_PATH . '/attendees/' . $_POST['attendee_firebase_key0'] . '/paid', true);
        sendRegistrationEmail($_POST['attendee_email0'], $_POST['attendee_name0'], $firebase_keys_string); // Should just be a single key and POST_0
        header("Location: index.php?p=registration_thanks&attendees=" . $_POST['attendee_firebase_key0']);
    } else {
        echo "Payment<br>";
        $school = $_POST['buyer_school'];
        header("Location: index.php?p=registration_payments&payment=$key&counselor=$isCounselor&school=$school");
    }
    
    if ($DEBUG) {
        die("");
    }   
}
exit();
?>
