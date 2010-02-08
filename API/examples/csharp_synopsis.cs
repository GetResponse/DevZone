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

using System;
using System.Web.Script.Serialization;
using System.IO;
using System.Text;
using System.Collections;
using System.Collections.Generic;
using System.Net;

namespace GetResponseApi
{
    class Synopsis
    {
        // your API key
        // available at http://www.getresponse.com/my_api_key.html
        static String api_key = "ENTER_YOUR_API_KEY_HERE";

        // API 2.x URL
        static String api_url = "http://api2.getresponse.com";

        public static void Main(string[] args)
        {
            JavaScriptSerializer jss = new JavaScriptSerializer();

            // get CAMPAIGN_ID of 'sample_marketing' campaign

            // new request object
            Hashtable _request = new Hashtable();

            // set method name
            _request["method"] = "get_campaigns";

            // set conditions
            Hashtable operator_obj = new Hashtable();
            operator_obj["EQUALS"] = "sample_marketing";

            Hashtable name_obj = new Hashtable();
            name_obj["name"] = operator_obj;

            // set params request object
            object[] params_array = {api_key, name_obj};

            _request["params"] = params_array;

            // send headers and content in one request
            // (disable 100 Continue behavior)
            System.Net.ServicePointManager.Expect100Continue = false;

            // initialize client
            HttpWebRequest request = (HttpWebRequest)WebRequest.Create(api_url);
            request.Method = "POST";

            byte[] request_bytes = Encoding.UTF8.GetBytes(jss.Serialize(_request));

            String response_string = null;

            try
            {
                // call method 'get_messages' and get result
                Stream request_stream = request.GetRequestStream();
                request_stream.Write(request_bytes, 0, request_bytes.Length);
                request_stream.Close();

                HttpWebResponse response = (HttpWebResponse)request.GetResponse();
                Stream response_stream = response.GetResponseStream();

                StreamReader reader = new StreamReader(response_stream);
                response_string = reader.ReadToEnd();
                reader.Close();

                response_stream.Close();
                response.Close();
            }
            catch (Exception e)
            {
                //check for communication and response errors
                //implement handling if needed
                Console.WriteLine(e.Message);
                Environment.Exit(0);
            }

            // decode response to Json object
            Dictionary<string, object> jsonContent = jss.DeserializeObject( response_string ) as Dictionary<string, object>;

            // get result
            Dictionary<string, object> result = jsonContent[ "result" ] as Dictionary<string, object>;

            string campaign_id = null;

            // get campaign id
            foreach( object key in result.Keys)
            {
                campaign_id = key.ToString();
            }

            // add contact to 'sample_marketing' campaign

            // new request object
            _request = new Hashtable();

            // set method name
            _request["method"] = "add_contact";

            Hashtable contact_params = new Hashtable();
            contact_params["campaign"] = campaign_id;
            contact_params["name"] = "Sample Name";
            contact_params["email"] = "sample@email.com";
            contact_params["cycle_day"] = "0";

            Hashtable custom = new Hashtable();
            custom["name"] = "last_purchased_product";
            custom["content"] = "netbook";

            // contact customs array
            object[] customs_array = {custom};

            // add customs to contact params
            contact_params["customs"] = customs_array;


            // set params request object
            object[] add_contact_params_array = {api_key, contact_params};

            _request["params"] = add_contact_params_array;

            // send headers and content in one request
            // (disable 100 Continue behavior)
            System.Net.ServicePointManager.Expect100Continue = false;

            // initialize client
            request = (HttpWebRequest)WebRequest.Create(api_url);
            request.Method = "POST";

            request_bytes = Encoding.UTF8.GetBytes(jss.Serialize(_request));

            response_string = null;

            try
            {
                // call method 'add_contact' and get result
                Stream request_stream = request.GetRequestStream();
                request_stream.Write(request_bytes, 0, request_bytes.Length);
                request_stream.Close();

                HttpWebResponse response = (HttpWebResponse)request.GetResponse();
                Stream response_stream = response.GetResponseStream();

                StreamReader reader = new StreamReader(response_stream);
                response_string = reader.ReadToEnd();
                reader.Close();

                response_stream.Close();
                response.Close();
            }
            catch (Exception e)
            {
                //check for communication and response errors
                //implement handling if needed
                Console.WriteLine(e.Message);
                Environment.Exit(0);
            }

            Console.Write("Contact added\n");
        }
    }
}
