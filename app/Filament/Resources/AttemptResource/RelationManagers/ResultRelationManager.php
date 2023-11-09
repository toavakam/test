<?php

namespace App\Filament\Resources\AttemptResource\RelationManagers;

use App\Models\Result;
use Filament\Forms;
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
                        $test = $model->attempt->test;

                        $answers = array_filter(array_values(Arr::wrap($model->answer)));

                        $result = [];
                        foreach ($test->getQuestions('lv') as $question) {
                            if (Arr::get($question, 'text') !== $model->question) {
                                continue;
                            }
                            $allAnswers = Arr::get($question, 'answers', []);
                            $i = 1;
                            foreach ($allAnswers as $item) {
                                if (in_array(Arr::get($item, 'id'), $answers, false)) {
                                    $prefix = Arr::get($question, 'type') === 'order' ? "$i. " : '';
                                    $result[] = $prefix . Arr::get($item, 'value');
                                    $i++;
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
