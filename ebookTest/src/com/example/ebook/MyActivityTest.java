package com.example.ebook;

import android.app.Activity;
import android.test.ActivityInstrumentationTestCase2;

/**
 * This is a simple framework for a test of an Application.  See
 * {@link android.test.ApplicationTestCase ApplicationTestCase} for more information on
 * how to write and extend Application tests.
 * <p/>
 * To run this test, you can type:
 * adb shell am instrument -w \
 * -e class com.example.ebook.MyActivityTest \
 * com.example.ebook.tests/android.test.InstrumentationTestRunner
 */
public class MyActivityTest extends ActivityInstrumentationTestCase2<BookListActivity> {

    Activity activity;

    public MyActivityTest() {
        super("com.example.ebook", BookListActivity.class);
    }

    @Override
    protected void setUp() throws Exception {
        super.setUp();
        activity = getActivity();


    }

    public void testActivity() {
        assertNotNull(activity);
    }

}
