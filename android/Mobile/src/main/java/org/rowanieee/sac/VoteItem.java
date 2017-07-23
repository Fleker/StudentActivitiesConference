package org.rowanieee.sac;

import com.google.firebase.database.Exclude;
import com.google.firebase.database.IgnoreExtraProperties;


@IgnoreExtraProperties
public class VoteItem {

    @Exclude
    String key;

    private String downloadUrl;
    private String title;
    private String abstrac;

    public String getKey() {
        return key;
    }

    public void setKey(String key) {
        this.key = key;
    }

    public String getDownloadUrl() {
        return downloadUrl;
    }

    public void setDownloadUrl(String downloadUrl) {
        this.downloadUrl = downloadUrl;
    }

    public String getTitle() {
        return title;
    }

    public void setTitle(String title) {
        this.title = title;
    }

    public String getAbstract() {
        return abstrac;
    }

    public void setAbstract(String abstrac) {
        this.abstrac = abstrac;
    }

}
