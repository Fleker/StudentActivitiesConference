//
//  VotingViewController.swift
//  SAC 17
//
//  Created on 3/29/17.
//  Copyright © 2017 Rowan IEEE. All rights reserved.
//

import UIKit
import Firebase

class VotingViewController: BaseViewController, UITableViewDelegate, UITableViewDataSource {
    
    var backButton: UIBarButtonItem!;
    
    let tshirtSection = 0;
    let projectSection = 1;

    @IBOutlet weak var tableView: UITableView!
    
    override func viewDidLoad() {
        super.viewDidLoad();
        
        Global.inst.tshirts = [TShirt]();
        Global.inst.projects = [Project]();
        
        setupDatabase();
        loadVotingData();
        
        // Back button to go back to the menuViewController
        backButton = UIBarButtonItem(image: UIImage(named: "ChevronLeft"), style: UIBarButtonItemStyle.plain, target: self, action: #selector(VotingViewController.backAction));
        navigationItem.leftBarButtonItem = backButton;
    }
    
    func setupDatabase() {
        Global.inst.ref = FIRDatabase.database().reference();
        
        Global.inst.ref.child(Global.inst.baseVoting).child(Global.inst.allowVotingFlagLocation).observe(FIRDataEventType.value, with: { (snapshot) in
            Global.inst.canVote = snapshot.value as! Bool;
        });
    }
    
    func loadVotingData() {
        let request = URLRequest(url: URL(string: Global.inst.votingLink)!);
        NSURLConnection.sendAsynchronousRequest(request, queue: OperationQueue.main, completionHandler: { (response, data, error) in
            do {
                let json = try JSONSerialization.jsonObject(with: data!, options: []) as! [String: Any];
                
                let data = json["data"] as! [String: Any];
                let tshirtData = data["tshirt"] as! [Any];
                let projectData = data["project"] as! [Any];
                
                self.parseTShirts(tshirtData: tshirtData);
                self.parseProjects(projectData: projectData);
            } catch {
                print("Error downloading voting JSON.");
            }
        });
    }
    
    func downloadImageForTShirt(shirt: TShirt) {
        if (shirt.imageLink != "") {
            let request = URLRequest(url: URL(string: shirt.imageLink)!);
            NSURLConnection.sendAsynchronousRequest(request, queue: OperationQueue.main, completionHandler: { (response, data, error) in
                shirt.image = UIImage(data: data!)!;
                Global.inst.tshirts.append(shirt);
                self.tableView.reloadData();
            });
        } else {
            Global.inst.tshirts.append(shirt);
            self.tableView.reloadData();
        }
    }
    
    func downloadImageForProject(project: Project) {
        if (project.imageLink != "") {
            let request = URLRequest(url: URL(string: project.imageLink)!);
            NSURLConnection.sendAsynchronousRequest(request, queue: OperationQueue.main, completionHandler: { (response, data, error) in
                project.image = UIImage(data: data!)!;
                Global.inst.projects.append(project);
                self.tableView.reloadData();
            });
        } else {
            Global.inst.projects.append(project);
            self.tableView.reloadData();
        }
    }
    
    func parseTShirts(tshirtData: [Any]) {
        Global.inst.tshirts = [TShirt]();
        for rawTShirt in tshirtData {
            let shirt = rawTShirt as! [String: Any];
            var imageLink = "";
            var status = "";
            var user = "";
            
            if let newImageLink = shirt["downloadUrl"] as? String {
                imageLink = newImageLink;
            }
            
            if let newUser = shirt["user"] as? String {
                user = newUser;
            }
            
            if let newStatus = shirt["status"] as? String {
                status = newStatus;
            }
            
            let theShirt = TShirt(imageLink1: imageLink, status1: status, user1: user);
            downloadImageForTShirt(shirt: theShirt);
        }
    }
    
    func parseProjects(projectData: [Any]) {
        Global.inst.projects = [Project]();
        for rawProject in projectData {
            let project = rawProject as! [String: Any];
            var imageLink = "";
            var status = "";
            var user = "";
            var title = "";
            var abstract = "";
            
            if let newImageLink = project["downloadUrl"] as? String {
                imageLink = newImageLink;
            }
            
            if let newUser = project["user"] as? String {
                user = newUser;
            }
            
            if let newStatus = project["status"] as? String {
                status = newStatus;
            }
            
            if let newTitle = project["title"] as? String {
                title = newTitle;
            }
            
            if let newAbstract = project["abstract"] as? String {
                abstract = newAbstract;
            }
            
            let theProject = Project(title1: title, status1: status, abstract1: abstract, user1: user, imageLink1: imageLink);
            downloadImageForProject(project: theProject);
        }
    }
    
    func backAction() {
        let viewControllers = self.navigationController!.viewControllers;
        for aViewController in viewControllers {
            if (aViewController is MenuViewController) {
                self.navigationController!.popToViewController(aViewController, animated: true);
            }
        }
    }
    
    func numberOfSections(in tableView: UITableView) -> Int {
        return 2;
    }
    
    func tableView(_ tableView: UITableView, titleForHeaderInSection section: Int) -> String? {
        if (section == tshirtSection) {
            return "T-Shirt Voting";
        } else if (section == projectSection) {
            return "Project Voting";
        } else {
            return "";
        }
    }
    
    func tableView(_ tableView: UITableView, willDisplayHeaderView view: UIView, forSection section: Int) {
        let header = view as! UITableViewHeaderFooterView;
        header.textLabel?.textColor = Global.inst.sacBrown;
        header.tintColor = Global.inst.sacYellow;
    }
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        if (section == tshirtSection) {
            return Global.inst.tshirts.count;
        } else if (section == projectSection) {
            return Global.inst.projects.count;
        } else {
            return 0;
        }
    }
    
    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
        if (indexPath.section == tshirtSection) {
            return 88;
        } else {
            return 44;
        }
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "VotingCell", for: indexPath) as! MenuTableViewCell;
        
        if (indexPath.section == tshirtSection) {
            cell.textLabel?.text = "";
            cell.imageView?.image = Global.inst.tshirts[indexPath.row].image;
            cell.imageView?.center = cell.center;
        } else if (indexPath.section == projectSection) {
            cell.textLabel?.text = Global.inst.projects[indexPath.row].title;
            cell.imageView?.image = Global.inst.projects[indexPath.row].image;
        } else {
            cell.textLabel?.text = "";
        }
        
        return cell;
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        tableView.deselectRow(at: indexPath, animated: true);
        if (indexPath.section == tshirtSection) {
            let shirt = Global.inst.tshirts[indexPath.row];
            let voteViewController = storyboard?.instantiateViewController(withIdentifier: "voteViewController") as! VoteViewController;
            voteViewController.shirt = shirt;
            navigationController?.pushViewController(voteViewController, animated: true);
        } else if (indexPath.section == projectSection) {
            let project = Global.inst.projects[indexPath.row];
            let voteViewController = storyboard?.instantiateViewController(withIdentifier: "voteViewController") as! VoteViewController;
            voteViewController.project = project;
            navigationController?.pushViewController(voteViewController, animated: true);
        }
    }
    
}
