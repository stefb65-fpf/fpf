<?php

namespace App\Console\Commands;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use GuzzleHttp\Client;

use Microsoft\Kiota\Authentication\Oauth\ClientCredentialContext;
use Microsoft\Graph\GraphServiceClient;
use Microsoft\Graph\Generated\Models\OnlineMeeting;
use Microsoft\Kiota\Abstractions\ApiException;
//require 'vendor/autoload.php';



class CreateTeamsMeeting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:teamsmeeting';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $guzzle = new Client();
        $tokenUrl = 'https://login.microsoftonline.com/common/oauth2/v2.0/token';
        // client_id et client secret de l'app Graph PHP quick start
        $clientId = '239be172-6f9b-42f8-92bc-06846ea2fe5b';
        $clientSecret = 'KGe8Q~kHbzeBmgiHuvNY2D0a5sW9Dz2QRuUuSaOQ';

        // on récupère le token
        $response = $guzzle->post($tokenUrl, [
            'form_params' => [
                'client_id' => $clientId,
                'scope' => 'https://graph.microsoft.com/.default',
                'client_secret' => $clientSecret,
                'grant_type' => 'client_credentials',
            ],
        ]);

        if ($response->getStatusCode() == 200) {
            $result = json_decode($response->getBody(), true);
            $accessToken = $result['access_token'];
            dd($accessToken);
            // user id de l'utilisateur formations@federation-photo.fr
            $userId = '45593d2e-a00b-4f82-9741-ba975f49178e';

            // si je mets le token de l'utilisateur, j'ai une erreur "403 forbidden message"
            // si je mets le token de l'app Graph Explorer récupéré sur l'outil web du  graph explore, j'ai une retour correct avec les infos de l'utilisateur
            $headers = [
//                'Authorization' => "Bearer eyJ0eXAiOiJKV1QiLCJub25jZSI6ImFMVnJZYTRmVDhYYWpLZFNzSUlZTlZtTDhiTkJxaUdJNzFjQ2dfX1BpbDAiLCJhbGciOiJSUzI1NiIsIng1dCI6Ik1HTHFqOThWTkxvWGFGZnBKQ0JwZ0I0SmFLcyIsImtpZCI6Ik1HTHFqOThWTkxvWGFGZnBKQ0JwZ0I0SmFLcyJ9.eyJhdWQiOiIwMDAwMDAwMy0wMDAwLTAwMDAtYzAwMC0wMDAwMDAwMDAwMDAiLCJpc3MiOiJodHRwczovL3N0cy53aW5kb3dzLm5ldC8wNmNhYjE2Yy0zZjY0LTQwNzYtYjJiYi1lYWNjMjg4YzU2YWIvIiwiaWF0IjoxNzIwMTgzODYyLCJuYmYiOjE3MjAxODM4NjIsImV4cCI6MTcyMDI3MDU2MiwiYWNjdCI6MCwiYWNyIjoiMSIsImFpbyI6IkFUUUF5LzhYQUFBQWVWb2QrYlA3dzRIM21Uc29NV2pDQkREWVh6YmZrV3MrMis2bkNFYnZ1NDJuRENMNFhtVjBpbHMyYjBBUzNtQ1QiLCJhbXIiOlsicHdkIl0sImFwcF9kaXNwbGF5bmFtZSI6IkdyYXBoIEV4cGxvcmVyIiwiYXBwaWQiOiJkZThiYzhiNS1kOWY5LTQ4YjEtYThhZC1iNzQ4ZGE3MjUwNjQiLCJhcHBpZGFjciI6IjAiLCJpZHR5cCI6InVzZXIiLCJpcGFkZHIiOiIyYTAxOmNiMTQ6ZGM4OmYxMDA6NjU1ZjplNDQ3OmVhMWY6ZDE3IiwibmFtZSI6IkZQRiAtIEZvcm1hdGlvbnMiLCJvaWQiOiI0NTU5M2QyZS1hMDBiLTRmODItOTc0MS1iYTk3NWY0OTE3OGUiLCJwbGF0ZiI6IjgiLCJwdWlkIjoiMTAwMzIwMDM4MDJBQzk5RiIsInJoIjoiMC5BVndBYkxIS0JtUV9ka0N5dS1yTUtJeFdxd01BQUFBQUFBQUF3QUFBQUFBQUFBQmNBSFUuIiwic2NwIjoib3BlbmlkIHByb2ZpbGUgVXNlci5SZWFkIGVtYWlsIE9ubGluZU1lZXRpbmdzLlJlYWRXcml0ZSIsInNpZ25pbl9zdGF0ZSI6WyJrbXNpIl0sInN1YiI6Ii1UZEk2eGNKZi1nbG9jMk5Wd1Z0NFZnd055ZHlCUVlkZU9vajJnR1htNHMiLCJ0ZW5hbnRfcmVnaW9uX3Njb3BlIjoiRVUiLCJ0aWQiOiIwNmNhYjE2Yy0zZjY0LTQwNzYtYjJiYi1lYWNjMjg4YzU2YWIiLCJ1bmlxdWVfbmFtZSI6ImZvcm1hdGlvbnNAZmVkZXJhdGlvbi1waG90by5mciIsInVwbiI6ImZvcm1hdGlvbnNAZmVkZXJhdGlvbi1waG90by5mciIsInV0aSI6IlEyQmVWbVQwclVTeDh1VlpYMS1WQUEiLCJ2ZXIiOiIxLjAiLCJ3aWRzIjpbImI3OWZiZjRkLTNlZjktNDY4OS04MTQzLTc2YjE5NGU4NTUwOSJdLCJ4bXNfY2MiOlsiQ1AxIl0sInhtc19pZHJlbCI6IjEgOCIsInhtc19zc20iOiIxIiwieG1zX3N0Ijp7InN1YiI6ImtZU3hOWVVGNDBHTEF3UGJXWE1mY09mUFBWOE10cTF1UWZ0cHlVYV9GUmMifSwieG1zX3RjZHQiOjE1ODk5MjIwNTYsInhtc190ZGJyIjoiRVUifQ.HnLaJ_sFh9NLR_ZE8gH5l-y1MgPVs_0sHPoivOU8wdqDYGbzvcT9GTdObRo_YfyRt1aEeW4mTHoclWZVQWr9AWcBkAnDpjvbFVK7VoQtutIEc7KxO6JSZP9IL1PWFhPGLzVMAsDX2aqr2Lc1YD9LlgFTmNnKrBaXALaEO7ocdrENGahb8C37vElM1c2JBUeUL1LAUI59UF8RTEAtZjM3K1J2GfBALLDrTEgm7lNpA5kSG8JwgUfLg2gOyn04eb6rlwuD2aACfc9Tb7ghhEeqRmAfirOIXyM1lTQnatFU_Nvfiz1cALQvf511jrBYPJmQ5JpRkKVU08k7J-jNiKZkTw",
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type' => 'application/json'
            ];

            $url = "https://graph.microsoft.com/v1.0/users/{$userId}/onlineMeetings";
            // TODO remplacer les datas par les infos de la session de formation
            $meetingData = [
                "startDateTime" => "2024-07-10T15:30:00Z",  // Start date and time in UTC
                "endDateTime" => "2024-07-10T16:00:00Z",    // End date and time in UTC
                "subject" => "Test Stéphane"
            ];

            try{
                $response = $guzzle->post($url, [
                    'headers' => $headers,
                    'json' => $meetingData
                ]);
                $data = json_decode($response->getBody()->getContents(), true);
                echo "Error: " . $response->getStatusCode() . " - " . $response->getReasonPhrase();
                echo "\n";
                echo $response->getBody();
                $joinUrl = $data['joinUrl'];
                $mettingCode = $data['meetingCode'];
                dd($joinUrl, $mettingCode);
                // me renvoie quelque chose comme
                //"https://teams.microsoft.com/l/meetup-join/19%3ameeting_Y2E3OTBmMWEtYzRlYS00OWYwLTkyNmQtOWM4NjRmZTdmNjBk%40thread.v2/0?context=%7b%22Tid%22%3a%2206cab16c-3f64-4076-b2bb-eacc288c56ab%22%2c%22Oid%22%3a%2245593d2e-a00b-4f82-9741-ba975f49178e%22%7d"
                //"387464703089"
                //
                // TODO enregistrer le joinUrl et le meetingCode dans la base de données
            } catch (ClientException $e) {
                dd($e);
            }
        }




        die();
        $scopes = ['https://graph.microsoft.com/.default'];
//        $scopes = ['https://graph.microsoft.com/OnlineMeetings.ReadWrite'];

// Values from app registration
        $tenantId = 'common';


        // Get user details
        $url = "https://graph.microsoft.com/v1.0/users/$userId";
        $headers = [
            'Authorization' => "Bearer $accessToken",
//            'Authorization' => "Bearer eyJ0eXAiOiJKV1QiLCJub25jZSI6Il9VTmplWU5WVUhMOWo2YXNYQjgyUWwzSTdMQ2VUN0E3LWp4a3YtZmhheDAiLCJhbGciOiJSUzI1NiIsIng1dCI6Ik1HTHFqOThWTkxvWGFGZnBKQ0JwZ0I0SmFLcyIsImtpZCI6Ik1HTHFqOThWTkxvWGFGZnBKQ0JwZ0I0SmFLcyJ9.eyJhdWQiOiIwMDAwMDAwMy0wMDAwLTAwMDAtYzAwMC0wMDAwMDAwMDAwMDAiLCJpc3MiOiJodHRwczovL3N0cy53aW5kb3dzLm5ldC8wNmNhYjE2Yy0zZjY0LTQwNzYtYjJiYi1lYWNjMjg4YzU2YWIvIiwiaWF0IjoxNzIwMTU3MjkwLCJuYmYiOjE3MjAxNTcyOTAsImV4cCI6MTcyMDI0Mzk5MSwiYWNjdCI6MCwiYWNyIjoiMSIsImFpbyI6IkFUUUF5LzhYQUFBQWhQTm4rVUtpYXJDVVpyczJxZ3pFYXhaSkR1Tm5XS0k0Q0RzYnYwSTlIMHFoVjdIUTJZWDVYVkh4dDFsVmQ4YjYiLCJhbXIiOlsicHdkIl0sImFwcF9kaXNwbGF5bmFtZSI6IkdyYXBoIEV4cGxvcmVyIiwiYXBwaWQiOiJkZThiYzhiNS1kOWY5LTQ4YjEtYThhZC1iNzQ4ZGE3MjUwNjQiLCJhcHBpZGFjciI6IjAiLCJpZHR5cCI6InVzZXIiLCJpcGFkZHIiOiIyYTAxOmNiMTQ6ZGM4OmYxMDA6Y2M2MTo5Y2Y1OmQ5OWY6Y2EzOSIsIm5hbWUiOiJGUEYgLSBGb3JtYXRpb25zIiwib2lkIjoiNDU1OTNkMmUtYTAwYi00ZjgyLTk3NDEtYmE5NzVmNDkxNzhlIiwicGxhdGYiOiI4IiwicHVpZCI6IjEwMDMyMDAzODAyQUM5OUYiLCJyaCI6IjAuQVZ3QWJMSEtCbVFfZGtDeXUtck1LSXhXcXdNQUFBQUFBQUFBd0FBQUFBQUFBQUJjQUhVLiIsInNjcCI6Im9wZW5pZCBwcm9maWxlIFVzZXIuUmVhZCBlbWFpbCIsInNpZ25pbl9zdGF0ZSI6WyJrbXNpIl0sInN1YiI6Ii1UZEk2eGNKZi1nbG9jMk5Wd1Z0NFZnd055ZHlCUVlkZU9vajJnR1htNHMiLCJ0ZW5hbnRfcmVnaW9uX3Njb3BlIjoiRVUiLCJ0aWQiOiIwNmNhYjE2Yy0zZjY0LTQwNzYtYjJiYi1lYWNjMjg4YzU2YWIiLCJ1bmlxdWVfbmFtZSI6ImZvcm1hdGlvbnNAZmVkZXJhdGlvbi1waG90by5mciIsInVwbiI6ImZvcm1hdGlvbnNAZmVkZXJhdGlvbi1waG90by5mciIsInV0aSI6IlVaMW9ER0ZnMlVlRkYzRjRpaDJEQUEiLCJ2ZXIiOiIxLjAiLCJ3aWRzIjpbImI3OWZiZjRkLTNlZjktNDY4OS04MTQzLTc2YjE5NGU4NTUwOSJdLCJ4bXNfY2MiOlsiQ1AxIl0sInhtc19pZHJlbCI6IjggMSIsInhtc19zc20iOiIxIiwieG1zX3N0Ijp7InN1YiI6ImtZU3hOWVVGNDBHTEF3UGJXWE1mY09mUFBWOE10cTF1UWZ0cHlVYV9GUmMifSwieG1zX3RjZHQiOjE1ODk5MjIwNTYsInhtc190ZGJyIjoiRVUifQ.iATV1pS2zjIRux5bGNsQyYiakVqowy9UsrTGrLzpi-Fi1p7nwo0tAzGmQOQRyfbC_Hso_LdOYWPPuawwdNe4SyVBrGtHs3gv_oQhIf8Unc0leNVlS8ueL6GxnEjJDIHikHii95PBPb2TG5nKgHUFdx_PNSBXmkhkD4ralLpG7LDhRJtx2c-YzPFFS3PvLmUXf7SEKaA3JEe2RKE7WadWcPTD8HV6k-0XJS-SZTOKSrtxtlqC9wFjPiyCklSryvcGB0A6NKF5x-7Pfb3dsVj5KfCDs1q_9XhXemoBSPkvKr3Vh5BCaOoAYPSiTxbwBGDAfZ7APixthti2bI8Euvl1ZA",
            'Content-Type' => 'application/json'
        ];


        try {
            $response = $guzzle->get($url, [
                'headers' => $headers
            ]);
            echo "Error: " . $response->getStatusCode() . " - " . $response->getReasonPhrase();
            echo "\n";
            echo $response->getBody();
            die();
            $data = json_decode($response->getBody(), true);
            dd($data);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }







//        $url = "https://graph.microsoft.com/v1.0/users/$userId/onlineMeetings";
//        $headers = [
//            'Authorization' => "Bearer $accessToken",
//            'Content-Type' => 'application/json'
//        ];
//        $meetingData = [
//            "startDateTime" => "2024-07-10T14:30:00Z",  // Start date and time in UTC
//            "endDateTime" => "2024-07-10T15:00:00Z",    // End date and time in UTC
//            "subject" => "My Online Meeting"
//        ];
//
        try{
            $response = $guzzle->post($url, [
                'headers' => $headers,
                'json' => $meetingData
            ]);
            $data = json_decode($response->getBody(), true);
            dd($data);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }





        $tokenContext = new ClientCredentialContext(
            $tenantId,
            $clientId,
            $clientSecret);


//        $graphServiceClient = new GraphServiceClient($tokenContext, $scopes);
//        $result = $graphServiceClient->users()->byUserId($userId)->get()->wait();
//        dd($result);

        $graphServiceClient = new GraphServiceClient($tokenContext);
//        $graphServiceClient = new GraphServiceClient($tokenContext, $scopes);

        try {
            $user = $graphServiceClient->users()->byUserId($userId)->get()->wait();
            dd($user);

        } catch (ApiException $ex) {
            echo $ex->getError()->getMessage();
        }
        die();


        $requestBody = new OnlineMeeting();
        $requestBody->setStartDateTime(new \DateTime('2024-07-12T14:30:34.2444915-07:00'));
        $requestBody->setEndDateTime(new \DateTime('2024-07-12T15:00:34.2464912-07:00'));
        $requestBody->setSubject('User Token Meeting');

        try {
            $result = $graphServiceClient->users()->byUserId($userId)->onlineMeetings()->post($requestBody)->wait();
            dd($result);
        } catch (ApiException $ex) {
            dd($ex->getError()->getMessage());
        }





















//        // Create online meeting
//        $userId = 'formations@federation-photo.fr';
//        $url = "https://graph.microsoft.com/v1.0/users/$userId/onlineMeetings";
//        $headers = [
//            'Authorization' => "Bearer $accessToken",
//            'Content-Type' => 'application/json'
//        ];
//        $meetingData = [
//            "startDateTime" => "2024-07-04T14:30:00Z",  // Start date and time in UTC
//            "endDateTime" => "2024-07-04T15:00:00Z",    // End date and time in UTC
//            "subject" => "My Online Meeting"
//        ];
//
//        $response = $guzzle->post($url, [
//            'headers' => $headers,
//            'json' => $meetingData
//        ]);
//
//        $data = json_decode($response->getBody(), true);
//        dd($data);
//
//
//
//
//        $graph = new \Microsoft\Graph();
//        $graph->setAccessToken($accessToken);
//
////        $tokenRequestContext = new ClientCredentialContext(
////            'tenantId',
////            'clientId',
////            'clientSecret'
////        );
////
////        $graphServiceClient = new GraphServiceClient($tokenRequestContext);
//
//        $meetingDetails = [
//            'startDateTime' => '2023-07-12T14:30:00',
//            'endDateTime' => '2023-07-12T15:30:00',
//            'subject' => 'My Teams Meeting',
//            'participants' => [
//                'organizer' => [
//                    'identity' => [
//                        'user' => [
//                            'id' => 'formations@federation-photo.fr', // Replace with actual user ID
//                        ],
//                    ],
//                ],
//            ],
//        ];
//
//        // Create the online meeting
//        $response = $graph->createRequest('POST', '/me/onlineMeetings')
//            ->attachBody($meetingDetails)
//            ->execute();
//
//// Get the join URL
//        $joinUrl = $response->getBody()['joinWebUrl'];
//
//        echo "Meeting created successfully! Join URL: $joinUrl";
////        dd($tokenRequestContext);
    }
}
