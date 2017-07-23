//
//  ViewController.swift
//  SAC 17
//
//  Created on 12/24/16.
//  Copyright © 2016 Rowan IEEE. All rights reserved.
//
import UIKit
import Firebase
import WebKit

class WebViewController: BaseViewController, WKNavigationDelegate, WebViewControllerDelegate {
    @IBOutlet var webContainer: UIView!;
    var webView: WKWebView!;
    @IBOutlet weak var splashView: UIView!
    
    var webViewLoads = 0;
    
    var menuButton: UIBarButtonItem!;
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        let websiteURL: String;
        if (FIRAuth.auth()?.currentUser != nil) {
            Global.inst.firebaseJustSignedIn();
        }
        
        if (Global.inst.signedIntoFirebase) {
            websiteURL = Global.inst.sacLinksFirebase["01.Home"]!;
        } else {
            websiteURL = Global.inst.sacLinksNoFirebase["01.Home"]!;
        }
        
        webView = WKWebView(frame: webContainer.frame);
        webView.scrollView.addObserver(self, forKeyPath: #keyPath(UIScrollView.contentSize), options: .new, context: nil);
        webView.autoresizingMask = [.flexibleWidth, .flexibleHeight];
        webView.navigationDelegate = self;
        webView.allowsBackForwardNavigationGestures = true;
        webViewLoads = 40; // 40 times the content of the scrollview will change size, and at that point, the webpage is ready to be displayed
        
        // Menu button for accessing the main menu
        menuButton = UIBarButtonItem(image: UIImage(named: "Menu"), style: UIBarButtonItemStyle.plain, target: self, action: #selector(WebViewController.menuAction));
        
        navigationItem.title = "SAC 17";
        
        webContainer.addSubview(webView);
        loadNewWebPage(url: websiteURL, pageTitle: "Home");
        
        // Preload the menu (it takes time)
        let _ = (storyboard?.instantiateViewController(withIdentifier: "menuViewController") as! MenuViewController).view;
    }
    
    override func viewDidAppear(_ animated: Bool) {
        super.viewDidAppear(animated);
    }
    
    override func observeValue(forKeyPath keyPath: String?, of object: Any?, change: [NSKeyValueChangeKey : Any]?, context: UnsafeMutableRawPointer?) {
        if keyPath == #keyPath(UIScrollView.contentSize) {
            if (webViewLoads != 0) {
                webViewLoads -= 1;
                if (webViewLoads == 0) {
                    webViewLoaded();
                }
            }
        }
    }
    
    func webView(_ webView: WKWebView, decidePolicyFor navigationAction: WKNavigationAction, decisionHandler: @escaping (WKNavigationActionPolicy) -> Void) {
        let urlString = navigationAction.request.url?.absoluteString;
        
        if (urlString!.contains(Global.inst.webURLApp) && urlString!.contains(Global.inst.webSacMain)) {
            decisionHandler(.allow); // Correct app geared website
        } else if (urlString!.contains(Global.inst.webSacMain)) {
            
            if let sepWebsiteString = urlString?.components(separatedBy: "?"), sepWebsiteString.count > 1 {
                let newWebsiteString = sepWebsiteString[0] + Global.inst.webURLApp + sepWebsiteString[1];
                loadNewWebPage(url: newWebsiteString, pageTitle: "");
                decisionHandler(.cancel); // Incorrect website, appending portion to load the correct website
            } else {
                decisionHandler(.allow); // Unsure what this is (may be PDF), but allow it just in case
            }
        } else {
            decisionHandler(.allow); // Some other website, just load it (though it may fail due to permissions in Info.plist)
        }
    }
    
    func webViewLoaded() {
        NSLog("Finished loading webpage");
        splashView.isHidden = true;
        navigationItem.leftBarButtonItem = menuButton;
        navigationItem.title = "Web";
        webView.scrollView.removeObserver(self, forKeyPath: #keyPath(UIScrollView.contentSize));
    }
    
    func backAction() {
        webView.goBack();
    }
    
    func menuAction() {
        let menuViewController = storyboard?.instantiateViewController(withIdentifier: "menuViewController") as! MenuViewController;
        menuViewController.webViewControllerDelegate = self;
        navigationController?.pushViewController(menuViewController, animated: true);
    }
    
    func loadNewWebPage(url: String, pageTitle: String) {
        let websiteURL = URL(string: url)!;
        webView.load(URLRequest(url: websiteURL, cachePolicy: .useProtocolCachePolicy, timeoutInterval: 10));
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning();
    }
    
    
}

