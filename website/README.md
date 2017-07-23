# Web App

This is the website for the Student Activities Conference. It provides users with plenty of competition information as well as a series of administrative tools.

## Requirements
To run this, you need

* A server with PHP v7+
* A GMail account. This will be used for sending out periodic emails.
* A [Firebase](http://firebase.google.com) project. This will be used for storing data.
* A [Stripe](http://stripe.com) project. This will be used for processing payments.

## Modifications
In order to get this project working completely, you will need to make several modifications to the webpages.

1. Replace the domain name in `app/index.php`
1. Update the values in `config.php` to reflect your competition
1. Update the URLs and the api key in `firebase_client.php`
1. Update the URLs and the api key in `firebase_include_js.php`
1. Update the email address and password in `mail_config.php`

Optionally:

1. Replace the value of `data-key` in `demo/pay.php` and `demo/stripe_test.php` with a test key from 

## Other things you'll want to do
* Replace the documents in the `docs/` directory with your own rubrics and rules
* Replace the images in the `images/` and the `photos/` directory with your own images and photos
* Give credit to Rowan University IEEE for creating the initial website
* Update the pages with the information of your event
* Update `datalist_academic_year.php`, `datalist_select_university.php`, and `datalist_university.php` if necessary
* Update the content of `footer.php`
* Update the information of `manifest.json` and `manifest_sign.json` for web app purposes.

## Other things you may want to do
* Update the PHP code to use Node.JS instead. With that migration, you may need to rewrite a lot of the server-side code. But you'll be able to take greater advantage of Firebase.
    * Right now, only the Firebase database supports PHP. Our site has a weird mix of client-side and server-side Firebase calls due to this limitation.
* Clean up the code
    * There are a few places where the code just isn't that good. It can be certainly be improved.
* Keep committing code
    * You should definitely improve this code and also make it available for the universities using it _next_ year. That way this continues to be useful.
    * Don't commit private keys or passwords. Or if you do, don't make those changes public.
    
# What's Inside
There are numerous webpages and useful tools

## PicoConference / Paper Competition
To make it easier to manage the event, there is

* `pico-conference/timer.html` - A handy timer tool with a focus on simple color coding and large font
* `pico_conf_admin.php` - An admin console, giving you all the submissions and highlights those who haven't submitted papers yet. It also lets you email groups. It also has a suggested schedule for presenters.
* `pico_conf_dashboard.php` - This is where Pico Conference attendees can submit their paper and add secondary authors

## T-Shirt Competition
To make it easier to manage the event, there is

* `tshirt_admin.php` - An admin console, showing you all the submissions and highlights those who haven't submitted yet.
* `tshirt_dashboard.php` - A dashboard where attendees can submit their t-shirts

## Administrative Tools
There are a number of tools for admin accounts. Admins are those in the database as `release/admin/<their-user-id>: true`. Admins will see an additional set of links for these tools on the website. These pages will verify the user is an admin before letting them access the page.

* `admin_banquet_tables.php` - Quickly lets admins set the tables of each attendee at the banquet. It includes options to change the number of tables and chairs per table. It shows errors in red if too many users are at one table. It shows errors if a chosen table is invalid. Clicking on each table number at the top shows the guests there. It supports VIP guests. It lets you export a series of dinner cards with each person, their table number, and their selected meal.
* `admin_export.php` - This page lets admins back-up Firebase data locally. It can also be set up to be based on a timer. The webpage needs to be loaded in your browser and running for the timer to work.
* `admin_search.php` - This shows all attendees of the event with options to filter out certain users and search for them. It also supports exporting attendees to a CSV file. It also supports sending bulk emails to particular attendees.
* `admin_vip.php` - This allows the admin to add VIPs - non-students and not counselors - to the event, mainly for purposes of the banquet
* `admin_vip_selfregistration.php` - This allows individuals to self-register as VIPs. But they will need to visit with a particular password, set as `PAGE_PASSWORD`, eg. `admin_vip_selfregistration.php?password=spacex_elon_musk`
* `photo_approval.php` - Only admins can approve photos submitted by users before they go in the gallery
* `registration_stats.php` - Provides a series of statistics on users, including banquet info, school info, and more
* `settings.php` - Provides a series of settings which can be modified
* `users_who_have_password_issues.php` - You should not need this page
* `vote_results.php` - Shows the results of user voting

## Digital Signage
Do you have a spare TV or monitor? You can easily turn it into a digital sign, giving attendees information about the conference as a whole or for a particular room. You can just point a web browser to `signage/index.html` with several GET parameters. With `signage/index.html?floor=1`, you can optionally show all events for the first floor. With `signage/index.html?room=101`, you can optionally show all events for room 101.

How does it work? It pulls from a public Google Calendar or any iCal file.

### Google Calendar
You can easily load your events into a Google Calendar, letting others see it and easily add it to their phones. It can also be accessed with an API, allowing you to pull it into a custom interface.

1. First, go to `signage/config.php` and set `CALENDAR_TYPE` = `TYPE_GOOGLE_CALENDAR`.
1. Go to `signage/config-google-calendar.php` and update the values for `calendarId` and `API_KEY`
1. Update the values for start and end of the event
1. Go to `signage/config.js` and update the `event_of_event` variable. It is based on the Unix epoch.

To set a specific room or floor for your event, do it as a JSON string in the event's description.

### iCal
You can easily load your events into any calendar program and export it as an iCal, making it cross-platform and easy to parse. It also doesn't need an API key.

Note: You can get a iCal URL for your Google Calendar in the Google Calendar settings.

1. First, go to `signage/config.php` and set `CALENDAR_TYPE` = `TYPE_ICAL`.
1. Go to `signage/config-ical.php` and update the values for `iCalUrl`
1. Update the values for start and end of the event
1. Go to `signage/config.js` and update the `event_of_event` variable. It is based on the Unix epoch.

To set a specific room or floor for your event, do it as a JSON string in the event's description.

## Easer Egg - MemSat
MemSat the game is an Asteroids-based game where you pilot a satellite in space, shooting different passive elements at asteroids with high/low voltage and high/low frequency.

## Other Pages
There are many other pages in this repository. They should be updated as needed with new content.

* `app.php` - A informational page telling you how to download the app.
* `banquet.php` - An informational page about the banquet.
* `competitions.php` - An informational page about the competitions. It is configured in `config.php`.
* `faq.php` - An informational page about questions and answers.
* `home.php` - The frontpage about the event
* `hotel.php` - An informational page about the hotel
* `photos.php` - Shows approved photos from the event, with captions
* `privacy-policy.html` - A default privacy policy generated by [this website](https://app-privacy-policy-generator.firebaseapp.com/)
* `results.php` - Shows the results of each competition
* `schedule.php` - Shows the schedule of events at the competition
* `shuttles.php` - Shows the location of shuttles in realtime, if applicable
* `sponsors.php` - Shows the event sponsors
* `sponsors_now.php` - Lets a sponsor pay directly via Stripe
* `sponsorwhy.php` - A page for potential sponsors showing the benefits
* `vote.php` - Lets users vote for t-shirt and project showcase

### Registration
* `registration_attendee.php` - Allows an attendee to complete registration or update their account information
* `registration_buyer.php` - Allows one to purchase tickets for themselves, or for multiple people including a counselor
* `registration_helper.php` - Helper functions for registration pages
* `registration_logout.php` - Logs out the user and returns them to the home page
* `registration_payments.php` - Handles the payment process for registration
* `registration_school_count.php` - Gets the number of attendees who went to a particular school
* `registration_signin.php` - Logs a user in
* `registration_submit.php` - Server-side logic only
* `registration_thanks.php` - A nice page thanking the user for registering

# Firebase
Firebase was used in this website for purposes of authentication, image storage, and database. It provides many server-side capabiltiies which made it convenient. We used a certain data structure throughout the website. This structure doesn't need to be followed, but if it is modified, you may need to modify the rest of the website.

```JSON
    release: /* We used alpha, beta, and release as root structures to test things out without affecting everything else */
        admins: /* User IDs for admins */
            <user-id>: true
        attendees: /* Not every attendee will have every value */
            <user-id>:
                banquet_entree: "baked_flounder",
                banquet_opt_out: false,
                competition: "brownbag"
                counselor: false,
                email: "user@example.com",
                guest_banquet_entree: "",
                guest_name: "",
                hotel_opt_out: false,
                ieee_number: 1234
                name: "John Smith"
                paid: true,
                paper_competition: false,
                password: "has been set",
                phone: "8675309"
                project_showcase: false,
                school: "Orchard University",
                special: "n/a",
                table: "4",
                toc: "true",
                tshirt: "medium",
                tshirt_competition: false,
                updated: 1234,
                vip: true,
                year: "sophomore"
        buyers:
            <random-key>:
                attendees: "1234,1235,1236",
                email: "john@smith.com",
                name: "John Smith",
                phone: "8675309",
                school: "Orchard University",
                stripe_token: "tok_1234",
                submitted: 1236
        flags:
            allow_user_updates: false,
            allow_voting: false
        images:
            <random-key>:
                approved: true,
                caption: "A caption",
                path: "/images/IMG.jpg",
                rated: true,
                timestamp: 1234,
                uid: "<user-id>"
        memsat:
            <random-key>:
                score: 100,
                user: "<user-id>",
                username: "John Smith"
        papers:
            <user-id>:
                downloadUrl: "https://firebasestorage.googleapis.com/v0/b/sac/temp/papers/araradfg.pdf",
                lastUpdate: "1234",
                paperFile: "papers/myresearch.pdf",
                status: "Submitted",
                teammates: "<user-id>,<user-id>",
                teammates_emails: "janesmith@example.com"
        profile-images:
            <user-id>:
                <random-key>: "/profile-images/t235.jpg"
        projects:
            <user-id>:
                abstract: "My abstract",
                downloadUrl: "https://firebasestorage.googleapis.com/v0/b/sac/temp/projects/araradfg.jpg",
                lastUpdate: "12343",
                paperFile: "projects/myproject.PNG",
                status: "Submitted",
                teammates: "<user-id>,<user-id>",
                teammates_emails: "janesmith@example.com"
                title: "My Title"
        sponsors:
            <random-key>:
                amount: 1000 /* in cents */,
                company: "Starfleet",
                contact: "Jean-Luc Picard",
                date: 41052
                email: "jean@starfleet.gov",
                phone: "8675309",
                stripe_token: "tok_1234"
        tshirts:
            <user-id>:
                downloadUrl: "https://firebasestorage.googleapis.com/v0/b/sac/temp/projects/araradfg.jpg",
                lastUpdate: 12,
                paperFile: "tshirts/test.png",
                status: "Submitted"
```

# Common Pit-falls

## JS errors
Use Chrome's built-in development tools to check the console for errors and do many other debugging things

## Strings v Numbers

Sometimes, Firebase stores a number or boolean as a string. When you try to do a boolean comparison, it fails. Make sure you validate the type of your variables.

## Oops, I messed up a file
Using Git allows you to view the history of files. If you maintain using Git, you can revert back changes quickly.

# Contact
Contact [handnf@gmail.com](me) if you have any questions about the site.