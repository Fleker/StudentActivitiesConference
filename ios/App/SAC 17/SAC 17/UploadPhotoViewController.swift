//
//  UploadPhotoViewController.swift
//  SAC 17
//
//  Created on 2/1/17.
//  Copyright © 2017 Rowan IEEE. All rights reserved.
//

import UIKit
import AVFoundation
import ImageIO
import Photos
import Firebase

class UploadPhotoViewController: BaseViewController, UIImagePickerControllerDelegate, UINavigationControllerDelegate, UITextFieldDelegate {
    
    @IBOutlet weak var chooseSourceView: UIView!;
    @IBOutlet weak var chooseSourceTypeCamera: UIButton!;
    @IBOutlet weak var uploadImageScrollView: UIScrollView!;
    @IBOutlet weak var captionTextField: UITextField!;
    @IBOutlet weak var uploadButton: UIButton!;
    @IBOutlet weak var previewImageView: UIImageView!;
    
    @IBOutlet weak var uploadProgress: UIProgressView!;
    
    var imagePickerController: UIImagePickerController!;
    
    var backButton: UIBarButtonItem!;
    
    var chosenImage: UIImage!;
    
    var uploading: Bool = false;
    
    override func viewDidLoad() {
        super.viewDidLoad();
        
        chooseSourceView.isHidden = false;
        uploadImageScrollView.isHidden = true;
        
        if (!UIImagePickerController.isSourceTypeAvailable(UIImagePickerControllerSourceType.camera)) {
            // Shouldn't happen, but here just in case
            chooseSourceTypeCamera.isHidden = true;
        }
        
        // Back button to go back to the menuViewController
        backButton = UIBarButtonItem(image: UIImage(named: "ChevronLeft"), style: UIBarButtonItemStyle.plain, target: self, action: #selector(UploadPhotoViewController.backAction));
        navigationItem.leftBarButtonItem = backButton;
        
        uploadProgress.setProgress(0, animated: false);
        uploadProgress.isHidden = true;
    }
    
    override func viewWillAppear(_ animated: Bool) {
        registerForKeyboardNotifications();
    }
    
    override func viewWillDisappear(_ animated: Bool) {
        super.viewWillDisappear(animated);
        unregisterForKeyboardNotifications();
    }
    
    func registerForKeyboardNotifications() {
        NotificationCenter.default.addObserver(self, selector: #selector(UploadPhotoViewController.keyboardDidShow(notification:)), name: NSNotification.Name.UIKeyboardDidShow, object: nil);
        NotificationCenter.default.addObserver(self, selector: #selector(UploadPhotoViewController.keyboardWillHide(notification:)), name: NSNotification.Name.UIKeyboardWillHide, object: nil);
    }
    
    func unregisterForKeyboardNotifications() {
        NotificationCenter.default.removeObserver(self);
    }
    
    func keyboardDidShow(notification: NSNotification) {
        let userInfo = notification.userInfo! as NSDictionary;
        let keyboardInfo = userInfo[UIKeyboardFrameBeginUserInfoKey] as! NSValue;
        let keyboardSize = keyboardInfo.cgRectValue.size;
        let contentInsets = UIEdgeInsets(top: uploadImageScrollView.contentInset.top, left: 0, bottom: keyboardSize.height, right: 0);
        uploadImageScrollView.contentInset = contentInsets;
        uploadImageScrollView.scrollIndicatorInsets = contentInsets;
    }
    
    func keyboardWillHide(notification: NSNotification) {
        let contentInsets = UIEdgeInsets(top: uploadImageScrollView.contentInset.top, left: 0, bottom: 0, right: 0);
        uploadImageScrollView.contentInset = contentInsets;
        uploadImageScrollView.scrollIndicatorInsets = contentInsets;
    }
    
    func textFieldShouldReturn(_ textField: UITextField) -> Bool {
        view.endEditing(true);
        if (!uploading) {
            uploadImage(chosenImage, withComment: captionTextField.text!);
        }
        
        return true;
    }
    
    func backAction() {
        let viewControllers = self.navigationController!.viewControllers;
        for aViewController in viewControllers {
            if (aViewController is MenuViewController) {
                self.navigationController!.popToViewController(aViewController, animated: true);
            }
        }
    }
    
    @IBAction func choosePhotos(_ sender: Any) {
        let photoLibraryAuthorizationStatus = PHPhotoLibrary.authorizationStatus();
        
        switch photoLibraryAuthorizationStatus {
        case .denied:
            photoLibraryPermissionDenied();
            break;
        case .authorized:
            imagePickerController = UIImagePickerController();
            imagePickerController.delegate = self;
            imagePickerController.allowsEditing = false;
            imagePickerController.sourceType = UIImagePickerControllerSourceType.photoLibrary;
            
            present(imagePickerController, animated: true, completion: nil);
            break;
        case .restricted:
            photoLibraryPermissionDenied();
            break;
        case .notDetermined:
            PHPhotoLibrary.requestAuthorization() { granted in
                if (granted == PHAuthorizationStatus.authorized) {
                    self.imagePickerController = UIImagePickerController();
                    self.imagePickerController.delegate = self;
                    self.imagePickerController.allowsEditing = false;
                    self.imagePickerController.sourceType = UIImagePickerControllerSourceType.photoLibrary;
                    
                    self.present(self.imagePickerController, animated: true, completion: nil);
                } else {
                    self.photoLibraryPermissionDenied();
                }
            }
            break;
        }
    }
    
    func photoLibraryPermissionDenied() {
        let errorPrompt = UIAlertController(title: "Photo Library Permission Denied", message: "To use the photo library, you need to grant permission for this app to use it. Go to Settings -> Privacy -> Photos and enable this app.", preferredStyle: .alert);
        let okAction = UIAlertAction(title: "OK", style: .default) { (action) in
            // Do nothing, just close the alert
        }
        
        errorPrompt.addAction(okAction);
        
        if #available(iOS 9.0, *) {
            errorPrompt.preferredAction = okAction
        } else {
            // Don't bold the OK button
        };
        
        self.present(errorPrompt, animated: true, completion: nil);
        
        errorPrompt.view.tintColor = Global.inst.sacBrown;
    }
    
    @IBAction func chooseCamera(_ sender: Any) {
        let cameraMediaType = AVMediaTypeVideo;
        let cameraAuthorizationStatus = AVCaptureDevice.authorizationStatus(forMediaType: cameraMediaType);
        
        switch cameraAuthorizationStatus {
        case .denied:
            cameraPermissionDenied();
            break;
        case .authorized:
            imagePickerController = UIImagePickerController();
            imagePickerController.delegate = self;
            imagePickerController.allowsEditing = false;
            imagePickerController.sourceType = UIImagePickerControllerSourceType.camera;
            
            present(imagePickerController, animated: true, completion: nil);
            break;
        case .restricted:
            cameraPermissionDenied();
            break;
        case .notDetermined:
            AVCaptureDevice.requestAccess(forMediaType: cameraMediaType) { granted in
                if (granted) {
                    self.imagePickerController = UIImagePickerController();
                    self.imagePickerController.delegate = self;
                    self.imagePickerController.allowsEditing = false;
                    self.imagePickerController.sourceType = UIImagePickerControllerSourceType.camera;
                    
                    self.present(self.imagePickerController, animated: true, completion: nil);
                } else {
                    self.cameraPermissionDenied();
                }
            }
            break;
        }
    }
    
    func cameraPermissionDenied() {
        let errorPrompt = UIAlertController(title: "Camera Permission Denied", message: "To use the camera, you need to grant permission. Go to Settings -> Privacy -> Camera and enable this app.", preferredStyle: .alert);
        let okAction = UIAlertAction(title: "OK", style: .default) { (action) in
            // Do nothing, just close the alert
        }
        
        errorPrompt.addAction(okAction);
        
        if #available(iOS 9.0, *) {
            errorPrompt.preferredAction = okAction
        } else {
            // Don't bold the OK button
        };
        
        self.present(errorPrompt, animated: true, completion: nil);
        
        errorPrompt.view.tintColor = Global.inst.sacBrown;
    }
    
    @IBAction func uploadButton(_ sender: Any) {
        if (!uploading) {
            uploadImage(chosenImage, withComment: captionTextField.text!);
        }
    }
    
    func imagePickerController(_ picker: UIImagePickerController, didFinishPickingMediaWithInfo info: [String : Any]) {
        uploadImageScrollView.isHidden = false;
        chooseSourceView.isHidden = true;
        
        captionTextField.becomeFirstResponder();
        
        chosenImage = info[UIImagePickerControllerOriginalImage] as! UIImage;
        previewImageView.image = chosenImage;
        
        imagePickerController.dismiss(animated: true, completion: nil);
    }
    
    func imagePickerControllerDidCancel(_ picker: UIImagePickerController) {
        imagePickerController.dismiss(animated: true, completion: nil);
    }
    
    func uploadImage(_ image: UIImage, withComment comment: String) {
        uploading = true;
        backButton.isEnabled = false;
        uploadButton.isEnabled = false;
        
        let imageData = UIImageJPEGRepresentation(image, 0.8)!;
        let imageSource = CGImageSourceCreateWithData(imageData as CFData, nil);
        let imageProperties = [kCGImagePropertyExifDictionary as String: [kCGImagePropertyExifUserComment as String: comment]];
        
        let uniformTypeIdentifier = CGImageSourceGetType(imageSource!);
        let finalData = NSMutableData(data: imageData);
        let destination = CGImageDestinationCreateWithData(finalData, uniformTypeIdentifier!, 1, nil);
        CGImageDestinationAddImageFromSource(destination!, imageSource!, 0, imageProperties as CFDictionary);
        CGImageDestinationFinalize(destination!);
        
        let date = Date();
        let calendar = Calendar.current;
        
        let year = calendar.component(.year, from: date);
        let month = calendar.component(.month, from: date);
        let day = calendar.component(.day, from: date);
        let hour = calendar.component(.hour, from: date);
        let minute = calendar.component(.minute, from: date);
        let second = calendar.component(.second, from: date);
        
        let storage = FIRStorage.storage();
        let imageURL = String(format: "/images/IMG_%04d%02d%02d_%02d%02d%02d.jpg", year, month, day, hour, minute, second);
        
        updateFirebaseDatabaseWith(imagePath: imageURL, comment: comment)
        
        let url = String(format: "%@%@", Global.inst.firebaseStorageLocation, imageURL);
        let imageRef = storage.reference(forURL: url);

        let metadata = FIRStorageMetadata();
        metadata.contentType = "image/jpeg";
        
        uploadProgress.isHidden = false;
        uploadProgress.setProgress(0.0, animated: false);
        
        let uploadTask = imageRef.put(finalData as Data, metadata: metadata);

        uploadTask.observe(.success) { snapshot in
            print("Uploaded photo");
            let viewControllers = self.navigationController!.viewControllers;
            for aViewController in viewControllers {
                if (aViewController is MenuViewController) {
                    self.navigationController!.popToViewController(aViewController, animated: true);
                }
            }
        }
        
        uploadTask.observe(.progress) { snapshot in
            let progress = Float(Double(snapshot.progress!.completedUnitCount) / Double(snapshot.progress!.totalUnitCount));
            self.uploadProgress.setProgress(progress, animated: true);
        }
    }
    
    func updateFirebaseDatabaseWith(imagePath: String, comment: String) {
        Global.inst.ref = FIRDatabase.database().reference();
        let key = Global.inst.ref.child("release").child("images").childByAutoId().key;
        let imageData: [String: Any] = ["approved": false,
                         "caption": comment,
                         "path": imagePath,
                         "timestamp": Int64(Date().timeIntervalSince1970 * 1000),
                         "uid": FIRAuth.auth()!.currentUser!.uid];
        let updates = ["\(Global.inst.baseVoting)\(Global.inst.imagesLocation)\(key)/": imageData];
        print(key);
        print(imageData);
        print(updates);
        Global.inst.ref.updateChildValues(updates);
    }
}
