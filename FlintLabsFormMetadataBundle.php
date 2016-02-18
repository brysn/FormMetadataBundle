<?php
/*
 * This file is part of the Form Metadata library
 *
 * (c) Cameron Manderson <camm@flintinteractive.com.au>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FlintLabs\Bundle\FormMetadataBundle;

use FlintLabs\Bundle\FormMetadataBundle\DependencyInjection\Compiler\FormExtensionCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 *
 * @author camm (camm@flintinteractive.com.au)
 */
class FlintLabsFormMetadataBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new FormExtensionCompilerPass());
    }
}