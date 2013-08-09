<?php
namespace MdgMultiUser\Options;

interface ModuleOptionsInterface
{

    /**
     *
     * @return string the $alias
     */
    public function getAlias();

    /**
     *
     * @param string $alias
     */
    public function setAlias($alias);
}
