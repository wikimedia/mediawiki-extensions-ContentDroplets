<?php

namespace MediaWiki\Extension\ContentDroplets\Droplet;

use Message;
use RawMessage;

class CustomTemplateDroplet extends TemplateDroplet {

	/** @var string */
	private $name;
	/** @var string */
	private $description;
	/** @var string */
	private $icon;
	/** @var string */
	private $target;
	/** @var string */
	private $content;
	/** @var array */
	private $categories;
	/** @var array */
	private $params;
	/** @var array */
	private $rlModules = [];
	/** @var string */
	private $type;

	/**
	 * @param string $name
	 * @param string $description
	 * @param string $icon
	 * @param string $target
	 * @param array $params
	 * @param string $type
	 * @param array|null $categories
	 * @param array $rlModules
	 */
	public function __construct(
		string $name, string $description, string $icon, string $target,
		array $params, string $type, ?array $categories = [], ?array $rlModules = []
	) {
		$this->name = $name;
		$this->description = $description;
		$this->icon = $icon;
		$this->target = $target;
		$this->params = $params;
		$this->categories = $categories;
		$this->rlModules = $rlModules;
		$this->type = $type;
	}

	/**
	 * Get target for the template
	 * @return string
	 */
	protected function getTarget(): string {
		return $this->target;
	}

	/**
	 * Template params
	 * @return array
	 */
	protected function getParams(): array {
		return $this->params;
	}

	/**
	 * @inheritDoc
	 */
	public function getName(): Message {
		$message = Message::newFromKey( $this->name );
		return $message->exists() ? $message : new RawMessage( $this->name );
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription(): Message {
		$message = Message::newFromKey( $this->description );
		return $message->exists() ? $message : new RawMessage( $this->description );
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon(): string {
		return $this->icon;
	}

	/**
	 * @inheritDoc
	 */
	public function getRLModules(): array {
		return $this->rlModules;
	}

	/**
	 * @inheritDoc
	 */
	public function getCategories(): array {
		return $this->categories;
	}

	/**
	 * @return string
	 */
	public function getType(): string {
		return $this->type;
	}

}
