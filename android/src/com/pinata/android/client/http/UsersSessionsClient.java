package com.pinata.android.client.http;

import com.pinata.android.client.*;
import com.pinata.shared.*;

/**
 * HTTP Client methods for the sessions API endpoints.
 * a.k.a.: The "who's who" code. The login code. I can't make it
 * any more clear than that.
 * @author Christian Gunderman
 */
public abstract class UsersSessionsClient {

    private static final String RESOURCE_SESSIONS
        = "/api/v1/users/%s/sessions/%s";

    /**
     * Creates a new UserSession.
     * a.k.a.: Logs a user in.
     * @param client The HttpClient.
     * @param request The json serializable request for the server.
     * @return The server's verbose response.
     */
    public static UserSessionResponse doCreateUserSessionRequest(
        HttpClient client,
        CreateUserSessionRequest request) throws ClientException {

        // Although this check is done server side, it is critical
        // that they username.length() > 0 because the end point for login is:
        // /api/{version}/users/{username}/sessions.
        // If username.length() == 0, we won't hit that end point and the
        // server error that is returned will be...weird.
        if (request.user.length() == 0) {
            throw new ClientException(ClientStatus
                                     .APP_MUST_ENTER_USERNAME_PASSWORD);
        }

        String path = String.format(RESOURCE_SESSIONS, request.user, "");

        UserSessionResponse response = new UserSessionResponse();
        client.doRequest(HttpClient.Verb.POST,
                         path,
                         null,
                         request,
                         response);

        return response;
    }

    /**
     * Performs a End UserSession Request.
     * a.k.a.: logs out the given user and session.
     * @param username The unique username of the user.
     * @param sessionId The unique id for the user's session.
     * @return The server's verbose response.
     */
    public static UserSessionResponse doEndUserSessionRequest(HttpClient client,
                                                              String username,
                                                              String sessionId)
        throws ClientException {

        // Although this checks is done server side, it is critical
        // that they username.length() > 0 because the end point for login is:
        // /api/{version}/users/{username}/sessions.
        // If username.length() == 0, we won't hit that end point and the
        // server error that is returned will be all WTF.
        if (username.length() == 0) {
            throw new ClientException(ClientStatus
                                     .APP_MUST_ENTER_USERNAME_PASSWORD);
        }

        String path = String.format(RESOURCE_SESSIONS, username, sessionId);

        UserSessionResponse response = new UserSessionResponse();
        client.doRequest(HttpClient.Verb.DELETE,
                         path,
                         null,
                         null,
                         response);

        return response;
    }
}
