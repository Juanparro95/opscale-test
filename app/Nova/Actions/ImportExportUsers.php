<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Exception;

class ImportExportUsers extends Action
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $name = 'Import/Export Users';

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Http\Requests\ActionRequest  $request
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, \Illuminate\Support\Collection $models)
    {
        if ($fields->operation === 'import') {
            return $this->importUsers($fields->file);
        }

        return $this->exportUsers();
    }

    /**
     * Import users from the given file.
     *
     * @param  \Laravel\Nova\Fields\File  $file
     * @return array
     */
    protected function importUsers($file)
    {
        $filePath = Storage::disk('local')->putFile('imports', $file);
        $errors = [];

        (new FastExcel)->import(storage_path('app/' . $filePath), function ($line) use (&$errors) {
            if (!User::where('email', $line['email'])->exists()) {
                User::create([
                    'name' => $line['name'],
                    'email' => $line['email'],
                    'password' => bcrypt($line['password']),
                ]);
            } else {
                $errors[] = "El email {$line['email']} ya se encuentra registrado en la base de datos";
            }
        });

        if (!empty($errors)) {
            return Action::danger(implode('<br>', $errors));
        }

        return Action::message('Users imported successfully');
    }

    /**
     * Export users to a file.
     *
     * @return array
     */
    protected function exportUsers()
    {
        $users = User::all();
        $directoryPath = storage_path('app/exports');

        // Ensure the directory exists
        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0755, true);
        }

        $filePath = $directoryPath . '/users.xlsx';
        (new FastExcel($users))->export($filePath);

        return Action::downloadUrl('users.xlsx', $filePath);
    }

    /**
     * Get the fields available on the action.
     *
     * @return \Illuminate\Support\Collection
     */
    public function fields(NovaRequest $request)
    {
        return [
            Select::make('Operation')
                ->options([
                    'import' => 'Import',
                    'export' => 'Export',
                ])
                ->rules('required'),

            File::make('File')
                ->disk('local')
                ->path('imports')
                ->rules('required_if:operation,import')
                ->help('Upload a file for import or leave blank for export.'),
        ];
    }
}
