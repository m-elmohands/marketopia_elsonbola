<?php

namespace App\Filament\Support;

use App\Domain\Content\Actions\DeleteContent;
use App\Domain\Content\Actions\ForceDeleteContent;
use App\Domain\Content\Actions\SetPublication;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class ContentActions
{
    public static function publication(): Action
    {
        return Action::make('publication')->label(fn (Model $record) => $record->is_published ? __('Unpublish') : __('Publish'))
            ->visible(fn (Model $record) => ! $record->trashed() && Gate::allows('update', $record))->requiresConfirmation()
            ->action(function (Model $record): void {
                try {
                    app(SetPublication::class)->execute($record, ! $record->is_published);
                    Notification::make()->title(__('Publication updated'))->success()->send();
                } catch (ValidationException $exception) {
                    Notification::make()->title(__('Unable to publish'))->body($exception->validator->errors()->first())->danger()->persistent()->send();
                }
            });
    }

    public static function recovery(bool $force = true): array
    {
        $actions = [
            DeleteAction::make()->requiresConfirmation()->using(fn (Model $record, Action $action) => self::recover($action, fn () => app(DeleteContent::class)->execute($record))),
            RestoreAction::make()->requiresConfirmation()->using(fn (Model $record, Action $action) => self::recover($action, fn () => app(DeleteContent::class)->execute($record, true))),
        ];
        if ($force) {
            $actions[] = ForceDeleteAction::make()->requiresConfirmation()->using(fn (Model $record, Action $action) => self::recover($action, function () use ($record): bool {
                app(ForceDeleteContent::class)->execute($record);

                return true;
            }));
        }

        return $actions;
    }

    private static function recover(Action $action, callable $callback): bool
    {
        try {
            return $callback();
        } catch (ValidationException $exception) {
            Notification::make()->title(__('Unable to complete this action'))->body($exception->validator->errors()->first())->danger()->persistent()->send();
            $action->halt(true);

            return false;
        }
    }
}
