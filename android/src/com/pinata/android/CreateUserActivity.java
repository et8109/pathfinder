package com.pinata.android;

import java.util.Calendar;
import java.util.GregorianCalendar;
import java.util.Date;

import android.app.Activity;
import android.os.Bundle;
import android.content.Intent;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;
import android.view.View;
import android.view.Window;

import com.pinata.android.client.*;
import com.pinata.android.client.http.*;
import com.pinata.shared.*;

/**
 * CreateUserActivity UI Handling Routines.
 * @author Christian Gunderman
 */
public class CreateUserActivity extends Activity {
    /** The username EditText. */
    private EditText usernameEditText;
    /** The password EditText. */
    private EditText passwordEditText;
    /** The gender radiobutton group. */
    private RadioGroup genderRadioGroup;
    /** The birthday DatePicker control. */
    private DatePicker birthdayDatePicker;
    /** The email EditText*/
    private EditText emailEditText;
    /** The Create button. */
    private Button submitButton;


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
        setContentView(R.layout.create_user);

        // Find view buttons and save references to convenient fields.
        this.usernameEditText
            = (EditText)this.findViewById(R.id.create_user_username_edittext);
        this.passwordEditText
            = (EditText)this.findViewById(R.id.create_user_password_edittext);
        this.genderRadioGroup
            = (RadioGroup)this.findViewById(R.id.create_user_gender_radiogroup);
        this.birthdayDatePicker
            = (DatePicker)this.findViewById(R.id.create_user_birthday_datepicker);
         this.emailEditText
            = (EditText)this.findViewById(R.id.create_user_email_edittext);
        this.submitButton
            = (Button)this.findViewById(R.id.create_user_submit_button);
    }

    /**
     * Called when the submit button is clicked. Makes async server call to
     * create a new user.
     * @param The view that was clicked, the submit button.
     */
    public void onSubmitButtonClicked(View view) {
        new AsyncCreateUserRequest().execute();
    }

    /**
     * Defines an async CreateUser operation that grabs data from the UI
     * widgets, tells the server to make a new user, and then gives UI
     * feedback.
     * @author Christian Gunderman
     */
    private class AsyncCreateUserRequest extends AsyncClientOperation {

        /** The username of the user to create. */
        private String username;
        /** The password for the new user. */
        private String password;
        /** The ID of the selected gender button, or -1 for none. */
        private int genderButtonId;
        /** The birthday for the new user. */
        private Date birthday;
        /** The email address for the new user. */
        private String email;

        /**
         * Async operation setup routine. This routine is run on the UI thread
         * before the async operation is run. It grabs all of the necessary info
         * the async operation needs from the UI controls and saves it in this
         * object because the async operation thread cannot access UI objects.
         */
        @Override
        protected void uiThreadBefore() {
            // TODO: start wait cursor animation.

            // Cache any data from the UI that we need for this request.
            this.username = usernameEditText.getText().toString();
            this.password = passwordEditText.getText().toString();
            this.genderButtonId
                = genderRadioGroup.getCheckedRadioButtonId();

            Calendar calendar = new GregorianCalendar(birthdayDatePicker.getYear(),
                                                      birthdayDatePicker.getMonth(),
                                                      birthdayDatePicker.getDayOfMonth());

            this.birthday = new Date(calendar.getTimeInMillis());
            this.email = emailEditText.getText().toString();
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

            // Check if the user has chosen a gender.
            User.Gender gender;
            if (genderButtonId == R.id.create_user_male_radiobutton) {
                gender = User.Gender.MALE;
            } else if (genderButtonId == R.id.create_user_female_radiobutton) {
                gender = User.Gender.FEMALE;
            } else {
                throw new ClientException(ClientStatus.APP_MUST_CHOOSE_GENDER);
            }

            // Create new user on server.
            User.create(client,
                        username,
                        password,
                        gender,
                        birthday,
                        email);
        }

        /**
         * This method is run on the UI thread if this object's cancel method
         * is called. It allows for the update of UI elements to inform the user
         * that the requested operation has ceased.
         */
        @Override
        protected void uiThreadOperationCancelled() {
            Toast.makeText(CreateUserActivity.this,
                           ClientStatus.APP_CANCELLED.message,
                           Toast.LENGTH_SHORT).show();
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
            Toast.makeText(CreateUserActivity.this,
                           ClientStatus.OK.message,
                           Toast.LENGTH_SHORT).show();

            // Login to our new user account.
            new AsyncLoginRequest().execute();
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
            Toast.makeText(CreateUserActivity.this,
                           message,
                           Toast.LENGTH_SHORT).show();
        }
    }

    /**
     * Defines an async CreateUserSession operation that grabs data from the UI
     * widgets, tells the server to make a new session, and then updates the UI.
     * @author Christian Gunderman
     */
    private class AsyncLoginRequest extends AsyncClientOperation {

        /** The username of the user to create. */
        private String username;
        /** The password for the new user. */
        private String password;

        /**
         * Async operation setup routine. This routine is run on the UI thread
         * before the async operation is run. It grabs all of the necessary info
         * the async operation needs from the UI controls and saves it in this
         * object because the async operation thread cannot access UI objects.
         */
        @Override
        protected void uiThreadBefore() {
            // TODO: start wait cursor animation.
            /* Shows "Waiting.." toast. This is awkward and should be replaced
               by wait cursor.

               Toast.makeText(CreateUserActivity.this,
                           ClientStatus.APP_WAITING.message,
                           Toast.LENGTH_SHORT).show();
            */

            // Cache any data from the UI that we need for this request.
            this.username = usernameEditText.getText().toString();
            this.password = passwordEditText.getText().toString();
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
            CreateUserActivity.this.session = UserSession.start(client,
                                                                username,
                                                                password);
        }

        /**
         * This method is run on the UI thread if this object's cancel method
         * is called. It allows for the update of UI elements to inform the user
         * that the requested operation has ceased.
         */
        @Override
        protected void uiThreadOperationCancelled() {
            Toast.makeText(CreateUserActivity.this,
                           ClientStatus.APP_CANCELLED.message,
                           Toast.LENGTH_SHORT).show();
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
            /* If we want UI feedback.
               Instead, though, we should probably just open the next window.

            Toast.makeText(CreateUserActivity.this.getContext(),
                           ClientStatus.OK.message,
                           Toast.LENGTH_SHORT).show();
            */

            // Launch profile activity.
            Intent launchActivityIntent
                = new Intent(CreateUserActivity.this, ProfileActivity.class);

            CreateUserActivity.this.session.bundleWithIntent(launchActivityIntent);
            startActivity(launchActivityIntent);
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

            // Display error message.
            Toast.makeText(CreateUserActivity.this,
                           message,
                           Toast.LENGTH_SHORT).show();
        }
    }
}
