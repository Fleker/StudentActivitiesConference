//
//  AboutViewController.swift
//  SAC 17
//
//  Created on 1/16/17.
//  Copyright © 2017 Rowan IEEE. All rights reserved.
//

import UIKit

class AboutViewController: BaseViewController {
    
    var backButton: UIBarButtonItem!;
    
    override func viewDidLoad() {
        super.viewDidLoad();
        
        // Back button to go back to the menuViewController
        backButton = UIBarButtonItem(image: UIImage(named: "ChevronLeft"), style: UIBarButtonItemStyle.plain, target: self, action: #selector(AboutViewController.backAction));
        navigationItem.leftBarButtonItem = backButton;
    }
    
    func backAction() {
        _ = navigationController?.popViewController(animated: true);
    }
    
}
