package com.example.ebook;

import android.app.Activity;
import android.content.res.AssetManager;
import android.os.Bundle;
import android.view.KeyEvent;
import android.webkit.WebChromeClient;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;

import java.io.*;


public class MyActivity extends Activity {

    WebView webView;
    Player player;

    /**
     * Called when the activity is first created.
     */
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.main);

        getWindow().addFlags(android.view.WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON);

        webView = (WebView) findViewById(R.id.webview);

        WebSettings webSettings = webView.getSettings();
        webSettings.setJavaScriptEnabled(true);
        webSettings.setPluginState(WebSettings.PluginState.ON_DEMAND);
        webSettings.setUseWideViewPort(true);
        webSettings.setSupportZoom(true);
        webSettings.setBuiltInZoomControls(true);
        webSettings.setDomStorageEnabled(true);
        webSettings.setDisplayZoomControls(false);
        webSettings.setAllowUniversalAccessFromFileURLs(true);

        webView.setWebChromeClient(new WebChromeClient());
        webView.setWebViewClient(new WebViewClient());

        Config.getInstance().setAssetManager(getAssets());

        try {
            this.copyDefaultBooks();
        } catch (IOException e) {
            System.out.println("Default books not found");
        }

        this.player = new Player();

        webView.addJavascriptInterface(new WebAppInterface(this, webView, player), "Android");

        webView.loadUrl("file:///android_asset/app/app.html");

    }

    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        if ((keyCode == KeyEvent.KEYCODE_BACK) && webView.canGoBack()) {
            webView.goBack();
            return true;
        }
        return super.onKeyDown(keyCode, event);
    }

    public void copyDefaultBooks() throws IOException {
        AssetManager assetManager = getAssets();
        String[] bookList = assetManager.list("books");

        String archivePath = Config.getInstance().getParameter("archiveDir");
        File archiveFolder = new File(archivePath);
        if (!archiveFolder.exists()) {
            archiveFolder.mkdirs();
        }

        for (String book : bookList) {
            this.copy(assetManager.open("books/" + book), archivePath + "/" + book);
        }
    }

    public void copy(InputStream source, String dest) throws IOException {
        InputStream input = null;
        OutputStream output = null;
        try {
            input = source;
            output = new FileOutputStream(dest);
            byte[] buf = new byte[1024];
            int bytesRead;
            while ((bytesRead = input.read(buf)) > 0) {
                output.write(buf, 0, bytesRead);
            }
        } catch (IOException e) {
            System.out.println(e.getMessage());
        }
    }

}