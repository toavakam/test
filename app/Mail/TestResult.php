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

    public $formatAnswers;

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
        $this->formatAnswers = $this->formatAnswers();

        return (new Content('email.test_results'))
            ->with('formatAnswers', $this->formatAnswers);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $pdf = PDF::loadView('email.test_result_pdf', [
            'attempt' => $this->attempt,
            'formatAnswers' => $this->formatAnswers,
        ]);

        $pdfContent = $pdf->output();

        return [
            Attachment::fromData(fn () => $pdfContent, 'Report.pdf')
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
