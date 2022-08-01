<?php
namespace App\Console\Commands\Environment;

use Illuminate\Console\Command;
use App\Traits\Commands\EnvironmentWriterTrait;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Support\Str;

class EmailSettingsCommand extends Command
{
    use EnvironmentWriterTrait;

    /**
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * @var string
     */
    protected $description = 'Set or update the email sending configuration for the Panel.';

    /**
     * @var string
     */
        protected $signature = 'crm:environment:mail
                                {--driver= : The mail driver to use.}
                            {--email= : Email address that messages from the Panel will originate from.}
                            {--from= : The name emails from the Panel will appear to be from.}
                            {--encryption=}
                            {--host=}
                            {--port=}
                            {--username=}
                            {--password=}';

    /**
     * @var array
     */
    protected $variables = [];

    /**
     * EmailSettingsCommand constructor.
     *
     * @param \Illuminate\Contracts\Config\Repository $config
     */
    public function __construct(ConfigRepository $config)
    {
        parent::__construct();

        $this->config = $config;
    }

    /**
     * Handle command execution.
     */
    public function handle()
    {
        $this->variables['MAIL_DRIVER'] = $this->option('driver') ?? $this->choice(
            'Quel driver doit être utilisé pour l\'envoi d\'e-mails ?', [
                'smtp' => 'SMTP Server',
                'mail' => 'PHP\'s Internal Mail Function',
                'mailgun' => 'Mailgun Transactional Email',
                'mandrill' => 'Mandrill Transactional Email',
                'postmark' => 'Postmarkapp Transactional Email',
            ], $this->config->get('mail.driver', 'smtp')
        );

        $method = 'setup' . Str::studly($this->variables['MAIL_DRIVER']) . 'DriverVariables';
        if (method_exists($this, $method)) {
            $this->{$method}();
        }

        $this->variables['MAIL_FROM_ADDRESS'] = $this->option('email') ?? $this->ask(
            'L\'adresse email d\'où les emails doivent provenir', $this->config->get('mail.from.address')
        );

        $this->variables['MAIL_FROM_NAME'] = $this->option('from') ?? $this->ask(
            'Nom à partir duquel les e-mails doivent apparaître', $this->config->get('mail.from.name')
        );

        $this->variables['MAIL_ENCRYPTION'] = $this->option('encryption') ?? $this->choice(
            'Méthode de cryptage à utiliser', ['tls' => 'TLS', 'ssl' => 'SSL', '' => 'None'], $this->config->get('mail.encryption', 'tls')
        );

        $this->writeToEnvironment($this->variables);

        $this->line('Mise à jour du fichier de configuration de l\'environnement stocké.');
        $this->line('');
    }

    /**
     * Handle variables for SMTP driver.
     */
    private function setupSmtpDriverVariables()
    {
        $this->variables['MAIL_HOST'] = $this->option('host') ?? $this->ask(
            'Hôte SMTP (e.g. smtp.swizcloud.fr)', $this->config->get('mail.host')
        );

        $this->variables['MAIL_PORT'] = $this->option('port') ?? $this->ask(
            'Port SMTP', $this->config->get('mail.port')
        );

        $this->variables['MAIL_USERNAME'] = $this->option('username') ?? $this->ask(
            'Nom d\'utilisateur SMTP', $this->config->get('mail.username')
        );

        $this->variables['MAIL_PASSWORD'] = $this->option('password') ?? $this->secret(
            'Mot de passe SMTP'
        );
    }

    /**
     * Handle variables for mailgun driver.
     */
    private function setupMailgunDriverVariables()
    {
        $this->variables['MAILGUN_DOMAIN'] = $this->option('host') ?? $this->ask(
            'Domaine Mailgun', $this->config->get('services.mailgun.domain')
        );

        $this->variables['MAILGUN_SECRET'] = $this->option('password') ?? $this->ask(
            'Clé API de Mailgun', $this->config->get('services.mailgun.secret')
        );
    }

    /**
     * Handle variables for mandrill driver.
     */
    private function setupMandrillDriverVariables()
    {
        $this->variables['MANDRILL_SECRET'] = $this->option('password') ?? $this->ask(
            'Clé API de Mandrill', $this->config->get('services.mandrill.secret')
        );
    }

    /**
     * Handle variables for postmark driver.
     */
    private function setupPostmarkDriverVariables()
    {
        $this->variables['MAIL_DRIVER'] = 'smtp';
        $this->variables['MAIL_HOST'] = 'smtp.postmarkapp.com';
        $this->variables['MAIL_PORT'] = 587;
        $this->variables['MAIL_USERNAME'] = $this->variables['MAIL_PASSWORD'] = $this->option('username') ?? $this->ask(
            'Clé API de Postmark', $this->config->get('mail.username')
        );
    }
}
