package com.example.ebook;

import android.app.ListActivity;
import android.content.Intent;
import android.content.res.AssetManager;
import android.os.Bundle;
import android.view.View;
import android.widget.ArrayAdapter;
import android.widget.ListView;

import java.io.*;

/**
 * Created by ashedrin on 5/26/14.
 */
public class BookListActivity extends ListActivity {

    private Player player;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        Config.getInstance().setAssetManager(getAssets());

        try {
            this.copyDefaultBooks();
        } catch(IOException e) {
            System.out.println("Default books not found");
        }

        this.player = new Player();

        String[] bookList = this.player.getBookList();

        ArrayAdapter<String> adapter = new ArrayAdapter<String>(this, R.layout.row_layout, R.id.label, this.player.getBookList());
        setListAdapter(adapter);
    }

    @Override
    protected void onListItemClick(ListView l, View v, int position, long id) {
        Intent intent = new Intent(BookListActivity.this, MyActivity.class);
        intent.putExtra("viewBook", this.player.getBookByPosition(position).getSlug());
        startActivity(intent);
    }

    public void copyDefaultBooks() throws IOException {
        AssetManager assetManager = getAssets();
        String[] bookList = assetManager.list("books");

        String archivePath = Config.getInstance().getParameter("archiveDir");
        File archiveFolder = new File(archivePath);
        if(!archiveFolder.exists()){ archiveFolder.mkdirs(); }

        for(String book: bookList) {
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
        } catch(IOException e) {
            System.out.println(e.getMessage());
        }
    }

}
