<?php

namespace Lumin;

use Exception;
use Lumin\Databases\DB;
use Lumin\Log\LogManager;
use Lumin\Requests\Request;
use Lumin\Responses\Response;
use Lumin\Routing\Router;
use Lumin\Schemas\Schema;
use Lumin\Support\Facades\Log;
use Lumin\Support\Facades\Route;

class App {
    /**
     * The root path of the application.
     *
     * This property holds the root path of the application.
     *
     * @var string
     */
    public static string $basePath;
    /**
     * The application instance.
     *
     * This property holds the singleton instance of the application.
     *
     * @var App
     */
    public static App $app;

    /**
     * The service container instance.
     *
     * This property holds the instance of the Container class.
     *
     * @var Container
     */
    public Container $container;

    /**
     * The request instance.
     *
     * This property holds the instance of the Request class.
     *
     * @var Request
     */
    public Request $request;

    public DB      $db;
    public Session $session;

    public function __construct($basePath) {
        self::$basePath  = $basePath;
        self::$app       = $this;
        $this->container = new Container();
        $this->db        = new DB();
    }

    /**
     * Run the application.
     *
     * This method starts the application by executing the necessary
     * initialization and handling the incoming request.
     *
     * @return void
     * @throws Exception
     */
    public function run(): void {
        try {
            $this->request = new Request();
            $this->session = new Session();
            $this->boot();
            Route::resolve();
        } catch ( Exception $e ) {
            Log::error($e->getMessage());
        }
    }

    /**
     * Boot the application.
     *
     * This method boots the application by registering the helpers,
     * connecting to the database, and registering the service providers.
     *
     * @return void
     */
    public function boot(): void {
        $this->registerFacades();
        $this->db->connectToDatabase();
        $this->registerServiceProviders();
    }

    private function registerFacades(): void {
        $facades = ['router'   => Router::class, 'log' => LogManager::class, 'db' => DB::class,
                    'schema'   => Schema::class, 'response' => Response::class];

        foreach ($facades as $name => $facade) {
            $this->container->set($name, function () use ($facade) {
                return new $facade();
            });
        }
    }

    /**
     * Register service providers.
     *
     * This method registers the service providers defined in the
     * configuration file.
     *
     * @return void
     */
    private function registerServiceProviders(): void {
        $config    = include_once self::$basePath.'/config/app.php';
        $providers = $config['providers'];

        foreach ($providers as $provider) {
            new $provider();
        }
    }
}