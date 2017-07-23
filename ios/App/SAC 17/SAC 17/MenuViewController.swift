//
//  MenuViewController.swift
//  SAC 17
//
//  Created on 1/12/17.
//  Copyright © 2017 Rowan IEEE. All rights reserved.
//

import UIKit
import Firebase
import WebKit

protocol WebViewControllerDelegate: class {
    func loadNewWebPage(url: String, pageTitle: String);
}

class MenuViewController: BaseViewController, UITableViewDelegate, UITableViewDataSource {
    
    weak var webViewControllerDelegate: WebViewControllerDelegate?;
    
    @IBOutlet weak var tableView: UITableView!;
    @IBOutlet weak var emailButton: UIButton!;
    @IBOutlet weak var campusImage: UIImageView!;
    
    var backButton: UIBarButtonItem!;
    
    override func viewDidLoad() {
        super.viewDidLoad();
        navigationItem.title = "Menu";
        // Back button to go back to the webViewController
        backButton = UIBarButtonItem(image: UIImage(named: "ChevronLeft"), style: UIBarButtonItemStyle.plain, target: self, action: #selector(MenuViewController.backAction));
        navigationItem.leftBarButtonItem = backButton;
    }
    
    override func viewWillAppear(_ animated: Bool) {
        tableView.reloadData();
        //tableView.setContentOffset(CGPoint.zero, animated: false);
        
        if (!Global.inst.signedIntoFirebase) {
            emailButton.isHidden = true;
        } else {
            if (FIRAuth.auth()?.currentUser?.displayName == nil) {
                emailButton.setTitle(FIRAuth.auth()?.currentUser?.email, for: UIControlState.normal);
                emailButton.setTitle(FIRAuth.auth()?.currentUser?.email, for: UIControlState.highlighted);
            } else {
                emailButton.setTitle(FIRAuth.auth()?.currentUser?.displayName, for: UIControlState.normal);
                emailButton.setTitle(FIRAuth.auth()?.currentUser?.displayName, for: UIControlState.highlighted);
            }
            emailButton.isHidden = false;
        }
    }
    
    func numberOfSections(in tableView: UITableView) -> Int {
        return 1;
    }
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return Global.inst.getTitlesOfMenuItems().count;
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "LabelCell", for: indexPath) as! MenuTableViewCell;
        let currentCellTitle = Global.inst.getTitlesOfMenuItems()[indexPath.row];
        cell.textLabel?.text = currentCellTitle;
        
        let origImage = UIImage(named: currentCellTitle);
        cell.imageView?.image = UIImage(cgImage: origImage!.cgImage!, scale: origImage!.size.width/15.0, orientation: origImage!.imageOrientation).withRenderingMode(.alwaysTemplate);
        return cell;
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        let selectionKey = Global.inst.getOrderedMenuKeys()[indexPath.row];
        
        if (Global.inst.signedIntoFirebase) {
            if (Global.inst.sacLinksFirebase[selectionKey]!.contains("func")) {
                if (Global.inst.sacLinksFirebase[selectionKey] == "funcsignout") {
                    do {
                        try FIRAuth.auth()?.signOut();
                    } catch let signOutError as NSError {
                        print("Error signing out: \(signOutError)");
                    }
                    
                    Global.inst.firebaseJustSignedOut();
                    let loginEmailViewController = storyboard?.instantiateViewController(withIdentifier: "loginEmailViewController") as! LoginEmailViewController;
                    loginEmailViewController.nextViewController = nil;
                    navigationController?.pushViewController(loginEmailViewController, animated: true);
                } else if (Global.inst.sacLinksFirebase[selectionKey] == "funcabout") {
                    displayAbout();
                } else if (Global.inst.sacLinksFirebase[selectionKey] == "funcuploadphotos") {
                    let uploadPhotoViewController = storyboard?.instantiateViewController(withIdentifier: "uploadPhotoViewController") as! UploadPhotoViewController;
                    navigationController?.pushViewController(uploadPhotoViewController, animated: true);
                } else if (Global.inst.sacLinksFirebase[selectionKey] == "funcnetwork") {
                    let networkViewController = storyboard?.instantiateViewController(withIdentifier: "networkViewController") as! NetworkViewController;
                    navigationController?.pushViewController(networkViewController, animated: true);
                } else if (Global.inst.sacLinksFirebase[selectionKey] == "funcvoting") {
                    let votingViewController = storyboard?.instantiateViewController(withIdentifier: "votingViewController") as! VotingViewController;
                    navigationController?.pushViewController(votingViewController, animated: true);
                }
            } else {
                let link = Global.inst.sacLinksFirebase[selectionKey];
                webViewControllerDelegate?.loadNewWebPage(url: link!, pageTitle: Global.inst.getTitlesOfMenuItems()[indexPath.row]);
                _ = navigationController?.popViewController(animated: true);
            }
        } else {
            if (Global.inst.sacLinksNoFirebase[selectionKey]!.contains("func")) {
                if (Global.inst.sacLinksNoFirebase[selectionKey] == "funcsignin") {
                    let loginEmailViewController = storyboard?.instantiateViewController(withIdentifier: "loginEmailViewController") as! LoginEmailViewController;
                    loginEmailViewController.nextViewController = nil;
                    navigationController?.pushViewController(loginEmailViewController, animated: true);
                } else if (Global.inst.sacLinksNoFirebase[selectionKey] == "funcabout") {
                    displayAbout();
                } else if (Global.inst.sacLinksNoFirebase[selectionKey] == "funcsigninuploadphotos") {
                    let loginEmailViewController = storyboard?.instantiateViewController(withIdentifier: "loginEmailViewController") as! LoginEmailViewController;
                    loginEmailViewController.nextViewController = storyboard?.instantiateViewController(withIdentifier: "uploadPhotoViewController") as! UploadPhotoViewController;
                    navigationController?.pushViewController(loginEmailViewController, animated: true);
                } else if (Global.inst.sacLinksNoFirebase[selectionKey] == "funcsigninnetwork") {
                    let loginEmailViewController = storyboard?.instantiateViewController(withIdentifier: "loginEmailViewController") as! LoginEmailViewController;
                    loginEmailViewController.nextViewController = storyboard?.instantiateViewController(withIdentifier: "networkViewController") as! NetworkViewController;
                    navigationController?.pushViewController(loginEmailViewController, animated: true);
                } else if (Global.inst.sacLinksNoFirebase[selectionKey] == "funcsigninvoting") {
                    let loginEmailViewController = storyboard?.instantiateViewController(withIdentifier: "loginEmailViewController") as! LoginEmailViewController;
                    loginEmailViewController.nextViewController = storyboard?.instantiateViewController(withIdentifier: "votingViewController") as! VotingViewController;
                    navigationController?.pushViewController(loginEmailViewController, animated: true);
                }
            } else {
                let link = Global.inst.sacLinksNoFirebase[selectionKey];
                webViewControllerDelegate?.loadNewWebPage(url: link!, pageTitle: Global.inst.getTitlesOfMenuItems()[indexPath.row]);
                _ = navigationController?.popViewController(animated: true);
            }
        }
    }
    
    func backAction() {
        _ = navigationController?.popViewController(animated: true);
    }
    
    func displayAbout() {
        let aboutViewController = storyboard?.instantiateViewController(withIdentifier: "aboutViewController") as! AboutViewController;
        navigationController?.pushViewController(aboutViewController, animated: true);
    }
    
    @IBAction func emailButtonAction(_ sender: Any) {
        // Display the user profile
        let profileViewController = storyboard?.instantiateViewController(withIdentifier: "profileViewController") as! ProfileViewController;
        navigationController?.pushViewController(profileViewController, animated: true);
    }
    
}
