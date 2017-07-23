//
//  TShirt.swift
//  SAC 17
//
//  Created on 3/31/17.
//  Copyright © 2017 Rowan IEEE. All rights reserved.
//

import UIKit

class TShirt {
    
    let imageLink: String;
    let status: String;
    let user: String;
    
    var image: UIImage;
    
    init(imageLink1: String, status1: String, user1: String) {
        imageLink = imageLink1;
        status = status1;
        user = user1;
        image = UIImage();
    }
    
}
