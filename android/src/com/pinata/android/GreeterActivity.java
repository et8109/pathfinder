package com.pinata.android;

import android.app.Activity;
import android.os.Bundle;

import android.content.Intent;
import android.view.View;
import android.view.Window;
import android.widget.Button;

import com.pinata.android.client.*;
import com.pinata.android.client.http.*;
import com.pinata.shared.*;

/**
 * Main Application welcome screen. Choose whether to sign up or signin.
 * @author Christian Gunderman
 */
public class GreeterActivity extends Activity {

    private Button createUserButton;

    /**
     * Called by Android OS when activity is first started.
     */
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        // Remove title bar.
        this.requestWindowFeature(Window.FEATURE_NO_TITLE);

        // Load window XML layout.
        setContentView(R.layout.greeter);

        this.createUserButton
            = (Button)findViewById(R.id.greeter_create_user_button);
    }

    /**
     * Called when the Sign up button is clicked.
     * @param The button that was clicked.
     */
    public void onCreateUserButtonClicked(View view) {
        Intent launchActivityIntent
            = new Intent(this, CreateUserActivity.class);

        startActivity(launchActivityIntent);
    }

    /**
     * Called when the Log in button is clicked.
     * @param view The button that was clicked.
     */
    public void onLoginButtonClicked(View view) {
        Intent launchActivityIntent
            = new Intent(this, LoginActivity.class);

        startActivity(launchActivityIntent);
    }
}
