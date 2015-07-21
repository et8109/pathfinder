package com.pinata.android.client.http;

import java.net.URI;
import java.net.URISyntaxException;
import java.io.IOException;
import java.io.UnsupportedEncodingException;

import android.net.http.AndroidHttpClient;

import org.apache.http.HttpResponse;
import org.apache.http.client.methods.*;
import org.apache.http.entity.StringEntity;
import org.apache.http.protocol.HTTP;
import org.apache.http.util.EntityUtils;

import com.pinata.android.client.*;
import com.pinata.shared.*;

/**
 * HTTP REST API Client. Performs all HTTP calls and translates the exceptions
 * into corresponding ClientStates wrapped in ClientExceptions. Be sure to call
 * close() when done.
 * @author Christian Gunderman
 */
public class HttpClient {
    /** URL scheme prefix. */
    private static final String SCHEME = "http";
    /** User agent: tells the server who this is that is connecting. */
    private static final String USER_AGENT = "Android/PinataClient";
    /** Domain name of the server to connect to. */
    private static final String HOSTNAME = "tmpd4bed93f341b.STUDENT.CWRU.Edu:8080";

    /** The Pinata Session Http Header. */
    private static final String SESSION_HEADER = "Pinata-Session";

    /** The HTTP client object backing this. */
    private final AndroidHttpClient httpClient;
    /** The Authenticated Session. */
    private UserSession session;

    /**
     * Constructs an HTTP client.
     */
    public HttpClient() {
        this.httpClient = AndroidHttpClient.newInstance(USER_AGENT);
    }

    /**
     * Sets this HttpClient's user session.
     * @param UserSession Returned by a call to UserSession.start().
     */
    public void setUserSession(UserSession session) {
        this.session = session;
    }

    /**
     * Closes this HttpClient.
     */
    public void close() {
        this.httpClient.close();
    }

    /**
     * Performs an HTTP request with the given ApiRequest object.
     * @param verb The HTTP verb, such as GET, POST, PUT, DELETE, etc.
     * @param path The path of the resource on the server. For example:
     * /api/v1/users.
     * @param query The query parameter string to append to the URL for the
     * request.
     * @param response The empty ApiResponse object that will receive the
     * response to this HTTP request.
     * @return The HTTP status code returned by the server.
     */
    int doRequest(Verb verb,
                  String path,
                  String query,
                  ApiRequest request,
                  ApiResponse response) throws ClientException {
        URI endpointURI = null;

        try {
            endpointURI = new URI(SCHEME,
                                  HOSTNAME,
                                  path,
                                  query,
                                  null);
        } catch (URISyntaxException ex) {
            throw new ClientException(ClientStatus.HTTP_UNKNOWN_ERROR, ex);
        }

        HttpRequestBase httpRequest
            = this.buildRequest(endpointURI, request, verb);
 
        return executeRequest(httpRequest, response);
    }

    /**
     * Constructs an HttpRequest object from the input parameters.
     * @param endpointURI The URI of the resource we are contacting.
     * @param request The unserialized JSON request object.
     * @param verb The HTTP verb to place in our request.
     */
    private HttpRequestBase buildRequest(URI endpointURI,
                                         ApiRequest request,
                                         Verb verb)
        throws ClientException {
        HttpRequestBase httpRequest = null;

        switch (verb) {
        case GET:
            httpRequest = new HttpGet(endpointURI);
            break;
        case POST:
            httpRequest = new HttpPost(endpointURI);
            break;
        case PUT:
            httpRequest = new HttpPut(endpointURI);
            break;
        case PATCH:
            httpRequest = new HttpPut(endpointURI);
            break;
        case DELETE:
            httpRequest = new HttpDelete(endpointURI);
            break;
        default:
            throw new ClientException(ClientStatus.HTTP_UNKNOWN_ERROR);
        }

        // Insert JSON Data.
        if (request != null &&
            (verb == Verb.POST ||
             verb == Verb.PUT ||
             verb == Verb.PATCH || 
             verb == Verb.DELETE)) {
            HttpEntityEnclosingRequestBase enclosingRequest
                = (HttpEntityEnclosingRequestBase)httpRequest;
            enclosingRequest.setHeader(HTTP.CONTENT_TYPE, "application/json");

            try {
                enclosingRequest.setEntity(new StringEntity(request.serialize()));
            } catch (UnsupportedEncodingException ex) {
                throw new ClientException(ClientStatus.HTTP_UNKNOWN_ERROR, ex);
            }
        }

        // Insert session header.
        if (this.session != null) {
            httpRequest.addHeader(SESSION_HEADER,
                                  this.session.toSessionHeader());
        }

        return httpRequest;
    }

    /**
     * Executes a request and stores the server response, deserialized in
     * response.
     * @param httpRequest The HttpRequest to execute.
     * @param response The deserialized JSON response from the remote server.
     * @return The HTTP status code.
     */
    private int executeRequest(HttpRequestBase httpRequest,
                               ApiResponse response) throws ClientException {
        HttpResponse httpResponse = null;
        int httpStatus = 500;
        try {
            httpResponse = this.httpClient.execute(httpRequest);

            httpStatus = httpResponse.getStatusLine().getStatusCode();

            // Parse response into JSON and check for exception messages.
            String jsonBody = EntityUtils.toString(httpResponse.getEntity());
            if (httpStatus >= 200 && httpStatus < 300) {

                // Deserialize Success JSON response.
                response.deserializeFrom(jsonBody);
            } else {
                // Deserialize Error JSON response.
                ErrorApiResponse errorResponse = new ErrorApiResponse();
                errorResponse.deserializeFrom(jsonBody);

                // Null check the exeception ID before we proceed.
                if (errorResponse.status == null) {
                    throw new ClientException(ClientStatus.HTTP_UNKNOWN_ERROR, null);
                }

                // Rethrow the server exception as a client side exception.
                throw new ClientException(ClientStatus.API_ERROR,
                    new ApiException(ApiStatus.valueOf(errorResponse.status)));
            }
        } catch (IOException ex) {
            throw new ClientException(ClientStatus.HTTP_UNKNOWN_ERROR, ex);
        } catch (ApiException ex) {
            throw new ClientException(ClientStatus.API_ERROR, ex);
        } catch (IllegalArgumentException ex) {
            throw new ClientException(ClientStatus.HTTP_UNKNOWN_ERROR, null);
        }

        return httpStatus;
    }

    /**
     * Supported HTTP verbs.
     */
    enum Verb {
        GET,
        POST,
        PUT,
        PATCH,
        DELETE
    }
}
