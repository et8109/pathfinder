package com.pinata.android.client;

import java.util.Date;

import com.pinata.shared.*;
import com.pinata.android.client.http.*;

/**
 * Android Client side Event object.
 * @author Elliot Essman
 */
public class Event {
    /** Event ID */
    private int ID;
    /** Eventname. */
    private String eventname;
    /** Event's location. */
    private String location;
    /** Date event date. */
    private Date date;
    /** Event's byob status*/
    private boolean byob;

    /**
     * Attempts to make a REST call to create a new event on the server.
     * @throws ClientException If a parameter is null or not within the accepted
     * set of values or a database or communication error occurs.
     * @param sql The connection to the database.
     * @param eventname The event's eventname.
     * @param location The event's location.
     * @param date The event's date.
     * @param byob The event's byob status.
     * @return A new Event object containing the created event.
     */
    public static Event create(HttpClient client,
                              String eventname,
                              String location,
                              Date date,
                              boolean byob) throws ClientException {
        // Create JSON request object.
        CreateEventRequest request = new CreateEventRequest(eventname,
                                                          location,
                                                          byob,
                                                          date);
        // Send request.
        EventResponse response
            = EventsClient.doCreateEventRequest(client, request);

        return new Event(response.eventID, response.name,
                         response.location, response.date, response.byob);
    }

    /**
     * Creates a new Event OM object. Constructor is private because Event
     * objects can only be created internally from REST calls.
     * @param ID The event's ID.
     * @param eventname The event's eventname.
     * @param location The event's location.
     * @param date The event's date.
     * @param byob The event's byob status.
     */
    private Event(int ID, String eventname, String location, Date date, boolean byob) {
        this.ID = ID;
        this.eventname = eventname;
        this.location = location;
        this.date = date;
        this.byob = byob;
    }
}
