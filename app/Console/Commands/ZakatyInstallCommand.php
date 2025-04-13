<?php

namespace App\Console\Commands;

use Filament\Support\Colors\Color;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use function Laravel\Prompts\password;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class ZakatyInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zakaty:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install a new instance of zakaty';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        File::copy(".env.example", ".env");
        File::append(".env", "\n\n\n");

        sleep(1);
        Artisan::call("config:clear", [], $this->getOutput());
        Artisan::call("key:generate --force", [], $this->getOutput());

        // APP URL
        $app_url = text(
            label: "What is the app url?",
            default: "http://localhost:8000",
            hint: "Default: http://localhost:8000"
        );
        File::append(".env", "APP_URL=$app_url\n");

        //database
        $database_type = select(
            label: "Database Type",
            options: ["sqlite", "mysql"],
            default: "sqlite"
        );

        if ($database_type == "mysql") {
            $this->setup_mysql_database();
        } elseif ($database_type != "sqlite") {
            $this->error("Database type not supported");
            exit;
        }
        Artisan::call("migrate:fresh --seed --force", [], $this->getOutput());


        Artisan::call("optimize:clear", [], $this->output);
        Artisan::call("icons:clear", [], $this->output);
        Artisan::call("config:clear", [], $this->output);

        $timezone = text(
            label: "What is your timezone",
            placeholder: "UTC",
            required: true,
        );
        File::append(".env", "APP_TIMEZONE=$timezone\n");

        $main_theme_color = select(
            label: 'Choose your preferred theme color',
            options: array_keys(Color::all()),
            default: 'amber',
            hint: "this can be changed later on"
        );
        File::append(".env", "THEME_COLOR=".Str::headline($main_theme_color)."\n");


        $this->info("Spinning up the website");


        Artisan::call("optimize", [], $this->output);
        Artisan::call("filament:cache-components", [], $this->output);
        Artisan::call("icons:cache", [], $this->output);


        $how_to_run = select(
            label: "how are you planning to run the schedule and queues?",
            options: ["cron", "terminal"],
            default: "cron",
            required: true
        );
        $os = select(
            label: "what is your operating system",
            options: ["linux", "windows", "mac"],
            default: "linux",
            required: true
        );

        $path = text(
            label: "what is the path to php",
            hint: ($os == "linux" || $os == "mac") ? "/path/to/php" : "C:\\path\\to\\php"
        );
        $project = text(
            label: "what is the path to the project folder",
            hint: ($os == "linux" || $os == "mac") ? "/var/www/discount" : "C:\\path\\to\\project"
        );


        if ($how_to_run == "cron" && ($os == "mac" || $os == "linux")) {
            $this->setup_cron_linux($path, $project);
        } elseif ($how_to_run == "terminal" && ($os == "mac" || $os == "linux")) {
            $this->setup_terminal_linux($path, $project);
        } elseif ($how_to_run == "cron" && $os == "windows") {
            $this->setup_cron_windows($path, $project);
        } elseif ($how_to_run == "terminal" && $os == "windows") {
            $this->setup_terminal_windows($path, $project);
        }
    }


    public function setup_mysql_database(): void
    {
        $db_host = text(
            label: "What is the database host?  you can use IP or URL",
            default: "127.0.0.1",
            hint: "Default: 127.0.0.1"
        );
        File::append(".env", "DB_HOST=$db_host\n");

        $db_port = text(
            label: "What is the database port? ",
            default: "3306",
            hint: "Default: 3306"
        );
        File::append(".env", "DB_PORT=$db_port\n");

        $db_name = text(
            label: "What is the database name?",
            default: "zakaty",
            hint: "Default: price-tracker"
        );
        File::append(".env", "DB_DATABASE=$db_name\n");

        $db_user = text(
            label: "What is the database user? ",
            default: "root",
            hint: "Default: root"
        );
        File::append(".env", "DB_USERNAME=$db_user\n");

        $db_pass = password(
            label: "What is the database password? ",
            hint: "Default: password"

        );
        (Str::length($db_pass)) ?: $db_pass = "password";
        File::append(".env", "DB_PASSWORD=\"$db_pass\"\n");
    }

    private function setup_cron_linux($path, $project): void
    {
        \Laravel\Prompts\info("Schedule Automation");
        \Laravel\Prompts\info("*/5 * * * * $path $project/artisan schedule:run >> /dev/null 2>&1\"");
        \Laravel\Prompts\info("*/5 * * * * $path $project/artisan queue:work  >> /dev/null 2>&1");

    }
    private function setup_cron_windows($path, $project): void
    {
        \Laravel\Prompts\info("Schedule Automation");
        \Laravel\Prompts\info("schtasks /create /sc minute /mo 5 /tn \"ZakatyScheduleTask\" /tr \"$path $project\\artisan schedule:run\"");
        \Laravel\Prompts\info("schtasks /create /sc minute /mo 6 /tn \"QueueJobForZakaty\" /tr \"$path $project\\artisan queue:work\"");
    }

    private function setup_terminal_linux($path, $project): void
    {
        \Laravel\Prompts\info("Schedule Automation");

        $final_string = "$path $project/artisan schedule:work >> /dev/null 2>&1 & $path $project/artisan queue:listen  >> /dev/null 2>&1 ";

        \Laravel\Prompts\info($final_string);
    }

    private function setup_terminal_windows($path, $project): void
    {
        \Laravel\Prompts\info("Schedule Automation");

        $final_string = "start /B $path $project\\artisan schedule:work > nul 2>&1  & start /B $path $project\\artisan queue:listen  > nul 2>&1";

        \Laravel\Prompts\info($final_string);
    }
}
