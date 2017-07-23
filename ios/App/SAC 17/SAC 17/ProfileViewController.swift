//
//  ProfileViewController.swift
//  SAC 17
//
//  Created on 1/16/17.
//  Copyright © 2017 Rowan IEEE. All rights reserved.
//

import UIKit
import Firebase

class ProfileViewController: BaseViewController, UITextFieldDelegate {
    
    @IBOutlet weak var image: UIImageView!
    @IBOutlet weak var name: UILabel!
    @IBOutlet weak var email: UILabel!
    var backButton: UIBarButtonItem!;
    
    @IBOutlet weak var phoneField: UITextField!
    @IBOutlet weak var twitterField: UITextField!
    @IBOutlet weak var instagramField: UITextField!
    @IBOutlet weak var snapchatField: UITextField!
    @IBOutlet weak var collabratecField: UITextField!
    
    @IBOutlet weak var scrollView: UIScrollView!
    override func viewDidLoad() {
        super.viewDidLoad();
        
        // Back button to go back to the menuViewController
        backButton = UIBarButtonItem(image: UIImage(named: "ChevronLeft"), style: UIBarButtonItemStyle.plain, target: self, action: #selector(ProfileViewController.backAction));
        navigationItem.leftBarButtonItem = backButton;
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated);
        name.text = FIRAuth.auth()?.currentUser?.displayName;
        email.text = FIRAuth.auth()?.currentUser?.email;
        
        phoneField.text = Global.inst.phone;
        twitterField.text = Global.inst.twitter;
        instagramField.text = Global.inst.instagram;
        snapchatField.text = Global.inst.snapchat;
        collabratecField.text = Global.inst.collabratec;
        
        registerForKeyboardNotifications();
    }
    
    override func viewWillDisappear(_ animated: Bool) {
        super.viewWillDisappear(animated);
        unregisterForKeyboardNotifications();
    }
    
    func registerForKeyboardNotifications() {
        NotificationCenter.default.addObserver(self, selector: #selector(ProfileViewController.keyboardDidShow(notification:)), name: NSNotification.Name.UIKeyboardDidShow, object: nil);
        NotificationCenter.default.addObserver(self, selector: #selector(ProfileViewController.keyboardWillHide(notification:)), name: NSNotification.Name.UIKeyboardWillHide, object: nil);
    }
    
    func unregisterForKeyboardNotifications() {
        NotificationCenter.default.removeObserver(self);
    }
    
    func keyboardDidShow(notification: NSNotification) {
        let userInfo = notification.userInfo! as NSDictionary;
        let keyboardInfo = userInfo[UIKeyboardFrameBeginUserInfoKey] as! NSValue;
        let keyboardSize = keyboardInfo.cgRectValue.size;
        let contentInsets = UIEdgeInsets(top: scrollView.contentInset.top, left: 0, bottom: keyboardSize.height, right: 0);
        scrollView.contentInset = contentInsets;
        scrollView.scrollIndicatorInsets = contentInsets;
    }
    
    func keyboardWillHide(notification: NSNotification) {
        let contentInsets = UIEdgeInsets(top: scrollView.contentInset.top, left: 0, bottom: 0, right: 0);
        scrollView.contentInset = contentInsets;
        scrollView.scrollIndicatorInsets = contentInsets;
    }
    
    func textFieldShouldReturn(_ textField: UITextField) -> Bool {
        view.endEditing(true);
        
        return true;
    }
    
    func backAction() {
        _ = navigationController?.popViewController(animated: true);
    }
    
    @IBAction func resetPasswordAction(_ sender: Any) {
        FIRAuth.auth()?.sendPasswordReset(withEmail: (FIRAuth.auth()?.currentUser?.email)!) { (error) in
            if let error = error {
                print(error.localizedDescription);
                
                let errorPrompt = UIAlertController(title: "Error", message: "Could not complete password reset. \(error.localizedDescription)", preferredStyle: .alert);
                let okAction = UIAlertAction(title: "OK", style: .default) { (action) in
                    // Do nothing, just close the alert
                }
                
                errorPrompt.addAction(okAction);
                
                if #available(iOS 9.0, *) {
                    errorPrompt.preferredAction = okAction
                } else {
                    // Don't bold the OK button
                };
                
                self.present(errorPrompt, animated: true, completion: nil);
                
                errorPrompt.view.tintColor = Global.inst.sacBrown;
                return;
            } else {
                let errorPrompt = UIAlertController(title: "Complete", message: "Check your email for a password reset link.", preferredStyle: .alert);
                let okAction = UIAlertAction(title: "OK", style: .default) { (action) in
                    // Do nothing, just close the alert
                }
                
                errorPrompt.addAction(okAction);
                
                if #available(iOS 9.0, *) {
                    errorPrompt.preferredAction = okAction
                } else {
                    // Don't bold the OK button
                };
                
                self.present(errorPrompt, animated: true, completion: nil);
                
                errorPrompt.view.tintColor = Global.inst.sacBrown;
            }
        }
    }
    
    @IBAction func editPhone(_ sender: Any) {
        Global.inst.phone = (sender as! UITextField).text!;
    }
    
    @IBAction func editTwitter(_ sender: Any) {
        Global.inst.twitter = (sender as! UITextField).text!;
    }
    
    @IBAction func editInstagram(_ sender: Any) {
        Global.inst.instagram = (sender as! UITextField).text!;
    }
    
    @IBAction func editSnapchat(_ sender: Any) {
        Global.inst.snapchat = (sender as! UITextField).text!;
    }
    
    @IBAction func editCollabratec(_ sender: Any) {
        Global.inst.collabratec = (sender as! UITextField).text!;
    }
    
}
