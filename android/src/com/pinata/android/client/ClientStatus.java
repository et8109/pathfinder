package com.pinata.android.client;

/**
 * ClientStatus and error messages.
 * @author Christian Gunderman
 */
public enum ClientStatus {
    // Success codes.
    OK(200, "Success."),

    // App Errors.
    APP_CANCELLED(300, "Cancelled."),
    APP_WAITING(301, "Waiting..."),
    APP_MUST_CHOOSE_GENDER(302, "You must first choose a gender."),
    APP_MUST_ENTER_USERNAME_PASSWORD(303, "You must enter a username and password."),

    // HTTP Client Errors.
    HTTP_UNKNOWN_ERROR(400, "Unknown error. Unable to reach server."),
    HTTP_MALFORMED_RESPONSE(401, "Server returned garbled response."),
    HTTP_INVALID_SESSION(402, "Invalid session. Please log in again."),

    // HTTP Server API Errors.
    API_ERROR(500, "Server error.");

    /** The String name of the state (OK, MALFORMED_REQUEST, ...) */
    public final String status;
    /** The integer code for this error or state. */
    public final int code;
    /** The String message explaining this error. */
    public final String message;

    /**
     * Constructor for error messages.
     * @param code The unique integer error code.
     * @param message The message for this error or state.
     */
    ClientStatus(int code, String message) {
        this.status = this.name();
        this.code = code;
        this.message = message;
    }
}
