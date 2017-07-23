//
//  MenuTableViewCell.swift
//  SAC 17
//
//  Created on 1/13/17.
//  Copyright © 2017 Rowan IEEE. All rights reserved.
//

import UIKit

class MenuTableViewCell: UITableViewCell {
    
    override func awakeFromNib() {
        super.awakeFromNib()
    }
    
    override func touchesBegan(_ touches: Set<UITouch>, with event: UIEvent?) {
        super.touchesBegan(touches, with: event);
        changeStyle(selected: true);
    }
    
    override func touchesEnded(_ touches: Set<UITouch>, with event: UIEvent?) {
        super.touchesEnded(touches, with: event);
        changeStyle(selected: false);
    }
    
    override func touchesCancelled(_ touches: Set<UITouch>, with event: UIEvent?) {
        super.touchesCancelled(touches, with: event);
        changeStyle(selected: false);
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)
        changeStyle(selected: selected);
    }
    
    func changeStyle(selected: Bool) {
        separatorInset.left = 0;
        
        if (selected) {
            textLabel?.textColor = Global.inst.sacYellow;
            backgroundColor = Global.inst.sacBrown;
            imageView?.tintColor = Global.inst.sacYellow;
        } else {
            textLabel?.textColor = Global.inst.sacBrown;
            backgroundColor = UIColor.white;
            imageView?.tintColor = Global.inst.sacYellow;
        }
    }

}
