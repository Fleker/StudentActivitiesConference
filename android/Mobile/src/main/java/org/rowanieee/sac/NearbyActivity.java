package org.rowanieee.sac;

import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.drawable.BitmapDrawable;
import android.os.Environment;
import android.support.annotation.NonNull;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.support.v7.app.AppCompatDelegate;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.util.Log;
import android.view.ViewGroup;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import com.afollestad.materialdialogs.MaterialDialog;
import com.google.android.gms.common.ConnectionResult;
import com.google.android.gms.common.api.GoogleApiClient;
import com.google.android.gms.nearby.Nearby;
import com.google.android.gms.nearby.messages.Message;
import com.google.android.gms.nearby.messages.MessageListener;
import com.google.android.gms.nearby.messages.Strategy;
import com.google.android.gms.nearby.messages.SubscribeCallback;
import com.google.android.gms.nearby.messages.SubscribeOptions;
import com.google.android.gms.tasks.OnCompleteListener;
import com.google.android.gms.tasks.Task;
import com.google.firebase.auth.FirebaseAuth;
import com.google.firebase.auth.FirebaseUser;

import org.json.JSONException;
import org.json.JSONObject;

import java.io.File;
import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.HashSet;
import java.util.List;

import butterknife.BindView;
import butterknife.ButterKnife;
import butterknife.OnClick;
import butterknife.OnLongClick;
import pl.aprilapps.easyphotopicker.DefaultCallback;
import pl.aprilapps.easyphotopicker.EasyImage;

public class NearbyActivity extends AppCompatActivity implements GoogleApiClient.ConnectionCallbacks, GoogleApiClient.OnConnectionFailedListener {

    static {
        AppCompatDelegate.setCompatVectorFromResourcesEnabled(true);
    }

    private static final String TAG = "NearbyActivity";
    private GoogleApiClient mGoogleApiClient;
    private Message mActiveMessage;
    private MessageListener mMessageListener;
    private HashSet<JSONObject> nearbyPeople = new HashSet<>();
    private List<String> nearbyPeopleEmails = new ArrayList<>();
    private NearbyAdapter nearbyAdapter;
    private MaterialDialog nearbyPeopleDialog;

    @BindView(R.id.snapchat)
    EditText etSnapchat;

    @BindView(R.id.twitter)
    EditText etTwitter;

    @BindView(R.id.instagram)
    EditText etInstagram;

    @BindView(R.id.collabratec)
    EditText etCollabratec;

    @BindView(R.id.phone)
    EditText etPhone;

    @BindView(R.id.profileImage)
    ImageView profileImage;

    @BindView(R.id.name)
    TextView name;

    @BindView(R.id.email)
    TextView email;

    @BindView(R.id.others)
    RecyclerView recyclerView;

    @OnLongClick(R.id.profileImage)
    boolean onProfileImageClick() {
        Utils.createImagePickerDialog(this,(ViewGroup)findViewById(android.R.id.content)).show();
        return true;
    }

    @OnClick(R.id.find)
    void onFindClick() {
        nearbyPeopleDialog.show();
    }
    // Handles the connect button - publishes message via nearby.
    @OnClick(R.id.connect)
    void onConnectClick() {
        FirebaseUser u = FirebaseAuth.getInstance().getCurrentUser();

        if(u == null) {
            Toast.makeText(this,"Error: not logged in!", Toast.LENGTH_SHORT).show();
            finish();
            return;
        }

        String sc = etSnapchat.getText().toString();
        String ig = etInstagram.getText().toString();
        String tw = etTwitter.getText().toString();
        String ct = etCollabratec.getText().toString();
        String ph = etPhone.getText().toString();

        JSONObject userJson = new JSONObject();

        try {
            userJson.put("snapchat", sc);
            userJson.put("instagram", ig);
            userJson.put("twitter", tw);
            userJson.put("collabratec", ct);
            userJson.put("phone", ph);
            userJson.put("name", u.getDisplayName());
            userJson.put("email", u.getEmail());
            userJson.put("profile_image_url", Utils.getSharedPref(this,"profile_image_url",""));
        } catch (JSONException e) {
            e.printStackTrace();
        }

        Utils.saveSharedPref(this, "snapchat", sc);
        Utils.saveSharedPref(this, "instagram", ig);
        Utils.saveSharedPref(this, "twitter", tw);
        Utils.saveSharedPref(this, "collabratec", ct);
        Utils.saveSharedPref(this, "phone", ph);
        Utils.saveSharedPref(this, "name", u.getDisplayName());
        Utils.saveSharedPref(this, "email", u.getEmail());

        publish(userJson);
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_nearby);
        AppCompatDelegate.setCompatVectorFromResourcesEnabled(true);

        ButterKnife.bind(this);

        populateProfile();

        nearbyAdapter = new NearbyAdapter(nearbyPeople);

        nearbyPeopleDialog = new MaterialDialog.Builder(this)
                .title(R.string.nearby)
                .adapter(nearbyAdapter, null)
                .build();

        mGoogleApiClient = new GoogleApiClient.Builder(this)
                .addApi(Nearby.MESSAGES_API)
                .addConnectionCallbacks(this)
                .enableAutoManage(this, this)
                .build();

        recyclerView.setLayoutManager(new LinearLayoutManager(this, LinearLayoutManager.HORIZONTAL, false));
        recyclerView.setAdapter(nearbyAdapter);

        mMessageListener = new MessageListener() {
            @Override
            public void onFound(Message message) {
                String messageAsString = new String(message.getContent());
                Log.d(TAG, "Found message: " + messageAsString);
                try {
                    JSONObject json = new JSONObject(messageAsString);
                    if (!nearbyPeopleEmails.contains(json.getString("email"))) {
                        nearbyPeopleEmails.add(json.getString("email"));
                        nearbyPeople.add(json);
                        nearbyAdapter.notifyItemInserted(nearbyPeople.size()-1);
                    }
                } catch (JSONException e) {
                    e.printStackTrace();
                }


            }

            @Override
            public void onLost(Message message) {
                String messageAsString = new String(message.getContent());
                Log.d(TAG, "Lost sight of message: " + messageAsString);
                try {
                    JSONObject json = new JSONObject(messageAsString);
                    nearbyPeople.remove(json);
                    nearbyPeopleEmails.remove(json.getString("email"));
                    nearbyAdapter.notifyDataSetChanged();
                } catch (JSONException e) {
                    e.printStackTrace();
                }
            }
        };

    }

    private void populateProfile() {
        HashMap<String,String> profileInfo = Utils.getProfileInfo(this);

        if(!profileInfo.get(Utils.PROFILE_IMAGES).equals("")) {
            Bitmap b = BitmapFactory.decodeFile(profileInfo.get(Utils.PROFILE_IMAGES));
            if(b != null) {
                Bitmap myBitmap = Utils.resizeBitmap(b, Utils.MAX_IMAGE_RES);
                profileImage.setImageBitmap(myBitmap);
            }
        }

        name.setText(profileInfo.get("name"));
        email.setText(profileInfo.get("email"));
        etPhone.setText(profileInfo.get("phone"));
        etSnapchat.setText(profileInfo.get("snapchat"));
        etInstagram.setText(profileInfo.get("instagram"));
        etTwitter.setText(profileInfo.get("twitter"));
        etCollabratec.setText(profileInfo.get("collabratec"));
    }


    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);

        EasyImage.handleActivityResult(requestCode, resultCode, data, this, new DefaultCallback() {
            @Override
            public void onImagePickerError(Exception e, EasyImage.ImageSource source, int type) {
                super.onImagePickerError(e, source, type);
            }

            @Override
            public void onCanceled(EasyImage.ImageSource source, int type) {
                super.onCanceled(source, type);
            }

            @Override
            public void onImagesPicked(@NonNull List<File> imageFiles, final EasyImage.ImageSource source, int type) {
                final File imageFile = imageFiles.get(0);

                File f = imageFile;

                try {
                    // Save image to camera directory if it was taken with camera
                    if(source == EasyImage.ImageSource.CAMERA) {
                        String path = Environment.getExternalStorageDirectory() + File.separator +
                                Environment.DIRECTORY_DCIM + File.separator +
                                "Camera" + File.separator;
                        f = Utils.exportFile(imageFile, new File(path));
                    }
                } catch (IOException e) {
                    e.printStackTrace();
                }

                OnCompleteListener<Void> oc = new OnCompleteListener<Void>() {
                    @Override
                    public void onComplete(@NonNull Task task) {
                        Utils.showToast(NearbyActivity.this, R.string.photo_uploaded);
                        String imageFile = Utils.getSharedPref(NearbyActivity.this, Utils.PROFILE_IMAGES, "");
                        Bitmap b = BitmapFactory.decodeFile(imageFile);
                        Bitmap myBitmap = Utils.resizeBitmap(b, Utils.MAX_IMAGE_RES);

                        if(profileImage.getDrawable() instanceof  BitmapDrawable) {
                            BitmapDrawable old = ((BitmapDrawable) profileImage.getDrawable());
                            if(old != null) {
                                old.getBitmap().recycle();
                            }
                        }



                        profileImage.setImageBitmap(myBitmap);
                    }
                };

                // Upload the image to firebase storage and add to database
                Utils.uploadImageToFirebase(NearbyActivity.this, Utils.PROFILE_IMAGES, f, null, oc);
            }
        });
    }

    @Override
    public void onConnected(Bundle connectionHint) {
        subscribe();
        onConnectClick();
    }

    @Override
    public void onConnectionSuspended(int i) {

    }

    @Override
    public void onStop() {
        if(mGoogleApiClient.isConnected()) {
            unpublish();
            unsubscribe();
        }
        if(profileImage.getDrawable() instanceof  BitmapDrawable){
            ((BitmapDrawable) profileImage.getDrawable()).getBitmap().recycle();
        }
        super.onStop();
    }

    @Override
    public void onConnectionFailed(@NonNull ConnectionResult connectionResult) {

    }

    // Subscribe to receive messages.
    private void subscribe() {
        Log.i(TAG, "Subscribing.");
        SubscribeOptions options = new SubscribeOptions.Builder()
                .setStrategy(Strategy.DEFAULT)
                .setCallback(new SubscribeCallback() {
                    @Override
                    public void onExpired() {
                        Log.i(TAG, "Subbscription Expired");
                        super.onExpired();
                    }
                })
                .build();

        Nearby.Messages.subscribe(mGoogleApiClient, mMessageListener, options);
    }

    private void publish(JSONObject message) {
        Log.i(TAG, "Publishing message: " + message);
        mActiveMessage = new Message(message.toString().getBytes());
        if (mGoogleApiClient.isConnected()) {
            Nearby.Messages.publish(mGoogleApiClient, mActiveMessage);
            Utils.showToast(this, "Sharing your info!");
            Log.i(TAG, "Publish occurs as far as we know.");
        } else {
            Utils.showToast(this, "Connecting..Please try again");
            Log.w(TAG, "Cannot publish until we are connected.");
        }

    }

    private void unpublish() {
        Log.i(TAG, "Unpublishing.");
        if (mActiveMessage != null) {
            Nearby.Messages.unpublish(mGoogleApiClient, mActiveMessage);
            mActiveMessage = null;
        }
    }

    private void unsubscribe() {
        Log.i(TAG, "Unsubscribing.");
        Nearby.Messages.unsubscribe(mGoogleApiClient, mMessageListener);
    }
}
