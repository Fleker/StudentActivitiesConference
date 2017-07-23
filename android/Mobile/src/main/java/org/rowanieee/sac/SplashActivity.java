package org.rowanieee.sac;

import android.content.Intent;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;

import com.firebase.ui.auth.AuthUI;
import com.google.firebase.auth.FirebaseAuth;
public class SplashActivity extends AppCompatActivity {

    private static final int RC_SIGN_IN = 901;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_splash);
        FirebaseAuth auth = FirebaseAuth.getInstance();
        if (auth.getCurrentUser() != null) {
            startActivity(new Intent(this,MainActivity.class));
            finish();
        } else {
            startActivityForResult(
                    AuthUI.getInstance()
                            .createSignInIntentBuilder()
                            .setAllowNewEmailAccounts(false)
                            .setTheme(R.style.RowanTheme)
                            .setTosUrl("https://sac17.rowanieee.org/privacy-policy.html")
                            .build(),
                    RC_SIGN_IN);
        }

    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        // RC_SIGN_IN is the request code you passed into startActivityForResult(...) when starting the sign in flow
        if (requestCode == RC_SIGN_IN) {
            if (resultCode == RESULT_OK) {
                startActivity(new Intent(SplashActivity.this, MainActivity.class));
            }

            // Sign in canceled
            if (resultCode == RESULT_CANCELED) {
                Utils.showToast(this, R.string.sign_in_cancelled);
            }

            finish();
        }
    }
}
