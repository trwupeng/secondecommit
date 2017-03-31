//
//  PopupViewCell.swift
//  tiger
//
//  Created by wayne on 16/10/13.
//  Copyright © 2016年 kkd. All rights reserved.
//

import UIKit

let popupViewCellHeight:CGFloat = 30


class PopupViewCell: UITableViewCell {
    
    var nameLabel:UILabel!
    
    
    override init(style: UITableViewCellStyle, reuseIdentifier: String?) {
        super.init(style: style, reuseIdentifier: reuseIdentifier)
        creatUI()
    }
    
    required init?(coder aDecoder: NSCoder) {
        fatalError("init(coder:) has not been implemented")
    }
    
    private func creatUI() {
        nameLabel = UILabel (frame: CGRect (x: 2, y: 0, width: popupViewWidth-2, height: popupViewCellHeight))
        nameLabel.adjustsFontSizeToFitWidth = true
        nameLabel.textColor = UIColor.black
        nameLabel.font = UIFont.systemFont(ofSize: 12)
        self.contentView.addSubview(nameLabel)
        
        let line = UIView (frame: CGRect (x: 0, y: popupViewCellHeight - 0.5, width: popupViewWidth, height: 0.5))
        line.backgroundColor = UIColor.gray
        self.contentView.addSubview(line)
        
    }
    
    func setNameLabel(name:String) {
        nameLabel.text = name
    }

    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
