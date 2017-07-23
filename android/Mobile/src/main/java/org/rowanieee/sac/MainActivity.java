package org.rowanieee.sac;

import android.annotation.SuppressLint;
import android.annotation.TargetApi;
import android.content.Intent;
import android.graphics.Bitmap;
import android.os.Build;
import android.os.Bundle;
import android.os.Environment;
import android.support.annotation.NonNull;
import android.support.v4.content.ContextCompat;
import android.support.v4.widget.SwipeRefreshLayout;
import android.support.v7.app.ActionBar;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.app.AppCompatDelegate;
import android.support.v7.widget.Toolbar;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.webkit.WebResourceRequest;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.ImageView;
import android.widget.Toast;

import com.afollestad.materialdialogs.MaterialDialog;
import com.firebase.ui.auth.AuthUI;
import com.google.android.gms.tasks.OnCompleteListener;
import com.google.android.gms.tasks.Task;
import com.google.firebase.auth.FirebaseAuth;
import com.google.firebase.auth.FirebaseUser;
import com.mikepenz.materialdrawer.AccountHeader;
import com.mikepenz.materialdrawer.AccountHeaderBuilder;
import com.mikepenz.materialdrawer.Drawer;
import com.mikepenz.materialdrawer.DrawerBuilder;
import com.mikepenz.materialdrawer.model.PrimaryDrawerItem;
import com.mikepenz.materialdrawer.model.ProfileDrawerItem;
import com.mikepenz.materialdrawer.model.interfaces.IDrawerItem;

import java.io.File;
import java.io.IOException;
import java.util.List;

import butterknife.BindView;
import butterknife.ButterKnife;
import pl.aprilapps.easyphotopicker.DefaultCallback;
import pl.aprilapps.easyphotopicker.EasyImage;

import static org.rowanieee.sac.Utils.DItems.*;

public class MainActivity extends AppCompatActivity implements SwipeRefreshLayout.OnRefreshListener{

    private final static String URL = "http://rowanieee.org/sac17?app";

    @BindView(R.id.webView)
    WebView webView;

    @BindView(R.id.toolbar)
    Toolbar toolbar;

    @BindView(R.id.swipeContainer)
    SwipeRefreshLayout srl;

    Drawer drawer;

    MaterialDialog photoPickerDialog;

    FirebaseUser user = FirebaseAuth.getInstance().getCurrentUser();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        AppCompatDelegate.setCompatVectorFromResourcesEnabled(true);
        if(!Utils.isOnline(this)) {
            Toast.makeText(this, R.string.internet_required, Toast.LENGTH_SHORT).show();
            finish();
        } else {
            ButterKnife.bind(this);
            initView();
        }
    }

    @Override
    public void onRefresh() {
        webView.reload();
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

                Utils.showCaptionDialog(MainActivity.this, new MaterialDialog.InputCallback() {
                    @Override
                    public void onInput(@NonNull  MaterialDialog dialog, CharSequence input) {
                        dialog.dismiss();

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
                                Utils.showToast(MainActivity.this,R.string.photo_uploaded);
                            }
                        };

                        // Upload the image to firebase storage and add to database
                        Utils.uploadImageToFirebase(MainActivity.this, Utils.IMAGES, f,input.toString(), oc);
                    }
                });
            }
        });
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        MenuInflater inflater = getMenuInflater();
        inflater.inflate(R.menu.menu_main, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        switch (item.getItemId()) {
            case R.id.photo:
                if(photoPickerDialog == null) {
                    photoPickerDialog = Utils.createImagePickerDialog(this,(ViewGroup)findViewById(android.R.id.content));
                }
                photoPickerDialog.show();
                return true;
            case R.id.nearby:
                startActivity(new Intent(MainActivity.this, NearbyActivity.class));
            default:
                return super.onOptionsItemSelected(item);
        }
    }

    private void initView() {

        setSupportActionBar(toolbar);

        srl.setOnRefreshListener(this);
        srl.setColorSchemeResources(R.color.accent, R.color.primary);

        PrimaryDrawerItem home = new PrimaryDrawerItem().withIdentifier(HOME).withName(R.string.item_home).withIcon(R.drawable.ic_home).withIconTintingEnabled(true);
        PrimaryDrawerItem photos = new PrimaryDrawerItem().withIdentifier(PHOTOS).withName(R.string.photos).withIcon(R.drawable.ic_photos).withIconTintingEnabled(true);
        PrimaryDrawerItem schedule = new PrimaryDrawerItem().withIdentifier(SCHEDULE).withName(R.string.schedule).withIcon(R.drawable.ic_schedule).withIconTintingEnabled(true);
        PrimaryDrawerItem shuttles = new PrimaryDrawerItem().withIdentifier(SHUTTLES).withName(R.string.shuttles).withIcon(R.drawable.ic_bus).withIconTintingEnabled(true);
        PrimaryDrawerItem competitions = new PrimaryDrawerItem().withIdentifier(COMPETITIONS).withName(R.string.competitions).withIcon(R.drawable.ic_competition).withIconTintingEnabled(true);
        PrimaryDrawerItem sponsors = new PrimaryDrawerItem().withIdentifier(SPONSORS).withName(R.string.sponsors).withIcon(R.drawable.ic_sponsor).withIconTintingEnabled(true);
        PrimaryDrawerItem hotel = new PrimaryDrawerItem().withIdentifier(HOTEL).withName(R.string.hotel).withIcon(R.drawable.ic_hotel).withIconTintingEnabled(true);
        PrimaryDrawerItem banquet = new PrimaryDrawerItem().withIdentifier(BANQUET).withName(R.string.banquet).withIcon(R.drawable.ic_banquet).withIconTintingEnabled(true);
        PrimaryDrawerItem faq = new PrimaryDrawerItem().withIdentifier(FAQ).withName(R.string.faq).withIcon(R.drawable.ic_faq).withIconTintingEnabled(true);
        PrimaryDrawerItem vote = new PrimaryDrawerItem().withIdentifier(VOTE).withName(R.string.vote).withIcon(R.drawable.ic_vote).withIconTintingEnabled(true);
        PrimaryDrawerItem logout = new PrimaryDrawerItem().withIdentifier(LOGOUT).withName(R.string.sign_out).withIcon(R.drawable.ic_logout).withIconTintingEnabled(true);

        AccountHeader headerResult = new AccountHeaderBuilder()
                .withActivity(this)
                .withHeaderBackground(R.drawable.college)
                .withHeaderBackgroundScaleType(ImageView.ScaleType.CENTER_CROP)
                .addProfiles(
                        new ProfileDrawerItem()
                                .withName(user.getDisplayName())
                                .withEmail(user.getEmail())
                                .withIcon(ContextCompat.getDrawable(this, R.drawable.sac_logo))
                )
                .withSelectionListEnabledForSingleProfile(false)
                .build();

        drawer = new DrawerBuilder()
                .withAccountHeader(headerResult)
                .withActivity(this)
                .withToolbar(toolbar)
                .withActionBarDrawerToggle(true)
                .withCloseOnClick(true)
                .addDrawerItems(home,photos,vote,schedule,shuttles,competitions,sponsors,hotel,banquet,faq,logout)
                .withOnDrawerItemClickListener(new Drawer.OnDrawerItemClickListener() {
                    @Override
                    public boolean onItemClick(View view, int position, IDrawerItem drawerItem) {

                        switch((int)drawerItem.getIdentifier()) {
                            case HOME:
                                webView.loadUrl(URL);
                                return false;
                            case PHOTOS:
                                webView.loadUrl(URL+"&p=photos");
                                return false;
                            case SCHEDULE:
                                webView.loadUrl(URL+"&p=schedule");
                                return false;
                            case SHUTTLES:
                                webView.loadUrl(URL+"&p=shuttles");
                                return false;
                            case COMPETITIONS:
                                webView.loadUrl(URL+"&p=competitions");
                                return false;
                            case SPONSORS:
                                webView.loadUrl(URL+"&p=sponsors");
                                return false;
                            case HOTEL:
                                webView.loadUrl(URL+"&p=hotel");
                                return false;
                            case BANQUET:
                                webView.loadUrl(URL+"&p=banquet");
                                return false;
                            case FAQ:
                                webView.loadUrl(URL+"&p=faq");
                                return false;
                            case VOTE:
                                startActivity(new Intent(MainActivity.this,VoteActivity.class));
                                return false;
                            case LOGOUT:
                                AuthUI.getInstance()
                                        .signOut(MainActivity.this)
                                        .addOnCompleteListener(new OnCompleteListener<Void>() {
                                            public void onComplete(@NonNull Task<Void> task) {
                                                Utils.showToast(MainActivity.this, R.string.signed_out);
                                                finish();
                                            }
                                        });
                            default:
                                return false;
                        }
                    }
                })
                .build();

        ActionBar ab = getSupportActionBar();
        if(ab != null) {
            ab.setDisplayHomeAsUpEnabled(false);
            drawer.getActionBarDrawerToggle().setDrawerIndicatorEnabled(true);
        }

        loadWebView();
    }

    @SuppressLint("SetJavaScriptEnabled")
    private void loadWebView() {
        webView.getSettings().setJavaScriptEnabled(true);
        webView.setWebViewClient(new CustomWebViewClient());
        webView.loadUrl(URL);
    }

    private class CustomWebViewClient extends WebViewClient {

        @SuppressWarnings("deprecation")
        @Override
        public boolean shouldOverrideUrlLoading(WebView view, String url) {
            view.loadUrl(url);
            return super.shouldOverrideUrlLoading(view, url);
        }

        @TargetApi(Build.VERSION_CODES.N)
        @Override
        public boolean shouldOverrideUrlLoading(WebView view, WebResourceRequest request) {
            view.loadUrl(request.getUrl().toString());
            return super.shouldOverrideUrlLoading(view, request);
        }

        @Override
        public void onPageStarted(WebView view, String url, Bitmap favicon) {
            srl.post(new Runnable() {
                @Override
                public void run() {
                    srl.setRefreshing(true);
                }
            });
            super.onPageStarted(view, url, favicon);
        }

        @Override
        public void onPageFinished(WebView view, String url) {
            srl.setRefreshing(false);
        }
    }
}
