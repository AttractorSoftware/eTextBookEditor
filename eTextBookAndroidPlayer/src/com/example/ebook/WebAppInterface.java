package com.example.ebook;

import android.content.Context;
import android.content.Intent;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.net.Uri;
import android.os.Environment;
import android.webkit.WebView;
import org.apache.http.HttpConnection;
import org.apache.http.HttpResponse;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.params.BasicHttpParams;
import org.apache.http.params.HttpConnectionParams;
import org.apache.http.params.HttpParams;
import org.apache.http.util.EntityUtils;

import java.io.*;
import java.lang.reflect.Method;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;

public class    WebAppInterface {
    Context mContext;
    WebView webView;
    Player player;
    String currentSearchWord;
    int currentSearchStep = 1;

    WebAppInterface(Context c, WebView w, Player p) {
        mContext = c;
        webView = w;
        player = p;

        webView.setFindListener(new WebView.FindListener() {
            @Override
            public void onFindResultReceived(int i, int i2, boolean b) {
                currentSearchStep = i;
            }
        });
    }

    public void getStorageBookList() {
        webView.loadUrl("javascript:app.setStorageBooks(" + player.getBookList() + ")");
        webView.loadUrl("javascript:app.drawShelfs()");
    }

    public void search(String findString) {
        this.currentSearchWord = findString;
        this.webView.findAllAsync(findString);
        try {
            for (Method m : WebView.class.getDeclaredMethods()) {
                if (m.getName().equals("setFindIsUp")) {
                    m.setAccessible(true);
                    m.invoke(this.webView, true);
                    break;
                }
            }
        } catch (Throwable ignored) {}
    }

    public void searchNext() {
        currentSearchStep++;
        for(int i = 0; i < currentSearchStep; i++) {
            this.webView.findNext(true);
        }
    }

    public void searchPrev() {
        this.webView.findNext(false);
        currentSearchStep--;
        for(int i = 0; i < currentSearchStep; i++) {
            this.webView.findNext(true);
        }
    }

    public String getRepositoryUrl() {
        return Config.getInstance().getParameter("repositoryUrl");
    }

    public void searchClear() {
        this.webView.clearMatches();
    }

    public void getRepositoryBookList() throws IOException {
        HttpGet httpGet = new HttpGet(Config.getInstance().getParameter("repositoryUrl") + "/api/books");
        HttpParams httpParams = new BasicHttpParams();
        HttpConnectionParams.setConnectionTimeout(httpParams, 10000);
        HttpConnectionParams.setSoTimeout(httpParams, 5000);
        HttpClient httpClient = new DefaultHttpClient(httpParams);

        if(this.isConnected()) {
            try {
                HttpResponse httpResponse = httpClient.execute(httpGet);
                String response = EntityUtils.toString(httpResponse.getEntity());
                webView.loadUrl("javascript:app.setRemoteBooks(JSON.parse('" + response + "').books)");
            } catch(ClientProtocolException e) {} catch(IOException e) {}
        } webView.loadUrl("javascript:app.drawShelfs()");
    }

    public void readBook(String bookSlug) {
        webView.loadUrl("file:///sdcard/eTextBook/cache/" + bookSlug + "/index.html");
        webView.getSettings().setDomStorageEnabled(true);
    }

    public void readSource(String bookSlug, String sourceSlug) {
        Uri path = Uri.fromFile(new File("sdcard/eTextBook/cache/" + bookSlug + "/" + sourceSlug));
        Intent intent = new Intent(Intent.ACTION_VIEW);
        intent.setDataAndType(path, "application/pdf");
        intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
        mContext.startActivity(intent);
    }

    public void downloadBook(String bookSlug) throws MalformedURLException {
        DownloadBookTask downloadTask = new DownloadBookTask();
        downloadTask.setWebView(webView);
        downloadTask.setPlayer(player);
        downloadTask.execute(bookSlug);
    }

    public boolean removeOldBook(String bookSlug) {
        File bookFile = new File("sdcard/eTextBook/" + bookSlug + ".etb");
        boolean deleted = bookFile.delete();
        return deleted;
    }

    public boolean isConnected() {
        boolean connected = false;

        ConnectivityManager cm =
                (ConnectivityManager) mContext.getSystemService(Context.CONNECTIVITY_SERVICE);

        if (cm != null) {
            NetworkInfo[] netInfo = cm.getAllNetworkInfo();

            for (NetworkInfo ni : netInfo) {
                if ((ni.getTypeName().equalsIgnoreCase("WIFI")
                        || ni.getTypeName().equalsIgnoreCase("MOBILE"))
                        && ni.isConnected() && ni.isAvailable()) {
                    connected = true;
                }

            }
        }

        return connected;
    }

}
