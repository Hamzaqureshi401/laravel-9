<?php

namespace App\Http\Controllers;

use App\Utilities\Contracts\ElasticsearchHelperInterface;
use App\Utilities\Contracts\RedisHelperInterface;
use Illuminate\Http\Request;
use App\Mail\MailObject;
use App\Jobs\SendEmailJob;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use App\Models\User;
use validate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

class EmailController extends Controller
{

    protected $elasticsearchHelper;
    protected $redisHelper;

    public function __construct(
        ElasticsearchHelperInterface $elasticsearchHelper,
        RedisHelperInterface $redisHelper
    ) {
        $this->elasticsearchHelper = $elasticsearchHelper;
        $this->redisHelper = $redisHelper;
    }

    public function send(Request $request){
        $to = $request->to_api_token;
        $subjectBody = $this->parseJsonInput($request->emails);
        foreach ($subjectBody as $value) {
            $mail = new MailObject($value['to'], $value['subject'], $value['body']);
            dispatch(new SendEmailJob($mail));
             $this->unlockElasticsearchIndices();
           $data['search_elastic'] = $this->elasticsearchHelper->storeEmail($value['to'], $value['subject'], $value['body']);
            $key = uniqid();
            $this->redisHelper->storeRecentMessage($key, $value['subject'], $value['body']);
           $data['search_redis'] = "recent_message:{$key}";
        }
        return 'Emails sent asynchronously';
    }

    private function parseJsonInput($json){
        $subjectBody = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'Invalid JSON'], 400);
        }
        return $subjectBody;
    }

     public function unlockElasticsearchIndices(){

        $elasticsearchEndpoint = 'http://localhost:9200';
        $url = "{$elasticsearchEndpoint}/_all/_settings";

        $response = Http::put($url, [
            'index.blocks.read_only_allow_delete' => null,
        ]);
        return $response->json();
    }


    //  TODO - BONUS: implement list method
    public function list(){
        $data['sentEmailsElastic'] = $this->elasticsearchHelper->listSentEmails();
        $data['sentEmailsRedis'] = $this->redisHelper->getRecentMessages();
        return response()->json($data);
    }

    public function testSend(Request $request){
        $emailData = $request->validate([
            'emailData.*.subject' => 'required|string', 
            'emailData.*.body' => 'required|string', 
            'emailData.*.to' => 'required|string', 

        ]);
        foreach(json_decode($request->emailData , true) as $key => $value){
            if(empty($value['subject'])){
                return $this->sendError();
            }elseif(empty($value['body'])){
                return $this->sendError();
            }elseif(empty($value['to'])){
                return $this->sendError();
            }
        }
        $user = User::where('id', Route::input('user'))->first();
        if (!$user) {
            //return response()->json(['error' => 'User not found'], 404);
            $email = 'a9c97cbb1eacc7e4f3686e5a7f9395fd.64e1b8d34f425d19e1ee2ea7236d3028';
        }else{
            /* for Authenticated user */
            // if ($user->api_token != $request->api_token){
            //     return response()->json(['error' => 'User not found'], 404);
            // }
            $email = $user->email;
        }
        $response = $this->send(new Request(['emails' => $request->emailData , 'to_api_token' =>  $email]));

        if($response == "Emails sent asynchronously"){
             return response()->json([
                'code' => '200',
                'status' => 'success',
                'message' => 'Email Sent Successfully!',
                'data' => $response
            ]);
        }else {
            return response()->json([
                'code' => '400',
                'status' => 'error',
                'message' => 'Emails could not be sent',
                'data' => null
            ], 400);
        }

    }
    protected function sendError(){

         return response()->json([
                'code' => '400',
                'status' => 'error',
                'message' => 'Valdation Failed subject , body and to should not Be empty!',
                'data' => null
                ], 400);
    }

}
