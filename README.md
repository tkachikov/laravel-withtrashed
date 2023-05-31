## Laravel WithTrashed

[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](https://opensource.org/licenses/MIT)

Trait for set magic method withTrashed for models with SoftDelete

### Usage

```shell
use Tkachikov\LaravelWithtrashed\WithTrsashed;

class User {
    use WithTrashed;
    
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}

$posts            = $user->posts;            // default
$postsWithTrashed = $user->postsWithTrashed; // using trait

$postsBuilder            = $user->posts();            //default
$postsBuilderWithTrashed = $user->postsWithTrashed(); // using trait

$userPosts            = User::with('posts')->first();            // default
$userPostsWithTrashed = User::with('postsWithTrashed')->first(); // using trait

```

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
