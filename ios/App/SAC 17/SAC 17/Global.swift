//
//  Global.swift
//  SAC 17
//
//  Created on 1/13/17.
//  Copyright © 2017 Rowan IEEE. All rights reserved.
//

import UIKit
import Firebase

class Global {
    
    static let inst = Global();
    
    let sacBrown = UIColor(red: 88.0 / 255.0, green: 23.0 / 255.0, blue: 12.0 / 255.0, alpha: 1.0); // 58170C
    let sacYellow = UIColor(red: 252.0 / 255.0, green: 208.0 / 255.0, blue: 14.0 / 255.0, alpha: 1.0); // FCD00E
    
    // Website
    let webSacMain = "https://sac17.rowanieee.org";
    let webURLApp = "?app&";
    
    // Google Firebase
    var sacLinksNoFirebase = [String : String]();
    var sacLinksFirebase = [String : String]();
    
    var signedIntoFirebase: Bool;
    
    let firebaseStorageLocation = "gs://sac17-9fc02.appspot.com";
    
    let votingLink = "https://sac17.rowanieee.org/vote_api.php";
    let baseVoting = "release/";
    let allowVotingFlagLocation = "flags/allow_voting";
    let attendeesLocation = "attendees/";
    let tshirtLocation = "vote_tshirt";
    let projectLocation = "vote_project";
    let imagesLocation = "images/";
    
    var canVote: Bool;
    var tshirts = [TShirt]();
    var projects = [Project]();
    var ref: FIRDatabaseReference!;
    
    // Google Nearby
    var nearbyContacts = [NetworkContact]();
    var nearbyPermissionGranted: Bool;
    var microphoneAllowed: Bool;
    
    // My contact info
    var phone = String();
    var twitter = String();
    var snapchat = String();
    var instagram = String();
    var collabratec = String();
    
    private init() {
        canVote = false;
        
        GNSMessageManager.setDebugLoggingEnabled(false);
        nearbyPermissionGranted = GNSPermission.isGranted();
        microphoneAllowed = false;
        
        let basePageURL = "https://sac17.rowanieee.org/?app&p=";
        
        // Menu items
        sacLinksNoFirebase["01.Home"] = "\(basePageURL)home";
        sacLinksNoFirebase["02.Photos"] = "\(basePageURL)photos";
        sacLinksNoFirebase["03.Upload Photo"] = "funcsigninuploadphotos";
        sacLinksNoFirebase["04.Schedule"] = "\(basePageURL)schedule";
        sacLinksNoFirebase["05.Shuttles"] = "\(basePageURL)shuttles";
        sacLinksNoFirebase["06.Competitions"] = "\(basePageURL)competitions";
        sacLinksNoFirebase["07.Sponsors"] = "\(basePageURL)sponsors";
        sacLinksNoFirebase["08.Hotel"] = "\(basePageURL)hotel";
        sacLinksNoFirebase["09.Banquet"] = "\(basePageURL)banquet";
        sacLinksNoFirebase["10.Voting"] = "funcsigninvoting";
        sacLinksNoFirebase["11.Network"] = "funcsigninnetwork";
        sacLinksNoFirebase["12.FAQ"] = "\(basePageURL)faq";
        sacLinksNoFirebase["13.About"] = "funcabout";
        sacLinksNoFirebase["14.Sign In"] = "funcsignin";
        
        sacLinksFirebase["01.Home"] = "\(basePageURL)home";
        sacLinksFirebase["02.Photos"] = "\(basePageURL)photos";
        sacLinksFirebase["03.Upload Photo"] = "funcuploadphotos";
        sacLinksFirebase["04.Schedule"] = "\(basePageURL)schedule";
        sacLinksFirebase["05.Shuttles"] = "\(basePageURL)shuttles";
        sacLinksFirebase["06.Competitions"] = "\(basePageURL)competitions";
        sacLinksFirebase["07.Sponsors"] = "\(basePageURL)sponsors";
        sacLinksFirebase["08.Hotel"] = "\(basePageURL)hotel";
        sacLinksFirebase["09.Banquet"] = "\(basePageURL)banquet";
        sacLinksFirebase["10.Voting"] = "funcvoting";
        sacLinksFirebase["11.Network"] = "funcnetwork";
        sacLinksFirebase["12.FAQ"] = "\(basePageURL)faq";
        sacLinksFirebase["13.About"] = "funcabout";
        sacLinksFirebase["14.Sign Out"] = "funcsignout";
        
        signedIntoFirebase = false;
    }
    
    func loadFromUserDefaults() {
        let defaults = UserDefaults.standard;
        
        if let ph = defaults.string(forKey: "phone") {
            phone = ph;
        }
        if let ph = defaults.string(forKey: "twitter") {
            twitter = ph;
        }
        if let ph = defaults.string(forKey: "snapchat") {
            snapchat = ph;
        }
        if let ph = defaults.string(forKey: "instagram") {
            instagram = ph;
        }
        if let ph = defaults.string(forKey: "collabratec") {
            collabratec = ph;
        }
    }
    
    func saveToUserDefaults() {
        let defaults = UserDefaults.standard;
        
        defaults.setValue(phone, forKey: "phone");
        defaults.setValue(twitter, forKey: "twitter");
        defaults.setValue(snapchat, forKey: "snapchat");
        defaults.setValue(instagram, forKey: "instagram");
        defaults.setValue(collabratec, forKey: "collabratec");
    }
    
    func resetNearbyContacts() {
        nearbyContacts = [NetworkContact]();
    }
    
    func getOrderedMenuKeys() -> [String] {
        var itemsInOrder: [String];
        
        if (signedIntoFirebase) {
            itemsInOrder = Array(sacLinksFirebase.keys).sorted(by: <);
        } else {
            itemsInOrder = Array(sacLinksNoFirebase.keys).sorted(by: <);
        }
        
        return itemsInOrder;
    }
    
    func getTitlesOfMenuItems() -> [String] {
        let itemsInOrder = getOrderedMenuKeys();
        var itemTitlesInOrder = [String]();
        
        for item in itemsInOrder {
            let start = item.index(item.startIndex, offsetBy: 3);
            let end = item.endIndex;
            itemTitlesInOrder.append(item[start..<end]);
        }
        
        return itemTitlesInOrder;
    }
    
    func firebaseJustSignedIn() {
        signedIntoFirebase = true;
    }
    
    func firebaseJustSignedOut() {
        signedIntoFirebase = false;
    }
    
}
