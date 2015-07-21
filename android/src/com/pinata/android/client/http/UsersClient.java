package com.pinata.android.client.http;

import com.pinata.android.client.*;
import com.pinata.shared.*;

/**
 * HTTP Client methods for the users API endpoints.
 * @author Christian Gunderman
 */
public abstract class UsersClient {

    private static final String RESOURCE_USERS = "/api/v1/users";

    /**
     * Performs CreateUserRequest on users end point.
     * @throws ClientException Thrown if the request is not successful
     * for any reason, including a non 2XX HTTP response code.
     * @param client The HttpClient to make the request.
     * @param request The CreateUserRequest to send via JSON.
     * @return UserResponse The unmodified server response, deserialized
     * to a class, if successful.
     */
    public static UserResponse doCreateUserRequest(HttpClient client,
                                                   CreateUserRequest request)
        throws ClientException {

        UserResponse userResponse = new UserResponse();
        client.doRequest(HttpClient.Verb.POST,
                         RESOURCE_USERS,
                         null,
                         request,
                         userResponse);

        return userResponse;
    }

    /**
     * Performs HTTP DELETE on Users resource to the delete the specified user.
     * Only ROLE_ADMIN users and the user themself can delete a user.
     * @param client The HttpClient.
     * @param username The user to delete.
     */
    public static UserResponse doDeleteUserRequest(HttpClient client,
                                                   String username)
        throws ClientException {

        UserResponse userResponse = new UserResponse();
        String path = String.format("%s/%s",
                                    RESOURCE_USERS,
                                    username);

        client.doRequest(HttpClient.Verb.DELETE,
                         path, null, null, userResponse);

        return userResponse;
    }
                                                   
}
