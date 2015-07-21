package com.pinata.android;

import java.util.Calendar;
import java.util.GregorianCalendar;
import java.util.Date;

import android.app.Activity;
import android.os.Bundle;

import android.widget.Button;
import android.widget.DatePicker;
import android.widget.TimePicker;
import android.widget.EditText;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.CheckBox;
import android.widget.TextView;
import android.widget.Toast;
import android.view.Window;
import android.util.Log;

import android.view.View;

import com.pinata.android.client.*;
import com.pinata.android.client.http.*;
import com.pinata.shared.*;

/**
 * CreateEventActivity UI Handling Routines.
 * @author Elliot Essman
 */
public class CreateEventActivity extends Activity {

    /** Authentication session. */
    private UserSession session;

    /** The application status TextView. */
    private TextView statusTextView;
    /** The eventname EditText. */
    private EditText eventnameEditText;
    /** The location EditText. */
    private EditText locationEditText;
    /** The byob checkbox. */
    private CheckBox byobCheckBox;
    /** The datePicker control. */
    private DatePicker datePicker;
    /** The timepicker control. */
    private TimePicker timePicker;
    /** The Create button. */
    private Button submitButton;

    /**
     * Called by Android OS when activity is first started.
     */
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        // Remove title bar.
        this.requestWindowFeature(Window.FEATURE_NO_TITLE);

        // Load window XML layout.
        setContentView(R.layout.create_event);

        // Unpack session, if there is one.
        try {
            this.session = UserSession.unbundleFromIntent(this.getIntent());
        } catch (ClientException ex) {
            Log.wtf("CreateEventActivity",
                    "Malformed or not provided sessionHeader.");
            this.finish();
        }


        // Find view buttons and save references to convenient fields.
        this.statusTextView 
            = (TextView)this.findViewById(R.id.create_event_status_textview);
        this.eventnameEditText
            = (EditText)this.findViewById(R.id.create_event_eventname_edittext);
        this.locationEditText
            = (EditText)this.findViewById(R.id.create_event_location_edittext);
        this.byobCheckBox
            = (CheckBox)this.findViewById(R.id.create_event_byob_checkbox);
        this.datePicker
            = (DatePicker)this.findViewById(R.id.create_event_date_datepicker);
        this.timePicker
            = (TimePicker)this.findViewById(R.id.create_event_date_timepicker);
        this.submitButton
            = (Button)this.findViewById(R.id.create_event_submit_button);
    }

    /**
     * Called when the submit button is clicked. Makes async server call to
     * create a new event.
     * @param The view that was clicked, the submit button.
     */
    public void onSubmitButtonClicked(View view) {
        new AsyncCreateEventRequest().execute();
    }

    /**
     * Defines an async CreateEvent operation that grabs data from the UI
     * widgets, tells the server to make a new event, and then updates the
     * statusTextView.
     * @author Elliot Essman
     */
    private class AsyncCreateEventRequest extends AsyncClientOperation {

        /** The eventname of the event to create. */
        private String eventname;
        /** The location for the new event. */
        private String location;
        /** The value of byob, */
        private boolean byob;
        /** The date for the new event. */
        private Date date;

        /**
         * Async operation setup routine. This routine is run on the UI thread
         * before the async operation is run. It grabs all of the necessary info
         * the async operation needs from the UI controls and saves it in this
         * object because the async operation thread cannot access UI objects.
         */
        @Override
        protected void uiThreadBefore() {
            // TODO: start wait cursor animation.
            statusTextView.setText("Creating event...");

            // Cache any data from the UI that we need for this request.
            this.eventname = eventnameEditText.getText().toString();
            this.location = locationEditText.getText().toString();
            this.byob
                = byobCheckBox.isChecked();

            // TODO: Date doesn't transfer time.
            // Replace calendar up the stack.
            Calendar calendar = new GregorianCalendar(
                    datePicker.getYear(),
                    datePicker.getMonth(),
                    datePicker.getDayOfMonth(),
                    timePicker.getCurrentHour(),
                    timePicker.getCurrentMinute());

            this.date = new Date(calendar.getTimeInMillis());
        }

        /**
         * Async operation routine. This routine is run by a background worker
         * thread, allowing the UI thread to go undisturbed while HTTP requests
         * are being sent. This method cannot access UI elements, so that is
         * done in uiThreadBefore() and cached in this object. Any
         * ClientExceptions thrown from this method are automatically grabbed
         * and unwrapped and dispatched to the uiThreadAfterFailure() method
         * back on the UI thread.
         * This implementation performs a CreateEvent request via the Event
         * object.
         * @param client A pre-instantiated HttpClient object.
         */
        @Override
        protected void backgroundThreadOperation(HttpClient client)
            throws ClientException {

            client.setUserSession(session);
        
            // Create new event on server.
            Event.create(client,
                        eventname,
                        location,
                        date,
                        byob);
        }

        /**
         * This method is run on the UI thread if this object's cancel method
         * is called. It allows for the update of UI elements to inform the event
         * that the requested operation has ceased.
         */
        @Override
        protected void uiThreadOperationCancelled() {
            statusTextView.setText(ClientStatus.APP_CANCELLED.message);
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
            statusTextView.setText(ClientStatus.OK.message);
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
            statusTextView.setText(message);
        }
    }
}
