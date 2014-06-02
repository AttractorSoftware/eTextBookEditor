package com.example.ebook;

import android.content.res.AssetManager;

import java.io.*;

/**
 * Created by ashedrin on 5/26/14.
 */
public class Config {

    private AssetManager assetManager;
    private static Config instance;

    private Config() {}

    public static Config getInstance() {
        if(instance == null) {
            instance = new Config();
        } return instance;
    }

    public String getParameter(String parameterName) {

        try{
            BufferedReader reader = new BufferedReader(new InputStreamReader(this.assetManager.open("config.cfg")));

            String readLine;
            while((readLine = reader.readLine()) != null) {
                String[] lineParts = readLine.split(":");
                if(lineParts[0].equals(parameterName)) {
                    return lineParts[1].replaceAll("\\s", "");
                }
            }

        } catch(IOException e) { System.out.println("Configuration file not found!"); }

        return "Param not found";
    }

    public void setAssetManager(AssetManager assetManager) {
        this.assetManager = assetManager;
    }
}
