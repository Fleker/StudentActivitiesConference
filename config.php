<?php

/* PAYMENTS */
const DEMO_KEYS = false;

const OUR_STRIPE_SECRET_KEY = "<our-stripe-secret-key>";
const OUR_STRIPE_PUBLIC_KEY = "<our-stripe-public-key>";

const STRIPE_DEMO_SECRET = "<test-stripe-secret-key>";
const STRIPE_DEMO_PUBLIC = "<test-stripe-publick-key>";

const STRIPE_SECRET_KEY = DEMO_KEYS ? STRIPE_DEMO_SECRET : OUR_STRIPE_SECRET_KEY;
const STRIPE_PUBLIC_KEY = DEMO_KEYS ? STRIPE_DEMO_PUBLIC : OUR_STRIPE_PUBLIC_KEY;

// Timestamps - Number of seconds since Unix Epoch. All based in GMT.
const EST_OFFSET = 60 * 60 * 5; // GMT - 5
const TIME_EARLY_BIRD = 1487116800 + EST_OFFSET; // Last timestamp for early bird - Feb 15
const TIME_REGULAR = 1489622400 + EST_OFFSET; // Last timestamp for regular registration - Mar 16
const TIME_LATE_BIRD = 1490313600 + EST_OFFSET; // Last timestamp for late registration - Mar 23; Beyond this point registration is closed.

$currentTimestamp = time();
if ($currentTimestamp < TIME_EARLY_BIRD) {
    define("TICKET_PRICE", 4000);
    define("PAYMENT_TITLE", "Early Bird");
    define("PAYMENT_START_DISP", "Jan 1<sup>st</sup>");
    define("PAYMENT_END_DISP", "Feb 14<sup>th</sup>");
} else if ($currentTimestamp < TIME_REGULAR) {
    define("TICKET_PRICE", 5000); // $50
    define("PAYMENT_TITLE", "Regular Registration");
    define("PAYMENT_START_DISP", "Feb 15<sup>th</sup>");
    define("PAYMENT_END_DISP", "March 15<sup>th</sup>");
} else {
    define("TICKET_PRICE", 6000); // $60
    define("PAYMENT_TITLE", "Late Registration");
    define("PAYMENT_START_DISP", "March 16<sup>th</sup>");
    define("PAYMENT_END_DISP", "March 23<sup>rd</sup>");
}

const OVERFLOW_TICKET_PRICE = 6000; // $60

const PAPER_NAME = "Pico Conference";

const COMPETITIONS = array(
        "Micromouse Kit and Scratch Robotics Competition" => array(
                "Photo" => "images/competitions/MicroMouse-Icon.png",
                "About" => "In this contest the contestant, or team of contestants, must design and build an autonomous robotic ”mouse”
capable of traversing a maze of standard dimensions from a specified corner to its center in the shortest time.",
                "Rules" => "docs/rules/sac-2017-micromouse.pdf"
            ),
        "Sumo Robotics Kit Competition" => array(
                "Photo" => "images/competitions/Sumo-Kit-Icon.png",
                "About" => " teams place an autonomous Pololu Zumo robot in a circular ring called a Dohyo, and much like the traditional
Japanese martial art of Sumo wrestling, each robot attempts to push the opposing robot out of the ring. Once
one of the robots has been pushed out of the ring, the round is over, and the one who remains in the Dohyo is
considered the winner. Whichever team’s robot successfully wins two rounds is permitted to proceed to the next
tier of the competition. In the kit competition, all teams will have the exact same robot; it is up to the team to write
the best and most strategic code in order to win.",
                "Rules" => "docs/rules/sac-2017-sumo.pdf"
            ),
        "Sumo Robotics Scratch Competition" => array(
                "Photo" => 'images/competitions/Sumo-Scratch-Icon.png',
                "About" => "Two teams place an autonomous robot in a circular ring called a Dohyo, and much like the traditional Japanese martial art of Sumo wrestling, each robot attempts to push the opposing robot out of the ring. Once one of the robots has been pushed out of the ring (or leaves the ring on its own power), the round is over, and the one who remains in the Dohyo is considered the winner. Whichever team’s robot successfully wins two rounds is permitted to proceed to the next tier of the competition.",
                "Rules" => "docs/rules/sac-2017-sumo-scratch.pdf"
            ),
        PAPER_NAME => array(
                "Photo" => "images/competitions/Pico-Icon.png",
                "About" => "An evolution of the Paper Competition from previous years, the IEEE SAC PicoConference gives undergraduate IEEE student members the opportunity to experience submitting a paper to and attending a conference. Throughout an engineer’s career, writing and presenting technical content is quintessential, regardless of if you are in the field or performing research. Researching, writing, and presenting a paper provides a student with invaluable early experience in expressing ideas relating to engineering. Since this event’s primary purpose is to improve a student’s communicative skills, no student should be discouraged from entering the contest due to a lack of technical knowledge.<br><br>Papers can be submitted using <a href='?p=pico_conf_dashboard' style='font-weight:bold'>this page</a>.",
                "Rules" => "docs/rules/sac-2017-paper.pdf",
                "Rubric" => "docs/rubrics/PicoConferenceRubric.pdf"
            ),
        "Brown Bag Circuit Competition" => array(
                "Photo" => "images/competitions/Brown-Bag-Icon.png",
                "About" => "In the Brown Bag competition, each team consisting of 1-4 members will be given a bag of various components
and then will be required to complete a challenge with some or all of the components. Each team will use only
the components and documentation provided to complete the challenge. They may not use additional notes,
references or aids, but must rely on their knowledge and ingenuity.",
                "Rules" => "docs/rules/sac-2017-brown.pdf"
            ),
        "Project Showcase" => array(
                "Photo" => "images/competitions/Project-Showcase-Icon.png",
                "About" => "The Project Showcase allows undergraduate students, as individuals or teams, to demonstrate what they have
learned or worked on throughout their undergraduate career. Projects/designs (other than competing robots) can
be related to school, work, or extracurricular activities that pertain to electrical and/or computer engineering.",
                "Rules" => "docs/rules/sac-2017-project.pdf",
                "Rubric" => "docs/rubrics/ProjectshowcaseRubric.pdf"
            ),
        "Physics Competition" => array(
                "Photo" => 'images/competitions/Physics-Icon.png',
                "About" => "This competition will consist of a design challenge and a conceptual portion. The conceptual
portion will be in the form of a written test given to each team. This competition will require a
basic understanding of mechanics and experimental design. All materials will be provided and
participants will not be allowed to use any supplies not provided. A detailed rule sheet will
become available the day of the competition.",
                "Rules" => "docs/rules/PhysicsRuleForWebsite.pdf"
            ),
        "Rockwell Automation Ethics Competition" => array(
                "Photo" => 'images/competitions/Ethics-Icon.png',
                "About" => "The SAC Ethics competition deals with an aspect of engineering that is often forgotten. Does my work infringe
on the rights or safety of others? Are there conflicts involving intellectual property? A case study which deals
with these issues will be presented to participants the day of the competition. They will have a period of time to
prepare a presentation with a response in accordance to the IEEE code of ethics, which will be presented to a
panel of judges. The presentation will be followed by a brief Q&amp;A session. This competition is an opportunity to
explore a prospect of engineering work that is not particularly glamorous but is extremely important.",
                "Rules" => "docs/rules/sac-2017-ethics.pdf"
            ),
        "Women in Engineering: WIE Teach Competition" => array(
                "Photo" => 'images/competitions/WIE-Icon.png',
                "About" => "The purpose of WIE Teach is to involve young girls in math and science based events and competitions to inspire
the next generation of female engineers. Local middle school girls will be competing alongside IEEE College
Students from Region 2 to grow the WIE Initiative. The mission of WIE Teach aligns with the WIE vision to
“Facilitate the development of...activities that promote the entry [of women] into...engineering programs”. In WIE
Teach, teams of 3-4 participants, comprising of 2 middle school (MS) girls and 1 or 2 college students, will be
given a set of components. The college participants will be explaining and instructing the MS girls on how to
create a circuit. Then, the students will be judged by the completeness of the circuit and how well the MS girls
understand the electrical engineering concepts behind the circuitry.",
                "Rules" => "docs/rules/sac-2017-wie.pdf"
            ),
        "T-Shirt Competition" => array(
                "Photo" => 'images/competitions/T-Shirt-Icon.png',
                "About" => "The T-Shirt competition allows participants to present their creativity by designing an IEEE related T-Shirt.",
                "Rules" => "docs/rules/2017T-ShirtRules.pdf"
            )
    );

/* SITEWIDE SETTINGS */
const PATH_TO_FLAGS = "/release/flags/";

const SETTING_ALLOW_USER_UPDATES = "allow_user_updates";
const SETTING_ALLOW_VOTING = "allow_voting";
?>
