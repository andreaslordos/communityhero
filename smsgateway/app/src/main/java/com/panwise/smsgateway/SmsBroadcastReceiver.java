package com.panwise.smsgateway;


import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.os.Handler;
import android.telephony.SmsManager;
import android.telephony.SmsMessage;
import android.util.Log;

import com.android.volley.DefaultRetryPolicy;
import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.net.URLEncoder;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

public class SmsBroadcastReceiver extends BroadcastReceiver {
    private final String BASE_URL = "https://rhubarb-cake-22341.herokuapp.com/api/v1/";
    private static final String SMS_RECEIVED = "android.provider.Telephony.SMS_RECEIVED";
    static HashMap<String, Boolean> registering = new HashMap<>();

    @Override
    public void onReceive(Context context, Intent intent) {

        Log.d("ON ", "RECEIVE");
        Bundle bundle = intent.getExtras();
        final Object[] messages = (Object[]) bundle.get("pdus");
        SmsMessage[] sms = new SmsMessage[messages.length];
        // Create messages for each incoming PDU
        for (int n = 0; n < messages.length; n++) {
            sms[n] = SmsMessage.createFromPdu((byte[]) messages[n]);
        }
        for (final SmsMessage msg : sms) {
            String contents = msg.getMessageBody().toLowerCase();
                final String url = BASE_URL + "chatbot";
                final RequestQueue queue = Volley.newRequestQueue(context);
                Map<String, String> m = new HashMap<>();
                m.put("from", msg.getOriginatingAddress());
                m.put("content", msg.getMessageBody());
                JsonObjectRequest orderRequest = new JsonObjectRequest(url, new JSONObject(m),
                        new Response.Listener<JSONObject>() {
                            @Override
                            public void onResponse(JSONObject response) {
                                try {
                                    int index = 0;
                                    for (String message : response.getString("content").split("\n")) {
                                        final String cmsg = message;
                                        new Handler().postDelayed(new Runnable() {

                                            @Override
                                            public void run() {
                                                sendSms(msg.getOriginatingAddress(), cmsg);
                                            }
                                        }, 3000 *index);
                                        index+=1;
                                    }
                                } catch (JSONException e) {
                                    e.printStackTrace();
                                }
                                Log.e("response", response.toString());
                            }
                        }, new Response.ErrorListener() {
                    @Override
                    public void onErrorResponse(VolleyError error) {
                        Log.e("ERROR", error.toString());
                    }
                });
                orderRequest.setRetryPolicy(new DefaultRetryPolicy(
                        0,
                        DefaultRetryPolicy.DEFAULT_MAX_RETRIES,
                        DefaultRetryPolicy.DEFAULT_BACKOFF_MULT));

                queue.add(orderRequest);

            Log.e("RECEIVED MSG", ":" + msg.getMessageBody());
            // Verify if the message came from our known sender

        }
    }

    private void sendSms(String addr, String mess){
        SmsManager sms = SmsManager.getDefault();
        ArrayList<String> parts = sms.divideMessage(mess);
        sms.sendMultipartTextMessage(addr, null, parts, null,
                null);}
}