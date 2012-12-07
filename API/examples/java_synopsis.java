// Demonstrates how to add new contact to campaign.

// JSON-RPC module is required
// available at http://software.dzhuvinov.com/json-rpc-2.0.html
import com.thetransactioncompany.jsonrpc2.client.*;
import com.thetransactioncompany.jsonrpc2.*;
import net.minidev.json.*;

import java.net.*;
import java.util.*;

public class java_synopsis {

    public static void main(String[] args) throws Exception {

        // your API key is available at
        // https://app.getresponse.com/my_api_key.html
        String api_key = "ENTER_YOUR_API_KEY_HERE";

        // API 2.x URL
        URL api_url = new URL("http://api2.getresponse.com");

        // initialize JSON-RPC client
        JSONRPC2Session client = new JSONRPC2Session(api_url);
        client.getOptions().setRequestContentType("application/json");

        // find campaign named 'test'
		JSONRPC2Response campaigns = client.send(
		    new JSONRPC2Request(
                "get_campaigns",
                Arrays.asList(new Object[]{
                    api_key,
                    // find by name literally
                    new Hashtable<String, Map>() {{
                        put("name",
                            new Hashtable<String, String>() {{
                                put("EQUALS","test");
                            }}
                        );
                    }}
                }),
                1
            )
		);

        // uncomment following line to preview Response
		// System.out.println(campaigns.getResult());

        // because there can be only one campaign of this name
        // first key is the CAMPAIGN_ID required by next method
        // (this ID is constant and should be cached for future use)
        @SuppressWarnings("unchecked")
        final String CAMPAIGN_ID = ((HashMap<String, Map>)campaigns.getResult()).keySet().iterator().next();

        // add contact to the campaign
        JSONRPC2Response result = client.send(
		    new JSONRPC2Request(
                "add_contact",
                Arrays.asList(new Object[]{
                    api_key,
                    new Hashtable<String, Object>() {{
                        
                        // identifier of 'test' campaign
                        put("campaign", CAMPAIGN_ID);
                        
                        // basic info
                        put("name", "Test");
                        put("email", "test@test.test");
                        
                        // custom fields
                        put("customs", Arrays.asList(new Hashtable[]{
                            new Hashtable<String, String>() {{
                                put("name", "likes_to_drink");
                                put("content", "tea");
                            }},
                            new Hashtable<String, String>() {{
                                put("name", "likes_to_drink");
                                put("content", "tea");
                            }}
                        }));
                    }}
                }),
                2
            )
		);
        
        // uncomment following line to preview Response
		// System.out.println(result.getResult());
        
        System.out.println("Contact Added");
    }
}

// Pawel Pabian http://implix.com
