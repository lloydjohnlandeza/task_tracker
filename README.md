# Tech Stack

1. Laravel
2. Vue 2
3. InertiaJS
4. Laravel Fortify

# Task Tracker

Basic Task Tracker

1. Basic user authentication
2. User can create/edit/soft-delete a task
3. List of tasks can be reordered by the user
4. Task should have the option to have sub-tasks (sub-tasks can have sub-task up to infinity)
5. Task can be marked as complete/pending/canceled/(custom)
6. Tasks should only be accessible to the owner
7. Ability to download all the tasks in Excel/CSV/JSON format
8. Ability to restore soft-deleted tasks
9. Soft deleted tasks should be forced deleted after 30 days
10. Chart to see users completed tasks

## How to install and run the repo

copy env.example then rename to .env
configure database in the env

```bash
composer install
npm install

php artisan migrate
php artisan db:seed

npm run watch
```

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](https://choosealicense.com/licenses/mit/)
