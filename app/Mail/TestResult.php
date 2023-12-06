<?php

namespace App\Mail;

use App\Models\Attempt;
use App\Models\Test;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

class TestResult extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Attempt $attempt, $lang
    ) {

    }
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Test Result for {$this->attempt->name} {$this->attempt->lastname} - {$this->attempt->test->name}",
        );
    }

    /**
     * Get the message content definition.
     */
    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $test = $this->attempt->QuestionOrder;
        


        foreach ($this->attempt->result as $result) {

            $answers = array_filter(array_values(Arr::wrap($result->answer)));
            $formatAnswer = [];
            foreach ($test as $question) {
                if (Arr::get($question, 'id') !== $result->question_id) {
                    continue;
                }

                if (Arr::get($question, 'type') === 'image-custom') {
                    foreach ($question['answers'] as $answer) {
                        $formatAnswer[] = $answer['value'] . ': ' . Arr::get($result->answer, $answer['id'], '');
                    }
                } else {
                    $allAnswers = Arr::get($question, 'answers', []);
                    $i = 1;
                    foreach ($allAnswers as $item) {
                        if (in_array(Arr::get($item, 'id'), $answers, false)) {
                            $prefix = Arr::get($question, 'type') === 'order' ? "$i. " : '';
                            $formatAnswer[] = $prefix . Arr::get($item, 'value');
                            $i++;
                        }
                    }
                }
            }

            $formatAnswers[] = [
                'question' => $result->question,
                'formatAnswer' => implode('<br>', $formatAnswer),
                'isCorrect' => $result->is_correct ? 'Correct' : 'Incorrect',
            ];
        }

        return (new Content('email.test_results'))
            ->with('formatAnswers', $formatAnswers);
    }


    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

}
