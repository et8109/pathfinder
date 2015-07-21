package com.pinata.android;

import android.os.AsyncTask;

import com.pinata.android.client.*;
import com.pinata.android.client.http.*;
import com.pinata.shared.*;

/**
 * Defines an async operation that grabs data from the UI widgets, performs an
 * operation, and then updates the UI to reflect the results. This object is
 * useful because it conveniently synchronizes UI and background operations,
 * where, typically background operations have no access to UI.
 * statusTextView.
 * @author Christian Gunderman
 */
public abstract class AsyncClientOperation {

    /** The Android OS async task that powers the whole shebang. */
    private final AsyncTask<Void, Void, Void> asyncTask;

    /**
     * Creates a new AsyncClientOperation.
     */
    public AsyncClientOperation() {
        this.asyncTask = new Task();
    }

    /**
     * Async operation setup routine. This routine is run on the UI thread
     * before the async operation is run. It grabs all of the necessary info
     * the async operation needs from the UI controls and saves it in this
     * object because the async operation thread cannot access UI objects.
     */
    protected abstract void uiThreadBefore();

    /**
     * Async operation routine. This routine is run by a background worker
     * thread, allowing the UI thread to go undisturbed while HTTP requests
     * are being sent. This method cannot access UI elements, so that is
     * done in uiThreadBefore() and cached in this object. Any
     * ClientExceptions thrown from this method are automatically grabbed
     * and unwrapped and dispatched to the uiThreadAfterFailure() method
     * back on the UI thread.
     * @param client A pre-instantiated HttpClient object.
     */
    protected abstract void backgroundThreadOperation(HttpClient client)
        throws ClientException;

    /**
     * This method is run on the UI thread if this object's cancel method
     * is called. It allows for the update of UI elements to inform the user
     * that the requested operation has ceased.
     */
    protected void uiThreadOperationCancelled() {
        // Do nothing by default.
    }

    /**
     * After the backgroundThreadOperation() function finishes executing,
     * if no exceptions occur, this method is called to signal that the
     * process is assumed to be a success. To signal that the background
     * process was a failure, backgroundThreadOperation() must throw a
     * ClientException.
     */
    protected abstract void uiThreadAfterSuccess();

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
    protected abstract void uiThreadAfterFailure(String message,
                                                 ClientStatus clientStatus,
                                                 ApiStatus apiStatus);

    /**
     * Executes an async task and calls the uiThreadBefore(),
     * backgroundThreadOperation(), and uiThreadAfterSuccess() or
     * uiThreadAfterFailure() accordingly. See the method descriptions
     * for more info.
     * methods accordingly.
     */
    public void execute() {
        this.asyncTask.execute();
    }

    /**
     * Cancels the currently running async task and runs the
     * uiThreadOperationCancelled() method.
     * @param mayInterruptIfRunning If true, allows the task to be cancelled mid
     * execution.
     */
    public void cancel(boolean mayInterruptIfRunning) {
        this.asyncTask.cancel(mayInterruptIfRunning);
    }

    /**
     * An Async task that implements the AsyncClientOperation required
     * functionality.
     * @author Christian Gunderman
     */
    private class Task extends AsyncTask<Void, Void, Void> {

        /** The exception thrown by backgroundThreadOperation. */
        private ClientException ex;

        /**
         * Before background op.
         */
        @Override
        protected void onPreExecute() {
            AsyncClientOperation.this.uiThreadBefore();
        }

        /**
         * Background op, saves exceptions to this.ex, and provides
         * subclasses with premade HttpClient.
         */
        @Override
        protected Void doInBackground(Void... params) {

            // If any exceptions occur, save them for post execution.
            HttpClient client = null;
            try {
                client = new HttpClient();
                AsyncClientOperation.this.backgroundThreadOperation(client);
            } catch (ClientException ex) {
                this.ex = ex;
            } finally {
                client.close();
            }

            return null;
        }

        /**
         * Dispatches uiThreadOperationCancelled handler.
         */
        @Override
        protected void onCancelled() {
            AsyncClientOperation.this.uiThreadOperationCancelled();
        }

        /**
         * Performs post background op exeception unpacking and dispatches
         * the uiThreadAfterFailure() method.
         */
        @Override
        protected void onPostExecute(Void result) {

            // Successful, rosy, happy case.
            if (this.ex == null) {
                AsyncClientOperation.this.uiThreadAfterSuccess();
                return;
            }

            // Failure case.
            String message = this.ex.getMessage();
            ClientStatus clientStatus = this.ex.status;
            ApiStatus apiStatus = null;

            // If inner exception is an API exception, append inner error message.
            if (this.ex.status == ClientStatus.API_ERROR &&
                this.ex.getCause() != null) {
                message += " " + ex.getCause().getMessage();
                apiStatus = ((ApiException)ex.getCause()).status;
            }

            AsyncClientOperation.this.uiThreadAfterFailure(message,
                                                           clientStatus,
                                                           apiStatus);
        }
    }
}
