package org.rowanieee.sac;

import android.app.Activity;
import android.content.Context;
import android.content.DialogInterface;
import android.content.SharedPreferences;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.media.ExifInterface;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.net.Uri;
import android.preference.PreferenceManager;
import android.support.annotation.NonNull;
import android.support.design.widget.Snackbar;
import android.support.v4.graphics.BitmapCompat;
import android.text.InputType;
import android.util.Log;
import android.view.ViewGroup;
import android.widget.Toast;

import com.afollestad.materialdialogs.DialogAction;
import com.afollestad.materialdialogs.MaterialDialog;
import com.afollestad.materialdialogs.simplelist.MaterialSimpleListAdapter;
import com.afollestad.materialdialogs.simplelist.MaterialSimpleListItem;
import com.google.android.gms.tasks.OnCompleteListener;
import com.google.android.gms.tasks.OnSuccessListener;
import com.google.android.gms.tasks.Task;
import com.google.firebase.auth.FirebaseAuth;
import com.google.firebase.auth.FirebaseUser;
import com.google.firebase.crash.FirebaseCrash;
import com.google.firebase.database.DatabaseReference;
import com.google.firebase.database.FirebaseDatabase;
import com.google.firebase.storage.FirebaseStorage;
import com.google.firebase.storage.OnProgressListener;
import com.google.firebase.storage.StorageReference;
import com.google.firebase.storage.UploadTask;
import com.karumi.dexter.Dexter;
import com.karumi.dexter.MultiplePermissionsReport;
import com.karumi.dexter.PermissionToken;
import com.karumi.dexter.listener.PermissionRequest;
import com.karumi.dexter.listener.multi.CompositeMultiplePermissionsListener;
import com.karumi.dexter.listener.multi.MultiplePermissionsListener;
import com.karumi.dexter.listener.multi.SnackbarOnAnyDeniedMultiplePermissionsListener;

import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.HashMap;
import java.util.List;
import java.util.Locale;

import pl.aprilapps.easyphotopicker.EasyImage;

class Utils {

    private static final String STORAGE_BASE = "gs://sac17-9fc02.appspot.com";
    private static final String ATTENDEES = "attendees";
    static final String IMAGES = "images";
    static final String PROFILE_IMAGES = "profile-images";
    static final int MAX_IMAGE_RES = 1600;

    static void showToast(Context c, String m) {
        Toast.makeText(c,m,Toast.LENGTH_SHORT).show();
    }

    static void showToast(Context c, int m) {
        Toast.makeText(c,m,Toast.LENGTH_SHORT).show();
    }

    static boolean isOnline(Context c) {
        ConnectivityManager connMgr = (ConnectivityManager)
                c.getApplicationContext().getSystemService(Context.CONNECTIVITY_SERVICE);
        NetworkInfo networkInfo = connMgr.getActiveNetworkInfo();
        return networkInfo != null && networkInfo.isConnected();
    }

    static File exportFile(File src, File dst) throws IOException {

        String timeStamp = new SimpleDateFormat("yyyyMMdd_HHmmss", Locale.getDefault()).format(new Date());
        File expFile = new File(dst.getPath() + File.separator + "IMG_" + timeStamp + ".jpg");
        Log.i("path",expFile.getAbsolutePath());
        FileInputStream inStream = new FileInputStream(src);
        FileOutputStream outStream = new FileOutputStream(expFile);
        byte[] buffer = new byte[1024];
        int read;
        while ((read = inStream.read(buffer)) != -1) {
            outStream.write(buffer, 0, read);
        }
        inStream.close();

        outStream.flush();
        outStream.close();

        return expFile;
    }

    static class DItems {
        static final int HOME = 0,
        PHOTOS = 1,
        SCHEDULE = 2,
        SHUTTLES = 3,
        COMPETITIONS = 4,
        SPONSORS = 5,
        HOTEL = 6,
        BANQUET = 7,
        FAQ = 8,
        VOTE = 9,
        LOGOUT = 10;
    }

    private static class ImageType {
        static final int CAMERA = 0,
        GALLERY = 1;
    }

    private static void showPermissionRationaleDialog(Context c, final PermissionToken token) {
        new MaterialDialog.Builder(c)
                .title(R.string.permisson_required)
                .content(R.string.camera_storage_permission_required)
                .iconRes(R.mipmap.ic_launcher)
                .titleColorRes(R.color.primary)
                .positiveText(android.R.string.ok)
                .negativeText(android.R.string.cancel)
                .positiveColorRes(R.color.accent)
                .negativeColorRes(R.color.accent)
                .onPositive(new MaterialDialog.SingleButtonCallback() {
                    @Override
                    public void onClick(@NonNull MaterialDialog dialog, @NonNull DialogAction which) {
                        token.continuePermissionRequest();
                    }
                })
                .onNegative(new MaterialDialog.SingleButtonCallback() {
                    @Override
                    public void onClick(@NonNull MaterialDialog dialog, @NonNull DialogAction which) {
                        token.cancelPermissionRequest();
                    }
                })
                .cancelListener(new DialogInterface.OnCancelListener() {
                    @Override
                    public void onCancel(DialogInterface dialogInterface) {
                        token.cancelPermissionRequest();
                    }
                })
                .show();
    }

    static void uploadImageToFirebase(final Context c, final String fbPath, final File image,
                                      final String caption, final OnCompleteListener<Void> oc) {

        if(caption != null) {
            try {
                Utils.modifyExif(caption, image);
            } catch (IOException e) {
                e.printStackTrace();
            }
        }

        // Resize the bitmap
        Bitmap temp = BitmapFactory.decodeFile(image.getAbsolutePath());
        Bitmap myBitmap = resizeBitmap(temp, MAX_IMAGE_RES);

        // Upload the smaller file size
        if(BitmapCompat.getAllocationByteCount(temp) < BitmapCompat.getAllocationByteCount(myBitmap)) {
            myBitmap = temp;
        }

        ByteArrayOutputStream baos = new ByteArrayOutputStream();
        myBitmap.compress(Bitmap.CompressFormat.JPEG, 80, baos);

        myBitmap.recycle();
        temp.recycle();;

        final MaterialDialog uploadDialog = new MaterialDialog.Builder(c)
                .title(R.string.uploading_photo)
                .titleColorRes(R.color.primary)
                .iconRes(R.mipmap.ic_launcher)
                .content(R.string.please_wait)
                .progress(false, 100, true)
                .show();

        final StorageReference storageReference = FirebaseStorage.getInstance()
                .getReferenceFromUrl(STORAGE_BASE).child(fbPath + "/" + image.getName());

        UploadTask uploadTask = storageReference.putBytes(baos.toByteArray());

        uploadTask.addOnProgressListener(new OnProgressListener<UploadTask.TaskSnapshot>() {
            @Override
            public void onProgress(UploadTask.TaskSnapshot taskSnapshot) {
                double progress = (100.0 * taskSnapshot.getBytesTransferred()) / taskSnapshot.getTotalByteCount();
                uploadDialog.setProgress((int)progress);
            }
        });

        uploadTask.addOnCompleteListener(new OnCompleteListener<UploadTask.TaskSnapshot>() {
            @Override
            public void onComplete(@NonNull Task<UploadTask.TaskSnapshot> task) {
                uploadDialog.dismiss();
                FirebaseUser u = FirebaseAuth.getInstance().getCurrentUser();
                String uid = "";
                if (u != null) {
                    uid = u.getUid();
                } else {
                    FirebaseCrash.report(new Exception("FirebaseUser was null [during image upload]"));
                }

                if(fbPath.equals(IMAGES)) {
                    saveImageDetails(new Image(uid, caption, storageReference.getPath()), oc);
                } else if(fbPath.equals(PROFILE_IMAGES)) {
                    saveSharedPref(c, PROFILE_IMAGES, image.getAbsolutePath());
                    saveProfileImageDetails(uid, storageReference.getPath(), oc);
                }
            }
        }).addOnSuccessListener(new OnSuccessListener<UploadTask.TaskSnapshot>() {
            @Override
            public void onSuccess(UploadTask.TaskSnapshot taskSnapshot) {
                saveSharedPref(c, "profile_image_url", String.valueOf(taskSnapshot.getDownloadUrl()));
            }
        });
    }

    private static void modifyExif(String message, File imageFile ) throws IOException {
        ExifInterface exif = new ExifInterface(imageFile.getAbsolutePath());
        exif.setAttribute("UserComment", message);
        exif.saveAttributes();
    }

    static void showCaptionDialog(Context c, MaterialDialog.InputCallback ip) {
        new MaterialDialog.Builder(c)
                .title(R.string.caption)
                .content(R.string.add_a_caption)
                .positiveText(R.string.upload)
                .inputType(InputType.TYPE_CLASS_TEXT)
                .input(c.getString(R.string.sac17), null, ip).show();
    }

    private static CompositeMultiplePermissionsListener getPermissionsListener(final Activity activity, ViewGroup rootView) {

        MultiplePermissionsListener snackbarPermissionListener =
                SnackbarOnAnyDeniedMultiplePermissionsListener.Builder
                        .with(rootView, R.string.camera_storage_permission_required)
                        .withOpenSettingsButton(R.string.settings)
                        .withCallback(new Snackbar.Callback() {
                            @Override
                            public void onShown(Snackbar snackbar) {
                                super.onShown(snackbar);
                            }
                            @Override
                            public void onDismissed(Snackbar snackbar, int event) {
                                super.onDismissed(snackbar, event);
                            }
                        }).build();

        MultiplePermissionsListener feedbackViewPermissionListener = new MultiplePermissionsListener() {
            @Override
            public void onPermissionsChecked(MultiplePermissionsReport report) {
                if(report.getDeniedPermissionResponses().size() == 0) {
                    EasyImage.openCamera(activity, Utils.ImageType.CAMERA);
                }
            }

            @Override
            public void onPermissionRationaleShouldBeShown(List<PermissionRequest> permissions, PermissionToken token) {
                Utils.showPermissionRationaleDialog(activity, token);
            }
        };

        return new CompositeMultiplePermissionsListener(feedbackViewPermissionListener, snackbarPermissionListener);
    }

    static MaterialDialog createImagePickerDialog(final Activity activity, final ViewGroup rootView) {
        final MaterialSimpleListAdapter adapter = new MaterialSimpleListAdapter(new MaterialSimpleListAdapter.Callback() {
            @Override
            public void onMaterialListItemSelected(MaterialDialog dialog, int index, MaterialSimpleListItem item) {
                if(item.getId() == Utils.ImageType.CAMERA) {
                    Dexter.withActivity(activity)
                            .withPermissions(android.Manifest.permission.CAMERA, android.Manifest.permission.WRITE_EXTERNAL_STORAGE)
                            .withListener(Utils.getPermissionsListener(activity, rootView))
                            .check();
                } else if(item.getId() == Utils.ImageType.GALLERY) {
                    EasyImage.openGallery(activity, Utils.ImageType.GALLERY);
                }
                dialog.dismiss();
            }
        });

        adapter.add(new MaterialSimpleListItem.Builder(activity)
                .content(R.string.camera)
                .icon(R.drawable.ic_camera)
                .iconPaddingDp(8)
                .backgroundColorRes(R.color.primary)
                .id(Utils.ImageType.CAMERA)
                .build());
        adapter.add(new MaterialSimpleListItem.Builder(activity)
                .content(R.string.gallery)
                .icon(R.drawable.ic_photos)
                .iconPaddingDp(8)
                .backgroundColorRes(R.color.primary)
                .id(Utils.ImageType.GALLERY)
                .build());

        return new MaterialDialog.Builder(activity)
                .title("Choose Source")
                .iconRes(R.mipmap.ic_launcher)
                .titleColorRes(R.color.primary)
                .adapter(adapter, null)
                .build();
    }

    static DatabaseReference getFirebaseParentRef() {
        return FirebaseDatabase.getInstance().getReference().child(BuildConfig.FIREBASE_PARENT);
    }

    private static void saveImageDetails(Image i, OnCompleteListener<Void> oc) {
        getFirebaseParentRef().child(IMAGES).push().setValue(i).addOnCompleteListener(oc);
    }

    private static void saveProfileImageDetails(String uid, String path, OnCompleteListener<Void> oc) {
        getFirebaseParentRef().child(PROFILE_IMAGES + "/" + uid).push().setValue(path).addOnCompleteListener(oc);
    }

    static void saveSharedPref(Context c, String key, String value) {
        SharedPreferences p = PreferenceManager.getDefaultSharedPreferences(c);
        p.edit().putString(key, value).apply();
    }

    static String getSharedPref(Context c, String key, String value) {
        SharedPreferences p = PreferenceManager.getDefaultSharedPreferences(c);
        return p.getString(key, value);
    }

    static Bitmap resizeBitmap(final Bitmap temp, final int size) {
        if (size > 0) {
            int width = temp.getWidth();
            int height = temp.getHeight();
            float ratioBitmap = (float) width / (float) height;
            int finalWidth = size;
            int finalHeight = size;
            if (ratioBitmap < 1) {
                finalWidth = (int) ((float) size * ratioBitmap);
            } else {
                finalHeight = (int) ((float) size / ratioBitmap);
            }
            return Bitmap.createScaledBitmap(temp, finalWidth, finalHeight, true);
        } else {
            return temp;
        }
    }

    static HashMap<String, String> getProfileInfo(Context c ) {

        HashMap<String, String> userJson = new HashMap<>();

        userJson.put("snapchat", getSharedPref(c, "snapchat", ""));
        userJson.put("instagram", getSharedPref(c, "instagram", ""));
        userJson.put("twitter", getSharedPref(c, "twitter", ""));
        userJson.put("collabratec", getSharedPref(c, "collabratec", ""));
        userJson.put("phone", getSharedPref(c, "phone", ""));
        userJson.put("name", getSharedPref(c, "name", ""));
        userJson.put("email", getSharedPref(c, "email", ""));
        userJson.put(PROFILE_IMAGES, getSharedPref(c, PROFILE_IMAGES, ""));


        return userJson;
    }
}
