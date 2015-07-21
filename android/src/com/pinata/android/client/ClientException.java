package com.pinata.android.client;

/**
 * ClientException for errors occurring during communication with the
 * server side.
 * @author Christian Gunderman
 */
public class ClientException extends Exception {

    /** Application status code. */
    public final ClientStatus status;

    /**
     * Creates a new ApiException.
     * @param status The application status that is representative of the
     * error.
     */
    public ClientException(ClientStatus status) {
        this(status, null);
    }

    /**
     * Creates a new ClientException.
     * @param status The application status that is representative of the
     * error.
     * @param cause The exception that caused this exception.
     */
    public ClientException(ClientStatus status, Throwable cause) {
        super(status.message, cause);
        this.status = status;
    }

    /**
     * Gets string representation of this exception.
     * @return The enum name of the error associated with this exception.
     */
    @Override
    public String toString() {
        return this.status.status;
    }
}
