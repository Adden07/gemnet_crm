<?php

namespace App\Providers;

use App\Channels\ZendeskNotificationChannel;
;
use App\Models\Category;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use App\Models\Customize;
use App\Models\Setting;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        \Illuminate\Support\Facades\Schema::defaultStringLength(191);
        if (config('app.force_https') == 'ON') {
            $this->app['url']->forceScheme('https');
        }

        Builder::macro('whereLike', function ($attributes, string $searchTerm) {
            $this->where(function (Builder $query) use ($attributes, $searchTerm) {
                foreach (\Arr::wrap($attributes) as $attribute) {
                    $query->when(
                        str_contains($attribute, '.'),
                        function (Builder $query) use ($attribute, $searchTerm) {
                            [$relationName, $relationAttribute] = explode('.', $attribute);

                            $query->orWhereHas($relationName, function (Builder $query) use ($relationAttribute, $searchTerm) {
                                $query->where($relationAttribute, 'LIKE', "%{$searchTerm}%");
                            });
                        },
                        function (Builder $query) use ($attribute, $searchTerm) {
                            $query->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
                        }
                    );
                }
            });
            return $this;
        });

        Paginator::useBootstrap();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {   
        $this->sendVerifyEmail();
        //if(Auth::check()){
            $this->customization();
            $this->siteSettings();
            view()->share('edit_setting',Cache::get('edit_setting'));
            // if(Cache::has('edit_setting')){
            //     echo '<pre>';
            //     print_r(Cache::get('edit_setting'));
            // }
            // exit();
            // view()->composer()
        //}   
    }

    //return customization from cache
    private function customization(){
        Cache::rememberForever('customization',function(){
            // if(auth()->check()){
                $custom = Customize::where('admin_id',1)->first();
                if(!empty($custom)){
                    //return json_decode($custom->data,true);
                    return $custom->data;
                }
                return NULL;
            //}
        });
    }
    //site setting cache
    public function siteSettings(){
        Cache::rememberForever('edit_setting',function(){
            return Setting::first();
        });

    }

    private function sendVerifyEmail(){
        VerifyEmail::toMailUsing(function ($notifiable) {
            $verify_url = URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(config('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );

            $data = [
                'user_id' => $notifiable->id,
                'user_type' => 'user',
                'subject' => 'ACCOUNT VERIFICATION REQUIRED',
                'type' => 'verify',
                'email_data' => ['verify_url' => $verify_url, 'name' => $notifiable->complete_name]
            ];

            return (new MailMessage())
                ->from(config('mail.from.address'), config('mail.from.name'))
                ->subject('Verify your email address')
                ->markdown('emails.verify', $data);
        });
    }
}
