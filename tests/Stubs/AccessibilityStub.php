<?php
declare(strict_types=1);

namespace CodeFoundation\FlowConfig\Tests\Stubs;

use CodeFoundation\FlowConfig\Interfaces\Accessibility\AccessibilityInterface;

/**
 * @coversNothing
 */
class AccessibilityStub implements AccessibilityInterface
{
    /**
     * @var bool
     */
    private $canGet;

    /**
     * @var bool
     */
    private $canSet;

    /**
     * Constructs a new stubbed accessibility class instance.
     *
     * @param bool|null $canGet Whether gets should be allowed.
     * @param bool|null $canSet Whether sets should be allowed.
     */
    public function __construct(?bool $canGet = null, ?bool $canSet = null)
    {
        $this->canGet = $canGet ?? true;
        $this->canSet = $canSet ?? true;
    }

    /**
     * {@inheritdoc}
     */
    public function canGetKey(string $key): bool
    {
        return $this->canGet;
    }

    /**
     * {@inheritdoc}
     */
    public function canSetKey(string $key): bool
    {
        return $this->canSet;
    }
}
