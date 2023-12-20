<?php

namespace App\Mail;

use App\Models\Attempt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class TestResult extends Mailable
{
    use Queueable, SerializesModels;

    public array $formatAnswers;

    public function __construct(
        public Attempt $attempt,
        string $lang,
    ) {
        $this->formatAnswers = $this->formatAnswers();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Test Result for {$this->attempt->name} {$this->attempt->lastname} - {$this->attempt->test->name}",
        );
    }

    public function content(): Content
    {
        return (new Content('email.test_results'))
            ->with('formatAnswers', $this->formatAnswers);
    }

    public function attachments(): array
    {
        $pdf = PDF::loadView('email.test_result_pdf', [
            'attempt' => $this->attempt,
            'formatAnswers' => $this->formatAnswers,
        ]);

        $pdfContent = $pdf->output();

        return [
            Attachment::fromData(static fn () => $pdfContent, 'Report.pdf')
                ->withMime('application/pdf'),
        ];
    }

    private function formatAnswers(): array
    {
        $test = $this->attempt->QuestionOrder;
        $formattedAnswers = [];
        $correctAnswers =[];

        foreach ($this->attempt->result as $result) {
            $answers = array_filter(array_values(Arr::wrap($result->answer)));
            $formatAnswer = [];
            $correctAnswers =[];

            foreach ($test as $question) {
                if (Arr::get($question, 'id') !== $result->question_id) {
                    continue;
                }
                    $allAnswers = Arr::get($question, 'answers', []);
                    $i = 1;
                    foreach ($allAnswers as $item) {
                        if (in_array(Arr::get($item, 'id'), $answers, false)) {
                            $prefix = Arr::get($question, 'type') === 'order' ? "$i. " : '';
                            $formatAnswer[] = $prefix.Arr::get($item, 'value');
                            }
                            $i++;
                    }
                if (!$result->is_correct) {
                    $correctAnswers = [];
                    foreach($this->attempt->QuestionOrder as $testquestion) {
                        if(Arr::get($testquestion, 'id') !== $result->question_id){
                            continue;
                        }
                        foreach(Arr::get($testquestion, 'answers') as $questionanswer) {
                            if (Arr::get($questionanswer, 'state')) {
                                $correctAnswers[] = Arr::get($questionanswer, 'value');
                            }
                        }
                    }
                }
            }
            $formattedAnswers[] = [
                'question' => $result->question,
                'formatAnswer' => implode('<br>', $formatAnswer),
                'correctAnswers' => implode('<br>', $correctAnswers),
                'isCorrect' => $result->is_correct ? 'Correct' : 'Incorrect',
            ];
        }
        return $formattedAnswers;
    }
}
