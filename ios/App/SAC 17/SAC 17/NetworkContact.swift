//
//  NetworkContact.swift
//  SAC 17
//
//  Created on 3/17/17.
//  Copyright © 2017 Rowan IEEE. All rights reserved.
//

import Foundation
import Firebase

class NetworkContact: Equatable {
    
    var name = String();
    var email = String();
    var phone = String();
    var twitter = String();
    var snapchat = String();
    var instagram = String();
    var collabratec = String();
    
    var valid = false;
    
    init() {
        
    }
    
    init(withJSON inJSON: Data) {
        do {
            let json = try JSONSerialization.jsonObject(with: inJSON, options: []) as! [String: Any];
            
            if let inName = json["name"] as? String {
                name = inName;
            }
            
            if let inEmail = json["email"] as? String {
                email = inEmail;
            }
            
            if let inPhone = json["phone"] as? String {
                phone = inPhone;
            }
            
            if let inTwitter = json["twitter"] as? String {
                twitter = inTwitter;
            }
            
            if let inSnapchat = json["snapchat"] as? String {
                snapchat = inSnapchat;
            }
            
            if let inInstagram = json["instagram"] as? String {
                instagram  = inInstagram;
            }
            
            if let inCollabratec = json["collabratec"] as? String {
                collabratec = inCollabratec;
            }
            
            valid = true;
        } catch {
            print("Error parsing JSON: \(error)");
            valid = false;
        }
    }
    
    func createJSONForCurrentUser() -> Data {
        var contactInfo = [String: String]();
        
        if (FIRAuth.auth()?.currentUser?.displayName != nil) {
            contactInfo["name"] = FIRAuth.auth()?.currentUser?.displayName;
        }
        
        contactInfo["email"] = FIRAuth.auth()?.currentUser?.email;
        
        if (!Global.inst.phone.isEmpty) {
            contactInfo["phone"] = Global.inst.phone;
        }
        
        if (!Global.inst.twitter.isEmpty) {
            contactInfo["twitter"] = Global.inst.twitter;
        }
        
        if (!Global.inst.snapchat.isEmpty) {
            contactInfo["snapchat"] = Global.inst.snapchat;
        }
        
        if (!Global.inst.instagram.isEmpty) {
            contactInfo["instagram"] = Global.inst.instagram;
        }
        
        if (!Global.inst.collabratec.isEmpty) {
            contactInfo["collabratec"] = Global.inst.collabratec;
        }
        
        let data = try! JSONSerialization.data(withJSONObject: contactInfo, options: JSONSerialization.WritingOptions.prettyPrinted);
        print(data);
        
        return data;
    }
    
    static func == (first: NetworkContact, second: NetworkContact) -> Bool {
        return (first.name == second.name) && (first.email == second.email) && (first.phone == second.phone) && (first.twitter == second.twitter) && (first.snapchat == second.snapchat) && (first.instagram == second.instagram) && (first.collabratec == second.collabratec);
    }
    
}
