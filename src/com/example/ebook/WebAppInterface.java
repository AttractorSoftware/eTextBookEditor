package com.example.ebook;

import android.content.Context;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
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
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;

/**
 * Created by ashedrin on 8/18/14.
 */
public class    WebAppInterface {
    Context mContext;
    WebView webView;
    Player player;

    WebAppInterface(Context c, WebView w, Player p) {
        mContext = c;
        webView = w;
        player = p;
    }

    public void getStorageBookList() {
        webView.loadUrl("javascript:app.setStorageBooks(" + player.getBookList() + ")");
        webView.loadUrl("javascript:app.drawShelfs()");
    }


    public void getRepositoryBookList() throws IOException {
        HttpGet httpGet = new HttpGet(Config.getInstance().getParameter("repositoryUrl") + "/api/books");
        HttpParams httpParams = new BasicHttpParams();
        HttpConnectionParams.setConnectionTimeout(httpParams, 3000);
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
        System.out.print(bookSlug);
        webView.loadUrl("file:///sdcard/eTextBook/cache/" + bookSlug + "/index.html");
        webView.getSettings().setDomStorageEnabled(true);
    }

    public void downloadBook(String bookSlug) {
        try {
            URL url = new URL(Config.getInstance().getParameter("repositoryUrl") + "/books/"+bookSlug+".etb");
            HttpURLConnection urlConnection = (HttpURLConnection) url.openConnection();
            urlConnection.setRequestMethod("GET");
            urlConnection.connect();


            File SDCardRoot = Environment.getExternalStorageDirectory();
            //create a new file, specifying the path, and the filename
            //which we want to save the file as.
            File file = new File(SDCardRoot + "/eTextBook", bookSlug + ".etb");

            //this will be used to write the downloaded data into the file we created
            FileOutputStream fileOutput = new FileOutputStream(file);

            //this will be used in reading the data from the internet
            InputStream inputStream = urlConnection.getInputStream();

            //this is the total size of the file
            int totalSize = urlConnection.getContentLength();
            //variable to store total downloaded bytes
            int downloadedSize = 0;

            //create a buffer...
            byte[] buffer = new byte[1024];
            int bufferLength = 0; //used to store a temporary size of the buffer

            //now, read through the input buffer and write the contents to the file
            while ( (bufferLength = inputStream.read(buffer)) > 0 ) {
                //add the data in the buffer to the file in the file output stream (the file on the sd card
                fileOutput.write(buffer, 0, bufferLength);
                //add up the size so we know how much is downloaded
                downloadedSize += bufferLength;
                //this is where you would do something to report the prgress, like this maybe
                if(downloadedSize == totalSize) {
                    player.loadBooksFromArchive();
                    webView.loadUrl("javascript:app.setStorageBooks(" + player.getBookList() + ")");
                    webView.loadUrl("javascript:app.drawShelfs()");
                    webView.loadUrl("javascript:app.switchScreen('shelf')");
                }

            }
            //close the output stream when done
            fileOutput.close();

        //catch some possible errors...
        } catch (MalformedURLException e) {
            e.printStackTrace();
        } catch (IOException e) {
            e.printStackTrace();
        }
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
