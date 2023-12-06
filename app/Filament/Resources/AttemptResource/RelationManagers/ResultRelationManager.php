<?php

namespace App\Filament\Resources\AttemptResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class ResultRelationManager extends RelationManager
{
    protected static string $relationship = 'result';

    protected static ?string $title = 'Test results';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('question'),

                Tables\Columns\TextColumn::make('answer')
                    ->formatStateUsing(function (string $state, Model $model): string {
                        $test = $model->attempt->QuestionOrder;
                        $answers = array_filter(array_values(Arr::wrap($model->answer)));

                        $result = [];
                        foreach ($test as $question) {
                            if (Arr::get($question, 'id') !== $model->question_id) {
                                continue;
                            }
                            if (Arr::get($question, 'type') === 'image-custom') {
                                foreach($question['answers'] as $answer) {
                                    $result[] = $answer['value'].': '.Arr::get($model->answer, $answer['id'], '');
                                }
                            } else {
                                $allAnswers = Arr::get($question, 'answers', []);

                                $i = 1;
                                foreach ($allAnswers as $item) {
                                    if (in_array(Arr::get($item, 'id'), $answers, false)) {
                                        $prefix = Arr::get($question, 'type') === 'order' ? "$i. " : '';
                                        $result[] = $prefix.Arr::get($item, 'value');
                                        $i++;
                                    }
                                }
                            }
                        }

                        return implode('<br>', $result);
                    })

                    ->html(),

                Tables\Columns\IconColumn::make('is_correct')
                    ->boolean(),
            ])
            ->defaultSort('id');
    }
}
