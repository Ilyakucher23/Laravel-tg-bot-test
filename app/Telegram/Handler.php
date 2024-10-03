<?php


namespace App\Telegram;

use DefStudio\Telegraph\Facades\Telegraph;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Stringable;
use DefStudio\Telegraph\Keyboard\ReplyButton;
use DefStudio\Telegraph\Keyboard\ReplyKeyboard;
use DefStudio\Telegraph\DTO;
use App\Models\UserQuizProgress;
use App\Models\TelegramUser;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

class Handler extends WebhookHandler
{
    protected $redisKey;
    protected $progress;

    public $questionNumber = 1;
    public $score = 0;


    public function hello(string $name): void
    {
        $this->reply("hello" . $name);
    }


    public function start(): void
    {
        $firstName = $this->message->from()->firstName();

        $this->reply("Добрий день! $firstName 🙌");

        //$this->reply("Цей бот задасть вам декілька питань. Почнемо?");
        Telegraph::message('Цей бот задасть вам декілька питань. Почнемо?')
            ->replyKeyboard(ReplyKeyboard::make()->buttons([
                ReplyButton::make('/startquiz'),
            ]))->send();

        //test
        // /** @var \DefStudio\Telegraph\Models\TelegraphBot $telegraphBot */
        // $telegraphBot = \DefStudio\Telegraph\Models\TelegraphBot::find(1);

        // $telegraphBot->registerWebhook()->send();
    }

    public function action()
    {
        /** @var DefStudio\Telegraph\DTO\Message $message */
        Log::info($message);

        Telegraph::message('hello world')
            ->replyKeyboard(ReplyKeyboard::make()->buttons([
                ReplyButton::make('foo')->requestPoll(),
                ReplyButton::make('bar')->requestQuiz(),
                ReplyButton::make('biz')->requestContact(),
            ]))->send();
    }

    public function info()
    {
        //first way
        /** @var \DefStudio\Telegraph\Models\TelegraphChat $telegraphChat */
        /** @var DefStudio\Telegraph\DTO\Chat $telegraphdata */
        // $telegraphdata = \DefStudio\Telegraph\Models\TelegraphChat::find(1);
        $telegraphChat = \DefStudio\Telegraph\Models\TelegraphChat::find(1);

        $user = $telegraphChat->info();

        Log::info("username:" . $user['username']);
        //second
        Log::info("chat id: " . $this->chat->id);
        //third

        $senderId = $this->message->from()->id();
        Log::info("sender id: " . $senderId);
        //Telegraph::chatInfo()->send();
    }

    protected function handleChatMessage(Stringable $msg): void
    {

        // $telegraphChat = $this->chat->chat_id;
        // Log::info($telegraphChat);

        // $this->progress = UserQuizProgress::find($user['username']);
        // $this->progress = UserQuizProgress::find($telegraphChat);
        if ($this->isQuizExpired()) {
            $this->reply("Time's up! The quiz is over.");
            return;
        }


        $senderId = $this->message->from()->id();
        Log::info('Sender ID:' . $senderId);
        //mysql
        //$this->progress = UserQuizProgress::where('user_id', $senderId)->first();

        // $questionNumber = $this->progress->question_number;
        // $score = $this->progress->score;

        //redis
        $questionNumber = (int)Redis::get('question_number');
        if ($questionNumber == "")
            $questionNumber = 1;
        //$score = Redis::get('laravel_database_question_number');

        $this->reply('current question: ' . $questionNumber);
        //$this->reply('current score: ' . $score);

        switch ($questionNumber) {
            case 1:
                $this->checkAnswer($msg, '15', 2, "5 * 10 = ?", [
                    '25',
                    '50',
                    '69'
                ]);
                break;
            case 2:
                $this->checkAnswer($msg, '50', 3, "10 / 5 = ?", [
                    '2',
                    '4',
                    '6'
                ]);
                break;
            case 3:
                $this->checkAnswer($msg, '2', 4, "5 + 15 - 10 = ?", [
                    '10',
                    '5',
                    '15'
                ]);
                break;
            case 4:
                $this->checkAnswer($msg, '10', null, 'Quiz finished! Your score: ' . $this->score);
                $this->saveFinalProgressToMySQL();
                break;
        }
    }

    protected function checkAnswer($msg, $correctAnswer, $nextQuestion, $nextQuestionText = null, $options = [])
    {
        $senderId = $this->message->from()->id();
        $questionNumber = (int)Redis::get('question_number');
        //$this->progress = UserQuizProgress::where('user_id', $senderId)->first();

        if ($msg == $correctAnswer) {
            //$this->progress->score += 1;
            //$this->progress['score'] += 1;
            Redis::set('answer:user' . $senderId . 'question:' . $questionNumber, $msg);
            $this->reply('Correct! Next question:');
        } else {
            Redis::set('answer:user' . $senderId . 'question:' . $questionNumber, $msg);
            $this->reply('Wrong! Next question:');
        }


        // Update the question number to the next one
        if ($nextQuestion) {
            //$this->progress->question_number = $nextQuestion;
            $questionNumber += 1;
            Redis::set('question_number', $questionNumber);
            $this->reply("Moving to question number: " . $questionNumber);

            Telegraph::message($nextQuestionText)
                ->replyKeyboard(ReplyKeyboard::make()->buttons(array_map(fn($opt) => ReplyButton::make($opt), $options)))
                ->send();
        } else {
            $this->reply('Quiz completed!');
        }

        //Redis::hmset($this->redisKey, $this->progress);
        //$this->progress->save();
    }

    public function startQuiz()
    {

        // Initialize session variables for new quiz
        $this->questionNumber = 1;
        $this->score = 0;

        //get info
        // $telegraphChat = \DefStudio\Telegraph\Models\TelegraphChat::find(1);
        // $user = $telegraphChat->info();
        $senderId = $this->message->from()->id();

        //mysql code
        // $this->progress = UserQuizProgress::firstOrCreate(
        //     ['user_id' => $senderId],
        //     ['question_number' => 1],
        //     ['score' => 0]
        // );
        //$this->progress->save();

        //Redis code
        //$this->redisKey = "quiz:progress:" . $senderId;
        Redis::set('user_id', $senderId);
        Redis::set('question_number', 1);
        Redis::set('start_time', Carbon::now()->toDateTimeString());
        Log::info("Redis stuff " . ' user_id ' . $senderId . ' start_time ' . Carbon::now()->toDateTimeString());


        // Redis::hmset($this->redisKey, [
        //     'question_number' => 1,
        //     'score' => 0,
        //     'start_time' => Carbon::now()->toDateTimeString(),
        // ]);

        Telegraph::message("5 + 10 = ?")
            ->replyKeyboard(ReplyKeyboard::make()->buttons([
                ReplyButton::make('5'),
                ReplyButton::make('10'),
                ReplyButton::make('15')
            ]))
            ->send();
    }

    protected function isQuizExpired(): bool
    {
        $time = Redis::get('start_time');
        $startTime = Carbon::parse($time);
        return Carbon::now()->diffInMinutes($startTime) > 5;
    }

    protected function saveFinalProgressToMySQL()
    {
        
        $senderId = $this->message->from()->id();
        $questionNumber = (int)Redis::get('question_number');
        $answers = array();
        for ($i = 1; $i < 5; $i++) {
            $answers[$i] = Redis::get('answer:user' . $senderId . 'question:' . $i);
            //$answers = $answers . Redis::get('answer:user' . $senderId . 'question:' . $i);
            Log::info('answers:' . json_encode($answers));
        }
        Log::info('save final:' . var_dump($answers));

        $user = UserQuizProgress::where('user_id', $senderId)->first();
        $user->user_id = $senderId;
        $user->answers = $answers;
        $user->save();

        // Save the final quiz progress to MySQL
        UserQuizProgress::updateOrCreate(
            ['user_id' => $senderId], // Unique identifier
            [
                'answers' => json_encode($answers),
            ]
        );

        $telegraphChat = \DefStudio\Telegraph\Models\TelegraphChat::where('name','[private] Ilya04151')->first();
        $telegraphChat->message('name: ' . $senderId . ' answers: ' . json_encode($answers))->send();
        // Remove progress from Redis after saving to MySQL
        //Redis::del($this->redisKey);
    }
}
