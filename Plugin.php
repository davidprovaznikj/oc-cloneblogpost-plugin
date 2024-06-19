<?php namespace DavidProvaznik\CloneBlogPost;

use Backend;
use Event;
use Illuminate\Support\Carbon;
use October\Rain\Support\Facades\Str;
use RainLab\Blog\Controllers\Posts;
use RainLab\Blog\Models\Post;
use System\Classes\PluginBase;

/**
 * Plugin Information File
 *
 * @link https://docs.octobercms.com/3.x/extend/system/plugins.html
 */
class Plugin extends PluginBase
{
    /** @var string[] $require Required plugins. */
    public $require = [
        'Rainlab.Blog',
    ];

    /**
     * pluginDetails about this plugin.
     */
    public function pluginDetails()
    {
        return [
            'name' => 'CloneBlogPost',
            'description' => 'Extends the RainLab.Blog plugin with additional functionality of Clone Blog Posts.',
            'author' => 'David Provaznik',
            'icon' => 'icon-leaf'
        ];
    }

    /**
     * boot method, called right before the request route.
     */
    public function boot(): void
    {
        Posts::extend(function($controller) {
            // Register the AJAX handler for cloning the post
            $controller->addDynamicMethod('onClonePost', function() {
                $postId = post('postId');
                $originalPost = Post::find($postId);

                if ($originalPost) {
                    $timestamp = Carbon::now()->format('Ymd H:i:s');
                    $clonedPost = $originalPost->replicate();
                    $clonedPost->title = $originalPost->title . ' (Clone ' . $timestamp . ')';
                    $clonedPost->slug = Str::slug($clonedPost->title);
                    $clonedPost->published = false; // Set the cloned post as unpublished
                    $clonedPost->published_at = NULL; // Set the cloned post as unpublished
                    $clonedPost->created_at = $timestamp; // Set the cloned post as unpublished
                    $clonedPost->save();

                    // category duplicate
                    $clonedPost->categories()->sync($originalPost->categories->pluck('id')->toArray());

                    return ['editUrl' => Backend::url('rainlab/blog/posts/update/' . $clonedPost->id)];
                }

                throw new \ApplicationException('Post not found');
            });
        });

        Event::listen('backend.page.beforeDisplay', function($controller, $action, $params) {
            if ($controller instanceof Posts && $action == 'index') {
                $controller->addJs('/plugins/davidprovaznik/cloneblogpost/assets/js/extendToolbar.js');
            }
        });
    }
}
