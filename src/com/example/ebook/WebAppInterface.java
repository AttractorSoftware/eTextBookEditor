package com.example.ebook;

import android.content.Context;
import android.webkit.WebView;
import android.widget.Toast;

/**
 * Created by ashedrin on 8/18/14.
 */
public class WebAppInterface {
    Context mContext;
    WebView webView;
    Player player;

    WebAppInterface(Context c, WebView w, Player p) {
        mContext = c;
        webView = w;
        player = p;
    }

    public void getStorageBookList() {
        System.out.println(player.getBookList());
        webView.loadUrl("javascript:app.setStorageBooks(" + player.getBookList() + ")");
        webView.loadUrl("javascript:app.drawShelfs()");
    }

    public void readBook(String bookSlug) {
        webView.loadUrl("file:///sdcard/eTextBook/cache/" + bookSlug + "/index.html");
    }

}
