//
//  VoteViewController.swift
//  SAC 17
//
//  Created on 4/1/17.
//  Copyright © 2017 Rowan IEEE. All rights reserved.
//

import UIKit
import Firebase

class VoteViewController: BaseViewController {
    
    var backButton: UIBarButtonItem!;
    var voteButton: UIBarButtonItem!;
    
    var shirt: TShirt!;
    var project: Project!;
    
    override func viewDidLoad() {
        super.viewDidLoad();
        
        voteButton = UIBarButtonItem(title: "Vote", style: UIBarButtonItemStyle.plain, target: self, action: #selector(VoteViewController.voteAction));
        navigationItem.rightBarButtonItem = voteButton;
        
        if (shirt != nil) {
            displayShirt();
        } else if (project != nil) {
            displayProject();
        } else {
            print("Invalid project or shirt.");
            backAction();
        }
        
        // Back button to go back to the votingViewController
        backButton = UIBarButtonItem(image: UIImage(named: "ChevronLeft"), style: UIBarButtonItemStyle.plain, target: self, action: #selector(VoteViewController.backAction));
        navigationItem.leftBarButtonItem = backButton;
    }
    
    func displayShirt() {
        navigationItem.title = "Shirt";
        let imageView = UIImageView(image: shirt.image);
        view.addSubview(imageView);
        
        imageView.clipsToBounds = true;
        imageView.contentMode = UIViewContentMode.scaleAspectFit;
        
        imageView.translatesAutoresizingMaskIntoConstraints = false;
        
        view.addConstraint(NSLayoutConstraint(item: imageView, attribute: .top, relatedBy: .equal, toItem: self.topLayoutGuide, attribute: .bottom, multiplier: 1, constant: 0));
        view.addConstraint(NSLayoutConstraint(item: imageView, attribute: .bottom, relatedBy: .equal, toItem: view, attribute: .bottom, multiplier: 1, constant: 0));
        
        view.addConstraints(NSLayoutConstraint.constraints(withVisualFormat: "H:|-0-[subview]-0-|", options: .directionLeadingToTrailing, metrics: nil, views: ["subview": imageView]));
    }
    
    func displayProject() {
        let imageHeight = CGFloat(128.0);
        navigationItem.title = project.title;
        
        let textView = UITextView();
        textView.text = project.abstract;
        
        textView.font = textView.font?.withSize(18);
        
        textView.backgroundColor = Global.inst.sacBrown;
        textView.textColor = Global.inst.sacYellow;
        textView.tintColor = Global.inst.sacYellow;
        
        textView.isEditable = false;
        
        textView.textContainerInset = UIEdgeInsetsMake(textView.textContainerInset.top, 8, textView.textContainerInset.bottom, 8);
        
        view.addSubview(textView);
        
        if (project.imageLink == "") {
            // TextView
            textView.translatesAutoresizingMaskIntoConstraints = false;
            
            view.addConstraint(NSLayoutConstraint(item: textView, attribute: .top, relatedBy: .equal, toItem: view, attribute: .top, multiplier: 1, constant: 0));
            view.addConstraint(NSLayoutConstraint(item: textView, attribute: .bottom, relatedBy: .equal, toItem: view, attribute: .bottom, multiplier: 1, constant: 0));
            
            view.addConstraints(NSLayoutConstraint.constraints(withVisualFormat: "H:|-0-[subview]-0-|", options: .directionLeadingToTrailing, metrics: nil, views: ["subview": textView]));
        } else {
            let imageView = UIImageView(image: project.image);
            view.addSubview(imageView);
            
            imageView.clipsToBounds = true;
            imageView.contentMode = UIViewContentMode.scaleAspectFit;
            imageView.backgroundColor = Global.inst.sacBrown;
            
            // ImageView
            imageView.translatesAutoresizingMaskIntoConstraints = false;
            
            view.addConstraint(NSLayoutConstraint(item: imageView, attribute: .top, relatedBy: .equal, toItem: topLayoutGuide, attribute: .bottom, multiplier: 1, constant: 0));
            view.addConstraint(NSLayoutConstraint(item: imageView, attribute: .bottom, relatedBy: .equal, toItem: topLayoutGuide, attribute: .bottom, multiplier: 1, constant: imageHeight));
            
            view.addConstraints(NSLayoutConstraint.constraints(withVisualFormat: "H:|-0-[subview]-0-|", options: .directionLeadingToTrailing, metrics: nil, views: ["subview": imageView]));
            
            // TextView
            textView.translatesAutoresizingMaskIntoConstraints = false;
            
            view.addConstraint(NSLayoutConstraint(item: textView, attribute: .top, relatedBy: .equal, toItem: view, attribute: .top, multiplier: 1, constant: imageHeight));
            view.addConstraint(NSLayoutConstraint(item: textView, attribute: .bottom, relatedBy: .equal, toItem: view, attribute: .bottom, multiplier: 1, constant: 0));
            
            view.addConstraints(NSLayoutConstraint.constraints(withVisualFormat: "H:|-0-[subview]-0-|", options: .directionLeadingToTrailing, metrics: nil, views: ["subview": textView]));
        }
    }
    
    func voteAction() {
        voteButton.isEnabled = false;
        if (Global.inst.canVote) {
            if (shirt != nil) {
                let uid = FIRAuth.auth()!.currentUser!.uid;
                Global.inst.ref.child(Global.inst.baseVoting).child(Global.inst.attendeesLocation).child(uid).child(Global.inst.tshirtLocation).observeSingleEvent(of: .value, with: { (snapshot) in
                    if let value = snapshot.value as? String {
                        if (value != "") {
                            // Ask the user if they are sure
                            let alert = UIAlertController(title: "Already Voted", message: "You have already voted for a T-Shirt, are you sure you want to change your vote?", preferredStyle: .alert);
                            let yesAction = UIAlertAction(title: "Yes", style: .default) { (action) in
                                let updates = ["\(Global.inst.baseVoting)\(Global.inst.attendeesLocation)\(uid)/\(Global.inst.tshirtLocation)": self.shirt.user];
                                Global.inst.ref.updateChildValues(updates);
                            }
                            let noAction = UIAlertAction(title: "No", style: .default) { (action) in
                                self.voteButton.isEnabled = true;
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
                            
                            return;
                        }
                        print(value);
                    } else {
                        print("value doesn't exist");
                    }
                    
                    // Ask the user if they are sure
                    let alert = UIAlertController(title: "Confirmation", message: "Are you sure you would like to vote for this T-Shirt? You can change your vote later.", preferredStyle: .alert);
                    let yesAction = UIAlertAction(title: "Yes", style: .default) { (action) in
                        let updates = ["\(Global.inst.baseVoting)\(Global.inst.attendeesLocation)\(uid)/\(Global.inst.tshirtLocation)": self.shirt.user];
                        Global.inst.ref.updateChildValues(updates);
                    }
                    let noAction = UIAlertAction(title: "No", style: .default) { (action) in
                        self.voteButton.isEnabled = true;
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
                }) { (error) in
                    print(error.localizedDescription);
                }
            } else if (project != nil) {
                
                let uid = FIRAuth.auth()!.currentUser!.uid;
                Global.inst.ref.child(Global.inst.baseVoting).child(Global.inst.attendeesLocation).child(uid).child(Global.inst.projectLocation).observeSingleEvent(of: .value, with: { (snapshot) in
                    if let value = snapshot.value as? String {
                        if (value != "") {
                            // Ask the user if they are sure
                            let alert = UIAlertController(title: "Already Voted", message: "You have already voted for a project, are you sure you want to change your vote?", preferredStyle: .alert);
                            let yesAction = UIAlertAction(title: "Yes", style: .default) { (action) in
                                let updates = ["\(Global.inst.baseVoting)\(Global.inst.attendeesLocation)\(uid)/\(Global.inst.projectLocation)": self.project.user];
                                Global.inst.ref.updateChildValues(updates);
                            }
                            let noAction = UIAlertAction(title: "No", style: .default) { (action) in
                                self.voteButton.isEnabled = true;
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
                            
                            return;
                        }
                        print(value);
                    } else {
                        print("value doesn't exist");
                    }
                    
                    // Ask the user if they are sure
                    let alert = UIAlertController(title: "Confirmation", message: "Are you sure you would like to vote for this project? You can change your vote later.", preferredStyle: .alert);
                    let yesAction = UIAlertAction(title: "Yes", style: .default) { (action) in
                        let updates = ["\(Global.inst.baseVoting)\(Global.inst.attendeesLocation)\(uid)/\(Global.inst.projectLocation)": self.project.user];
                        Global.inst.ref.updateChildValues(updates);
                    }
                    let noAction = UIAlertAction(title: "No", style: .default) { (action) in
                        self.voteButton.isEnabled = true;
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
                }) { (error) in
                    print(error.localizedDescription);
                }
            } else {
                // Do nothing
            }
        } else {
            print("Voting not allowed.");
            let alert = UIAlertController(title: "Voting Closed", message: "Voting is currently closed.", preferredStyle: .alert);
            let okAction = UIAlertAction(title: "OK", style: .default) { (action) in
                self.voteButton.isEnabled = true;
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
    }
    
    func backAction() {
        navigationController!.popViewController(animated: true);
    }
    
}
