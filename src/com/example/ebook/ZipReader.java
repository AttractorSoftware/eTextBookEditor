package com.example.ebook;

import java.io.*;
import java.util.zip.ZipEntry;
import java.util.zip.ZipInputStream;

/**
 * Created by ashedrin on 5/23/14.
 */
public class ZipReader {

    String filePath;
    Integer bufferSize = 1024;
    byte[] buffer = new byte[bufferSize];
    File file;
    File cacheDir;

    public ZipReader(String path, File cache) {
        this.filePath = path;
        this.file = new File(path);
        this.cacheDir = cache;
    }

    public void extract() throws IOException {
        InputStream fileStream = this.getFileStream();
        ZipInputStream fileZipStream = this.getZipStream(fileStream);

        ZipEntry entry = null;

        boolean isRootFolder = true;
        String rootFolderPath = "";

        while((entry = fileZipStream.getNextEntry()) != null) {

            String entryPath = this.cacheDir.getPath() + "/" + entry.getName();

            File entryFile = new File(entryPath);
            if(entry.isDirectory()) {
                if(entryFile.isDirectory()) {
                    entryFile.mkdirs();
                }
            } else {
                File parentDir = entryFile.getParentFile();
                if(parentDir != null && !parentDir.isDirectory()) {
                    parentDir.mkdirs();
                }

                FileOutputStream fileOut = new FileOutputStream(entryFile, false);
                BufferedOutputStream bufferOut = new BufferedOutputStream(fileOut, this.bufferSize);
                int size;

                try {
                    while ( (size = fileZipStream.read(this.buffer, 0, this.bufferSize)) != -1 ) {
                        fileOut.write(buffer, 0, size);
                    }

                    fileZipStream.closeEntry();
                }
                finally {
                    fileOut.flush();
                    fileOut.close();
                }

            }
        }
    }

    public ZipInputStream getZipStream(InputStream fileStream) {
        try {
            return new ZipInputStream(new BufferedInputStream(fileStream, this.bufferSize));
        } catch (Exception e) {
            System.out.println(e);
            return null;
        }
    }

    public InputStream getFileStream() {
        try {
            return new FileInputStream(this.filePath);
        } catch (FileNotFoundException e) {
            System.out.println("InputStream exception: file not found");
            return null;
        }
    }

}
