//
//  LoginViewController.swift
//  tiger
//
//  Created by wayne on 16/9/30.
//  Copyright © 2016年 kkd. All rights reserved.
//

import UIKit

class LoginViewController: UIViewControllerEx ,UITextFieldDelegate{

    var nameText: UITextField?
    var pwdText: UITextField?
    
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        creatUI()
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    fileprivate func creatUI(){
        view.backgroundColor = UIColor.white
        
        let titleArray = ["用户名：","密    码："]
        let textPlace = ["用户名/手机号码","请输入密码"]
        
        let diff:CGFloat = 20
        let labWid:CGFloat = 80
        let labHei:CGFloat = 30
        let textFieldWid:CGFloat = 200
        let labX:CGFloat = (ScreenWid - labWid - textFieldWid)/2
        
        
        //遍历 用户名密码
        for i in 0..<2 {
            let label = UILabel()
            label.frame = CGRect(x: labX, y: 40 + CGFloat(i) * (labHei + diff), width: labWid, height: labHei)
            label.text = titleArray[i]
            label.textColor = UIColor.black
            view.addSubview(label)
            
            let textField = UITextField()
            textField.delegate = self
            textField.frame = CGRect(x: label.frame.maxX, y: label.frame.origin.y, width: textFieldWid, height: labHei)
            textField.placeholder = textPlace[i]
//            textField.layer.masksToBounds = true
//            textField.layer.cornerRadius = 4
//            textField.layer.borderWidth = 1
//            textField.layer.borderColor = UIColor.black.cgColor
            textField.adjustsFontSizeToFitWidth = true
            textField.borderStyle = UITextBorderStyle.roundedRect
            textField.clearButtonMode = .whileEditing
            textField.addTarget(self, action: #selector(textFieldChange), for: .editingChanged)
            view.addSubview(textField)
            
            if i == 0 {
                textField.returnKeyType = .next
                
                nameText = textField
            }else{
                textField.returnKeyType = .done
                textField.isSecureTextEntry = true
                pwdText = textField
            }
        }
        
        //登陆按钮
        let btnWid:CGFloat = 220
        let btnHei:CGFloat = 40
        let btnX:CGFloat = (ScreenWid - btnWid)/2
        let btnY:CGFloat = pwdText!.frame.maxY + 20
        
        let button = UIButton()
        button.frame = CGRect(x: btnX, y: btnY, width: btnWid, height: btnHei)
        button.setTitle("登陆", for: UIControlState())
        button.backgroundColor = UIColor.green
        button.layer.masksToBounds = true
        button.layer.cornerRadius = 4
        button.addTarget(self, action: #selector(clickLoginBtn), for: .touchUpInside)
        view.addSubview(button)
        
    }
    
    /**
     点击按钮
     */
    func clickLoginBtn(){
        resign()
        
        
        if (nameText?.text?.isEmpty)! {
            Utils.alertMessage(message: "请输入用户名")
            return
        }
        
        if (pwdText?.text?.isEmpty)! {
            Utils.alertMessage(message: "请输入密码")
            return
        }
        
        //登陆
        CommSender.login("root", p: "123456", callback: { (code, msg, error) in
            
            if code == 200{
                Utils.setUserLogin(login: true)
                let mainNav = UINavigationController.init(rootViewController: MainViewController())
                UIApplication.shared.keyWindow?.rootViewController = mainNav
                Utils.alertMessage(message: "登陆成功")
            }else{
                Utils.alertMessage(message: msg!)
            }
            
            }, view: view)
    }
    
    override func touchesEnded(_ touches: Set<UITouch>, with event: UIEvent?) {
//        resign()
        view.endEditing(true)
    }
    
    fileprivate func resign(){
        nameText?.resignFirstResponder()
        pwdText?.resignFirstResponder()
    }
    
    func textFieldShouldReturn(_ textField: UITextField) -> Bool {
        if textField == nameText {
            pwdText?.becomeFirstResponder()
        }else{
            resign()
        }
        return true
    }
    
    
    //监听输入框的变化
    func textFieldChange(sender:UITextField){
        if sender == nameText {
            if (sender.text?.characters.count)! > 8{
                var string:String = sender.text!
                let rang = string.index(string.endIndex, offsetBy: -(string.characters.count - 8))..<string.endIndex
                string.removeSubrange(rang)
                sender.text = string
            }
        }else{
            if (sender.text?.characters.count)! > 6{
                var string:String = sender.text!
                let rang = string.index(string.endIndex, offsetBy: -(string.characters.count - 6))..<string.endIndex
                string.removeSubrange(rang)
                sender.text = string
            }
        }
    }
    
    /******************* UITextFieldDelegate *******************/
    func textField(_ textField: UITextField, shouldChangeCharactersIn range: NSRange, replacementString string: String) -> Bool {
       return true
    }

}
