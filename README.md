## GitHub Followers 

A page with a form for searching user data and followers by GitHub username. 
The user's GitHub username, number of subscribers, and avatar of the user's subscribers are displayed for the user found.

Because some users (e.g. taylorotwell, etc.) have many thousands of subscribers, GitHub only returns a portion of the subscribers on each request. Added a “Load More” button, which when clicked receives the next portion of subscribers. This button persists until there are no more subscriber pages to receive.

Information on the GitHub API is available [here](https://docs.github.com/en/rest?apiVersion=2022-11-28).

[Live demo](http://github.free.nf)  
    - [Swagger UI API](http://github.free.nf/swagger/)

Automatic documentation can be created using the phpDocumentor tool: https://phpdoc.org/.

### Technologies used
- PHP ([Laravel](https://github.com/laravel/laravel))
- JS, AJAX ([Vue](https://github.com/vuejs/vue))
- GitHub API ([last version 2022-11-28](https://docs.github.com/en/rest?apiVersion=2022-11-28))


### Installation
- Clone the repo
- Run ```composer install```<!--- Run ```php -r "file_exists('.env') || copy('.env.example', '.env');"```-->
- Set your [GitHub token](https://docs.github.com/en/authentication/keeping-your-account-and-data-secure/managing-your-personal-access-tokens#creating-a-fine-grained-personal-access-token) as the configuration value ```GITHUB_ACCESS_TOKEN``` in the ```.env``` file
- Run ```php artisan serve``` and open page in browser (http://127.0.0.1:8000) 🎉

Running tests: ```php artisan test --filter ApiTest```
