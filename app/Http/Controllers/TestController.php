<?php

namespace App\Http\Controllers;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Events\NewMessage;
use App\Models\Message;
use Illuminate\Support\Facades\Bus;
class TestController extends Controller
{
    public function sendMessages(){
        try{
            $token = 'EAAPVdx4hYY4BO8ttnLjnJ9oZAp1QBUpfD5tGw0LauZA1jztKR7PAOPnkMXTZAYEFjGEW8Hq609p7ZCjuqZAashwiLWRicz8NoIzM4CrNSm0gaZBZCGBnBOauEOkS0pUhaOObNsxloCObeoYJZBwPVFhDsIWUBf5SbHHZCN4ZC2EwNkpCtsB3Rrt4i6z3FA2nqICZBhZB5PZC7XRXFTPmYiBJIkA1ZC5R7DYt0s';    
            $phoneId = '144004162126072';
            $version = 'v17.0';
            $payload = [
                "messaging_product" => "whatsapp",
                "to" => "51984267854",
                "type" => "template",
                "template" => [
                    "name" => "hello_world",
                    "language" => [
                        "code" => "en_US"
                    ]
                ]
            ];
            $message = Http::withToken($token)->post('https://graph.facebook.com/'.$version.'/'.$phoneId.'/messages', $payload)->throw()->json();
            
            return response()->json([
                'status' => true,
                'data' => $message
            ], 200);
        } catch (Exception $e){
            return response()->json([
                'status' => false,
                'data' => $e->getMessage()
            ], 500);
        }
        
    }

    public function verifyWebhook(Request $request){
        try {
            $verifyToken = 'machupicchuterra';
            $query = $request->query();

            $mode = $query['hub_mode'];
            $token = $query['hub_verify_token'];
            $challenge = $query['hub_challenge'];
            if($mode && $token){
                if($mode === 'subscribe' && $token == $verifyToken){
                    return response($challenge, 200)->header('Content-Type', 'text/plain');
                    //respuesta que espera facebook par aceptar el webhokk y darlo como valido
                }
            }

            throw new Exception('Invalid request');
            
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'data' => $e->getMessage()
            ], 500);
        }
    }

    public function processWebhook(Request $request){
        try {
            $bodyContend = json_decode($request->getContent(), true);
            $value = $bodyContend['entry'][0]['changes'][0]['value'];
            if(!empty($value['messages'])){
                if($value['messages'][0]['type'] == 'text'){
                    //mensaje tipo texto
                    $body = $value['messages'][0]['text']['body'];
                    // crear nuevo mensaje en la bd
                    $newMessageEntry = json_decode($request->getContent(), true);
                    $name = $newMessageEntry['entry'][0]['changes'][0]['value']['contacts'][0]['profile']['name'];
                    $body = $newMessageEntry['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'];

                    $message = new Message([
                        'name' => $name,
                        'message' => $body
                    ]);
                    $message->save();

                    //Bus::dispatch(new NewMessage());
                    event(new NewMessage());
                }
            }
            
            return response()->json([
                'status' => true,
                'data' => $body
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'tipo' => 'error no manejado',
                'data' => $e->getMessage()
            ], 200);
        }
    }
}
