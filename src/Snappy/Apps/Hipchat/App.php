<?php namespace Snappy\Apps\Hipchat;

use Snappy\Apps\App as BaseApp;
use Snappy\Apps\TagsChangedHandler;
use Snappy\Apps\WallPostHandler;

class App extends BaseApp implements TagsChangedHandler, WallPostHandler {

	/**
	 * The name of the application.
	 *
	 * @var string
	 */
	public $name = 'HipChat';

	/**
	 * The application description.
	 *
	 * @var string
	 */
	public $description = 'Notify HipChat with tickets and wall posts.';

	/**
	 * Any notes about this application
	 *
	 * @var string
	 */
	public $notes = '<p>You can find your HipChat <a href="https://www.hipchat.com/admin/api" target="_blank">API Token here</a>.</p>';

	/**
	 * The application's icon filename.
	 *
	 * @var string
	 */
	public $icon = 'hipchat.png';

	/**
	 * The application author name.
	 *
	 * @var string
	 */
	public $author = 'UserScape, Inc.';

	/**
	 * The application author e-mail.
	 *
	 * @var string
	 */
	public $email = 'it@userscape.com';

	/**
	 * The settings required by the application.
	 *
	 * @var array
	 */
	public $settings = array(
		array('name' => 'token', 'type' => 'text', 'help' => 'Enter your HipChat v1 API Token'),
		array('name' => 'room', 'type' => 'text', 'help' => 'Enter your HipChat Room Name or ID'),
		array('name' => 'wall_notify', 'label' => 'Wall Notification', 'type' => 'checkbox', 'help' => 'Notify on new wall posts?'),
		array('name' => 'tag', 'label' => 'Watch for tag', 'type' => 'text', 'placeholder' => '#hipchat', 'help' => 'Tickets with this tag will send a notification to HipChat'),
	);

	/**
	 * Wall post added.
	 *
	 * @param  array  $wall
	 * @return void
	 */
	public function handleWallPost(array $wall)
	{
		if ($this->config['wall_notify'])
		{
			$client = $this->getClient();

			$text = '<a href="https://app.besnappy.com/#wall">https://app.besnappy.com/#wall</a> - '. $wall['content'];

			$client->message_room($this->config['room'], 'Snappy', $text);
		}
	}

	/**
	 * Handle tags changed.
	 *
	 * @param  array  $ticket
	 * @param  array  $added
	 * @param  array  $removed
	 * @return void
	 */
	public function handleTagsChanged(array $ticket, array $added, array $removed)
	{
		if ($this->config['tag'] != "" and in_array($this->config['tag'], $added))
		{
			$client = $this->getClient();

			$url = 'https://app.besnappy.com/#ticket/'.$ticket['id'];
			$text = $ticket['default_subject'].' - <a href="'.$url.'">'.$url.'</a>';

			$client->message_room($this->config['room'], 'Snappy', $text);
		}
	}

	/**
	 * Get the Hipchat client instance.
	 *
	 * @return \HipChat\HipChat
	 */
	public function getClient()
	{
		$client = new \HipChat\HipChat($this->config['token']);

		return $client;
	}

}
