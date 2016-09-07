<?php
namespace Robo\Common;

use Symfony\Component\Process\ProcessUtils;

/**
 * Use this to add arguments and options to the $arguments property.
 */
trait CommandArguments
{
    protected $arguments = '';

    /**
     * Pass argument to executable
     *
     * @param $arg
     * @return $this
     */
    public function arg($arg)
    {
        return $this->args($arg);
    }

    /**
     * Pass methods parameters as arguments to executable
     *
     * @param $args
     * @return $this
     */
    public function args($args)
    {
        if (!is_array($args)) {
            $args = func_get_args();
        }
        array_map('static::escape', $args);
        $this->arguments .= " ".implode(' ', $args);
        return $this;
    }

    public function rawArg($arg)
    {
        $this->arguments .= " $arg";
    }

    /**
     * Escape the provided value, unless it contains only alphanumeric
     * plus a few other basic characters.
     *
     * @param string $value
     * @return string
     */
    public static function escape($value)
    {
        if (preg_match('/^[a-zA-Z0-9\/.@~_-]*$/', $value)) {
            return $value;
        }
        return ProcessUtils::escapeArgument($value);
    }

    /**
     * Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
     *
     * Option values are automatically escaped if necessary.
     *
     * @param $option
     * @param string $value
     * @return $this
     */
    public function option($option, $value = null)
    {
        if ($option !== null and strpos($option, '-') !== 0) {
            $option = "--$option";
        }
        $this->arguments .= null == $option ? '' : " " . $option;
        $this->arguments .= null == $value ? '' : " " . static::escape($value);
        return $this;
    }

    /**
     * Pass multiple options to executable. Value can be a string or array.
     *
     * Option values should be provided in raw, unescaped form
     *
     * @param $option
     * @param string|array $value
     * @return $this
     */
    public function optionList($option, $value = array())
    {
        if (is_array($value)) {
            foreach ($value as $item) {
                $this->optionList($option, $item);
            }
        } else {
            $this->option($option, $value);
        }

        return $this;
    }
}
