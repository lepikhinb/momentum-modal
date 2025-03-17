<?php

declare(strict_types=1);

namespace Momentum\Modal\Tests\Stubs;

use Momentum\Modal\Modal;

class ExampleModal extends Modal {
	protected function component(): array
	{
		return [
			'foo' => 'bar',
			...parent::component(),
		];
	}
}