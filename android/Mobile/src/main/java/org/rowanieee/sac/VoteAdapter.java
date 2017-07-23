package org.rowanieee.sac;

import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;

import com.google.firebase.database.DatabaseError;
import com.google.firebase.database.DatabaseReference;
import com.squareup.picasso.Picasso;

import java.util.Collections;
import java.util.List;

import butterknife.BindView;
import butterknife.ButterKnife;

class VoteAdapter extends RecyclerView.Adapter<VoteAdapter.VoteVH> {

    private List<VoteItem> voteItems;
    private String uid;
    private boolean TSHIRT = false;
    private CloseCallback cb;

    VoteAdapter(List<VoteItem> voteItems, String uid, boolean tshirt, CloseCallback cb) {
        this.voteItems = voteItems;
        // Random sort to reduce cognitive bias
        Collections.shuffle(this.voteItems);
        this.uid = uid;
        this.TSHIRT = tshirt;
        this.cb = cb;
    }

    @Override
    public VoteVH onCreateViewHolder(ViewGroup parent, int viewType) {
        final View view = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.item_vote, parent, false);
        return new VoteVH(view);
    }

    @Override
    public void onBindViewHolder(final VoteVH holder, int position) {
        final VoteItem v = voteItems.get(position);
        String imageUrl = v.getDownloadUrl();

        if(imageUrl != null && !imageUrl.equals("") && !imageUrl.isEmpty()) {
            Picasso.with(holder.imageView.getContext()).load(imageUrl).fit().centerInside().into(holder.imageView);
        } else {
            holder.imageView.setImageResource(R.drawable.sac_logo);
        }

        if (TSHIRT) {
            holder.title.setVisibility(View.GONE);
            holder.abstrac.setVisibility(View.GONE);
        } else {
            holder.title.setText(v.getTitle());
            holder.abstrac.setText(v.getAbstract());
        }
        final String vote_node = TSHIRT ? "vote_tshirt" : "vote_project";
        holder.imageView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(final View view) {
                Utils.getFirebaseParentRef().child("attendees").child(uid).child(vote_node)
                    .setValue(v.getKey(), new DatabaseReference.CompletionListener() {
                        @Override
                        public void onComplete(DatabaseError databaseError, DatabaseReference databaseReference) {
                            if(databaseError == null) {
                                Utils.showToast(view.getContext(),"Voted!");
                                cb.closeCallback();
                            }
                        }
                    }
                );
            }
        });
    }

    @Override
    public int getItemCount() {
        return voteItems.size();
    }

    class VoteVH extends RecyclerView.ViewHolder {

        @BindView(R.id.imageView)
        ImageView imageView;

        @BindView(R.id.tvTitle)
        TextView title;

        @BindView(R.id.tvAbstract)
        TextView abstrac;

        View view;

        VoteVH(View itemView) {
            super(itemView);
            ButterKnife.bind(this, itemView);
            view = itemView;
        }
    }

    interface CloseCallback {
        void closeCallback();
    }
}