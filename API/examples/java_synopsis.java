/**
*
* Implementation of sample scenario using GetResponse API:
*
* Add new contact to campaign 'sample_marketing'.
* Start his follow-up cycle and set custom field
* 'last_purchased_product' to 'netbook'.
*
* @author Dawid Ostapiuk
* http://implix.com
* http://dev.getresponse.com
*
*/

package gr_api;

import org.apache.commons.httpclient.*;
import org.apache.commons.httpclient.methods.*;
import net.sf.json.*;

import java.io.*;
import java.util.Hashtable;
import java.util.Iterator;

public class Main
{

    private static String url = "http://api2.getresponse.com/";
    private static String api_key = "ENTER_YOUR_API_KEY_HERE";

    /**
     * @param args the command line arguments
     */
    public static void main(String[] args)
    {
        // create an instance of HttpClient.
        HttpClient client = new HttpClient();

        // request params
        Hashtable operator_obj = new Hashtable();
        operator_obj.put("EQUALS", "sample_marketing");

        Hashtable name_obj = new Hashtable();
        name_obj.put("name", operator_obj);

        Object[] params = { api_key, name_obj};

        Hashtable request = new Hashtable();
        request.put("method", "get_campaigns");
        request.put("params", params);

        JSONObject json = new JSONObject();

        // create a method instance.
        PostMethod method = new PostMethod(url);
        method.setRequestBody((String)json.fromObject(request).toString());

        String response_string = null;
        
        try
        {
            // make request.
            int statusCode = client.executeMethod(method);

            if (statusCode != HttpStatus.SC_OK)
            {
                System.err.println("Method failed: " + method.getStatusLine());
            }

            // read the response body
            byte[] responseBody = method.getResponseBody();

            response_string = new String(responseBody);
        }
        catch (HttpException e)
        {
            System.err.println("Fatal protocol violation: " + e.getMessage());
            e.printStackTrace();
        } 
        catch (IOException e)
        {
            System.err.println("Fatal transport error: " + e.getMessage());
            e.printStackTrace();
        } 
        finally
        {
            // release the connection.
            method.releaseConnection();
        }

        // parse response
        JSONObject jsonObj = JSONObject.fromObject(response_string);

        JSONObject result =  jsonObj.getJSONObject("result");

        Iterator iterator = result.keys();

        String campaign_id = null;
   
        while (iterator.hasNext())
        {
            // set campaign ID
            campaign_id = (String) iterator.next();
        }
        
        // add new contact

        // new instance of HttpClient.
        client = new HttpClient();

        // contact params
        Hashtable custom = new Hashtable();
        custom.put("name", "last_purchased_product");
        custom.put("content", "netbook");

        // contact customs array
        Object[] customs_array = {custom};
     
        Hashtable contact_params = new Hashtable();
        contact_params.put("campaign", campaign_id);
        contact_params.put("name", "Sample Name");
        contact_params.put("email", "sample@email.com");
        contact_params.put("cycle_day", "0");
        contact_params.put("customs", customs_array);

        Object[] request_params = { api_key, contact_params};

        // request object
        request = new Hashtable();
        request.put("method", "add_contact");
        request.put("params", request_params);

        json = new JSONObject();

        // create a method instance.
        method = new PostMethod(url);
        method.setRequestBody((String)json.fromObject(request).toString());

        response_string = null;

        try
        {
            // execute the method.
            int statusCode = client.executeMethod(method);

            if (statusCode != HttpStatus.SC_OK)
            {
                System.err.println("Method failed: " + method.getStatusLine());
            }

            // read the response body.
            byte[] responseBody = method.getResponseBody();

            response_string = new String(responseBody);
        }
        catch (HttpException e)
        {
            System.err.println("Fatal protocol violation: " + e.getMessage());
            e.printStackTrace();
        }
        catch (IOException e)
        {
            System.err.println("Fatal transport error: " + e.getMessage());
            e.printStackTrace();
        }
        finally
        {
            // release the connection.
            method.releaseConnection();
        }

        System.out.println("Contact Added");
    }
}

