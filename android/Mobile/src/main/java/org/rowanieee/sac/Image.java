package org.rowanieee.sac;

import com.google.firebase.database.Exclude;
import com.google.firebase.database.ServerValue;

import java.util.HashMap;
import java.util.Map;

class Image {

    private String uid;
    private String path;
    private String caption;
    private boolean approved;
    private Map<String, String> timestamp = ServerValue.TIMESTAMP;

    Image() {}

    Image(String uid, String caption, String path) {
        this.caption = caption;
        this.path = path;
        this.approved = false;
        this.uid = uid;
    }

    public String getCaption() {
        return caption;
    }

    public void setCaption(String caption) {
        this.caption = caption;
    }

    public boolean isApproved() {
        return approved;
    }

    public void setApproved(boolean approved) {
        this.approved = approved;
    }

    public String getUid() {
        return uid;
    }

    public void setUid(String uid) {
        this.uid = uid;
    }

    public Map<String, String> getTimestamp(){
        return timestamp;
    }

    public void setTimestamp(long timestamp) {
        Map<String,String> temp = new HashMap<>();
        temp.put("timestamp",String.valueOf(timestamp));
        this.timestamp = temp;
    }

    @Exclude
    public long getTimestampCreatedLong(){
        return Long.valueOf(timestamp.get("timestamp"));
    }

    public String getPath() {
        return path;
    }

    public void setPath(String path) {
        this.path = path;
    }
}
