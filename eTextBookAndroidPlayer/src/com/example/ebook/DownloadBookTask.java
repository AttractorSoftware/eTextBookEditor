package com.example.ebook;

import android.os.AsyncTask;
import android.os.Environment;
import android.webkit.WebView;

import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;

/**
 * Created by ashedrin on 11/4/14.
 */
public class DownloadBookTask extends AsyncTask<String, Integer, Long> {

    WebView webView;
    Player player;

    public void setPlayer(Player player) {
        this.player = player;
    }

    public void setWebView(WebView webView) {
        this.webView = webView;
    }

    protected Long doInBackground(String... bookSlugs) {

        String bookSlug = bookSlugs[0];
        URL url = null;

        try {
            url = new URL(Config.getInstance().getParameter("repositoryUrl") + "/publicBooks/"+bookSlug+".etb");
        } catch (MalformedURLException e) {
            e.printStackTrace();
        }

        HttpURLConnection urlConnection = null;

        try {
            urlConnection = (HttpURLConnection) url.openConnection();
            urlConnection.setRequestMethod("GET");
            urlConnection.connect();
            File SDCardRoot = Environment.getExternalStorageDirectory();
            File file = new File(SDCardRoot + "/eTextBook", bookSlug + ".etb");

            FileOutputStream fileOutput = new FileOutputStream(file);
            InputStream inputStream = urlConnection.getInputStream();

            int totalSize = urlConnection.getContentLength();

            byte[] buffer = new byte[1024];
            int bufferLength = 0;
            int downloadedSize = 0;
            int progress = 0;
            while ( (bufferLength = inputStream.read(buffer)) > 0 ) {
                fileOutput.write(buffer, 0, bufferLength);
                downloadedSize += bufferLength;
                progress = downloadedSize * 100 / totalSize;
                this.webView.loadUrl("javascript:app.updateUploadingProgress('" + progress + "')");
                if(downloadedSize == totalSize) {
                    player.loadBooksFromArchive();
                    webView.loadUrl("javascript:app.setStorageBooks(" + player.getBookList() + ")");
                    webView.loadUrl("javascript:app.drawShelfs()");
                    webView.loadUrl("javascript:app.switchScreen('shelf')");
                    webView.loadUrl("javascript:app.updateUploadingProgress('0')");
                    fileOutput.close();
                    inputStream.close();
                    webView.loadUrl("javascript:app.downloadComplete()");
                }
            }
        } catch (IOException e) { e.printStackTrace(); }
        return null;
    }

    @Override
    protected void onPreExecute() {

    }

    protected void onProgressUpdate(Integer progress) {

    }
}
