package com.example.ebook;

import java.io.File;
import java.util.ArrayList;

/**
 * Created by ashedrin on 5/23/14.
 */
public class Player {

    private ArrayList<ETextBook> bookList;
    private File archiveDir;

    public Player() {
        this.archiveDir = new File(Config.getInstance().getParameter("archiveDir"));
        if(!this.archiveDir.exists()) { this.archiveDir.mkdirs(); }
        this.loadBooksFromArchive();
    }

    public String getBookList() {
        String result = "";
        int pos = 0;
        for(ETextBook book: this.bookList) {
            String sep = ",";
            if(pos == this.bookList.size()) { sep = ""; }
            result += "{ slug: '" + book.getSlug() + "', title: '" + book.getTitle() + "', source: '" + book.getSource() + "'}" + sep;
            pos++;
        }
        return "[" + result + "]";
    }

    public void loadBooksFromArchive() {
        String[] filesPath = archiveDir.list();
        ArrayList<ETextBook> eList = new ArrayList<ETextBook>();
        for(String filePath: filesPath) {
            File file = new File(archiveDir.getPath() + "/" + filePath);
            if(!file.isDirectory()) {
                ETextBook book = new ETextBook(file);
                eList.add(book);
            }
        }
        this.bookList = eList;
    }

    public ETextBook getBookByPosition(int position) {
        return this.bookList.get(position);
    }

}
