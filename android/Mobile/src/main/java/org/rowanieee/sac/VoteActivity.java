package org.rowanieee.sac;

import android.content.Intent;
import android.support.annotation.NonNull;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.widget.Button;
import android.widget.TextView;

import com.afollestad.materialdialogs.MaterialDialog;
import com.firebase.ui.auth.AuthUI;
import com.google.android.gms.tasks.OnCompleteListener;
import com.google.android.gms.tasks.Task;
import com.google.firebase.auth.FirebaseAuth;
import com.google.firebase.auth.FirebaseUser;
import com.google.firebase.database.DataSnapshot;
import com.google.firebase.database.DatabaseError;
import com.google.firebase.database.ValueEventListener;

import java.util.ArrayList;
import java.util.List;

import butterknife.BindView;
import butterknife.ButterKnife;
import butterknife.OnClick;

public class VoteActivity extends AppCompatActivity {

    String uid;
    MaterialDialog voteDialog;
    MaterialDialog loadingDialog;

    @BindView(R.id.btVoteProjects)
    Button btProjects;

    @BindView(R.id.btVoteTshirt)
    Button btTshirt;

    @BindView(R.id.tvCheckVoting)
    TextView checkVoting;

    @OnClick(R.id.btVoteTshirt)
    void onTshirtClick() {
        loadingDialog.show();
        Utils.getFirebaseParentRef().child("tshirts").addListenerForSingleValueEvent(new ValueEventListener() {
            @Override
            public void onDataChange(DataSnapshot dataSnapshot) {
                List<VoteItem> tshirts = new ArrayList<>();
                for(DataSnapshot ds : dataSnapshot.getChildren()) {
                    VoteItem v = ds.getValue(VoteItem.class);
                    v.setKey(ds.getKey());
                    tshirts.add(v);
                }

                uid = getUid();

                VoteAdapter.CloseCallback cb = new VoteAdapter.CloseCallback() {
                    @Override
                    public void closeCallback() {
                        voteDialog.hide();
                    }
                };

                VoteAdapter voteAdapter = new VoteAdapter(tshirts, uid, true, cb);

                loadingDialog.hide();

                voteDialog = new MaterialDialog.Builder(VoteActivity.this)
                        .title(R.string.vote_tshirt)
                        .adapter(voteAdapter, null)
                        .show();

            }

            @Override
            public void onCancelled(DatabaseError databaseError) {

            }
        });
    }

    @OnClick(R.id.btVoteProjects)
    void onProjectClick() {
        loadingDialog.show();
        Utils.getFirebaseParentRef().child("projects").addListenerForSingleValueEvent(new ValueEventListener() {
            @Override
            public void onDataChange(DataSnapshot dataSnapshot) {
                List<VoteItem> projects = new ArrayList<>();
                for(DataSnapshot ds : dataSnapshot.getChildren()) {
                    VoteItem v = ds.getValue(VoteItem.class);
                    v.setAbstract(ds.child("abstract").getValue().toString());
                    v.setKey(ds.getKey());
                    projects.add(v);
                }

                uid = getUid();

                VoteAdapter.CloseCallback cb = new VoteAdapter.CloseCallback() {
                    @Override
                    public void closeCallback() {
                        voteDialog.hide();
                    }
                };

                VoteAdapter voteAdapter = new VoteAdapter(projects, uid, false, cb);

                loadingDialog.hide();

                voteDialog = new MaterialDialog.Builder(VoteActivity.this)
                        .title(R.string.vote_project)
                        .adapter(voteAdapter, null)
                        .show();
            }

            @Override
            public void onCancelled(DatabaseError databaseError) {

            }
        });
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_vote);
        ButterKnife.bind(this);

        loadingDialog = new MaterialDialog.Builder(this)
                .title(R.string.loading_voting)
                .content(R.string.please_wait)
                .progress(true, 0)
                .build();
    }

    @Override
    protected void onResume() {
        super.onResume();
        Utils.getFirebaseParentRef().child("flags").child("allow_voting").addValueEventListener(new ValueEventListener() {
            @Override
            public void onDataChange(DataSnapshot dataSnapshot) {
                boolean allowVoting = (boolean)dataSnapshot.getValue();
                enableVoting(allowVoting);
        }

        @Override
        public void onCancelled(DatabaseError databaseError) {

        }
    });
}

    private void enableVoting(boolean allowVoting) {
        if(!allowVoting) {
            checkVoting.setText("Voting is disabled.");
        } else {
            checkVoting.setText("Voting is enabled.");
        }

        btProjects.setEnabled(allowVoting);
        btTshirt.setEnabled(allowVoting);
    }


    private String getUid() {
        FirebaseUser u = FirebaseAuth.getInstance().getCurrentUser();
        if(u == null) {
            Utils.showToast(this, "An error has occurred.");
            AuthUI.getInstance()
                    .signOut(this)
                    .addOnCompleteListener(new OnCompleteListener<Void>() {
                        public void onComplete(@NonNull Task<Void> task) {
                            // user is now signed out
                            startActivity(new Intent(VoteActivity.this, SplashActivity.class));
                            finish();
                        }
                    });
            return null;
        } else {
            return u.getUid();
        }
    }
}
