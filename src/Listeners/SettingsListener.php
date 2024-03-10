<?php

namespace Gtdxyz\Checkin\Listeners;

use Carbon\Carbon;
use Flarum\Foundation\ValidationException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;

use Flarum\Settings\Event\Saving;

use Illuminate\Support\Arr;


class SettingsListener{
    protected $settings;
    protected $events;
    protected $translator;

    public function __construct(SettingsRepositoryInterface $settings, Dispatcher $events, TranslatorInterface $translator){
        $this->settings = $settings;
        $this->events = $events;
        $this->translator = $translator;
    }

    public function settingsSaving(Saving $event){
        $settings = $event->settings;

        // if(!isset($event->settings['gtdxyz-checkin.reward'])){
        //     return false;
        // }

        if(isset($event->settings['gtdxyz-checkin.reward']) && $event->settings['gtdxyz-checkin.reward'] < 0){
            $event->settings['gtdxyz-checkin.reward'] = 0;
        }
        if(isset($event->settings['gtdxyz-checkin.constant-reward']) && $event->settings['gtdxyz-checkin.constant-reward'] < 0){
            $event->settings['gtdxyz-checkin.constant-reward'] = 0;
        }
        if(isset($event->settings['gtdxyz-checkin.constant-days']) && $event->settings['gtdxyz-checkin.constant-days'] < 0){
            $event->settings['gtdxyz-checkin.constant-days'] = 0;
        }


        // if($event->settings['gtdxyz-checkin.reward'] < 0 ||
        // $event->settings['gtdxyz-checkin.constant-reward'] < 0 || 
        // $event->settings['gtdxyz-checkin.constant-days'] < 0){
        //     throw new ValidationException([
        //         'message' => $this->translator->trans('gtdxyz-checkin.admin.settings.validation.number')
        //     ]);
        // }

        if(array_key_exists('gtdxyz-checkin.constant-days', $settings)){
            if($settings['gtdxyz-checkin.constant-days'] <= 0 ){
                $event->settings['gtdxyz-checkin.constant'] = 0;
                $event->settings['gtdxyz-checkin.constant-reward'] = 0;
                $event->settings['gtdxyz-checkin.constant-days'] = 0;
            }
        }
        
    }
}
