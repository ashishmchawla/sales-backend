<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use App\Models\UserDevice;
use App\Models\PushNotification;

class PushNotificationController extends Controller
{

    public function registerUserDevice(Request $request) {

        $user_id = $request->user_id;
        $device_token = $request->device_token;

        UserDevice::updateOrInsert(
            ['user_id' => $user_id, 'device_token' => $device_token],
            ['user_id' => $user_id, 'device_token' => $device_token],
        );

        return array(
            'status' => 1,
            'message' => "Device registered successfully."
        );

    }

    public function savePushNotification( $title, $content ) {

        $pushNotification = new PushNotification();
        $pushNotification->title = $title;
        $pushNotification->content = $content;
        if( $pushNotification->save() ) {
            return $pushNotification->id;
        }
        else {
            return 0;
        }

    }

    public function sendPushNotification( Request $request ) {

        $title = $request->title;
        $content = $request->content;
        $match_id = $request->match_id;

		$tokens = array();
		$app_token = config('app.fcm_token');
        $url = 'https://fcm.googleapis.com/fcm/send';

        $data = array(
            'match_id' => $match_id
        );
        $message = array(
            "title" => $title,
            "body" => $content,
        );

		$userTokens = UserDevices::get();
		if( $userTokens != null ) {

			$notification_id = self::savePushNotification( $title, $content, $match_id );

			foreach( $userTokens as $userToken ) {
				$tokensAndroid = $userToken->device_token;
				$tokens[] = $tokensAndroid;
			}
			$allTokens = array_values(array_unique($tokens));
            $data['ref_id'] = $notification_id;

            $tokenChunks = array_chunk($allTokens, 900);
            $allSent = 0;
            $totalToBeSent = sizeof($tokenChunks);

            foreach($tokenChunks as $tokens) {
                if( sizeof( $tokens ) > 0  ) {
                    $payload = array(
                        'registration_ids' => $tokens,
                        'notification' => $message,
                        'data' => $data,
                        'priority' => "normal",
                        'content_available' => true
                    );
                    $payloadEncoded = json_encode($payload);

                    $client = new Client(); //GuzzleHttp\Client
                    $result = $client->post($url, [
                        'body' => $payloadEncoded,
                        'headers' => [
                            'Authorization' => 'key='.$app_token,
                            'Content-Type' => 'application/json'
                        ]
                    ]);
                    $allSent++;
                }

            }
            if( $allSent == $totalToBeSent ) {

                $notification = PushNotification::find($notification_id);
                $notification->payload = json_encode($result);
                $notification->save();
                return array(
                    'status' => 1,
                    'result' => $result
                );

            }
            else {
                $notification = PushNotification::find($notification_id);
                $notification->failed = 1;
                $notification->save();

                return array(
                    'status' => 0,
                    'result' => 'Notification not sent'
                );
            }

		} else {
			return array(
                'status' => 0,
                'result' => 'No user tokens found in Database'
            );
		}

    }


}
