package org.rowanieee.sac;

import android.content.Intent;
import android.provider.ContactsContract;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;

import com.squareup.picasso.Picasso;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.HashSet;
import java.util.List;

import butterknife.BindView;
import butterknife.ButterKnife;

class NearbyAdapter extends RecyclerView.Adapter<NearbyAdapter.NearbyVH> {

    private HashSet<JSONObject> nearbyItemList;

    NearbyAdapter(HashSet<JSONObject> nearbyPeople) {
        nearbyItemList = nearbyPeople;
    }

    @Override
    public NearbyVH onCreateViewHolder(ViewGroup parent, int viewType) {
        final View view = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.item_nearby, parent, false);
        return new NearbyVH(view);
    }

    @Override
    public void onBindViewHolder(final NearbyVH holder, int position) {
        JSONObject userJson = nearbyItemList.toArray(new JSONObject[nearbyItemList.size()])[position];
        try {
            if(userJson.has("snapchat")) {
                holder.etSnapchat.setText(userJson.get("snapchat").toString());
            }

            if(userJson.has("instagram")) {
                holder.etInstagram.setText(userJson.get("instagram").toString());
            }

            if(userJson.has("twitter")) {
                holder.etTwitter.setText(userJson.get("twitter").toString());
            }

            if(userJson.has("collabratec")) {
                holder.etCollabratec.setText(userJson.get("collabratec").toString());
            }

            if(userJson.has("phone")) {
                holder.etPhone.setText(userJson.get("phone").toString());
            }

            if(userJson.has("name")) {
                holder.name.setText(userJson.get("name").toString());
            }

            if(userJson.has("email")) {
                holder.email.setText(userJson.get("email").toString());
            }

            if(userJson.has("profile_image_url")) {
                String imageUrl = userJson.get("profile_image_url").toString();
                if(!imageUrl.equals("") || !imageUrl.isEmpty()) {
                    Picasso.with(holder.profileImage.getContext()).load(imageUrl).into(holder.profileImage);
                }
            }
        } catch (JSONException e) {
            e.printStackTrace();
        }

        holder.view.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                String notes = "Instagram: " + holder.etInstagram.getText() + "\n"
                        + "Snapchat: " + holder.etSnapchat.getText() + "\n"
                        + "Twitter: " + holder.etTwitter.getText() + "\n"
                        + "Collabratec: " + holder.etCollabratec.getText() + "\n";

                Intent intent = new Intent(ContactsContract.Intents.Insert.ACTION);
                intent.setType(ContactsContract.RawContacts.CONTENT_TYPE);
                intent.putExtra(ContactsContract.Intents.Insert.EMAIL, holder.email.getText())
                    .putExtra(ContactsContract.Intents.Insert.EMAIL_TYPE, ContactsContract.CommonDataKinds.Email.TYPE_HOME)
                    .putExtra(ContactsContract.Intents.Insert.PHONE, holder.etPhone.getText())
                    .putExtra(ContactsContract.Intents.Insert.PHONE_TYPE, ContactsContract.CommonDataKinds.Phone.TYPE_MOBILE)
                    .putExtra(ContactsContract.Intents.Insert.NOTES, notes)
                    .putExtra(ContactsContract.Intents.Insert.NAME, holder.name.getText());
                holder.view.getContext().startActivity(intent);
            }
        });
    }

    @Override
    public int getItemCount() {
        return nearbyItemList.size();
    }

    class NearbyVH extends RecyclerView.ViewHolder {

        @BindView(R.id.snapchat)
        TextView etSnapchat;

        @BindView(R.id.twitter)
        TextView etTwitter;

        @BindView(R.id.instagram)
        TextView etInstagram;

        @BindView(R.id.collabratec)
        TextView etCollabratec;

        @BindView(R.id.phone)
        TextView etPhone;

        @BindView(R.id.profileImage)
        ImageView profileImage;

        @BindView(R.id.name)
        TextView name;

        @BindView(R.id.email)
        TextView email;

        View view;

        NearbyVH(View itemView) {
            super(itemView);
            ButterKnife.bind(this, itemView);
            view = itemView;
        }
    }
}
