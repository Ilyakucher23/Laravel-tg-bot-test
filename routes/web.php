<?php

use App\Telegram\Handler;
use Illuminate\Support\Facades\Route;
use DefStudio\Telegraph\Telegraph;
use App\Models\TelegramUser;
use Illuminate\Http\Request;
use DefStudio\Telegraph\Keyboard\InlineButton;
use DefStudio\Telegraph\Keyboard\InlineKeyboard;
use DefStudio\Telegraph\Models\TelegraphBot;
use App\Http\Controllers\QuizProgressController;

Route::get('/', function () {

    return view('welcome');
});

Route::get('/quiz', [QuizProgressController::class, 'index']);


Route::get('/register-webhook', function () {
    $bot = TelegraphBot::first(); // Get the first bot instance

    if ($bot) {
        // Register and send the webhook to Telegram
        $bot->registerWebhook()->send();
        
        return 'Webhook registered successfully!';
    }

    return 'No bot found!';
});

// Route::post('/telegram/webhook', function (Request $request) use ($quizQuestions) {
//     $update = $request->all(); // Get the raw update from Telegram

//     // Check if the message exists
//     if (isset($update['message'])) {
//         $chatId = $update['message']['chat']['id'];
//         $text = $update['message']['text'];

//         // If user starts quiz with '/start'
//         if ($text === '/start') {
//             startQuiz($chatId, 1); // Start with question 1
//         }
//     }

//     // Check if it's a callback query (for inline button responses)
//     if (isset($update['callback_query'])) {
//         $chatId = $update['callback_query']['message']['chat']['id'];
//         $data = $update['callback_query']['data'];

//         // Expected callback data format: answer:questionNumber:answerText
//         [$action, $questionNumber, $answer] = explode(':', $data);

//         if ($action === 'answer') {
//             handleAnswer($chatId, (int)$questionNumber, $answer);
//         }
//     }

//     return response()->json(['status' => 'ok']);
// });