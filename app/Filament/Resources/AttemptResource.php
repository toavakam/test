<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttemptResource\Pages;
use App\Filament\Resources\AttemptResource\RelationManagers;
use App\Models\Attempt;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AttemptResource extends Resource
{
    protected static ?string $model = Attempt::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';

    protected static ?string $modelLabel = 'Testing results';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('test')->relationship('test', titleAttribute: 'name')->columnSpanFull(),

                Fieldset::make('Employee')->schema([
                    TextInput::make('name')->label('First name'),

                    TextInput::make('lastname')->label('Last name'),

                    TextInput::make('question_count')->label('Result')
                        ->formatStateUsing(function (Model $record): string {
                            return "$record->correct_answer_count / $record->question_count";
                        }),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date'),

                Tables\Columns\TextColumn::make('name')
                    ->label('First name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('lastname')
                    ->label('Last name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('test.name')
                    ->label('Test Name'),

                Tables\Columns\TextColumn::make('question_count')
                    ->label('Result')
                    ->formatStateUsing(function (int $state, Model $model): string {
                        return "$model->correct_answer_count / $state";
                    }),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make('View')
                    ->url(fn (Attempt $record): string => route('filament.admin.resources.attempts.edit', $record)),
            ])
            ->bulkActions([

            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ResultRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttempts::route('/'),
            'edit' => Pages\EditAttempt::route('/{record}/edit'),
        ];
    }
}
