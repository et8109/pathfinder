package com.pinata.android.client.http;

import com.pinata.android.client.*;
import com.pinata.shared.*;

/**
 * HTTP Client methods for the events API endpoints.
 * @author Elliot Essman
 */
public abstract class EventsClient {

    private static final String RESOURCE_EVENTS = "/api/v1/events";

    /**
     * Performs CreateEventRequest on events end point.
     * @throws ClientException Thrown if the request is not successful
     * for any reason, including a non 2XX HTTP response code.
     * @param client The HttpClient to make the request.
     * @param request The CreateEventRequest to send via JSON.
     * @return EventResponse The unmodified server response, deserialized
     * to a class, if successful.
     */
    public static EventResponse doCreateEventRequest(HttpClient client,
                                                     CreateEventRequest request)
        throws ClientException {

        EventResponse eventResponse = new EventResponse();
        client.doRequest(HttpClient.Verb.POST,
                         RESOURCE_EVENTS,
                         null,
                         request,
                         eventResponse);

        return eventResponse;
    }
}
