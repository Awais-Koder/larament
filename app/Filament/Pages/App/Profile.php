<?php

namespace App\Filament\Pages\App;

use App\Filament\Actions\GeneratePasswordAction;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile;
use Filament\Actions\Action;


class Profile extends EditProfile
{
    public function getHeaderActions(): array
    {
        return [
            Action::make('testModal')
                ->label('Test Modal')
                ->modalHeading('Debug Modal')
                ->modalSubmitActionLabel('Confirm')
                ->form([
                    TextInput::make('note')
                        ->label('Test Input')
                        ->required(),
                ])
                ->action(function (array $data) {
                    \Filament\Notifications\Notification::make()
                        ->title("Submitted: {$data['note']}")
                        ->success()
                        ->send();
                }),
        ];
    }
    public function getBreadcrumbs(): array
    {
        return [
            null => __('Dashboard'),
            'profile' => __('Profile'),
        ];
    }

    public function form(Form $form): Form
    {
        /** @var TextInput $passwordComponent */
        $passwordComponent = $this->getPasswordFormComponent();

        return $form->schema([
            Section::make()
                ->inlineLabel(false)
                ->schema([
                    $this->getNameFormComponent(),
                    $this->getEmailFormComponent(),
                    $passwordComponent->suffixActions([
                        GeneratePasswordAction::make(),
                    ]),
                    $this->getPasswordConfirmationFormComponent(),
                ]),

            Section::make('Company Details')
                ->description('Optional company information for your account')
                ->columns(2)
                ->schema([
                    TextInput::make('company_name')->label('Company Name')->maxLength(255),
                    TextInput::make('company_reg_no')->label('Company Registration No')->maxLength(255),
                    TextInput::make('company_address')->label('Company Address')->maxLength(255),
                    TextInput::make('company_email')->label('Company Email')->email()->maxLength(255),
                    TextInput::make('company_phone')->label('Company Phone')->tel()->maxLength(255),
                ]),
        ]);
    }
}
