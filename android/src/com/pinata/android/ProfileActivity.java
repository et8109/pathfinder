package com.pinata.android;

import java.util.Calendar;
import java.util.GregorianCalendar;
import java.util.Date;

import android.content.Intent;
import android.app.Activity;
import android.os.Bundle;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;
import android.view.View;
import android.view.Window;
import android.util.Log;

import com.pinata.android.client.*;
import com.pinata.android.client.http.*;
import com.pinata.shared.*;

/**
 * ProfileActivity UI Handling Routines.
 * @author Christian Gunderman
 */
public class ProfileActivity extends Activity {

    /** Authentication session. */
    private UserSession session;

    /**
     * Called by Android OS when activity is first started.
     */
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        // Remove title bar.
        this.requestWindowFeature(Window.FEATURE_NO_TITLE);

        // Load window XML layout.
        setContentView(R.layout.profile);

        // Unpack session, if there is one.
        try {
            this.session = UserSession.unbundleFromIntent(this.getIntent());
        } catch (ClientException ex) {
            Log.wtf("ProfileActivity",
                    "Malformed or not provided sessionHeader.");
            this.finish();
        }
    }

    /**
     * Logs out the current user and closes the window.
     */
    public void onLogoutButtonClicked(View view) {
        new AsyncDeleteSessionRequest().execute();
    }

    public void onCreateEventButtonClicked(View view){
        Intent launchActivityIntent
            = new Intent(ProfileActivity.this, CreateEventActivity.class);
        ProfileActivity.this.session.bundleWithIntent(launchActivityIntent);
        startActivity(launchActivityIntent);
    }

    /**
     * Defines an async DeleteSession operation that tells the server
     * to log out of the session and then updates the UI.
     * @author Christian Gunderman
     */
    private class AsyncDeleteSessionRequest extends AsyncClientOperation {

        /**
         * Async operation setup routine. This routine is run on the UI thread
         * before the async operation is run. It grabs all of the necessary info
         * the async operation needs from the UI controls and saves it in this
         * object because the async operation thread cannot access UI objects.
         */
        @Override
        protected void uiThreadBefore() {
            // TODO: start wait cursor animation.
        }

        /**
         * Async operation routine. This routine is run by a background worker
         * thread, allowing the UI thread to go undisturbed while HTTP requests
         * are being sent. This method cannot access UI elements, so that is
         * done in uiThreadBefore() and cached in this object. Any
         * ClientExceptions thrown from this method are automatically grabbed
         * and unwrapped and dispatched to the uiThreadAfterFailure() method
         * back on the UI thread.
         * This implementation performs a CreateUser request via the User
         * object.
         * @param client A pre-instantiated HttpClient object.
         */
        @Override
        protected void backgroundThreadOperation(HttpClient client)
            throws ClientException {

            // Let client know that we are logged in.
            client.setUserSession(session);

            // Log us out.
            session.end(client);

            // Close window.
            finish();
        }

        /**
         * This method is run on the UI thread if this object's cancel method
         * is called. It allows for the update of UI elements to inform the user
         * that the requested operation has ceased.
         */
        @Override
        protected void uiThreadOperationCancelled() {

        }

        /**
         * After the backgroundThreadOperation() function finishes executing,
         * if no exceptions occur, this method is called to signal that the
         * process is assumed to be a success. To signal that the background
         * process was a failure, backgroundThreadOperation() must throw a
         * ClientException.
         */
        @Override
        protected void uiThreadAfterSuccess() {

        }

        /**
         * The background operation failure handler method. This method is
         * called automatically if a ClientException was thrown from the
         * backgroundThreadOperation() function. Message contains the
         * ClientException message, and the ApiException message too,
         * if the ClientException has an inner ApiException (exception
         * passed from the server).
         * @param message The UI friendly error message from the client
         * level AND/OR passed back from the server.
         * @param clientStatus A ClientStatus enum relating the current
         * status of the application.
         * @param apiStatus The server's status reply. This value is null
         * unless clientStatus == ClientStatus.API_ERROR.
         */
        @Override
        protected void uiThreadAfterFailure(String message,
                                            ClientStatus clientStatus,
                                            ApiStatus apiStatus) {

            // Check to see if we were already logged out server side.
            if (clientStatus == ClientStatus.API_ERROR &&
                apiStatus == ApiStatus.ACCESS_DENIED) {

                // Close window.
                finish();
            }

            // Display error message.
            Toast.makeText(ProfileActivity.this,
                           message,
                           Toast.LENGTH_SHORT).show();
        }
    }
}
