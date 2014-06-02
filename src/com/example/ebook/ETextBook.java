package com.example.ebook;

import java.io.*;

/**
 * Created by ashedrin on 5/23/14.
 */
public class ETextBook {

    private File sourcesFile;
    private File sourcesFolder;
    private String title;
    private String slug;

    public ETextBook(File file) {
        this.sourcesFile = file;
        ZipReader zipReader = new ZipReader(sourcesFile.getPath(), new File(Config.getInstance().getParameter("cacheDir")));
        try {
            sourcesFolder = new File(zipReader.extract());
            String sourcesFolderPath = sourcesFolder.getPath();
            String[] sourcesFolderPathParts = sourcesFolderPath.split("/");
            slug = sourcesFolderPathParts[sourcesFolderPathParts.length-1];
        } catch(IOException e) {
            System.out.println("File not found");
        }
        this.loadBookInfo();
    }

    private void loadBookInfo() {
        File infoFile = this.getInfoFile();
        try{
            BufferedReader reader = new BufferedReader(new InputStreamReader(new FileInputStream(infoFile)));

            String readLine;
            while((readLine = reader.readLine()) != null) {
                String[] lineParts = readLine.split("=\\+=");
                String propertyName = lineParts[0].replaceAll("\\s", "");
                if(propertyName.equals("title")) { this.title =  lineParts[1]; }
            }

        } catch(IOException e) { System.out.println("Configuration file not found!"); }
    }

    public File getInfoFile() {
        FilenameFilter bookInfoFilter = new FilenameFilter() {
            @Override
            public boolean accept(File file, String s) {
                if(s.endsWith(".info")) {
                    return true;
                } else { return false; }
            }
        };

        File[] bookInfoFiles = sourcesFolder.listFiles(bookInfoFilter);
        try {
            return bookInfoFiles[0];
        } catch(Exception e) {
            System.out.println("Info file is missing");
        } return null;
    }

    public String getTitle() {
        return title;
    }

    public String getSlug() {
        return slug;
    }

}

