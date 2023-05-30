# Laravel Shopify App Template
This template can be used to streamline the development of Shopify apps on the Laravel framework with the intention of hosting through Laravel Vapor.

The template is still in development. 

This was based off the [Shopify App Template for PHP](https://github.com/Shopify/shopify-app-template-php).

How is this different:
- Removes the deployment difficulties/limitations presented by Shopify's solution.
- Easier and clearer routing.
- Frontend runs through Laravel. Support for latest Polaris React framework.
- Intended for use with Laravel Vapor.

## Getting Started
What you need:
1. A [Shopify partner account](https://partners.shopify.com/signup).
2. A [development store](https://help.shopify.com/en/partners/dashboard/managing-stores/development-stores#create-a-development-store) for testing.
3. PHP and [Composer](https://getcomposer.org/) installed.
4. [Node and NPM](https://nodejs.org/en) installed.

### Installing the template

1. Clone the repository to your local machine.
    ```
    git clone https://github.com/teckrowd/shopify-laravel.git
    ```

2. Install composer dependencies.
    ```
    composer install
    ```

3. Create an .env file and generate an app key.
    ```
    cp .env.example .env
    php artisan key:generate
    ```

4. Setup your preferred database for testing and migrate the starter tables. Refer to [Laravel's docs](https://laravel.com/docs/10.x#databases-and-migrations) for quick database setup.
    ```
    php artisan migrate
    ```

5. Install node dependencies.
    ```
    npm install
    ```

6. Start the development server and happy building! The Shopify CLI will create a tunnel between your local environment and the development store using Cloudflare. For convenience I recommend using your own domain through [Ngrok](https://ngrok.com/).
    ```
    npm run shopify.dev -- --tunnel-url https://yourngroktunnel.example:443
    ```

### Configuration
You'll need to update the following variables in Laravel's env file before using the app.
```
SHOPIFY_API_KEY = ######   // Your apps API key.
SHOPIFY_API_SECRET = ######   // Your apps API secret.
SHOPIFY_API_SCOPES = write_products   // Separate each with a commar.
SHOPIFY_PATH_PREFIX = shopify // URL prefix. https://example.com/{prefix}/{app}
```

### Development
This template uses the Shopify CLI for development and publishing your store. To get a better understanding of the CLI and the available commands please refer to [Shopify's documentation](https://shopify.dev/docs/apps/tools/cli/commands).

#### Backend configuration
In the root directory you will find the `shopify.web.toml` config file. This file is required for starting the development server. You can change the port that the Laravel development server will launch on by changing the value of the `port` property. If you wish to change the other settings please refer to [Shopify's documentation](https://shopify.dev/docs/apps/tools/cli/structure#shopify-web-toml).

#### Frontend configuration
You'll find a `shopify.web.toml` file in the `resources/js` directory. This file is used for spinning up the frontend through Vite. The location of this file technically does not matter and you are free to move it about to suit your build. Modify this file if you move away from Vite or need extra functionality. Refer to [Shopify's documentation](https://shopify.dev/docs/apps/tools/cli/structure#shopify-web-toml) for more information.

### Deployment
Before you deploy to Vapor you will need to update the `vapor.yml` config file.
```
id: ##
name: vapor-project
environments:
    production:
        memory: 1024
        cli-memory: 512
        runtime: 'php-8.2:al2'
        build:
            - 'composer install --no-dev'
            - 'php artisan event:cache'
```

Pull down Vapor's environment variables.
```
vapor env:pull production
```

Update based on your development `.env` file and push.
```
vapor env:push production
```

Deploy the app.
```
vapor deploy production
```

## Contributing
Help improve this template by contributing.

Before opening a pull request, please first discuss the proposed changes via Github issue or <a href="mailto:support@teckrowd.com">email</a>.

## License
This project is licensed under the MIT License - see the [LICENSE](https://github.com/teckrowd/shopify-laravel/blob/master/LICENSE.md) file for details