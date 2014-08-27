package com.example.ebook;

import org.json.JSONException;
import org.json.JSONObject;

import java.io.*;

/**
 * Created by ashedrin on 5/23/14.
 */
public class ETextBook {

    private File sourcesFile;
    private File sourcesFolder;
    private String title = "default title";
    private String slug;

    public ETextBook(File file) {
        this.sourcesFile = file;
        String[] filePathParts = this.sourcesFile.getName().split("\\.");
        this.slug = filePathParts[0];
        this.sourcesFolder = new File(Config.getInstance().getParameter("cacheDir") + "/" + this.slug);
        ZipReader zipReader = new ZipReader(sourcesFile.getPath(), new File(Config.getInstance().getParameter("cacheDir") + "/" + this.slug));
        try {
            zipReader.extract();
        } catch(IOException e) {
            System.out.println("File not found");
        }
        this.loadBookInfo();
    }

    private void loadBookInfo() {
        File infoFile = new File(this.sourcesFolder.getPath() + "/book.info");
        try{
            BufferedReader reader = new BufferedReader(new InputStreamReader(new FileInputStream(infoFile)));
            StringBuilder stringBuilder = new StringBuilder();
            String readLine;
            while((readLine = reader.readLine()) != null) {
                stringBuilder.append(readLine + "\n");
            }
            try {
                JSONObject infoJson = new JSONObject(stringBuilder.toString());
                this.title = infoJson.getString("title");
            } catch(JSONException e) {
                System.out.println("Bad json info");
            }
        } catch(IOException e) { System.out.println("Configuration file not found!"); }
    }

    public String getTitle() {
        return title;
    }

    public String getSlug() {
        return slug;
    }

}

