//
//  NetworkViewController.swift
//  SAC 17
//
//  Created on 3/17/17.
//  Copyright © 2017 Rowan IEEE. All rights reserved.
//

import UIKit
import AVFoundation
import AddressBook
import Contacts

class NetworkViewController: BaseViewController, UITableViewDelegate, UITableViewDataSource {
    
    var nearbyManager: GNSMessageManager!;
    var networkPublication: GNSPublication!;
    var networkSubscription: GNSSubscription!;
    var networkPermission: GNSPermission!;
    
    var book: ABAddressBook!;
    var duplicate: ABRecord!;
    
    @IBOutlet weak var tableView: UITableView!;
    
    var backButton: UIBarButtonItem!;
    
    override func viewDidLoad() {
        super.viewDidLoad();
        
        // Back button to go back to the menuViewController
        backButton = UIBarButtonItem(image: UIImage(named: "ChevronLeft"), style: UIBarButtonItemStyle.plain, target: self, action: #selector(NetworkViewController.backAction));
        navigationItem.leftBarButtonItem = backButton;
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated);
        
        Global.inst.resetNearbyContacts();
        makeSurePermissionsAreCorrectAndBroadcast();
    }
    
    override func viewWillDisappear(_ animated: Bool) {
        super.viewWillDisappear(animated);
        
        networkPublication = nil;
        networkSubscription = nil;
        networkPermission = nil;
        nearbyManager = nil;
    }
    
    func makeSurePermissionsAreCorrectAndBroadcast() {
        networkPermission = GNSPermission(changedHandler: { (granted: Bool) in
            Global.inst.nearbyPermissionGranted = granted;
            if (!granted) {
                print("Permission for Nearby was denied");
                self.backAction();
            } else {
                self.checkMicrophoneAndBroadcast();
            }
        });
        
        if (!GNSPermission.isGranted()) {
            // Give user yes no option. Will automatically check the mic and broadcast
            let alert = UIAlertController(title: "Permission Requested", message: "Networking needs permission to allow you to exchange contact information easily. Would you like to grant permission?", preferredStyle: .alert);
            
            let yesAction = UIAlertAction(title: "Yes", style: .default) { (action) in
                GNSPermission.setGranted(true);
            };
            let noAction = UIAlertAction(title: "No", style: .default) { (action) in
                self.backAction();
            };
            
            alert.addAction(noAction);
            alert.addAction(yesAction);
            
            if #available(iOS 9.0, *) {
                alert.preferredAction = yesAction;
            } else {
                // Don't bold the OK button
            };
            
            self.present(alert, animated: true, completion: nil);
            
            alert.view.tintColor = Global.inst.sacBrown;
        } else {
            checkMicrophoneAndBroadcast();
        }
    }
    
    func checkMicrophoneAndBroadcast() {
        AVAudioSession.sharedInstance().requestRecordPermission( { (granted: Bool) in
            if (granted) {
                Global.inst.microphoneAllowed = true;
                self.nearbyManager = GNSMessageManager(apiKey: "AIzaSyC1pLrswaIqbuIe_UL1Ixx8LL2_VTJxz7c", paramsBlock: { (params: GNSMessageManagerParams?) in
                    guard let params = params else { return }
                    params.microphonePermissionErrorHandler = { (hasError: Bool) in
                        Global.inst.microphoneAllowed = !hasError;
                        if (hasError) {
                            print("Nearby microphone permission error");
                            self.backAction();
                        }
                    }
                });
                
                let alert = UIAlertController(title: "Notice", message: "Please make sure that the volume is up and this device is not connected to any external microphones or speakers.", preferredStyle: .alert);
                
                let okAction = UIAlertAction(title: "OK", style: .default) { (action) in
                    // Do nothing
                }
                
                alert.addAction(okAction);
                
                if #available(iOS 9.0, *) {
                    alert.preferredAction = okAction;
                } else {
                    // Don't bold the OK button
                };
                
                self.present(alert, animated: true, completion: nil);
                
                alert.view.tintColor = Global.inst.sacBrown;
                
                self.publishMessage();
                self.subscribeToMessages();
            } else {
                // Display alert and jumps back
                print("Microphone permission not granted");
                
                let alert = UIAlertController(title: "Permission Denied", message: "Networking needs the microphone to allow you to exchange contact information easily. Go to Settings -> Privacy -> Microphone and enable this app.", preferredStyle: .alert);
                
                let okAction = UIAlertAction(title: "OK", style: .default) { (action) in
                    self.backAction();
                }
                
                alert.addAction(okAction);
                
                if #available(iOS 9.0, *) {
                    alert.preferredAction = okAction;
                } else {
                    // Don't bold the OK button
                };
                
                self.present(alert, animated: true, completion: nil);
                
                alert.view.tintColor = Global.inst.sacBrown;
            }
        });
    }
    
    func publishMessage() {
        let message = NetworkContact().createJSONForCurrentUser();
        networkPublication = nearbyManager.publication(with: GNSMessage(content: message), paramsBlock: {
            (params: GNSPublicationParams?) in
            guard let params = params else { return }
            params.strategy = GNSStrategy(paramsBlock: {
                (params: GNSStrategyParams?) in
                guard let params = params else { return }
                params.discoveryMediums = .audio
            })
        });
    }
    
    func subscribeToMessages() {
        networkSubscription = nearbyManager.subscription(messageFoundHandler: { (message: GNSMessage?) in
            self.messageAppeared(message!);
        }, messageLostHandler: { (message: GNSMessage?) in
            self.messageDisappeared(message!);
        }, paramsBlock: { (params: GNSSubscriptionParams?) in
            guard let params = params else { return }
            params.strategy = GNSStrategy(paramsBlock: { (params: GNSStrategyParams?) in
                guard let params = params else { return }
                params.allowInBackground = false;
                params.discoveryMediums = .audio;
            })
        });
    }
    
    func messageAppeared(_ message: GNSMessage) {
        let contact = NetworkContact(withJSON: message.content);
        print("Found contact for \(contact.email)");
        Global.inst.nearbyContacts.append(contact);
        tableView.reloadData();
    }
    
    func messageDisappeared(_ message: GNSMessage) {
        let contact = NetworkContact(withJSON: message.content);
        print("Lost contact for \(contact.email)");
        if let index = Global.inst.nearbyContacts.index(of: contact) {
            Global.inst.nearbyContacts.remove(at: index);
            tableView.reloadData();
        }
    }
    
    func numberOfSections(in tableView: UITableView) -> Int {
        return 1;
    }
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return Global.inst.nearbyContacts.count + 1;
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "PersonCell", for: indexPath) as! MenuTableViewCell;
        
        if (indexPath.row == 0) {
            cell.textLabel?.textAlignment = .center;
            cell.textLabel?.text = "Listening...";
            return cell;
        } else {
            cell.textLabel?.textAlignment = .left;
        }
        
        let currentContact = Global.inst.nearbyContacts[indexPath.row - 1];
        
        if (!currentContact.name.isEmpty) {
            cell.textLabel?.text = currentContact.name;
        } else {
            cell.textLabel?.text = currentContact.email;
        }
        
        return cell;
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        tableView.deselectRow(at: indexPath, animated: true);
        
        if (indexPath.row != 0) {
            getAddressBook(index: indexPath.row - 1);
        }
    }
    
    func getAddressBook(index: Int) {
        let status = ABAddressBookGetAuthorizationStatus();
        switch status {
        case .denied, .restricted:
            notifyUserToEnableAddressBook();
            break;
        case .authorized, .notDetermined:
            var error: Unmanaged<CFError>? = nil;
            let adbk: ABAddressBook? = ABAddressBookCreateWithOptions(nil, &error)?.takeRetainedValue();
            if (adbk == nil) {
                print(error);
                let alert = UIAlertController(title: "Contacts Error", message: "Contacts do not exist.", preferredStyle: .alert);
                let okAction = UIAlertAction(title: "OK", style: .default) { (action) in
                    self.backAction();
                }
                alert.addAction(okAction);
                
                if #available(iOS 9.0, *) {
                    alert.preferredAction = okAction;
                } else {
                    // Don't bold the OK button
                };
                
                self.present(alert, animated: true, completion: nil);
                
                alert.view.tintColor = Global.inst.sacBrown;
            } else {
                ABAddressBookRequestAccessWithCompletion(adbk) { (granted: Bool, error: CFError?) in
                    if (granted) {
                        self.book = adbk;
                        self.addContact(index: index);
                    } else {
                        self.notifyUserToEnableAddressBook();
                    }
                };
            }
        }
    }
    
    func addContact(index: Int) {
        let contact = createContact(contact: Global.inst.nearbyContacts[index]);
        if (!hasDuplicateContact(contact: contact, networkContact: Global.inst.nearbyContacts[index])) {
            let alert = UIAlertController(title: "Add Contact", message: "Are you sure you would like to add this person to your contacts?", preferredStyle: .alert);
            let yesAction = UIAlertAction(title: "Yes", style: .default) { (action) in
                self.forceAddContact(contact: contact);
            }
            let noAction = UIAlertAction(title: "No", style: .default) { (action) in
                // Do nothing
            }
            alert.addAction(noAction);
            alert.addAction(yesAction);
            
            if #available(iOS 9.0, *) {
                alert.preferredAction = yesAction;
            } else {
                // Don't bold the OK button
            };
            
            self.present(alert, animated: true, completion: nil);
            
            alert.view.tintColor = Global.inst.sacBrown;
        } else {
            print("Found duplicate contact.");
            let alert = UIAlertController(title: "Duplicate Contact", message: "This person already exists in your contacts. Would you like to create a new contact, overwrite the existing contact, or cancel?", preferredStyle: .alert);
            
            let newAction = UIAlertAction(title: "New", style: .default) { (action) in
                self.forceAddContact(contact: contact);
            }
            let overwriteAction = UIAlertAction(title: "Overwrite", style: .default) { (action) in
                ABAddressBookRemoveRecord(self.book, self.duplicate, nil);
                self.forceAddContact(contact: contact);
            }
            let cancelAction = UIAlertAction(title: "Cancel", style: .default) { (action) in
                // Do nothing
            }
            
            alert.addAction(cancelAction);
            alert.addAction(newAction);
            alert.addAction(overwriteAction);
            
            if #available(iOS 9.0, *) {
                alert.preferredAction = cancelAction;
            } else {
                // Don't bold the OK button
            };
            
            self.present(alert, animated: true, completion: nil);
            
            alert.view.tintColor = Global.inst.sacBrown;
        }
    }
    
    func forceAddContact(contact: ABRecord) {
        ABAddressBookAddRecord(book, contact, nil);
        ABAddressBookSave(book, nil);
        let alert = UIAlertController(title: "Contact Added", message: "This person was added successfully.", preferredStyle: .alert);
        let okAction = UIAlertAction(title: "OK", style: .default) { (action) in
            // Do nothing
        }
        alert.addAction(okAction);
        
        if #available(iOS 9.0, *) {
            alert.preferredAction = okAction;
        } else {
            // Don't bold the OK button
        };
        
        self.present(alert, animated: true, completion: nil);
        
        alert.view.tintColor = Global.inst.sacBrown;
    }
    
    func notifyUserToEnableAddressBook() {
        let alert = UIAlertController(title: "Permission Denied", message: "To add to your contacts, permission must be given. Go to Settings -> Privacy -> Contacts and enable this app.", preferredStyle: .alert);
        let okAction = UIAlertAction(title: "OK", style: .default) { (action) in
            self.backAction();
        }
        alert.addAction(okAction);
        
        if #available(iOS 9.0, *) {
            alert.preferredAction = okAction;
        } else {
            // Don't bold the OK button
        };
        
        self.present(alert, animated: true, completion: nil);
        
        alert.view.tintColor = Global.inst.sacBrown;
    }
    
    func createContact(contact: NetworkContact) -> ABRecord {
        let newContact = ABPersonCreate().takeRetainedValue();
        
        var error: Unmanaged<CFError>? = nil;
        
        let names = contact.name.components(separatedBy: " ");
        ABRecordSetValue(newContact, kABPersonFirstNameProperty, names.first as CFTypeRef, &error);
        ABRecordSetValue(newContact, kABPersonLastNameProperty, names.last as CFTypeRef, &error);
        if (names.count >= 3) {
            ABRecordSetValue(newContact, kABPersonMiddleNameProperty, names[1] as CFTypeRef, &error);
        }
        
        let multiEmail = ABMultiValueCreateMutable(ABPropertyType(kABMultiStringPropertyType)).takeRetainedValue();
        ABMultiValueAddValueAndLabel(multiEmail, contact.email as CFString, kABWorkLabel, nil);
        ABRecordSetValue(newContact, kABPersonEmailProperty, multiEmail, &error);
        
        if (!contact.phone.isEmpty) {
            let multiPhone = ABMultiValueCreateMutable(ABPropertyType(kABMultiStringPropertyType)).takeRetainedValue();
            ABMultiValueAddValueAndLabel(multiPhone, contact.phone as CFString, kABPersonPhoneMainLabel, nil);
            ABRecordSetValue(newContact, kABPersonPhoneProperty, multiPhone, &error);
        }
        
        let multiSocial = ABMultiValueCreateMutable(ABPropertyType(kABMultiDictionaryPropertyType)).takeRetainedValue();
        if (!contact.twitter.isEmpty) {
            ABMultiValueAddValueAndLabel(multiSocial, [kABPersonSocialProfileServiceKey: kABPersonSocialProfileServiceTwitter, kABPersonSocialProfileUsernameKey: contact.twitter as CFString] as NSDictionary, kABPersonSocialProfileServiceTwitter, nil);
        }
        if (!contact.instagram.isEmpty) {
            ABMultiValueAddValueAndLabel(multiSocial, [kABPersonSocialProfileServiceKey: "Instagram" as CFString, kABPersonSocialProfileUsernameKey: contact.instagram as CFString] as NSDictionary, "Instagram" as CFString, nil);
        }
        if (!contact.snapchat.isEmpty) {
            ABMultiValueAddValueAndLabel(multiSocial, [kABPersonSocialProfileServiceKey: "Snapchat" as CFString, kABPersonSocialProfileUsernameKey: contact.snapchat as CFString] as NSDictionary, "Snapchat" as CFString, nil);
        }
        if (!contact.collabratec.isEmpty) {
            ABMultiValueAddValueAndLabel(multiSocial, [kABPersonSocialProfileServiceKey: "Collabratec" as CFString, kABPersonSocialProfileUsernameKey: contact.collabratec as CFString] as NSDictionary, "Collabratec" as CFString, nil);
        }
        
        ABRecordSetValue(newContact, kABPersonSocialProfileProperty, multiSocial, &error);
        
        return newContact;
    }
    
    func hasDuplicateContact(contact: ABRecord, networkContact: NetworkContact) -> Bool {
        let allContacts = ABAddressBookCopyArrayOfAllPeople(book).takeRetainedValue() as Array;
        for record in allContacts {
            let currentContact: ABRecord = record;
            if (ABRecordCopyCompositeName(currentContact) == nil) {
                print("INVALID RECORD");
                continue;
            }
            let currentContactName = ABRecordCopyCompositeName(currentContact).takeRetainedValue() as String;
            print(currentContactName);
            if (networkContact.name == currentContactName) {
                print("name same");
                let currentContactEmails = ABRecordCopyValue(currentContact, kABPersonEmailProperty).takeRetainedValue() as ABMultiValue;
                if (ABMultiValueGetCount(currentContactEmails) > 0) {
                    
                    for i in 0 ..< ABMultiValueGetCount(currentContactEmails) {
                        let currentContactEmail = ABMultiValueCopyValueAtIndex(currentContactEmails, i).takeRetainedValue() as! CFString;
                        print(currentContactEmail as String);
                        if (networkContact.email == currentContactEmail as String) {
                            duplicate = record;
                            print("email same");
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }
    
    func backAction() {
        let viewControllers = self.navigationController!.viewControllers;
        for aViewController in viewControllers {
            if (aViewController is MenuViewController) {
                self.navigationController!.popToViewController(aViewController, animated: true);
            }
        }
    }
    
}
