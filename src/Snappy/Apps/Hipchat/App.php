<?php namespace Snappy\Apps\Hipchat;

use Snappy\Apps\App as BaseApp;
use Snappy\Apps\TagsChangedHandler;

class App extends BaseApp implements TagsChangedHandler {

	/**
	 * The name of the application.
	 *
	 * @var string
	 */
	public $name = 'Hipchat';

	/**
	 * The application description.
	 *
	 * @var string
	 */
	public $description = 'Notify Hipchat.';

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
		array('name' => 'token', 'type' => 'text', 'help' => 'Enter your HipChat <a href="https://www.hipchat.com/admin/api" target="_blank">v1 API Token</a>'),
		array('name' => 'room', 'type' => 'text', 'help' => 'Enter your HipChat Room Name or ID'),
		array('name' => 'wall_notify', 'type' => 'checkbox', 'help' => 'Notify on new wall posts?'),
		array('name' => 'ticket_notify', 'type' => 'checkbox', 'help' => 'Notify on tickets tagged #hipchat?'),
	);

	/**
	 * Wall post added.
	 *
	 * @param  array  $wall
	 * @return void
	 */
	public function handleWallCreated(array $wall)
	{
		if ($this->config['wall_notify'])
		{
			$client = $this->getClient();

			$text = $wall['content'].' - <a href="https://app.besnappy.com/#wall">https://app.besnappy.com/#wall</a>';

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
		if ($this->config['ticket_notify'] and in_array('#hipchat', $added))
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
