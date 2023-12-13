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

        foreach ($this->attempt->result as $result) {
            $answers = array_filter(array_values(Arr::wrap($result->answer)));
            $formatAnswer = [];

            foreach ($test as $question) {
                if (Arr::get($question, 'id') !== $result->question_id) {
                    continue;
                }

                if (Arr::get($question, 'type') === 'image-custom') {
                    foreach ($question['answers'] as $answer) {
                        $formatAnswer[] = $answer['value'].': '.Arr::get($result->answer, $answer['id'], '');
                    }
                } else {
                    $allAnswers = Arr::get($question, 'answers', []);
                    $i = 1;

                    foreach ($allAnswers as $item) {
                        if (in_array(Arr::get($item, 'id'), $answers, false)) {
                            $prefix = Arr::get($question, 'type') === 'order' ? "$i. " : '';
                            $formatAnswer[] = $prefix.Arr::get($item, 'value');
                            $i++;
                        }
                    }
                }
            }
            $formattedAnswers[] = [
                'question' => $result->question,
                'formatAnswer' => implode('<br>', $formatAnswer),
                'isCorrect' => $result->is_correct ? 'Correct' : 'Incorrect',
            ];
        }

        return $formattedAnswers;
    }
}
