//
//  Project.swift
//  SAC 17
//
//  Created on 3/31/17.
//  Copyright © 2017 Rowan IEEE. All rights reserved.
//

import UIKit

class Project {
    
    let title: String;
    let status: String;
    let abstract: String;
    let user: String;
    let imageLink: String;
    
    var image: UIImage;
    
    init(title1: String, status1: String, abstract1: String, user1: String, imageLink1: String) {
        title = title1;
        status = status1;
        abstract = abstract1;
        user = user1;
        imageLink = imageLink1;
        image = UIImage();
    }
    
}
