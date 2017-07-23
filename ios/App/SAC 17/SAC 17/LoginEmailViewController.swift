//
//  LoginEmailViewController.swift
//  SAC 17
//
//  Created on 1/14/17.
//  Copyright © 2017 Rowan IEEE. All rights reserved.
//

import UIKit
import Firebase

class LoginEmailViewController: BaseViewController, UITextFieldDelegate {
    
    @IBOutlet weak var emailField: UITextField!;
    @IBOutlet weak var passwordField: UITextField!;
    @IBOutlet weak var scrollView: UIScrollView!;
    
    var backButton: UIBarButtonItem!;
    
    var nextViewController: BaseViewController!;
    
    override func viewDidLoad() {
        super.viewDidLoad();
        
        // Back button to go back to the menuViewController
        backButton = UIBarButtonItem(image: UIImage(named: "ChevronLeft"), style: UIBarButtonItemStyle.plain, target: self, action: #selector(LoginEmailViewController.backAction));
        navigationItem.leftBarButtonItem = backButton;
        registerForKeyboardNotifications();
    }
    
    override func viewDidAppear(_ animated: Bool) {
        super.viewDidAppear(animated);
        
        emailField.becomeFirstResponder();
    }
    
    override func viewWillDisappear(_ animated: Bool) {
        super.viewWillDisappear(animated);
        unregisterForKeyboardNotifications();
    }
    
    func registerForKeyboardNotifications() {
        NotificationCenter.default.addObserver(self, selector: #selector(LoginEmailViewController.keyboardDidShow(notification:)), name: NSNotification.Name.UIKeyboardDidShow, object: nil);
        NotificationCenter.default.addObserver(self, selector: #selector(LoginEmailViewController.keyboardWillHide(notification:)), name: NSNotification.Name.UIKeyboardWillHide, object: nil);
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
    
    func login() {
        guard let email = emailField.text, let password = passwordField.text else { return; }
        FIRAuth.auth()?.signIn(withEmail: email, password: password) { (user, error) in
            if let error = error {
                print(error.localizedDescription);
                
                let errorPrompt = UIAlertController(title: "Could Not Sign In", message: error.localizedDescription, preferredStyle: .alert);
                let okAction = UIAlertAction(title: "OK", style: .default) { (action) in
                    // Do nothing, just close the alert
                }
                
                errorPrompt.addAction(okAction);
                
                if #available(iOS 9.0, *) {
                    errorPrompt.preferredAction = okAction;
                } else {
                    // Don't bold the OK button
                };
                
                self.present(errorPrompt, animated: true, completion: nil);
                
                errorPrompt.view.tintColor = Global.inst.sacBrown;
                return;
            }
            
            Global.inst.firebaseJustSignedIn();
            
            if (self.nextViewController == nil) {
                _ = self.navigationController?.popViewController(animated: true);
            } else {
                self.navigationController?.pushViewController(self.nextViewController, animated: true);
            }
        }
    }
    
    func backAction() {
        _ = navigationController?.popViewController(animated: true);
    }
    
    func textFieldShouldReturn(_ textField: UITextField) -> Bool {
        if (textField == emailField) {
            passwordField.becomeFirstResponder();
        } else {
            login();
        }
        return true;
    }
    
    @IBAction func loginAction(_ sender: Any) {
        login();
    }
    
    @IBAction func forgotPasswordAction(_ sender: Any) {
        let prompt = UIAlertController(title: "Reset Password", message: "Email:", preferredStyle: .alert);
        let cancelAction = UIAlertAction(title: "Cancel", style: .default) { (action) in
            // Do nothing, just close the alert
        }
        let okAction = UIAlertAction(title: "Send", style: .default) { (action) in
            let userInput = prompt.textFields![0].text;
            
            if (userInput!.isEmpty) {
                return;
            }
            
            FIRAuth.auth()?.sendPasswordReset(withEmail: userInput!) { (error) in
                if let error = error {
                    print(error.localizedDescription);
                    
                    let errorPrompt = UIAlertController(title: "Could Not Reset Password", message: error.localizedDescription, preferredStyle: .alert);
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
                    let errorPrompt = UIAlertController(title: "Sent!", message: "Check your email for a password reset link.", preferredStyle: .alert);
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
        
        prompt.addTextField(configurationHandler: nil);
        
        prompt.textFields![0].textColor = Global.inst.sacBrown;
        prompt.textFields![0].tintColor = Global.inst.sacYellow;
        
        prompt.textFields![0].text = emailField.text;
        prompt.addAction(cancelAction);
        prompt.addAction(okAction);
        
        if #available(iOS 9.0, *) {
            prompt.preferredAction = okAction
        } else {
            // Don't bold the Send button
        };
        
        present(prompt, animated: true, completion: nil);
        
        prompt.view.tintColor = Global.inst.sacBrown;
    }
}
